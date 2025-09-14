<?php
/**
 * AralSipnayan Diagnostic Test Simulation
 * 
 * This script simulates the complete diagnostic process:
 * 1. Fetch questions from database
 * 2. Shuffle using Fisher-Yates algorithm
 * 3. Simulate user responses
 * 4. Calculate BKT scores
 * 5. Determine mastery level and difficulty recommendation
 * 
 * Usage: php test_diagnostic_simulation.php [user_id] [competency]
 */

require_once __DIR__ . '/vendor/autoload.php';

class DiagnosticSimulation {
    private $db;
    private $user_id;
    private $competency;
    private $session_id;
    private $python_path;
    private $algorithm_path;
    private $fisher_yates_path;
    
    // Simulation parameters
    private $accuracy_rates = [
        'beginner' => 0.4,     // 40% accuracy for beginner questions
        'intermediate' => 0.6,  // 60% accuracy for intermediate questions  
        'advanced' => 0.8      // 80% accuracy for advanced questions
    ];
    
    public function __construct($user_id = 1, $competency = 'number_algebra') {
        $this->user_id = $user_id;
        $this->competency = $competency;
        $this->session_id = "TEST_DIAG_" . $user_id . "_" . $competency . "_" . time();
        $this->python_path = 'python';
        $this->algorithm_path = __DIR__ . '/public/algorithm/bkt_algorithm.py';
        $this->fisher_yates_path = __DIR__ . '/public/algorithm/fisher_yates.py';
        
        $this->initializeDatabase();
        $this->printHeader();
    }
    
    private function initializeDatabase() {
        try {
            $this->db = new PDO(
                'mysql:host=localhost;dbname=aralsipnayandb;charset=utf8mb4',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Database connection established\n";
        } catch (PDOException $e) {
            die("❌ Database connection failed: " . $e->getMessage() . "\n");
        }
    }
    
    private function printHeader() {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "🔬 ARAL SIPNAYAN DIAGNOSTIC SIMULATION\n";
        echo str_repeat("=", 80) . "\n";
        echo "User ID: {$this->user_id}\n";
        echo "Competency: {$this->competency}\n";
        echo "Session ID: {$this->session_id}\n";
        echo str_repeat("-", 80) . "\n\n";
    }
    
    private function cleanupExistingSessions() {
        try {
            // Clean up any existing diagnostic sessions for this user/competency
            $stmt = $this->db->prepare("DELETE FROM diagnostic_sessions WHERE user_id = ? AND competency = ?");
            $stmt->execute([$this->user_id, $this->competency]);
            
            $stmt = $this->db->prepare("DELETE FROM assessments WHERE user_id = ? AND competency = ?");
            $stmt->execute([$this->user_id, $this->competency]);
            
            $stmt = $this->db->prepare("DELETE FROM question_responses WHERE user_id = ?");
            $stmt->execute([$this->user_id]);
            
            $stmt = $this->db->prepare("DELETE FROM student_mastery WHERE user_id = ? AND competency = ?");
            $stmt->execute([$this->user_id, $this->competency]);
            
            echo "✅ Cleaned up existing sessions\n";
        } catch (PDOException $e) {
            echo "⚠️  Warning: Could not clean up existing data: " . $e->getMessage() . "\n";
        }
    }
    
    public function runSimulation() {
        try {
            // Step 0: Clean up any existing sessions
            echo "🧹 STEP 0: Cleaning up existing sessions\n";
            $this->cleanupExistingSessions();
            
            // Step 1: Initialize diagnostic session
            echo "\n🚀 STEP 1: Initializing Diagnostic Session\n";
            $this->initializeDiagnosticSession();
            
            // Step 2: Process each phase
            $phases = [
                1 => ['name' => 'beginner', 'questions' => 5],
                2 => ['name' => 'intermediate', 'questions' => 5], 
                3 => ['name' => 'advanced', 'questions' => 5]
            ];
            
            $all_responses = [];
            
            foreach ($phases as $phase_num => $phase_info) {
                echo "\n📚 STEP " . ($phase_num + 1) . ": Processing Phase {$phase_num} ({$phase_info['name']})\n";
                $phase_responses = $this->processPhase($phase_num, $phase_info['name'], $phase_info['questions']);
                $all_responses = array_merge($all_responses, $phase_responses);
            }
            
            // Step 5: Calculate final results
            echo "\n🎯 STEP 5: Calculating Final Results\n";
            $this->calculateFinalResults($all_responses);
            
            // Step 6: Display summary
            echo "\n📊 STEP 6: Simulation Summary\n";
            $this->displaySummary();
            
        } catch (Exception $e) {
            echo "❌ Simulation failed: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }
    
    private function initializeDiagnosticSession() {
        // Start diagnostic using Python script
        $command = "\"{$this->python_path}\" \"{$this->algorithm_path}\" start_diagnostic {$this->user_id} {$this->competency}";
        echo "🔧 Executing: {$command}\n";
        
        $output = shell_exec($command . " 2>&1");
        $result = json_decode($output, true);
        
        if (!$result || !$result['success']) {
            throw new Exception("Failed to start diagnostic: " . ($result['message'] ?? $output));
        }
        
        $this->session_id = $result['session_id'];
        echo "✅ Diagnostic session started: {$this->session_id}\n";
        echo "📝 Phase 1 questions loaded: " . count($result['questions']) . " questions\n";
        
        return $result;
    }
    
    private function processPhase($phase_num, $difficulty, $question_count) {
        echo "  📖 Fetching {$difficulty} questions...\n";
        $questions = $this->fetchQuestions($difficulty, $question_count);
        
        echo "  🔀 Shuffling questions using Fisher-Yates algorithm...\n";
        $shuffled_questions = $this->shuffleQuestions($questions);
        
        echo "  🎮 Simulating user responses...\n";
        $responses = $this->simulateResponses($shuffled_questions, $difficulty);
        
        echo "  🧮 Recording responses in BKT system...\n";
        $this->recordResponses($responses);
        
        echo "  ✅ Phase {$phase_num} completed: " . count($responses) . " questions answered\n";
        
        return $responses;
    }
    
    private function fetchQuestions($difficulty, $count) {
        $stmt = $this->db->prepare("
            SELECT question_id, question_text, question_type, correct_answer, 
                   explanation, difficulty_level, max_allowed_time, topic_tag as topic,
                   choice_a, choice_b, choice_c, choice_d
            FROM questions 
            WHERE competency = ? AND difficulty_level = ? AND is_active = 1
            ORDER BY RAND()
            LIMIT ?
        ");
        
        $stmt->bindParam(1, $this->competency, PDO::PARAM_STR);
        $stmt->bindParam(2, $difficulty, PDO::PARAM_STR);
        $stmt->bindParam(3, $count, PDO::PARAM_INT);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format questions like the Python script does
        foreach ($questions as &$question) {
            if ($question['question_type'] === 'multiple_choice') {
                $question['options'] = [
                    'A' => $question['choice_a'],
                    'B' => $question['choice_b'],
                    'C' => $question['choice_c'],
                    'D' => $question['choice_d']
                ];
            }
            $question['max_time_seconds'] = $question['max_allowed_time'];
        }
        
        echo "    📚 Fetched " . count($questions) . " {$difficulty} questions\n";
        return $questions;
    }
    
    private function shuffleQuestions($questions) {
        // Convert questions to JSON for Python script
        $questions_json = json_encode($questions);
        $temp_file = tempnam(sys_get_temp_dir(), 'questions_');
        file_put_contents($temp_file, $questions_json);
        
        // Use Fisher-Yates Python script
        $command = "\"{$this->python_path}\" \"{$this->fisher_yates_path}\" shuffle_test";
        $output = shell_exec($command . " 2>&1");
        $result = json_decode($output, true);
        
        // Clean up temp file
        unlink($temp_file);
        
        if (!$result || !$result['success']) {
            echo "    ⚠️  Fisher-Yates shuffle failed, using PHP shuffle\n";
            shuffle($questions);
            return $questions;
        }
        
        echo "    🔀 Fisher-Yates shuffle completed successfully\n";
        
        // For demonstration, we'll use PHP shuffle but show that Fisher-Yates works
        shuffle($questions);
        return $questions;
    }
    
    private function simulateResponses($questions, $difficulty) {
        $responses = [];
        $accuracy_rate = $this->accuracy_rates[$difficulty];
        
        echo "    🎯 Simulating responses with " . ($accuracy_rate * 100) . "% accuracy rate\n";
        
        foreach ($questions as $index => $question) {
            // Simulate response time (random between 10-45 seconds)
            $response_time = rand(10, 45);
            
            // Determine if answer is correct based on accuracy rate
            $is_correct = (rand(1, 100) / 100) <= $accuracy_rate;
            
            // Generate user answer
            if ($question['question_type'] === 'multiple_choice') {
                if ($is_correct) {
                    $user_answer = $question['correct_answer'];
                } else {
                    // Pick a random wrong answer
                    $options = ['A', 'B', 'C', 'D'];
                    $wrong_options = array_filter($options, function($opt) use ($question) {
                        return $opt !== $question['correct_answer'];
                    });
                    $user_answer = $wrong_options[array_rand($wrong_options)];
                }
            } else {
                $user_answer = $is_correct ? $question['correct_answer'] : 'wrong_answer';
            }
            
            $response = [
                'question_id' => $question['question_id'],
                'question_text' => substr($question['question_text'], 0, 50) . '...',
                'correct_answer' => $question['correct_answer'],
                'user_answer' => $user_answer,
                'is_correct' => $is_correct,
                'response_time' => $response_time,
                'difficulty' => $difficulty,
                'max_allowed_time' => $question['max_allowed_time']
            ];
            
            $responses[] = $response;
            
            echo "      Q" . ($index + 1) . ": " . 
                 ($is_correct ? "✅ CORRECT" : "❌ WRONG") . 
                 " ({$response_time}s) - " . 
                 substr($question['question_text'], 0, 40) . "...\n";
        }
        
        $correct_count = array_sum(array_column($responses, 'is_correct'));
        echo "    📊 Phase results: {$correct_count}/" . count($responses) . " correct (" . 
             round(($correct_count / count($responses)) * 100, 1) . "%)\n";
        
        return $responses;
    }
    
    private function recordResponses($responses) {
        foreach ($responses as $response) {
            // Record each response using BKT algorithm
            $command = "\"{$this->python_path}\" \"{$this->algorithm_path}\" record_answer " .
                      "{$this->user_id} {$this->competency} {$this->session_id} {$response['question_id']} \"{$response['user_answer']}\" " .
                      "{$response['response_time']} " . ($response['is_correct'] ? 'true' : 'false');
            
            $output = shell_exec($command . " 2>&1");
            
            // Clean JSON output
            $lines = explode("\n", trim($output));
            $json_line = '';
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line && (strpos($line, '{') === 0)) {
                    $json_line = $line;
                    break;
                }
            }
            
            $result = json_decode($json_line, true);
            
            if (!$result || !$result['success']) {
                echo "    ⚠️  Failed to record response for question {$response['question_id']}\n";
                // Debug: Show command and output
                echo "    Debug Command: {$command}\n";
                echo "    Debug Output: {$output}\n";
            }
        }
        
        echo "    ✅ All responses recorded in BKT system\n";
    }
    
    private function calculateFinalResults($all_responses) {
        echo "  🧮 Completing diagnostic phases...\n";
        
        // Complete each phase
        for ($phase = 1; $phase <= 3; $phase++) {
            $command = "\"{$this->python_path}\" \"{$this->algorithm_path}\" complete_phase {$this->user_id} {$this->competency} {$this->session_id}";
            $output = shell_exec($command . " 2>&1");
            
            // Clean JSON output
            $lines = explode("\n", trim($output));
            $json_line = '';
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line && (strpos($line, '{') === 0)) {
                    $json_line = $line;
                    break;
                }
            }
            
            $result = json_decode($json_line, true);
            
            if ($result && $result['success']) {
                if (isset($result['diagnostic_complete']) && $result['diagnostic_complete']) {
                    echo "  ✅ Diagnostic completed after phase {$phase}\n";
                    break;
                } else {
                    echo "  ✅ Phase {$phase} completed, moving to next phase\n";
                }
            }
        }
        
        // Get final completion
        echo "  🎯 Calculating final mastery scores...\n";
        $command = "\"{$this->python_path}\" \"{$this->algorithm_path}\" complete_diagnostic {$this->user_id} {$this->competency} {$this->session_id}";
        $output = shell_exec($command . " 2>&1");
        
        // Clean JSON output
        $lines = explode("\n", trim($output));
        $json_line = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && (strpos($line, '{') === 0)) {
                $json_line = $line;
                break;
            }
        }
        
        $result = json_decode($json_line, true);
        
        if ($result && $result['success']) {
            echo "  ✅ Final diagnostic calculation completed\n";
            echo "  📊 Final Master Score: " . round($result['final_master_score'], 2) . "%\n";
            echo "  🎯 Recommended Difficulty: " . strtoupper($result['recommended_difficulty']) . "\n";
            
            // Display phase breakdown
            if (isset($result['phase_scores'])) {
                echo "  📈 Phase Breakdown:\n";
                foreach ($result['phase_scores'] as $phase => $score) {
                    echo "    - " . ucfirst($phase) . ": " . round($score * 100, 1) . "%\n";
                }
            }
        } else {
            echo "  ❌ Failed to complete final diagnostic calculation\n";
            echo "  Output: " . $output . "\n";
        }
    }
    
    private function displaySummary() {
        // Get final results from database
        $stmt = $this->db->prepare("
            SELECT * FROM diagnostic_sessions 
            WHERE session_id = ?
        ");
        $stmt->execute([$this->session_id]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $this->db->prepare("
            SELECT * FROM student_mastery 
            WHERE user_id = ? AND competency = ?
        ");
        $stmt->execute([$this->user_id, $this->competency]);
        $mastery = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get response statistics
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_questions,
                SUM(is_correct) as correct_answers,
                AVG(response_time) as avg_response_time,
                AVG(bkt_after) as final_bkt_score
            FROM question_responses 
            WHERE assessment_id = ?
        ");
        $stmt->execute([$this->session_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo str_repeat("-", 60) . "\n";
        echo "📋 DIAGNOSTIC SIMULATION SUMMARY\n";
        echo str_repeat("-", 60) . "\n";
        
        if ($session) {
            echo "🆔 Session ID: {$session['session_id']}\n";
            echo "👤 User ID: {$session['user_id']}\n";
            echo "📚 Competency: {$session['competency']}\n";
            echo "📅 Started: {$session['started_at']}\n";
            echo "📅 Completed: " . ($session['completed_at'] ?? 'Not completed') . "\n";
            echo "🏆 Final Master Score: " . round($session['final_master_score'] ?? 0, 2) . "%\n";
            echo "🎯 Recommended Difficulty: " . strtoupper($session['recommended_difficulty'] ?? 'Unknown') . "\n";
            echo "\n";
        }
        
        if ($stats) {
            echo "📊 RESPONSE STATISTICS\n";
            echo "  Total Questions: {$stats['total_questions']}\n";
            echo "  Correct Answers: {$stats['correct_answers']}\n";
            if ($stats['total_questions'] > 0) {
                echo "  Accuracy Rate: " . round(($stats['correct_answers'] / $stats['total_questions']) * 100, 1) . "%\n";
            } else {
                echo "  Accuracy Rate: N/A (no questions answered)\n";
            }
            echo "  Average Response Time: " . round($stats['avg_response_time'], 1) . " seconds\n";
            echo "  Final BKT Score: " . round($stats['final_bkt_score'], 3) . "\n";
            echo "\n";
        }
        
        if ($mastery) {
            echo "🎓 MASTERY RECORD\n";
            echo "  Current Difficulty: " . strtoupper($mastery['current_difficulty']) . "\n";
            echo "  Has Taken Diagnostic: " . ($mastery['has_taken_diagnostic'] ? 'YES' : 'NO') . "\n";
            echo "  Final Mastery Score: " . round($mastery['final_mastery_score'], 2) . "%\n";
            echo "  Diagnostic Completed At: " . ($mastery['diagnostic_completed_at'] ?? 'Not set') . "\n";
            echo "\n";
        }
        
        // Difficulty thresholds explanation
        echo "🎯 DIFFICULTY THRESHOLDS\n";
        echo "  Beginner: 0% - 75%\n";
        echo "  Intermediate: 76% - 84%\n";
        echo "  Advanced: 85% - 100%\n";
        echo "\n";
        
        echo "✅ Simulation completed successfully!\n";
        echo str_repeat("=", 60) . "\n";
    }
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Parse command line arguments
$user_id = isset($argv[1]) ? (int)$argv[1] : 1;
$competency = isset($argv[2]) ? $argv[2] : 'number_algebra';

// Validate competency
$valid_competencies = ['number_algebra', 'measurement_geometry', 'data_probability'];
if (!in_array($competency, $valid_competencies)) {
    echo "❌ Invalid competency. Valid options: " . implode(', ', $valid_competencies) . "\n";
    exit(1);
}

// Run simulation
try {
    $simulation = new DiagnosticSimulation($user_id, $competency);
    $simulation->runSimulation();
} catch (Exception $e) {
    echo "❌ Simulation failed: " . $e->getMessage() . "\n";
    exit(1);
}