#!/usr/bin/env python3
"""
Final BKT Optimization Analysis
Identify specific bottlenecks preventing 0.85+ AUC-ROC achievement
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

import mysql.connector
from sklearn.metrics import roc_auc_score, confusion_matrix
import numpy as np

def analyze_performance_ceiling():
    """Analyze why AUC-ROC is capped at ~0.71"""
    
    # Connect to database
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='aralsipnayandb'
        )
        cursor = conn.cursor()
        
        print("=== FINAL BKT PERFORMANCE ANALYSIS ===")
        
        # Get all responses with BKT data (using bkt_before for prediction accuracy)
        cursor.execute("""
            SELECT bkt_before, is_correct, response_time, max_allowed_time
            FROM question_responses 
            WHERE bkt_before IS NOT NULL
            ORDER BY user_id, answered_at
        """)
        
        data = cursor.fetchall()
        print(f"Analyzing {len(data)} responses...")
        
        bkt_scores = [float(row[0]) for row in data]
        actual_results = [int(row[1]) for row in data]
        
        # Detailed BKT distribution analysis
        print(f"\n=== BKT SCORE DISTRIBUTION ===")
        bkt_array = np.array(bkt_scores)
        print(f"Mean BKT: {np.mean(bkt_array):.3f}")
        print(f"Std BKT: {np.std(bkt_array):.3f}")
        print(f"Min BKT: {np.min(bkt_array):.3f}")
        print(f"Max BKT: {np.max(bkt_array):.3f}")
        print(f"Range: {np.max(bkt_array) - np.min(bkt_array):.3f}")
        
        # Boundary clustering analysis
        floor_clustered = np.sum(bkt_array <= 0.22) / len(bkt_array) * 100
        ceiling_clustered = np.sum(bkt_array >= 0.80) / len(bkt_array) * 100
        mid_range = np.sum((bkt_array > 0.22) & (bkt_array < 0.80)) / len(bkt_array) * 100
        
        print(f"\n=== CLUSTERING ANALYSIS ===")
        print(f"Floor Clustering (≤0.22): {floor_clustered:.1f}%")
        print(f"Ceiling Clustering (≥0.80): {ceiling_clustered:.1f}%")
        print(f"Mid-Range Distribution: {mid_range:.1f}%")
        
        # Correlation analysis by performance level
        print(f"\n=== PERFORMANCE CORRELATION ===")
        
        # Split into performance bands
        low_performers = [(bkt, actual) for bkt, actual in zip(bkt_scores, actual_results) if bkt < 0.4]
        mid_performers = [(bkt, actual) for bkt, actual in zip(bkt_scores, actual_results) if 0.4 <= bkt < 0.7]
        high_performers = [(bkt, actual) for bkt, actual in zip(bkt_scores, actual_results) if bkt >= 0.7]
        
        for band_name, band_data in [("Low (BKT<0.4)", low_performers), 
                                     ("Mid (0.4≤BKT<0.7)", mid_performers),
                                     ("High (BKT≥0.7)", high_performers)]:
            if len(band_data) > 10:
                band_bkt = [x[0] for x in band_data]
                band_actual = [x[1] for x in band_data]
                band_accuracy = np.mean(band_actual)
                correlation = np.corrcoef(band_bkt, band_actual)[0,1] if len(set(band_bkt)) > 1 else 0
                
                print(f"{band_name}: {len(band_data)} responses, {band_accuracy:.3f} accuracy, {correlation:.3f} correlation")
        
        # Calculate current AUC-ROC
        auc_roc = roc_auc_score(actual_results, bkt_scores)
        print(f"\nCurrent AUC-ROC: {auc_roc:.4f}")
        
        # Theoretical maximum with current data distribution
        print(f"\n=== OPTIMIZATION POTENTIAL ===")
        
        # Perfect discrimination would separate all correct/incorrect perfectly
        sorted_indices = np.argsort(bkt_scores)
        sorted_actual = np.array(actual_results)[sorted_indices]
        
        # Find optimal threshold that maximizes separation
        best_threshold = 0.5
        best_accuracy = 0
        for threshold in np.arange(0.1, 0.9, 0.05):
            predictions = (np.array(bkt_scores) >= threshold).astype(int)
            accuracy = np.mean(predictions == actual_results)
            if accuracy > best_accuracy:
                best_accuracy = accuracy
                best_threshold = threshold
        
        print(f"Best Threshold: {best_threshold:.2f}")
        print(f"Best Accuracy: {best_accuracy:.4f}")
        
        # Estimate theoretical maximum AUC-ROC
        if len(set(bkt_scores)) > 1:
            perfect_separation = np.sum(actual_results) / len(actual_results)
            theoretical_max = min(0.95, 0.5 + (best_accuracy - 0.5) * 1.5)
            print(f"Theoretical Max AUC-ROC: {theoretical_max:.4f}")
            
            gap_to_target = 0.85 - auc_roc
            gap_to_max = theoretical_max - auc_roc
            
            print(f"\nGap to 0.85 target: {gap_to_target:.4f}")
            print(f"Gap to theoretical max: {gap_to_max:.4f}")
            
            if theoretical_max >= 0.85:
                print("✓ Target 0.85+ is theoretically achievable!")
            else:
                print("✗ Need better data distribution for 0.85+ target")
        
        conn.close()
        
    except Exception as e:
        print(f"Analysis error: {e}")

if __name__ == "__main__":
    analyze_performance_ceiling()