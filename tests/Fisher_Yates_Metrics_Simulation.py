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

    def calculate_shannon_entropy(self, position_counts, num_trials):
        """Calculate Shannon entropy for each question's position distribution"""
        entropies = {}
        max_entropy = 0
        
        for qid, counts in position_counts.items():
            # Convert counts to probabilities
            probabilities = [count / num_trials for count in counts if count > 0]
            
            # Calculate Shannon entropy: H = -Σ(p * log2(p))
            if probabilities:
                entropy = -sum(p * np.log2(p) for p in probabilities)
                entropies[qid] = entropy
                
                # Calculate maximum possible entropy (uniform distribution)
                n = len(counts)
                max_entropy = np.log2(n)
            else:
                entropies[qid] = 0
                
        return entropies, max_entropy
    
    def visualize_shannon_entropy(self, position_counts, num_trials, num_positions):
        """Create Shannon entropy visualization to show unpredictability"""
        question_ids = list(position_counts.keys())
        
        # Calculate Shannon entropy for each question
        entropies, max_entropy = self.calculate_shannon_entropy(position_counts, num_trials)
        
        # Create figure with multiple entropy visualizations
        fig, ((ax1, ax2), (ax3, ax4)) = plt.subplots(2, 2, figsize=(16, 12))
        
        # Plot 1: Shannon Entropy by Question
        entropy_values = [entropies[qid] for qid in question_ids]
        normalized_entropies = [e / max_entropy * 100 for e in entropy_values]
        
        colors = ['#2ecc71' if ne > 95 else '#f39c12' if ne > 90 else '#e74c3c' for ne in normalized_entropies]
        bars = ax1.bar(range(len(question_ids)), entropy_values, color=colors, alpha=0.7, edgecolor='black', linewidth=1.2)
        ax1.axhline(y=max_entropy, color='red', linestyle='--', linewidth=2, label=f'Max Entropy ({max_entropy:.3f})', alpha=0.7)
        ax1.axhline(y=max_entropy * 0.95, color='orange', linestyle=':', linewidth=1.5, label='95% of Max', alpha=0.5)
        ax1.set_xlabel('Question ID', fontsize=11, fontweight='bold')
        ax1.set_ylabel('Shannon Entropy (bits)', fontsize=11, fontweight='bold')
        ax1.set_title('Shannon Entropy by Question\n(Higher = More Unpredictable)', fontsize=12, fontweight='bold')
        ax1.set_xticks(range(len(question_ids)))
        ax1.set_xticklabels(question_ids, rotation=45, fontsize=9)
        ax1.legend()
        ax1.grid(axis='y', alpha=0.3)
        
        # Plot 2: Entropy Distribution Histogram
        ax2.hist(entropy_values, bins=20, color='steelblue', alpha=0.7, edgecolor='black')
        ax2.axvline(x=max_entropy, color='red', linestyle='--', linewidth=2, label=f'Max Entropy')
        ax2.axvline(x=np.mean(entropy_values), color='green', linestyle='-', linewidth=2, label=f'Mean ({np.mean(entropy_values):.3f})')
        ax2.set_xlabel('Entropy Value', fontsize=11, fontweight='bold')
        ax2.set_ylabel('Frequency', fontsize=11, fontweight='bold')
        ax2.set_title('Distribution of Entropy Values\n(Should cluster near maximum)', fontsize=12, fontweight='bold')
        ax2.legend()
        ax2.grid(True, alpha=0.3)
        
        # Plot 3: Entropy Efficiency (% of maximum possible)
        ax3.plot(range(len(question_ids)), normalized_entropies, marker='o', linewidth=2, markersize=8, color='steelblue', label='Actual Entropy %')
        ax3.axhline(y=100, color='red', linestyle='--', linewidth=2, label='Perfect Randomness (100%)', alpha=0.7)
        ax3.axhline(y=95, color='orange', linestyle=':', linewidth=1.5, label='Excellent Threshold (95%)', alpha=0.5)
        ax3.fill_between(range(len(question_ids)), normalized_entropies, alpha=0.3, color='steelblue')
        ax3.set_xlabel('Question ID', fontsize=11, fontweight='bold')
        ax3.set_ylabel('Entropy Efficiency (%)', fontsize=11, fontweight='bold')
        ax3.set_title('Entropy Efficiency Analysis\n(% of Maximum Possible Entropy)', fontsize=12, fontweight='bold')
        ax3.set_xticks(range(len(question_ids)))
        ax3.set_xticklabels(question_ids, rotation=45, fontsize=9)
        ax3.set_ylim([80, 105])
        ax3.legend()
        ax3.grid(True, alpha=0.3)
        
        # Plot 4: Entropy Statistics Panel
        ax4.axis('off')
        
        # Calculate comprehensive statistics
        mean_entropy = np.mean(entropy_values)
        std_entropy = np.std(entropy_values)
        min_entropy = min(entropy_values)
        max_entropy_actual = max(entropy_values)
        mean_efficiency = np.mean(normalized_entropies)
        
        # Predictability score based on entropy
        if mean_efficiency > 95:
            predictability = "HIGHLY UNPREDICTABLE (Excellent)"
        elif mean_efficiency > 90:
            predictability = "UNPREDICTABLE (Good)"
        elif mean_efficiency > 85:
            predictability = "MODERATELY UNPREDICTABLE (Fair)"
        else:
            predictability = "PREDICTABLE (Poor)"
        
        stats_text = f"""
SHANNON ENTROPY ANALYSIS RESULTS

Theoretical Background:
  • Shannon Entropy measures unpredictability
  • Higher entropy = More random/unpredictable
  • Maximum Entropy = log₂(positions) = {max_entropy:.3f} bits

Entropy Statistics:
  • Mean Entropy: {mean_entropy:.3f} bits
  • Std Deviation: {std_entropy:.3f} bits
  • Min Entropy: {min_entropy:.3f} bits
  • Max Entropy: {max_entropy_actual:.3f} bits

Efficiency Analysis:
  • Mean Efficiency: {mean_efficiency:.2f}%
  • Questions > 95%: {sum(1 for ne in normalized_entropies if ne > 95)}/{len(normalized_entropies)}
  • Questions > 90%: {sum(1 for ne in normalized_entropies if ne > 90)}/{len(normalized_entropies)}

Unpredictability Assessment:
  • Status: {predictability}
  • Information Loss: {(100 - mean_efficiency):.2f}%
  • Entropy Deficit: {max_entropy - mean_entropy:.4f} bits

Interpretation:
  Perfect shuffle → Entropy ≈ {max_entropy:.3f} bits
  Current shuffle → Entropy = {mean_entropy:.3f} bits
  Efficiency: {mean_efficiency:.1f}% of theoretical maximum
        """
        
        ax4.text(0.05, 0.95, stats_text, transform=ax4.transAxes, fontsize=10,
                verticalalignment='top', fontfamily='monospace',
                bbox=dict(boxstyle='round', facecolor='lightblue', alpha=0.3))
        
        plt.suptitle('Fisher-Yates Shuffle Unpredictability Analysis using Shannon Entropy', 
                     fontsize=14, fontweight='bold', y=1.02)
        plt.tight_layout()
        
        # Save figure
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_dir = os.path.join(os.path.dirname(__file__), 'visualizations')
        os.makedirs(output_dir, exist_ok=True)
        filepath = os.path.join(output_dir, f'fisher_yates_shannon_entropy_{timestamp}.png')
        plt.savefig(filepath, dpi=300, bbox_inches='tight')
        print(f"✓ Shannon entropy visualization saved to: {filepath}")
        plt.close()
        
        return filepath, mean_efficiency
    
    def visualize_chi_square_results(self, position_counts, num_trials, num_positions):
        """Create visualization for Chi-Square test results - showing only the bar chart and statistics"""
        question_ids = list(position_counts.keys())
        expected_frequency = num_trials / num_positions
        
        # Create figure with two subplots side by side
        fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(16, 8))
        
        # Subplot 1: Chi-Square Values by Question (Bar Chart)
        chi_square_values = []
        for qid in question_ids:
            chi_sq = sum((observed - expected_frequency) ** 2 / expected_frequency
                        for observed in position_counts[qid])
            chi_square_values.append(chi_sq)
        
        colors = ['#2ecc71' if cs < 15 else '#f39c12' if cs < 25 else '#e74c3c' for cs in chi_square_values]
        bars = ax1.bar(range(len(question_ids)), chi_square_values, color=colors, alpha=0.7, edgecolor='black', linewidth=1.2)
        ax1.axhline(y=15, color='orange', linestyle='--', linewidth=2, label='Threshold (15)', alpha=0.7)
        ax1.set_xlabel('Question ID', fontsize=11, fontweight='bold')
        ax1.set_ylabel('Chi-Square Value', fontsize=11, fontweight='bold')
        ax1.set_title('Chi-Square Values by Question\n(Lower = More Random)', fontsize=12, fontweight='bold')
        ax1.set_xticks(range(len(question_ids)))
        ax1.set_xticklabels(question_ids, rotation=45, fontsize=9)
        ax1.legend()
        ax1.grid(axis='y', alpha=0.3)
        
        # Subplot 2: Distribution Statistics (Text Panel)
        ax2.axis('off')
        
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
        
        ax2.text(0.05, 0.95, stats_text, transform=ax2.transAxes, fontsize=10,
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
        print(f"\n[VISUALIZING] Generating test visualizations...")
        entropy_efficiency = None
        try:
            # Generate Chi-Square visualization
            self.visualize_chi_square_results(position_counts, num_trials, num_positions)
            print("[SUCCESS] Chi-Square visualization generated successfully!")
            
            # Generate Shannon Entropy visualization
            print(f"\n[VISUALIZING] Generating Shannon Entropy analysis...")
            _, entropy_efficiency = self.visualize_shannon_entropy(position_counts, num_trials, num_positions)
            print("[SUCCESS] Shannon Entropy visualization generated successfully!")
            print(f"[ENTROPY] Mean entropy efficiency: {entropy_efficiency:.2f}% of theoretical maximum")
        except Exception as e:
            print(f"[WARNING] Could not generate visualizations: {e}")

        return {
            'total_chi_square': total_chi_square,
            'average_chi_square': average_chi_square,
            'degrees_of_freedom': degrees_of_freedom,
            'randomness_score': randomness_score,
            'uniformity_variance': uniformity_variance,
            'execution_time': end_time - start_time,
            'entropy_efficiency': entropy_efficiency
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