import json
import sys
import os
import time
import random
from collections import Counter
from math import sqrt
import statistics
from datetime import datetime, timedelta

# Add the public/algorithm directory to the Python path
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'public', 'algorithm'))

# Import the fisher_yates module
try:
    from fisher_yates import EnhancedFisherYatesShuffle
except ImportError as e:
    print(f"Error importing fisher_yates module: {e}")
    sys.exit(1)

class ComprehensiveAlgorithmMetrics:
    def __init__(self):
        self.questions_data = []
        self.fisher_yates = EnhancedFisherYatesShuffle()
        self.load_questions_data()

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

    def chi_square_test_fisher_yates(self, num_trials=1000, num_positions=10):
        """
        Perform Chi-Square test on Fisher-Yates shuffle algorithm
        Tests the randomness of the shuffling by checking position distribution
        """
        print("\n" + "="*70)
        print("FISHER-YATES ALGORITHM - CHI-SQUARE RANDOMNESS TEST")
        print("="*70)
        print(f"Testing shuffle randomness with {num_trials} trials")
        print(f"Analyzing first {num_positions} positions")

        # Create a subset of questions for testing
        test_questions = self.questions_data[:num_positions]
        question_ids = [q['question_id'] for q in test_questions]

        # Track position frequencies for each question ID
        position_counts = {qid: [0] * num_positions for qid in question_ids}

        # Run multiple shuffle trials
        print(f"\n[RUNNING] {num_trials} shuffle trials...")
        start_time = time.time()

        for trial in range(num_trials):
            # Create a copy of questions for shuffling
            questions_copy = test_questions.copy()

            # Apply Fisher-Yates shuffle
            shuffled = self.fisher_yates.fisher_yates_shuffle(questions_copy)

            # Record positions of each question ID
            for position, question in enumerate(shuffled):
                if position < num_positions:
                    position_counts[question['question_id']][position] += 1

            # Progress indicator
            if (trial + 1) % 200 == 0:
                progress = ((trial + 1) / num_trials) * 100
                print(f"[PROGRESS] {progress:.1f}% complete ({trial + 1}/{num_trials} trials)")

        end_time = time.time()
        print(f"[COMPLETED] All trials finished in {end_time - start_time:.2f} seconds")

        # Calculate Chi-Square statistics
        expected_frequency = num_trials / num_positions
        chi_square_values = []

        print(f"\nExpected frequency per position: {expected_frequency:.2f}")
        print(f"\nPosition Distribution Analysis (First 5 positions):")
        print("-" * 80)
        print(f"{'Question ID':<12} {'Pos 0':<8} {'Pos 1':<8} {'Pos 2':<8} {'Pos 3':<8} {'Pos 4':<8} {'Chi-Sq':<10}")
        print("-" * 80)

        total_chi_square = 0

        for qid in question_ids:
            counts = position_counts[qid][:5]  # Show first 5 positions

            # Calculate Chi-Square for this question
            chi_sq = sum((observed - expected_frequency) ** 2 / expected_frequency
                        for observed in position_counts[qid])
            chi_square_values.append(chi_sq)
            total_chi_square += chi_sq

            # Display position counts
            count_str = " ".join(f"{count:<8}" for count in counts)
            print(f"{qid:<12} {count_str} {chi_sq:<10.3f}")

        print("-" * 80)

        # Calculate overall statistics
        degrees_of_freedom = (num_positions - 1) * num_positions
        average_chi_square = total_chi_square / len(question_ids)

        # Interpretation
        critical_value_95 = degrees_of_freedom * 1.2  # Rough approximation

        if total_chi_square < critical_value_95:
            randomness_verdict = "PASS"
            randomness_score = "EXCELLENT"
        else:
            randomness_verdict = "FAIL"
            randomness_score = "POOR"

        # Additional uniformity analysis
        all_counts = [count for counts in position_counts.values() for count in counts]
        uniformity_variance = statistics.variance(all_counts)

        print(f"\n[CHI-SQUARE RESULTS]")
        print(f"Total Chi-Square Value: {total_chi_square:.3f}")
        print(f"Average Chi-Square per Question: {average_chi_square:.3f}")
        print(f"Degrees of Freedom: {degrees_of_freedom}")
        print(f"Critical Value (95% confidence): {critical_value_95:.3f}")
        print(f"Test Result: {randomness_verdict}")
        print(f"Randomness Score: {randomness_score}")
        print(f"Uniformity Variance: {uniformity_variance:.3f}")

        return {
            'total_chi_square': total_chi_square,
            'average_chi_square': average_chi_square,
            'degrees_of_freedom': degrees_of_freedom,
            'randomness_score': randomness_score,
            'randomness_verdict': randomness_verdict,
            'uniformity_variance': uniformity_variance,
            'execution_time': end_time - start_time
        }

    def simulate_cooldown_data(self, cooldown_percentage=0.3):
        """Simulate cooldown data for testing"""
        total_questions = len(self.questions_data)
        cooldown_count = int(total_questions * cooldown_percentage)

        cooldown_questions = random.sample(self.questions_data, cooldown_count)
        cooldown_data = []
        current_time = datetime.now()

        for i, question in enumerate(cooldown_questions):
            if i % 2 == 0:
                # Correct answer - 30 minute cooldown
                cooldown_time = current_time - timedelta(minutes=random.randint(1, 29))
                answer_correct = True
            else:
                # Incorrect answer - 60 minute cooldown
                cooldown_time = current_time - timedelta(minutes=random.randint(1, 59))
                answer_correct = False

            cooldown_data.append({
                'question_id': question['question_id'],
                'last_answered': cooldown_time.isoformat(),
                'was_correct': answer_correct,
                'cooldown_minutes': 30 if answer_correct else 60
            })

        return cooldown_data

    def create_ground_truth(self, cooldown_data, difficulty_filter=None, topic_filter=None):
        """Create ground truth of which questions should be available"""
        current_time = datetime.now()
        available_questions = []

        # Create set of question IDs in cooldown
        cooldown_ids = set()
        for cooldown in cooldown_data:
            last_answered = datetime.fromisoformat(cooldown['last_answered'])
            cooldown_minutes = cooldown['cooldown_minutes']

            time_diff = (current_time - last_answered).total_seconds() / 60
            if time_diff < cooldown_minutes:
                cooldown_ids.add(cooldown['question_id'])

        # Filter questions based on availability and other criteria
        for question in self.questions_data:
            if question['question_id'] in cooldown_ids:
                continue

            if difficulty_filter and question.get('difficulty_level') != difficulty_filter:
                continue

            if topic_filter and question.get('topic_tag') != topic_filter:
                continue

            available_questions.append(question)

        return available_questions, cooldown_ids

    def simulate_get_available_questions(self, cooldown_data, difficulty_filter=None, topic_filter=None):
        """Simulate the get_available_questions function behavior"""
        current_time = datetime.now()
        available_questions = []

        # Create cooldown lookup
        cooldown_lookup = {}
        for cooldown in cooldown_data:
            cooldown_lookup[cooldown['question_id']] = {
                'last_answered': datetime.fromisoformat(cooldown['last_answered']),
                'cooldown_minutes': cooldown['cooldown_minutes']
            }

        # Filter questions
        for question in self.questions_data:
            question_id = question['question_id']

            # Check cooldown status
            if question_id in cooldown_lookup:
                last_answered = cooldown_lookup[question_id]['last_answered']
                cooldown_minutes = cooldown_lookup[question_id]['cooldown_minutes']

                time_diff = (current_time - last_answered).total_seconds() / 60
                if time_diff < cooldown_minutes:
                    continue  # Skip - still in cooldown

            # Apply additional filters
            if difficulty_filter and question.get('difficulty_level') != difficulty_filter:
                continue

            if topic_filter and question.get('topic_tag') != topic_filter:
                continue

            available_questions.append(question)

        return available_questions

    def calculate_filtering_metrics(self, predicted_questions, ground_truth_questions):
        """Calculate F1, Precision, and Recall metrics"""
        predicted_ids = set(q['question_id'] for q in predicted_questions)
        ground_truth_ids = set(q['question_id'] for q in ground_truth_questions)

        true_positives = len(predicted_ids.intersection(ground_truth_ids))
        false_positives = len(predicted_ids - ground_truth_ids)
        false_negatives = len(ground_truth_ids - predicted_ids)

        precision = true_positives / (true_positives + false_positives) if (true_positives + false_positives) > 0 else 0
        recall = true_positives / (true_positives + false_negatives) if (true_positives + false_negatives) > 0 else 0
        f1_score = 2 * (precision * recall) / (precision + recall) if (precision + recall) > 0 else 0

        return {
            'true_positives': true_positives,
            'false_positives': false_positives,
            'false_negatives': false_negatives,
            'precision': precision,
            'recall': recall,
            'f1_score': f1_score,
            'predicted_count': len(predicted_questions),
            'ground_truth_count': len(ground_truth_questions)
        }

    def test_rule_based_filtering(self):
        """Test rule-based filtering algorithm with multiple scenarios"""
        print("\n" + "="*70)
        print("RULE-BASED FILTERING ALGORITHM - F1/PRECISION/RECALL TEST")
        print("="*70)
        print("Testing question filtering algorithm excluding cooldown questions")

        scenarios = [
            {"name": "Standard Cooldown", "cooldown_pct": 0.3},
            {"name": "High Cooldown Load", "cooldown_pct": 0.5},
            {"name": "Low Cooldown Load", "cooldown_pct": 0.1},
            {"name": "Extreme Cooldown", "cooldown_pct": 0.7}
        ]

        filtering_results = []

        for i, scenario in enumerate(scenarios, 1):
            print(f"\n[SCENARIO {i}] {scenario['name']} ({scenario['cooldown_pct']*100:.0f}% in cooldown)")
            print("-" * 50)

            # Generate cooldown data
            cooldown_data = self.simulate_cooldown_data(scenario['cooldown_pct'])

            # Create ground truth
            ground_truth, cooldown_ids = self.create_ground_truth(cooldown_data)

            # Test the filtering algorithm
            start_time = time.time()
            predicted = self.simulate_get_available_questions(cooldown_data)
            end_time = time.time()

            # Calculate metrics
            metrics = self.calculate_filtering_metrics(predicted, ground_truth)

            print(f"Total questions: {len(self.questions_data)}")
            print(f"Questions in cooldown: {len(cooldown_ids)}")
            print(f"Ground truth available: {metrics['ground_truth_count']}")
            print(f"Algorithm predicted: {metrics['predicted_count']}")
            print(f"Execution time: {(end_time - start_time)*1000:.2f}ms")

            print(f"Precision: {metrics['precision']:.4f}")
            print(f"Recall: {metrics['recall']:.4f}")
            print(f"F1 Score: {metrics['f1_score']:.4f}")

            # Performance classification
            if metrics['f1_score'] >= 0.95:
                performance = "EXCELLENT"
            elif metrics['f1_score'] >= 0.85:
                performance = "GOOD"
            elif metrics['f1_score'] >= 0.70:
                performance = "FAIR"
            else:
                performance = "POOR"

            print(f"Performance: {performance}")

            filtering_results.append({
                'scenario': scenario['name'],
                'metrics': metrics,
                'execution_time': end_time - start_time,
                'performance': performance
            })

        return filtering_results

    def run_comprehensive_test(self):
        """Run both Fisher-Yates and filtering algorithm tests"""
        print("COMPREHENSIVE ALGORITHM METRICS SIMULATION")
        print("Testing Fisher-Yates shuffling and rule-based question filtering")
        print("="*70)

        # Set random seed for reproducible results
        random.seed(42)

        # Test 1: Fisher-Yates Chi-Square Test
        fisher_yates_results = self.chi_square_test_fisher_yates(num_trials=1000, num_positions=10)

        # Test 2: Rule-Based Filtering Test
        filtering_results = self.test_rule_based_filtering()

        # Generate comprehensive summary
        self.generate_comprehensive_summary(fisher_yates_results, filtering_results)

        return {
            'fisher_yates': fisher_yates_results,
            'filtering': filtering_results
        }

    def generate_comprehensive_summary(self, fisher_yates_results, filtering_results):
        """Generate comprehensive summary of all tests"""
        print(f"\n" + "="*70)
        print("COMPREHENSIVE ALGORITHM TEST SUMMARY")
        print("="*70)

        # Fisher-Yates Summary
        print(f"\n[FISHER-YATES SHUFFLE ALGORITHM]")
        print(f"Randomness Test: {fisher_yates_results['randomness_verdict']}")
        print(f"Randomness Score: {fisher_yates_results['randomness_score']}")
        print(f"Chi-Square Value: {fisher_yates_results['total_chi_square']:.3f}")
        print(f"Execution Time: {fisher_yates_results['execution_time']:.2f}s")

        # Filtering Algorithm Summary
        avg_f1 = sum(r['metrics']['f1_score'] for r in filtering_results) / len(filtering_results)
        avg_precision = sum(r['metrics']['precision'] for r in filtering_results) / len(filtering_results)
        avg_recall = sum(r['metrics']['recall'] for r in filtering_results) / len(filtering_results)
        avg_exec_time = sum(r['execution_time'] for r in filtering_results) / len(filtering_results)

        print(f"\n[RULE-BASED FILTERING ALGORITHM]")
        print(f"Average F1 Score: {avg_f1:.4f}")
        print(f"Average Precision: {avg_precision:.4f}")
        print(f"Average Recall: {avg_recall:.4f}")
        print(f"Average Execution Time: {avg_exec_time*1000:.2f}ms")

        # Overall Assessment
        print(f"\n[OVERALL ASSESSMENT]")

        # Fisher-Yates assessment
        fy_status = "PASS" if fisher_yates_results['randomness_verdict'] == "PASS" else "FAIL"
        print(f"Fisher-Yates Shuffle: {fy_status}")

        # Filtering assessment
        if avg_f1 >= 0.95:
            filtering_status = "EXCELLENT"
        elif avg_f1 >= 0.85:
            filtering_status = "GOOD"
        elif avg_f1 >= 0.70:
            filtering_status = "FAIR"
        else:
            filtering_status = "POOR"

        print(f"Rule-Based Filtering: {filtering_status}")

        # Combined recommendation
        if fy_status == "PASS" and filtering_status in ["EXCELLENT", "GOOD"]:
            recommendation = "RECOMMENDED - Both algorithms perform well"
        elif fy_status == "PASS" or filtering_status in ["EXCELLENT", "GOOD"]:
            recommendation = "CONDITIONAL - One algorithm needs improvement"
        else:
            recommendation = "NOT RECOMMENDED - Both algorithms need improvement"

        print(f"\nRecommendation: {recommendation}")

        print(f"\n[DETAILED PERFORMANCE BREAKDOWN]")
        print(f"Scenarios Tested: {len(filtering_results)}")
        print(f"Total Questions Analyzed: {len(self.questions_data)}")

        best_filtering = max(filtering_results, key=lambda x: x['metrics']['f1_score'])
        worst_filtering = min(filtering_results, key=lambda x: x['metrics']['f1_score'])

        print(f"Best Filtering Scenario: {best_filtering['scenario']} (F1: {best_filtering['metrics']['f1_score']:.4f})")
        print(f"Worst Filtering Scenario: {worst_filtering['scenario']} (F1: {worst_filtering['metrics']['f1_score']:.4f})")

def main():
    """Run the comprehensive algorithm metrics test"""
    print("COMPREHENSIVE ALGORITHM METRICS SIMULATION")
    print("Fisher-Yates Shuffling + Rule-Based Question Filtering")

    simulation = ComprehensiveAlgorithmMetrics()
    results = simulation.run_comprehensive_test()

    print(f"\n[SIMULATION COMPLETE]")
    print(f"All algorithm tests completed successfully!")

if __name__ == "__main__":
    main()