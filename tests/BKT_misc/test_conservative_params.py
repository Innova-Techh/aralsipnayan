#!/usr/bin/env python3
"""
Test Conservative BKT Parameters
Testing very conservative learning to prevent ceiling clustering
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from public.algorithm.bkt_algorithm import BKTAlgorithm

def test_conservative_learning():
    """Test if conservative parameters prevent ceiling clustering"""
    print("=== Testing Conservative BKT Parameters ===")
    
    bkt = BKTAlgorithm()
    print("Parameters:")
    print(f"  Prior Knowledge: {bkt.default_params['prior_knowledge']}")
    print(f"  Learn Rate: {bkt.default_params['learn_rate']}")
    print(f"  Slip Rate: {bkt.default_params['slip_rate']}")
    print(f"  Guess Rate: {bkt.default_params['guess_rate']}")
    print()
    
    # Test 70% performance scenario (should stay well below ceiling)
    print("Testing 70% Correct Performance (Conservative Learning):")
    current_bkt = bkt.default_params['prior_knowledge']  # Use actual starting point
    
    for i in range(20):
        # 70% correct responses
        is_correct = (i % 10) < 7
        
        new_bkt = bkt.calculate_bkt_update(current_bkt, is_correct, bkt.default_params)
        change = new_bkt - current_bkt
        
        print(f"  Response {i+1:2d}: {'✓' if is_correct else '✗'} | BKT: {current_bkt:.3f} → {new_bkt:.3f} (Δ{change:+.3f})")
        
        # Check if hitting boundaries
        if new_bkt >= 0.82:
            print(f"    ⚠️  CEILING HIT at response {i+1}!")
        elif new_bkt <= 0.15:
            print(f"    ⚠️  FLOOR HIT at response {i+1}!")
            
        current_bkt = new_bkt
    
    print(f"\nFinal BKT: {current_bkt:.3f}")
    print(f"Range Used: {0.25:.3f} → {current_bkt:.3f} = {current_bkt-0.25:.3f}")
    print(f"Boundary Status: {'✓ Good' if 0.18 < current_bkt < 0.79 else '✗ Too close to boundary'}")

if __name__ == "__main__":
    test_conservative_learning()