#!/usr/bin/env python3
"""
Test improved BKT parameters
"""

import sys
sys.path.append('public/algorithm')
from bkt_algorithm import BKTAlgorithm

def test_improved_parameters():
    print('TESTING IMPROVED BKT PARAMETERS')
    print('='*40)

    bkt = BKTAlgorithm()
    params = bkt.default_params

    print(f'New Parameters:')
    print(f'  Prior Knowledge: {params["prior_knowledge"]}')
    print(f'  Learn Rate: {params["learn_rate"]} (was 0.10)')
    print(f'  Slip Rate: {params["slip_rate"]}')
    print(f'  Guess Rate: {params["guess_rate"]}')
    print(f'  BKT Ceiling: 0.95 (was 0.90)')

    # Test 70% correct performance over 10 responses
    print(f'\nTesting 70% Performance (7 correct, 3 incorrect):')
    current_bkt = params['prior_knowledge']
    correct_sequence = [True, True, False, True, True, True, False, True, True, True]

    print(f'Response | Answer | BKT Before | BKT After | Delta')
    print('-' * 50)
    for i, is_correct in enumerate(correct_sequence):
        new_bkt = bkt.calculate_bkt_update(current_bkt, is_correct, params)
        result = '✓' if is_correct else '✗'
        delta = new_bkt - current_bkt
        print(f'{i+1:8} | {result:6} | {current_bkt:10.4f} | {new_bkt:9.4f} | {delta:+6.4f}')
        current_bkt = new_bkt

    print(f'\nFinal BKT: {current_bkt:.4f}')
    print(f'Expected range for 70% accuracy: 0.60-0.80')
    if 0.60 <= current_bkt <= 0.80:
        print('✓ EXCELLENT correlation with performance!')
    else:
        print('✗ Still needs adjustment')

if __name__ == "__main__":
    test_improved_parameters()