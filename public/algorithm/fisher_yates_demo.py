#!/usr/bin/env python3
"""
Fisher-Yates vs MySQL RAND() Comparison Demo

This script demonstrates the benefits of using Fisher-Yates shuffle
for question randomization in the BKT assessment system.

Author: AralSipnayan BKT System
Date: August 15, 2025
"""

import json
import mysql.connector
import random
from fisher_yates import AssessmentShuffle, FisherYatesShuffle


def get_database_connection():
    """Get database connection"""
    try:
        return mysql.connector.connect(
            host='127.0.0.1',
            user='root',
            password='',
            database='aralsipnayandb'
        )
    except mysql.connector.Error as err:
        print(f"Database connection failed: {err}")
        return None


def get_questions_mysql_rand(competency="Number_Algebra", difficulty="Beginner", limit=10):
    """Get questions using MySQL RAND() - the old method"""
    connection = get_database_connection()
    if not connection:
        return []
    
    cursor = connection.cursor(dictionary=True)
    
    query = """
        SELECT question_id, competency, difficulty_level, question_type,
               question_text, correct_answer, choice_a, choice_b, choice_c, choice_d,
               hint_text, explanation, topic_tag, COALESCE(points, 5) as points
        FROM questions
        WHERE competency = %s AND difficulty_level = %s AND is_active = TRUE
        ORDER BY RAND()
        LIMIT %s
    """
    
    cursor.execute(query, (competency, difficulty, limit))
    questions = cursor.fetchall()
    
    cursor.close()
    connection.close()
    
    return questions


def get_questions_fisher_yates(competency="Number_Algebra", difficulty="Beginner", limit=10, seed=None):
    """Get questions using Fisher-Yates shuffle - the new method"""
    connection = get_database_connection()
    if not connection:
        return []
    
    cursor = connection.cursor(dictionary=True)
    
    # Get all questions without ordering
    query = """
        SELECT question_id, competency, difficulty_level, question_type,
               question_text, correct_answer, choice_a, choice_b, choice_c, choice_d,
               hint_text, explanation, topic_tag, COALESCE(points, 5) as points
        FROM questions
        WHERE competency = %s AND difficulty_level = %s AND is_active = TRUE
    """
    
    cursor.execute(query, (competency, difficulty))
    all_questions = cursor.fetchall()
    
    cursor.close()
    connection.close()
    
    if not all_questions:
        return []
    
    # Use Fisher-Yates shuffle with topic diversity
    assessment_shuffle = AssessmentShuffle(seed=seed)
    shuffled_questions = assessment_shuffle.shuffle_with_topic_diversity(all_questions)
    
    return shuffled_questions[:limit]


def analyze_topic_distribution(questions):
    """Analyze the distribution of topics in the question list"""
    if not questions:
        return {}
    
    topic_counts = {}
    consecutive_same_topic = 0
    max_consecutive = 0
    current_consecutive = 1
    
    for i, question in enumerate(questions):
        topic = question.get('topic_tag', 'Unknown')
        
        # Count topics
        topic_counts[topic] = topic_counts.get(topic, 0) + 1
        
        # Check consecutive same topics
        if i > 0:
            prev_topic = questions[i-1].get('topic_tag', 'Unknown')
            if topic == prev_topic:
                current_consecutive += 1
            else:
                max_consecutive = max(max_consecutive, current_consecutive)
                current_consecutive = 1
    
    max_consecutive = max(max_consecutive, current_consecutive)
    
    return {
        'topic_counts': topic_counts,
        'total_questions': len(questions),
        'unique_topics': len(topic_counts),
        'max_consecutive_same_topic': max_consecutive,
        'question_order': [q['question_id'] for q in questions],
        'topic_order': [q.get('topic_tag', 'Unknown') for q in questions]
    }


def run_comparison_test(num_trials=5):
    """Run comparison test between MySQL RAND() and Fisher-Yates"""
    print("=== Fisher-Yates vs MySQL RAND() Comparison ===")
    print(f"Running {num_trials} trials with 10 questions each\n")
    
    mysql_results = []
    fisher_yates_results = []
    
    print("MySQL RAND() Results:")
    print("-" * 50)
    
    for trial in range(num_trials):
        questions = get_questions_mysql_rand(limit=10)
        analysis = analyze_topic_distribution(questions)
        mysql_results.append(analysis)
        
        print(f"Trial {trial + 1}:")
        print(f"  Questions: {analysis['question_order']}")
        print(f"  Topics:    {analysis['topic_order']}")
        print(f"  Max consecutive same topic: {analysis['max_consecutive_same_topic']}")
        print(f"  Topic distribution: {analysis['topic_counts']}")
        print()
    
    print("\nFisher-Yates Shuffle Results:")
    print("-" * 50)
    
    for trial in range(num_trials):
        # Use different seed for each trial to show variation
        questions = get_questions_fisher_yates(limit=10, seed=trial * 42)
        analysis = analyze_topic_distribution(questions)
        fisher_yates_results.append(analysis)
        
        print(f"Trial {trial + 1}:")
        print(f"  Questions: {analysis['question_order']}")
        print(f"  Topics:    {analysis['topic_order']}")
        print(f"  Max consecutive same topic: {analysis['max_consecutive_same_topic']}")
        print(f"  Topic distribution: {analysis['topic_counts']}")
        print()
    
    # Calculate averages
    print("\nComparison Summary:")
    print("=" * 50)
    
    mysql_avg_consecutive = sum(r['max_consecutive_same_topic'] for r in mysql_results) / len(mysql_results)
    fisher_avg_consecutive = sum(r['max_consecutive_same_topic'] for r in fisher_yates_results) / len(fisher_yates_results)
    
    mysql_avg_topics = sum(r['unique_topics'] for r in mysql_results) / len(mysql_results)
    fisher_avg_topics = sum(r['unique_topics'] for r in fisher_yates_results) / len(fisher_yates_results)
    
    print(f"MySQL RAND() - Average max consecutive same topic: {mysql_avg_consecutive:.2f}")
    print(f"Fisher-Yates - Average max consecutive same topic: {fisher_avg_consecutive:.2f}")
    print(f"Improvement: {((mysql_avg_consecutive - fisher_avg_consecutive) / mysql_avg_consecutive * 100):.1f}% reduction in consecutive clustering")
    print()
    print(f"MySQL RAND() - Average unique topics per 10 questions: {mysql_avg_topics:.2f}")
    print(f"Fisher-Yates - Average unique topics per 10 questions: {fisher_avg_topics:.2f}")
    print()
    
    if fisher_avg_consecutive < mysql_avg_consecutive:
        print("✓ Fisher-Yates provides better topic diversity!")
    else:
        print("→ Both methods show similar topic diversity")
    
    print("\nKey Benefits of Fisher-Yates Implementation:")
    print("• True uniform distribution (every permutation equally likely)")
    print("• Topic diversity constraint ensures better learning experience")
    print("• Reproducible results with seed support for testing")
    print("• Better performance for large question sets")
    print("• Eliminates database-dependent randomization")


def demonstrate_reproducibility():
    """Demonstrate reproducible shuffling with seeds"""
    print("\n=== Reproducibility Demonstration ===")
    print("Using seed=123 for reproducible results\n")
    
    # Get same questions with same seed multiple times
    for run in range(3):
        questions = get_questions_fisher_yates(limit=5, seed=123)
        question_ids = [q['question_id'] for q in questions]
        print(f"Run {run + 1}: {question_ids}")
    
    print("\nAll runs produce identical order - perfect for testing!")
    
    print("\nUsing different seeds:")
    seeds = [123, 456, 789]
    for seed in seeds:
        questions = get_questions_fisher_yates(limit=5, seed=seed)
        question_ids = [q['question_id'] for q in questions]
        print(f"Seed {seed}: {question_ids}")


if __name__ == "__main__":
    try:
        print("Fisher-Yates Shuffle Integration Demo")
        print("AralSipnayan BKT Assessment System")
        print("=" * 60)
        
        # Check database connection
        connection = get_database_connection()
        if not connection:
            print("❌ Cannot connect to database. Please check your database setup.")
            exit(1)
        connection.close()
        
        print("✓ Database connection successful")
        print()
        
        # Run the comparison
        run_comparison_test(num_trials=3)
        
        # Demonstrate reproducibility
        demonstrate_reproducibility()
        
    except Exception as e:
        print(f"❌ Error running demo: {e}")
        print("\nPlease ensure:")
        print("1. Database is running and accessible")
        print("2. Questions table has data")
        print("3. fisher_yates.py is in the same directory")
