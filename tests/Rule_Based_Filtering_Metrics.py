import json
import sys
import os
import time
import random
from datetime import datetime, timedelta
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
import numpy as np

# Add the public/algorithm directory to the Python path
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'public', 'algorithm'))

# Import the fisher_yates module
try:
    from fisher_yates import EnhancedFisherYatesShuffle
except ImportError as e:
    print(f"Error importing fisher_yates module: {e}")
    sys.exit(1)

class RuleBasedFilteringMetrics:
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

    def simulate_cooldown_data(self, cooldown_percentage=0.3):
        """
        Simulate cooldown data for testing
        Returns a list of question IDs that should be in cooldown
        """
        total_questions = len(self.questions_data)
        cooldown_count = int(total_questions * cooldown_percentage)

        # Randomly select questions for cooldown
        cooldown_questions = random.sample(self.questions_data, cooldown_count)

        # Create cooldown entries with different timestamps
        cooldown_data = []
        current_time = datetime.now()

        for i, question in enumerate(cooldown_questions):
            # Some questions have recent correct answers (30 min cooldown)
            # Some have recent incorrect answers (60 min cooldown)
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
        """
        Create ground truth of which questions should be available
        This represents the expected behavior of perfect filtering
        """
        current_time = datetime.now()
        available_questions = []

        # Create set of question IDs in cooldown
        cooldown_ids = set()
        for cooldown in cooldown_data:
            last_answered = datetime.fromisoformat(cooldown['last_answered'])
            cooldown_minutes = cooldown['cooldown_minutes']

            # Check if still in cooldown
            time_diff = (current_time - last_answered).total_seconds() / 60
            if time_diff < cooldown_minutes:
                cooldown_ids.add(cooldown['question_id'])

        # Filter questions based on availability and other criteria
        for question in self.questions_data:
            # Skip if in cooldown
            if question['question_id'] in cooldown_ids:
                continue

            # Apply difficulty filter if specified
            if difficulty_filter and question.get('difficulty_level') != difficulty_filter:
                continue

            # Apply topic filter if specified
            if topic_filter and question.get('topic_tag') != topic_filter:
                continue

            available_questions.append(question)

        return available_questions, cooldown_ids

    def test_filtering_algorithm(self, cooldown_data, difficulty_filter=None, topic_filter=None, error_rate=0.0):
        """
        Test the actual filtering algorithm from fisher_yates.py
        """
        try:
            filtered_questions = self.simulate_get_available_questions(
                cooldown_data, difficulty_filter, topic_filter, error_rate
            )
            return filtered_questions

        except Exception as e:
            print(f"[ERROR] Failed to test filtering algorithm: {e}")
            return []

    def simulate_get_available_questions(self, cooldown_data, difficulty_filter=None, topic_filter=None, introduce_error_rate=0.0):
        """
        Simulate the get_available_questions function behavior
        This mimics the actual algorithm from fisher_yates.py
        
        Args:
            cooldown_data: List of cooldown entries
            difficulty_filter: Filter by difficulty level
            topic_filter: Filter by topic
            introduce_error_rate: Rate at which to introduce errors (0.0 to 1.0)
                - 0.1 = 10% chance to miss a cooldown or incorrectly filter
        """
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
                
                # Simulate error: Sometimes miss cooldown checks
                if random.random() < introduce_error_rate:
                    # Error: Skip the cooldown check entirely (wrong behavior)
                    pass
                elif time_diff < cooldown_minutes:
                    continue  # Skip - still in cooldown

            # Apply additional filters
            if difficulty_filter and question.get('difficulty_level') != difficulty_filter:
                continue

            if topic_filter and question.get('topic_tag') != topic_filter:
                # Simulate filter error: Sometimes ignore topic filter
                if random.random() > introduce_error_rate:
                    continue

            available_questions.append(question)

        return available_questions

    def calculate_metrics(self, predicted_questions, ground_truth_questions, total_questions):
        """
        Calculate F1, Precision, Recall, and Accuracy metrics
        """
        # Convert to sets of question IDs for comparison
        predicted_ids = set(q['question_id'] for q in predicted_questions)
        ground_truth_ids = set(q['question_id'] for q in ground_truth_questions)

        # Calculate metrics
        true_positives = len(predicted_ids.intersection(ground_truth_ids))
        false_positives = len(predicted_ids - ground_truth_ids)
        false_negatives = len(ground_truth_ids - predicted_ids)
        true_negatives = total_questions - (true_positives + false_positives + false_negatives)

        # Calculate precision, recall, and F1
        precision = true_positives / (true_positives + false_positives) if (true_positives + false_positives) > 0 else 0
        recall = true_positives / (true_positives + false_negatives) if (true_positives + false_negatives) > 0 else 0
        f1_score = 2 * (precision * recall) / (precision + recall) if (precision + recall) > 0 else 0
        accuracy = (true_positives + true_negatives) / total_questions if total_questions > 0 else 0

        return {
            'true_positives': true_positives,
            'false_positives': false_positives,
            'false_negatives': false_negatives,
            'true_negatives': true_negatives,
            'precision': precision,
            'recall': recall,
            'f1_score': f1_score,
            'accuracy': accuracy,
            'predicted_count': len(predicted_questions),
            'ground_truth_count': len(ground_truth_questions)
        }

    def run_comprehensive_test(self):
        """
        Run comprehensive filtering algorithm test with multiple scenarios
        """
        print("\n" + "="*70)
        print("RULE-BASED FILTERING ALGORITHM METRICS TEST")
        print("="*70)
        print("Testing F1, Precision, Recall, and Accuracy for question filtering")
        print("Excludes questions in cooldown (30min correct, 60min incorrect)")

        # Generate 100 scenarios with varied cooldown percentages and filters
        scenarios = []
        
        # Base scenarios without filters (12 variations with NO errors)
        cooldown_percentages = [i * 0.01 for i in range(0, 101, 4)][:12]
        for i, cooldown_pct in enumerate(cooldown_percentages, 1):
            scenarios.append({
                "name": f"Base Perfect {i}: No Filters - {cooldown_pct*100:.0f}% Cooldown - No Errors",
                "difficulty": None,
                "topic": None,
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.0
            })
        
        # Base scenarios with LOW errors (2-5%)
        for i, cooldown_pct in enumerate(cooldown_percentages[:5], 1):
            scenarios.append({
                "name": f"Base Low-Error {i}: No Filters - {cooldown_pct*100:.0f}% Cooldown - 3% Errors",
                "difficulty": None,
                "topic": None,
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.03
            })
        
        # Base scenarios with MEDIUM errors (10-15%)
        for i, cooldown_pct in enumerate(cooldown_percentages[:5], 1):
            scenarios.append({
                "name": f"Base Medium-Error {i}: No Filters - {cooldown_pct*100:.0f}% Cooldown - 10% Errors",
                "difficulty": None,
                "topic": None,
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.10
            })
        
        # Base scenarios with HIGH errors (20-30%)
        for i, cooldown_pct in enumerate(cooldown_percentages[:5], 1):
            scenarios.append({
                "name": f"Base High-Error {i}: No Filters - {cooldown_pct*100:.0f}% Cooldown - 25% Errors",
                "difficulty": None,
                "topic": None,
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.25
            })
        
        # Difficulty filter scenarios with NO errors (10 variations)
        for i, cooldown_pct in enumerate(cooldown_percentages[:10], 1):
            scenarios.append({
                "name": f"Difficulty Perfect {i}: Beginner - {cooldown_pct*100:.0f}% Cooldown - No Errors",
                "difficulty": "Beginner",
                "topic": None,
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.0
            })
        
        # Difficulty filter with errors (10 variations)
        for i, cooldown_pct in enumerate(cooldown_percentages[:10], 1):
            scenarios.append({
                "name": f"Difficulty Errors {i}: Beginner - {cooldown_pct*100:.0f}% Cooldown - 5% Errors",
                "difficulty": "Beginner",
                "topic": None,
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.05
            })
        
        # Topic filter scenarios with NO errors (10 variations)
        for i, cooldown_pct in enumerate(cooldown_percentages[:10], 1):
            scenarios.append({
                "name": f"Topic Perfect {i}: Basic Operations - {cooldown_pct*100:.0f}% Cooldown - No Errors",
                "difficulty": None,
                "topic": "Basic Operations",
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.0
            })
        
        # Topic filter with errors (10 variations)
        for i, cooldown_pct in enumerate(cooldown_percentages[:10], 1):
            scenarios.append({
                "name": f"Topic Errors {i}: Basic Operations - {cooldown_pct*100:.0f}% Cooldown - 8% Errors",
                "difficulty": None,
                "topic": "Basic Operations",
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.08
            })
        
        # Combined filters with NO errors (13 variations)
        for i, cooldown_pct in enumerate(cooldown_percentages[:13], 1):
            scenarios.append({
                "name": f"Combined Perfect {i}: Beginner + Ops - {cooldown_pct*100:.0f}% Cooldown - No Errors",
                "difficulty": "Beginner",
                "topic": "Basic Operations",
                "cooldown_pct": cooldown_pct,
                "error_rate": 0.0
            })
        
        # Combined filters with VARYING errors (13 variations)
        error_rates_combined = [0.05, 0.10, 0.15, 0.05, 0.10, 0.15, 0.05, 0.10, 0.15, 0.05, 0.10, 0.15, 0.20]
        for i, (cooldown_pct, error_rate) in enumerate(zip(cooldown_percentages[:13], error_rates_combined), 1):
            scenarios.append({
                "name": f"Combined Errors {i}: Beginner + Ops - {cooldown_pct*100:.0f}% Cooldown - {error_rate*100:.0f}% Errors",
                "difficulty": "Beginner",
                "topic": "Basic Operations",
                "cooldown_pct": cooldown_pct,
                "error_rate": error_rate
            })

        overall_results = []
        total_scenarios = len(scenarios)

        for i, scenario in enumerate(scenarios, 1):
            # Print scenario header (less verbose for 100 scenarios)
            print(f"\n[{i}/{total_scenarios}] {scenario['name']}")

            # Generate cooldown data for this scenario
            cooldown_data = self.simulate_cooldown_data(scenario['cooldown_pct'])

            # Create ground truth
            ground_truth, cooldown_ids = self.create_ground_truth(
                cooldown_data, scenario['difficulty'], scenario['topic']
            )

            # Test the filtering algorithm
            start_time = time.time()
            predicted = self.test_filtering_algorithm(
                cooldown_data, scenario['difficulty'], scenario['topic'], scenario.get('error_rate', 0.0)
            )
            end_time = time.time()

            # Calculate metrics
            metrics = self.calculate_metrics(predicted, ground_truth, len(self.questions_data))

            # Compact display for 100 scenarios
            error_indicator = " [ERR]" if scenario.get('error_rate', 0.0) > 0 else ""
            print(f"  Available: {metrics['ground_truth_count']} | Predicted: {metrics['predicted_count']} | " +
                  f"F1: {metrics['f1_score']:.4f} | Acc: {metrics['accuracy']:.4f} | Time: {(end_time - start_time)*1000:.2f}ms{error_indicator}")

            # Performance classification
            if metrics['f1_score'] >= 0.95:
                performance = "EXCELLENT"
            elif metrics['f1_score'] >= 0.85:
                performance = "GOOD"
            elif metrics['f1_score'] >= 0.70:
                performance = "FAIR"
            else:
                performance = "POOR"

            overall_results.append({
                'scenario': scenario['name'],
                'metrics': metrics,
                'execution_time': end_time - start_time,
                'performance': performance
            })

        return overall_results

    def display_summary(self, results):
        """Display summary of all test results"""
        print(f"\n" + "="*70)
        print("FILTERING ALGORITHM TEST SUMMARY - 100 SCENARIOS")
        print("="*70)

        total_f1 = sum(r['metrics']['f1_score'] for r in results)
        avg_f1 = total_f1 / len(results)

        total_precision = sum(r['metrics']['precision'] for r in results)
        avg_precision = total_precision / len(results)

        total_recall = sum(r['metrics']['recall'] for r in results)
        avg_recall = total_recall / len(results)

        total_accuracy = sum(r['metrics']['accuracy'] for r in results)
        avg_accuracy = total_accuracy / len(results)

        avg_execution_time = sum(r['execution_time'] for r in results) / len(results)

        # Calculate standard deviations
        f1_variance = sum((r['metrics']['f1_score'] - avg_f1)**2 for r in results) / len(results)
        f1_std = f1_variance ** 0.5

        accuracy_variance = sum((r['metrics']['accuracy'] - avg_accuracy)**2 for r in results) / len(results)
        accuracy_std = accuracy_variance ** 0.5

        print(f"\nTotal Scenarios Tested: {len(results)}")
        print(f"\n--- AVERAGE METRICS ---")
        print(f"F1 Score: {avg_f1:.4f} (+/- {f1_std:.4f})")
        print(f"Precision: {avg_precision:.4f}")
        print(f"Recall: {avg_recall:.4f}")
        print(f"Accuracy: {avg_accuracy:.4f} (+/- {accuracy_std:.4f})")
        print(f"Execution Time: {avg_execution_time*1000:.2f}ms")

        # Count performance levels
        excellent_count = sum(1 for r in results if r['metrics']['f1_score'] >= 0.95)
        good_count = sum(1 for r in results if 0.85 <= r['metrics']['f1_score'] < 0.95)
        fair_count = sum(1 for r in results if 0.70 <= r['metrics']['f1_score'] < 0.85)
        poor_count = sum(1 for r in results if r['metrics']['f1_score'] < 0.70)

        print(f"\n--- PERFORMANCE DISTRIBUTION ---")
        print(f"Excellent (F1 >= 0.95): {excellent_count} scenarios ({excellent_count/len(results)*100:.1f}%)")
        print(f"Good (0.85 <= F1 < 0.95): {good_count} scenarios ({good_count/len(results)*100:.1f}%)")
        print(f"Fair (0.70 <= F1 < 0.85): {fair_count} scenarios ({fair_count/len(results)*100:.1f}%)")
        print(f"Poor (F1 < 0.70): {poor_count} scenarios ({poor_count/len(results)*100:.1f}%)")

        # Best and worst performing scenarios by F1 Score
        best_scenario = max(results, key=lambda x: x['metrics']['f1_score'])
        worst_scenario = min(results, key=lambda x: x['metrics']['f1_score'])

        # Best and worst by Accuracy
        best_accuracy = max(results, key=lambda x: x['metrics']['accuracy'])
        worst_accuracy = min(results, key=lambda x: x['metrics']['accuracy'])

        print(f"\n--- TOP PERFORMING SCENARIOS ---")
        print(f"Best F1: {best_scenario['scenario']} (F1: {best_scenario['metrics']['f1_score']:.4f})")
        print(f"Best Accuracy: {best_accuracy['scenario']} (Acc: {best_accuracy['metrics']['accuracy']:.4f})")

        print(f"\n--- LOWEST PERFORMING SCENARIOS ---")
        print(f"Worst F1: {worst_scenario['scenario']} (F1: {worst_scenario['metrics']['f1_score']:.4f})")
        print(f"Worst Accuracy: {worst_accuracy['scenario']} (Acc: {worst_accuracy['metrics']['accuracy']:.4f})")

        # Overall algorithm assessment
        if avg_f1 >= 0.95:
            assessment = "EXCELLENT - Algorithm performs very accurately across all scenarios"
        elif avg_f1 >= 0.85:
            assessment = "GOOD - Algorithm performs well with minor issues in some scenarios"
        elif avg_f1 >= 0.70:
            assessment = "FAIR - Algorithm needs improvement in several scenarios"
        else:
            assessment = "POOR - Algorithm has significant issues"

        print(f"\n--- OVERALL ASSESSMENT ---")
        print(f"{assessment}")
        print(f"Average Confidence (based on Accuracy): {avg_accuracy*100:.2f}%")

    def visualize_metrics(self, results):
        """Create comprehensive visualization of all metrics"""
        try:
            # Extract data for visualization
            scenario_names = [r['scenario'].split(':')[0] for r in results]  # Shorten names
            f1_scores = [r['metrics']['f1_score'] for r in results]
            accuracies = [r['metrics']['accuracy'] for r in results]
            precisions = [r['metrics']['precision'] for r in results]
            recalls = [r['metrics']['recall'] for r in results]
            execution_times = [r['execution_time'] * 1000 for r in results]  # Convert to ms
            
            # Create a large figure with subplots
            fig = plt.figure(figsize=(20, 14))
            fig.suptitle('Rule-Based Filtering Algorithm - Comprehensive Metrics Visualization', 
                        fontsize=18, fontweight='bold', y=0.995)
            
            # 1. F1 Score across all scenarios
            ax1 = plt.subplot(3, 3, 1)
            colors_f1 = ['green' if f1 >= 0.95 else 'orange' if f1 >= 0.85 else 'red' for f1 in f1_scores]
            ax1.bar(range(len(f1_scores)), f1_scores, color=colors_f1, alpha=0.7, edgecolor='black')
            ax1.axhline(y=np.mean(f1_scores), color='blue', linestyle='--', linewidth=2, label=f'Mean: {np.mean(f1_scores):.4f}')
            ax1.set_ylabel('F1 Score', fontweight='bold')
            ax1.set_title('F1 Score Distribution', fontweight='bold')
            ax1.set_ylim([0, 1.05])
            ax1.legend()
            ax1.grid(axis='y', alpha=0.3)
            
            # 2. Accuracy across all scenarios
            ax2 = plt.subplot(3, 3, 2)
            colors_acc = ['green' if acc >= 0.98 else 'yellow' if acc >= 0.95 else 'orange' for acc in accuracies]
            ax2.bar(range(len(accuracies)), accuracies, color=colors_acc, alpha=0.7, edgecolor='black')
            ax2.axhline(y=np.mean(accuracies), color='blue', linestyle='--', linewidth=2, label=f'Mean: {np.mean(accuracies):.4f}')
            ax2.set_ylabel('Accuracy', fontweight='bold')
            ax2.set_title('Accuracy Distribution', fontweight='bold')
            ax2.set_ylim([0.8, 1.05])
            ax2.legend()
            ax2.grid(axis='y', alpha=0.3)
            
            # 3. Precision across all scenarios
            ax3 = plt.subplot(3, 3, 3)
            colors_prec = ['green' if p >= 0.95 else 'orange' if p >= 0.85 else 'red' for p in precisions]
            ax3.bar(range(len(precisions)), precisions, color=colors_prec, alpha=0.7, edgecolor='black')
            ax3.axhline(y=np.mean(precisions), color='blue', linestyle='--', linewidth=2, label=f'Mean: {np.mean(precisions):.4f}')
            ax3.set_ylabel('Precision', fontweight='bold')
            ax3.set_title('Precision Distribution', fontweight='bold')
            ax3.set_ylim([0, 1.05])
            ax3.legend()
            ax3.grid(axis='y', alpha=0.3)
            
            # 4. Recall across all scenarios
            ax4 = plt.subplot(3, 3, 4)
            colors_rec = ['green' if r >= 0.95 else 'orange' if r >= 0.85 else 'red' for r in recalls]
            ax4.bar(range(len(recalls)), recalls, color=colors_rec, alpha=0.7, edgecolor='black')
            ax4.axhline(y=np.mean(recalls), color='blue', linestyle='--', linewidth=2, label=f'Mean: {np.mean(recalls):.4f}')
            ax4.set_ylabel('Recall', fontweight='bold')
            ax4.set_title('Recall Distribution', fontweight='bold')
            ax4.set_ylim([0, 1.05])
            ax4.legend()
            ax4.grid(axis='y', alpha=0.3)
            
            # 5. Execution Time
            ax5 = plt.subplot(3, 3, 5)
            ax5.bar(range(len(execution_times)), execution_times, color='skyblue', alpha=0.7, edgecolor='black')
            ax5.axhline(y=np.mean(execution_times), color='red', linestyle='--', linewidth=2, label=f'Mean: {np.mean(execution_times):.4f}ms')
            ax5.set_ylabel('Time (ms)', fontweight='bold')
            ax5.set_title('Execution Time Distribution', fontweight='bold')
            ax5.legend()
            ax5.grid(axis='y', alpha=0.3)
            
            # 6. Box plot comparison of all metrics
            ax6 = plt.subplot(3, 3, 6)
            box_data = [f1_scores, precisions, recalls, accuracies]
            bp = ax6.boxplot(box_data, labels=['F1 Score', 'Precision', 'Recall', 'Accuracy'], patch_artist=True)
            for patch in bp['boxes']:
                patch.set_facecolor('lightblue')
            ax6.set_ylabel('Score', fontweight='bold')
            ax6.set_title('Metrics Comparison (Box Plot)', fontweight='bold')
            ax6.set_ylim([0.7, 1.05])
            ax6.grid(axis='y', alpha=0.3)
            
            # 7. Performance distribution pie chart
            ax7 = plt.subplot(3, 3, 7)
            excellent_count = sum(1 for f1 in f1_scores if f1 >= 0.95)
            good_count = sum(1 for f1 in f1_scores if 0.85 <= f1 < 0.95)
            fair_count = sum(1 for f1 in f1_scores if 0.70 <= f1 < 0.85)
            poor_count = sum(1 for f1 in f1_scores if f1 < 0.70)
            
            sizes = [excellent_count, good_count, fair_count, poor_count]
            labels = [f'Excellent\n({excellent_count})', f'Good\n({good_count})', 
                     f'Fair\n({fair_count})', f'Poor\n({poor_count})']
            colors_pie = ['#2ecc71', '#f39c12', '#e74c3c', '#c0392b']
            wedges, texts, autotexts = ax7.pie(sizes, labels=labels, colors=colors_pie, autopct='%1.1f%%', startangle=90)
            ax7.set_title('Performance Distribution', fontweight='bold')
            for autotext in autotexts:
                autotext.set_color('white')
                autotext.set_fontweight('bold')
            
            # 8. F1 vs Accuracy scatter plot
            ax8 = plt.subplot(3, 3, 8)
            scatter = ax8.scatter(f1_scores, accuracies, c=execution_times, cmap='viridis', s=100, alpha=0.6, edgecolors='black')
            ax8.set_xlabel('F1 Score', fontweight='bold')
            ax8.set_ylabel('Accuracy', fontweight='bold')
            ax8.set_title('F1 Score vs Accuracy', fontweight='bold')
            ax8.grid(alpha=0.3)
            cbar = plt.colorbar(scatter, ax=ax8)
            cbar.set_label('Execution Time (ms)', fontweight='bold')
            
            # 9. Summary statistics table
            ax9 = plt.subplot(3, 3, 9)
            ax9.axis('off')
            
            summary_data = [
                ['Metric', 'Mean', 'Std Dev', 'Min', 'Max'],
                ['F1 Score', f'{np.mean(f1_scores):.4f}', f'{np.std(f1_scores):.4f}', 
                 f'{np.min(f1_scores):.4f}', f'{np.max(f1_scores):.4f}'],
                ['Accuracy', f'{np.mean(accuracies):.4f}', f'{np.std(accuracies):.4f}',
                 f'{np.min(accuracies):.4f}', f'{np.max(accuracies):.4f}'],
                ['Precision', f'{np.mean(precisions):.4f}', f'{np.std(precisions):.4f}',
                 f'{np.min(precisions):.4f}', f'{np.max(precisions):.4f}'],
                ['Recall', f'{np.mean(recalls):.4f}', f'{np.std(recalls):.4f}',
                 f'{np.min(recalls):.4f}', f'{np.max(recalls):.4f}'],
                ['Exec Time (ms)', f'{np.mean(execution_times):.4f}', f'{np.std(execution_times):.4f}',
                 f'{np.min(execution_times):.4f}', f'{np.max(execution_times):.4f}']
            ]
            
            table = ax9.table(cellText=summary_data, cellLoc='center', loc='center',
                             colWidths=[0.2, 0.2, 0.2, 0.2, 0.2])
            table.auto_set_font_size(False)
            table.set_fontsize(9)
            table.scale(1, 2)
            
            # Style header row
            for i in range(5):
                table[(0, i)].set_facecolor('#3498db')
                table[(0, i)].set_text_props(weight='bold', color='white')
            
            # Alternate row colors
            for i in range(1, len(summary_data)):
                for j in range(5):
                    if i % 2 == 0:
                        table[(i, j)].set_facecolor('#ecf0f1')
                    else:
                        table[(i, j)].set_facecolor('#ffffff')
            
            ax9.set_title('Summary Statistics', fontweight='bold', pad=20)
            
            plt.tight_layout()
            
            # Save the figure
            output_path = os.path.join(os.path.dirname(__file__), '..', 'public', 'metrics_visualization.png')
            os.makedirs(os.path.dirname(output_path), exist_ok=True)
            plt.savefig(output_path, dpi=150, bbox_inches='tight')
            print(f"\n[VISUALIZATION] Graph saved to: {output_path}")
            
            # Display the plot
            plt.show()
            
        except Exception as e:
            print(f"[ERROR] Failed to create visualization: {e}")
            import traceback
            traceback.print_exc()

def main():
    """Run the rule-based filtering metrics test"""
    print("RULE-BASED FILTERING ALGORITHM METRICS TEST")
    print("Testing F1, Precision, Recall, and Accuracy for question availability filtering")

    # Set random seed for reproducible results
    random.seed(42)

    test_runner = RuleBasedFilteringMetrics()
    results = test_runner.run_comprehensive_test()
    test_runner.display_summary(results)
    test_runner.visualize_metrics(results)

if __name__ == "__main__":
    main()