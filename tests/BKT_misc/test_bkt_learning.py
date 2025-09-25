#!/usr/bin/env python3
"""
Test BKT Algorithm Learning with Known Scenarios
"""

import sys
import os
sys.path.append('public/algorithm')

from bkt_algorithm import BKTAlgorithm

def test_bkt_learning():
    """Test BKT learning with controlled scenarios"""
    bkt = BKTAlgorithm()
    params = bkt.default_params
    
    print("BKT ALGORITHM LEARNING TEST")
    print("="*50)
    print(f"Initial Parameters:")
    print(f"  Prior Knowledge: {params['prior_knowledge']}")
    print(f"  Learn Rate: {params['learn_rate']}")
    print(f"  Slip Rate: {params['slip_rate']}")
    print(f"  Guess Rate: {params['guess_rate']}")
    
    # Test 1: All correct answers (should increase BKT significantly)
    print(f"\n1. TESTING PERFECT PERFORMANCE (10 correct answers):")
    current_bkt = params['prior_knowledge']
    print(f"   Starting BKT: {current_bkt:.4f}")
    
    for i in range(10):
        new_bkt = bkt.calculate_bkt_update(current_bkt, True, params)
        print(f"   Answer {i+1} (Correct): {current_bkt:.4f} → {new_bkt:.4f} (Δ {new_bkt-current_bkt:+.4f})")
        current_bkt = new_bkt
    
    # Test 2: All incorrect answers (should change minimally due to guessing)
    print(f"\n2. TESTING POOR PERFORMANCE (10 incorrect answers):")
    current_bkt = params['prior_knowledge']
    print(f"   Starting BKT: {current_bkt:.4f}")
    
    for i in range(10):
        new_bkt = bkt.calculate_bkt_update(current_bkt, False, params)
        print(f"   Answer {i+1} (Wrong): {current_bkt:.4f} → {new_bkt:.4f} (Δ {new_bkt-current_bkt:+.4f})")
        current_bkt = new_bkt
    
    # Test 3: Mixed performance (realistic scenario)
    print(f"\n3. TESTING MIXED PERFORMANCE (7 correct, 3 incorrect):")
    current_bkt = params['prior_knowledge']
    print(f"   Starting BKT: {current_bkt:.4f}")
    
    # Simulate 70% correct performance
    correct_sequence = [True, True, False, True, True, True, False, True, True, False]
    for i, is_correct in enumerate(correct_sequence):
        new_bkt = bkt.calculate_bkt_update(current_bkt, is_correct, params)
        result = "Correct" if is_correct else "Wrong"
        print(f"   Answer {i+1} ({result}): {current_bkt:.4f} → {new_bkt:.4f} (Δ {new_bkt-current_bkt:+.4f})")
        current_bkt = new_bkt
    
    print(f"\nFinal BKT: {current_bkt:.4f}")
    print(f"Performance: {sum(correct_sequence)}/10 = {sum(correct_sequence)*10}% correct")

if __name__ == "__main__":
    test_bkt_learning()