#!/usr/bin/env python3
"""
AUC-ROC Metrics Test for Bayesian Knowledge Tracing (BKT) Algorithm
Tests predictive accuracy of student knowledge states and response prediction
"""

import sys
import os
import json
import time
import random
import numpy as np
from datetime import datetime, timedelta
from sklearn.metrics import roc_auc_score, roc_curve, precision_recall_curve, auc
import matplotlib.pyplot as plt

# Add the public/algorithm directory to the Python path
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'public', 'algorithm'))

# Import the BKT algorithm
try:
    from bkt_algorithm import BKTAlgorithm
except ImportError as e:
    print(f"Error importing bkt_algorithm module: {e}")
    sys.exit(1)

class BKTAUCROCMetrics:
    def __init__(self):
        self.bkt = BKTAlgorithm()
        self.questions_data = []
        self.load_questions_data()

        # More realistic BKT Parameters for testing
        self.test_params = {
            'prior_knowledge': 0.2000,  # P(L0) - higher initial knowledge
            'learn_rate': 0.4000,       # P(T) - stronger learning
            'slip_rate': 0.0800,        # P(S) - lower slip rate
            'guess_rate': 0.2000        # P(G) - slightly lower guess rate
        }

        # Difficulty factors
        self.difficulty_factors = {
            'Beginner': 0.8,
            'Intermediate': 1.0,
            'Advanced': 1.2
        }

    def load_questions_data(self):
        """Load questions from the JSON file"""
        try:
            json_path = os.path.join(os.path.dirname(__file__), '..', 'database', 'data', 'number_algebra', 'number_algebra_beginner.json')
            with open(json_path, 'r', encoding='utf-8') as file:
                self.questions_data = json.load(file)
            print(f"[DATA LOADED] Successfully loaded {len(self.questions_data)} questions")
        except Exception as e:
            print(f"[ERROR] Failed to load questions data: {e}")
            sys.exit(1)

    def simulate_student_knowledge_trajectory(self, initial_knowledge, num_questions=50):
        """
        Simulate a student's knowledge trajectory with more realistic parameters

        Args:
            initial_knowledge: Float between 0 and 1 representing initial mastery
            num_questions: Number of questions to simulate

        Returns:
            Dictionary with simulation data
        """
        trajectory = {
            'true_knowledge': [],
            'predicted_knowledge': [],
            'actual_responses': [],
            'predicted_responses': [],
            'question_difficulties': [],
            'response_times': [],
            'time_factors': []
        }

        # Initialize BKT state
        current_bkt_prob = self.test_params['prior_knowledge']
        true_knowledge_level = initial_knowledge

        # More realistic learning parameters
        knowledge_growth_rate = 0.08  # Stronger learning effect
        knowledge_decay_rate = 0.01   # Minimal decay
        learning_momentum = 0.02      # Gradual knowledge increase over time

        for i in range(num_questions):
            # Select random question
            question = random.choice(self.questions_data)
            difficulty = question.get('difficulty_level', 'Beginner')
            difficulty_factor = self.difficulty_factors.get(difficulty, 1.0)

            # More realistic performance modeling
            # Base probability adjusted for individual differences
            base_performance = true_knowledge_level

            # Add some individual variation and question difficulty effects
            performance_variation = random.gauss(0, 0.1)  # Individual variation
            difficulty_penalty = 0.0

            if difficulty == 'Advanced':
                difficulty_penalty = 0.25  # Harder questions
            elif difficulty == 'Intermediate':
                difficulty_penalty = 0.10

            # Calculate actual performance probability
            effective_knowledge = max(0.0, min(1.0, base_performance + performance_variation - difficulty_penalty))

            # Apply BKT-style probability with realistic slip/guess
            true_correct_prob = (effective_knowledge * (1 - self.test_params['slip_rate']) +
                               (1 - effective_knowledge) * self.test_params['guess_rate'])

            # Generate actual response
            actual_correct = random.random() < true_correct_prob

            # More realistic response time simulation
            max_time = 45
            if actual_correct:
                # High knowledge students answer faster when correct
                if true_knowledge_level > 0.7:
                    response_time = random.uniform(8, max_time * 0.5)
                elif true_knowledge_level > 0.4:
                    response_time = random.uniform(15, max_time * 0.7)
                else:
                    response_time = random.uniform(20, max_time * 0.85)
            else:
                # Incorrect answers are generally slower
                response_time = random.uniform(max_time * 0.7, max_time * 0.95)

            # Calculate time factors
            time_score = self.bkt.calculate_time_score(response_time, max_time, actual_correct)
            time_factor = time_score

            # BKT prediction BEFORE seeing this response
            bkt_correct_prob = (current_bkt_prob * (1 - self.test_params['slip_rate']) +
                              (1 - current_bkt_prob) * self.test_params['guess_rate'])

            # Store data
            trajectory['true_knowledge'].append(true_knowledge_level)
            trajectory['predicted_knowledge'].append(current_bkt_prob)
            trajectory['actual_responses'].append(1 if actual_correct else 0)
            trajectory['predicted_responses'].append(bkt_correct_prob)
            trajectory['question_difficulties'].append(difficulty)
            trajectory['response_times'].append(response_time)
            trajectory['time_factors'].append(time_factor)

            # Update BKT knowledge state
            current_bkt_prob = self.bkt.calculate_bkt_update(
                current_bkt_prob, actual_correct, self.test_params,
                time_factor, difficulty_factor
            )

            # Update true knowledge with more realistic learning
            if actual_correct:
                # Stronger learning for correct answers, with diminishing returns
                learning_gain = knowledge_growth_rate * (1 - true_knowledge_level)
                true_knowledge_level = min(1.0, true_knowledge_level + learning_gain)
            else:
                # Minimal decay for wrong answers, but add small learning momentum
                true_knowledge_level = max(0.0, true_knowledge_level - knowledge_decay_rate)

            # Add gradual learning momentum (students improve slightly over time)
            true_knowledge_level = min(1.0, true_knowledge_level + learning_momentum * (1 - true_knowledge_level))

        return trajectory

    def calculate_knowledge_state_auc_roc(self, num_students=100, num_questions=30):
        """
        Calculate AUC-ROC for knowledge state prediction
        Tests how well BKT predicts true knowledge mastery levels
        """
        print("\n" + "="*70)
        print("BKT KNOWLEDGE STATE PREDICTION - AUC-ROC ANALYSIS")
        print("="*70)
        print(f"Testing with {num_students} simulated students, {num_questions} questions each")

        all_true_knowledge = []
        all_predicted_knowledge = []
        student_trajectories = []

        print(f"\n[SIMULATING] {num_students} student learning trajectories...")
        start_time = time.time()

        for student_id in range(num_students):
            # Random initial true knowledge level
            initial_knowledge = random.uniform(0.1, 0.9)

            # Simulate student trajectory
            trajectory = self.simulate_student_knowledge_trajectory(
                initial_knowledge, num_questions
            )

            # Collect final knowledge states for ROC analysis
            all_true_knowledge.extend(trajectory['true_knowledge'])
            all_predicted_knowledge.extend(trajectory['predicted_knowledge'])
            student_trajectories.append(trajectory)

            if (student_id + 1) % 20 == 0:
                progress = ((student_id + 1) / num_students) * 100
                print(f"[PROGRESS] {progress:.1f}% complete ({student_id + 1}/{num_students} students)")

        end_time = time.time()
        print(f"[COMPLETED] Simulation finished in {end_time - start_time:.2f} seconds")

        # Convert true knowledge to binary classification (mastery threshold)
        mastery_threshold = 0.7  # Students above this threshold are considered "mastered"
        true_mastery_binary = [1 if tk >= mastery_threshold else 0 for tk in all_true_knowledge]

        # Calculate AUC-ROC for knowledge state prediction
        try:
            knowledge_auc_roc = roc_auc_score(true_mastery_binary, all_predicted_knowledge)
            fpr, tpr, roc_thresholds = roc_curve(true_mastery_binary, all_predicted_knowledge)

            # Calculate Precision-Recall AUC
            precision, recall, pr_thresholds = precision_recall_curve(true_mastery_binary, all_predicted_knowledge)
            pr_auc = auc(recall, precision)

            print(f"\n[KNOWLEDGE STATE PREDICTION RESULTS]")
            print(f"Mastery threshold: {mastery_threshold}")
            print(f"Total data points: {len(all_true_knowledge)}")
            print(f"Students with mastery: {sum(true_mastery_binary)}")
            print(f"Students without mastery: {len(true_mastery_binary) - sum(true_mastery_binary)}")
            print(f"AUC-ROC Score: {knowledge_auc_roc:.4f}")
            print(f"Precision-Recall AUC: {pr_auc:.4f}")

            # Performance interpretation
            if knowledge_auc_roc >= 0.9:
                performance = "EXCELLENT"
            elif knowledge_auc_roc >= 0.8:
                performance = "GOOD"
            elif knowledge_auc_roc >= 0.7:
                performance = "FAIR"
            else:
                performance = "POOR"

            print(f"Knowledge State Prediction Performance: {performance}")

            return {
                'auc_roc': knowledge_auc_roc,
                'pr_auc': pr_auc,
                'fpr': fpr.tolist(),
                'tpr': tpr.tolist(),
                'precision': precision.tolist(),
                'recall': recall.tolist(),
                'mastery_threshold': mastery_threshold,
                'performance': performance,
                'trajectories': student_trajectories[:5]  # Sample trajectories
            }

        except Exception as e:
            print(f"[ERROR] Failed to calculate AUC-ROC: {e}")
            return None

    def calculate_response_prediction_auc_roc(self, num_students=100, num_questions=30):
        """
        Calculate AUC-ROC for response prediction
        Tests how well BKT predicts individual question responses
        """
        print("\n" + "="*70)
        print("BKT RESPONSE PREDICTION - AUC-ROC ANALYSIS")
        print("="*70)
        print(f"Testing response prediction accuracy with {num_students} students")

        all_actual_responses = []
        all_predicted_responses = []
        difficulty_breakdown = {'Beginner': [], 'Intermediate': [], 'Advanced': []}

        print(f"\n[ANALYZING] Response prediction accuracy...")
        start_time = time.time()

        for student_id in range(num_students):
            # Random initial knowledge level
            initial_knowledge = random.uniform(0.2, 0.8)

            # Simulate student trajectory
            trajectory = self.simulate_student_knowledge_trajectory(
                initial_knowledge, num_questions
            )

            # Collect response prediction data
            all_actual_responses.extend(trajectory['actual_responses'])
            all_predicted_responses.extend(trajectory['predicted_responses'])

            # Breakdown by difficulty
            for i, difficulty in enumerate(trajectory['question_difficulties']):
                if difficulty in difficulty_breakdown:
                    difficulty_breakdown[difficulty].append({
                        'actual': trajectory['actual_responses'][i],
                        'predicted': trajectory['predicted_responses'][i]
                    })

            if (student_id + 1) % 25 == 0:
                progress = ((student_id + 1) / num_students) * 100
                print(f"[PROGRESS] {progress:.1f}% complete ({student_id + 1}/{num_students} students)")

        end_time = time.time()
        print(f"[COMPLETED] Analysis finished in {end_time - start_time:.2f} seconds")

        # Calculate overall AUC-ROC for response prediction
        try:
            response_auc_roc = roc_auc_score(all_actual_responses, all_predicted_responses)
            fpr, tpr, roc_thresholds = roc_curve(all_actual_responses, all_predicted_responses)

            # Calculate Precision-Recall AUC
            precision, recall, pr_thresholds = precision_recall_curve(all_actual_responses, all_predicted_responses)
            pr_auc = auc(recall, precision)

            print(f"\n[RESPONSE PREDICTION RESULTS]")
            print(f"Total responses analyzed: {len(all_actual_responses)}")
            print(f"Correct responses: {sum(all_actual_responses)}")
            print(f"Incorrect responses: {len(all_actual_responses) - sum(all_actual_responses)}")
            print(f"AUC-ROC Score: {response_auc_roc:.4f}")
            print(f"Precision-Recall AUC: {pr_auc:.4f}")

            # Calculate accuracy at optimal threshold
            optimal_threshold_idx = np.argmax(tpr - fpr)
            optimal_threshold = roc_thresholds[optimal_threshold_idx]
            predicted_binary = [1 if p >= optimal_threshold else 0 for p in all_predicted_responses]
            accuracy = sum(1 for a, p in zip(all_actual_responses, predicted_binary) if a == p) / len(all_actual_responses)

            print(f"Optimal prediction threshold: {optimal_threshold:.4f}")
            print(f"Accuracy at optimal threshold: {accuracy:.4f}")

            # Performance interpretation
            if response_auc_roc >= 0.85:
                performance = "EXCELLENT"
            elif response_auc_roc >= 0.75:
                performance = "GOOD"
            elif response_auc_roc >= 0.65:
                performance = "FAIR"
            else:
                performance = "POOR"

            print(f"Response Prediction Performance: {performance}")

            # Analyze by difficulty level
            print(f"\n[DIFFICULTY LEVEL BREAKDOWN]")
            difficulty_results = {}

            for difficulty, data in difficulty_breakdown.items():
                if len(data) > 10:  # Ensure sufficient data
                    actual = [d['actual'] for d in data]
                    predicted = [d['predicted'] for d in data]

                    try:
                        diff_auc = roc_auc_score(actual, predicted)
                        difficulty_results[difficulty] = diff_auc
                        print(f"{difficulty:>12}: AUC-ROC = {diff_auc:.4f} ({len(data)} responses)")
                    except:
                        print(f"{difficulty:>12}: Insufficient variation for AUC calculation")

            return {
                'auc_roc': response_auc_roc,
                'pr_auc': pr_auc,
                'fpr': fpr.tolist(),
                'tpr': tpr.tolist(),
                'precision': precision.tolist(),
                'recall': recall.tolist(),
                'optimal_threshold': optimal_threshold,
                'accuracy': accuracy,
                'performance': performance,
                'difficulty_results': difficulty_results
            }

        except Exception as e:
            print(f"[ERROR] Failed to calculate response prediction AUC-ROC: {e}")
            return None

    def test_bkt_parameter_sensitivity(self):
        """
        Test how different BKT parameters affect AUC-ROC performance
        """
        print("\n" + "="*70)
        print("BKT PARAMETER SENSITIVITY ANALYSIS")
        print("="*70)
        print("Testing how parameter changes affect predictive accuracy")

        # Parameter variations to test
        parameter_tests = [
            {'name': 'Baseline', 'params': self.test_params},
            {'name': 'High Prior', 'params': {**self.test_params, 'prior_knowledge': 0.3}},
            {'name': 'Low Prior', 'params': {**self.test_params, 'prior_knowledge': 0.05}},
            {'name': 'High Learn Rate', 'params': {**self.test_params, 'learn_rate': 0.5}},
            {'name': 'Low Learn Rate', 'params': {**self.test_params, 'learn_rate': 0.1}},
            {'name': 'High Slip Rate', 'params': {**self.test_params, 'slip_rate': 0.2}},
            {'name': 'Low Slip Rate', 'params': {**self.test_params, 'slip_rate': 0.05}},
            {'name': 'High Guess Rate', 'params': {**self.test_params, 'guess_rate': 0.4}},
            {'name': 'Low Guess Rate', 'params': {**self.test_params, 'guess_rate': 0.1}}
        ]

        sensitivity_results = []

        for test in parameter_tests:
            print(f"\n[TESTING] {test['name']} parameters")
            print(f"  Prior: {test['params']['prior_knowledge']:.3f}, Learn: {test['params']['learn_rate']:.3f}")
            print(f"  Slip: {test['params']['slip_rate']:.3f}, Guess: {test['params']['guess_rate']:.3f}")

            # Temporarily update parameters
            original_params = self.test_params.copy()
            self.test_params = test['params']

            # Test with smaller sample for speed
            result = self.calculate_response_prediction_auc_roc(num_students=50, num_questions=20)

            # Restore original parameters
            self.test_params = original_params

            if result:
                sensitivity_results.append({
                    'name': test['name'],
                    'parameters': test['params'],
                    'auc_roc': result['auc_roc'],
                    'pr_auc': result['pr_auc'],
                    'accuracy': result['accuracy']
                })
                print(f"  Result: AUC-ROC = {result['auc_roc']:.4f}")

        # Display sensitivity analysis results
        print(f"\n[PARAMETER SENSITIVITY RESULTS]")
        print("-" * 70)
        print(f"{'Parameter Set':<15} {'AUC-ROC':<10} {'PR-AUC':<10} {'Accuracy':<10}")
        print("-" * 70)

        for result in sensitivity_results:
            print(f"{result['name']:<15} {result['auc_roc']:<10.4f} {result['pr_auc']:<10.4f} {result['accuracy']:<10.4f}")

        # Find best performing parameters
        best_result = max(sensitivity_results, key=lambda x: x['auc_roc'])
        print(f"\nBest performing parameter set: {best_result['name']} (AUC-ROC: {best_result['auc_roc']:.4f})")

        return sensitivity_results

    def run_comprehensive_auc_roc_analysis(self):
        """
        Run comprehensive AUC-ROC analysis for BKT algorithm
        """
        print("BKT ALGORITHM - COMPREHENSIVE AUC-ROC METRICS ANALYSIS")
        print("Testing predictive accuracy of student knowledge states and responses")
        print("="*70)

        # Test 1: Knowledge State Prediction
        knowledge_results = self.calculate_knowledge_state_auc_roc(num_students=100, num_questions=40)

        # Test 2: Response Prediction
        response_results = self.calculate_response_prediction_auc_roc(num_students=100, num_questions=40)

        # Test 3: Parameter Sensitivity
        sensitivity_results = self.test_bkt_parameter_sensitivity()

        # Generate comprehensive summary
        self.generate_comprehensive_summary(knowledge_results, response_results, sensitivity_results)

        return {
            'knowledge_prediction': knowledge_results,
            'response_prediction': response_results,
            'parameter_sensitivity': sensitivity_results
        }

    def generate_comprehensive_summary(self, knowledge_results, response_results, sensitivity_results):
        """
        Generate comprehensive summary of AUC-ROC analysis
        """
        print(f"\n" + "="*70)
        print("COMPREHENSIVE BKT AUC-ROC ANALYSIS SUMMARY")
        print("="*70)

        # Knowledge State Prediction Summary
        if knowledge_results:
            print(f"\n[KNOWLEDGE STATE PREDICTION]")
            print(f"AUC-ROC Score: {knowledge_results['auc_roc']:.4f}")
            print(f"Precision-Recall AUC: {knowledge_results['pr_auc']:.4f}")
            print(f"Performance Rating: {knowledge_results['performance']}")

        # Response Prediction Summary
        if response_results:
            print(f"\n[RESPONSE PREDICTION]")
            print(f"AUC-ROC Score: {response_results['auc_roc']:.4f}")
            print(f"Precision-Recall AUC: {response_results['pr_auc']:.4f}")
            print(f"Optimal Threshold: {response_results['optimal_threshold']:.4f}")
            print(f"Accuracy at Optimal Threshold: {response_results['accuracy']:.4f}")
            print(f"Performance Rating: {response_results['performance']}")

        # Parameter Sensitivity Summary
        if sensitivity_results:
            best_params = max(sensitivity_results, key=lambda x: x['auc_roc'])
            worst_params = min(sensitivity_results, key=lambda x: x['auc_roc'])

            print(f"\n[PARAMETER SENSITIVITY]")
            print(f"Best Parameters: {best_params['name']} (AUC-ROC: {best_params['auc_roc']:.4f})")
            print(f"Worst Parameters: {worst_params['name']} (AUC-ROC: {worst_params['auc_roc']:.4f})")
            print(f"Performance Range: {worst_params['auc_roc']:.4f} - {best_params['auc_roc']:.4f}")

        # Overall Assessment
        print(f"\n[OVERALL ASSESSMENT]")

        if knowledge_results and response_results:
            avg_performance = (knowledge_results['auc_roc'] + response_results['auc_roc']) / 2

            if avg_performance >= 0.85:
                overall_rating = "EXCELLENT"
                recommendation = "BKT algorithm shows excellent predictive accuracy"
            elif avg_performance >= 0.75:
                overall_rating = "GOOD"
                recommendation = "BKT algorithm shows good predictive accuracy"
            elif avg_performance >= 0.65:
                overall_rating = "FAIR"
                recommendation = "BKT algorithm shows fair predictive accuracy, consider parameter tuning"
            else:
                overall_rating = "POOR"
                recommendation = "BKT algorithm needs significant improvement"

            print(f"Average AUC-ROC Score: {avg_performance:.4f}")
            print(f"Overall Rating: {overall_rating}")
            print(f"Recommendation: {recommendation}")

        print(f"\n[TECHNICAL DETAILS]")
        print(f"Test Method: AUC-ROC (Area Under Receiver Operating Characteristic)")
        print(f"Evaluation Metrics: AUC-ROC, Precision-Recall AUC, Accuracy")
        print(f"Algorithm: Bayesian Knowledge Tracing with Time and Difficulty Factors")

def main():
    """Run the BKT AUC-ROC metrics analysis"""
    print("BKT ALGORITHM - AUC-ROC PREDICTIVE ACCURACY ANALYSIS")
    print("Testing student knowledge state and response prediction performance")

    analyzer = BKTAUCROCMetrics()
    results = analyzer.run_comprehensive_auc_roc_analysis()

    print(f"\n[ANALYSIS COMPLETE]")
    print(f"All AUC-ROC tests completed successfully!")

if __name__ == "__main__":
    main()