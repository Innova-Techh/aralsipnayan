#!/usr/bin/env python3

import sys
import json
import mysql.connector
import random
from datetime import datetime
from typing import Dict, Any, Optional, List
from fisher_yates import AssessmentShuffle, FisherYatesShuffle

class SimpleBKTIntegration:
    def __init__(self, shuffle_seed: Optional[int] = None):
        """Initialize with database connection and optional shuffle seed"""
        try:
            self.connection = mysql.connector.connect(
                host='127.0.0.1',
                user='root',
                password='',
                database='aralsipnayandb'
            )
            self.cursor = self.connection.cursor(dictionary=True)
            self.shuffle_seed = shuffle_seed
        except mysql.connector.Error as err:
            raise Exception(f"Database connection failed: {err}")
    
    def execute_query(self, query: str, params: tuple = None):
        """Execute a database query"""
        try:
            self.cursor.execute(query, params or ())
            if query.strip().upper().startswith(('INSERT', 'UPDATE', 'DELETE')):
                self.connection.commit()
                return True
            return self.cursor.fetchall()
        except mysql.connector.Error as err:
            self.connection.rollback()
            raise Exception(f"Query failed: {err}")
    
    def get_student_mastery(self, user_id: int, competency: str) -> Optional[Dict]:
        """Get current mastery for a student and competency"""
        result = self.execute_query("""
            SELECT * FROM student_competency_mastery
            WHERE user_id = %s AND competency = %s
        """, (user_id, competency))
        return result[0] if result else None
    
    def initialize_mastery_if_needed(self, user_id: int, competency: str):
        """Initialize mastery record if it doesn't exist"""
        existing = self.get_student_mastery(user_id, competency)
        if not existing:
            self.execute_query("""
                INSERT INTO student_competency_mastery 
                (user_id, competency, current_difficulty_level, mastery_probability, 
                 total_questions_answered, correct_answers, diagnostic_completed)
                VALUES (%s, %s, 'Beginner', 0.300, 0, 0, FALSE)
            """, (user_id, competency))
    
    def get_questions_for_assessment(self, competency: str, difficulty: str, exclude_ids: List[str] = None) -> List[Dict]:
        """Get questions for assessment with Fisher-Yates shuffling"""
        exclude_clause = ""
        params = [competency, difficulty]
        
        if exclude_ids:
            placeholders = ','.join(['%s'] * len(exclude_ids))
            exclude_clause = f" AND question_id NOT IN ({placeholders})"
            params.extend(exclude_ids)
        
        # First, get all available questions without RAND() ordering
        query = f"""
            SELECT question_id, competency, difficulty_level, question_type,
                   question_text, correct_answer, choice_a, choice_b, choice_c, choice_d,
                   hint_text, explanation, topic_tag, COALESCE(points, 5) as points
            FROM questions
            WHERE competency = %s AND difficulty_level = %s AND is_active = TRUE
            {exclude_clause}
        """
        
        all_questions = self.execute_query(query, tuple(params))
        
        if not all_questions:
            return []
        
        # Use Fisher-Yates shuffle for proper randomization
        assessment_shuffle = AssessmentShuffle(seed=self.shuffle_seed)
        
        # Shuffle with topic diversity to ensure better question distribution
        shuffled_questions = assessment_shuffle.shuffle_with_topic_diversity(all_questions)
        
        # Return up to 15 questions
        return shuffled_questions[:15]
    
    def start_assessment(self, user_id: int, competency: str) -> Dict[str, Any]:
        """Start a new assessment session"""
        try:
            # Initialize mastery if needed
            self.initialize_mastery_if_needed(user_id, competency)
            
            # Get current mastery
            mastery = self.get_student_mastery(user_id, competency)
            if not mastery:
                return {"success": False, "error": "Failed to get mastery data"}
            
            # Determine if diagnostic is needed
            diagnostic_needed = not mastery['diagnostic_completed']
            
            # Determine difficulty level
            if diagnostic_needed:
                difficulty_level = "Mixed"  # For diagnostic, we'll use Beginner for now
                actual_difficulty = "Beginner"
            else:
                mastery_prob = float(mastery['mastery_probability'])
                if mastery_prob < 0.4:
                    actual_difficulty = "Beginner"
                elif mastery_prob < 0.7:
                    actual_difficulty = "Intermediate"
                else:
                    actual_difficulty = "Advanced"
                difficulty_level = actual_difficulty
            
            # Get questions
            questions = self.get_questions_for_assessment(competency, actual_difficulty)
            
            if not questions:
                return {"success": False, "error": "No questions available for this competency and difficulty"}
            
            # Generate session ID
            session_id = f"STU{user_id:03d}-{competency.split('_')[0]}-{datetime.now().strftime('%Y%m%d')}-{random.randint(100, 999):03d}"
            
            # Create session record
            question_ids = [q['question_id'] for q in questions]
            self.execute_query("""
                INSERT INTO assessment_sessions
                (session_id, user_id, session_type, competency, difficulty_level,
                 questions_json, total_questions, start_time, status, initial_mastery_probability)
                VALUES (%s, %s, %s, %s, %s, %s, %s, NOW(), 'in_progress', %s)
            """, (session_id, user_id, 
                  'Diagnostic' if diagnostic_needed else 'Adaptive',
                  competency, difficulty_level, json.dumps(question_ids),
                  len(questions), float(mastery['mastery_probability'])))
            
            return {
                "success": True,
                "session_id": session_id,
                "competency": competency,
                "diagnostic": diagnostic_needed,
                "difficulty_level": difficulty_level,
                "initial_mastery": float(mastery['mastery_probability']),
                "questions": questions
            }
            
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def update_bkt_mastery(self, user_id: int, competency: str, is_correct: bool) -> float:
        """Simple BKT update"""
        mastery = self.get_student_mastery(user_id, competency)
        if not mastery:
            return 0.3
        
        current_prob = float(mastery['mastery_probability'])
        p_learn = 0.1
        p_guess = 0.25
        p_slip = 0.1
        
        if is_correct:
            # P(K=1|correct) using Bayes' theorem
            numerator = current_prob * (1 - p_slip)
            denominator = current_prob * (1 - p_slip) + (1 - current_prob) * p_guess
            new_prob = numerator / denominator if denominator > 0 else current_prob
        else:
            # P(K=1|incorrect)
            numerator = current_prob * p_slip
            denominator = current_prob * p_slip + (1 - current_prob) * (1 - p_guess)
            new_prob = numerator / denominator if denominator > 0 else current_prob
        
        # Apply learning
        final_prob = new_prob + (1 - new_prob) * p_learn
        final_prob = max(0.0, min(1.0, final_prob))
        
        # Update in database
        self.execute_query("""
            UPDATE student_competency_mastery
            SET mastery_probability = %s,
                total_questions_answered = total_questions_answered + 1,
                correct_answers = correct_answers + %s,
                last_updated = NOW()
            WHERE user_id = %s AND competency = %s
        """, (final_prob, 1 if is_correct else 0, user_id, competency))
        
        return final_prob
    
    def record_answer(self, user_id: int, session_id: str, question_id: str, 
                     answer: str, is_correct: bool, time_taken: Optional[int] = None,
                     hint_used: bool = False) -> Dict[str, Any]:
        """Record an answer and update BKT"""
        try:
            # Get question details
            question_data = self.execute_query("""
                SELECT competency, COALESCE(points, 5) as points
                FROM questions WHERE question_id = %s
            """, (question_id,))
            
            if not question_data:
                return {"success": False, "error": "Question not found"}
            
            question = question_data[0]
            competency = question['competency']
            
            # Get mastery before update
            mastery_before = self.get_student_mastery(user_id, competency)
            m_before = float(mastery_before['mastery_probability']) if mastery_before else 0.3
            
            # Update BKT mastery
            m_after = self.update_bkt_mastery(user_id, competency, is_correct)
            
            # Calculate points
            points = int(question['points'])
            if hint_used and is_correct:
                points = max(1, points // 2)
            elif not is_correct:
                points = 0
            
            # Get question order
            order_result = self.execute_query("""
                SELECT COALESCE(MAX(question_order), 0) + 1 as next_order
                FROM question_responses WHERE session_id = %s
            """, (session_id,))
            question_order = order_result[0]['next_order'] if order_result else 1
            
            # Record response
            self.execute_query("""
                INSERT INTO question_responses
                (session_id, question_id, question_order, student_answer, is_correct,
                 points_earned, response_time_seconds, mastery_before, mastery_after,
                 hint_used, hint_penalty_applied, answered_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW())
            """, (session_id, question_id, question_order, answer, is_correct,
                  points, time_taken, m_before, m_after, hint_used, 
                  hint_used and is_correct))
            
            return {
                "success": True,
                "new_mastery": m_after,
                "points_earned": points
            }
            
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def complete_session(self, user_id: int, session_id: str, competency: str, 
                        initial_mastery) -> Dict[str, Any]:
        """Complete assessment session"""
        try:
            # Ensure initial_mastery is a float, handle None and empty values
            try:
                initial_mastery = float(initial_mastery) if initial_mastery not in (None, '', 'null') else 0.3
            except (ValueError, TypeError):
                initial_mastery = 0.3
            
            # Get session statistics
            stats = self.execute_query("""
                SELECT 
                    COUNT(*) as total_questions,
                    SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                    SUM(points_earned) as total_points,
                    AVG(CASE WHEN is_correct = 1 THEN 1.0 ELSE 0.0 END) as accuracy_rate
                FROM question_responses
                WHERE session_id = %s
            """, (session_id,))
            
            session_stats = stats[0] if stats else {}
            
            # Get final mastery
            mastery = self.get_student_mastery(user_id, competency)
            final_mastery = float(mastery['mastery_probability']) if mastery else initial_mastery
            mastery_change = final_mastery - initial_mastery
            
            # Determine new difficulty level based on mastery change
            current_level = mastery['current_difficulty_level'] if mastery else 'Beginner'
            new_level = current_level
            action = "MAINTAIN"
            
            if mastery_change >= 0.15:
                if current_level == 'Beginner':
                    new_level = 'Intermediate'
                elif current_level == 'Intermediate':
                    new_level = 'Advanced'
                action = "PROMOTE"
            elif mastery_change <= -0.10:
                if current_level == 'Advanced':
                    new_level = 'Intermediate'
                elif current_level == 'Intermediate':
                    new_level = 'Beginner'
                action = "REMEDIATE"
            
            # Update mastery level
            self.execute_query("""
                UPDATE student_competency_mastery
                SET current_difficulty_level = %s,
                    diagnostic_completed = TRUE,
                    last_assessment_date = NOW()
                WHERE user_id = %s AND competency = %s
            """, (new_level, user_id, competency))
            
            # Close session
            self.execute_query("""
                UPDATE assessment_sessions
                SET end_time = NOW(),
                    status = 'completed',
                    questions_answered = %s,
                    correct_answers = %s,
                    total_points_earned = %s,
                    accuracy_rate = %s,
                    final_mastery_probability = %s
                WHERE session_id = %s
            """, (int(session_stats.get('total_questions', 0) or 0),
                  int(session_stats.get('correct_answers', 0) or 0),
                  int(session_stats.get('total_points', 0) or 0),
                  float(session_stats.get('accuracy_rate', 0) or 0),
                  final_mastery, session_id))
            
            return {
                "success": True,
                "results": {
                    "total_questions": int(session_stats.get('total_questions', 0) or 0),
                    "correct_answers": int(session_stats.get('correct_answers', 0) or 0),
                    "total_points": int(session_stats.get('total_points', 0) or 0),
                    "accuracy_rate": round(float(session_stats.get('accuracy_rate', 0) or 0), 3),
                    "initial_mastery": round(initial_mastery, 3),
                    "final_mastery": round(final_mastery, 3),
                    "mastery_change": round(mastery_change, 3),
                    "new_difficulty_level": new_level,
                    "action_taken": action
                }
            }
            
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def close(self):
        """Close database connection"""
        if hasattr(self, 'cursor') and self.cursor:
            self.cursor.close()
        if hasattr(self, 'connection') and self.connection:
            self.connection.close()

def main():
    """Main entry point"""
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "No input file provided"}))
        return
    
    try:
        # Read input from file
        input_file = sys.argv[1]
        with open(input_file, 'r') as f:
            input_data = json.load(f)
        
        action = input_data.get('action')
        
        if not action:
            print(json.dumps({"success": False, "error": "No action specified"}))
            return
        
        # Initialize BKT integration
        bkt = SimpleBKTIntegration()
        
        result = {"success": False, "error": "Unknown action"}
        
        try:
            if action == 'start_assessment':
                user_id = input_data.get('user_id')
                competency = input_data.get('competency')
                
                if not user_id or not competency:
                    result = {"success": False, "error": "Missing user_id or competency"}
                else:
                    # Ensure user_id is an integer
                    user_id = int(user_id)
                    result = bkt.start_assessment(user_id, competency)
                    
            elif action == 'record_answer':
                user_id = input_data.get('user_id')
                session_id = input_data.get('session_id')
                question_id = input_data.get('question_id')
                answer = input_data.get('answer')
                is_correct = input_data.get('is_correct')
                time_taken = input_data.get('time_taken')
                hint_used = input_data.get('hint_used', False)
                
                # Ensure proper types
                if user_id is not None:
                    user_id = int(user_id)
                if time_taken is not None:
                    time_taken = int(time_taken)
                if isinstance(is_correct, str):
                    is_correct = is_correct.lower() in ('true', '1', 'yes')
                elif is_correct is not None:
                    is_correct = bool(is_correct)
                
                result = bkt.record_answer(
                    user_id, session_id, question_id, answer,
                    is_correct, time_taken, hint_used
                )
                
            elif action == 'complete_session':
                user_id = input_data.get('user_id')
                session_id = input_data.get('session_id')
                competency = input_data.get('competency')
                initial_mastery = input_data.get('initial_mastery')
                
                # Ensure user_id is an integer
                if user_id is not None:
                    user_id = int(user_id)
                
                result = bkt.complete_session(
                    user_id, session_id, competency, initial_mastery
                )
        
        finally:
            bkt.close()
        
        print(json.dumps(result))
        
    except FileNotFoundError:
        print(json.dumps({"success": False, "error": "Input file not found"}))
    except json.JSONDecodeError as e:
        print(json.dumps({"success": False, "error": f"Invalid JSON input: {str(e)}"}))
    except Exception as e:
        print(json.dumps({"success": False, "error": f"Unexpected error: {str(e)}"}))

if __name__ == "__main__":
    main()
