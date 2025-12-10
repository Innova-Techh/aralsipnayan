#!/usr/bin/env python3
"""
Simplified BKT AUC-ROC Metrics Test
Tests predictive accuracy using real data from question_responses table
Focuses on essential metrics: AUC-ROC, Confusion Matrix, and BKT performance
"""

import mysql.connector
from sklearn.metrics import roc_auc_score, confusion_matrix, roc_curve
import sys
import matplotlib.pyplot as plt
import seaborn as sns
import numpy as np
from datetime import datetime
import os

class SimpleBKTAUCTest:
    def __init__(self):
        self.db_connection = None
        self.connect_to_database()

    def connect_to_database(self):
        """Connect to the Laravel database"""
        try:
            self.db_connection = mysql.connector.connect(
                host='localhost',
                database='aralsipnayandb',
                user='root',
                password='root',
                charset='utf8mb4'
            )
            print("✓ Connected to database successfully")
        except mysql.connector.Error as e:
            print(f"✗ Database connection failed: {e}")
            sys.exit(1)

    def fetch_question_responses(self, assessment_id=None):
        """Fetch question responses from database"""
        cursor = self.db_connection.cursor(dictionary=True)
        
        if assessment_id:
            sql = """
                SELECT bkt_before, is_correct, user_id, question_id, response_time
                FROM question_responses 
                WHERE assessment_id = %s
                ORDER BY answered_at
            """
            cursor.execute(sql, (assessment_id,))
        else:
            sql = """
                SELECT bkt_before, is_correct, user_id, question_id, response_time
                FROM question_responses 
                ORDER BY answered_at
            """
            cursor.execute(sql)
        
        responses = cursor.fetchall()
        cursor.close()
        return responses

    def calculate_auc_roc_metrics(self, responses, threshold=0.2):
        """Calculate AUC-ROC and confusion matrix"""
        
        # Extract data
        bkt_predictions = [float(r['bkt_before']) for r in responses]
        actual_outcomes = [1 if r['is_correct'] else 0 for r in responses]
        
        # Convert BKT scores to binary predictions
        binary_predictions = [1 if score >= threshold else 0 for score in bkt_predictions]
        
        # Calculate AUC-ROC
        auc_score = roc_auc_score(actual_outcomes, bkt_predictions)
        
        # Calculate confusion matrix
        tn, fp, fn, tp = confusion_matrix(actual_outcomes, binary_predictions).ravel()
        
        # Calculate metrics
        accuracy = (tp + tn) / (tp + fp + tn + fn)
        precision = tp / (tp + fp) if (tp + fp) > 0 else 0
        recall = tp / (tp + fn) if (tp + fn) > 0 else 0
        specificity = tn / (tn + fp) if (tn + fp) > 0 else 0
        
        return {
            'auc_roc': auc_score,
            'confusion_matrix': {'tp': tp, 'fp': fp, 'tn': tn, 'fn': fn},
            'metrics': {
                'accuracy': accuracy,
                'precision': precision, 
                'recall': recall,
                'specificity': specificity
            },
            'threshold': threshold,
            'total_responses': len(responses)
        }

    def display_results(self, results):
        """Display results in clean format"""
        print("\n" + "="*60)
        print("BKT AUC-ROC ANALYSIS RESULTS")
        print("="*60)
        
        print(f"\nDATA SUMMARY:")
        print(f"Total Responses: {results['total_responses']}")
        print(f"BKT Threshold: {results['threshold']}")
        
        print(f"\nAUC-ROC PERFORMANCE:")
        print(f"AUC-ROC Score: {results['auc_roc']:.4f}")
        
        # Performance rating
        if results['auc_roc'] >= 0.8:
            rating = "EXCELLENT"
        elif results['auc_roc'] >= 0.7:
            rating = "GOOD" 
        elif results['auc_roc'] >= 0.6:
            rating = "FAIR"
        else:
            rating = "POOR"
        print(f"Performance: {rating}")
        
        # Confusion Matrix
        cm = results['confusion_matrix']
        print(f"\nCONFUSION MATRIX:")
        print(f"True Positive (TP):  {cm['tp']} - BKT predicted correct, student answered correctly")
        print(f"False Positive (FP): {cm['fp']} - BKT predicted correct, student answered incorrectly") 
        print(f"True Negative (TN):  {cm['tn']} - BKT predicted incorrect, student answered incorrectly")
        print(f"False Negative (FN): {cm['fn']} - BKT predicted incorrect, student answered correctly")
        
        # Key Metrics
        metrics = results['metrics']
        print(f"\nKEY METRICS:")
        print(f"Accuracy:  {metrics['accuracy']:.4f}")
        print(f"Precision: {metrics['precision']:.4f}")
        print(f"Recall:    {metrics['recall']:.4f}")
        print(f"Specificity: {metrics['specificity']:.4f}")
        
    def get_auc_interpretation(self, auc_score):
        """Get detailed interpretation of AUC-ROC score"""
        if auc_score >= 0.9:
            interpretation = "EXCELLENT - Outstanding discriminative ability"
            description = "The model has excellent discriminative power. It can distinguish between students who will answer correctly and incorrectly with very high accuracy."
        elif auc_score >= 0.8:
            interpretation = "GOOD - Excellent discriminative ability"
            description = "The model shows good discriminative power. It is performing well at distinguishing between correct and incorrect student responses."
        elif auc_score >= 0.7:
            interpretation = "FAIR - Acceptable discriminative ability"
            description = "The model demonstrates fair discriminative ability. While it performs better than random guessing, there is room for improvement."
        elif auc_score >= 0.6:
            interpretation = "POOR - Weak discriminative ability"
            description = "The model shows weak discriminative ability. Performance is only slightly better than random guessing."
        else:
            interpretation = "FAIL - No discriminative ability"
            description = "The model fails to discriminate between outcomes. Performance is no better than random guessing or worse."
        
        return {
            'rating': interpretation,
            'description': description,
            'score': auc_score
        }
    
    def visualize_roc_curve(self, responses, results, threshold=0.2):
        """Create ROC curve visualization"""
        bkt_predictions = [float(r['bkt_before']) for r in responses]
        actual_outcomes = [1 if r['is_correct'] else 0 for r in responses]
        
        # Calculate ROC curve
        fpr, tpr, thresholds = roc_curve(actual_outcomes, bkt_predictions)
        auc_score = results['auc_roc']
        
        # Create figure with subplots
        fig, axes = plt.subplots(1, 2, figsize=(14, 5))
        
        # Plot 1: ROC Curve
        ax1 = axes[0]
        ax1.plot(fpr, tpr, color='darkorange', lw=2, label=f'ROC curve (AUC = {auc_score:.4f})')
        ax1.plot([0, 1], [0, 1], color='navy', lw=2, linestyle='--', label='Random Classifier')
        ax1.set_xlim([0.0, 1.0])
        ax1.set_ylim([0.0, 1.05])
        ax1.set_xlabel('False Positive Rate', fontsize=11)
        ax1.set_ylabel('True Positive Rate', fontsize=11)
        ax1.set_title('ROC Curve - BKT Predictive Performance', fontsize=12, fontweight='bold')
        ax1.legend(loc="lower right", fontsize=10)
        ax1.grid(alpha=0.3)
        
        # Plot 2: Confusion Matrix Heatmap
        ax2 = axes[1]
        cm_data = np.array([
            [results['confusion_matrix']['tn'], results['confusion_matrix']['fp']],
            [results['confusion_matrix']['fn'], results['confusion_matrix']['tp']]
        ])
        sns.heatmap(cm_data, annot=True, fmt='d', cmap='Blues', ax=ax2, 
                    xticklabels=['Predicted Incorrect', 'Predicted Correct'],
                    yticklabels=['Actual Incorrect', 'Actual Correct'],
                    cbar_kws={'label': 'Count'})
        ax2.set_title('Confusion Matrix', fontsize=12, fontweight='bold')
        ax2.set_ylabel('Actual', fontsize=11)
        ax2.set_xlabel('Predicted', fontsize=11)
        
        plt.tight_layout()
        
        # Save figure
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_dir = os.path.join(os.path.dirname(__file__), 'visualizations')
        os.makedirs(output_dir, exist_ok=True)
        filepath = os.path.join(output_dir, f'auc_roc_analysis_{timestamp}.png')
        plt.savefig(filepath, dpi=300, bbox_inches='tight')
        print(f"\n✓ ROC Curve visualization saved to: {filepath}")
        plt.show()
        
        return filepath
    
    def visualize_performance_metrics(self, results):
        """Create performance metrics visualization"""
        metrics = results['metrics']
        metric_names = ['Accuracy', 'Precision', 'Recall', 'Specificity']
        metric_values = [metrics['accuracy'], metrics['precision'], metrics['recall'], metrics['specificity']]
        
        fig, ax = plt.subplots(figsize=(10, 6))
        
        colors = ['#2ecc71' if v >= 0.7 else '#f39c12' if v >= 0.5 else '#e74c3c' for v in metric_values]
        bars = ax.bar(metric_names, metric_values, color=colors, alpha=0.7, edgecolor='black', linewidth=1.5)
        
        # Add value labels on bars
        for bar, value in zip(bars, metric_values):
            height = bar.get_height()
            ax.text(bar.get_x() + bar.get_width()/2., height,
                   f'{value:.4f}',
                   ha='center', va='bottom', fontsize=11, fontweight='bold')
        
        ax.set_ylim([0, 1.1])
        ax.set_ylabel('Score', fontsize=12, fontweight='bold')
        ax.set_xlabel('Metrics', fontsize=12, fontweight='bold')
        ax.set_title(f"BKT Performance Metrics (AUC-ROC: {results['auc_roc']:.4f})", 
                     fontsize=13, fontweight='bold')
        ax.grid(axis='y', alpha=0.3, linestyle='--')
        ax.set_ylim([0, 1.1])
        
        plt.tight_layout()
        
        # Save figure
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        output_dir = os.path.join(os.path.dirname(__file__), 'visualizations')
        os.makedirs(output_dir, exist_ok=True)
        filepath = os.path.join(output_dir, f'performance_metrics_{timestamp}.png')
        plt.savefig(filepath, dpi=300, bbox_inches='tight')
        print(f"✓ Performance metrics visualization saved to: {filepath}")
        plt.show()
        
        return filepath
        
    def print_auc_interpretation(self, results):
        """Print AUC-ROC interpretation"""
        auc_score = results['auc_roc']
        interpretation = self.get_auc_interpretation(auc_score)
        
        print("\n" + "="*60)
        print("AUC-ROC INTERPRETATION")
        print("="*60)
        print(f"\nAUC-ROC Score: {auc_score:.4f}")
        print(f"Rating: {interpretation['rating']}")
        print(f"\nDescription:")
        print(f"{interpretation['description']}")
        
        # Additional insights
        print(f"\nKEY INSIGHTS:")
        cm = results['confusion_matrix']
        total_positive = cm['tp'] + cm['fn']
        total_negative = cm['tn'] + cm['fp']
        
        if total_positive > 0:
            sensitivity = cm['tp'] / total_positive
            print(f"✓ Sensitivity (True Positive Rate): {sensitivity:.4f}")
            print(f"  → Out of {total_positive} correct responses, BKT predicted {cm['tp']} correctly")
        
        if total_negative > 0:
            specificity = cm['tn'] / total_negative
            print(f"✓ Specificity (True Negative Rate): {specificity:.4f}")
            print(f"  → Out of {total_negative} incorrect responses, BKT predicted {cm['tn']} correctly")
        
        print("\n" + "="*60)
        
    
    def run_analysis(self, assessment_id=None, threshold=0.2, visualize=True):
        """Run the complete AUC-ROC analysis"""
        print("BKT AUC-ROC TESTING")
        print("Testing BKT predictive accuracy using real database data")
        
        # Fetch data
        responses = self.fetch_question_responses(assessment_id)
        
        if not responses:
            print("✗ No question responses found in database")
            return None
            
        print(f"✓ Loaded {len(responses)} question responses")
        
        # Calculate metrics
        results = self.calculate_auc_roc_metrics(responses, threshold)
        
        # Display results
        self.display_results(results)
        
        # Print AUC-ROC interpretation
        self.print_auc_interpretation(results)
        
        # Create visualizations
        if visualize:
            try:
                print("\n📊 Generating visualizations...")
                self.visualize_roc_curve(responses, results, threshold)
                self.visualize_performance_metrics(results)
                print("✓ All visualizations generated successfully!")
            except Exception as e:
                print(f"⚠ Warning: Could not generate visualizations: {e}")
        
        return results

    def test_multiple_thresholds(self, assessment_id=None):
        """Test multiple BKT thresholds to find optimal"""
        print("\nTESTING MULTIPLE THRESHOLDS")
        print("-" * 40)
        
        responses = self.fetch_question_responses(assessment_id)
        
        if not responses:
            return
            
        thresholds = [0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9]
        
        print(f"{'Threshold':<10} {'AUC-ROC':<10} {'Accuracy':<10}")
        print("-" * 30)
        
        best_threshold = 0.2
        best_accuracy = 0
        
        for threshold in thresholds:
            results = self.calculate_auc_roc_metrics(responses, threshold)
            print(f"{threshold:<10.1f} {results['auc_roc']:<10.4f} {results['metrics']['accuracy']:<10.4f}")
            
            if results['metrics']['accuracy'] > best_accuracy:
                best_accuracy = results['metrics']['accuracy']
                best_threshold = threshold
        
        print(f"\nOptimal Threshold: {best_threshold} (Accuracy: {best_accuracy:.4f})")
        return best_threshold

    def close_connection(self):
        """Close database connection"""
        if self.db_connection:
            self.db_connection.close()

def main():
    """Run the simplified BKT AUC-ROC test"""
    tester = SimpleBKTAUCTest()
    
    try:
        # Run basic analysis
        results = tester.run_analysis()
        
        if results:
            # Test multiple thresholds
            optimal_threshold = tester.test_multiple_thresholds()
            
            # Re-run with optimal threshold
            if optimal_threshold != 0.5:
                print(f"\nRE-ANALYZING WITH OPTIMAL THRESHOLD ({optimal_threshold})")
                tester.run_analysis(threshold=optimal_threshold)
                
    finally:
        tester.close_connection()

if __name__ == "__main__":
    main()