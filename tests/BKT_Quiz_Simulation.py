#!/usr/bin/env python3
"""
BKT Algorithm Quiz Simulation - AralSipnayan
Demonstrates the complete quiz process with actual formula calculations and BKT implementation
Shows step-by-step how the algorithm processes each question response
"""

import sys
import os
import time
import json
from decimal import Decimal, ROUND_HALF_UP

# Add the algorithm directory to path for imports
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'public', 'algorithm'))

try:
    from bkt_algorithm import BKTAlgorithm
except ImportError:
    print("Error: Could not import BKTAlgorithm. Please ensure the bkt_algorithm.py file is in the correct location.")
    sys.exit(1)

class QuizSimulation:
    """Simulates a complete quiz session with detailed BKT calculations"""

    def __init__(self):
        self.bkt = BKTAlgorithm()
        self.student_id = 123
        self.competency = "number_algebra"

        # Sample questions for the simulation
        self.diagnostic_questions = [
            # Phase 1: Beginner (5 questions)
            {
                'question_id': 'Q_DIAG_B001',
                'phase': 1,
                'difficulty': 'beginner',
                'question_text': 'What is 5 + 3?',
                'correct_answer': '8',
                'max_allowed_time': 30,
                'topic': 'Basic Addition'
            },
            {
                'question_id': 'Q_DIAG_B002',
                'phase': 1,
                'difficulty': 'beginner',
                'question_text': 'What is 12 - 7?',
                'correct_answer': '5',
                'max_allowed_time': 30,
                'topic': 'Basic Subtraction'
            },
            {
                'question_id': 'Q_DIAG_B003',
                'phase': 1,
                'difficulty': 'beginner',
                'question_text': 'What is 4 × 3?',
                'correct_answer': '12',
                'max_allowed_time': 30,
                'topic': 'Basic Multiplication'
            },
            {
                'question_id': 'Q_DIAG_B004',
                'phase': 1,
                'difficulty': 'beginner',
                'question_text': 'What is 15 ÷ 3?',
                'correct_answer': '5',
                'max_allowed_time': 30,
                'topic': 'Basic Division'
            },
            {
                'question_id': 'Q_DIAG_B005',
                'phase': 1,
                'difficulty': 'beginner',
                'question_text': 'What is 2 + 3 × 4?',
                'correct_answer': '14',
                'max_allowed_time': 30,
                'topic': 'Order of Operations'
            },

            # Phase 2: Intermediate (5 questions)
            {
                'question_id': 'Q_DIAG_I001',
                'phase': 2,
                'difficulty': 'intermediate',
                'question_text': 'Solve for x: 2x + 5 = 13',
                'correct_answer': '4',
                'max_allowed_time': 45,
                'topic': 'Linear Equations'
            },
            {
                'question_id': 'Q_DIAG_I002',
                'phase': 2,
                'difficulty': 'intermediate',
                'question_text': 'What is 25% of 80?',
                'correct_answer': '20',
                'max_allowed_time': 45,
                'topic': 'Percentages'
            },
            {
                'question_id': 'Q_DIAG_I003',
                'phase': 2,
                'difficulty': 'intermediate',
                'question_text': 'Simplify: 3(x + 2) - 2x',
                'correct_answer': 'x + 6',
                'max_allowed_time': 45,
                'topic': 'Algebraic Expressions'
            },
            {
                'question_id': 'Q_DIAG_I004',
                'phase': 2,
                'difficulty': 'intermediate',
                'question_text': 'What is the square root of 144?',
                'correct_answer': '12',
                'max_allowed_time': 45,
                'topic': 'Square Roots'
            },
            {
                'question_id': 'Q_DIAG_I005',
                'phase': 2,
                'difficulty': 'intermediate',
                'question_text': 'Convert 3/4 to a decimal',
                'correct_answer': '0.75',
                'max_allowed_time': 45,
                'topic': 'Fractions to Decimals'
            },

            # Phase 3: Advanced (5 questions)
            {
                'question_id': 'Q_DIAG_A001',
                'phase': 3,
                'difficulty': 'advanced',
                'question_text': 'Solve: x² - 5x + 6 = 0',
                'correct_answer': 'x = 2, 3',
                'max_allowed_time': 60,
                'topic': 'Quadratic Equations'
            },
            {
                'question_id': 'Q_DIAG_A002',
                'phase': 3,
                'difficulty': 'advanced',
                'question_text': 'Factor: 6x² + 11x + 3',
                'correct_answer': '(2x + 3)(3x + 1)',
                'max_allowed_time': 60,
                'topic': 'Factoring'
            },
            {
                'question_id': 'Q_DIAG_A003',
                'phase': 3,
                'difficulty': 'advanced',
                'question_text': 'Simplify: (x²y³)²/(xy)³',
                'correct_answer': 'x*y³',
                'max_allowed_time': 60,
                'topic': 'Exponent Rules'
            },
            {
                'question_id': 'Q_DIAG_A004',
                'phase': 3,
                'difficulty': 'advanced',
                'question_text': 'Find the slope of line through (2,3) and (6,11)',
                'correct_answer': '2',
                'max_allowed_time': 60,
                'topic': 'Slope Calculation'
            },
            {
                'question_id': 'Q_DIAG_A005',
                'phase': 3,
                'difficulty': 'advanced',
                'question_text': 'Solve system: 2x + y = 8, x - y = 1',
                'correct_answer': 'x = 3, y = 2',
                'max_allowed_time': 60,
                'topic': 'System of Equations'
            }
        ]

        # Simulated student responses (response_time, user_answer)
        self.student_responses = [
            # Phase 1: Beginner responses
            (12, '8'),      # Correct, fast
            (18, '5'),      # Correct, medium
            (8, '12'),      # Correct, fast
            (25, '4'),      # Wrong, slow
            (22, '14'),     # Correct, medium-slow

            # Phase 2: Intermediate responses
            (35, '4'),      # Correct, medium
            (28, '20'),     # Correct, fast-medium
            (42, 'x + 6'),  # Correct, slow
            (20, '12'),     # Correct, fast
            (38, '0.8'),    # Wrong, medium-slow

            # Phase 3: Advanced responses
            (55, 'x = 2, 3'),           # Correct, medium
            (48, '(2x + 3)(3x + 1)'),   # Correct, fast-medium
            (52, 'x*y³'),               # Correct, medium
            (45, '3'),                  # Wrong, fast-medium
            (58, 'x = 3, y = 2')        # Correct, medium-slow
        ]

        # Initialize tracking variables
        self.current_bkt = self.bkt.default_params['prior_knowledge']  # Start with prior knowledge
        self.cumulative_time_score = 0.0
        self.total_responses = 0
        self.correct_answers = 0
        self.phase_scores = {'phase_1': 0, 'phase_2': 0, 'phase_3': 0}
        self.phase_time_factors = {'phase_1': 0, 'phase_2': 0, 'phase_3': 0}

    def print_header(self):
        """Print simulation header"""
        print("="*100)
        print("ARALSIPNAYAN BKT ALGORITHM - DIAGNOSTIC QUIZ SIMULATION")
        print("="*100)
        print(f"Student ID: {self.student_id}")
        print(f"Competency: {self.competency.replace('_', ' ').title()}")
        print(f"Starting BKT Score: {self.current_bkt:.4f} ({self.current_bkt*100:.1f}%)")
        print("\nBKT Algorithm Parameters:")
        print(f"  • Prior Knowledge (P(L0)): {self.bkt.default_params['prior_knowledge']:.4f}")
        print(f"  • Learning Rate (P(T)):    {self.bkt.default_params['learn_rate']:.4f}")
        print(f"  • Slip Rate (P(S)):        {self.bkt.default_params['slip_rate']:.4f}")
        print(f"  • Guess Rate (P(G)):       {self.bkt.default_params['guess_rate']:.4f}")
        print("\nDifficulty Factors:")
        for diff, factor in self.bkt.difficulty_factors.items():
            print(f"  • {diff.title()}: {factor}")
        print("="*100)

    def calculate_question_metrics(self, question, response_time, user_answer):
        """Calculate all metrics for a single question response"""

        # Basic metrics
        is_correct = user_answer.lower().strip() == question['correct_answer'].lower().strip()
        max_time = question['max_allowed_time']
        normalized_time = response_time / max_time

        # Time score calculation using BKT algorithm
        time_score = self.bkt.calculate_time_score(response_time, max_time, is_correct)

        # Difficulty factor
        difficulty_factor = self.bkt.difficulty_factors[question['difficulty']]

        # Update cumulative time tracking
        self.cumulative_time_score += time_score
        self.total_responses += 1
        if is_correct:
            self.correct_answers += 1

        # Calculate current ftime factor (average time score)
        ftime_factor = self.cumulative_time_score / self.total_responses

        # Store BKT before update
        bkt_before = self.current_bkt

        # Update BKT using the algorithm
        self.current_bkt = self.bkt.calculate_bkt_update(
            bkt_before,
            is_correct,
            self.bkt.default_params,
            ftime_factor,
            difficulty_factor
        )

        return {
            'is_correct': is_correct,
            'response_time': response_time,
            'max_time': max_time,
            'normalized_time': normalized_time,
            'time_score': time_score,
            'difficulty_factor': difficulty_factor,
            'ftime_factor': ftime_factor,
            'bkt_before': bkt_before,
            'bkt_after': self.current_bkt
        }

    def print_question_details(self, question_num, question, response_time, user_answer, metrics):
        """Print detailed information for each question"""

        print(f"\n{'='*20} QUESTION {question_num} - PHASE {question['phase']} ({'='*20})")
        print(f"Difficulty: {question['difficulty'].title()}")
        print(f"Topic: {question['topic']}")
        print(f"Question: {question['question_text']}")
        print(f"Correct Answer: {question['correct_answer']}")
        print(f"Student Answer: {user_answer}")
        print(f"Result: {'[CORRECT]' if metrics['is_correct'] else '[INCORRECT]'}")

        print(f"\n[TIME ANALYSIS]:")
        print(f"  Response Time: {response_time}s / {metrics['max_time']}s")
        print(f"  Normalized Time: {metrics['normalized_time']:.3f} ({metrics['normalized_time']*100:.1f}%)")

        # Time category
        if metrics['normalized_time'] <= 0.5:
            time_category = "Fast (<=50%)"
        elif metrics['normalized_time'] <= 0.8:
            time_category = "Medium (50-80%)"
        else:
            time_category = "Slow (>80%)"
        print(f"  Time Category: {time_category}")
        print(f"  Time Score: {metrics['time_score']:.3f}")

        print(f"\n[BKT CALCULATION]:")
        print(f"  Prior BKT: {metrics['bkt_before']:.4f} ({metrics['bkt_before']*100:.1f}%)")
        print(f"  Difficulty Factor: {metrics['difficulty_factor']}")
        print(f"  Time Factor (ftime): {metrics['ftime_factor']:.4f}")

        # Show the actual BKT formula being used
        P_L = metrics['bkt_before']
        P_S = self.bkt.default_params['slip_rate']
        P_G = self.bkt.default_params['guess_rate']
        P_T = self.bkt.default_params['learn_rate']

        if metrics['is_correct']:
            print(f"\n  [BKT Formula (Correct Answer)]:")
            print(f"    P(Ln+1) = P(Ln)*(1-P(S))*ftime*fdifficulty / [P(Ln)*(1-P(S))+(1-P(Ln))*P(G)]")
            numerator = P_L * (1 - P_S) * metrics['ftime_factor'] * metrics['difficulty_factor']
            denominator = P_L * (1 - P_S) + (1 - P_L) * P_G
            print(f"    Numerator = {P_L:.4f}*(1-{P_S:.4f})*{metrics['ftime_factor']:.4f}*{metrics['difficulty_factor']:.1f}")
            print(f"              = {P_L:.4f}*{1-P_S:.4f}*{metrics['ftime_factor']:.4f}*{metrics['difficulty_factor']:.1f} = {numerator:.6f}")
            print(f"    Denominator = {P_L:.4f}*{1-P_S:.4f} + {1-P_L:.4f}*{P_G:.4f}")
            print(f"                = {P_L*(1-P_S):.6f} + {(1-P_L)*P_G:.6f} = {denominator:.6f}")
            posterior = numerator / denominator if denominator > 0 else P_L
            print(f"    P(L_posterior) = {numerator:.6f} / {denominator:.6f} = {posterior:.6f}")
        else:
            print(f"\n  [BKT Formula (Incorrect Answer)]:")
            print(f"    P(Ln+1) = P(Ln)*P(S) / [P(Ln)*P(S)+(1-P(Ln))*(1-P(G))*ftime*fdifficulty]")
            numerator = P_L * P_S
            denominator = P_L * P_S + (1 - P_L) * (1 - P_G) * metrics['ftime_factor'] * metrics['difficulty_factor']
            print(f"    Numerator = {P_L:.4f}*{P_S:.4f} = {numerator:.6f}")
            print(f"    Denominator = {P_L:.4f}*{P_S:.4f} + {1-P_L:.4f}*{1-P_G:.4f}*{metrics['ftime_factor']:.4f}*{metrics['difficulty_factor']:.1f}")
            print(f"                = {P_L*P_S:.6f} + {(1-P_L)*(1-P_G)*metrics['ftime_factor']*metrics['difficulty_factor']:.6f} = {denominator:.6f}")
            posterior = numerator / denominator if denominator > 0 else P_L
            print(f"    P(L_posterior) = {numerator:.6f} / {denominator:.6f} = {posterior:.6f}")

        # Learning transition
        final_bkt = posterior + (1 - posterior) * P_T
        print(f"    Learning Transition: {posterior:.6f} + (1 - {posterior:.6f}) * {P_T:.4f}")
        print(f"                        = {posterior:.6f} + {(1-posterior)*P_T:.6f} = {final_bkt:.6f}")

        print(f"  [RESULT] Updated BKT: {metrics['bkt_after']:.4f} ({metrics['bkt_after']*100:.1f}%)")

        bkt_change = metrics['bkt_after'] - metrics['bkt_before']
        change_direction = "[INCREASED]" if bkt_change > 0 else "[DECREASED]" if bkt_change < 0 else "[UNCHANGED]"
        print(f"  Change: {change_direction} by {abs(bkt_change):.4f} ({abs(bkt_change)*100:.2f}%)")

        print(f"\n[CUMULATIVE PROGRESS]:")
        print(f"  Total Questions: {self.total_responses}")
        print(f"  Correct Answers: {self.correct_answers}")
        print(f"  Current Accuracy: {self.correct_answers/self.total_responses*100:.1f}%")
        print(f"  Cumulative Time Score: {self.cumulative_time_score:.3f}")
        print(f"  Average Time Factor: {metrics['ftime_factor']:.4f}")

    def calculate_phase_results(self, phase_num, start_question, end_question):
        """Calculate phase-specific results"""

        phase_responses = self.total_responses - start_question + 1
        phase_correct = 0
        phase_time_scores = []

        # Count correct answers in this phase (simplified for simulation)
        if phase_num == 1:
            phase_correct = min(self.correct_answers, 5)
        elif phase_num == 2:
            phase_correct = max(0, min(self.correct_answers - 5, 5))
        else:  # phase 3
            phase_correct = max(0, min(self.correct_answers - 10, 5))

        phase_questions = min(5, end_question - start_question + 1)
        phase_accuracy = phase_correct / phase_questions if phase_questions > 0 else 0

        # Calculate phase time factor (simplified - would need actual phase-specific scores)
        phase_time_factor = self.cumulative_time_score / self.total_responses

        self.phase_scores[f'phase_{phase_num}'] = phase_accuracy
        self.phase_time_factors[f'phase_{phase_num}'] = phase_time_factor

        print(f"\n{'=' * 10} PHASE {phase_num} COMPLETE {'=' * 10}")
        print(f"Phase Accuracy: {phase_correct}/{phase_questions} = {phase_accuracy*100:.1f}%")
        print(f"Phase Time Factor: {phase_time_factor:.4f}")

        return phase_accuracy, phase_time_factor

    def calculate_final_results(self):
        """Calculate final diagnostic results"""

        print(f"\n{'=' * 20} FINAL DIAGNOSTIC RESULTS {'=' * 20}")

        # Final metrics
        final_accuracy = self.correct_answers / self.total_responses
        final_bkt = self.current_bkt
        average_time_factor = self.cumulative_time_score / self.total_responses

        print(f"\n[FINAL METRICS]:")
        print(f"  Total Questions Answered: {self.total_responses}")
        print(f"  Correct Answers: {self.correct_answers}")
        print(f"  Final Accuracy: {final_accuracy:.4f} ({final_accuracy*100:.1f}%)")
        print(f"  Final BKT Score: {final_bkt:.4f} ({final_bkt*100:.1f}%)")
        print(f"  Average Time Factor: {average_time_factor:.4f}")

        # Phase breakdown
        print(f"\n[PHASE BREAKDOWN]:")
        for phase in range(1, 4):
            phase_score = self.phase_scores[f'phase_{phase}']
            phase_time = self.phase_time_factors[f'phase_{phase}']
            phase_name = ['Beginner', 'Intermediate', 'Advanced'][phase-1]
            print(f"  Phase {phase} ({phase_name}): {phase_score*100:.1f}% accuracy, {phase_time:.4f} time factor")

        # Mastery score calculation
        mastery_result = self.bkt.calculate_mastery_score(final_accuracy, final_bkt)
        final_mastery_score = mastery_result['final_score']
        accuracy_component = mastery_result['accuracy_component']
        bkt_component = mastery_result['bkt_component']

        print(f"\n[MASTERY SCORE CALCULATION]:")
        print(f"  Formula: (55% * Accuracy) + (45% * BKT Score) * 100")
        print(f"  Accuracy Component: 0.55 * {final_accuracy:.4f} = {accuracy_component:.4f}")
        print(f"  BKT Component: 0.45 * {final_bkt:.4f} = {bkt_component:.4f}")
        print(f"  Final Mastery Score: ({accuracy_component:.4f} + {bkt_component:.4f}) * 100 = {final_mastery_score:.2f}%")

        # Difficulty level determination
        recommended_difficulty = self.bkt.determine_difficulty_level(final_mastery_score)

        print(f"\n[DIFFICULTY LEVEL DETERMINATION]:")
        print(f"  Mastery Score: {final_mastery_score:.2f}%")
        if final_mastery_score <= 75:
            print(f"  Level: BEGINNER (<=75%)")
        elif final_mastery_score <= 84:
            print(f"  Level: INTERMEDIATE (76-84%)")
        else:
            print(f"  Level: ADVANCED (>=85%)")
        print(f"  Recommended Difficulty: {recommended_difficulty.upper()}")

        # Summary
        print(f"\n{'=' * 50}")
        print(f"[DIAGNOSTIC ASSESSMENT COMPLETE]")
        print(f"Final Knowledge State: {final_bkt*100:.1f}%")
        print(f"Mastery Score: {final_mastery_score:.1f}%")
        print(f"Recommended Level: {recommended_difficulty.title()}")
        print(f"{'=' * 50}")

        return {
            'final_accuracy': final_accuracy,
            'final_bkt': final_bkt,
            'final_mastery_score': final_mastery_score,
            'recommended_difficulty': recommended_difficulty,
            'phase_scores': self.phase_scores,
            'total_responses': self.total_responses,
            'correct_answers': self.correct_answers
        }

    def run_simulation(self):
        """Run the complete diagnostic simulation"""

        self.print_header()

        # Process each question
        for i, (question, (response_time, user_answer)) in enumerate(zip(self.diagnostic_questions, self.student_responses)):
            question_num = i + 1

            # Calculate metrics for this question
            metrics = self.calculate_question_metrics(question, response_time, user_answer)

            # Print detailed analysis
            self.print_question_details(question_num, question, response_time, user_answer, metrics)

            # Check for phase completion
            if question_num in [5, 10, 15]:  # End of each phase
                phase_num = question_num // 5
                start_question = (phase_num - 1) * 5 + 1
                self.calculate_phase_results(phase_num, start_question, question_num)

        # Calculate and display final results
        final_results = self.calculate_final_results()

        return final_results

def main():
    """Main function to run the quiz simulation"""

    print("Starting BKT Algorithm Quiz Simulation...")
    print("This simulation demonstrates how the AralSipnayan BKT algorithm")
    print("processes student responses and calculates knowledge state.\n")

    # Create and run simulation
    simulation = QuizSimulation()
    results = simulation.run_simulation()

    # Optional: Save results to JSON file
    try:
        with open('bkt_simulation_results.json', 'w') as f:
            json.dump(results, f, indent=2, default=str)
        print(f"\n[SAVED] Results saved to: bkt_simulation_results.json")
    except Exception as e:
        print(f"\n[WARNING] Could not save results file: {e}")

    print(f"\n[SUCCESS] Simulation completed successfully!")
    return results

if __name__ == '__main__':
    """Run the simulation when executed directly"""
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n[STOPPED] Simulation interrupted by user.")
    except Exception as e:
        print(f"\n[ERROR] Error during simulation: {e}")
        import traceback
        traceback.print_exc()