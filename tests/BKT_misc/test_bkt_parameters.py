#!/usr/bin/env python3
"""
Test different BKT parameters to improve correlation
"""

import sys
import os
sys.path.append('public/algorithm')

from bkt_algorithm import BKTAlgorithm

def test_bkt_parameters():
    """Test different BKT parameter sets for better correlation"""
    
    # Current parameters (causing issues)
    current_params = {
        'prior_knowledge': 0.15,
        'learn_rate': 0.10,      
        'slip_rate': 0.20,
        'guess_rate': 0.25
    }
    
    # Alternative parameter sets for testing
    param_sets = [
        {
            'name': 'Current (Problematic)',
            'prior_knowledge': 0.15,
            'learn_rate': 0.10,      
            'slip_rate': 0.20,
            'guess_rate': 0.25
        },
        {
            'name': 'Conservative Learning',
            'prior_knowledge': 0.20,
            'learn_rate': 0.05,      # Much slower learning
            'slip_rate': 0.15,       # Lower slip rate
            'guess_rate': 0.25
        },
        {
            'name': 'Balanced (Academic)',
            'prior_knowledge': 0.10,
            'learn_rate': 0.08,      
            'slip_rate': 0.10,       # Lower slip 
            'guess_rate': 0.20       # Lower guess
        },
        {
            'name': 'Gradual Learning',
            'prior_knowledge': 0.25,
            'learn_rate': 0.03,      # Very gradual
            'slip_rate': 0.12,       
            'guess_rate': 0.22       
        }
    ]
    
    print("BKT PARAMETER OPTIMIZATION TEST")
    print("="*60)
    
    for params in param_sets:
        print(f"\n{params['name']}:")
        print(f"  Prior: {params['prior_knowledge']}, Learn: {params['learn_rate']}, Slip: {params['slip_rate']}, Guess: {params['guess_rate']}")
        
        bkt = BKTAlgorithm()
        
        # Test realistic scenario: 70% correct performance over 10 responses
        current_bkt = params['prior_knowledge']
        correct_sequence = [True, True, False, True, True, True, False, True, True, True]
        
        bkt_progression = [current_bkt]
        for is_correct in correct_sequence:
            current_bkt = bkt.calculate_bkt_update(current_bkt, is_correct, params)
            bkt_progression.append(current_bkt)
        
        final_bkt = bkt_progression[-1]
        mid_bkt = bkt_progression[5]  # After 5 responses
        
        print(f"    Start: {params['prior_knowledge']:.3f} → Mid: {mid_bkt:.3f} → Final: {final_bkt:.3f}")
        print(f"    Range: {final_bkt - params['prior_knowledge']:.3f}, Midpoint: {mid_bkt:.3f}")
        
        # Check if final BKT correlates well with 70% performance
        expected_range = (0.6, 0.8)  # For 70% accuracy
        if expected_range[0] <= final_bkt <= expected_range[1]:
            print(f"    ✓ GOOD correlation with 70% performance")
        else:
            print(f"    ✗ Poor correlation (expected {expected_range[0]}-{expected_range[1]})")

if __name__ == "__main__":
    test_bkt_parameters()