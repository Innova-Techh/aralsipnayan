<?php
/**
 * Individual Algorithm Testing Script
 * 
 * Test individual components of the diagnostic system:
 * - Fisher-Yates shuffling
 * - BKT calculations  
 * - Question fetching
 * - Mastery scoring
 */

require_once __DIR__ . '/vendor/autoload.php';

class AlgorithmTester {
    private $db;
    private $python_path = 'python';
    private $algorithm_path;
    private $fisher_yates_path;
    
    public function __construct() {
        $this->algorithm_path = __DIR__ . '/public/algorithm/bkt_algorithm.py';
        $this->fisher_yates_path = __DIR__ . '/public/algorithm/fisher_yates.py';
        
        try {
            $this->db = new PDO(
                'mysql:host=localhost;dbname=aralsipnayandb;charset=utf8mb4',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Database connected\n\n";
        } catch (PDOException $e) {
            die("❌ Database connection failed: " . $e->getMessage() . "\n");
        }
    }
    
    public function testFisherYatesShuffle() {
        echo "🔀 TESTING FISHER-YATES SHUFFLE ALGORITHM\n";
        echo str_repeat("-", 50) . "\n";
        
        // Test with sample data
        $command = "\"{$this->python_path}\" \"{$this->fisher_yates_path}\" shuffle_test";
        echo "Command: {$command}\n";
        
        $output = shell_exec($command . " 2>&1");
        echo "Raw Output: {$output}\n";
        
        // Clean the output - take only the first JSON line
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
            echo "✅ Fisher-Yates shuffle successful!\n";
            echo "📊 Shuffled items:\n";
            foreach ($result['shuffled'] as $item) {
                echo "  - Order {$item['question_order']}: {$item['name']} (ID: {$item['id']})\n";
            }
        } else {
            echo "❌ Fisher-Yates shuffle failed\n";
            echo "Error: " . ($result['message'] ?? 'Unknown error') . "\n";
            echo "JSON Line: {$json_line}\n";
        }
        echo "\n";
    }
    
    public function testQuestionFetching($competency = 'number_algebra', $difficulty = 'beginner') {
        echo "📚 TESTING QUESTION FETCHING\n";
        echo str_repeat("-", 50) . "\n";
        echo "Competency: {$competency}\n";
        echo "Difficulty: {$difficulty}\n\n";
        
        $stmt = $this->db->prepare("
            SELECT question_id, question_text, question_type, correct_answer, 
                   difficulty_level, max_allowed_time, topic_tag
            FROM questions 
            WHERE competency = ? AND difficulty_level = ? AND is_active = 1
            LIMIT 5
        ");
        
        $stmt->execute([$competency, $difficulty]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "📊 Found " . count($questions) . " questions:\n";
        foreach ($questions as $i => $q) {
            echo "  " . ($i + 1) . ". [{$q['question_id']}] " . substr($q['question_text'], 0, 60) . "...\n";
            echo "     Type: {$q['question_type']} | Answer: {$q['correct_answer']} | Time: {$q['max_allowed_time']}s\n";
        }
        echo "\n";
        
        return $questions;
    }
    
    public function testBKTCalculation($user_id = 1, $competency = 'number_algebra') {
        echo "🧮 TESTING BKT ALGORITHM\n";
        echo str_repeat("-", 50) . "\n";
        
        // Test starting diagnostic
        echo "1. Starting diagnostic session...\n";
        $command = "\"{$this->python_path}\" \"{$this->algorithm_path}\" start_diagnostic {$user_id} {$competency}";
        echo "Command: {$command}\n";
        
        $output = shell_exec($command . " 2>&1");
        echo "Raw Output: {$output}\n";
        
        // Clean the output - take only the first JSON line
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
            echo "✅ Diagnostic started successfully!\n";
            echo "Session ID: {$result['session_id']}\n";
            echo "Phase: {$result['phase']} ({$result['phase_name']})\n";
            echo "Questions loaded: " . count($result['questions']) . "\n\n";
            
            // Test recording an answer
            echo "2. Recording a sample answer...\n";
            $session_id = $result['session_id'];
            $question_id = $result['questions'][0]['question_id'];
            
            $record_command = "\"{$this->python_path}\" \"{$this->algorithm_path}\" record_answer " .
                            "{$user_id} {$competency} {$session_id} {$question_id} \"A\" 25 true";
            echo "Command: {$record_command}\n";
            
            $record_output = shell_exec($record_command . " 2>&1");
            echo "Record Output: {$record_output}\n";
            
            // Clean the output - take only the first JSON line
            $record_lines = explode("\n", trim($record_output));
            $record_json_line = '';
            foreach ($record_lines as $line) {
                $line = trim($line);
                if ($line && (strpos($line, '{') === 0)) {
                    $record_json_line = $line;
                    break;
                }
            }
            
            $record_result = json_decode($record_json_line, true);
            
            if ($record_result && $record_result['success']) {
                echo "✅ Answer recorded successfully!\n";
            } else {
                echo "❌ Failed to record answer\n";
            }
            
        } else {
            echo "❌ Failed to start diagnostic\n";
            echo "Error: " . ($result['message'] ?? 'Unknown error') . "\n";
        }
        echo "\n";
    }
    
    public function testMasteryScoring() {
        echo "🎯 TESTING MASTERY SCORING THRESHOLDS\n";
        echo str_repeat("-", 50) . "\n";
        
        $test_scores = [65, 75, 80, 85, 90, 95];
        
        foreach ($test_scores as $score) {
            $difficulty = $this->determineDifficulty($score);
            echo "Score: {$score}% → Difficulty: " . strtoupper($difficulty) . "\n";
        }
        
        echo "\nThreshold Rules:\n";
        echo "- Beginner: 0% - 75%\n";
        echo "- Intermediate: 76% - 84%\n";
        echo "- Advanced: 85% - 100%\n";
        echo "\n";
    }
    
    private function determineDifficulty($score) {
        if ($score <= 75) {
            return 'beginner';
        } elseif ($score > 75 && $score <= 84) {
            return 'intermediate';
        } else {
            return 'advanced';
        }
    }
    
    public function testDatabaseStructure() {
        echo "🗄️ TESTING DATABASE STRUCTURE\n";
        echo str_repeat("-", 50) . "\n";
        
        $tables = [
            'questions' => 'SELECT COUNT(*) as count FROM questions',
            'diagnostic_sessions' => 'SELECT COUNT(*) as count FROM diagnostic_sessions',
            'question_responses' => 'SELECT COUNT(*) as count FROM question_responses',
            'student_mastery' => 'SELECT COUNT(*) as count FROM student_mastery'
        ];
        
        foreach ($tables as $table => $query) {
            try {
                $stmt = $this->db->query($query);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ {$table}: {$result['count']} records\n";
            } catch (PDOException $e) {
                echo "❌ {$table}: Error - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    public function runAllTests($user_id = 1, $competency = 'number_algebra') {
        echo "🧪 RUNNING ALL ALGORITHM TESTS\n";
        echo str_repeat("=", 60) . "\n\n";
        
        $this->testDatabaseStructure();
        $this->testQuestionFetching($competency);
        $this->testFisherYatesShuffle();
        $this->testMasteryScoring();
        $this->testBKTCalculation($user_id, $competency);
        
        echo "🎉 All tests completed!\n";
    }
}

// Command line interface
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

$tester = new AlgorithmTester();

// Parse command line arguments
$command = isset($argv[1]) ? $argv[1] : 'all';
$user_id = isset($argv[2]) ? (int)$argv[2] : 1;
$competency = isset($argv[3]) ? $argv[3] : 'number_algebra';

switch ($command) {
    case 'shuffle':
        $tester->testFisherYatesShuffle();
        break;
        
    case 'questions':
        $difficulty = isset($argv[4]) ? $argv[4] : 'beginner';
        $tester->testQuestionFetching($competency, $difficulty);
        break;
        
    case 'bkt':
        $tester->testBKTCalculation($user_id, $competency);
        break;
        
    case 'mastery':
        $tester->testMasteryScoring();
        break;
        
    case 'database':
        $tester->testDatabaseStructure();
        break;
        
    case 'all':
    default:
        $tester->runAllTests($user_id, $competency);
        break;
}

echo "\nUsage Examples:\n";
echo "php test_algorithms.php all                    # Run all tests\n";
echo "php test_algorithms.php shuffle                # Test Fisher-Yates only\n";
echo "php test_algorithms.php questions 1 number_algebra beginner\n";
echo "php test_algorithms.php bkt 1 number_algebra   # Test BKT algorithm\n";
echo "php test_algorithms.php mastery                # Test scoring thresholds\n";
echo "php test_algorithms.php database               # Check database structure\n";