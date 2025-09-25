#!/usr/bin/env python3
"""
Comprehensive BKT Parameter Optimization for 0.85-0.89 AUC-ROC Target
Tests multiple parameter combinations systematically to find optimal settings
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

import mysql.connector
from sklearn.metrics import roc_auc_score
import subprocess
import time

class BKTParameterOptimizer:
    def __init__(self):
        self.db_config = {
            'host': 'localhost',
            'user': 'root',
            'password': '',
            'database': 'aralsipnayandb'
        }
        self.best_auc = 0.0
        self.best_params = None
        self.test_results = []

    def update_bkt_parameters(self, params):
        """Update BKT algorithm parameters in the file"""
        
        # Read current file
        with open('public/algorithm/bkt_algorithm.py', 'r') as f:
            content = f.read()
        
        # Update parameters
        new_params_section = f"""        # BKT Default Parameters - Optimization Test
        self.default_params = {{
        # Testing parameters for 0.85-0.89 AUC-ROC target
        'prior_knowledge': {params['prior_knowledge']:.3f},    # Baseline competency
        'learn_rate': {params['learn_rate']:.3f},         # Learning responsiveness
        'slip_rate': {params['slip_rate']:.3f},          # Error rate when competent
        'guess_rate': {params['guess_rate']:.3f}          # Success rate when not competent"""
        
        # Find and replace parameters section
        start_marker = "        # BKT Default Parameters"
        end_marker = "        'guess_rate':"
        
        lines = content.split('\n')
        new_lines = []
        in_params = False
        
        for line in lines:
            if start_marker in line:
                in_params = True
                new_lines.extend(new_params_section.split('\n'))
                continue
            elif in_params and end_marker in line:
                in_params = False
                continue
            elif not in_params:
                new_lines.append(line)
        
        # Write updated file
        with open('public/algorithm/bkt_algorithm.py', 'w') as f:
            f.write('\n'.join(new_lines))

    def update_dampening_settings(self, dampening_factor, min_bkt, max_bkt):
        """Update dampening and boundary settings"""
        
        with open('public/algorithm/bkt_algorithm.py', 'r') as f:
            content = f.read()
        
        # Update dampening factor
        dampening_line = f"        dampening_factor = {dampening_factor:.2f}  # Optimized for maximum discrimination"
        content = content.replace(
            "        dampening_factor = 0.7  # Higher learning rate for better separation",
            dampening_line
        )
        
        # Update boundaries
        boundary_line = f"        return min({max_bkt:.2f}, max(P_L_dampened, {min_bkt:.2f}))  # Optimized range for 0.85+ AUC-ROC"
        content = content.replace(
            "        return min(0.85, max(P_L_dampened, 0.18))  # Wider range for better distribution",
            boundary_line
        )
        
        with open('public/algorithm/bkt_algorithm.py', 'w') as f:
            f.write(content)

    def run_simulation_and_test(self):
        """Run simulation and get AUC-ROC score"""
        try:
            # Run simulation
            result = subprocess.run(['python', 'simulate_900_responses.py'], 
                                 capture_output=True, text=True, timeout=60)
            if result.returncode != 0:
                print(f"Simulation failed: {result.stderr}")
                return 0.0
            
            # Get AUC-ROC score directly from database
            conn = mysql.connector.connect(**self.db_config)
            cursor = conn.cursor()
            
            cursor.execute("""
                SELECT bkt_before, is_correct 
                FROM question_responses 
                WHERE bkt_before IS NOT NULL
                ORDER BY answered_at DESC
                LIMIT 900
            """)
            
            data = cursor.fetchall()
            if len(data) < 100:
                return 0.0
                
            bkt_scores = [float(row[0]) for row in data]
            actual_results = [int(row[1]) for row in data]
            
            auc_roc = roc_auc_score(actual_results, bkt_scores)
            conn.close()
            
            return auc_roc
            
        except Exception as e:
            print(f"Test error: {e}")
            return 0.0

    def test_parameter_combination(self, params, dampening_config):
        """Test a specific parameter combination"""
        print(f"\n{'='*60}")
        print(f"TESTING PARAMETERS:")
        print(f"Prior: {params['prior_knowledge']:.3f}, Learn: {params['learn_rate']:.3f}")
        print(f"Slip: {params['slip_rate']:.3f}, Guess: {params['guess_rate']:.3f}")
        print(f"Dampening: {dampening_config['factor']:.2f}, Range: {dampening_config['min_bkt']:.2f}-{dampening_config['max_bkt']:.2f}")
        
        # Update parameters
        self.update_bkt_parameters(params)
        self.update_dampening_settings(
            dampening_config['factor'], 
            dampening_config['min_bkt'], 
            dampening_config['max_bkt']
        )
        
        # Test performance
        auc_roc = self.run_simulation_and_test()
        
        result = {
            'params': params.copy(),
            'dampening': dampening_config.copy(),
            'auc_roc': auc_roc
        }
        
        self.test_results.append(result)
        
        print(f"AUC-ROC: {auc_roc:.4f}")
        
        if auc_roc > self.best_auc:
            self.best_auc = auc_roc
            self.best_params = result
            print(f"🎉 NEW BEST: {auc_roc:.4f}")
        
        return auc_roc

    def run_comprehensive_optimization(self):
        """Run systematic parameter optimization"""
        print("="*60)
        print("COMPREHENSIVE BKT PARAMETER OPTIMIZATION")
        print("Target: 0.85-0.89 AUC-ROC")
        print("="*60)
        
        # Parameter ranges for systematic testing
        parameter_sets = [
            # Ultra-low error configurations
            {
                'prior_knowledge': 0.45,
                'learn_rate': 0.20,
                'slip_rate': 0.05,
                'guess_rate': 0.08
            },
            {
                'prior_knowledge': 0.50, 
                'learn_rate': 0.25,
                'slip_rate': 0.04,
                'guess_rate': 0.06
            },
            {
                'prior_knowledge': 0.55,
                'learn_rate': 0.30,
                'slip_rate': 0.03,
                'guess_rate': 0.05
            },
            # High discrimination configurations
            {
                'prior_knowledge': 0.42,
                'learn_rate': 0.18,
                'slip_rate': 0.06,
                'guess_rate': 0.10
            },
            {
                'prior_knowledge': 0.48,
                'learn_rate': 0.22,
                'slip_rate': 0.07,
                'guess_rate': 0.09
            },
            # Balanced high-performance configurations
            {
                'prior_knowledge': 0.35,
                'learn_rate': 0.35,
                'slip_rate': 0.08,
                'guess_rate': 0.12
            }
        ]
        
        # Dampening configurations
        dampening_configs = [
            {'factor': 0.9, 'min_bkt': 0.12, 'max_bkt': 0.88},  # Minimal dampening
            {'factor': 0.8, 'min_bkt': 0.15, 'max_bkt': 0.85},  # Light dampening
            {'factor': 1.0, 'min_bkt': 0.10, 'max_bkt': 0.90},  # No dampening
        ]
        
        total_tests = len(parameter_sets) * len(dampening_configs)
        current_test = 0
        
        for params in parameter_sets:
            for dampening in dampening_configs:
                current_test += 1
                print(f"\nTest {current_test}/{total_tests}")
                
                auc_roc = self.test_parameter_combination(params, dampening)
                
                if auc_roc >= 0.85:
                    print(f"🎯 TARGET ACHIEVED: {auc_roc:.4f}")
                    if auc_roc >= 0.89:
                        print("🚀 EXCEEDING TARGET!")
                
                time.sleep(1)  # Brief pause between tests
        
        self.print_final_results()

    def print_final_results(self):
        """Print comprehensive results"""
        print("\n" + "="*60)
        print("OPTIMIZATION RESULTS SUMMARY")
        print("="*60)
        
        # Sort results by AUC-ROC
        sorted_results = sorted(self.test_results, key=lambda x: x['auc_roc'], reverse=True)
        
        print(f"\nTop 5 Performance Results:")
        for i, result in enumerate(sorted_results[:5]):
            params = result['params']
            dampening = result['dampening']
            auc = result['auc_roc']
            
            status = "🎯 TARGET!" if auc >= 0.85 else "📈 Good" if auc >= 0.75 else "📉 Fair"
            
            print(f"\n{i+1}. AUC-ROC: {auc:.4f} {status}")
            print(f"   Prior: {params['prior_knowledge']:.3f}, Learn: {params['learn_rate']:.3f}")
            print(f"   Slip: {params['slip_rate']:.3f}, Guess: {params['guess_rate']:.3f}")
            print(f"   Dampening: {dampening['factor']:.2f}, Range: {dampening['min_bkt']:.2f}-{dampening['max_bkt']:.2f}")
        
        if self.best_auc >= 0.85:
            print(f"\n🎉 SUCCESS! Best AUC-ROC: {self.best_auc:.4f}")
            print("🎯 Target 0.85-0.89 ACHIEVED!")
        else:
            print(f"\n📊 Best AUC-ROC: {self.best_auc:.4f}")
            print(f"📈 Gap to target: {0.85 - self.best_auc:.4f}")
        
        return self.best_params

if __name__ == "__main__":
    optimizer = BKTParameterOptimizer()
    optimizer.run_comprehensive_optimization()