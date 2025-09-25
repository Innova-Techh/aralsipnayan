#!/usr/bin/env python3
"""
Comprehensive BKT Performance Analysis
"""

import mysql.connector

def analyze_bkt_performance():
    conn = mysql.connector.connect(host='localhost', user='root', password='', database='aralsipnayandb')
    cursor = conn.cursor()

    print('COMPREHENSIVE BKT PERFORMANCE ANALYSIS')
    print('='*50)

    # 1. Check BKT vs Actual Performance Correlation
    print('\n1. BKT PREDICTION ACCURACY BY RANGE:')
    cursor.execute('''
        SELECT 
            CASE 
                WHEN bkt_after < 0.1 THEN '0.0-0.1'
                WHEN bkt_after < 0.2 THEN '0.1-0.2'
                WHEN bkt_after < 0.3 THEN '0.2-0.3'
                WHEN bkt_after < 0.4 THEN '0.3-0.4'
                WHEN bkt_after < 0.5 THEN '0.4-0.5'
                WHEN bkt_after < 0.6 THEN '0.5-0.6'
                WHEN bkt_after < 0.7 THEN '0.6-0.7'
                ELSE '0.7+'
            END as bkt_range,
            COUNT(*) as responses,
            AVG(is_correct) as actual_accuracy,
            AVG(bkt_after) as avg_bkt
        FROM question_responses 
        GROUP BY 
            CASE 
                WHEN bkt_after < 0.1 THEN '0.0-0.1'
                WHEN bkt_after < 0.2 THEN '0.1-0.2'
                WHEN bkt_after < 0.3 THEN '0.2-0.3'
                WHEN bkt_after < 0.4 THEN '0.3-0.4'
                WHEN bkt_after < 0.5 THEN '0.4-0.5'
                WHEN bkt_after < 0.6 THEN '0.5-0.6'
                WHEN bkt_after < 0.7 THEN '0.6-0.7'
                ELSE '0.7+'
            END
        ORDER BY AVG(bkt_after)
    ''')

    print('BKT Range | Count | Actual Acc | Avg BKT | Expected Acc | Correlation')
    print('-' * 70)
    
    correlation_issues = 0
    total_responses = 0
    
    for row in cursor.fetchall():
        bkt_range, count, actual_acc, avg_bkt = row
        actual_acc = float(actual_acc)
        avg_bkt = float(avg_bkt)
        correlation = abs(actual_acc - avg_bkt)
        status = 'GOOD' if correlation < 0.15 else 'POOR'
        if status == 'POOR':
            correlation_issues += count
        total_responses += count
        print(f'{bkt_range:8} | {count:5} | {actual_acc:10.3f} | {avg_bkt:7.3f} | {avg_bkt:12.3f} | {status}')

    print(f'\nCorrelation Issues: {correlation_issues}/{total_responses} ({correlation_issues/total_responses*100:.1f}%)')

    # 2. Check clustering issues
    print('\n2. CLUSTERING ANALYSIS:')
    cursor.execute('SELECT COUNT(*) FROM question_responses WHERE bkt_after <= 0.06')
    floor_count = cursor.fetchone()[0]
    
    cursor.execute('SELECT COUNT(*) FROM question_responses WHERE bkt_after >= 0.74')
    ceiling_count = cursor.fetchone()[0]
    
    print(f'Responses at floor (≤0.06): {floor_count} ({floor_count/total_responses*100:.1f}%)')
    print(f'Responses at ceiling (≥0.74): {ceiling_count} ({ceiling_count/total_responses*100:.1f}%)')
    print(f'Total clustering: {(floor_count + ceiling_count)/total_responses*100:.1f}%')
    
    # 3. Student diversity analysis
    print('\n3. STUDENT DIVERSITY ANALYSIS:')
    cursor.execute('''
        SELECT user_id, 
               COUNT(*) as responses,
               AVG(is_correct) as accuracy,
               STDDEV(bkt_after) as bkt_std
        FROM question_responses
        WHERE user_id > 1
        GROUP BY user_id
        HAVING responses >= 15
        ORDER BY accuracy
    ''')
    
    accuracies = []
    print('User | Responses | Accuracy | BKT StdDev | Profile')
    print('-' * 50)
    
    for row in cursor.fetchall():
        user_id, responses, accuracy, bkt_std = row
        accuracy = float(accuracy)
        bkt_std = float(bkt_std) if bkt_std else 0
        accuracies.append(accuracy)
        
        # Classify student profile
        if accuracy < 0.3:
            profile = 'Struggling'
        elif accuracy < 0.5:
            profile = 'Developing'
        elif accuracy < 0.7:
            profile = 'Proficient'
        else:
            profile = 'Advanced'
            
        print(f'{user_id:4} | {responses:9} | {accuracy:8.3f} | {bkt_std:10.3f} | {profile}')
    
    # Calculate diversity metrics
    if accuracies:
        acc_mean = sum(accuracies) / len(accuracies)
        acc_variance = sum((x - acc_mean)**2 for x in accuracies) / len(accuracies)
        print(f'\nStudent Accuracy Variance: {acc_variance:.4f}')
        print(f'Diversity Quality: {"GOOD" if acc_variance > 0.02 else "POOR"} (target > 0.02)')
    
    # 4. Sequential learning check
    print('\n4. SEQUENTIAL LEARNING CHECK:')
    cursor.execute('''
        SELECT user_id FROM question_responses 
        WHERE user_id > 1 
        GROUP BY user_id 
        HAVING COUNT(*) >= 20
        ORDER BY COUNT(*) DESC 
        LIMIT 1
    ''')
    
    sample_user = cursor.fetchone()
    if sample_user:
        sample_user_id = sample_user[0]
        cursor.execute('''
            SELECT is_correct, bkt_before, bkt_after
            FROM question_responses
            WHERE user_id = %s
            ORDER BY answered_at
            LIMIT 10
        ''', (sample_user_id,))
        
        print(f'Sample User {sample_user_id} Learning Sequence:')
        print('Response | Correct | BKT Before | BKT After | Change | Logic')
        print('-' * 60)
        
        sequence_issues = 0
        for i, row in enumerate(cursor.fetchall()):
            is_correct, bkt_before, bkt_after = row
            bkt_before = float(bkt_before)
            bkt_after = float(bkt_after)
            change = bkt_after - bkt_before
            correct_symbol = '✓' if is_correct else '✗'
            
            # Check learning logic
            if is_correct and change > 0:
                logic = 'GOOD'
            elif not is_correct and change <= 0:
                logic = 'GOOD' 
            else:
                logic = 'POOR'
                sequence_issues += 1
                
            print(f'{i+1:8} | {correct_symbol:7} | {bkt_before:10.3f} | {bkt_after:9.3f} | {change:6.3f} | {logic}')
        
        print(f'Learning Logic Issues: {sequence_issues}/10')

    cursor.close()
    conn.close()
    
    print('\n' + '='*50)
    print('BOTTLENECK IDENTIFICATION:')
    print('='*50)
    print(f'1. Correlation Issues: {correlation_issues/total_responses*100:.1f}% (target < 20%)')
    print(f'2. Clustering Issues: {(floor_count + ceiling_count)/total_responses*100:.1f}% (target < 40%)')  
    print(f'3. Student Diversity: {"ADEQUATE" if acc_variance > 0.02 else "INSUFFICIENT"} (variance = {acc_variance:.4f})')
    print(f'4. Learning Logic: {sequence_issues}/10 issues (target = 0)')

if __name__ == "__main__":
    analyze_bkt_performance()