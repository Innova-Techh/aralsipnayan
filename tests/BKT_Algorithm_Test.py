#!/usr/bin/env python3
"""
Comprehensive Test Suite for AralSipnayan BKT Algorithm
Tests all aspects of the BKT implementation including calculations, accuracy, time response, and results
"""

import unittest
import json
import sys
import os
import time
import random
from decimal import Decimal, ROUND_HALF_UP
from unittest.mock import Mock, patch, MagicMock

# Add the algorithm directory to path for imports
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'public', 'algorithm'))

try:
    from bkt_algorithm import BKTAlgorithm
except ImportError:
    print("Error: Could not import BKTAlgorithm. Please ensure the bkt_algorithm.py file is in the correct location.")
    sys.exit(1)

class TestBKTAlgorithm(unittest.TestCase):
    """Test suite for BKT Algorithm functionality"""

    def setUp(self):
        """Set up test environment"""
        self.bkt = BKTAlgorithm()
        self.mock_db_config = {
            'host': 'localhost',
            'user': 'test_user',
            'password': 'test_pass',
            'database': 'test_db',
            'charset': 'utf8mb4'
        }

        # Test data for calculations
        self.test_user_id = 999
        self.test_competency = 'number_algebra'
        self.test_session_id = 'TEST_DIAG_999_number_algebra_' + str(int(time.time()))

        # Sample question data
        self.sample_question = {
            'question_id': 'Q_TEST_001',
            'question_text': 'What is 2 + 2?',
            'correct_answer': '4',
            'difficulty_level': 'beginner',
            'max_allowed_time': 30
        }

    def tearDown(self):
        """Clean up after tests"""
        pass

class TestBKTCalculations(TestBKTAlgorithm):
    """Test BKT calculation functions"""

    def test_time_score_calculation(self):
        """Test time score calculation with various scenarios"""

        # Test fast correct answer (normalized_time <= 0.5)
        time_score = self.bkt.calculate_time_score(15, 30, True)  # 50% of max time, correct
        self.assertEqual(time_score, 1.0, "Fast correct answer should score 1.0")

        # Test medium speed correct answer (0.5 < normalized_time <= 0.8)
        time_score = self.bkt.calculate_time_score(20, 30, True)  # 67% of max time, correct
        self.assertEqual(time_score, 0.8, "Medium speed correct answer should score 0.8")

        # Test slow correct answer (normalized_time > 0.8)
        time_score = self.bkt.calculate_time_score(25, 30, True)  # 83% of max time, correct
        self.assertEqual(time_score, 0.6, "Slow correct answer should score 0.6")

        # Test fast incorrect answer
        time_score = self.bkt.calculate_time_score(10, 30, False)  # 33% of max time, incorrect
        self.assertEqual(time_score, 0.2, "Fast incorrect answer should score 0.2")

        # Test slow incorrect answer
        time_score = self.bkt.calculate_time_score(25, 30, False)  # 83% of max time, incorrect
        self.assertEqual(time_score, 0.1, "Slow incorrect answer should score 0.1")

        # Test timeout scenario
        time_score = self.bkt.calculate_time_score(35, 30, True)  # Over max time
        self.assertEqual(time_score, 0.0, "Timeout should score 0.0")

    def test_bkt_update_correct_answer(self):
        """Test BKT update calculation for correct answers"""

        prior_prob = 0.3
        is_correct = True
        params = self.bkt.default_params
        time_factor = 0.8
        difficulty_factor = 1.0

        new_bkt = self.bkt.calculate_bkt_update(
            prior_prob, is_correct, params, time_factor, difficulty_factor
        )

        # BKT should increase for correct answers
        self.assertGreater(new_bkt, prior_prob, "BKT should increase for correct answers")
        self.assertLessEqual(new_bkt, 1.0, "BKT should not exceed 1.0")
        self.assertGreaterEqual(new_bkt, 0.0, "BKT should not be negative")

    def test_bkt_update_incorrect_answer(self):
        """Test BKT update calculation for incorrect answers"""

        prior_prob = 0.7
        is_correct = False
        params = self.bkt.default_params
        time_factor = 0.6
        difficulty_factor = 1.0

        new_bkt = self.bkt.calculate_bkt_update(
            prior_prob, is_correct, params, time_factor, difficulty_factor
        )

        # BKT should generally decrease for incorrect answers
        self.assertLess(new_bkt, prior_prob, "BKT should decrease for incorrect answers")
        self.assertLessEqual(new_bkt, 1.0, "BKT should not exceed 1.0")
        self.assertGreaterEqual(new_bkt, 0.0, "BKT should not be negative")

    def test_mastery_score_calculation(self):
        """Test mastery score calculation with weighted formula"""

        accuracy_score = 0.8  # 80% accuracy
        bkt_score = 0.6       # 60% BKT score

        result = self.bkt.calculate_mastery_score(accuracy_score, bkt_score)

        # Expected calculation: (0.55 * 0.8 + 0.45 * 0.6) * 100 = 71%
        expected_score = (0.55 * accuracy_score + 0.45 * bkt_score) * 100

        self.assertEqual(result['final_score'], expected_score, "Mastery score calculation incorrect")
        self.assertEqual(result['accuracy_component'], 0.55 * accuracy_score, "Accuracy component incorrect")
        self.assertEqual(result['bkt_component'], 0.45 * bkt_score, "BKT component incorrect")

    def test_difficulty_level_determination(self):
        """Test difficulty level classification based on mastery score"""

        # Test beginner level (75 and below)
        self.assertEqual(self.bkt.determine_difficulty_level(65), 'beginner')
        self.assertEqual(self.bkt.determine_difficulty_level(75), 'beginner')

        # Test intermediate level (76 to 84)
        self.assertEqual(self.bkt.determine_difficulty_level(76), 'intermediate')
        self.assertEqual(self.bkt.determine_difficulty_level(80), 'intermediate')
        self.assertEqual(self.bkt.determine_difficulty_level(84), 'intermediate')

        # Test advanced level (85 to 100)
        self.assertEqual(self.bkt.determine_difficulty_level(85), 'advanced')
        self.assertEqual(self.bkt.determine_difficulty_level(95), 'advanced')

class TestDiagnosticFlow(TestBKTAlgorithm):
    """Test the complete diagnostic assessment flow"""

    @patch('mysql.connector.connect')
    def test_start_diagnostic_new_session(self, mock_connect):
        """Test starting a new diagnostic session"""

        # Mock database connection and cursor
        mock_conn = Mock()
        mock_cursor = Mock()
        mock_connect.return_value = mock_conn
        mock_conn.cursor.return_value = mock_cursor

        # Mock database queries
        mock_cursor.fetchone.side_effect = [
            None,  # No existing mastery record
            None,  # No existing session
            None   # No recent in-progress session
        ]
        mock_cursor.execute.return_value = None

        # Mock questions data
        mock_questions = [
            {
                'question_id': 'Q001',
                'question_text': 'Test question 1',
                'question_type': 'multiple_choice',
                'correct_answer': 'A',
                'choice_a': 'Option A',
                'choice_b': 'Option B',
                'choice_c': 'Option C',
                'choice_d': 'Option D',
                'max_allowed_time': 30,
                'difficulty_level': 'beginner'
            }
        ]

        with patch.object(self.bkt, 'get_diagnostic_questions', return_value=mock_questions):
            result = self.bkt.start_diagnostic(self.test_user_id, self.test_competency)

            self.assertTrue(result['success'], "Diagnostic start should succeed")
            self.assertIn('session_id', result, "Should return session_id")
            self.assertEqual(result['phase'], 1, "Should start with phase 1")
            self.assertEqual(result['phase_name'], 'beginner', "Phase 1 should be beginner")
            self.assertEqual(len(result['questions']), 1, "Should return questions")

    @patch('mysql.connector.connect')
    def test_record_diagnostic_answer(self, mock_connect):
        """Test recording a diagnostic answer with BKT calculation"""

        # Mock database connection
        mock_conn = Mock()
        mock_cursor = Mock()
        mock_connect.return_value = mock_conn
        mock_conn.cursor.return_value = mock_cursor

        # Mock session data
        mock_cursor.fetchone.side_effect = [
            {'user_id': self.test_user_id, 'competency': self.test_competency, 'current_phase': 1},  # Session info
            {'max_allowed_time': 30, 'difficulty_level': 'beginner'},  # Question info
            {  # Mastery info
                'bkt_score': 0.3,
                'prior_knowledge': 0.1,
                'learn_rate': 0.3,
                'slip_rate': 0.1,
                'guess_rate': 0.25
            },
            {'total_time_score': 0.8, 'total_responses': 1}  # Time data
        ]

        result = self.bkt.record_diagnostic_answer(
            self.test_session_id,
            self.sample_question['question_id'],
            self.sample_question['correct_answer'],
            25,  # response_time
            True  # is_correct
        )

        self.assertTrue(result['success'], "Answer recording should succeed")
        self.assertIn('bkt_before', result, "Should return BKT before")
        self.assertIn('bkt_after', result, "Should return BKT after")
        self.assertIn('time_score', result, "Should return time score")
        self.assertIn('ftime_factor', result, "Should return ftime factor")

class TestAccuracyCalculations(TestBKTAlgorithm):
    """Test accuracy calculation scenarios"""

    def test_accuracy_scenarios(self):
        """Test various accuracy calculation scenarios"""

        test_scenarios = [
            # (correct_answers, total_questions, expected_accuracy)
            (10, 10, 1.0),    # Perfect score
            (8, 10, 0.8),     # 80% accuracy
            (5, 10, 0.5),     # 50% accuracy
            (0, 10, 0.0),     # No correct answers
            (3, 5, 0.6),      # 60% accuracy with fewer questions
        ]

        for correct, total, expected in test_scenarios:
            accuracy = correct / total if total > 0 else 0
            self.assertEqual(accuracy, expected,
                           f"Accuracy calculation failed for {correct}/{total}")

class TestTimeResponseAnalysis(TestBKTAlgorithm):
    """Test time response analysis and scoring"""

    def test_time_response_categories(self):
        """Test time response categorization"""

        max_time = 30

        # Fast responses (0-50% of max time)
        fast_times = [5, 10, 15]
        for response_time in fast_times:
            normalized = response_time / max_time
            self.assertLessEqual(normalized, 0.5, f"Time {response_time}s should be categorized as fast")

        # Medium responses (50-80% of max time)
        medium_times = [16, 20, 24]
        for response_time in medium_times:
            normalized = response_time / max_time
            self.assertTrue(0.5 < normalized <= 0.8, f"Time {response_time}s should be categorized as medium")

        # Slow responses (>80% of max time)
        slow_times = [25, 28, 29]
        for response_time in slow_times:
            normalized = response_time / max_time
            self.assertGreater(normalized, 0.8, f"Time {response_time}s should be categorized as slow")

    def test_time_factor_calculation(self):
        """Test cumulative time factor calculation"""

        # Simulate multiple responses with different time scores
        time_scores = [1.0, 0.8, 0.6, 0.2, 1.0]  # Mix of fast/slow, correct/incorrect
        total_responses = len(time_scores)

        # Calculate average time factor (ftime)
        ftime_factor = sum(time_scores) / total_responses
        expected_ftime = 0.72  # (1.0 + 0.8 + 0.6 + 0.2 + 1.0) / 5

        self.assertAlmostEqual(ftime_factor, expected_ftime, places=2,
                              msg="Time factor calculation should be accurate")

class TestDifficultyprogression(TestBKTAlgorithm):
    """Test difficulty progression logic"""

    def test_difficulty_factors(self):
        """Test difficulty factor application"""

        factors = self.bkt.difficulty_factors

        self.assertEqual(factors['beginner'], 0.8, "Beginner difficulty factor should be 0.8")
        self.assertEqual(factors['intermediate'], 1.0, "Intermediate difficulty factor should be 1.0")
        self.assertEqual(factors['advanced'], 1.2, "Advanced difficulty factor should be 1.2")

    def test_progression_thresholds(self):
        """Test difficulty progression thresholds"""

        thresholds = self.bkt.difficulty_thresholds

        self.assertEqual(thresholds['beginner'], 75, "Beginner threshold should be 75")
        self.assertEqual(thresholds['intermediate'], 84, "Intermediate threshold should be 84")
        self.assertEqual(thresholds['advanced'], 100, "Advanced threshold should be 100")

class TestCompleteAssessmentFlow(TestBKTAlgorithm):
    """Test complete assessment scenarios end-to-end"""

    def test_diagnostic_simulation(self):
        """Simulate a complete diagnostic assessment"""

        # Simulate Phase 1 (Beginner) - 5 questions
        phase1_responses = [
            {'is_correct': True, 'response_time': 15, 'max_time': 30},
            {'is_correct': True, 'response_time': 20, 'max_time': 30},
            {'is_correct': False, 'response_time': 25, 'max_time': 30},
            {'is_correct': True, 'response_time': 18, 'max_time': 30},
            {'is_correct': True, 'response_time': 12, 'max_time': 30},
        ]

        # Calculate phase 1 metrics
        phase1_correct = sum(1 for r in phase1_responses if r['is_correct'])
        phase1_accuracy = phase1_correct / len(phase1_responses)

        # Calculate time scores for phase 1
        phase1_time_scores = []
        for response in phase1_responses:
            time_score = self.bkt.calculate_time_score(
                response['response_time'],
                response['max_time'],
                response['is_correct']
            )
            phase1_time_scores.append(time_score)

        phase1_time_factor = sum(phase1_time_scores) / len(phase1_time_scores)

        # Simulate BKT progression through phase 1
        current_bkt = self.bkt.default_params['prior_knowledge']
        for i, response in enumerate(phase1_responses):
            time_score = phase1_time_scores[i]
            current_bkt = self.bkt.calculate_bkt_update(
                current_bkt,
                response['is_correct'],
                self.bkt.default_params,
                time_score,
                self.bkt.difficulty_factors['beginner']
            )

        # Verify reasonable BKT progression
        self.assertGreater(current_bkt, self.bkt.default_params['prior_knowledge'],
                          "BKT should increase with mostly correct answers")

        # Calculate final mastery score
        mastery_result = self.bkt.calculate_mastery_score(phase1_accuracy, current_bkt)
        final_score = mastery_result['final_score']

        # Verify mastery calculation
        self.assertGreater(final_score, 0, "Final mastery score should be positive")
        self.assertLessEqual(final_score, 100, "Final mastery score should not exceed 100")

        # Test difficulty determination
        recommended_difficulty = self.bkt.determine_difficulty_level(final_score)
        self.assertIn(recommended_difficulty, ['beginner', 'intermediate', 'advanced'],
                     "Recommended difficulty should be valid")

    def test_regular_assessment_simulation(self):
        """Simulate a regular assessment scenario"""

        # Simulate a 15-question assessment at intermediate level
        assessment_responses = []
        current_bkt = 0.5  # Starting BKT for intermediate level

        for i in range(15):
            # Simulate varying performance
            is_correct = random.choice([True, True, True, False])  # 75% accuracy
            response_time = random.randint(10, 40)
            max_time = 45  # Intermediate level max time

            response = {
                'is_correct': is_correct,
                'response_time': response_time,
                'max_time': max_time
            }
            assessment_responses.append(response)

            # Calculate time score and update BKT
            time_score = self.bkt.calculate_time_score(response_time, max_time, is_correct)
            current_bkt = self.bkt.calculate_bkt_update(
                current_bkt,
                is_correct,
                self.bkt.default_params,
                time_score,
                self.bkt.difficulty_factors['intermediate']
            )

        # Calculate final metrics
        total_correct = sum(1 for r in assessment_responses if r['is_correct'])
        final_accuracy = total_correct / len(assessment_responses)

        # Calculate mastery score
        mastery_result = self.bkt.calculate_mastery_score(final_accuracy, current_bkt)

        # Verify results are within expected ranges
        self.assertGreaterEqual(final_accuracy, 0.0, "Accuracy should be non-negative")
        self.assertLessEqual(final_accuracy, 1.0, "Accuracy should not exceed 1.0")
        self.assertGreaterEqual(current_bkt, 0.0, "BKT should be non-negative")
        self.assertLessEqual(current_bkt, 1.0, "BKT should not exceed 1.0")
        self.assertGreater(mastery_result['final_score'], 0, "Final score should be positive")

class TestErrorHandling(TestBKTAlgorithm):
    """Test error handling and edge cases"""

    def test_zero_division_handling(self):
        """Test handling of zero division scenarios"""

        # Create scenario where denominator could be zero
        # For correct answers: denominator = P_L * (1 - P_S) + (1 - P_L) * P_G
        # For this to be zero, we need P_L = 1.0 and P_G = 0.0
        zero_guess_params = {
            'prior_knowledge': 0.1,
            'learn_rate': 0.3,
            'slip_rate': 0.1,
            'guess_rate': 0.0  # Set guess rate to 0
        }

        result = self.bkt.calculate_bkt_update(
            1.0,  # prior_prob = 1.0 (perfect knowledge)
            True,  # is_correct
            zero_guess_params,
            1.0,  # time_factor
            1.0   # difficulty_factor
        )

        # Should return the prior probability when denominator is zero
        # With P_L=1.0, P_S=0.1, P_G=0.0: denominator = 1.0*(1-0.1) + (1-1.0)*0.0 = 0.9 + 0 = 0.9
        # This doesn't create zero, let me try incorrect answer scenario

        # For incorrect answers: denominator = P_L * P_S + (1 - P_L) * (1 - P_G) * time_factor * difficulty_factor
        # For this to be zero: P_L = 0, P_S = 0, P_G = 1.0, time_factor = 0, difficulty_factor = 0
        zero_scenario_params = {
            'prior_knowledge': 0.1,
            'learn_rate': 0.3,
            'slip_rate': 0.0,  # Set slip rate to 0
            'guess_rate': 1.0  # Set guess rate to 1.0
        }

        result = self.bkt.calculate_bkt_update(
            0.0,  # prior_prob = 0.0
            False,  # is_correct = False (incorrect)
            zero_scenario_params,
            0.0,  # time_factor = 0
            0.0   # difficulty_factor = 0
        )

        # With P_L=0.0, P_S=0.0, P_G=1.0, time_factor=0, difficulty_factor=0:
        # denominator = 0.0*0.0 + (1-0.0)*(1-1.0)*0.0*0.0 = 0.0 + 1.0*0.0*0.0*0.0 = 0.0
        # Should return the prior probability when denominator is zero
        self.assertEqual(result, 0.0, "Should handle zero denominator gracefully")

    def test_boundary_values(self):
        """Test boundary value handling"""

        # Test with extreme values
        extreme_time_score = self.bkt.calculate_time_score(0, 30, True)  # Zero response time
        self.assertGreaterEqual(extreme_time_score, 0.0, "Should handle zero response time")

        # Test with very high BKT values
        high_bkt = self.bkt.calculate_bkt_update(
            0.99,  # Very high prior
            False,  # Incorrect answer
            self.bkt.default_params,
            1.0,
            1.0
        )
        self.assertLessEqual(high_bkt, 1.0, "BKT should be clamped to 1.0")
        self.assertGreaterEqual(high_bkt, 0.0, "BKT should be clamped to 0.0")

class TestResultsValidation(TestBKTAlgorithm):
    """Test results validation and output format"""

    def test_result_structure(self):
        """Test that results have the expected structure"""

        # Test mastery score result structure
        mastery_result = self.bkt.calculate_mastery_score(0.8, 0.6)

        required_keys = ['final_score', 'accuracy_component', 'bkt_component']
        for key in required_keys:
            self.assertIn(key, mastery_result, f"Result should contain {key}")

        # Test data types
        self.assertIsInstance(mastery_result['final_score'], (int, float),
                             "Final score should be numeric")
        self.assertIsInstance(mastery_result['accuracy_component'], (int, float),
                             "Accuracy component should be numeric")
        self.assertIsInstance(mastery_result['bkt_component'], (int, float),
                             "BKT component should be numeric")

def run_comprehensive_test():
    """Run comprehensive test suite and generate report"""

    print("="*80)
    print("AralSipnayan BKT Algorithm - Comprehensive Test Suite")
    print("="*80)

    # Create test suite
    test_classes = [
        TestBKTCalculations,
        TestDiagnosticFlow,
        TestAccuracyCalculations,
        TestTimeResponseAnalysis,
        TestDifficultyprogression,
        TestCompleteAssessmentFlow,
        TestErrorHandling,
        TestResultsValidation
    ]

    total_tests = 0
    total_failures = 0
    total_errors = 0

    for test_class in test_classes:
        print(f"\nRunning {test_class.__name__}...")
        print("-" * 50)

        suite = unittest.TestLoader().loadTestsFromTestCase(test_class)
        runner = unittest.TextTestRunner(verbosity=2)
        result = runner.run(suite)

        total_tests += result.testsRun
        total_failures += len(result.failures)
        total_errors += len(result.errors)

        if result.failures:
            print(f"\nFAILURES in {test_class.__name__}:")
            for test, traceback in result.failures:
                print(f"  - {test}: {traceback}")

        if result.errors:
            print(f"\nERRORS in {test_class.__name__}:")
            for test, traceback in result.errors:
                print(f"  - {test}: {traceback}")

    # Generate summary report
    print("\n" + "="*80)
    print("TEST SUMMARY REPORT")
    print("="*80)
    print(f"Total Tests Run: {total_tests}")
    print(f"Successes: {total_tests - total_failures - total_errors}")
    print(f"Failures: {total_failures}")
    print(f"Errors: {total_errors}")

    success_rate = ((total_tests - total_failures - total_errors) / total_tests * 100) if total_tests > 0 else 0
    print(f"Success Rate: {success_rate:.1f}%")

    if total_failures == 0 and total_errors == 0:
        print("\n[SUCCESS] ALL TESTS PASSED! BKT Algorithm implementation is working correctly.")
    else:
        print(f"\n[FAILURE] {total_failures + total_errors} tests failed. Please review the implementation.")

    print("="*80)

    return total_failures + total_errors == 0

if __name__ == '__main__':
    """Main execution for running tests"""

    try:
        # Run the comprehensive test suite
        success = run_comprehensive_test()

        # Exit with appropriate code
        sys.exit(0 if success else 1)

    except ImportError as e:
        print(f"Import Error: {e}")
        print("Please ensure all required files are in the correct locations.")
        sys.exit(1)
    except Exception as e:
        print(f"Unexpected error: {e}")
        sys.exit(1)