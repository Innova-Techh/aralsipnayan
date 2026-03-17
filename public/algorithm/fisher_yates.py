#!/usr/bin/env python3
"""
Enhanced Fisher-Yates Shuffle Algorithm for AralSipnayan
Provides unbiased randomization with integrated cooldown system
Prevents duplicate questions and respects cooldown periods
"""

import random
import sys
import json
import os
import mysql.connector
from urllib.parse import urlparse
from datetime import datetime, timedelta

class EnhancedFisherYatesShuffle:
    def __init__(self):
        jawsdb_url = os.getenv('JAWSDB_URL')

        if jawsdb_url:
            parsed = urlparse(jawsdb_url)
            self.db_config = {
                'host': parsed.hostname,
                'user': parsed.username,
                'password': parsed.password,
                'database': parsed.path.lstrip('/'),
                'port': parsed.port or 3306,
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

    def get_questions_in_cooldown(self, user_id, competency):
        """
        Get list of question IDs that are currently in cooldown for the user
        FIXED: Properly check question_cooldowns table first
        """
        conn = self.connect_db()
        if not conn:
            return []
        
        try:
            cursor = conn.cursor()
            
            # PRIMARY: Check question_cooldowns table first (most accurate)
            cursor.execute("""
                SELECT DISTINCT question_id, cooldown_until, was_correct
                FROM question_cooldowns 
                WHERE user_id = %s 
                AND competency = %s
                AND is_active = TRUE
                AND cooldown_until > NOW()
                ORDER BY cooldown_until DESC
            """, (user_id, competency))
            
            cooldown_data = cursor.fetchall()
            cooldown_questions = [row[0] for row in cooldown_data]
            
            print(f"DEBUG: Found {len(cooldown_questions)} questions in cooldown table for user {user_id}, competency {competency}", file=sys.stderr)
            
            # SECONDARY: If no cooldown table entries, fall back to question_responses analysis
            if not cooldown_questions:
                print(f"DEBUG: No cooldown table entries found, checking question_responses fallback", file=sys.stderr)
                
                # Get questions answered correctly within 30 minutes (excluding diagnostic)
                cursor.execute("""
                    SELECT DISTINCT qr.question_id
                    FROM question_responses qr
                    JOIN assessments a ON qr.assessment_id = a.assessment_id
                    WHERE qr.user_id = %s 
                    AND a.competency = %s
                    AND qr.is_correct = TRUE
                    AND a.assessment_type = 'regular'
                    AND qr.answered_at >= DATE_SUB(NOW(), INTERVAL %s MINUTE)
                """, (user_id, competency, self.cooldown_rules['correct']))
                
                correct_cooldowns = [row[0] for row in cursor.fetchall()]
                
                # Get questions answered incorrectly within 60 minutes (excluding diagnostic)
                cursor.execute("""
                    SELECT DISTINCT qr.question_id
                    FROM question_responses qr
                    JOIN assessments a ON qr.assessment_id = a.assessment_id
                    WHERE qr.user_id = %s 
                    AND a.competency = %s
                    AND qr.is_correct = FALSE
                    AND a.assessment_type = 'regular'
                    AND qr.answered_at >= DATE_SUB(NOW(), INTERVAL %s MINUTE)
                """, (user_id, competency, self.cooldown_rules['incorrect']))
                
                incorrect_cooldowns = [row[0] for row in cursor.fetchall()]
                
                cooldown_questions = list(set(correct_cooldowns + incorrect_cooldowns))
                print(f"DEBUG: Fallback found {len(correct_cooldowns)} correct + {len(incorrect_cooldowns)} incorrect = {len(cooldown_questions)} total cooldown questions", file=sys.stderr)
            
            return cooldown_questions
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get cooldown questions: {e}", file=sys.stderr)
            return []
        finally:
            conn.close()

    def create_cooldown_entry(self, user_id, question_id, competency, was_correct, response_time):
        """
        Create a cooldown entry when a question is answered
        """
        conn = self.connect_db()
        if not conn:
            return False
        
        try:
            cursor = conn.cursor()
            
            # Calculate cooldown duration and end time
            cooldown_minutes = self.cooldown_rules['correct'] if was_correct else self.cooldown_rules['incorrect']
            cooldown_until = datetime.now() + timedelta(minutes=cooldown_minutes)
            
            # Generate unique cooldown_id
            import hashlib
            timestamp = int(datetime.now().timestamp())
            combined = f"{user_id}_{question_id}_{timestamp}"
            cooldown_id = f"CD_{hashlib.md5(combined.encode()).hexdigest()[:12]}"
            
            # Deactivate any existing cooldowns for this user-question pair
            cursor.execute("""
                UPDATE question_cooldowns 
                SET is_active = FALSE 
                WHERE user_id = %s AND question_id = %s AND is_active = TRUE
            """, (user_id, question_id))
            
            # Insert new cooldown entry
            cursor.execute("""
                INSERT INTO question_cooldowns 
                (cooldown_id, user_id, question_id, competency, was_correct, 
                 answered_at, response_time, cooldown_duration, cooldown_until, is_active)
                VALUES (%s, %s, %s, %s, %s, NOW(), %s, %s, %s, TRUE)
            """, (
                cooldown_id, user_id, question_id, competency, was_correct,
                response_time, cooldown_minutes, cooldown_until
            ))
            
            conn.commit()
            print(f"DEBUG: Created cooldown entry for question {question_id}, user {user_id}, expires at {cooldown_until}", file=sys.stderr)
            return True
            
        except mysql.connector.Error as e:
            conn.rollback()
            print(f"ERROR: Failed to create cooldown entry: {e}", file=sys.stderr)
            return False
        finally:
            conn.close()

    def cleanup_expired_cooldowns(self):
        """
        Clean up expired cooldown entries
        """
        conn = self.connect_db()
        if not conn:
            return False
        
        try:
            cursor = conn.cursor()
            
            # Deactivate expired cooldowns
            cursor.execute("""
                UPDATE question_cooldowns 
                SET is_active = FALSE 
                WHERE cooldown_until <= NOW() AND is_active = TRUE
            """)
            
            affected_rows = cursor.rowcount
            conn.commit()
            
            print(f"DEBUG: Cleaned up {affected_rows} expired cooldown entries", file=sys.stderr)
            return True
            
        except mysql.connector.Error as e:
            conn.rollback()
            print(f"ERROR: Failed to cleanup expired cooldowns: {e}", file=sys.stderr)
            return False
        finally:
            conn.close()


    def get_available_questions(self, user_id, competency, difficulty_level, requested_count=15):
        """
        Get questions available for assessment (not in cooldown, unique per user)
        FIXED: Enhanced cooldown checking and better filtering
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # First cleanup expired cooldowns
            self.cleanup_expired_cooldowns()
            
            # Get questions in cooldown (only exclude cooldown questions)
            cooldown_questions = self.get_questions_in_cooldown(user_id, competency)

            # Only exclude cooldown questions (removed recent usage filtering)
            excluded_questions = cooldown_questions

            print(f"DEBUG: Excluding {len(cooldown_questions)} cooldown questions", file=sys.stderr)
            
            # Build exclusion clause
            exclusion_clause = ""
            params = [competency, difficulty_level]
            
            if excluded_questions:
                placeholders = ','.join(['%s'] * len(excluded_questions))
                exclusion_clause = f"AND q.question_id NOT IN ({placeholders})"
                params.extend(excluded_questions)
            
            # Get available questions with higher limit to ensure we have enough
            query = f"""
                SELECT q.question_id, q.question_text, q.question_type, 
                       q.choice_a, q.choice_b, q.choice_c, q.choice_d,
                       q.correct_answer, q.difficulty_level, q.max_allowed_time,
                       q.topic_tag, q.explanation, q.base_points
                FROM questions q
                WHERE q.competency = %s 
                AND q.difficulty_level = %s 
                AND q.is_active = TRUE
                {exclusion_clause}
                ORDER BY RAND()
                LIMIT %s
            """
            
            # Request more questions than needed to account for potential filtering
            limit = min(requested_count * 3, 200)  # Increased multiplier and cap
            params.append(limit)
            
            cursor.execute(query, params)
            available_questions = cursor.fetchall()
            
            print(f"DEBUG: Found {len(available_questions)} available questions out of requested {requested_count}", file=sys.stderr)
            
            # Convert datetime objects to strings for JSON serialization
            for question in available_questions:
                for key, value in question.items():
                    if hasattr(value, 'strftime'):  # datetime object
                        question[key] = value.strftime('%Y-%m-%d %H:%M:%S')
            
            return {
                'success': True,
                'available_questions': available_questions[:requested_count],
                'total_found': len(available_questions),
                'excluded_cooldown': len(cooldown_questions),
                'excluded_recent': 0,  # No recent usage filtering anymore
                'excluded_total': len(excluded_questions),
                'requested_count': requested_count
            }
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get available questions: {e}", file=sys.stderr)
            return {
                'success': False, 
                'message': f'Failed to get available questions: {str(e)}'
            }
        finally:
            conn.close()

    def create_assessment_pool(self, assessment_id, user_id=None, competency=None, difficulty_level=None, question_count=15):
        """
        Create shuffled assessment pool with enhanced cooldown checking
        FIXED: Prevent duplicate assessments and ensure unique question pools
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor()
            
            # Check if assessment already has questions (assessment_questions table)
            cursor.execute("SELECT COUNT(*) FROM assessment_questions WHERE assessment_id = %s", (assessment_id,))
            existing_questions = cursor.fetchone()[0]
            
            if existing_questions > 0:
                print(f"DEBUG: Assessment {assessment_id} already has questions, skipping pool creation", file=sys.stderr)
                return {'success': False, 'message': 'Assessment questions already exist'}
            
            # Check if assessment record exists in assessments table
            cursor.execute("SELECT assessment_id FROM assessments WHERE assessment_id = %s", (assessment_id,))
            existing_assessment = cursor.fetchone()
            
            assessment_exists = existing_assessment is not None
            print(f"DEBUG: Assessment {assessment_id} exists in assessments table: {assessment_exists}", file=sys.stderr)
            
            # Extract parameters from assessment_id if not provided
            if user_id is None or competency is None or difficulty_level is None:
                parts = assessment_id.split('_')
                if len(parts) >= 5:
                    user_id = int(parts[1])
                    
                    # Handle competency that might contain underscores
                    if len(parts) >= 7:
                        competency = f"{parts[2]}_{parts[3]}"
                        difficulty_level = parts[4]
                    else:
                        competency = parts[2]
                        difficulty_level = parts[3]
                else:
                    return {'success': False, 'message': 'Invalid assessment_id format or missing parameters'}
            
            print(f"DEBUG: Creating assessment pool for user {user_id}, competency {competency}, difficulty {difficulty_level}", file=sys.stderr)
            
            # Get available questions (FIXED: enhanced cooldown checking)
            available_result = self.get_available_questions(
                user_id, competency, difficulty_level, question_count * 2  # Get more than needed
            )
            
            if not available_result['success']:
                return available_result
            
            available_questions = available_result['available_questions']
            
            if len(available_questions) < question_count:
                return {
                    'success': False,
                    'message': f'Insufficient questions available. Found {len(available_questions)}, need {question_count}',
                    'cooldown_count': available_result.get('excluded_cooldown', 0),
                    'recent_count': available_result.get('excluded_recent', 0),
                    'available_count': len(available_questions)
                }
            
            # Take only the requested number of questions
            questions_to_use = available_questions[:question_count]
            
            # Create assessment record only if it doesn't exist (PHP may have already created it)
            if not assessment_exists:
                try:
                    cursor.execute("""
                        INSERT INTO assessments 
                        (assessment_id, user_id, competency, assessment_type, difficulty_level, 
                         total_questions, status, is_diagnostic_phase, correct_answers, 
                         incorrect_answers, questions_answered, total_time_spent, 
                         cumulative_time_score)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    """, (
                        assessment_id, user_id, competency, 'regular', difficulty_level,
                        len(questions_to_use), 'in_progress', 0, 0, 0, 0, 0, 0.0000
                    ))
                    print(f"DEBUG: Created new assessment record {assessment_id}", file=sys.stderr)
                except mysql.connector.IntegrityError as e:
                    print(f"DEBUG: Assessment {assessment_id} race condition, already exists", file=sys.stderr)
                    # Continue with pool creation even if assessment creation failed due to race condition
            else:
                print(f"DEBUG: Assessment {assessment_id} already exists, proceeding with pool creation", file=sys.stderr)
            
            # Shuffle questions using Fisher-Yates
            shuffled_questions = self.fisher_yates_shuffle(questions_to_use)
            
            # Assign order numbers
            for index, question in enumerate(shuffled_questions):
                question['question_order'] = index + 1
            
            # Clean up any existing questions for this assessment (shouldn't happen with duplicate check)
            cursor.execute("DELETE FROM assessment_questions WHERE assessment_id = %s", (assessment_id,))
            
            # Insert shuffled questions into assessment_questions table
            for question in shuffled_questions:
                # Create unique pool_id
                import hashlib
                combined = f"{assessment_id}_{question['question_id']}"
                hash_suffix = hashlib.md5(combined.encode()).hexdigest()[:8]
                pool_id = f"POOL_{hash_suffix}_{question['question_order']:03d}"
                
                cursor.execute("""
                    INSERT INTO assessment_questions 
                    (pool_id, assessment_id, question_id, question_order, is_answered, is_current)
                    VALUES (%s, %s, %s, %s, %s, %s)
                """, (
                    pool_id, assessment_id, question['question_id'], 
                    question['question_order'], False,
                    question['question_order'] == 1  # First question is current
                ))
            
            conn.commit()
            print(f"DEBUG: Successfully created assessment pool with {len(shuffled_questions)} questions", file=sys.stderr)
            
            return {
                'success': True,
                'shuffled_questions': shuffled_questions,
                'total_questions': len(shuffled_questions),
                'excluded_cooldown': available_result.get('excluded_cooldown', 0),
                'excluded_recent': available_result.get('excluded_recent', 0),
                'available_pool_size': available_result.get('total_found', 0),
                'assessment_id': assessment_id
            }
            
        except mysql.connector.Error as e:
            conn.rollback()
            print(f"ERROR: Failed to create assessment pool: {e}", file=sys.stderr)
            return {
                'success': False, 
                'message': f'Failed to create assessment pool: {str(e)}'
            }
        except Exception as e:
            conn.rollback()
            print(f"ERROR: Unexpected error: {e}", file=sys.stderr)
            return {
                'success': False, 
                'message': f'Unexpected error: {str(e)}'
            }
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
                SELECT aq.*, q.question_text, q.question_type, q.choice_a, q.choice_b, 
                       q.choice_c, q.choice_d, q.correct_answer, q.difficulty_level, 
                       q.max_allowed_time, q.topic_tag, q.explanation, q.base_points
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
            
            # Convert datetime objects to strings for JSON serialization
            if question:
                for key, value in question.items():
                    if hasattr(value, 'strftime'):
                        question[key] = value.strftime('%Y-%m-%d %H:%M:%S')
            
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
            print(f"DEBUG: Marked question {question_id} as answered for assessment {assessment_id}", file=sys.stderr)
            
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
            'message': 'Usage: python enhanced_fisher_yates.py <action> [parameters]'
        }))
        return
    
    action = sys.argv[1]
    shuffle = EnhancedFisherYatesShuffle()
    
    try:
        if action == 'create_pool':
            if len(sys.argv) < 6:
                result = {
                    'success': False, 
                    'message': 'Missing parameters: assessment_id, user_id, competency, difficulty_level'
                }
            else:
                assessment_id = sys.argv[2]
                user_id = int(sys.argv[3])
                competency = sys.argv[4]
                difficulty_level = sys.argv[5]
                question_count = int(sys.argv[6]) if len(sys.argv) > 6 else 15
                
                result = shuffle.create_assessment_pool(
                    assessment_id, user_id, competency, difficulty_level, question_count
                )
                
        elif action == 'create_pool_file':
            # Enhanced backward compatibility with existing Laravel code
            if len(sys.argv) < 4:
                result = {'success': False, 'message': 'Missing parameters: assessment_id and file_path'}
            else:
                assessment_id = sys.argv[2]
                file_path = sys.argv[3]
                try:
                    # Extract user_id, competency, and difficulty from assessment_id
                    parts = assessment_id.split('_')
                    if len(parts) >= 5:
                        user_id = int(parts[1])
                        if len(parts) >= 7:
                            competency = f"{parts[2]}_{parts[3]}"
                            difficulty_level = parts[4]
                        else:
                            competency = parts[2]
                            difficulty_level = parts[3]
                        
                        # Determine question count from file or use default
                        question_count = 15
                        try:
                            with open(file_path, 'r', encoding='utf-8') as f:
                                questions = json.load(f)
                                question_count = len(questions)
                        except:
                            pass  # Use default count if file can't be read
                        
                        # Use enhanced assessment pool creation with proper cooldown checking
                        result = shuffle.create_assessment_pool(
                            assessment_id, user_id, competency, difficulty_level, question_count
                        )
                    else:
                        result = {'success': False, 'message': 'Invalid assessment_id format'}
                except Exception as e:
                    result = {'success': False, 'message': f'Error processing assessment: {str(e)}'}
                    
        elif action == 'create_cooldown':
            if len(sys.argv) < 7:
                result = {
                    'success': False, 
                    'message': 'Missing parameters: user_id, question_id, competency, was_correct, response_time'
                }
            else:
                user_id = int(sys.argv[2])
                question_id = sys.argv[3]
                competency = sys.argv[4]
                was_correct = sys.argv[5].lower() == 'true'
                response_time = float(sys.argv[6])
                
                success = shuffle.create_cooldown_entry(user_id, question_id, competency, was_correct, response_time)
                result = {'success': success}
                
        elif action == 'cleanup_cooldowns':
            success = shuffle.cleanup_expired_cooldowns()
            result = {'success': success}
                
        elif action == 'get_available':
            if len(sys.argv) < 5:
                result = {
                    'success': False, 
                    'message': 'Missing parameters: user_id, competency, difficulty_level'
                }
            else:
                user_id = int(sys.argv[2])
                competency = sys.argv[3]
                difficulty_level = sys.argv[4]
                count = int(sys.argv[5]) if len(sys.argv) > 5 else 15
                
                result = shuffle.get_available_questions(user_id, competency, difficulty_level, count)
                
        elif action == 'next_question':
            if len(sys.argv) < 3:
                result = {'success': False, 'message': 'Missing assessment_id parameter'}
            else:
                assessment_id = sys.argv[2]
                result = shuffle.get_next_question(assessment_id)
                
        elif action == 'mark_answered':
            if len(sys.argv) < 4:
                result = {
                    'success': False, 
                    'message': 'Missing parameters: assessment_id and question_id'
                }
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
                
        elif action == 'cooldown_check':
            if len(sys.argv) < 4:
                result = {
                    'success': False, 
                    'message': 'Missing parameters: user_id and competency'
                }
            else:
                user_id = int(sys.argv[2])
                competency = sys.argv[3]
                cooldown_questions = shuffle.get_questions_in_cooldown(user_id, competency)
                result = {
                    'success': True,
                    'cooldown_questions': cooldown_questions,
                    'cooldown_count': len(cooldown_questions)
                }
                
        else:
            result = {'success': False, 'message': f'Unknown action: {action}'}
        
        print(json.dumps(result, default=str))
        
    except Exception as e:
        print(json.dumps({
            'success': False, 
            'message': f'Error executing {action}: {str(e)}'
        }))

if __name__ == '__main__':
    main()