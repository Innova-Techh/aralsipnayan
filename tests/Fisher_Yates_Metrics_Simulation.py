import json
import sys
import os
import time
import random
from collections import Counter
from math import sqrt
import statistics
import matplotlib
matplotlib.use('Agg')  # Use non-interactive backend
import matplotlib.pyplot as plt
import numpy as np
from datetime import datetime

# Add the public/algorithm directory to the Python path
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'public', 'algorithm'))

# Import the fisher_yates module
try:
    from fisher_yates import EnhancedFisherYatesShuffle
except ImportError as e:
    print(f"Error importing fisher_yates module: {e}")
    sys.exit(1)

class FisherYatesMetricsSimulation:
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

    def visualize_chi_square_results(self, position_counts, num_trials, num_positions):
        """Create visualization for Chi-Square test results"""
        question_ids = list(position_counts.keys())
        expected_frequency = num_trials / num_positions
        
        # Create figure with multiple subplots
        fig = plt.figure(figsize=(16, 10))
        
        # Subplot 1: Position Distribution Heatmap
        ax1 = plt.subplot(2, 2, 1)
        data_matrix = np.array([position_counts[qid] for qid in question_ids])
        im = ax1.imshow(data_matrix, cmap='YlOrRd', aspect='auto')
        ax1.set_xlabel('Position', fontsize=11, fontweight='bold')
        ax1.set_ylabel('Question ID', fontsize=11, fontweight='bold')
        ax1.set_title('Position Distribution Heatmap\n(Darker = More Frequent)', fontsize=12, fontweight='bold')
        ax1.set_xticks(range(num_positions))
        ax1.set_yticks(range(len(question_ids)))
        ax1.set_yticklabels(question_ids, fontsize=8)
        plt.colorbar(im, ax=ax1, label='Frequency Count')
        
        # Subplot 2: Chi-Square Values by Question
        ax2 = plt.subplot(2, 2, 2)
        chi_square_values = []
        for qid in question_ids:
            chi_sq = sum((observed - expected_frequency) ** 2 / expected_frequency
                        for observed in position_counts[qid])
            chi_square_values.append(chi_sq)
        
        colors = ['#2ecc71' if cs < 15 else '#f39c12' if cs < 25 else '#e74c3c' for cs in chi_square_values]
        bars = ax2.bar(range(len(question_ids)), chi_square_values, color=colors, alpha=0.7, edgecolor='black', linewidth=1.2)
        ax2.axhline(y=15, color='orange', linestyle='--', linewidth=2, label='Threshold (15)', alpha=0.7)
        ax2.set_xlabel('Question ID', fontsize=11, fontweight='bold')
        ax2.set_ylabel('Chi-Square Value', fontsize=11, fontweight='bold')
        ax2.set_title('Chi-Square Values by Question\n(Lower = More Random)', fontsize=12, fontweight='bold')
        ax2.set_xticks(range(len(question_ids)))
        ax2.set_xticklabels(question_ids, rotation=45, fontsize=9)
        ax2.legend()
        ax2.grid(axis='y', alpha=0.3)
        
        # Subplot 3: Position Distribution Line Chart
        ax3 = plt.subplot(2, 2, 3)
        for i, qid in enumerate(question_ids):
            ax3.plot(range(num_positions), position_counts[qid], marker='o', label=qid, linewidth=2, markersize=6)
        ax3.axhline(y=expected_frequency, color='red', linestyle='--', linewidth=2, label=f'Expected ({expected_frequency:.1f})', alpha=0.8)
        ax3.set_xlabel('Position', fontsize=11, fontweight='bold')
        ax3.set_ylabel('Frequency', fontsize=11, fontweight='bold')
        ax3.set_title('Position Frequency Distribution\n(Should be near expected line)', fontsize=12, fontweight='bold')
        ax3.legend(loc='best', fontsize=9)
        ax3.grid(True, alpha=0.3)
        
        # Subplot 4: Distribution Statistics
        ax4 = plt.subplot(2, 2, 4)
        ax4.axis('off')
        
        # Calculate statistics
        all_counts = [count for counts in position_counts.values() for count in counts]
        uniformity_variance = statistics.variance(all_counts)
        uniformity_std = sqrt(uniformity_variance)
        total_chi_square = sum(chi_square_values)
        avg_chi_square = total_chi_square / len(question_ids)
        cv = (uniformity_std / expected_frequency) * 100
        
        # Create statistics text
        stats_text = f"""
FISHER-YATES CHI-SQUARE TEST RESULTS

Test Configuration:
  • Number of Trials: {num_trials:,}
  • Positions Analyzed: {num_positions}
  • Expected Frequency: {expected_frequency:.2f}

Chi-Square Statistics:
  • Total Chi-Square: {total_chi_square:.3f}
  • Average Chi-Square: {avg_chi_square:.3f}
  • Min Chi-Square: {min(chi_square_values):.3f}
  • Max Chi-Square: {max(chi_square_values):.3f}

Uniformity Analysis:
  • Variance: {uniformity_variance:.3f}
  • Std Deviation: {uniformity_std:.3f}
  • Coefficient of Variation: {cv:.2f}%

Randomness Assessment:
  • Status: {'✓ PASS - Excellent Randomness' if total_chi_square < 15*num_positions else '✗ FAIL - Poor Randomness'}
  • Quality: {'EXCELLENT' if cv < 10 else 'GOOD' if cv < 15 else 'FAIR' if cv < 20 else 'POOR'}
        """
        
        ax4.text(0.05, 0.95, stats_text, transform=ax4.transAxes, fontsize=10,
                verticalalignment='top', fontfamily='monospace',
                bbox=dict(boxstyle='round', facecolor='wheat', alpha=0.5))
        
        plt.tight_layout()
        
        # Save figure
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_dir = os.path.join(os.path.dirname(__file__), 'visualizations')
        os.makedirs(output_dir, exist_ok=True)
        filepath = os.path.join(output_dir, f'fisher_yates_chi_square_{timestamp}.png')
        plt.savefig(filepath, dpi=300, bbox_inches='tight')
        print(f"\n✓ Chi-Square visualization saved to: {filepath}")
        plt.close()
        
        return filepath
    
    def visualize_randomness_quality(self, position_counts, num_trials, num_positions):
        """Create detailed randomness quality visualization"""
        question_ids = list(position_counts.keys())
        expected_frequency = num_trials / num_positions
        
        fig, axes = plt.subplots(2, 2, figsize=(14, 10))
        
        # Plot 1: Expected vs Actual Frequencies (Bar Chart)
        ax1 = axes[0, 0]
        all_actual_freqs = []
        for qid in question_ids:
            for pos in range(num_positions):
                all_actual_freqs.append(position_counts[qid][pos])
        
        freq_bins = np.linspace(min(all_actual_freqs), max(all_actual_freqs), 15)
        ax1.hist(all_actual_freqs, bins=freq_bins, color='steelblue', alpha=0.7, edgecolor='black')
        ax1.axvline(x=expected_frequency, color='red', linestyle='--', linewidth=2, label=f'Expected ({expected_frequency:.1f})')
        ax1.set_xlabel('Frequency Count', fontsize=11, fontweight='bold')
        ax1.set_ylabel('Number of Occurrences', fontsize=11, fontweight='bold')
        ax1.set_title('Frequency Distribution Histogram\n(Should be centered at expected line)', fontsize=12, fontweight='bold')
        ax1.legend()
        ax1.grid(True, alpha=0.3)
        
        # Plot 2: Q-Q Plot for Normality Check
        ax2 = axes[0, 1]
        sorted_freqs = sorted(all_actual_freqs)
        theoretical_quantiles = np.sort(np.random.normal(expected_frequency, np.std(all_actual_freqs), len(sorted_freqs)))
        ax2.scatter(theoretical_quantiles, sorted_freqs, alpha=0.6, s=50, color='steelblue', edgecolor='black')
        ax2.plot([sorted_freqs[0], sorted_freqs[-1]], [sorted_freqs[0], sorted_freqs[-1]], 'r--', linewidth=2, label='Perfect Fit')
        ax2.set_xlabel('Theoretical Quantiles', fontsize=11, fontweight='bold')
        ax2.set_ylabel('Actual Quantiles', fontsize=11, fontweight='bold')
        ax2.set_title('Q-Q Plot: Normality Assessment\n(Points close to line = more normal)', fontsize=12, fontweight='bold')
        ax2.legend()
        ax2.grid(True, alpha=0.3)
        
        # Plot 3: Position Deviation from Expected
        ax3 = axes[1, 0]
        deviations = []
        position_labels = []
        for pos in range(num_positions):
            pos_freqs = [position_counts[qid][pos] for qid in question_ids]
            avg_pos_freq = np.mean(pos_freqs)
            deviation = avg_pos_freq - expected_frequency
            deviations.append(deviation)
            position_labels.append(f'Pos {pos}')
        
        colors_dev = ['#2ecc71' if abs(d) < 5 else '#f39c12' if abs(d) < 10 else '#e74c3c' for d in deviations]
        ax3.bar(position_labels, deviations, color=colors_dev, alpha=0.7, edgecolor='black')
        ax3.axhline(y=0, color='black', linestyle='-', linewidth=1)
        ax3.set_ylabel('Deviation from Expected', fontsize=11, fontweight='bold')
        ax3.set_title('Position-wise Deviation Analysis\n(Ideally all should be near 0)', fontsize=12, fontweight='bold')
        ax3.grid(axis='y', alpha=0.3)
        
        # Plot 4: Cumulative Chi-Square Distribution
        ax4 = axes[1, 1]
        chi_square_values = []
        for qid in question_ids:
            chi_sq = sum((observed - expected_frequency) ** 2 / expected_frequency
                        for observed in position_counts[qid])
            chi_square_values.append(chi_sq)
        
        chi_square_cumsum = np.cumsum(sorted(chi_square_values))
        ax4.plot(range(len(chi_square_cumsum)), chi_square_cumsum, marker='o', linewidth=2, markersize=6, color='steelblue')
        ax4.fill_between(range(len(chi_square_cumsum)), chi_square_cumsum, alpha=0.3, color='steelblue')
        ax4.set_xlabel('Question Number (sorted by Chi-Square)', fontsize=11, fontweight='bold')
        ax4.set_ylabel('Cumulative Chi-Square', fontsize=11, fontweight='bold')
        ax4.set_title('Cumulative Chi-Square Distribution\n(Smoother curve = better randomness)', fontsize=12, fontweight='bold')
        ax4.grid(True, alpha=0.3)
        
        plt.tight_layout()
        
        # Save figure
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_dir = os.path.join(os.path.dirname(__file__), 'visualizations')
        os.makedirs(output_dir, exist_ok=True)
        filepath = os.path.join(output_dir, f'fisher_yates_randomness_quality_{timestamp}.png')
        plt.savefig(filepath, dpi=300, bbox_inches='tight')
        print(f"✓ Randomness quality visualization saved to: {filepath}")
        plt.close()
        
        return filepath


    def chi_square_test_fisher_yates(self, num_trials=1000, num_positions=10):
        """
        Perform Chi-Square test on Fisher-Yates shuffle algorithm
        Tests the randomness of the shuffling by checking position distribution
        """
        print("\n" + "="*60)
        print("FISHER-YATES CHI-SQUARE TEST SIMULATION")
        print("="*60)
        print(f"Testing randomness with {num_trials} trials")
        print(f"Using first {num_positions} positions for analysis")

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
            if (trial + 1) % 100 == 0:
                progress = ((trial + 1) / num_trials) * 100
                print(f"[PROGRESS] {progress:.1f}% complete ({trial + 1}/{num_trials} trials)")

        end_time = time.time()
        print(f"[COMPLETED] All trials finished in {end_time - start_time:.2f} seconds")

        # Calculate Chi-Square statistics
        print(f"\n[ANALYSIS] Calculating Chi-Square statistics...")

        expected_frequency = num_trials / num_positions
        chi_square_values = []

        print(f"\nExpected frequency per position: {expected_frequency:.2f}")
        print(f"\nPosition Distribution Analysis:")
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

        print(f"\n[RESULTS] Chi-Square Test Results:")
        print(f"Total Chi-Square Value: {total_chi_square:.3f}")
        print(f"Average Chi-Square per Question: {average_chi_square:.3f}")
        print(f"Degrees of Freedom: {degrees_of_freedom}")
        print(f"Standard Deviation of Chi-Square values: {statistics.stdev(chi_square_values):.3f}")

        # Interpretation (basic guidelines)
        critical_value_95 = degrees_of_freedom * 1.2  # Rough approximation

        print(f"\n[INTERPRETATION]")
        print(f"Critical value (95% confidence, approx): {critical_value_95:.3f}")

        if total_chi_square < critical_value_95:
            print("[PASS] Fisher-Yates shuffle appears to be random (Chi-Square < Critical Value)")
            randomness_score = "EXCELLENT"
        else:
            print("[FAIL] Fisher-Yates shuffle may not be sufficiently random")
            randomness_score = "POOR"

        # Additional uniformity analysis
        all_counts = [count for counts in position_counts.values() for count in counts]
        uniformity_variance = statistics.variance(all_counts)
        uniformity_std = sqrt(uniformity_variance)

        print(f"\nUniformity Analysis:")
        print(f"Variance in position frequencies: {uniformity_variance:.3f}")
        print(f"Standard deviation: {uniformity_std:.3f}")
        print(f"Coefficient of variation: {(uniformity_std / expected_frequency) * 100:.2f}%")

        # Generate visualizations
        print(f"\n[VISUALIZING] Generating Chi-Square test visualizations...")
        try:
            self.visualize_chi_square_results(position_counts, num_trials, num_positions)
            self.visualize_randomness_quality(position_counts, num_trials, num_positions)
            print("[SUCCESS] All visualizations generated successfully!")
        except Exception as e:
            print(f"[WARNING] Could not generate visualizations: {e}")

        return {
            'total_chi_square': total_chi_square,
            'average_chi_square': average_chi_square,
            'degrees_of_freedom': degrees_of_freedom,
            'randomness_score': randomness_score,
            'uniformity_variance': uniformity_variance,
            'execution_time': end_time - start_time
        }

def main():
    """Run the Fisher-Yates Chi-Square test simulation"""
    print("FISHER-YATES ALGORITHM METRICS SIMULATION")
    print("Testing randomness of shuffling algorithm using Chi-Square test")

    simulation = FisherYatesMetricsSimulation()

    # Run Chi-Square test with different parameters
    results = simulation.chi_square_test_fisher_yates(num_trials=1000, num_positions=10)

    print(f"\n" + "="*60)
    print("SIMULATION SUMMARY")
    print("="*60)
    print(f"Algorithm: Fisher-Yates Shuffle")
    print(f"Test Method: Chi-Square Test for Randomness")
    print(f"Randomness Score: {results['randomness_score']}")
    print(f"Total Chi-Square: {results['total_chi_square']:.3f}")
    print(f"Execution Time: {results['execution_time']:.2f} seconds")
    print(f"Uniformity Variance: {results['uniformity_variance']:.3f}")

if __name__ == "__main__":
    main()