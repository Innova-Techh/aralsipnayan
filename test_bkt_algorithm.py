#!/usr/bin/env python3
"""
Test script for the updated BKT Algorithm implementation
"""

import sys
import os

# Add the algorithm directory to the Python path
sys.path.append(os.path.join(os.path.dirname(__file__), 'public', 'algorithm'))

from bkt_algorithm import BKTAlgorithm

def test_bkt_calculations():
    """Test the BKT algorithm calculations"""
    print("=" * 60)
    print("Testing Updated BKT Algorithm Implementation")
    print("=" * 60)
    
    bkt = BKTAlgorithm()
    
    # Test 1: Time Score Calculation
    print("\n1. Testing Time Score Calculation:")
    print("   Formula: normalized_time = response_time / max_allowed_time")
    
    test_cases = [
        (10, 30, True),   # 0.33 normalized, correct -> should be 1.0
        (20, 30, True),   # 0.67 normalized, correct -> should be 0.8  
        (25, 30, True),   # 0.83 normalized, correct -> should be 0.6
        (10, 30, False),  # 0.33 normalized, incorrect -> should be 0.2
        (20, 30, False),  # 0.67 normalized, incorrect -> should be 0.1
    ]
    
    for response_time, max_time, is_correct in test_cases:
        normalized = response_time / max_time
        time_score = bkt.calculate_time_score(response_time, max_time, is_correct)
        print(f"   Response: {response_time}s/{max_time}s = {normalized:.2f}, Correct: {is_correct} -> Time Score: {time_score}")
    
    # Test 2: F-time Factor Calculation
    print("\n2. Testing F-time Factor Calculation:")
    print("   Formula: ftime = Σ(timescore) / total_questions")
    
    cumulative_scores = [8.5, 12.3, 15.8]
    total_questions = [10, 15, 20]
    
    for cum_score, total_q in zip(cumulative_scores, total_questions):
        ftime = bkt.calculate_ftime_factor(cum_score, total_q)
        print(f"   Cumulative: {cum_score}, Questions: {total_q} -> F-time: {ftime:.4f}")
    
    # Test 3: BKT Formula Update
    print("\n3. Testing BKT Formula Updates:")
    print("   For Correct: P(Ln+1) = P(Ln)×(1-P(S))×ftime×fdifficulty / [P(Ln)×(1-P(S))+(1-P(Ln))×P(G)]")
    print("   For Incorrect: P(Ln+1) = P(Ln)×P(S) / [P(Ln)×P(S)+(1-P(Ln))×(1-P(G))×ftime×fdifficulty]")
    
    prior_prob = 0.3
    params = bkt.default_params
    time_factor = 0.75
    difficulty_factor = 1.0
    
    # Test correct answer
    bkt_correct = bkt.calculate_bkt_update(prior_prob, True, params, time_factor, difficulty_factor)
    print(f"   Prior: {prior_prob}, Correct Answer -> New BKT: {bkt_correct:.4f}")
    
    # Test incorrect answer  
    bkt_incorrect = bkt.calculate_bkt_update(prior_prob, False, params, time_factor, difficulty_factor)
    print(f"   Prior: {prior_prob}, Incorrect Answer -> New BKT: {bkt_incorrect:.4f}")
    
    # Test 4: Mastery Score Calculation
    print("\n4. Testing Mastery Score Calculation:")
    print("   Formula: (55% × Accuracy) + (45% × BKT)")
    
    accuracy_scores = [0.8, 0.9, 0.6]
    bkt_scores = [0.7, 0.85, 0.4]
    
    for acc, bkt_score in zip(accuracy_scores, bkt_scores):
        mastery = bkt.calculate_mastery_score(acc, bkt_score)
        print(f"   Accuracy: {acc:.1f}, BKT: {bkt_score:.2f} -> Mastery: {mastery:.2f}")
    
    # Test 5: Difficulty Level Determination
    print("\n5. Testing Difficulty Level Determination:")
    print("   Thresholds: ≤75=beginner, 76-84=intermediate, 85-100=advanced")
    
    mastery_scores = [70, 75, 80, 85, 95]
    for score in mastery_scores:
        level = bkt.determine_difficulty_level(score)
        print(f"   Mastery Score: {score} -> Difficulty: {level}")
    
    print("\n" + "=" * 60)
    print("BKT Algorithm Test Complete!")
    print("=" * 60)

if __name__ == "__main__":
    test_bkt_calculations()