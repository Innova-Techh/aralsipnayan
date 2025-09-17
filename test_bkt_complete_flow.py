#!/usr/bin/env python3
"""
Comprehensive test demonstrating the complete BKT calculation flow
"""

import sys
import os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), 'public', 'algorithm'))

from bkt_algorithm import BKTAlgorithm

def test_complete_bkt_flow():
    """Test the complete BKT calculation flow with step-by-step updates"""
    
    print("=== Complete BKT Calculation Flow Test ===")
    print()
    
    bkt = BKTAlgorithm()
    
    # Initial BKT parameters
    params = bkt.default_params
    print("BKT Parameters:")
    print(f"  Prior Knowledge (P(L0)): {params['prior_knowledge']}")
    print(f"  Learning Rate (P(T)): {params['learn_rate']}")
    print(f"  Slip Rate (P(S)): {params['slip_rate']}")
    print(f"  Guess Rate (P(G)): {params['guess_rate']}")
    print()
    
    # Difficulty factors
    print("Difficulty Factors:")
    for diff, factor in bkt.difficulty_factors.items():
        print(f"  {diff}: {factor}")
    print()
    
    # Sample question sequence from different difficulties
    questions = [
        # (response_time, max_allowed_time, is_correct, difficulty, description)
        (10, 30, True, 'beginner', "Q1: Fast correct answer on beginner"),
        (5, 30, False, 'beginner', "Q2: Fast incorrect answer on beginner"),
        (25, 30, True, 'beginner', "Q3: Slow correct answer on beginner"),
        (15, 45, True, 'intermediate', "Q4: Medium correct answer on intermediate"),
        (40, 45, False, 'intermediate', "Q5: Slow incorrect answer on intermediate"),
        (20, 60, True, 'advanced', "Q6: Fast correct answer on advanced"),
    ]
    
    # Track BKT progression
    current_bkt = params['prior_knowledge']
    total_time_score = 0
    
    print("Question-by-Question BKT Updates:")
    print("=" * 70)
    
    for i, (response_time, max_time, is_correct, difficulty, description) in enumerate(questions, 1):
        # Calculate time score
        time_score = bkt.calculate_time_score(response_time, max_time, is_correct)
        total_time_score += time_score
        
        # Calculate average time factor (ftime)
        ftime_factor = total_time_score / i
        
        # Get difficulty factor
        difficulty_factor = bkt.difficulty_factors[difficulty]
        
        # Calculate normalized time for display
        normalized_time = response_time / max_time
        
        print(f"\n{description}")
        print(f"  Response Time: {response_time}s / {max_time}s (normalized: {normalized_time:.2f})")
        print(f"  Time Score: {time_score}")
        print(f"  Difficulty Factor: {difficulty_factor}")
        print(f"  Average Time Factor (ftime): {ftime_factor:.3f}")
        print(f"  BKT Before: {current_bkt:.4f}")
        
        # Update BKT
        new_bkt = bkt.calculate_bkt_update(
            current_bkt, is_correct, params, ftime_factor, difficulty_factor
        )
        
        print(f"  BKT After: {new_bkt:.4f}")
        print(f"  BKT Change: {new_bkt - current_bkt:+.4f}")
        
        current_bkt = new_bkt
    
    # Calculate final mastery score
    print("\n" + "=" * 70)
    print("FINAL CALCULATION")
    print("=" * 70)
    
    correct_count = sum(1 for _, _, is_correct, _, _ in questions if is_correct)
    total_questions = len(questions)
    accuracy_score = correct_count / total_questions
    final_ftime = total_time_score / total_questions
    
    print(f"Total Questions: {total_questions}")
    print(f"Correct Answers: {correct_count}")
    print(f"Accuracy Score: {accuracy_score:.3f} ({accuracy_score*100:.1f}%)")
    print(f"Final BKT Score: {current_bkt:.4f}")
    print(f"Average Time Factor: {final_ftime:.3f}")
    print()
    
    # Calculate weighted mastery score
    mastery_result = bkt.calculate_mastery_score(accuracy_score, current_bkt)
    
    print("Mastery Score Calculation:")
    print(f"  Accuracy Component: {bkt.weights['accuracy']} × {accuracy_score:.3f} = {mastery_result['accuracy_component']:.3f}")
    print(f"  BKT Component: {bkt.weights['bkt']} × {current_bkt:.4f} = {mastery_result['bkt_component']:.3f}")
    print(f"  Final Mastery Score: ({mastery_result['accuracy_component']:.3f} + {mastery_result['bkt_component']:.3f}) × 100 = {mastery_result['final_score']:.1f}")
    print()
    
    # Determine difficulty level
    recommended_difficulty = bkt.determine_difficulty_level(mastery_result['final_score'])
    print(f"Recommended Difficulty Level: {recommended_difficulty}")
    
    # Show thresholds
    print("\nDifficulty Thresholds:")
    print("  Beginner: ≤ 75")
    print("  Intermediate: 76-84")
    print("  Advanced: ≥ 85")

if __name__ == '__main__':
    test_complete_bkt_flow()