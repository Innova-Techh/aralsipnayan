#!/usr/bin/env python3
"""
Fisher-Yates Shuffle Algorithm for AralSipnayan
Provides unbiased randomization of question order
"""

import random
import sys
import json
import mysql.connector
from datetime import datetime

class FisherYatesShuffle:
    def __init__(self):
        self.db_config = {
            'host': 'localhost',
            'user': 'root',
            'password': '',
            'database': 'aralsipnayandb',
            'charset': 'utf8mb4'
        }

    def connect_db(self):
        """Establish database connection"""
        try:
            return mysql.connector.connect(**self.db_config)
        except mysql.connector.Error as e:
            print(f"ERROR: Database connection failed: {e}", file=sys.stderr)
            return None

    def fisher_yates_shuffle(self, array):
        """
        Implement Fisher-Yates shuffle algorithm
        Time Complexity: O(n)
        Space Complexity: O(1)
        """
        if not array:
            return array
        
        # Work with a copy to avoid modifying original
        shuffled = array.copy()
        n = len(shuffled)
        
        # Start from the last element and work backwards
        for i in range(n - 1, 0, -1):
            # Pick a random index from 0 to i (inclusive)
            j = random.randint(0, i)
            
            # Swap elements at positions i and j
            shuffled[i], shuffled[j] = shuffled[j], shuffled[i]
        
        return shuffled

    def shuffle_questions_for_assessment(self, questions_list):
        """
        Shuffle questions and assign order numbers
        Returns list of questions with order assigned
        """
        if not questions_list:
            return []
        
        # Shuffle the questions
        shuffled_questions = self.fisher_yates_shuffle(questions_list)
        
        # Assign order numbers
        for index, question in enumerate(shuffled_questions):
            question['question_order'] = index + 1
        
        return shuffled_questions

    def create_assessment_pool(self, assessment_id, questions):
        """
        Create shuffled assessment pool in database
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor()
            
            # Shuffle questions first
            shuffled_questions = self.shuffle_questions_for_assessment(questions)
            
            # Insert shuffled questions into assessment_questions table
            for question in shuffled_questions:
                pool_id = f"POOL_{assessment_id}_{question['question_id']}_{int(datetime.now().timestamp())}"
                
                cursor.execute("""
                    INSERT INTO assessment_questions 
                    (pool_id, assessment_id, question_id, question_order, is_answered, is_current)
                    VALUES (%s, %s, %s, %s, %s, %s)
                """, (
                    pool_id, 
                    assessment_id, 
                    question['question_id'], 
                    question['question_order'],
                    False,
                    question['question_order'] == 1  # First question is current
                ))
            
            conn.commit()
            
            return {
                'success': True, 
                'shuffled_questions': shuffled_questions,
                'total_questions': len(shuffled_questions)
            }
            
        except mysql.connector.Error as e:
            conn.rollback()
            print(f"ERROR: Failed to create assessment pool: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to create assessment pool'}
        finally:
            conn.close()

    def get_next_question(self, assessment_id):
        """
        Get the next unanswered question in order
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get current question or next unanswered question
            cursor.execute("""
                SELECT aq.*, q.question_text, q.question_type, q.options, 
                       q.correct_answer, q.difficulty_level, q.max_time_seconds,
                       q.topic, q.explanation
                FROM assessment_questions aq
                JOIN questions q ON aq.question_id = q.question_id
                WHERE aq.assessment_id = %s 
                AND aq.is_answered = FALSE
                ORDER BY aq.question_order ASC
                LIMIT 1
            """, (assessment_id,))
            
            question = cursor.fetchone()
            
            if not question:
                return {'success': False, 'message': 'No more questions available'}
            
            # Mark as current question
            cursor.execute("""
                UPDATE assessment_questions 
                SET is_current = FALSE 
                WHERE assessment_id = %s
            """, (assessment_id,))
            
            cursor.execute("""
                UPDATE assessment_questions 
                SET is_current = TRUE 
                WHERE pool_id = %s
            """, (question['pool_id'],))
            
            conn.commit()
            
            return {
                'success': True,
                'question': question
            }
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get next question: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to get next question'}
        finally:
            conn.close()

    def mark_question_answered(self, assessment_id, question_id):
        """
        Mark a question as answered
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor()
            
            cursor.execute("""
                UPDATE assessment_questions 
                SET is_answered = TRUE, is_current = FALSE
                WHERE assessment_id = %s AND question_id = %s
            """, (assessment_id, question_id))
            
            conn.commit()
            
            return {'success': True}
            
        except mysql.connector.Error as e:
            conn.rollback()
            print(f"ERROR: Failed to mark question answered: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to update question status'}
        finally:
            conn.close()

    def get_assessment_progress(self, assessment_id):
        """
        Get current progress of assessment
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            cursor.execute("""
                SELECT 
                    COUNT(*) as total_questions,
                    SUM(CASE WHEN is_answered = TRUE THEN 1 ELSE 0 END) as answered_questions,
                    MIN(CASE WHEN is_current = TRUE THEN question_order ELSE NULL END) as current_question_number
                FROM assessment_questions 
                WHERE assessment_id = %s
            """, (assessment_id,))
            
            progress = cursor.fetchone()
            
            return {
                'success': True,
                'progress': progress
            }
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get assessment progress: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to get progress'}
        finally:
            conn.close()

def main():
    """Main function for command line interface"""
    if len(sys.argv) < 2:
        print(json.dumps({
            'success': False, 
            'message': 'Usage: python fisher_yates.py <action> [parameters]'
        }))
        return
    
    action = sys.argv[1]
    shuffle = FisherYatesShuffle()
    
    try:
        if action == 'shuffle_test':
            # Test shuffling with sample data
            test_data = [{'id': i, 'name': f'Item {i}'} for i in range(1, 11)]
            result = shuffle.shuffle_questions_for_assessment(test_data)
            print(json.dumps({'success': True, 'shuffled': result}))
            
        elif action == 'create_pool':
            if len(sys.argv) < 4:
                result = {'success': False, 'message': 'Missing parameters: assessment_id and questions'}
            else:
                assessment_id = sys.argv[2]
                questions_json = sys.argv[3]
                questions = json.loads(questions_json)
                result = shuffle.create_assessment_pool(assessment_id, questions)
            
        elif action == 'next_question':
            if len(sys.argv) < 3:
                result = {'success': False, 'message': 'Missing assessment_id parameter'}
            else:
                assessment_id = sys.argv[2]
                result = shuffle.get_next_question(assessment_id)
                
        elif action == 'mark_answered':
            if len(sys.argv) < 4:
                result = {'success': False, 'message': 'Missing parameters: assessment_id and question_id'}
            else:
                assessment_id = sys.argv[2]
                question_id = sys.argv[3]
                result = shuffle.mark_question_answered(assessment_id, question_id)
                
        elif action == 'progress':
            if len(sys.argv) < 3:
                result = {'success': False, 'message': 'Missing assessment_id parameter'}
            else:
                assessment_id = sys.argv[2]
                result = shuffle.get_assessment_progress(assessment_id)
                
        else:
            result = {'success': False, 'message': f'Unknown action: {action}'}
        
        print(json.dumps(result))
        
    except Exception as e:
        print(json.dumps({
            'success': False, 
            'message': f'Error executing {action}: {str(e)}'
        }))

if __name__ == '__main__':
    main()