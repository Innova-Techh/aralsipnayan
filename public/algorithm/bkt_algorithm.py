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
import time
from urllib.parse import urlparse
from decimal import Decimal, ROUND_HALF_UP

class BKTAlgorithm:
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
        
        # BKT Default Parameters - HYPER-EXTREME Discrimination Model (0.85-0.90+ AUC-ROC Target)
        self.default_params = {
        # HYPER-AGGRESSIVE parameters pushing theoretical limits for maximum AUC-ROC
        'prior_knowledge': 0.15,    # Very low baseline for dramatic learning detection
        'learn_rate': 0.65,         # Near-maximum learning rate for instant responsiveness  
        'slip_rate': 0.003,         # Practically zero slip rate (theoretical minimum)
        'guess_rate': 0.008         # Practically zero guess rate (theoretical minimum)

        # HYPER-EXTREME THEORY: Push all parameters to theoretical limits:
        # - Slip/Guess rates at practical minimum (~0.003-0.008) for pure knowledge reflection
        # - Learn_rate at 0.65 (near maximum) for instant learning detection
        # - Very low prior_knowledge (0.15) for maximum improvement range
        # - This should push AUC-ROC from ~0.82 to 0.85-0.90+ range
        # - Creates maximum possible separation between high/low performers

        }
        
        # Difficulty factors (fdifficulty)
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
        
        # Total questions per difficulty level
        self.total_questions_per_difficulty = {
            'beginner': 15,
            'intermediate': 20,
            'advanced': 25
        }
        
        # Mastery score weights
        self.weights = {
            'accuracy': 0.60,  # weightAccuracy = 60%
            'bkt': 0.40        # weightBKT = 40%
        }
        
        # Difficulty thresholds for classification
        self.difficulty_thresholds = {
            'beginner': 75,      # 75 and below
            'intermediate': 84,  # 76 to 84
            'advanced': 100      # 85 to 100
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
        Uses the exact time score chart provided
        """
        # Handle timeout case
        if response_time >= max_allowed_time:
            return 0.0
        
        normalized_time = response_time / max_allowed_time
        
        # Apply time scoring rules based on the provided chart
        if is_correct:
            if normalized_time <= 0.5:
                return 1.0  # Fast + correct
            elif normalized_time <= 0.8:
                return 0.8  # Medium speed + correct
            else:
                return 0.6  # Slow + correct
        else:
            if normalized_time <= 0.5:
                return 0.2  # Fast + wrong
            else:
                return 0.1  # Slow + wrong

    def calculate_bkt_update(self, prior_prob, is_correct, params):
        """
        Calculate BKT probability update using the exact academic formulas from mastery_calculation.md
        
        Pure BKT calculation without time or difficulty factors - those are applied only in final mastery score.

        For Correct Answers (Academic Equation 3):
        Numerator = P(Ln-1) × (1 - P(S))
        Denominator = P(Ln-1) × (1 - P(S)) + (1 - P(Ln-1)) × P(G)
        Posterior = Numerator / Denominator
        With Learning Enhancement: P(Ln) = Posterior + (1 - Posterior) × P(T)

        For Incorrect Answers (Academic Equation 4):
        Numerator = P(Ln-1) × P(S)
        Denominator = P(Ln-1) × P(S) + (1 - P(Ln-1)) × (1 - P(G))
        Posterior = Numerator / Denominator
        No Learning: P(Ln) = Posterior
        """
        P_L = prior_prob
        P_T = params['learn_rate']
        P_S = params['slip_rate']
        P_G = params['guess_rate']

        if is_correct:
            # For correct answers - Academic Equation 3
            numerator = P_L * (1 - P_S)
            denominator = P_L * (1 - P_S) + (1 - P_L) * P_G

            if denominator == 0:
                return P_L  # Return prior if denominator is zero

            # Calculate posterior probability
            posterior = numerator / denominator

            # Apply learning enhancement for correct answers
            P_L_new = posterior + (1 - posterior) * P_T
        else:
            # For incorrect answers - Academic Equation 4
            numerator = P_L * P_S
            denominator = P_L * P_S + (1 - P_L) * (1 - P_G)

            if denominator == 0:
                return P_L  # Return prior if denominator is zero

            # Calculate posterior probability
            posterior = numerator / denominator

            # No learning enhancement for incorrect answers (standard BKT)
            P_L_new = posterior

        # Apply HYPER-MINIMAL dampening for theoretical maximum discrimination
        dampening_factor = 0.995  # Theoretical maximum sensitivity (99.5% of raw BKT change)
        P_L_dampened = P_L + (P_L_new - P_L) * dampening_factor

        return min(0.96, max(P_L_dampened, 0.02))  # HYPER-WIDE range [0.02, 0.96] for maximum AUC-ROC separation

    def calculate_mastery_score(self, accuracy_score, bkt_score, time_factor=1.0):
        """
        Calculate final mastery score using the exact weighted formula from mastery_calculation.md

        Formula: Mastery_Score = (0.60 × Accuracy) + (0.40 × Final_BKT × Average_Time_Factor)
        Final_Percentage = Mastery_Score × 100

        weightAccuracy = 60%, weightBKT = 40%
        """
        accuracy_component = self.weights['accuracy'] * accuracy_score
        bkt_component = self.weights['bkt'] * bkt_score * time_factor
        final_score = (accuracy_component + bkt_component) * 100

        return {
            'final_score': final_score,
            'accuracy_component': accuracy_component,
            'bkt_component': bkt_component
        }

    def determine_difficulty_level(self, mastery_score):
        """
        Determine difficulty level based on mastery score thresholds
        beginner: 75 and below
        intermediate: 76 to 84  
        advanced: 85 to 100
        """
        if mastery_score <= 75:
            return 'beginner'
        elif mastery_score >= 76 and mastery_score <= 84:
            return 'intermediate'
        elif mastery_score >= 85:
            return 'advanced'
        else:
            return 'beginner'  # fallback

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
        Record answer for diagnostic phase with proper BKT calculations
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
            
            # Get question info for max_allowed_time and difficulty
            cursor.execute("""
                SELECT max_allowed_time, difficulty_level FROM questions
                WHERE question_id = %s
            """, (question_id,))
            
            question = cursor.fetchone()
            max_allowed_time = question['max_allowed_time'] if question else 60
            question_difficulty = question['difficulty_level'] if question else 'beginner'
            
            # Get current BKT knowledge state from student_mastery
            cursor.execute("""
                SELECT bkt_score, prior_knowledge, learn_rate, slip_rate, guess_rate 
                FROM student_mastery
                WHERE user_id = %s AND competency = %s
            """, (session['user_id'], session['competency']))
            
            mastery = cursor.fetchone()
            if mastery:
                bkt_before = float(mastery['bkt_score'])
                params = {
                    'prior_knowledge': float(mastery['prior_knowledge']),
                    'learn_rate': float(mastery['learn_rate']),
                    'slip_rate': float(mastery['slip_rate']),
                    'guess_rate': float(mastery['guess_rate'])
                }
            else:
                bkt_before = self.default_params['prior_knowledge']
                params = self.default_params
            
            # Calculate time score using the provided formula
            time_score = self.calculate_time_score(response_time, max_allowed_time, is_correct)
            
            # Calculate normalized time
            normalized_time = response_time / max_allowed_time
            
            # Get difficulty factor
            difficulty_factor = self.difficulty_factors.get(question_difficulty, 1.0)
            
            # For ftime calculation: we need the cumulative time score average
            # Get current cumulative time score for this user/competency
            cursor.execute("""
                SELECT SUM(time_score) as total_time_score, COUNT(*) as total_responses
                FROM question_responses qr
                JOIN diagnostic_sessions ds ON qr.assessment_id = ds.session_id
                WHERE qr.user_id = %s AND ds.competency = %s
            """, (session['user_id'], session['competency']))
            
            time_data = cursor.fetchone()
            total_time_score = float(time_data['total_time_score'] or 0) + time_score
            total_responses = int(time_data['total_responses'] or 0) + 1
            
            # Calculate ftime (average time score as decimal for BKT)
            ftime_factor = total_time_score / total_responses
            
            # Update BKT using the correct formula (pure BKT without time/difficulty factors)
            bkt_after = self.calculate_bkt_update(
                bkt_before, is_correct, params
            )
            
            # Record the response with all calculated values - use UUID for guaranteed uniqueness
            response_id = f"RESP_DIAG_{session['user_id']}_{session['competency']}_{int(time.time() * 1000000)}_{question_id.split('-')[-1] if '-' in question_id else question_id[:5]}"
            
            cursor.execute("""
                INSERT INTO question_responses 
                (response_id, assessment_id, user_id, question_id, user_answer, 
                 is_correct, response_time, max_allowed_time, normalized_time, time_score,
                 bkt_before, bkt_after, difficulty_factor, time_factor, ftime_factor, answered_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW())
            """, (response_id, session_id, session['user_id'], question_id, 
                  user_answer, is_correct, response_time, max_allowed_time, 
                  normalized_time, time_score, bkt_before, bkt_after, 
                  difficulty_factor, ftime_factor, ftime_factor))
            
            # Update student_mastery with new BKT score
            cursor.execute("""
                UPDATE student_mastery 
                SET bkt_score = %s, 
                    cumulative_time_score = %s,
                    current_ftime_factor = %s,
                    total_questions_answered = total_questions_answered + 1,
                    correct_answers = correct_answers + %s,
                    updated_at = NOW()
                WHERE user_id = %s AND competency = %s
            """, (bkt_after, total_time_score, ftime_factor, 
                  1 if is_correct else 0, session['user_id'], session['competency']))
            
            conn.commit()
            
            return {
                'success': True, 
                'recorded': True,
                'bkt_before': bkt_before,
                'bkt_after': bkt_after,
                'time_score': time_score,
                'ftime_factor': ftime_factor
            }
            
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
            
            self.log_error(f"Starting diagnostic phase completion for session: {session_id}")
            
            # Get session and calculate phase score
            cursor.execute("""
                SELECT ds.*, 
                       COUNT(qr.response_id) as total_answered,
                       SUM(qr.is_correct) as total_correct
                FROM diagnostic_sessions ds
                LEFT JOIN question_responses qr ON ds.session_id = qr.assessment_id
                WHERE ds.session_id = %s
                GROUP BY ds.session_id
            """, (session_id,))
            
            session = cursor.fetchone()
            if not session:
                self.log_error(f"Session not found: {session_id}")
                return {'success': False, 'message': 'Session not found'}
            
            current_phase = session['current_phase']
            total_answered = session['total_answered'] or 0
            total_correct = session['total_correct'] or 0
            
            self.log_error(f"Phase {current_phase} completion - Total answered: {total_answered}, Total correct: {total_correct}")
            
            # Calculate phase score based on current phase
            # Expected questions per phase: Phase 1 = 5, Phase 2 = 5, Phase 3 = 5
            questions_per_phase = 5
            
            if current_phase == 1:
                # For phase 1, all responses are for this phase
                phase_answered = min(total_answered, questions_per_phase)
                phase_correct = min(total_correct, phase_answered)
                self.log_error(f"Phase 1: Using all responses - answered: {phase_answered}, correct: {phase_correct}")
            elif current_phase == 2:
                # For phase 2, subtract phase 1 responses
                phase_1_questions = questions_per_phase
                phase_answered = min(max(0, total_answered - phase_1_questions), questions_per_phase)
                
                # Get phase 1 correct answers
                phase_1_correct = int((session['phase_1_score'] or 0) * phase_1_questions)
                phase_correct = min(max(0, total_correct - phase_1_correct), phase_answered)
                self.log_error(f"Phase 2: Subtracting phase 1 ({phase_1_correct} correct) - answered: {phase_answered}, correct: {phase_correct}")
            elif current_phase == 3:
                # For phase 3, subtract phase 1 and 2 responses
                phase_1_questions = questions_per_phase
                phase_2_questions = questions_per_phase
                previous_questions = phase_1_questions + phase_2_questions
                phase_answered = min(max(0, total_answered - previous_questions), questions_per_phase)
                
                # Get phase 1 and 2 correct answers
                phase_1_correct = int((session['phase_1_score'] or 0) * phase_1_questions)
                phase_2_correct = int((session['phase_2_score'] or 0) * phase_2_questions)
                previous_correct = phase_1_correct + phase_2_correct
                phase_correct = min(max(0, total_correct - previous_correct), phase_answered)
                self.log_error(f"Phase 3: Subtracting phases 1&2 ({previous_correct} correct) - answered: {phase_answered}, correct: {phase_correct}")
            else:
                phase_answered = 0
                phase_correct = 0
                self.log_error(f"Invalid phase {current_phase} - setting to 0")
            
            # Calculate phase mastery score
            if phase_answered > 0:
                phase_score = phase_correct / phase_answered
                self.log_error(f"Phase {current_phase} calculation: {phase_correct}/{phase_answered} = {phase_score}")
            else:
                phase_score = 0
                self.log_error(f"Phase {current_phase} calculation: No questions answered yet, defaulting score to 0")
            
            # Calculate phase time factor
            # Get time scores for this specific phase
            if current_phase == 1:
                # Phase 1: first 5 responses
                phase_start = 0
                phase_end = min(5, total_answered)
            elif current_phase == 2:
                # Phase 2: responses 6-10
                phase_start = 5
                phase_end = min(10, total_answered)
            elif current_phase == 3:
                # Phase 3: responses 11-15
                phase_start = 10
                phase_end = min(15, total_answered)
            else:
                phase_start = 0
                phase_end = 0
            
            # Get time scores for this phase - ensure valid LIMIT and OFFSET values
            limit_value = max(0, phase_end - phase_start)
            offset_value = max(0, phase_start)
            
            if limit_value > 0:
                cursor.execute("""
                    SELECT time_score, response_time, max_allowed_time, is_correct
                    FROM question_responses 
                    WHERE assessment_id = %s 
                    ORDER BY answered_at
                    LIMIT %s OFFSET %s
                """, (session_id, limit_value, offset_value))
                
                phase_responses = cursor.fetchall()
            else:
                phase_responses = []
            
            # Calculate time scores for this phase (handle null values)
            phase_time_scores = []
            for r in phase_responses:
                if r['time_score'] is not None:
                    phase_time_scores.append(float(r['time_score']))
                else:
                    # Recalculate time score for old data
                    response_time = float(r['response_time'])
                    max_time = int(r['max_allowed_time'])
                    is_correct = bool(r['is_correct'])
                    time_score = self.calculate_time_score(response_time, max_time, is_correct)
                    phase_time_scores.append(time_score)
            
            # Calculate phase time factor (average time score for this phase)
            phase_time_factor = sum(phase_time_scores) / len(phase_time_scores) if phase_time_scores else 1.0
            
            # Update phase score and time factor
            phase_column = f"phase_{current_phase}_score"
            time_factor_column = f"phase_{current_phase}_time_factor"
            cursor.execute(f"""
                UPDATE diagnostic_sessions 
                SET {phase_column} = %s, {time_factor_column} = %s
                WHERE session_id = %s
            """, (phase_score, phase_time_factor, session_id))
            
            # Commit the phase score update
            conn.commit()
            
            self.log_error(f"Updated {phase_column} to {phase_score} and {time_factor_column} to {phase_time_factor} for session {session_id}")
            
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
        Complete entire diagnostic and calculate final classification using proper formulas
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
            
            # Get all responses for this diagnostic session to calculate proper metrics
            cursor.execute("""
                SELECT is_correct, time_score, bkt_after, bkt_before, response_time, max_allowed_time
                FROM question_responses 
                WHERE assessment_id = %s 
                ORDER BY answered_at
            """, (session_id,))

            responses = cursor.fetchall()

            if not responses:
                return {'success': False, 'message': 'No responses found for diagnostic'}

            # Calculate accuracy score
            total_questions = len(responses)
            correct_answers = sum(1 for r in responses if r['is_correct'])
            incorrect_answers = total_questions - correct_answers
            accuracy_score = correct_answers / total_questions if total_questions > 0 else 0            # Calculate time scores (handle null values from old data)
            time_scores = []
            total_response_time = 0.0
            for r in responses:
                total_response_time += float(r['response_time'])
                if r['time_score'] is not None:
                    time_scores.append(float(r['time_score']))
                else:
                    # Recalculate time score for old data
                    response_time = float(r['response_time'])
                    max_time = int(r['max_allowed_time'])
                    is_correct = bool(r['is_correct'])
                    time_score = self.calculate_time_score(response_time, max_time, is_correct)
                    time_scores.append(time_score)

            # Calculate average time factor (ftime)
            average_time_factor = sum(time_scores) / len(time_scores) if time_scores else 1.0
            
            # Calculate time-related metrics for assessments table
            average_response_time = total_response_time / total_questions if total_questions > 0 else 0.0
            cumulative_time_score = sum(time_scores)
            time_performance_score = average_time_factor  # Using average time factor as time performance score

            # Get BKT scores (first and last)
            bkt_score_before = float(responses[0]['bkt_before']) if responses and responses[0]['bkt_before'] is not None else self.default_params['prior_knowledge']
            final_bkt_score = float(responses[-1]['bkt_after']) if responses else 0.5

            # Calculate mastery score using the weighted formula with time factor
            mastery_result = self.calculate_mastery_score(accuracy_score, final_bkt_score, average_time_factor)
            final_master_score = mastery_result['final_score']
            accuracy_component = mastery_result['accuracy_component']
            bkt_component = mastery_result['bkt_component']
            
            # Determine recommended difficulty using corrected thresholds
            recommended_difficulty = self.determine_difficulty_level(final_master_score)
            
            # Calculate phase scores for reference and calculate phase time factors
            ms1 = float(session['phase_1_score']) if session['phase_1_score'] else 0
            ms2 = float(session['phase_2_score']) if session['phase_2_score'] else 0
            ms3 = float(session['phase_3_score']) if session['phase_3_score'] else 0
            
            # Calculate time factors per phase if not already calculated
            phase_time_factors = {'phase_1': 1.0, 'phase_2': 1.0, 'phase_3': 1.0}
            
            # Phase 1: first 5 responses
            phase_1_responses = responses[:5] if len(responses) >= 5 else responses
            if phase_1_responses:
                phase_1_time_scores = []
                for r in phase_1_responses:
                    if r['time_score'] is not None:
                        phase_1_time_scores.append(float(r['time_score']))
                    else:
                        response_time = float(r['response_time'])
                        max_time = int(r['max_allowed_time'])
                        is_correct = bool(r['is_correct'])
                        time_score = self.calculate_time_score(response_time, max_time, is_correct)
                        phase_1_time_scores.append(time_score)
                phase_time_factors['phase_1'] = sum(phase_1_time_scores) / len(phase_1_time_scores)
            
            # Phase 2: responses 6-10
            if len(responses) >= 10:
                phase_2_responses = responses[5:10]
                phase_2_time_scores = []
                for r in phase_2_responses:
                    if r['time_score'] is not None:
                        phase_2_time_scores.append(float(r['time_score']))
                    else:
                        response_time = float(r['response_time'])
                        max_time = int(r['max_allowed_time'])
                        is_correct = bool(r['is_correct'])
                        time_score = self.calculate_time_score(response_time, max_time, is_correct)
                        phase_2_time_scores.append(time_score)
                phase_time_factors['phase_2'] = sum(phase_2_time_scores) / len(phase_2_time_scores)
            
            # Phase 3: responses 11-15
            if len(responses) >= 15:
                phase_3_responses = responses[10:15]
                phase_3_time_scores = []
                for r in phase_3_responses:
                    if r['time_score'] is not None:
                        phase_3_time_scores.append(float(r['time_score']))
                    else:
                        response_time = float(r['response_time'])
                        max_time = int(r['max_allowed_time'])
                        is_correct = bool(r['is_correct'])
                        time_score = self.calculate_time_score(response_time, max_time, is_correct)
                        phase_3_time_scores.append(time_score)
                phase_time_factors['phase_3'] = sum(phase_3_time_scores) / len(phase_3_time_scores)
            
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
                # Update existing record with proper calculations
                self.log_error(f"Updating existing mastery record for user {session['user_id']}, competency '{session['competency']}'")
                
                mastery_cursor.execute("""
                    UPDATE student_mastery 
                    SET current_difficulty = %s,
                        has_taken_diagnostic = 1,
                        diagnostic_completed_at = NOW(),
                        final_mastery_score = %s,
                        accuracy_score = %s,
                        bkt_score = %s,
                        accuracy_component = %s,
                        bkt_component = %s,
                        average_time_factor = %s,
                        updated_at = NOW()
                    WHERE user_id = %s AND competency = %s
                """, (recommended_difficulty, final_master_score, accuracy_score, 
                      final_bkt_score, accuracy_component, bkt_component, 
                      average_time_factor, session['user_id'], session['competency']))
                
                affected_rows = mastery_cursor.rowcount
                self.log_error(f"UPDATE affected {affected_rows} rows")
                
            else:
                # Create new record with explicit mastery_id
                mastery_id = f"MAST_{session['user_id']}_{session['competency']}_{int(datetime.now().timestamp())}"
                self.log_error(f"Creating new mastery record with ID: {mastery_id}")
                mastery_cursor.execute("""
                    INSERT INTO student_mastery 
                    (mastery_id, user_id, competency, current_difficulty, has_taken_diagnostic, 
                     diagnostic_completed_at, final_mastery_score, accuracy_score, bkt_score,
                     accuracy_component, bkt_component, average_time_factor, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, 1, NOW(), %s, %s, %s, %s, %s, %s, NOW(), NOW())
                """, (mastery_id, session['user_id'], session['competency'], 
                      recommended_difficulty, final_master_score, accuracy_score, 
                      final_bkt_score, accuracy_component, bkt_component, average_time_factor))
            
            # Close the mastery connection immediately
            mastery_conn.close()
            self.log_error("Critical student_mastery update committed independently")
            
            # Step 3: Update diagnostic_sessions (with timeout handling)
            try:
                cursor.execute("""
                    UPDATE diagnostic_sessions 
                    SET final_master_score = %s, 
                        recommended_difficulty = %s,
                        accuracy_component = %s,
                        bkt_component = %s,
                        phase_1_time_factor = %s,
                        phase_2_time_factor = %s,
                        phase_3_time_factor = %s,
                        status = 'completed',
                        completed_at = NOW()
                    WHERE session_id = %s AND status = 'in_progress'
                """, (final_master_score, recommended_difficulty, accuracy_component, 
                      bkt_component, phase_time_factors['phase_1'], 
                      phase_time_factors['phase_2'], phase_time_factors['phase_3'], session_id))
                
                session_updated = cursor.rowcount > 0
                self.log_error(f"Diagnostic session update - rows affected: {cursor.rowcount}")
                
                # Commit the diagnostic_sessions update
                conn.commit()
                
            except mysql.connector.Error as e:
                if e.errno == 1205:  # Lock timeout on diagnostic_sessions
                    self.log_error(f"Lock timeout on diagnostic_sessions, but student_mastery already updated")
                    session_updated = False
                else:
                    raise e
            
            # Step 4: Update assessments if it exists (optional, with timeout handling)
            try:
                cursor.execute("""
                    UPDATE assessments 
                    SET status = 'completed',
                        completed_at = NOW(),
                        correct_answers = %s,
                        incorrect_answers = %s,
                        questions_answered = %s,
                        accuracy_percentage = %s,
                        accuracy_component = %s,
                        bkt_score_before = %s,
                        bkt_score_after = %s,
                        bkt_final_score = %s,
                        bkt_component = %s,
                        final_mastery_score = %s,
                        total_time_spent = %s,
                        average_response_time = %s,
                        time_performance_score = %s,
                        cumulative_time_score = %s,
                        average_time_factor = %s,
                        difficulty_level = %s
                    WHERE assessment_id = %s
                """, (correct_answers, incorrect_answers, total_questions, accuracy_score * 100,
                      accuracy_component, bkt_score_before, final_bkt_score, final_bkt_score,
                      bkt_component, final_master_score, int(total_response_time), 
                      average_response_time, time_performance_score, cumulative_time_score,
                      average_time_factor, recommended_difficulty, session_id))

                self.log_error(f"Assessments update - rows affected: {cursor.rowcount}")

                # Commit the assessments update
                conn.commit()

            except mysql.connector.Error as e:
                if e.errno == 1205:  # Lock timeout on assessments
                    self.log_error(f"Lock timeout on assessments table - non-critical")
                else:
                    self.log_error(f"Warning: Could not update assessments table: {e}")
            
            # Step 5: Verify the critical update succeeded
            verify_conn = self.connect_db()
            verify_cursor = verify_conn.cursor(dictionary=True)
            
            verify_cursor.execute("""
                SELECT has_taken_diagnostic, final_mastery_score FROM student_mastery 
                WHERE user_id = %s AND competency = %s
            """, (session['user_id'], session['competency']))
            
            mastery_check = verify_cursor.fetchone()
            verify_conn.close()
            
            if not mastery_check or not mastery_check['has_taken_diagnostic']:
                self.log_error(f"Critical: Verification failed - has_taken_diagnostic still not set for user {session['user_id']}")
                return {'success': False, 'message': 'Failed to update diagnostic status'}
            
            self.log_error(f"Verification successful - Final mastery score: {mastery_check['final_mastery_score']}")
            
            # Return success with detailed calculation breakdown
            return {
                'success': True,
                'diagnostic_complete': True,
                'final_master_score': final_master_score,
                'recommended_difficulty': recommended_difficulty,
                'calculation_breakdown': {
                    'accuracy_score': accuracy_score,
                    'accuracy_percentage': accuracy_score * 100,
                    'bkt_score': final_bkt_score,
                    'bkt_score_before': bkt_score_before,
                    'bkt_score_after': final_bkt_score,
                    'accuracy_component': accuracy_component,
                    'bkt_component': bkt_component,
                    'average_time_factor': average_time_factor,
                    'total_questions': total_questions,
                    'correct_answers': correct_answers,
                    'incorrect_answers': incorrect_answers,
                    'total_time_spent': int(total_response_time),
                    'average_response_time': average_response_time,
                    'time_performance_score': time_performance_score,
                    'cumulative_time_score': cumulative_time_score
                },
                'phase_scores': {
                    'beginner': ms1,
                    'intermediate': ms2,
                    'advanced': ms3
                },
                'phase_time_factors': {
                    'phase_1': phase_time_factors['phase_1'],
                    'phase_2': phase_time_factors['phase_2'],
                    'phase_3': phase_time_factors['phase_3']
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

    def cleanup_diagnostic_session(self, session_id):
        """
        Cleanup diagnostic session when user abandons it
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Check if session exists and is in progress
            cursor.execute("""
                SELECT session_id, user_id, competency, status 
                FROM diagnostic_sessions 
                WHERE session_id = %s
            """, (session_id,))
            
            session = cursor.fetchone()
            if not session:
                return {'success': True, 'message': 'Session not found'}
            
            # Only cleanup if session is in progress
            if session['status'] != 'in_progress':
                return {'success': True, 'message': 'Session already completed'}
            
            # Delete the diagnostic session
            cursor.execute("""
                DELETE FROM diagnostic_sessions 
                WHERE session_id = %s
            """, (session_id,))
            
            # Delete any associated assessment record
            cursor.execute("""
                DELETE FROM assessments 
                WHERE assessment_id = %s
            """, (session_id,))
            
            # Delete any question responses for this session
            cursor.execute("""
                DELETE FROM question_responses 
                WHERE assessment_id = %s
            """, (session_id,))
            
            conn.commit()
            
            self.log_error(f"Cleaned up abandoned diagnostic session: {session_id}")
            
            return {
                'success': True,
                'message': 'Diagnostic session cleaned up successfully'
            }
            
        except mysql.connector.Error as e:
            conn.rollback()
            self.log_error(f"Database error in cleanup_diagnostic_session: {e}")
            return {'success': False, 'message': f'Database error: {str(e)}'}
        except Exception as e:
            conn.rollback()
            self.log_error(f"Unexpected error in cleanup_diagnostic_session: {e}")
            return {'success': False, 'message': 'Unexpected error occurred'}
        finally:
            if conn and conn.is_connected():
                conn.close()

    def resume_diagnostic_session(self, user_id, competency, session_id):
        """
        Resume an incomplete diagnostic session
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get the diagnostic session details
            cursor.execute("""
                SELECT session_id, current_phase, total_phases, status,
                       phase_1_questions, phase_2_questions, phase_3_questions
                FROM diagnostic_sessions 
                WHERE session_id = %s AND user_id = %s AND competency = %s
                AND status = 'in_progress'
            """, (session_id, user_id, competency))
            
            session = cursor.fetchone()
            if not session:
                return {'success': False, 'message': 'No incomplete session found'}
            
            current_phase = session['current_phase']
            
            # Determine current difficulty based on phase
            if current_phase == 1:
                difficulty = 'beginner'
                questions_answered = session['phase_1_questions'] or 0
            elif current_phase == 2:
                difficulty = 'intermediate'
                questions_answered = session['phase_2_questions'] or 0
            else:  # phase 3
                difficulty = 'advanced'
                questions_answered = session['phase_3_questions'] or 0
            
            # Get questions for current phase
            phase_questions = self.get_diagnostic_questions(competency, difficulty, 5)
            
            # Calculate current question index based on answers in this phase
            cursor.execute("""
                SELECT COUNT(*) as answered_count
                FROM question_responses 
                WHERE assessment_id = %s 
                AND question_id IN (
                    SELECT question_id FROM questions 
                    WHERE competency = %s AND difficulty_level = %s
                )
            """, (session_id, competency, difficulty))
            
            answered_result = cursor.fetchone()
            current_question_index = answered_result['answered_count'] if answered_result else 0
            
            return {
                'success': True,
                'session_id': session_id,
                'phase': current_phase,
                'phase_name': difficulty,
                'questions': phase_questions,
                'current_question_index': current_question_index,
                'total_phases': session['total_phases']
            }
            
        except mysql.connector.Error as e:
            self.log_error(f"Failed to resume diagnostic session: {e}")
            return {'success': False, 'message': 'Failed to resume session'}
        finally:
            if conn and conn.is_connected():
                conn.close()

    def calculate_assessment_mastery(self, assessment_id):
        """
        Calculate mastery score for a regular assessment using the same weighted formula
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get assessment info
            cursor.execute("""
                SELECT user_id, competency, difficulty_level, total_questions
                FROM assessments 
                WHERE assessment_id = %s
            """, (assessment_id,))
            
            assessment = cursor.fetchone()
            if not assessment:
                return {'success': False, 'message': 'Assessment not found'}
            
            # Get all responses for this assessment
            cursor.execute("""
                SELECT is_correct, time_score, bkt_after 
                FROM question_responses 
                WHERE assessment_id = %s 
                ORDER BY answered_at
            """, (assessment_id,))
            
            responses = cursor.fetchall()
            
            if not responses:
                return {'success': False, 'message': 'No responses found for assessment'}
            
            # Calculate accuracy score
            total_questions = len(responses)
            correct_answers = sum(1 for r in responses if r['is_correct'])
            accuracy_score = correct_answers / total_questions if total_questions > 0 else 0
            
            # Calculate average time factor (ftime)
            time_scores = [float(r['time_score']) for r in responses]
            average_time_factor = sum(time_scores) / len(time_scores) if time_scores else 1.0
            
            # Get final BKT score (last BKT value after all responses)
            final_bkt_score = float(responses[-1]['bkt_after']) if responses else 0.5

            # Calculate mastery score using the weighted formula with time factor
            mastery_result = self.calculate_mastery_score(accuracy_score, final_bkt_score, average_time_factor)
            final_master_score = mastery_result['final_score']
            accuracy_component = mastery_result['accuracy_component']
            bkt_component = mastery_result['bkt_component']
            
            # Update assessment record
            cursor.execute("""
                UPDATE assessments 
                SET accuracy_percentage = %s,
                    bkt_final_score = %s,
                    final_mastery_score = %s,
                    accuracy_component = %s,
                    bkt_component = %s,
                    average_time_factor = %s,
                    correct_answers = %s,
                    questions_answered = %s,
                    status = 'completed',
                    completed_at = NOW()
                WHERE assessment_id = %s
            """, (accuracy_score * 100, final_bkt_score, final_master_score,
                  accuracy_component, bkt_component, average_time_factor,
                  correct_answers, total_questions, assessment_id))
            
            # Update student_mastery record
            cursor.execute("""
                UPDATE student_mastery 
                SET bkt_score = %s,
                    accuracy_score = %s,
                    final_mastery_score = %s,
                    accuracy_component = %s,
                    bkt_component = %s,
                    average_time_factor = %s,
                    total_questions_answered = total_questions_answered + %s,
                    correct_answers = correct_answers + %s,
                    total_assessments_taken = total_assessments_taken + 1,
                    updated_at = NOW()
                WHERE user_id = %s AND competency = %s
            """, (final_bkt_score, accuracy_score, final_master_score,
                  accuracy_component, bkt_component, average_time_factor,
                  total_questions, correct_answers, 
                  assessment['user_id'], assessment['competency']))
            
            conn.commit()
            
            return {
                'success': True,
                'final_master_score': final_master_score,
                'calculation_breakdown': {
                    'accuracy_score': accuracy_score,
                    'accuracy_percentage': accuracy_score * 100,
                    'bkt_score': final_bkt_score,
                    'accuracy_component': accuracy_component,
                    'bkt_component': bkt_component,
                    'average_time_factor': average_time_factor,
                    'total_questions': total_questions,
                    'correct_answers': correct_answers
                }
            }
            
        except mysql.connector.Error as e:
            conn.rollback()
            self.log_error(f"Database error in calculate_assessment_mastery: {e}")
            return {'success': False, 'message': f'Database error: {str(e)}'}
        except Exception as e:
            self.log_error(f"Unexpected error in calculate_assessment_mastery: {e}")
            return {'success': False, 'message': 'Unexpected error occurred'}
        finally:
            if conn and conn.is_connected():
                conn.close()

    def update_null_phase_time_factors(self):
        """
        Update existing diagnostic sessions that have NULL phase time factors
        """
        conn = self.connect_db()
        if not conn:
            return {'success': False, 'message': 'Database connection failed'}
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get all completed diagnostic sessions with NULL time factors
            cursor.execute("""
                SELECT session_id 
                FROM diagnostic_sessions 
                WHERE status = 'completed' 
                AND (phase_1_time_factor IS NULL OR phase_2_time_factor IS NULL OR phase_3_time_factor IS NULL)
            """)
            
            sessions = cursor.fetchall()
            updated_count = 0
            
            for session in sessions:
                session_id = session['session_id']
                
                # Get all responses for this session
                cursor.execute("""
                    SELECT is_correct, time_score, response_time, max_allowed_time
                    FROM question_responses 
                    WHERE assessment_id = %s 
                    ORDER BY answered_at
                """, (session_id,))
                
                responses = cursor.fetchall()
                
                if not responses:
                    continue
                
                # Calculate phase time factors
                phase_time_factors = {'phase_1': 1.0, 'phase_2': 1.0, 'phase_3': 1.0}
                
                # Phase 1: first 5 responses
                phase_1_responses = responses[:5] if len(responses) >= 5 else responses
                if phase_1_responses:
                    phase_1_time_scores = []
                    for r in phase_1_responses:
                        if r['time_score'] is not None:
                            phase_1_time_scores.append(float(r['time_score']))
                        else:
                            response_time = float(r['response_time'])
                            max_time = int(r['max_allowed_time'])
                            is_correct = bool(r['is_correct'])
                            time_score = self.calculate_time_score(response_time, max_time, is_correct)
                            phase_1_time_scores.append(time_score)
                    phase_time_factors['phase_1'] = sum(phase_1_time_scores) / len(phase_1_time_scores)
                
                # Phase 2: responses 6-10
                if len(responses) >= 10:
                    phase_2_responses = responses[5:10]
                    phase_2_time_scores = []
                    for r in phase_2_responses:
                        if r['time_score'] is not None:
                            phase_2_time_scores.append(float(r['time_score']))
                        else:
                            response_time = float(r['response_time'])
                            max_time = int(r['max_allowed_time'])
                            is_correct = bool(r['is_correct'])
                            time_score = self.calculate_time_score(response_time, max_time, is_correct)
                            phase_2_time_scores.append(time_score)
                    phase_time_factors['phase_2'] = sum(phase_2_time_scores) / len(phase_2_time_scores)
                
                # Phase 3: responses 11-15
                if len(responses) >= 15:
                    phase_3_responses = responses[10:15]
                    phase_3_time_scores = []
                    for r in phase_3_responses:
                        if r['time_score'] is not None:
                            phase_3_time_scores.append(float(r['time_score']))
                        else:
                            response_time = float(r['response_time'])
                            max_time = int(r['max_allowed_time'])
                            is_correct = bool(r['is_correct'])
                            time_score = self.calculate_time_score(response_time, max_time, is_correct)
                            phase_3_time_scores.append(time_score)
                    phase_time_factors['phase_3'] = sum(phase_3_time_scores) / len(phase_3_time_scores)
                
                # Update the diagnostic session
                cursor.execute("""
                    UPDATE diagnostic_sessions 
                    SET phase_1_time_factor = %s,
                        phase_2_time_factor = %s,
                        phase_3_time_factor = %s
                    WHERE session_id = %s
                """, (phase_time_factors['phase_1'], phase_time_factors['phase_2'], 
                      phase_time_factors['phase_3'], session_id))
                
                updated_count += 1
                self.log_error(f"Updated time factors for session {session_id}: P1={phase_time_factors['phase_1']:.3f}, P2={phase_time_factors['phase_2']:.3f}, P3={phase_time_factors['phase_3']:.3f}")
            
            conn.commit()
            
            return {
                'success': True,
                'updated_sessions': updated_count,
                'message': f'Updated {updated_count} diagnostic sessions with phase time factors'
            }
            
        except mysql.connector.Error as e:
            conn.rollback()
            self.log_error(f"Database error in update_null_phase_time_factors: {e}")
            return {'success': False, 'message': f'Database error: {str(e)}'}
        except Exception as e:
            self.log_error(f"Unexpected error in update_null_phase_time_factors: {e}")
            return {'success': False, 'message': 'Unexpected error occurred'}
        finally:
            if conn and conn.is_connected():
                conn.close()

def main():
    """Main function for command line interface"""
    if len(sys.argv) < 3:
        print(json.dumps({
            'success': False, 
            'message': 'Usage: python bkt_algorithm.py <action> [params...]'
        }))
        return
    
    action = sys.argv[1]
    bkt = BKTAlgorithm()
    
    try:
        if action == 'cleanup_diagnostic':
            # Special case for cleanup - only needs session_id
            if len(sys.argv) < 3:
                result = {'success': False, 'message': 'Missing session_id for cleanup'}
            else:
                session_id = sys.argv[2]
                result = bkt.cleanup_diagnostic_session(session_id)
        
        elif action == 'complete_diagnostic':
            # Special case for complete_diagnostic - can be called directly with just session_id
            if len(sys.argv) < 3:
                result = {'success': False, 'message': 'Missing session_id for complete_diagnostic'}
            else:
                session_id = sys.argv[2]
                result = bkt.complete_diagnostic(session_id)
        
        elif action == 'update_time_factors':
            # Update NULL phase time factors in existing sessions
            result = bkt.update_null_phase_time_factors()
        
        elif action == 'update_time_factors':
            # Update NULL phase time factors in existing sessions
            result = bkt.update_null_phase_time_factors()
        
        elif len(sys.argv) < 4:
            # Other actions need at least user_id and competency
            print(json.dumps({
                'success': False, 
                'message': 'Usage: python bkt_algorithm.py <action> <user_id> <competency> [additional_params]'
            }))
            return
        
        else:
            user_id = int(sys.argv[2])
            competency = sys.argv[3]
            
            if action == 'start_diagnostic':
                result = bkt.start_diagnostic(user_id, competency)
            
            elif action == 'resume_diagnostic':
                if len(sys.argv) < 5:
                    result = {'success': False, 'message': 'Missing session_id for resume_diagnostic'}
                else:
                    session_id = sys.argv[4]
                    result = bkt.resume_diagnostic_session(user_id, competency, session_id)
            
            elif action == 'record_answer':
                if len(sys.argv) < 8:
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
            
            elif action == 'calculate_mastery':
                # For regular assessments - calculate mastery score
                assessment_id = sys.argv[4]
                result = bkt.calculate_assessment_mastery(assessment_id)
                
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