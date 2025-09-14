#!/usr/bin/env python3
"""
AralSipnayan BKT Algorithm Implementation
Bayesian Knowledge Tracing with Time and Difficulty Factors
"""

import sys
import json
import mysql.connector
from datetime import datetime, timedelta
import math
import uuid
import os
from decimal import Decimal, ROUND_HALF_UP

class BKTAlgorithm:
    def __init__(self):
        self.db_config = {
            'host': 'localhost',
            'user': 'root',
            'password': '',
            'database': 'aralsipnayandb',
            'charset': 'utf8mb4'
        }
        
        # BKT Default Parameters
        self.default_params = {
            'prior_knowledge': 0.1000,  # P(L0)
            'learn_rate': 0.3000,       # P(T)
            'slip_rate': 0.1000,        # P(S)
            'guess_rate': 0.2500        # P(G)
        }
        
        # Difficulty factors
        self.difficulty_factors = {
            'beginner': 0.8,
            'intermediate': 1.0,
            'advanced': 1.2
        }
        
        # Max allowed time per difficulty (seconds)
        self.max_times = {
            'beginner': 30,
            'intermediate': 45,
            'advanced': 60
        }
        
        # Mastery score weights
        self.weights = {
            'accuracy': 0.55,
            'bkt': 0.45
        }

    def connect_db(self):
        """Establish database connection"""
        try:
            return mysql.connector.connect(**self.db_config)
        except mysql.connector.Error as e:
            self.log_error(f"Database connection failed: {e}")
            return None

    def log_error(self, message):
        """Log error messages"""
        print(f"ERROR: {message}", file=sys.stderr)

    def calculate_time_score(self, response_time, max_allowed_time, is_correct):
        """
        Calculate time score based on AralSipnayan formula
        """
        normalized_time = response_time / max_allowed_time
        
        # Apply time scoring rules
        if is_correct:
            if normalized_time <= 0.5:
                return 1.0  # Fast + correct
            elif normalized_time <= 0.8:
                return 0.8  # Medium + correct
            else:
                return 0.6  # Slow + correct
        else:
            if normalized_time <= 0.5:
                return 0.2  # Fast + wrong
            else:
                return 0.1  # Slow + wrong
        
        # Timeout case (should be handled before this function)
        return 0.0

    def calculate_bkt_update(self, prior_prob, is_correct, params, time_factor, difficulty_factor):
        """
        Calculate BKT probability update with time and difficulty factors
        """
        P_L = prior_prob
        P_T = params['learn_rate']
        P_S = params['slip_rate']
        P_G = params['guess_rate']
        
        if is_correct:
            # For correct answers
            numerator = P_L * (1 - P_S) * time_factor * difficulty_factor
            denominator = P_L * (1 - P_S) + (1 - P_L) * P_G
        else:
            # For incorrect answers
            numerator = P_L * P_S
            denominator = P_L * P_S + (1 - P_L) * (1 - P_G) * time_factor * difficulty_factor
        
        if denominator == 0:
            return P_L  # Return prior if denominator is zero
        
        # Apply learning transition
        P_L_new = numerator / denominator
        if not is_correct:
            # Only apply learning for correct answers in traditional BKT
            P_L_new = P_L_new + (1 - P_L_new) * P_T
        else:
            P_L_new = P_L_new + (1 - P_L_new) * P_T
            
        return min(max(P_L_new, 0.0), 1.0)  # Clamp between 0 and 1

    def calculate_mastery_score(self, accuracy_score, bkt_score):
        """
        Calculate final mastery score using weighted formula
        """
        return (self.weights['accuracy'] * accuracy_score + 
                self.weights['bkt'] * bkt_score) * 100

    def determine_difficulty_level(self, mastery_score):
        """
        Determine difficulty level based on mastery score
        """
        if mastery_score <= 75:
            return 'beginner'
        elif mastery_score > 75 and mastery_score <= 84:
            return 'intermediate'
        elif mastery_score > 84 and mastery_score <= 100:
            return 'advanced'
        else:
            return 'beginner'

    def start_diagnostic(self, user_id, competency):
        """
        Start a diagnostic session for a competency
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Set shorter lock timeout
            cursor.execute("SET SESSION innodb_lock_wait_timeout = 5")
            
            # Check if diagnostic is actually completed (check student_mastery table)
            cursor.execute("""
                SELECT has_taken_diagnostic FROM student_mastery 
                WHERE user_id = %s AND competency = %s
            """, (user_id, competency))
            
            mastery = cursor.fetchone()
            if mastery and mastery['has_taken_diagnostic']:
                return {'success': False, 'message': 'Diagnostic already taken for this competency'}
            
            # Clean up any incomplete diagnostic sessions (be more specific)
            cursor.execute("""
                DELETE FROM diagnostic_sessions 
                WHERE user_id = %s AND competency = %s AND status = 'in_progress' 
                AND started_at < DATE_SUB(NOW(), INTERVAL 2 HOUR)
            """, (user_id, competency))
            
            # Check if there's a recent in-progress session (within last 2 hours)
            cursor.execute("""
                SELECT session_id FROM diagnostic_sessions 
                WHERE user_id = %s AND competency = %s AND status = 'in_progress'
                AND started_at >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
            """, (user_id, competency))
            
            existing_session = cursor.fetchone()
            if existing_session:
                return {
                    'success': False, 
                    'message': 'You have a diagnostic session in progress. Please complete it first.'
                }
            
            # Create new diagnostic session with timestamp to avoid conflicts
            session_id = f"DIAG_{user_id}_{competency}_{int(datetime.now().timestamp())}"
            
            # Use autocommit mode to avoid transaction issues
            conn.autocommit = True
            
            cursor.execute("""
                INSERT INTO diagnostic_sessions 
                (session_id, user_id, competency, current_phase, status, started_at)
                VALUES (%s, %s, %s, 1, 'in_progress', NOW())
            """, (session_id, user_id, competency))
            
            # Create corresponding assessment record for foreign key constraint
            cursor.execute("""
                INSERT INTO assessments 
                (assessment_id, user_id, competency, assessment_type, difficulty_level, 
                 total_questions, status, is_diagnostic_phase, diagnostic_phase, started_at)
                VALUES (%s, %s, %s, 'diagnostic', 'beginner', 15, 'in_progress', TRUE, 1, NOW())
            """, (session_id, user_id, competency))
            
            # Get Phase 1 (Beginner) questions
            questions = self.get_diagnostic_questions(competency, 'beginner', 5)
            
            return {
                'success': True,
                'session_id': session_id,
                'phase': 1,
                'phase_name': 'beginner',
                'questions': questions,
                'total_phases': 3
            }
            
        except mysql.connector.Error as e:
            error_code = e.errno if hasattr(e, 'errno') else 0
            if error_code == 1062:  # Duplicate entry
                return {'success': False, 'message': 'Diagnostic session already exists'}
            elif error_code == 1205:  # Lock timeout
                return {'success': False, 'message': 'System busy, please try again'}
            else:
                self.log_error(f"Failed to start diagnostic: {e}")
                return {'success': False, 'message': 'Failed to start diagnostic'}
        finally:
            if conn and conn.is_connected():
                conn.close()

    def get_diagnostic_questions(self, competency, difficulty, count):
        """
        Get questions for diagnostic phase
        """
        conn = self.connect_db()
        if not conn:
            return []
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            cursor.execute("""
                SELECT question_id, question_text, question_type, correct_answer, explanation,
                       difficulty_level, max_allowed_time, topic_tag as topic,
                       choice_a, choice_b, choice_c, choice_d
                FROM questions 
                WHERE competency = %s AND difficulty_level = %s AND is_active = 1
                ORDER BY RAND()
                LIMIT %s
            """, (competency, difficulty, count))
            
            questions = cursor.fetchall()
            
            # Process questions to format options correctly
            for question in questions:
                if question['question_type'] == 'multiple_choice':
                    # Create options array from individual choice columns
                    options = []
                    if question['choice_a']: options.append(question['choice_a'])
                    if question['choice_b']: options.append(question['choice_b'])
                    if question['choice_c']: options.append(question['choice_c'])
                    if question['choice_d']: options.append(question['choice_d'])
                    
                    question['options'] = json.dumps(options)
                elif question['question_type'] == 'true_false':
                    # Create True/False options
                    options = ["True", "False"]
                    question['options'] = json.dumps(options)
                else:
                    # For fill_blanks and other types, no options needed
                    question['options'] = '[]'
                
                # Rename for consistency
                question['max_time_seconds'] = question['max_allowed_time']
            
            return questions
            
        except mysql.connector.Error as e:
            self.log_error(f"Failed to get diagnostic questions: {e}")
            return []
        finally:
            conn.close()

    def record_diagnostic_answer(self, session_id, question_id, user_answer, response_time, is_correct):
        """
        Record answer for diagnostic phase
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get diagnostic session info
            cursor.execute("""
                SELECT user_id, competency, current_phase FROM diagnostic_sessions
                WHERE session_id = %s
            """, (session_id,))
            
            session = cursor.fetchone()
            if not session:
                return {'success': False, 'message': 'Diagnostic session not found'}
            
            # Get question info for max_allowed_time
            cursor.execute("""
                SELECT max_allowed_time FROM questions
                WHERE question_id = %s
            """, (question_id,))
            
            question = cursor.fetchone()
            max_allowed_time = question['max_allowed_time'] if question else 60
            
            # Get current BKT knowledge state (simplified for diagnostic)
            cursor.execute("""
                SELECT bkt_score FROM student_mastery
                WHERE user_id = %s AND competency = %s
            """, (session['user_id'], session['competency']))
            
            mastery = cursor.fetchone()
            bkt_before = float(mastery['bkt_score']) if mastery else 0.5
            
            # Simple BKT update for diagnostic (just track correct/incorrect)
            if is_correct:
                bkt_after = min(0.95, bkt_before + 0.1)  # Increase knowledge
            else:
                bkt_after = max(0.05, bkt_before - 0.05)  # Slight decrease
            
            # Record the response with all required fields
            response_id = f"RESP_{session_id}_{question_id}_{int(datetime.now().timestamp())}"
            
            cursor.execute("""
                INSERT INTO question_responses 
                (response_id, assessment_id, user_id, question_id, user_answer, 
                 is_correct, response_time, max_allowed_time, bkt_before, bkt_after, answered_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW())
            """, (response_id, session_id, session['user_id'], question_id, 
                  user_answer, is_correct, response_time, max_allowed_time, bkt_before, bkt_after))
            
            conn.commit()
            
            return {'success': True, 'recorded': True}
            
        except mysql.connector.Error as e:
            conn.rollback()
            self.log_error(f"Failed to record diagnostic answer: {e}")
            return {'success': False, 'message': f'Failed to record answer: {str(e)}'}
        finally:
            conn.close()

    def complete_diagnostic_phase(self, session_id):
        """
        Complete current diagnostic phase and move to next or finish
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get session and calculate phase score
            cursor.execute("""
                SELECT ds.*, 
                       COUNT(qr.response_id) as total_answered,
                       SUM(qr.is_correct) as correct_answers
                FROM diagnostic_sessions ds
                LEFT JOIN question_responses qr ON ds.session_id = qr.assessment_id
                WHERE ds.session_id = %s
                GROUP BY ds.session_id
            """, (session_id,))
            
            session = cursor.fetchone()
            if not session:
                return {'success': False, 'message': 'Session not found'}
            
            # Calculate phase mastery score
            phase_score = (session['correct_answers'] / session['total_answered']) if session['total_answered'] > 0 else 0
            current_phase = session['current_phase']
            
            # Update phase score
            phase_column = f"phase_{current_phase}_score"
            cursor.execute(f"""
                UPDATE diagnostic_sessions 
                SET {phase_column} = %s 
                WHERE session_id = %s
            """, (phase_score, session_id))
            
            # Check if we need to continue to next phase
            if current_phase < 3:
                # Move to next phase
                next_phase = current_phase + 1
                phase_names = {2: 'intermediate', 3: 'advanced'}
                
                cursor.execute("""
                    UPDATE diagnostic_sessions 
                    SET current_phase = %s 
                    WHERE session_id = %s
                """, (next_phase, session_id))
                
                # Get next phase questions
                questions = self.get_diagnostic_questions(
                    session['competency'], 
                    phase_names[next_phase], 
                    5 if next_phase < 3 else 5
                )
                
                conn.commit()
                
                return {
                    'success': True,
                    'phase_complete': True,
                    'next_phase': next_phase,
                    'phase_name': phase_names[next_phase],
                    'questions': questions,
                    'diagnostic_complete': False
                }
            else:
                # Complete diagnostic
                return self.complete_diagnostic(session_id)
                
        except mysql.connector.Error as e:
            conn.rollback()
            self.log_error(f"Failed to complete diagnostic phase: {e}")
            return {'success': False, 'message': 'Failed to complete phase'}
        finally:
            conn.close()

    def complete_diagnostic(self, session_id):
        """
        Complete entire diagnostic and calculate final classification
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Set connection timeout to prevent long waits
            cursor.execute("SET SESSION innodb_lock_wait_timeout = 5")
            
            # Get session data
            cursor.execute("""
                SELECT user_id, competency, status,
                       phase_1_score, phase_1_questions,
                       phase_2_score, phase_2_questions,
                       phase_3_score, phase_3_questions
                FROM diagnostic_sessions 
                WHERE session_id = %s
            """, (session_id,))
            
            session = cursor.fetchone()
            if not session:
                return {'success': False, 'message': 'Session not found'}
            
            # Check if already completed
            if session['status'] == 'completed':
                return {'success': True, 'message': 'Diagnostic already completed'}
            
            # Calculate final master score
            ms1 = float(session['phase_1_score']) if session['phase_1_score'] else 0
            ms2 = float(session['phase_2_score']) if session['phase_2_score'] else 0
            ms3 = float(session['phase_3_score']) if session['phase_3_score'] else 0
            q1 = session['phase_1_questions'] or 5
            q2 = session['phase_2_questions'] or 5
            q3 = session['phase_3_questions'] or 5
            
            total_questions = q1 + q2 + q3
            if total_questions > 0:
                final_master_score = ((ms1 * q1) + (ms2 * q2) + (ms3 * q3)) / total_questions * 100
            else:
                final_master_score = 0
            
            # Determine recommended difficulty
            recommended_difficulty = self.determine_difficulty_level(final_master_score)
            
            # Step 1: Check if student_mastery record exists
            cursor.execute("""
                SELECT mastery_id FROM student_mastery 
                WHERE user_id = %s AND competency = %s
            """, (session['user_id'], session['competency']))
            
            mastery_exists = cursor.fetchone()
            
            # Step 2: Handle student_mastery record (most critical update)
            # Use separate connection for this critical update
            mastery_conn = self.connect_db()
            mastery_cursor = mastery_conn.cursor(dictionary=True)
            mastery_conn.autocommit = True  # Immediate commit
            
            if mastery_exists:
                # Update existing record
                self.log_error(f"Updating existing mastery record for user {session['user_id']}, competency '{session['competency']}'")
                
                # First, let's see what we're trying to update
                mastery_cursor.execute("""
                    SELECT mastery_id, has_taken_diagnostic FROM student_mastery 
                    WHERE user_id = %s AND competency = %s
                """, (session['user_id'], session['competency']))
                
                before_update = mastery_cursor.fetchone()
                self.log_error(f"Before update - Mastery ID: {before_update['mastery_id']}, has_taken_diagnostic: {before_update['has_taken_diagnostic']}")
                
                mastery_cursor.execute("""
                    UPDATE student_mastery 
                    SET current_difficulty = %s,
                        has_taken_diagnostic = 1,
                        diagnostic_completed_at = NOW(),
                        final_mastery_score = %s,
                        updated_at = NOW()
                    WHERE user_id = %s AND competency = %s
                """, (recommended_difficulty, final_master_score, 
                      session['user_id'], session['competency']))
                
                affected_rows = mastery_cursor.rowcount
                self.log_error(f"UPDATE affected {affected_rows} rows")
                
                # Check after update
                mastery_cursor.execute("""
                    SELECT has_taken_diagnostic FROM student_mastery 
                    WHERE user_id = %s AND competency = %s
                """, (session['user_id'], session['competency']))
                
                after_update = mastery_cursor.fetchone()
                self.log_error(f"After update - has_taken_diagnostic: {after_update['has_taken_diagnostic']}")
                
            else:
                # Create new record with explicit mastery_id
                mastery_id = f"MAST_{session['user_id']}_{session['competency']}_{int(datetime.now().timestamp())}"
                self.log_error(f"Creating new mastery record with ID: {mastery_id}")
                mastery_cursor.execute("""
                    INSERT INTO student_mastery 
                    (mastery_id, user_id, competency, current_difficulty, has_taken_diagnostic, 
                     diagnostic_completed_at, final_mastery_score, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, 1, NOW(), %s, NOW(), NOW())
                """, (mastery_id, session['user_id'], session['competency'], 
                      recommended_difficulty, final_master_score))
            
            # Close the mastery connection immediately
            mastery_conn.close()
            self.log_error("Critical student_mastery update committed independently")
            
            # Step 3: Update diagnostic_sessions (with timeout handling)
            try:
                cursor.execute("""
                    UPDATE diagnostic_sessions 
                    SET final_master_score = %s, 
                        recommended_difficulty = %s,
                        status = 'completed',
                        completed_at = NOW()
                    WHERE session_id = %s AND status = 'in_progress'
                """, (final_master_score, recommended_difficulty, session_id))
                
                session_updated = cursor.rowcount > 0
                self.log_error(f"Diagnostic session update - rows affected: {cursor.rowcount}")
                
            except mysql.connector.Error as e:
                if e.errno == 1205:  # Lock timeout on diagnostic_sessions
                    self.log_error(f"Lock timeout on diagnostic_sessions, but student_mastery already updated")
                    # Don't fail the entire process since student_mastery (most important) succeeded
                    session_updated = False
                else:
                    raise e
            
            # Step 4: Update assessments if it exists (optional, with timeout handling)
            try:
                cursor.execute("""
                    UPDATE assessments 
                    SET status = 'completed',
                        completed_at = NOW(),
                        final_mastery_score = %s,
                        difficulty_level = %s
                    WHERE assessment_id = %s
                """, (final_master_score, recommended_difficulty, session_id))
                
                self.log_error(f"Assessments update - rows affected: {cursor.rowcount}")
                
            except mysql.connector.Error as e:
                if e.errno == 1205:  # Lock timeout on assessments
                    self.log_error(f"Lock timeout on assessments table - non-critical")
                    # Don't fail since this is less critical
                else:
                    self.log_error(f"Warning: Could not update assessments table: {e}")
                    # Don't fail the entire process for this
            
            # Step 5: Verify the critical update succeeded (use fresh connection to avoid isolation issues)
            verify_conn = self.connect_db()
            verify_cursor = verify_conn.cursor(dictionary=True)
            
            verify_cursor.execute("""
                SELECT has_taken_diagnostic FROM student_mastery 
                WHERE user_id = %s AND competency = %s
            """, (session['user_id'], session['competency']))
            
            mastery_check = verify_cursor.fetchone()
            verify_conn.close()
            
            if not mastery_check or not mastery_check['has_taken_diagnostic']:
                self.log_error(f"Critical: Verification failed - has_taken_diagnostic still not set for user {session['user_id']}")
                return {'success': False, 'message': 'Failed to update diagnostic status'}
            
            self.log_error(f"Verification successful - has_taken_diagnostic is now: {mastery_check['has_taken_diagnostic']}")
            
            # Return success even if some secondary updates failed (as long as student_mastery updated)
            return {
                'success': True,
                'diagnostic_complete': True,
                'final_master_score': final_master_score,
                'recommended_difficulty': recommended_difficulty,
                'phase_scores': {
                    'beginner': ms1,
                    'intermediate': ms2,
                    'advanced': ms3
                },
                'warnings': [] if session_updated else ['Some secondary updates may have timed out']
            }
            
        except mysql.connector.Error as e:
            error_code = e.errno if hasattr(e, 'errno') else 0
            if error_code == 1205:  # Lock wait timeout
                self.log_error(f"Lock timeout in complete_diagnostic: {e}")
                return {'success': False, 'message': 'System busy, please try again'}
            else:
                self.log_error(f"Database error in complete_diagnostic: {e}")
                return {'success': False, 'message': f'Database error: {str(e)}'}
        except Exception as e:
            self.log_error(f"Unexpected error in complete_diagnostic: {e}")
            return {'success': False, 'message': 'Unexpected error occurred'}
        finally:
            if conn and conn.is_connected():
                conn.close()

def main():
    """Main function for command line interface"""
    if len(sys.argv) < 4:
        print(json.dumps({
            'success': False, 
            'message': 'Usage: python bkt_algorithm.py <action> <user_id> <competency> [additional_params]'
        }))
        return
    
    action = sys.argv[1]
    user_id = int(sys.argv[2])
    competency = sys.argv[3]
    
    bkt = BKTAlgorithm()
    
    try:
        if action == 'start_diagnostic':
            result = bkt.start_diagnostic(user_id, competency)
            
        elif action == 'record_answer':
            if len(sys.argv) < 7:
                result = {'success': False, 'message': 'Missing parameters for record_answer'}
            else:
                session_id = sys.argv[4]
                question_id = sys.argv[5]
                user_answer = sys.argv[6]
                response_time = float(sys.argv[7])
                is_correct = sys.argv[8].lower() == 'true'
                
                result = bkt.record_diagnostic_answer(session_id, question_id, user_answer, response_time, is_correct)
                
        elif action == 'complete_phase':
            session_id = sys.argv[4]
            result = bkt.complete_diagnostic_phase(session_id)
            
        elif action == 'complete_diagnostic':
            session_id = sys.argv[4]
            result = bkt.complete_diagnostic(session_id)
            
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