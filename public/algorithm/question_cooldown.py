#!/usr/bin/env python3
"""
Question Cooldown System for AralSipnayan
Rule-based algorithm for question availability based on previous performance
"""

import sys
import json
import os
import mysql.connector
from datetime import datetime, timedelta

class QuestionCooldown:
    def __init__(self):
        if os.getenv('DYNO') or os.getenv('JAWSDB_URL'):
            self.db_config = {
                'host': 'nuskkyrsgmn5rw8c.cbetxkdyhwsb.us-east-1.rds.amazonaws.com',
                'user': 'imnsg8f1wjljg1p2',
                'password': 'q5qgardw1jlkpqa3',
                'database': 'ir2h6saapp46zwh1',
                'port': 3306,
                'charset': 'utf8mb4'
            }
        else:
            self.db_config = {
                'host': 'localhost',
                'user': 'root',
                'password': '',
                'database': 'aralsipnayandb',
                'port': 3306,
                'charset': 'utf8mb4'
            }
        
        # Cooldown rules (in minutes)
        self.cooldown_rules = {
            'correct': 30,      # 30 minutes for correct answers
            'incorrect': 60     # 60 minutes for incorrect answers
        }

    def connect_db(self):
        """Establish database connection"""
        try:
            return mysql.connector.connect(**self.db_config)
        except mysql.connector.Error as e:
            print(f"ERROR: Database connection failed: {e}", file=sys.stderr)
            return None

    def get_question_last_attempt(self, user_id, question_id):
        """
        Get the last attempt details for a specific question by user
        """
        conn = self.connect_db()
        if not conn:
            return None
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            cursor.execute("""
                SELECT question_id, is_correct, answered_at
                FROM question_responses 
                WHERE user_id = %s AND question_id = %s
                ORDER BY answered_at DESC
                LIMIT 1
            """, (user_id, question_id))
            
            return cursor.fetchone()
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get last attempt: {e}", file=sys.stderr)
            return None
        finally:
            conn.close()

    def calculate_cooldown_expiry(self, last_attempt_time, was_correct):
        """
        Calculate when cooldown expires based on last attempt
        """
        cooldown_minutes = self.cooldown_rules['correct'] if was_correct else self.cooldown_rules['incorrect']
        return last_attempt_time + timedelta(minutes=cooldown_minutes)

    def is_question_available(self, user_id, question_id):
        """
        Check if a question is available for the user (not in cooldown)
        """
        last_attempt = self.get_question_last_attempt(user_id, question_id)
        
        if not last_attempt:
            # Question never attempted - available
            return True
        
        cooldown_expiry = self.calculate_cooldown_expiry(
            last_attempt['answered_at'], 
            last_attempt['is_correct']
        )
        
        current_time = datetime.now()
        
        # Check if cooldown has expired
        return current_time >= cooldown_expiry

    def get_available_questions(self, user_id, competency, difficulty_level, limit=20):
        """
        Get questions that are available (not in cooldown) for assessment
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get all questions for the competency and difficulty
            cursor.execute("""
                SELECT q.question_id, q.question_text, q.question_type, q.options,
                       q.correct_answer, q.difficulty_level, q.max_time_seconds,
                       q.topic, q.explanation, q.competency
                FROM questions q
                WHERE q.competency = %s 
                AND q.difficulty_level = %s 
                AND q.is_active = 1
                ORDER BY RAND()
            """, (competency, difficulty_level))
            
            all_questions = cursor.fetchall()
            available_questions = []
            
            # Filter out questions in cooldown
            for question in all_questions:
                if self.is_question_available(user_id, question['question_id']):
                    available_questions.append(question)
                
                # Stop when we have enough questions
                if len(available_questions) >= limit:
                    break
            
            return {
                'success': True,
                'available_questions': available_questions,
                'total_available': len(available_questions),
                'requested_limit': limit
            }
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get available questions: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to get available questions'}
        finally:
            conn.close()

    def get_cooldown_status(self, user_id, question_id):
        """
        Get detailed cooldown status for a specific question
        """
        last_attempt = self.get_question_last_attempt(user_id, question_id)
        
        if not last_attempt:
            return {
                'is_available': True,
                'never_attempted': True,
                'cooldown_expires': None,
                'minutes_remaining': 0
            }
        
        cooldown_expiry = self.calculate_cooldown_expiry(
            last_attempt['answered_at'], 
            last_attempt['is_correct']
        )
        
        current_time = datetime.now()
        is_available = current_time >= cooldown_expiry
        
        minutes_remaining = 0
        if not is_available:
            time_diff = cooldown_expiry - current_time
            minutes_remaining = max(0, int(time_diff.total_seconds() / 60))
        
        return {
            'is_available': is_available,
            'never_attempted': False,
            'last_attempt': last_attempt,
            'cooldown_expires': cooldown_expiry,
            'minutes_remaining': minutes_remaining,
            'was_correct': last_attempt['is_correct']
        }

    def get_user_cooldown_summary(self, user_id, competency):
        """
        Get summary of all questions in cooldown for a user in a competency
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get all attempted questions for this competency
            cursor.execute("""
                SELECT DISTINCT qr.question_id, qr.is_correct, qr.answered_at,
                       q.question_text, q.difficulty_level, q.topic
                FROM question_responses qr
                JOIN questions q ON qr.question_id = q.question_id
                WHERE qr.user_id = %s AND q.competency = %s
                ORDER BY qr.answered_at DESC
            """, (user_id, competency))
            
            attempted_questions = cursor.fetchall()
            cooldown_summary = []
            
            for attempt in attempted_questions:
                status = self.get_cooldown_status(user_id, attempt['question_id'])
                
                if not status['is_available']:
                    cooldown_summary.append({
                        'question_id': attempt['question_id'],
                        'question_text': attempt['question_text'][:100] + '...',
                        'difficulty_level': attempt['difficulty_level'],
                        'topic': attempt['topic'],
                        'last_attempt': attempt['answered_at'],
                        'was_correct': attempt['is_correct'],
                        'minutes_remaining': status['minutes_remaining'],
                        'cooldown_expires': status['cooldown_expires']
                    })
            
            return {
                'success': True,
                'cooldown_questions': cooldown_summary,
                'total_in_cooldown': len(cooldown_summary)
            }
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get cooldown summary: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to get cooldown summary'}
        finally:
            conn.close()

    def cleanup_expired_cooldowns(self):
        """
        Optional: Clean up tracking for very old attempts (housekeeping)
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor()
            
            # Remove response records older than 7 days (optional cleanup)
            cutoff_date = datetime.now() - timedelta(days=7)
            
            cursor.execute("""
                DELETE FROM question_responses 
                WHERE answered_at < %s
            """, (cutoff_date,))
            
            deleted_count = cursor.rowcount
            conn.commit()
            
            return {
                'success': True,
                'deleted_records': deleted_count,
                'cutoff_date': cutoff_date
            }
            
        except mysql.connector.Error as e:
            conn.rollback()
            print(f"ERROR: Failed to cleanup cooldowns: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to cleanup cooldowns'}
        finally:
            conn.close()

def main():
    """Main function for command line interface"""
    if len(sys.argv) < 2:
        print(json.dumps({
            'success': False, 
            'message': 'Usage: python question_cooldown.py <action> [parameters]'
        }))
        return
    
    action = sys.argv[1]
    cooldown = QuestionCooldown()
    
    try:
        if action == 'check_question':
            if len(sys.argv) < 4:
                result = {'success': False, 'message': 'Missing parameters: user_id and question_id'}
            else:
                user_id = int(sys.argv[2])
                question_id = sys.argv[3]
                is_available = cooldown.is_question_available(user_id, question_id)
                status = cooldown.get_cooldown_status(user_id, question_id)
                result = {'success': True, 'is_available': is_available, 'status': status}
                
        elif action == 'get_available':
            if len(sys.argv) < 5:
                result = {'success': False, 'message': 'Missing parameters: user_id, competency, difficulty_level'}
            else:
                user_id = int(sys.argv[2])
                competency = sys.argv[3]
                difficulty_level = sys.argv[4]
                limit = int(sys.argv[5]) if len(sys.argv) > 5 else 20
                result = cooldown.get_available_questions(user_id, competency, difficulty_level, limit)
                
        elif action == 'cooldown_summary':
            if len(sys.argv) < 4:
                result = {'success': False, 'message': 'Missing parameters: user_id and competency'}
            else:
                user_id = int(sys.argv[2])
                competency = sys.argv[3]
                result = cooldown.get_user_cooldown_summary(user_id, competency)
                
        elif action == 'cleanup':
            result = cooldown.cleanup_expired_cooldowns()
            
        else:
            result = {'success': False, 'message': f'Unknown action: {action}'}
        
        print(json.dumps(result, default=str))  # default=str for datetime serialization
        
    except Exception as e:
        print(json.dumps({
            'success': False, 
            'message': f'Error executing {action}: {str(e)}'
        }))

if __name__ == '__main__':
    main()