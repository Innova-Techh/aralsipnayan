#!/usr/bin/env python3
"""
Enhanced Fisher-Yates Shuffle Algorithm for AralSipnayan
Provides unbiased randomization with integrated cooldown system
Prevents duplicate questions and respects cooldown periods
"""

import random
import sys
import json
import mysql.connector
from datetime import datetime, timedelta

class EnhancedFisherYatesShuffle:
    def __init__(self):
        self.db_config = {
            'host': 'localhost',
            'user': 'root',
            'password': '',
            'database': 'aralsipnayandb',
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
        """
        conn = self.connect_db()
        if not conn:
            return []
        
        try:
            cursor = conn.cursor()
            
            # Check question_cooldowns table first (primary source)
            cursor.execute("""
                SELECT DISTINCT question_id
                FROM question_cooldowns 
                WHERE user_id = %s 
                AND competency = %s
                AND is_active = TRUE
                AND cooldown_until > NOW()
            """, (user_id, competency))
            
            cooldown_questions = [row[0] for row in cursor.fetchall()]
            
            # If no cooldown table entries, fall back to question_responses
            if not cooldown_questions:
                # Get questions answered correctly within 30 minutes (excluding diagnostic)
                cursor.execute("""
                    SELECT DISTINCT question_id
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
                    SELECT DISTINCT question_id
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
            
            return cooldown_questions
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get cooldown questions: {e}", file=sys.stderr)
            return []
        finally:
            conn.close()

    def get_recently_used_questions(self, user_id, competency, days=7):
        """
        Get questions recently used by user to avoid repetition
        """
        conn = self.connect_db()
        if not conn:
            return []
        
        try:
            cursor = conn.cursor()
            
            # Get questions used in recent assessments (last 7 days)
            cursor.execute("""
                SELECT DISTINCT qr.question_id
                FROM question_responses qr
                JOIN assessments a ON qr.assessment_id = a.assessment_id
                WHERE qr.user_id = %s 
                AND a.competency = %s
                AND a.assessment_type = 'regular'
                AND qr.answered_at >= DATE_SUB(NOW(), INTERVAL %s DAY)
                ORDER BY qr.answered_at DESC
            """, (user_id, competency, days))
            
            return [row[0] for row in cursor.fetchall()]
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to get recently used questions: {e}", file=sys.stderr)
            return []
        finally:
            conn.close()

    def get_available_questions(self, user_id, competency, difficulty_level, requested_count=15):
        """
        Get questions available for assessment (not in cooldown, unique per user)
        IMPLEMENTS RULE-BASED ALGORITHM (CogniSeq System) as documented:
        1. BKT FILTERING: Filter questions matching current difficulty level
        2. RULE-BASED FILTERING: Remove cooldown questions 
        3. ASSESSMENT COMPOSITION: Create 15-question assessment
        4. FISHER-YATES SHUFFLING: Randomize question order
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get questions in cooldown
            cooldown_questions = self.get_questions_in_cooldown(user_id, competency)
            recently_used = self.get_recently_used_questions(user_id, competency, days=3)
            
            # Combine all excluded questions
            excluded_questions = list(set(cooldown_questions + recently_used))
            
            # Build exclusion clause
            exclusion_clause = ""
            params = [competency, difficulty_level]
            
            if excluded_questions:
                placeholders = ','.join(['%s'] * len(excluded_questions))
                exclusion_clause = f"AND q.question_id NOT IN ({placeholders})"
                params.extend(excluded_questions)
            
            # Get available questions with higher limit to ensure we have enough
            # after filtering and shuffling
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
            limit = min(requested_count * 2, 100)  # Cap at 100 for performance
            params.append(limit)
            
            cursor.execute(query, params)
            available_questions = cursor.fetchall()
            
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
                'excluded_recent': len(recently_used),
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
        Create shuffled assessment pool with cooldown checking
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor()
            
            # Extract parameters from assessment_id if not provided (backward compatibility)
            if user_id is None or competency is None or difficulty_level is None:
                parts = assessment_id.split('_')
                if len(parts) >= 5:
                    user_id = int(parts[1])
                    
                    # Handle competency that might contain underscores
                    if len(parts) >= 7:  # Has multiple parts including underscore in competency
                        competency = f"{parts[2]}_{parts[3]}"  # "number_algebra"
                        difficulty_level = parts[4]  # "beginner"
                    else:
                        competency = parts[2]
                        difficulty_level = parts[3]
                else:
                    return {'success': False, 'message': 'Invalid assessment_id format or missing parameters'}
            
            # Get available questions (not in cooldown)
            available_result = self.get_available_questions(
                user_id, competency, difficulty_level, question_count
            )
            
            if not available_result['success']:
                return available_result
            
            available_questions = available_result['available_questions']
            
            if len(available_questions) < question_count:
                # Try to get more questions by reducing recent usage filter
                backup_result = self.get_available_questions(
                    user_id, competency, difficulty_level, question_count * 2
                )
                if backup_result['success']:
                    available_questions = backup_result['available_questions']
            
            if len(available_questions) == 0:
                return {
                    'success': False,
                    'message': f'No available questions found for {competency} {difficulty_level}',
                    'cooldown_count': available_result.get('excluded_cooldown', 0),
                    'recent_count': available_result.get('excluded_recent', 0)
                }
            
            # Take only the requested number of questions
            questions_to_use = available_questions[:question_count]
            
            # Ensure assessment record exists
            try:
                cursor.execute("SELECT assessment_id FROM assessments WHERE assessment_id = %s", (assessment_id,))
                if not cursor.fetchone():
                    # Create assessment record
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
            except mysql.connector.IntegrityError:
                # Assessment already exists, continue
                pass
            
            # Shuffle questions using Fisher-Yates
            shuffled_questions = self.fisher_yates_shuffle(questions_to_use)
            
            # Assign order numbers
            for index, question in enumerate(shuffled_questions):
                question['question_order'] = index + 1
            
            # Clean up any existing questions for this assessment
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
            
            return {
                'success': True,
                'shuffled_questions': shuffled_questions,
                'total_questions': len(shuffled_questions),
                'excluded_cooldown': available_result.get('excluded_cooldown', 0),
                'excluded_recent': available_result.get('excluded_recent', 0),
                'available_pool_size': available_result.get('total_found', 0)
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

    def validate_assessment_uniqueness(self, user_id, competency, questions):
        """
        Validate that assessment questions are unique for the user
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor()
            question_ids = [q['question_id'] for q in questions]
            
            if not question_ids:
                return {'success': True, 'duplicates': []}
            
            # Check for duplicates in recent assessments
            placeholders = ','.join(['%s'] * len(question_ids))
            cursor.execute(f"""
                SELECT DISTINCT qr.question_id, COUNT(*) as usage_count
                FROM question_responses qr
                JOIN assessments a ON qr.assessment_id = a.assessment_id
                WHERE qr.user_id = %s 
                AND a.competency = %s
                AND a.assessment_type = 'regular'
                AND qr.question_id IN ({placeholders})
                AND qr.answered_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                GROUP BY qr.question_id
            """, [user_id, competency] + question_ids)
            
            duplicates = cursor.fetchall()
            
            return {
                'success': True,
                'duplicates': [{'question_id': d[0], 'usage_count': d[1]} for d in duplicates],
                'has_duplicates': len(duplicates) > 0
            }
            
        except mysql.connector.Error as e:
            print(f"ERROR: Failed to validate uniqueness: {e}", file=sys.stderr)
            return {'success': False, 'message': 'Failed to validate uniqueness'}
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
            # Backward compatibility with existing Laravel code
            if len(sys.argv) < 4:
                result = {'success': False, 'message': 'Missing parameters: assessment_id and file_path'}
            else:
                assessment_id = sys.argv[2]
                file_path = sys.argv[3]
                try:
                    # Extract user_id, competency, and difficulty from assessment_id
                    # Format: ASSESS_{userId}_{competency}_{difficulty}_{i}_{timestamp}
                    parts = assessment_id.split('_')
                    if len(parts) >= 5:
                        user_id = int(parts[1])
                        if len(parts) >= 7:  # Has multiple parts including underscore in competency
                            competency = f"{parts[2]}_{parts[3]}"  # "number_algebra"
                            difficulty_level = parts[4]  # "beginner"
                        else:
                            competency = parts[2]
                            difficulty_level = parts[3]
                        
                        # Read questions from file (legacy support)
                        try:
                            with open(file_path, 'r', encoding='utf-8') as f:
                                questions = json.load(f)
                        except UnicodeDecodeError:
                            with open(file_path, 'r', encoding='utf-16') as f:
                                questions = json.load(f)
                        
                        # Instead of using file questions, get available questions with cooldown filtering
                        result = shuffle.create_assessment_pool(
                            assessment_id, user_id, competency, difficulty_level, len(questions)
                        )
                    else:
                        result = {'success': False, 'message': 'Invalid assessment_id format'}
                except FileNotFoundError:
                    result = {'success': False, 'message': 'Questions file not found'}
                except json.JSONDecodeError as e:
                    result = {'success': False, 'message': f'Invalid JSON in file: {str(e)}'}
                except Exception as e:
                    result = {'success': False, 'message': f'Error processing questions file: {str(e)}'}
                    
        elif action == 'create_pool_simple':
            # Simple backward compatibility - just need assessment_id
            if len(sys.argv) < 3:
                result = {'success': False, 'message': 'Missing assessment_id parameter'}
            else:
                assessment_id = sys.argv[2]
                result = shuffle.create_assessment_pool(assessment_id)
                
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
                
        elif action == 'validate_uniqueness':
            if len(sys.argv) < 5:
                result = {
                    'success': False, 
                    'message': 'Missing parameters: user_id, competency, questions_json'
                }
            else:
                user_id = int(sys.argv[2])
                competency = sys.argv[3]
                questions_json = sys.argv[4]
                
                try:
                    questions = json.loads(questions_json)
                    result = shuffle.validate_assessment_uniqueness(user_id, competency, questions)
                except json.JSONDecodeError:
                    result = {'success': False, 'message': 'Invalid JSON format for questions'}
                    
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