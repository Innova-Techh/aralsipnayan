#!/usr/bin/env python3

import sys
import json
import mysql.connector
from datetime import datetime
from typing import Dict, Any, Optional
import random

# Import the existing BKT classes
try:
    from bkt import (
        Competency, DifficultyLevel, QuestionType, Question, StudentMastery,
        DatabaseManager, BayesianKnowledgeTracing, QuestionSelector,
        LearningSessionManager, run_competency_assessment_flow
    )
except ImportError as e:
    print(json.dumps({"success": False, "error": f"Failed to import BKT modules: {str(e)}"}))
    sys.exit(1)

class BKTIntegration:
    def __init__(self):
        """Initialize BKT integration with database connection"""
        self.db = DatabaseManager(
            host='localhost',
            user='root',
            password='',
            database='aralsipnayan'
        )
        self.db.connect()
        
        if not self.db.connection:
            raise Exception("Failed to connect to database")
        
        self.session_manager = LearningSessionManager(self.db)
    
    def start_assessment(self, user_id: int, competency_str: str) -> Dict[str, Any]:
        """
        Start a new assessment session for the given user and competency
        """
        try:
            competency = Competency(competency_str)
            
            # Use the existing BKT flow
            result = run_competency_assessment_flow(self.db, user_id, competency)
            
            return {
                "success": True,
                "session_id": result["session_id"],
                "competency": result["competency"],
                "diagnostic": result["diagnostic"],
                "difficulty_level": result["difficulty_level_used"],
                "initial_mastery": result["initial_mastery"],
                "questions": result["questions"]
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e)
            }
    
    def record_answer(self, user_id: int, session_id: str, question_id: str, 
                     answer: str, is_correct: bool, time_taken: Optional[int] = None,
                     hint_used: bool = False) -> Dict[str, Any]:
        """
        Record a student's answer and update BKT in real-time
        """
        try:
            # Get question details from database
            question_data = self.db.execute_query("""
                SELECT question_id, competency, difficulty_level, question_type,
                       question_text, correct_answer, choice_a, choice_b, choice_c, choice_d,
                       hint_text, explanation, topic_tag, COALESCE(points, 1) as points
                FROM questions 
                WHERE question_id = %s
            """, (question_id,))
            
            if not question_data:
                return {"success": False, "error": "Question not found"}
            
            q_data = question_data[0]
            
            # Create Question object
            question = Question(
                question_id=q_data['question_id'],
                competency=Competency(q_data['competency']),
                difficulty_level=DifficultyLevel(q_data['difficulty_level']),
                question_type=QuestionType(q_data['question_type']),
                question_text=q_data['question_text'],
                correct_answer=q_data['correct_answer'],
                choice_a=q_data.get('choice_a'),
                choice_b=q_data.get('choice_b'),
                choice_c=q_data.get('choice_c'),
                choice_d=q_data.get('choice_d'),
                hint_text=q_data.get('hint_text'),
                explanation=q_data.get('explanation'),
                topic_tag=q_data.get('topic_tag'),
                points=q_data.get('points', 1)
            )
            
            # Record the answer using the session manager
            success = self.session_manager.record_answer(
                user_id=user_id,
                session_id=session_id,
                question=question,
                user_answer=answer,
                is_correct=is_correct,
                time_taken=time_taken,
                hint_used=hint_used
            )
            
            if not success:
                return {"success": False, "error": "Failed to record answer"}
            
            # Get updated mastery
            bkt = BayesianKnowledgeTracing(self.db)
            mastery = bkt.get_student_mastery(user_id, question.competency)
            new_mastery = mastery.mastery_probability if mastery else 0.3
            
            # Calculate points earned
            points = question.points or 1
            if hint_used and is_correct:
                points = max(1, points // 2)  # Half points for hint usage
            elif not is_correct:
                points = 0
            
            return {
                "success": True,
                "new_mastery": round(new_mastery, 3),
                "points_earned": points
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e)
            }
    
    def complete_session(self, user_id: int, session_id: str, competency_str: str, 
                        initial_mastery: float) -> Dict[str, Any]:
        """
        Complete the assessment session and apply post-processing
        """
        try:
            competency = Competency(competency_str)
            
            # End session and get results
            results = self.session_manager.end_session_and_postprocess(
                user_id=user_id,
                session_id=session_id,
                competency=competency,
                initial_mastery=initial_mastery
            )
            
            # Get session statistics
            stats = self.db.execute_query("""
                SELECT 
                    COUNT(*) as total_questions,
                    SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                    SUM(points_earned) as total_points,
                    AVG(CASE WHEN is_correct = 1 THEN 1.0 ELSE 0.0 END) as accuracy_rate,
                    AVG(response_time_seconds) as avg_response_time
                FROM question_responses
                WHERE session_id = %s
            """, (session_id,))
            
            session_stats = stats[0] if stats else {}
            
            # Get final mastery
            bkt = BayesianKnowledgeTracing(self.db)
            mastery = bkt.get_student_mastery(user_id, competency)
            final_mastery = mastery.mastery_probability if mastery else initial_mastery
            
            return {
                "success": True,
                "results": {
                    "total_questions": session_stats.get('total_questions', 0),
                    "correct_answers": session_stats.get('correct_answers', 0),
                    "total_points": session_stats.get('total_points', 0),
                    "accuracy_rate": round(float(session_stats.get('accuracy_rate', 0)), 3),
                    "avg_response_time": session_stats.get('avg_response_time'),
                    "initial_mastery": round(initial_mastery, 3),
                    "final_mastery": round(final_mastery, 3),
                    "mastery_change": round(final_mastery - initial_mastery, 3),
                    "new_difficulty_level": mastery.current_difficulty_level.value if mastery else "Beginner",
                    "action_taken": results.get("action", "MAINTAIN") if results else "MAINTAIN"
                }
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e)
            }
    
    def close(self):
        """Close database connection"""
        if self.db:
            self.db.disconnect()

def main():
    """Main entry point for the integration script"""
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "No input provided"}))
        return
    
    try:
        # Parse input
        input_data = json.loads(sys.argv[1])
        action = input_data.get('action')
        
        # Initialize BKT integration
        bkt_integration = BKTIntegration()
        
        result = {"success": False, "error": "Unknown action"}
        
        try:
            if action == 'start_assessment':
                user_id = input_data.get('user_id')
                competency = input_data.get('competency')
                result = bkt_integration.start_assessment(user_id, competency)
                
            elif action == 'record_answer':
                user_id = input_data.get('user_id')
                session_id = input_data.get('session_id')
                question_id = input_data.get('question_id')
                answer = input_data.get('answer')
                is_correct = input_data.get('is_correct')
                time_taken = input_data.get('time_taken')
                hint_used = input_data.get('hint_used', False)
                
                result = bkt_integration.record_answer(
                    user_id, session_id, question_id, answer, 
                    is_correct, time_taken, hint_used
                )
                
            elif action == 'complete_session':
                user_id = input_data.get('user_id')
                session_id = input_data.get('session_id')
                competency = input_data.get('competency')
                initial_mastery = input_data.get('initial_mastery')
                
                result = bkt_integration.complete_session(
                    user_id, session_id, competency, initial_mastery
                )
        
        finally:
            bkt_integration.close()
        
        print(json.dumps(result))
        
    except json.JSONDecodeError:
        print(json.dumps({"success": False, "error": "Invalid JSON input"}))
    except Exception as e:
        print(json.dumps({"success": False, "error": str(e)}))

if __name__ == "__main__":
    main()
