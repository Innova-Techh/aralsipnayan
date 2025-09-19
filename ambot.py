#!/usr/bin/env python3

import requests
import json
import time
import random
import mysql.connector
from datetime import datetime
from concurrent.futures import ThreadPoolExecutor, as_completed

class SimpleQuestionBot:
    def __init__(self, base_url="http://127.0.0.1:8000"):
        self.base_url = base_url.rstrip('/')
        self.session = requests.Session()
        self.current_username = None
        self.current_user_id = None
        
        # Database configuration
        self.db_config = {
            'host': '127.0.0.1',
            'user': 'root',
            'password': '',
            'database': 'aralsipnayandb',
            'charset': 'utf8mb4'
        }
    
    def connect_db(self):
        """Connect to database"""
        try:
            return mysql.connector.connect(**self.db_config)
        except mysql.connector.Error as e:
            print(f"Database connection failed: {e}")
            return None
    
    def login_student(self, username, password):
        """Login as a student"""
        login_url = f"{self.base_url}/login"
        
        # Get CSRF token first
        response = self.session.get(login_url)
        if response.status_code != 200:
            print(f"Failed to access login page: {response.status_code}")
            return False
        
        # Extract CSRF token (simplified - you may need to parse HTML)
        csrf_token = self._extract_csrf_token(response.text)
        
        # Login
        login_data = {
            'username': username,
            'password': password,
            '_token': csrf_token
        }
        
        response = self.session.post(login_url, data=login_data)
        
        if 'dashboard' in response.url or response.status_code == 200:
            print(f"Successfully logged in as {username}")
            self.current_username = username
            # Try to resolve and cache user_id for DB ops
            self.current_user_id = self._get_user_id_by_username(username)
            return True
        else:
            print(f"Login failed for {username}")
            return False
    
    def _extract_csrf_token(self, html_content):
        """Extract CSRF token from HTML using multiple methods"""
        import re
        
        # Method 1: Hidden input field
        patterns = [
            r'name="_token" value="([^"]+)"',
            r'name="_token"\s+value="([^"]+)"',
            r'<input[^>]*name="_token"[^>]*value="([^"]+)"',
            r'<input[^>]*value="([^"]+)"[^>]*name="_token"'
        ]
        
        for pattern in patterns:
            match = re.search(pattern, html_content, re.IGNORECASE)
            if match:
                return match.group(1)
        
        # Method 2: Meta tag
        meta_patterns = [
            r'<meta name="csrf-token" content="([^"]+)"',
            r'<meta name="_token" content="([^"]+)"',
            r'name="csrf-token"[^>]*content="([^"]+)"'
        ]
        
        for pattern in meta_patterns:
            match = re.search(pattern, html_content, re.IGNORECASE)
            if match:
                return match.group(1)
        
        # Method 3: JavaScript variable
        js_patterns = [
            r'window\.Laravel\s*=\s*{[^}]*"csrfToken"\s*:\s*"([^"]+)"',
            r'_token["\']?\s*[:=]\s*["\']([^"\']+)["\']',
            r'csrf_token["\']?\s*[:=]\s*["\']([^"\']+)["\']'
        ]
        
        for pattern in js_patterns:
            match = re.search(pattern, html_content, re.IGNORECASE)
            if match:
                return match.group(1)
        
        return ''
    
    # Removed unused assessment_id extractor
    
    def auto_answer_diagnostic(self, competency, accuracy_rate=0.7, speed_factor=0.5):
        """Automatically complete a diagnostic assessment"""
        print(f"Starting diagnostic for competency: {competency}")
        print(f"Target accuracy: {accuracy_rate*100}%")
        
        # Follow the correct flow: assessments -> category -> quiz
        # Step 1: Navigate to assessments page
        assessments_url = f"{self.base_url}/student/assessments"
        response = self.session.get(assessments_url)
        
        if response.status_code != 200:
            print(f"Failed to access assessments page: {response.status_code}")
            return False
        
        print("✓ Accessed assessments page")
        
        # Step 2: Navigate to specific competency assessments
        competency_formatted = '_'.join(word.capitalize() for word in competency.split('_'))
        category_url = f"{self.base_url}/student/assessments/{competency_formatted}"
        response = self.session.get(category_url)
        
        if response.status_code != 200:
            print(f"Failed to access category page: {response.status_code}")
            return False
        
        print(f"✓ Accessed {competency_formatted} category page")
        
        # Ensure a student_mastery row exists so per-answer updates in backend succeed
        try:
            user_id = self.current_user_id or self._get_user_id_by_username(self.current_username) if self.current_username else None
            if user_id:
                self._ensure_student_mastery_row(user_id, competency)
            else:
                print("⚠ Could not resolve user_id; skipping mastery pre-create.")
        except Exception as e:
            print(f"⚠ Failed to ensure mastery row exists: {e}")
        
        # Step 3: For diagnostic, we don't need assessment_id - the session handles it
        # Navigate directly to the diagnostic quiz page (it will use session data)
        quiz_url = f"{self.base_url}/student/quiz/{competency_formatted}"
        response = self.session.get(quiz_url)
        
        if response.status_code != 200:
            print(f"Failed to access quiz: {response.status_code}")
            return False
        
        print("✓ Accessed diagnostic quiz page")
        # print("quiz_url: ", quiz_url)
        # Extract the current session question from the quiz page
        current_question_id = self._extract_current_question_id_from_quiz_page(response.text)
        if not current_question_id:
            print("Failed to detect current question from quiz page. The page may load via JS.")
            print("Tip: ensure the quiz page renders a hidden input or data-question-id attribute.")
            return False

        answered_count = 0
        correct_count = 0
        total_time_taken = 0
        while True:
            # Fetch full question from DB using the exact id from the session
            question = self._get_question_by_id(current_question_id)
            if not question:
                print(f"Could not load question by id: {current_question_id}")
                return False

            answered_count += 1
            print(f"Answering question #{answered_count}: {question['question_text'][:50]}...")

            # Simulate thinking time
            thinking_time = random.uniform(1, 2)
            time.sleep(thinking_time)

            # Generate answer for this question
            answer_data = self._generate_smart_answer(question, accuracy_rate, speed_factor)

            # Submit and use server response to advance the session
            submit_result = self._submit_diagnostic_answer(question, answer_data, competency, return_json=True)
            if not submit_result or not submit_result.get('success'):
                print("  ✗ Failed to submit answer")
                return False

            print(f"  ✓ Answered: {answer_data['answer'][:30]}... | Correct: {submit_result.get('is_correct')}")
            print(f" Time taken: {answer_data['time_taken']}s")
            # Track stats
            if submit_result.get('is_correct'):
                correct_count += 1
            total_time_taken += answer_data['time_taken']
            # Check for completion states
            if submit_result.get('diagnostic_complete'):
                total_answered = answered_count
                avg_time = total_time_taken / total_answered if total_answered > 0 else 0
                accuracy_pct = (correct_count / total_answered * 100) if total_answered > 0 else 0
                print(f"Correct: {correct_count}/{total_answered}")
                print(f"Accuracy: {accuracy_pct:.1f}%")
                print(f"Avg response time: {avg_time:.1f}s")
                print("Diagnostic completed!")
                return True

            # Not complete yet, move to the next question provided by server
            next_question = submit_result.get('next_question')
            if not next_question:
                # If phase completed, server may return next_phase and a next_question
                # but if not present, try to refresh the quiz page to pick up the new question
                print("No next_question in response, refreshing quiz page to locate next question...")
                response = self.session.get(quiz_url)
                if response.status_code != 200:
                    print(f"Failed to refresh quiz page: {response.status_code}")
                    return False
                current_question_id = self._extract_current_question_id_from_quiz_page(response.text)
                if not current_question_id:
                    print("Failed to detect next question after refresh.")
                    return False
                continue

            # Prefer the id from server response to avoid drift
            current_question_id = next_question.get('question_id') or next_question.get('id')
            if not current_question_id:
                print("Server did not include question_id for next question. Aborting to avoid misalignment.")
                return False
    
    def _get_diagnostic_questions(self, competency):
        """Get diagnostic questions from database"""
        conn = self.connect_db()
        if not conn:
            return []
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get questions for all difficulty levels (diagnostic pattern)
            cursor.execute("""
                (SELECT * FROM questions WHERE competency = %s AND difficulty_level = 'beginner' 
                 AND is_active = 1 ORDER BY RAND() LIMIT 5)
                UNION ALL
                (SELECT * FROM questions WHERE competency = %s AND difficulty_level = 'intermediate' 
                 AND is_active = 1 ORDER BY RAND() LIMIT 5)
                UNION ALL
                (SELECT * FROM questions WHERE competency = %s AND difficulty_level = 'advanced' 
                 AND is_active = 1 ORDER BY RAND() LIMIT 5)
            """, (competency, competency, competency))
            
            return cursor.fetchall()
        
        except mysql.connector.Error as e:
            print(f"Error fetching questions: {e}")
            return []
        finally:
            conn.close()

    def _get_question_by_id(self, question_id):
        """Fetch a single question by id from the DB."""
        conn = self.connect_db()
        if not conn:
            return None

        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute("SELECT * FROM questions WHERE question_id = %s", (question_id,))
            return cursor.fetchone()
        except mysql.connector.Error as e:
            print(f"Error fetching question by id: {e}")
            return None
        finally:
            conn.close()

    def _get_user_id_by_username(self, username):
        """Resolve user_id from users table by username."""
        conn = self.connect_db()
        if not conn:
            return None

        try:
            cursor = conn.cursor()
            cursor.execute("SELECT id FROM users WHERE username = %s", (username,))
            row = cursor.fetchone()
            return row[0] if row else None
        except mysql.connector.Error as e:
            print(f"Error fetching user id: {e}")
            return None
        finally:
            conn.close()

    def _ensure_student_mastery_row(self, user_id, competency):
        """Create student_mastery row if it does not exist yet, so counters can be updated by backend."""
        conn = self.connect_db()
        if not conn:
            return False

        try:
            cursor = conn.cursor()
            cursor.execute("SELECT COUNT(*) FROM student_mastery WHERE user_id = %s AND competency = %s", (user_id, competency))
            exists = cursor.fetchone()[0] > 0
            if exists:
                return True

            # Insert minimal row; defaults in schema handle most fields
            mastery_id = f"MAST_{user_id}_{competency}_{int(time.time())}"
            cursor.execute(
                """
                INSERT INTO student_mastery (mastery_id, user_id, competency, current_difficulty, created_at, updated_at)
                VALUES (%s, %s, %s, 'beginner', NOW(), NOW())
                """,
                (mastery_id, user_id, competency)
            )
            conn.commit()
            print(f"✓ Ensured student_mastery row for user {user_id}, {competency}")
            return True
        except mysql.connector.Error as e:
            print(f"Error ensuring student_mastery row: {e}")
            return False
        finally:
            conn.close()
    
    def _generate_smart_answer(self, question, accuracy_rate, speed_factor):
        """Generate a smart answer based on question content and desired accuracy"""
        will_be_correct = random.random() < accuracy_rate
        
        # Calculate response time
        max_time = question['max_allowed_time']
        base_time = max_time * speed_factor
        response_time = int(base_time * (0.8 + 0.4 * random.random()))
        response_time = max(1, min(response_time, max_time))
        
        if question['question_type'] == 'multiple_choice':
            if will_be_correct:
                # Return correct answer letter (A, B, C, or D) - this should match what's in correct_answer field
                answer = question['correct_answer'].upper()
                actual_is_correct = True
            else:
                # Return a wrong answer letter
                correct_letter = question['correct_answer'].upper()
                wrong_choices = [l for l in ['A', 'B', 'C', 'D'] if l != correct_letter]
                answer = random.choice(wrong_choices)
                actual_is_correct = False
        
        elif question['question_type'] == 'true_false':
            if will_be_correct:
                # For true/false: Send "A" for True, "B" for False (but backend expects the letter)
                # The correct_answer field should already contain "A" or "B"
                answer = question['correct_answer'].upper()
                actual_is_correct = True
            else:
                # Return opposite letter
                correct_letter = question['correct_answer'].upper()
                answer = "B" if correct_letter == "A" else "A"
                actual_is_correct = False
        
        elif question['question_type'] == 'fill_blanks':
            if will_be_correct:
                answer = question['correct_answer']
                actual_is_correct = True
            else:
                # Generate plausible wrong answer
                answer = self._generate_wrong_fill_answer(question['correct_answer'])
                actual_is_correct = False
        
        else:
            if will_be_correct:
                answer = question['correct_answer']
                actual_is_correct = True
            else:
                answer = "wrong"
                actual_is_correct = False
        
        return {
            'answer': answer,
            'time_taken': response_time,
            'is_correct': actual_is_correct
        }
    
    def _generate_wrong_fill_answer(self, correct_answer):
        """Generate plausible wrong answers for fill-in-the-blank questions"""
        correct = correct_answer.lower().strip()
        
        # Common wrong answers for math terms (lightweight defaults)
        wrong_mappings = {
            'addition': 'subtraction',
            'subtraction': 'addition',
            'multiplication': 'division',
            'division': 'multiplication',
            'triangle': 'square',
            'square': 'circle',
            'circle': 'triangle',
            'rectangle': 'triangle',
            'area': 'perimeter',
            'perimeter': 'area',
            'volume': 'area',
            'mean': 'median',
            'median': 'mode',
            'mode': 'mean',
            'probability': 'percentage',
            'fraction': 'decimal',
            'decimal': 'fraction'
        }
        
        return wrong_mappings.get(correct, f"{correct}_alt")
    
    def _submit_diagnostic_answer(self, question, answer_data, competency, return_json=False):
        """Submit answer to diagnostic endpoint with exact payload format.
        If return_json=True, return parsed JSON body from server.
        """
        submit_url = f"{self.base_url}/student/quiz/diagnostic/submit"
        
        # Get fresh CSRF token
        csrf_token = self._get_csrf_token()
        
        # Create payload matching the exact format for diagnostic submissions
        payload = {
            'question_id': question['question_id'],
            'answer': answer_data['answer'],
            'time_taken': answer_data['time_taken'],
            '_token': csrf_token
        }
        
        print(f"    Submitting payload: {payload}")
        
        # Submit with proper headers
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json, text/javascript, */*; q=0.01'
        }
        
        response = self.session.post(submit_url, data=payload, headers=headers)
        
        print(f"    Response status: {response.status_code}")
        if response.status_code != 200:
            print(f"    Response text: {response.text[:200]}...")
            return {} if return_json else False

        if return_json:
            try:
                return response.json()
            except Exception:
                print("    Failed to parse JSON response")
                return {}
        return True
    
    def _get_csrf_token(self):
        """Get CSRF token from current session"""
        # Try multiple approaches to get a valid CSRF token
        
        # Method 1: Get from current page meta tag
        try:
            # Get current page to extract token
            current_url = f"{self.base_url}/student/assessments"
            response = self.session.get(current_url)
            if response.status_code == 200:
                token = self._extract_csrf_token(response.text)
                if token:
                    print(f"    Got CSRF token: {token[:20]}...")
                    return token
        except Exception as e:
            print(f"    CSRF method 1 failed: {e}")
        
        # Method 2: Try to get from dashboard
        try:
            response = self.session.get(f"{self.base_url}/student/dashboard")
            if response.status_code == 200:
                token = self._extract_csrf_token(response.text)
                if token:
                    print(f"    Got CSRF token from dashboard: {token[:20]}...")
                    return token
        except Exception as e:
            print(f"    CSRF method 2 failed: {e}")
        
        # Method 3: Extract from cookies (Laravel XSRF-TOKEN)
        try:
            for cookie in self.session.cookies:
                if 'XSRF-TOKEN' in cookie.name:
                    import urllib.parse
                    token = urllib.parse.unquote(cookie.value)
                    print(f"    Got CSRF token from cookie: {token[:20]}...")
                    return token
        except Exception as e:
            print(f"    CSRF method 3 failed: {e}")
        
        print("    ⚠ No CSRF token found - using empty string")
        return ''

    def _extract_current_question_id_from_quiz_page(self, html_content):
        """Attempt to extract the current question_id from the quiz page HTML.
        Tries common patterns: data attributes, hidden inputs, inline JS.
        """
        import re

        patterns = [
            r'data-question-id="([^"]+)"',
            r'name=["\']question_id["\']\s+value=["\']([^"\']+)["\']',
            r'question_id["\']?\s*[:=]\s*["\']([^"\']+)["\']',
            r'questionId["\']?\s*[:=]\s*["\']([^"\']+)["\']',
        ]
        for pattern in patterns:
            match = re.search(pattern, html_content, re.IGNORECASE)
            if match:
                return match.group(1)
        return None

    def _normalize_category_param(self, competency):
        """Convert competency like 'number_algebra' to 'Number_Algebra' for route paths."""
        return '_'.join(word.capitalize() for word in competency.strip().split('_'))

    def _parse_assessment_options_from_html(self, html_content):
        """Extract assessmentOptions JSON array from the category page HTML."""
        import re
        try:
            # Try common patterns used by the blade template
            patterns = [
                r'assessmentOptions\s*=\s*(\[[\s\S]*?\])\s*;',
                r'let\s+assessmentOptions\s*=\s*(\[[\s\S]*?\])\s*;',
            ]
            for pattern in patterns:
                m = re.search(pattern, html_content, re.IGNORECASE)
                if m:
                    json_str = m.group(1)
                    options = json.loads(json_str)
                    try:
                        print(f"  Parsed assessmentOptions: {len(options)} found")
                    except Exception:
                        pass
                    return options
        except Exception:
            pass
        return []

    def _start_regular_assessment(self, assessment_id, category_param):
        """Start a regular assessment by calling the start-assessment endpoint."""
        url = f"{self.base_url}/student/quiz/start-assessment"
        csrf_token = self._get_csrf_token()
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json, text/javascript, */*; q=0.01',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf_token,
        }
        payload = {
            'assessment_id': assessment_id,
            'category': category_param,
        }
        resp = self.session.post(url, headers=headers, data=json.dumps(payload))
        if resp.status_code != 200:
            print(f"Failed to start assessment {assessment_id}: {resp.status_code}")
            return {}
        try:
            return resp.json()
        except Exception:
            return {}

    def _get_next_assessment_question_from_db(self, assessment_id):
        """Get the next unanswered question for a regular assessment from DB."""
        conn = self.connect_db()
        if not conn:
            return None
        try:
            cursor = conn.cursor(dictionary=True)
            cursor.execute(
                """
                SELECT q.*
                FROM assessment_questions AS aq
                JOIN questions AS q ON aq.question_id = q.question_id
                WHERE aq.assessment_id = %s AND aq.is_answered = 0
                ORDER BY aq.question_order ASC
                LIMIT 1
                """,
                (assessment_id,)
            )
            return cursor.fetchone()
        except mysql.connector.Error as e:
            print(f"Error fetching next assessment question: {e}")
            return None
        finally:
            conn.close()
    
    def auto_answer_quiz(self, competency, accuracy_rate=0.7, speed_factor=0.5):
        """Start a regular assessment from the category page, then complete it automatically."""
        category_param = self._normalize_category_param(competency)
        category_url = f"{self.base_url}/student/assessments/{category_param}"
        resp = self.session.get(category_url)
        if resp.status_code != 200:
            print(f"Failed to access category page: {resp.status_code}")
            return False
        options = self._parse_assessment_options_from_html(resp.text)
        if not options:
            print("No assessment options found on category page")
            return False
        chosen = options[0]
        assessment_id = chosen.get('assessment_id')
        if not assessment_id:
            print("Chosen assessment missing assessment_id")
            return False
        start_result = self._start_regular_assessment(assessment_id, category_param)
        if not start_result or not start_result.get('success'):
            print(f"Failed to start assessment: {start_result.get('message')}" if start_result else "Failed to start assessment")
            return False
        # Use the assessment_id returned by server (may point to an existing/resumed one)
        returned_assessment_id = start_result.get('assessment_id') or assessment_id
        # Adopt returned assessment id/session
        assessment_id = returned_assessment_id
        session_id = start_result.get('session_id')
        redirect_url = start_result.get('redirect')
        page_html = ''
        if redirect_url:
            page_resp = self.session.get(redirect_url)
            if page_resp.status_code == 200:
                page_html = page_resp.text
        
        # Try extracting first question_id directly from quiz page
        question = None
        if page_html:
            first_qid = self._extract_current_question_id_from_quiz_page(page_html)
            if first_qid:
              #  print(f"  Extracted first question_id from page: {first_qid}")
                question = self._get_question_by_id(first_qid)
            else:
                print("  No question_id found in quiz page HTML; will poll DB")
        
        # If not found yet, wait briefly for assessment to populate
        if not question:
            for attempt in range(1, 11):
                question = self._get_next_assessment_question_from_db(assessment_id)
                if question:
                    print(f"  DB poll attempt {attempt}: found question_id={question['question_id']}")
                    break
                print(f"  DB poll attempt {attempt}: no question yet, retrying...")
                time.sleep(0.5)
        if not question:
            print("No questions available yet after starting assessment")
            return False

        answered_count = 0
        while True:
            # Use DB state to get current next question
            if not question:
                question = self._get_next_assessment_question_from_db(assessment_id)
                if not question:
                    print("No more questions or failed to fetch next question")
                    break
            answered_count += 1
            print(f"Question {answered_count}: id={question['question_id']} {question['question_text'][:50]}...")
            answer_data = self._generate_smart_answer(question, accuracy_rate, speed_factor)
            submit_res = self._submit_quiz_answer(assessment_id, question, answer_data, return_json=True)
            if not submit_res or not submit_res.get('success'):
                print("  ✗ Failed to submit answer")
                return False
            print(f"  ✓ Answered: {answer_data['answer'][:30]}... | Correct: {submit_res.get('is_correct')}")
            # Accumulate stats
            if submit_res.get('is_correct'):
                correct_count = correct_count + 1 if 'correct_count' in locals() else 1
            total_time_taken = (total_time_taken + answer_data['time_taken']) if 'total_time_taken' in locals() else answer_data['time_taken']
            if submit_res.get('assessment_complete'):
                total_answered = answered_count
                avg_time = total_time_taken / total_answered if total_answered > 0 else 0
                accuracy_pct = (correct_count / total_answered * 100) if total_answered > 0 else 0
                print(f"Correct: {correct_count}/{total_answered}")
                print(f"Accuracy: {accuracy_pct:.1f}%")
                print(f"Avg response time: {avg_time:.1f}s")
                print("Assessment completed!")
                return True
            # Small delay to mimic user pacing
            time.sleep(1)
            # Reset question so we fetch the next one
            question = None
        return True
    
    def _get_assessment_questions(self, assessment_id):
        """Get questions for a regular assessment"""
        conn = self.connect_db()
        if not conn:
            return []
        
        try:
            cursor = conn.cursor(dictionary=True)
            
            # Get assessment details first
            cursor.execute("SELECT competency, difficulty_level FROM assessments WHERE assessment_id = %s", (assessment_id,))
            assessment = cursor.fetchone()
            
            if not assessment:
                return []
            
            # Get questions for this assessment
            cursor.execute("""
                SELECT * FROM questions 
                WHERE competency = %s AND difficulty_level = %s AND is_active = 1
                ORDER BY RAND() LIMIT 20
            """, (assessment['competency'], assessment['difficulty_level']))
            
            return cursor.fetchall()
        
        except mysql.connector.Error as e:
            print(f"Error fetching assessment questions: {e}")
            return []
        finally:
            conn.close()
    
    def _submit_quiz_answer(self, assessment_id, question, answer_data, return_json=False):
        """Submit answer to regular quiz endpoint. Optionally return parsed JSON."""
        submit_url = f"{self.base_url}/student/quiz/submit-regular"
        csrf_token = self._get_csrf_token()
        payload = {
            'assessment_id': assessment_id,
            'question_id': question['question_id'],
            'answer': answer_data['answer'],
            'time_taken': answer_data['time_taken'],
            '_token': csrf_token
        }
        headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json, text/javascript, */*; q=0.01'
        }
        response = self.session.post(submit_url, data=payload, headers=headers)
        if response.status_code != 200:
            try:
                pass
            except Exception:
                pass
        if return_json:
            try:
                return response.json()
            except Exception:
                return {}
        return response.status_code == 200
    
    def run_performance_test(self, username, password, num_diagnostics=5, competencies=None):
        """Run performance test with multiple diagnostics"""
        if competencies is None:
            competencies = ['number_algebra', 'measurement_geometry', 'data_probability']
        
        print(f"Starting performance test:")
        print(f"  User: {username}")
        print(f"  Diagnostics per competency: {num_diagnostics}")
        print(f"  Competencies: {competencies}")
        
        # Login
        if not self.login_student(username, password):
            return False
        
        total_tests = 0
        successful_tests = 0
        
        for competency in competencies:
            print(f"\n--- Testing {competency.upper()} ---")
            
            for i in range(num_diagnostics):
                print(f"Run {i+1}/{num_diagnostics}")
                
                # Vary student performance for realistic testing
                accuracy_rate = 0.5 + (0.4 * random.random())  # 50-90% accuracy
                speed_factor = 0.3 + (0.5 * random.random())   # 30-80% of max time
                
                total_tests += 1
                
                if self.auto_answer_diagnostic(competency, accuracy_rate, speed_factor):
                    successful_tests += 1
                    print(f"  ✓ Test completed successfully")
                else:
                    print(f"  ✗ Test failed")
                
                # Wait between tests to avoid overwhelming the system
                time.sleep(5)
        
        print(f"\nPerformance test completed:")
        print(f"  Total tests: {total_tests}")
        print(f"  Successful: {successful_tests}")
        print(f"  Success rate: {successful_tests/total_tests*100:.1f}%")
        
        return successful_tests == total_tests


def _run_diagnostic_for_user(username, password, competency, accuracy, speed, base_url):
    """Helper to run a diagnostic for a single user in its own session."""
    try:
        bot = SimpleQuestionBot(base_url=base_url)
        if not bot.login_student(username, password):
            return {
                'username': username,
                'success': False,
                'message': 'login_failed'
            }
        ok = bot.auto_answer_diagnostic(competency, accuracy, speed)
        return {
            'username': username,
            'success': bool(ok),
            'message': 'completed' if ok else 'failed'
        }
    except Exception as e:
        return {
            'username': username,
            'success': False,
            'message': f'error: {e}'
        }


def run_multi_diagnostics(usernames, password, competency, accuracy=0.7, speed=0.5, workers=3, base_url="http://127.0.0.1:8000"):
    """Run diagnostics concurrently for multiple users.

    usernames: list[str] of usernames
    password: shared password for all users
    competency: e.g. 'number_algebra'
    accuracy, speed: behavior knobs
    workers: thread pool size
    base_url: target site base
    """
    print(f"Running multi-diagnostics for {len(usernames)} users | workers: {workers}")
    results = []

    with ThreadPoolExecutor(max_workers=workers) as executor:
        futures = [
            executor.submit(_run_diagnostic_for_user, u, password, competency, accuracy, speed, base_url)
            for u in usernames
        ]
        for fut in as_completed(futures):
            res = fut.result()
            results.append(res)
            status = '✓' if res.get('success') else '✗'
            print(f" {status} {res.get('username')}: {res.get('message')}")

    # Summarize
    total = len(results)
    passed = sum(1 for r in results if r.get('success'))
    print(f"\nSummary: {passed}/{total} successful ({passed/total*100 if total else 0:.1f}%)")
    return passed == total

def main():
    """Command line interface"""
    import sys
    
    if len(sys.argv) < 2:
        print("Simple Question Bot - AralSipnayan Automation Tool")
        print("\nUsage:")
        print("  python simple_question_bot.py diagnostic USERNAME PASSWORD COMPETENCY [ACCURACY] [SPEED]")
        print("  python simple_question_bot.py quiz USERNAME PASSWORD ASSESSMENT_ID [ACCURACY] [SPEED]")
        print("  python simple_question_bot.py performance USERNAME PASSWORD [NUM_TESTS]")
        print("  python simple_question_bot.py multi USERNAMES PASSWORD COMPETENCY [NUM_RUNS] [ACCURACY] [SPEED] [WORKERS]")
        print("\nExamples:")
        print("  python simple_question_bot.py diagnostic student001 password123 number_algebra 0.8 0.4")
        print("  python simple_question_bot.py quiz student001 password123 ASSESS_001 0.7 0.6")
        print("  python simple_question_bot.py performance student001 password123 3")
        print("  python simple_question_bot.py multi studenta1,studentb1,studentc1 123 number_algebra 0.7 0.5 3")
        print("\nParameters:")
        print("  ACCURACY: 0.0-1.0 (0.7 = 70% correct answers)")
        print("  SPEED: 0.0-1.0 (0.5 = use 50% of allowed time)")
        return
    
    bot = SimpleQuestionBot()
    command = sys.argv[1]
    
    if command == 'diagnostic':
        if len(sys.argv) < 5:
            print("Usage: diagnostic USERNAME PASSWORD COMPETENCY [ACCURACY] [SPEED]")
            return
        
        username = sys.argv[2]
        password = sys.argv[3]
        competency = sys.argv[4]
        accuracy = float(sys.argv[5]) if len(sys.argv) > 5 else 0.7
        speed = float(sys.argv[6]) if len(sys.argv) > 6 else 0.5
        
        if bot.login_student(username, password):
            bot.auto_answer_diagnostic(competency, accuracy, speed)
    
    elif command == 'quiz':
        if len(sys.argv) < 5:
            print("Usage: quiz USERNAME PASSWORD ASSESSMENT_ID [ACCURACY] [SPEED]")
            return
        
        username = sys.argv[2]
        password = sys.argv[3]
        assessment_id = sys.argv[4]
        accuracy = float(sys.argv[5]) if len(sys.argv) > 5 else 0.7
        speed = float(sys.argv[6]) if len(sys.argv) > 6 else 0.5
        
        if bot.login_student(username, password):
            bot.auto_answer_quiz(assessment_id, accuracy, speed)
    
    elif command == 'performance':
        if len(sys.argv) < 4:
            print("Usage: performance USERNAME PASSWORD [NUM_TESTS]")
            return
        
        username = sys.argv[2]
        password = sys.argv[3]
        num_tests = int(sys.argv[4]) if len(sys.argv) > 4 else 2
        
        bot.run_performance_test(username, password, num_tests)
    
    elif command == 'multi':
        if len(sys.argv) < 5:
            print("Usage: multi USERNAMES PASSWORD COMPETENCY [ACCURACY] [SPEED] [WORKERS]")
            return
        usernames = [u.strip() for u in sys.argv[2].split(',') if u.strip()]
        password = sys.argv[3]
        competency = sys.argv[4]
        accuracy = float(sys.argv[5]) if len(sys.argv) > 5 else 0.7
        speed = float(sys.argv[6]) if len(sys.argv) > 6 else 0.5
        workers = int(sys.argv[7]) if len(sys.argv) > 7 else min(3, len(usernames))
        run_multi_diagnostics(usernames, password, competency, accuracy, speed, workers)
    
    else:
        print(f"Unknown command: {command}")

if __name__ == '__main__':
    main()
