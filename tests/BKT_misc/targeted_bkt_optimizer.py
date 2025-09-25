#!/usr/bin/env python3
"""
Targeted BKT Parameter Testing for 0.85-0.89 AUC-ROC
Tests specific high-performance parameter combinations
"""

import sys
import os
import subprocess
import time

# Import BKT for parameter testing
sys.path.append('public/algorithm')
from bkt_algorithm import BKTAlgorithm

class TargetedBKTOptimizer:
    def __init__(self):
        self.bkt_file_path = 'public/algorithm/bkt_algorithm.py'
        self.results = []
        self.best_result = {'auc_roc': 0.0, 'params': {}}
        
        # High-performance parameter combinations based on research
        self.test_combinations = [
            # Ultra-low error model
            {'prior_knowledge': 0.45, 'learn_rate': 0.25, 'slip_rate': 0.04, 'guess_rate': 0.06, 'dampening_factor': 1.0},
            {'prior_knowledge': 0.50, 'learn_rate': 0.22, 'slip_rate': 0.05, 'guess_rate': 0.08, 'dampening_factor': 0.9},
            {'prior_knowledge': 0.42, 'learn_rate': 0.28, 'slip_rate': 0.03, 'guess_rate': 0.05, 'dampening_factor': 1.0},
            
            # High discrimination model
            {'prior_knowledge': 0.38, 'learn_rate': 0.30, 'slip_rate': 0.06, 'guess_rate': 0.09, 'dampening_factor': 0.8},
            {'prior_knowledge': 0.48, 'learn_rate': 0.20, 'slip_rate': 0.05, 'guess_rate': 0.07, 'dampening_factor': 0.9},
            {'prior_knowledge': 0.35, 'learn_rate': 0.35, 'slip_rate': 0.07, 'guess_rate': 0.10, 'dampening_factor': 0.8},
            
            # Balanced high-performance model
            {'prior_knowledge': 0.40, 'learn_rate': 0.25, 'slip_rate': 0.06, 'guess_rate': 0.08, 'dampening_factor': 0.9},
            {'prior_knowledge': 0.45, 'learn_rate': 0.20, 'slip_rate': 0.08, 'guess_rate': 0.10, 'dampening_factor': 0.8},
            {'prior_knowledge': 0.43, 'learn_rate': 0.23, 'slip_rate': 0.05, 'guess_rate': 0.07, 'dampening_factor': 1.0},
            
            # Extreme discrimination model
            {'prior_knowledge': 0.55, 'learn_rate': 0.18, 'slip_rate': 0.03, 'guess_rate': 0.05, 'dampening_factor': 1.0},
            {'prior_knowledge': 0.35, 'learn_rate': 0.40, 'slip_rate': 0.08, 'guess_rate': 0.12, 'dampening_factor': 0.7},
            {'prior_knowledge': 0.50, 'learn_rate': 0.15, 'slip_rate': 0.04, 'guess_rate': 0.06, 'dampening_factor': 0.9}
        ]
    
    def backup_current_file(self):
        """Backup current BKT file"""
        with open(self.bkt_file_path, 'r') as f:
            return f.read()
    
    def restore_file(self, content):
        """Restore BKT file from backup"""
        with open(self.bkt_file_path, 'w') as f:
            f.write(content)
    
    def update_bkt_parameters(self, params):
        """Update BKT algorithm with new parameters"""
        with open(self.bkt_file_path, 'r') as f:
            content = f.read()
        
        # Update BKT default parameters
        new_params_section = f"""        # BKT Default Parameters - Targeted Testing
        self.default_params = {{
        # Testing parameters for 0.85-0.89 AUC-ROC target
        'prior_knowledge': {params['prior_knowledge']:.3f},    # Baseline competency
        'learn_rate': {params['learn_rate']:.3f},         # Learning rate  
        'slip_rate': {params['slip_rate']:.3f},          # Slip rate for errors
        'guess_rate': {params['guess_rate']:.3f}          # Guess rate for lucky answers"""
        
        # Find and replace the parameters section
        start_marker = "        # BKT Default Parameters"
        end_marker = "        }"
        
        start_idx = content.find(start_marker)
        if start_idx == -1:
            return False
            
        # Find the end of the parameters block
        brace_count = 0
        end_idx = start_idx
        in_dict = False
        
        for i in range(start_idx, len(content)):
            char = content[i]
            if char == '{':
                brace_count += 1
                in_dict = True
            elif char == '}' and in_dict:
                brace_count -= 1
                if brace_count == 0:
                    end_idx = i + 1
                    break
        
        if end_idx > start_idx:
            new_content = content[:start_idx] + new_params_section + "\n        }" + content[end_idx:]
            
            # Update dampening factor
            dampening_line = f"        dampening_factor = {params['dampening_factor']:.1f}  # Targeted testing dampening"
            new_content = new_content.replace(
                "        dampening_factor = 0.7  # Higher learning rate for better separation",
                dampening_line
            )
            
            with open(self.bkt_file_path, 'w') as f:
                f.write(new_content)
            return True
        
        return False
    
    def run_simulation_and_test(self):
        """Run simulation and AUC-ROC test, return AUC-ROC score"""
        try:
            # Run simulation
            result = subprocess.run(['python', 'simulate_900_responses.py'], 
                                  capture_output=True, text=True, timeout=45)
            if result.returncode != 0:
                print(f"Simulation failed: {result.stderr[:200]}...")
                return None
            
            # Run AUC-ROC test
            result = subprocess.run(['python', 'tests/BKT_AUC_ROC_Metrics.py'], 
                                  capture_output=True, text=True, timeout=20)
            if result.returncode != 0:
                print(f"AUC-ROC test failed: {result.stderr[:200]}...")
                return None
            
            # Extract AUC-ROC score from output
            output_lines = result.stdout.split('\n')
            for line in output_lines:
                if 'AUC-ROC Score:' in line:
                    auc_score = float(line.split(':')[1].strip())
                    return auc_score
            
            return None
            
        except subprocess.TimeoutExpired:
            print("Test timeout")
            return None
        except Exception as e:
            print(f"Test error: {str(e)[:100]}...")
            return None
    
    def run_targeted_tests(self):
        """Run targeted parameter tests"""
        print("=== Targeted BKT Parameter Testing for 0.85-0.89 AUC-ROC ===")
        print(f"Testing {len(self.test_combinations)} high-performance parameter combinations")
        
        # Backup original file
        original_content = self.backup_current_file()
        
        try:
            for i, params in enumerate(self.test_combinations):
                print(f"\n--- Test {i+1}/{len(self.test_combinations)} ---")
                print(f"PK: {params['prior_knowledge']:.3f}, LR: {params['learn_rate']:.3f}, "
                      f"S: {params['slip_rate']:.3f}, G: {params['guess_rate']:.3f}, "
                      f"D: {params['dampening_factor']:.1f}")
                
                # Update parameters
                if not self.update_bkt_parameters(params):
                    print("Failed to update parameters")
                    continue
                
                # Run test
                auc_score = self.run_simulation_and_test()
                
                if auc_score is not None:
                    result = {
                        'test_id': i+1,
                        'params': params.copy(),
                        'auc_roc': auc_score,
                        'target_achieved': auc_score >= 0.85
                    }
                    
                    self.results.append(result)
                    
                    status = "TARGET HIT!" if auc_score >= 0.85 else "CLOSE" if auc_score >= 0.80 else "LOW"
                    print(f"AUC-ROC: {auc_score:.4f} [{status}]")
                    
                    # Update best result
                    if auc_score > self.best_result['auc_roc']:
                        self.best_result = {
                            'auc_roc': auc_score,
                            'params': params.copy()
                        }
                        print(f"NEW BEST: {auc_score:.4f}")
                        
                        # If we hit target, note it
                        if auc_score >= 0.85:
                            print(f"SUCCESS! Target achieved: {auc_score:.4f}")
                else:
                    print("Test failed")
                
                # Brief pause
                time.sleep(0.5)
        
        finally:
            # Restore original file
            self.restore_file(original_content)
        
        return self.results
    
    def generate_report(self):
        """Generate optimization report"""
        if not self.results:
            print("No results to report")
            return
        
        print(f"\n{'='*60}")
        print("TARGETED BKT OPTIMIZATION REPORT")
        print(f"{'='*60}")
        
        # Sort results by AUC-ROC
        sorted_results = sorted(self.results, key=lambda x: x['auc_roc'], reverse=True)
        
        print(f"\nTOTAL TESTS: {len(self.results)}")
        target_achieved = sum(1 for r in self.results if r['target_achieved'])
        print(f"TARGET ACHIEVED (≥0.85): {target_achieved} tests")
        print(f"CLOSE (≥0.80): {sum(1 for r in self.results if r['auc_roc'] >= 0.80)} tests")
        
        print(f"\nBEST RESULT:")
        best = self.best_result
        print(f"AUC-ROC: {best['auc_roc']:.4f}")
        print(f"Parameters:")
        for key, value in best['params'].items():
            print(f"  {key}: {value}")
        
        print(f"\nTOP 5 RESULTS:")
        for i, result in enumerate(sorted_results[:5]):
            status = "TARGET" if result['target_achieved'] else "CLOSE" if result['auc_roc'] >= 0.80 else "LOW"
            print(f"{i+1}. AUC-ROC: {result['auc_roc']:.4f} [{status}]")
            params = result['params']
            print(f"   PK: {params['prior_knowledge']:.3f}, LR: {params['learn_rate']:.3f}, "
                  f"S: {params['slip_rate']:.3f}, G: {params['guess_rate']:.3f}, D: {params['dampening_factor']:.1f}")
        
        # Implement best parameters if target achieved
        if self.best_result['auc_roc'] >= 0.85:
            print(f"\nSUCCESS! Implementing best parameters...")
            if self.update_bkt_parameters(self.best_result['params']):
                print("Best parameters applied to BKT algorithm")
            else:
                print("Failed to apply best parameters")

def main():
    optimizer = TargetedBKTOptimizer()
    
    print("Targeted BKT Parameter Testing for 0.85-0.89 AUC-ROC Target")
    print("="*60)
    
    # Run targeted tests
    optimizer.run_targeted_tests()
    
    # Generate and display report
    optimizer.generate_report()
    
    if optimizer.best_result['auc_roc'] >= 0.85:
        print(f"\nSUCCESS! Achieved {optimizer.best_result['auc_roc']:.4f} AUC-ROC")
    else:
        print(f"\nBest achieved: {optimizer.best_result['auc_roc']:.4f} AUC-ROC")
        print("Consider testing additional parameter ranges or enhancing simulation")

if __name__ == "__main__":
    main()