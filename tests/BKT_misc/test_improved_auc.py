#!/usr/bin/env python3
"""
Test BKT AUC-ROC with optimized threshold
"""

import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), '.'))

# Import the test class directly
import importlib.util
spec = importlib.util.spec_from_file_location("BKT_AUC_ROC_Metrics", "tests/BKT_AUC_ROC_Metrics.py")
test_module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(test_module)

SimpleBKTAUCTest = test_module.SimpleBKTAUCTest

def main():
    print("TESTING BKT AUC-ROC WITH OPTIMIZED THRESHOLD")
    print("="*60)
    
    tester = SimpleBKTAUCTest()
    
    # Test with optimal threshold from analysis (0.10)
    print("\n1. Testing with threshold 0.10 (optimized):")
    results_010 = tester.run_analysis(threshold=0.10)
    tester.display_results(results_010)
    
    # Test with even lower threshold 
    print("\n2. Testing with threshold 0.05 (very low):")
    results_005 = tester.run_analysis(threshold=0.05)
    tester.display_results(results_005)
    
    # Test with median BKT threshold
    print("\n3. Testing with threshold 0.18 (closer to mean):")
    results_018 = tester.run_analysis(threshold=0.18)
    tester.display_results(results_018)
    
    print("\n" + "="*60)
    print("THRESHOLD COMPARISON SUMMARY")
    print("="*60)
    print(f"Threshold 0.05: AUC-ROC = {results_005['auc_roc']:.4f}")
    print(f"Threshold 0.10: AUC-ROC = {results_010['auc_roc']:.4f}")
    print(f"Threshold 0.18: AUC-ROC = {results_018['auc_roc']:.4f}")
    print(f"Threshold 0.20: AUC-ROC = 0.5132 (original)")

if __name__ == "__main__":
    main()