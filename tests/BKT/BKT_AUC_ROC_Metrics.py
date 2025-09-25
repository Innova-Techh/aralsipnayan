#!/usr/bin/env python3
"""
Simplified BKT AUC-ROC Metrics Test
Tests predictive accuracy using real data from question_responses table
Focuses on essential metrics: AUC-ROC, Confusion Matrix, and BKT performance
"""

import mysql.connector
from sklearn.metrics import roc_auc_score, confusion_matrix
import sys

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
                password='',
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
        
    def run_analysis(self, assessment_id=None, threshold=0.2):
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