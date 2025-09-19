import json
import sys
import os
import time
import random
from collections import Counter
from math import sqrt
import statistics

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