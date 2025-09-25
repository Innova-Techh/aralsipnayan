#!/usr/bin/env python3
"""
Generate 900 realistic question responses for BKT AUC-ROC testing
This creates a diverse dataset with realistic student performance patterns
"""

import mysql.connector
import random
import sys
import time
from datetime import datetime, timedelta
import json

# Import the BKT algorithm
sys.path.append('public/algorithm')
from bkt_algorithm import BKTAlgorithm

class ResponseSimulator:
    def __init__(self):
        self.db_config = {
            'host': 'localhost',
            'user': 'root', 
            'password': '',
            'database': 'aralsipnayandb',
            'charset': 'utf8mb4'
        }
        self.bkt = BKTAlgorithm()
        
        # Simulation parameters - match what's actually in the database
        self.competencies = ['number_algebra', 'measurement_geometry', 'data_probability']
        self.difficulty_levels = ['beginner', 'intermediate', 'advanced']
        
        # Enhanced student performance profiles for better AUC-ROC discrimination
        self.student_profiles = {
            'exceptional_performer': {
                'accuracy_base': 0.92,
                'response_time_factor': 0.6,  # Very fast responses
                'consistency': 0.95,
                'learning_rate': 0.8  # Fast learning
            },
            'high_performer': {
                'accuracy_base': 0.82,
                'response_time_factor': 0.7,  # Fast responses
                'consistency': 0.88,
                'learning_rate': 0.6  # Good learning
            },
            'above_average_performer': {
                'accuracy_base': 0.72,
                'response_time_factor': 0.85,  # Slightly fast responses
                'consistency': 0.75,
                'learning_rate': 0.4  # Moderate learning
            },
            'average_performer': {
                'accuracy_base': 0.58,
                'response_time_factor': 1.0,  # Average responses
                'consistency': 0.65,
                'learning_rate': 0.3  # Slow learning
            },
            'below_average_performer': {
                'accuracy_base': 0.45,
                'response_time_factor': 1.2,  # Slow responses
                'consistency': 0.5,
                'learning_rate': 0.2  # Very slow learning
            },
            'struggling_performer': {
                'accuracy_base': 0.28,
                'response_time_factor': 1.4,  # Very slow responses
                'consistency': 0.35,
                'learning_rate': 0.1  # Minimal learning
            }
        }
        
    def connect_db(self):
        """Connect to database"""
        try:
            return mysql.connector.connect(**self.db_config)
        except mysql.connector.Error as e:
            print(f"Database connection failed: {e}")
            return None
    
    def get_or_create_questions(self, conn):
        """Get existing questions or create sample ones"""
        cursor = conn.cursor(dictionary=True)
        
        # Check if we have questions
        cursor.execute("SELECT COUNT(*) as count FROM questions WHERE is_active = 1")
        result = cursor.fetchone()
        
        if result['count'] < 50:
            print("Creating sample questions...")
            self.create_sample_questions(conn)
        
        # Get all active questions
        cursor.execute("""
            SELECT question_id, competency, difficulty_level, max_allowed_time
            FROM questions 
            WHERE is_active = 1
            ORDER BY competency, difficulty_level
        """)
        questions = cursor.fetchall()
        cursor.close()
        return questions
    
    def create_sample_questions(self, conn):
        """Create sample questions for simulation"""
        cursor = conn.cursor()
        
        question_templates = []
        question_id = 1
        
        for competency in self.competencies:
            for difficulty in self.difficulty_levels:
                for i in range(20):  # 20 questions per competency-difficulty combination
                    max_time = {
                        'beginner': 30,
                        'intermediate': 45, 
                        'advanced': 60
                    }[difficulty]
                    
                    question_templates.append((
                        f"Q_{competency.upper()}_{difficulty.upper()}_{i:03d}",
                        f"Sample {difficulty} question {i+1} for {competency}",
                        "multiple_choice",
                        "A", # correct_answer
                        f"This is a {difficulty} level question about {competency}",
                        competency,
                        difficulty,
                        max_time,
                        "sample_topic",
                        "Option A", "Option B", "Option C", "Option D",
                        1  # is_active
                    ))
        
        cursor.executemany("""
            INSERT INTO questions 
            (question_id, question_text, question_type, correct_answer, explanation,
             competency, difficulty_level, max_allowed_time, topic_tag,
             choice_a, choice_b, choice_c, choice_d, is_active)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """, question_templates)
        
        conn.commit()
        cursor.close()
        print(f"Created {len(question_templates)} sample questions")
    
    def simulate_student_response(self, student_profile, question, current_bkt, response_count=0):
        """Enhanced student response simulation with learning curves and better discrimination"""
        profile = self.student_profiles[student_profile]
        max_time = question['max_allowed_time']
        
        # Enhanced BKT influence with learning progression
        bkt_influence = current_bkt * 0.8  # Increased BKT influence for better discrimination
        
        # Profile influence decreases as BKT becomes more accurate (learning effect)
        profile_weight = 0.2 * (1 - min(response_count / 20, 0.7))  # Decreases as student answers more
        profile_influence = profile['accuracy_base'] * profile_weight
        
        # Difficulty-based adjustment
        difficulty_multipliers = {
            'beginner': 1.1,     # Slightly easier
            'intermediate': 1.0,  # Standard
            'advanced': 0.85     # Harder
        }
        difficulty_factor = difficulty_multipliers.get(question['difficulty_level'], 1.0)
        
        # Learning curve effect - students improve over time
        learning_improvement = min(response_count * profile['learning_rate'] * 0.01, 0.15)
        
        # Consistency factor with more variation for better discrimination
        consistency_range = 0.15 * (1 - profile['consistency'])
        random_factor = random.uniform(-consistency_range, consistency_range)
        
        # Calculate success probability with enhanced factors
        success_probability = (
            bkt_influence + 
            profile_influence + 
            learning_improvement + 
            random_factor
        ) * difficulty_factor
        
        # More realistic probability bounds with better spread
        success_probability = max(0.02, min(0.98, success_probability))
        
        is_correct = random.random() < success_probability
        
        # Enhanced response time simulation with performance correlation
        base_time = max_time * profile['response_time_factor']
        
        # Response time correlates with performance and confidence
        confidence_factor = current_bkt * 0.3  # Higher BKT = more confident = faster
        
        if is_correct:
            # Correct answers: faster for confident students, variable for others
            if current_bkt > 0.7:  # High confidence
                time_range = (0.3, 0.6)
            elif current_bkt > 0.4:  # Medium confidence  
                time_range = (0.4, 0.8)
            else:  # Low confidence but got it right (lucky/learning)
                time_range = (0.6, 1.0)
            
            response_time = base_time * random.uniform(*time_range) * (1 - confidence_factor * 0.2)
        else:
            # Incorrect answers: more variable timing patterns
            if current_bkt > 0.6:  # Should know but got wrong (slip)
                if random.random() < 0.6:
                    response_time = base_time * random.uniform(0.2, 0.5)  # Quick mistake
                else:
                    response_time = base_time * random.uniform(0.8, 1.1)  # Overthought
            else:  # Low knowledge, expected wrong
                response_time = base_time * random.uniform(0.7, 1.3)  # Struggled
        
        # Ensure reasonable bounds
        response_time = max(1.0, min(max_time * 1.2, response_time))
        
        return is_correct, response_time
    
    def create_realistic_users(self, conn, num_users=100):
        """Create realistic test users if they don't exist"""
        cursor = conn.cursor(dictionary=True)
        
        # Check existing users
        cursor.execute("SELECT COUNT(*) as count FROM users WHERE username LIKE 'test_student_%'")
        existing = cursor.fetchone()['count']
        
        if existing >= num_users:
            cursor.execute("SELECT id FROM users WHERE username LIKE 'test_student_%' LIMIT %s", (num_users,))
            return [row['id'] for row in cursor.fetchall()]
        
        # Create new test users  
        users_to_create = num_users - existing
        print(f"Creating {users_to_create} test users...")
        
        user_data = []
        for i in range(existing, num_users):
            user_data.append((
                f"test_student_{i:03d}",
                f"test_student_{i:03d}@example.com",
                "student",
                "$2y$10$example_hash",  # Laravel bcrypt hash
                datetime.now(),
                "active"
            ))
        
        cursor.executemany("""
            INSERT INTO users (username, email, role, password, created_at, status)
            VALUES (%s, %s, %s, %s, %s, %s)
        """, user_data)
        
        conn.commit()
        
        # Get all test user IDs
        cursor.execute("SELECT id FROM users WHERE username LIKE 'test_student_%' LIMIT %s", (num_users,))
        user_ids = [row['id'] for row in cursor.fetchall()]
        cursor.close()
        
        return user_ids
    
    def generate_900_responses(self):
        """Generate 900 realistic question responses"""
        conn = self.connect_db()
        if not conn:
            return False
        
        cursor = None
        try:
            print("Starting 900-response simulation...")
            
            # Get questions and users
            questions = self.get_or_create_questions(conn)
            user_ids = self.create_realistic_users(conn, 30)  # 30 test users
            
            print(f"Using {len(questions)} questions and {len(user_ids)} users")
            
            cursor = conn.cursor()
            
            # Initialize user BKT tracking and response counting for sequential learning
            user_bkt_tracking = {}
            user_response_counts = {}  # Track responses per user for learning curves
            user_profiles = {}  # Assign consistent profiles to users
            
            # Assign student profiles to users with better distribution for AUC-ROC
            profile_names = list(self.student_profiles.keys())
            for user_id in user_ids:
                profile_weights = [0.10, 0.20, 0.25, 0.25, 0.15, 0.05]  # More balanced distribution
                user_profiles[user_id] = random.choices(profile_names, weights=profile_weights)[0]
                user_response_counts[user_id] = 0
            
            # Create assessment sessions
            assessment_sessions = {}
            print("Creating assessment sessions...")
            for i, user_id in enumerate(user_ids):
                for competency in self.competencies:
                    session_id = f"SIM_ASSESS_{user_id}_{competency}_{int(time.time())}_{i}"
                    session_key = f"{user_id}_{competency}"
                    assessment_sessions[session_key] = session_id
                    
                    # Create assessment record
                    cursor.execute("""
                        INSERT INTO assessments 
                        (assessment_id, user_id, competency, assessment_type, difficulty_level,
                         total_questions, status, started_at)
                        VALUES (%s, %s, %s, 'simulation', 'mixed', 10, 'completed', NOW())
                    """, (session_id, user_id, competency))
            
            print(f"Created {len(assessment_sessions)} assessment sessions")
            conn.commit()  # Commit the assessments before creating responses
            
            responses_created = 0
            target_responses = 900
            
            # Generate responses
            response_data = []
            
            print("Generating responses...")
            for response_num in range(target_responses):
                # Select random user and question
                user_id = random.choice(user_ids)
                question = random.choice(questions)
                
                # Get consistent student profile for this user
                student_profile = user_profiles[user_id]
                
                # Get the session for this user-competency combination
                competency = question['competency']
                session_key = f"{user_id}_{competency}"
                
                if session_key not in assessment_sessions:
                    print(f"Warning: Session key '{session_key}' not found. Available keys: {list(assessment_sessions.keys())[:5]}...")
                    # Create a fallback session
                    session_id = f"SIM_FALLBACK_{user_id}_{competency.replace(' ', '_')}_{response_num}"
                else:
                    session_id = assessment_sessions[session_key]
                
                # FIXED: Get previous BKT for this user (across ALL competencies) to maintain sequential learning
                if user_id not in user_bkt_tracking:
                    # First response for this user - check if they have any existing responses
                    cursor.execute("""
                        SELECT bkt_after FROM question_responses 
                        WHERE user_id = %s
                        ORDER BY answered_at DESC LIMIT 1
                    """, (user_id,))
                    
                    result = cursor.fetchone()
                    user_bkt_tracking[user_id] = float(result[0]) if result else self.bkt.default_params['prior_knowledge']
                
                current_bkt = user_bkt_tracking[user_id]
                
                # Simulate response with learning curve tracking
                is_correct, response_time = self.simulate_student_response(
                    student_profile, question, current_bkt, user_response_counts[user_id]
                )
                
                # Increment response count for this user
                user_response_counts[user_id] += 1
                
                # Calculate BKT update
                bkt_after = self.bkt.calculate_bkt_update(
                    current_bkt, is_correct, self.bkt.default_params
                )
                
                # Update user BKT tracking for next response
                user_bkt_tracking[user_id] = bkt_after
                
                # Calculate time metrics
                max_time = question['max_allowed_time'] 
                normalized_time = response_time / max_time
                time_score = self.bkt.calculate_time_score(response_time, max_time, is_correct)
                
                # Create response record
                response_id = f"RESP_SIM_{user_id}_{question['question_id']}_{int(time.time() * 1000000)}_{response_num}"
                
                response_data.append((
                    response_id,
                    session_id,
                    user_id,
                    question['question_id'],
                    "A" if is_correct else "B",  # Simulated answer
                    is_correct,
                    response_time,
                    max_time,
                    normalized_time,
                    time_score,
                    current_bkt,
                    bkt_after,
                    1.0,  # difficulty_factor
                    time_score,  # time_factor
                    time_score   # ftime_factor
                ))
                
                responses_created += 1
                
                if responses_created % 100 == 0:
                    print(f"Generated {responses_created} responses...")
            
            # Batch insert all responses
            print("Inserting responses into database...")
            cursor.executemany("""
                INSERT INTO question_responses 
                (response_id, assessment_id, user_id, question_id, user_answer,
                 is_correct, response_time, max_allowed_time, normalized_time, time_score,
                 bkt_before, bkt_after, difficulty_factor, time_factor, ftime_factor)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """, response_data)
            
            conn.commit()
            
            print(f"✓ Successfully created {responses_created} question responses")
            return True
            
        except Exception as e:
            print(f"Error during simulation: {e}")
            conn.rollback()
            return False
        finally:
            if cursor:
                cursor.close()
            conn.close()

def main():
    """Run the 900-response simulation"""
    simulator = ResponseSimulator()
    
    print("="*60)
    print("900-RESPONSE BKT SIMULATION")
    print("="*60)
    
    success = simulator.generate_900_responses()
    
    if success:
        print("\nSimulation completed successfully!")
        print("You can now run the AUC-ROC metrics test to see improved performance")
        print("Command: python tests/BKT_AUC_ROC_Metrics.py")
    else:
        print("\nSimulation failed")

if __name__ == "__main__":
    main()