#!/usr/bin/env python3
"""
Test script for the updated BKT Algorithm calculations
This script will test the new mastery calculation formulas
"""

import sys
import os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), 'public', 'algorithm'))

from bkt_algorithm import BKTAlgorithm

def test_time_score_calculation():
    """Test the time score calculation using the provided chart"""
    bkt = BKTAlgorithm()
    
    print("=== Testing Time Score Calculation ===")
    
    # Test cases based on the provided chart
    test_cases = [
        # (response_time, max_allowed_time, is_correct, expected_score, description)
        (4, 30, True, 1.0, "Fast + correct (normalized: 0.13)"),
        (15, 30, True, 1.0, "Fast + correct (normalized: 0.5)"),
        (20, 30, True, 0.8, "Medium speed + correct (normalized: 0.67)"),
        (25, 30, True, 0.6, "Slow + correct (normalized: 0.83)"),
        (4, 30, False, 0.2, "Fast + wrong (normalized: 0.13)"),
        (20, 30, False, 0.1, "Slow + wrong (normalized: 0.67)"),
        (30, 30, False, 0.0, "Timeout"),
        (35, 30, True, 0.0, "Timeout even if marked correct"),
    ]
    
    for response_time, max_time, is_correct, expected, description in test_cases:
        result = bkt.calculate_time_score(response_time, max_time, is_correct)
        status = "✓" if result == expected else "✗"
        print(f"{status} {description}: {result} (expected: {expected})")
    
    print()

def test_mastery_score_calculation():
    """Test the mastery score calculation using the weighted formula"""
    bkt = BKTAlgorithm()
    
    print("=== Testing Mastery Score Calculation ===")
    
    # Test case: accuracy = 60%, BKT score = 0.7
    accuracy_score = 0.6  # 60%
    bkt_score = 0.7       # 70%
    
    result = bkt.calculate_mastery_score(accuracy_score, bkt_score)
    
    # Manual calculation:
    # accuracy_component = 0.55 * 0.6 = 0.33
    # bkt_component = 0.45 * 0.7 = 0.315
    # final_score = (0.33 + 0.315) * 100 = 64.5
    
    expected_accuracy_component = 0.55 * 0.6
    expected_bkt_component = 0.45 * 0.7
    expected_final_score = (expected_accuracy_component + expected_bkt_component) * 100
    
    print(f"Input: Accuracy = {accuracy_score*100}%, BKT = {bkt_score}")
    print(f"Accuracy component: {result['accuracy_component']:.3f} (expected: {expected_accuracy_component:.3f})")
    print(f"BKT component: {result['bkt_component']:.3f} (expected: {expected_bkt_component:.3f})")
    print(f"Final score: {result['final_score']:.1f} (expected: {expected_final_score:.1f})")
    
    print()

def test_difficulty_thresholds():
    """Test the difficulty threshold classification"""
    bkt = BKTAlgorithm()
    
    print("=== Testing Difficulty Thresholds ===")
    
    test_cases = [
        (74, 'beginner'),
        (75, 'beginner'),
        (76, 'intermediate'),
        (84, 'intermediate'),
        (85, 'advanced'),
        (100, 'advanced'),
    ]
    
    for score, expected in test_cases:
        result = bkt.determine_difficulty_level(score)
        status = "✓" if result == expected else "✗"
        print(f"{status} Score {score}: {result} (expected: {expected})")
    
    print()

def test_real_data_calculation():
    """Test with the actual diagnostic session data"""
    print("=== Testing Real Data Calculation ===")
    
    # Data from the actual diagnostic session
    responses = [
        (True, 1.0, 60),   # Phase 1 (beginner, max 30s, but recorded as 60s)
        (False, 1.0, 60),  # Phase 1
        (False, 1.0, 60),  # Phase 1
        (True, 2.0, 60),   # Phase 1
        (True, 1.0, 60),   # Phase 1
        (False, 7.0, 30),  # Phase 2 (intermediate)
        (False, 2.0, 30),  # Phase 2
        (True, 1.0, 30),   # Phase 2
        (True, 2.0, 30),   # Phase 2
        (True, 1.0, 30),   # Phase 2
        (False, 1.0, 45),  # Phase 3 (advanced)
        (False, 1.0, 45),  # Phase 3
        (False, 1.0, 45),  # Phase 3
        (False, 1.0, 45),  # Phase 3
        (True, 1.0, 45),   # Phase 3
    ]
    
    bkt = BKTAlgorithm()
    
    total_questions = len(responses)
    correct_answers = sum(1 for r in responses if r[0])
    accuracy_score = correct_answers / total_questions
    
    print(f"Total questions: {total_questions}")
    print(f"Correct answers: {correct_answers}")
    print(f"Accuracy: {accuracy_score:.3f} ({accuracy_score*100:.1f}%)")
    
    # Calculate time scores for each response
    time_scores = []
    for is_correct, response_time, max_time in responses:
        time_score = bkt.calculate_time_score(response_time, max_time, is_correct)
        time_scores.append(time_score)
    
    average_time_factor = sum(time_scores) / len(time_scores)
    print(f"Average time factor: {average_time_factor:.3f}")
    
    # Use final BKT score (last response)
    final_bkt_score = 0.6  # From the data
    
    # Calculate mastery score
    mastery_result = bkt.calculate_mastery_score(accuracy_score, final_bkt_score)
    final_mastery_score = mastery_result['final_score']
    
    print(f"Final BKT score: {final_bkt_score}")
    print(f"Accuracy component: {mastery_result['accuracy_component']:.3f}")
    print(f"BKT component: {mastery_result['bkt_component']:.3f}")
    print(f"Final mastery score: {final_mastery_score:.1f}")
    
    # Determine difficulty
    difficulty = bkt.determine_difficulty_level(final_mastery_score)
    print(f"Recommended difficulty: {difficulty}")
    
    print()

if __name__ == '__main__':
    print("BKT Algorithm Test Suite")
    print("=" * 50)
    
    test_time_score_calculation()
    test_mastery_score_calculation()
    test_difficulty_thresholds()
    test_real_data_calculation()
    
    print("Test suite completed!")