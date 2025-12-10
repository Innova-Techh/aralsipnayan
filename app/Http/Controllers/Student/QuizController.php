<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    /**
     * Get the correct Python command for the environment
     */
    private function getPythonCommand()
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        if ($isWindows) {
            // On Windows, try python first, then python3
            $testOutput = shell_exec('python --version 2>&1');
            if ($testOutput && strpos($testOutput, 'Python') !== false) {
                return 'python';
            }
            
            $testOutput = shell_exec('python3 --version 2>&1');
            if ($testOutput && strpos($testOutput, 'Python') !== false) {
                return 'python3';
            }
            
            // Default for Windows
            return 'python';
        } else {
            // On Linux/Unix, try python3 first, then python
            $python3 = shell_exec('which python3 2>&1');
            if ($python3 && trim($python3) !== '' && file_exists(trim($python3))) {
                return 'python3';
            }
            
            $python = shell_exec('which python 2>&1');
            if ($python && trim($python) !== '' && file_exists(trim($python))) {
                return 'python';
            }
            
            // Fallback: try direct execution
            $testOutput = shell_exec('python3 --version 2>&1');
            if ($testOutput && strpos($testOutput, 'Python') !== false) {
                return 'python3';
            }
            
            $testOutput = shell_exec('python --version 2>&1');
            if ($testOutput && strpos($testOutput, 'Python') !== false) {
                return 'python';
            }
            
            // Default fallback for Linux
            return 'python3';
        }
    }

    /**
     * Show quiz interface
     */
    public function show(Request $request, $category)
    {
        $user = Auth::guard('student')->user();
        
        // Validate category
        $validCategories = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('student.assessments');
        }
        
        // Check if this is diagnostic mode
        $diagnosticMode = session('diagnostic_session_id') ? true : false;
        
        if ($diagnosticMode) {
            return $this->showDiagnosticQuestion($category);
        } else {
            // Check for specific assessment ID or create new assessment
            $assessmentId = $request->get('assessment_id');
            return $this->showRegularAssessment($category, $assessmentId);
        }
    }
    
    /**
     * Show diagnostic question
     */
    private function showDiagnosticQuestion($category)
    {
        // Get current diagnostic question
        $questions = session('diagnostic_questions', []);
        $currentIndex = session('current_question_index', 0);
        $currentQuestion = $questions[$currentIndex] ?? null;
        
        if (!$currentQuestion) {
            return redirect()->route('student.assessments')
                ->with('error', 'No active diagnostic session found.');
        }
        
        $questionData = [
            'question_id' => $currentQuestion['question_id'],
            'text' => $currentQuestion['question_text'],
            'type' => $currentQuestion['question_type'] ?? 'multiple_choice',
            'options' => json_decode($currentQuestion['options'] ?? '[]', true),
            'max_time' => $currentQuestion['max_time_seconds'] ?? 30,
            'difficulty_level' => $currentQuestion['difficulty_level'] ?? 'beginner'
        ];
        
        return view('student.quiz', [
            'category' => $category,
            'question' => (object) $questionData,
            'currentQuestion' => $currentIndex + 1,
            'totalQuestions' => count($questions),
            'diagnosticMode' => true,
            'diagnosticPhase' => session('diagnostic_phase', 1)
        ]);
    }
    
    /**
     * Show regular assessment (post-diagnostic)
     */
    private function showRegularAssessment($category, $assessmentId = null)
    {
        $user = Auth::guard('student')->user();
        $dbCompetency = strtolower(str_replace('_', '_', $category));
        
        // Get user's current difficulty level
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->first();
        
        if (!$mastery || !$mastery->has_taken_diagnostic) {
            return redirect()->route('student.quiz.diagnostic', $category);
        }
        
        // Get or create assessment
        if ($assessmentId) {
            // Check if this assessment exists and belongs to user
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$assessment) {
                // Create new assessment based on the custom assessment ID format
                $assessment = $this->createCustomAssessment($assessmentId, $user->id, $dbCompetency, $mastery->current_difficulty);
            }
        } else {
            // Check for active assessment
            $assessment = DB::table('assessments')
                ->where('user_id', $user->id)
                ->where('competency', $dbCompetency)
                ->where('status', 'in_progress')
                ->orderBy('started_at', 'desc')
                ->first();
            
            if (!$assessment) {
                // Create default assessment
                $assessment = $this->createDefaultAssessment($user->id, $dbCompetency, $mastery->current_difficulty);
            }
        }
        
        if (!$assessment) {
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'Failed to create assessment.');
        }
        
        // Check if assessment has exceeded time limit
        $timeLimit = ($assessment->time_limit ?? 30) * 60; // Convert minutes to seconds
        $elapsedTime = time() - strtotime($assessment->started_at);
        
        if ($elapsedTime > $timeLimit && $assessment->status === 'in_progress') {
            // Auto-complete the assessment due to timeout
            DB::table('assessments')
                ->where('assessment_id', $assessment->assessment_id)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'The quiz has exceeded the time limit and has been automatically completed.');
        }
        
        // Get current question
        $currentQuestion = $this->getCurrentQuestion($assessment->assessment_id);
        
        if (!$currentQuestion) {
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'No questions available for assessment.');
        }
        
        // Get progress
        $progress = $this->getAssessmentProgress($assessment->assessment_id);
        
        // Initialize quiz start time if not set
        $sessionKey = "quiz_start_time_{$assessment->assessment_id}";
        if (!session($sessionKey)) {
            session([$sessionKey => now()->timestamp * 1000]); // JavaScript timestamp (milliseconds)
        }
        
        return view('student.regular-quiz', [
            'category' => $category,
            'question' => $currentQuestion,
            'currentQuestion' => $progress['answered_questions'] + 1,
            'totalQuestions' => $progress['total_questions'] ?? 15,
            'diagnosticMode' => false,
            'assessmentId' => $assessment->assessment_id,
            'quizStartTime' => session($sessionKey),
            'timeLimit' => $assessment->time_limit ?? 30 // Pass time limit to view
        ]);
    }
    
    /**
     * Create custom assessment based on assessment ID parameters
     */
    private function createCustomAssessment($assessmentId, $userId, $competency, $difficultyLevel)
    {
        try {
            // Parse assessment ID to get question count
            // Format: CUSTOM_{userId}_{competency}_{difficulty}_{questionCount}_{timestamp}_{index}
            $parts = explode('_', $assessmentId);
            $questionCount = isset($parts[4]) ? (int)$parts[4] : 15;
            
            // Get available questions using cooldown system
            $python = $this->getPythonCommand();
            $scriptPath = base_path('public/algorithm/question_cooldown.py');
            $command = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) . ' get_available ' . escapeshellarg($userId) . ' ' . escapeshellarg($competency) . ' ' . escapeshellarg($difficultyLevel) . ' ' . escapeshellarg($questionCount) . ' 2>&1';
            
            $output = shell_exec($command);
            
            // Filter out ERROR and DEBUG lines
            if ($output) {
                $lines = explode("\n", $output);
                $jsonLines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, 'ERROR:') === 0 || strpos($line, 'DEBUG:') === 0) {
                        continue;
                    }
                    $jsonLines[] = $line;
                }
                $jsonOutput = implode("\n", $jsonLines);
                if (!empty($jsonOutput)) {
                    // Extract JSON
                    $lastOpenBrace = strrpos($jsonOutput, '{');
                    $lastCloseBrace = strrpos($jsonOutput, '}');
                    if ($lastOpenBrace !== false && $lastCloseBrace !== false && $lastCloseBrace > $lastOpenBrace) {
                        $potentialJson = substr($jsonOutput, $lastOpenBrace, $lastCloseBrace - $lastOpenBrace + 1);
                        $testDecode = json_decode($potentialJson, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $output = $potentialJson;
                        }
                    }
                }
            }
            
            $result = json_decode($output, true);
            
            if (!$result || !$result['success'] || empty($result['available_questions'])) {
                throw new \Exception('No available questions found');
            }
            
            $questions = array_slice($result['available_questions'], 0, $questionCount);
            
            // Create assessment record
            DB::table('assessments')->insert([
                'assessment_id' => $assessmentId,
                'user_id' => $userId,
                'competency' => $competency,
                'assessment_type' => 'regular',
                'difficulty_level' => $difficultyLevel,
                'total_questions' => count($questions),
                'time_limit' => 30, // 30 minutes default
                'status' => 'in_progress',
                'started_at' => now()
            ]);
            
            // Shuffle questions using Fisher-Yates
            $this->createShuffledQuestionPool($assessmentId, $questions);
            
            return DB::table('assessments')->where('assessment_id', $assessmentId)->first();
            
        } catch (\Exception $e) {
            Log::error('Failed to create custom assessment: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create default assessment (15 questions)
     */
    private function createDefaultAssessment($userId, $competency, $difficultyLevel)
    {
        try {
            // Get available questions using cooldown system
            $python = $this->getPythonCommand();
            $scriptPath = base_path('public/algorithm/question_cooldown.py');
            $command = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) . ' get_available ' . escapeshellarg($userId) . ' ' . escapeshellarg($competency) . ' ' . escapeshellarg($difficultyLevel) . ' 15 2>&1';
            
            $output = shell_exec($command);
            
            // Filter out ERROR and DEBUG lines
            if ($output) {
                $lines = explode("\n", $output);
                $jsonLines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, 'ERROR:') === 0 || strpos($line, 'DEBUG:') === 0) {
                        continue;
                    }
                    $jsonLines[] = $line;
                }
                $jsonOutput = implode("\n", $jsonLines);
                if (!empty($jsonOutput)) {
                    // Extract JSON
                    $lastOpenBrace = strrpos($jsonOutput, '{');
                    $lastCloseBrace = strrpos($jsonOutput, '}');
                    if ($lastOpenBrace !== false && $lastCloseBrace !== false && $lastCloseBrace > $lastOpenBrace) {
                        $potentialJson = substr($jsonOutput, $lastOpenBrace, $lastCloseBrace - $lastOpenBrace + 1);
                        $testDecode = json_decode($potentialJson, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $output = $potentialJson;
                        }
                    }
                }
            }
            
            $result = json_decode($output, true);
            
            if (!$result || !$result['success'] || empty($result['available_questions'])) {
                throw new \Exception('No available questions found');
            }
            
            $questions = $result['available_questions'];
            
            // Create assessment record
            $assessmentId = "ASSESS_{$userId}_{$competency}_" . time();
            
            DB::table('assessments')->insert([
                'assessment_id' => $assessmentId,
                'user_id' => $userId,
                'competency' => $competency,
                'assessment_type' => 'regular',
                'difficulty_level' => $difficultyLevel,
                'total_questions' => count($questions),
                'time_limit' => 30, // 30 minutes default
                'status' => 'in_progress',
                'started_at' => now()
            ]);
            
            // Shuffle questions using Fisher-Yates
            $this->createShuffledQuestionPool($assessmentId, $questions);
            
            return DB::table('assessments')->where('assessment_id', $assessmentId)->first();
            
        } catch (\Exception $e) {
            Log::error('Failed to create default assessment: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create shuffled question pool
     */
    private function createShuffledQuestionPool($assessmentId, $questions)
    {
        $python = $this->getPythonCommand();
        $shuffleScript = base_path('public/algorithm/fisher_yates.py');
        $questionsJson = json_encode($questions);
        $shuffleCommand = escapeshellarg($python) . ' ' . escapeshellarg($shuffleScript) . ' create_pool ' . escapeshellarg($assessmentId) . ' ' . escapeshellarg($questionsJson) . ' 2>&1';
        
        $shuffleOutput = shell_exec($shuffleCommand);
        
        // Filter out ERROR and DEBUG lines
        if ($shuffleOutput) {
            $lines = explode("\n", $shuffleOutput);
            $jsonLines = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, 'ERROR:') === 0 || strpos($line, 'DEBUG:') === 0) {
                    continue;
                }
                $jsonLines[] = $line;
            }
            $jsonOutput = implode("\n", $jsonLines);
            if (!empty($jsonOutput)) {
                // Extract JSON
                $lastOpenBrace = strrpos($jsonOutput, '{');
                $lastCloseBrace = strrpos($jsonOutput, '}');
                if ($lastOpenBrace !== false && $lastCloseBrace !== false && $lastCloseBrace > $lastOpenBrace) {
                    $potentialJson = substr($jsonOutput, $lastOpenBrace, $lastCloseBrace - $lastOpenBrace + 1);
                    $testDecode = json_decode($potentialJson, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $shuffleOutput = $potentialJson;
                    }
                }
            }
        }
        
        $shuffleResult = json_decode($shuffleOutput, true);
        
        if (!$shuffleResult || !$shuffleResult['success']) {
            throw new \Exception('Failed to create question pool');
        }
        
        return $shuffleResult;
    }
    
    /**
     * Get current question from assessment
     */
    private function getCurrentQuestion($assessmentId)
    {
        try {
            $python = $this->getPythonCommand();
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) . ' next_question ' . escapeshellarg($assessmentId) . ' 2>&1';
            
            $output = shell_exec($command);
            
            // Filter out ERROR and DEBUG lines
            if ($output) {
                $lines = explode("\n", $output);
                $jsonLines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, 'ERROR:') === 0 || strpos($line, 'DEBUG:') === 0) {
                        continue;
                    }
                    $jsonLines[] = $line;
                }
                $jsonOutput = implode("\n", $jsonLines);
                if (!empty($jsonOutput)) {
                    // Extract JSON
                    $lastOpenBrace = strrpos($jsonOutput, '{');
                    $lastCloseBrace = strrpos($jsonOutput, '}');
                    if ($lastOpenBrace !== false && $lastCloseBrace !== false && $lastCloseBrace > $lastOpenBrace) {
                        $potentialJson = substr($jsonOutput, $lastOpenBrace, $lastCloseBrace - $lastOpenBrace + 1);
                        $testDecode = json_decode($potentialJson, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $output = $potentialJson;
                        }
                    }
                }
            }
            
            $result = json_decode($output, true);
            
            if ($result && $result['success'] && isset($result['question'])) {
                $q = $result['question'];
                
                // Convert individual choice fields to options array with numeric indices
                $options = [];
                if (!empty($q['choice_a'])) $options[0] = $q['choice_a'];
                if (!empty($q['choice_b'])) $options[1] = $q['choice_b'];
                if (!empty($q['choice_c'])) $options[2] = $q['choice_c'];
                if (!empty($q['choice_d'])) $options[3] = $q['choice_d'];
                
                return (object) [
                    'question_id' => $q['question_id'],
                    'text' => $q['question_text'],
                    'type' => $q['question_type'] ?? 'multiple_choice',
                    'options' => $options,
                    'max_time' => $q['max_allowed_time'] ?? 30,
                    'difficulty_level' => $q['difficulty_level'] ?? 'beginner',
                    'topic' => $q['topic_tag'] ?? '',
                    'explanation' => $q['explanation'] ?? '',
                    'correct_answer' => $q['correct_answer'] ?? ''
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Failed to get current question: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get assessment progress
     */
    private function getAssessmentProgress($assessmentId)
    {
        try {
            $python = $this->getPythonCommand();
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) . ' progress ' . escapeshellarg($assessmentId) . ' 2>&1';
            
            $output = shell_exec($command);
            
            // Filter out ERROR and DEBUG lines
            if ($output) {
                $lines = explode("\n", $output);
                $jsonLines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, 'ERROR:') === 0 || strpos($line, 'DEBUG:') === 0) {
                        continue;
                    }
                    $jsonLines[] = $line;
                }
                $jsonOutput = implode("\n", $jsonLines);
                if (!empty($jsonOutput)) {
                    // Extract JSON
                    $lastOpenBrace = strrpos($jsonOutput, '{');
                    $lastCloseBrace = strrpos($jsonOutput, '}');
                    if ($lastOpenBrace !== false && $lastCloseBrace !== false && $lastCloseBrace > $lastOpenBrace) {
                        $potentialJson = substr($jsonOutput, $lastOpenBrace, $lastCloseBrace - $lastOpenBrace + 1);
                        $testDecode = json_decode($potentialJson, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $output = $potentialJson;
                        }
                    }
                }
            }
            
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return $result['progress'];
            }
            
            return ['total_questions' => 15, 'answered_questions' => 0, 'current_question_number' => 1];
            
        } catch (\Exception $e) {
            Log::error('Failed to get assessment progress: ' . $e->getMessage());
            return ['total_questions' => 15, 'answered_questions' => 0, 'current_question_number' => 1];
        }
    }
    
    /**
     * Submit answer for regular assessment
     */
    public function submitAnswer(Request $request)
    {
        Log::info('Submit answer request received', [
            'question_id' => $request->question_id,
            'answer' => $request->answer,
            'time_taken' => $request->time_taken,
            'assessment_id' => $request->assessment_id,
            'user_id' => Auth::id()
        ]);
        
        try {
            $request->validate([
                'question_id' => 'required|string',
                'answer' => 'required|string',
                'time_taken' => 'required|integer|min:1',
                'assessment_id' => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in submitAnswer', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        $user = Auth::guard('student')->user();
        
        // Validate quiz time limit
        $assessment = DB::table('assessments')
            ->where('assessment_id', $request->assessment_id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found.'
            ], 404);
        }
        
        // Check if quiz has exceeded time limit
        $quizStartTime = strtotime($assessment->started_at);
        $timeLimit = ($assessment->time_limit ?? 30) * 60; // Convert minutes to seconds
        $elapsedTime = time() - $quizStartTime;
        
        if ($elapsedTime > $timeLimit) {
            // Auto-complete the assessment due to timeout
            DB::table('assessments')
                ->where('assessment_id', $request->assessment_id)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                
            return response()->json([
                'success' => false,
                'message' => 'Quiz has exceeded the time limit.',
                'timeout' => true,
                'redirect' => route('student.assessments')
            ], 410);
        }
        
        try {
            // Get question details
            $question = DB::table('questions')
                ->where('question_id', $request->question_id)
                ->first();
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found.'
                ]);
            }
            
            $isCorrect = $request->answer === $question->correct_answer;
            
            // Get current BKT values
            $competency = DB::table('assessments')
                ->where('assessment_id', $request->assessment_id)
                ->value('competency');
            
            $mastery = DB::table('student_mastery')
                ->where('user_id', $user->id)
                ->where('competency', $competency)
                ->first();
            
            if (!$mastery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mastery record not found.'
                ]);
            }
            
            // Calculate time score
            $normalizedTime = $request->time_taken / $question->max_allowed_time;
            $timeScore = $this->calculateTimeScore($normalizedTime, $isCorrect);
            
            // Update BKT using algorithm
            $newBktScore = $this->updateBKT(
                $mastery->bkt_score,
                $isCorrect,
                $timeScore,
                $mastery->current_difficulty,
                [
                    'prior_knowledge' => $mastery->prior_knowledge,
                    'learn_rate' => $mastery->learn_rate,
                    'slip_rate' => $mastery->slip_rate,
                    'guess_rate' => $mastery->guess_rate
                ]
            );
            
            // Record response - use shorter response_id to fit 50 char limit
            $responseData = "{$request->assessment_id}_{$request->question_id}_" . time();
            $responseHash = substr(md5($responseData), 0, 8);
            $responseId = "RESP_{$responseHash}_" . substr(time(), -6); // RESP_8charhash_6digits = ~20 chars
            
            DB::table('question_responses')->insert([
                'response_id' => $responseId,
                'assessment_id' => $request->assessment_id,
                'user_id' => $user->id,
                'question_id' => $request->question_id,
                'user_answer' => $request->answer,
                'is_correct' => $isCorrect,
                'response_time' => $request->time_taken,
                'max_allowed_time' => $question->max_allowed_time,
                'normalized_time' => $normalizedTime,
                'time_score' => $timeScore,
                'bkt_before' => $mastery->bkt_score,
                'bkt_after' => $newBktScore,
                'difficulty_factor' => $this->getDifficultyFactor($mastery->current_difficulty),
                'time_factor' => $timeScore,
                'base_points' => $this->getBasePoints($mastery->current_difficulty),
                'time_bonus_points' => $isCorrect ? $this->getTimeBonusPoints($normalizedTime) : 0,
                'total_points' => $this->getBasePoints($mastery->current_difficulty) + ($isCorrect ? $this->getTimeBonusPoints($normalizedTime) : 0),
                'answered_at' => now()
            ]);
            
            // Update mastery record
            $newCorrectCount = $mastery->correct_answers + ($isCorrect ? 1 : 0);
            $newTotalCount = $mastery->total_questions_answered + 1;
            $newAccuracy = $newTotalCount > 0 ? $newCorrectCount / $newTotalCount : 0;
            $newMasteryScore = $this->calculateMasteryScore($newAccuracy, $newBktScore);
            
            // Check for difficulty level progression
            $newDifficulty = $this->checkDifficultyProgression($newMasteryScore, $mastery->current_difficulty);
            
            DB::table('student_mastery')
                ->where('user_id', $user->id)
                ->where('competency', $competency)
                ->update([
                    'bkt_score' => $newBktScore,
                    'accuracy_score' => $newAccuracy,
                    'final_mastery_score' => $newMasteryScore,
                    'current_difficulty' => $newDifficulty,
                    'correct_answers' => $newCorrectCount,
                    'total_questions_answered' => $newTotalCount,
                    'updated_at' => now()
                ]);
            
            // Mark question as answered
            $python = $this->getPythonCommand();
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $markCommand = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) . ' mark_answered ' . escapeshellarg($request->assessment_id) . ' ' . escapeshellarg($request->question_id) . ' 2>&1';
            $markResult = shell_exec($markCommand);
            
            // Filter out ERROR and DEBUG lines from mark result
            if ($markResult) {
                $lines = explode("\n", $markResult);
                $jsonLines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, 'ERROR:') === 0 || strpos($line, 'DEBUG:') === 0) {
                        continue;
                    }
                    $jsonLines[] = $line;
                }
                $markResult = implode("\n", $jsonLines);
            }
            
            Log::info("Mark answered result: " . $markResult);
            
            // Small delay to ensure database is updated
            usleep(100000); // 0.1 second delay
            
            // Get next question
            $nextQuestion = $this->getCurrentQuestion($request->assessment_id);
            $progress = $this->getAssessmentProgress($request->assessment_id);
            Log::info("Progress after marking answered: " . json_encode($progress));
            
            if (!$nextQuestion || $progress['answered_questions'] >= $progress['total_questions']) {
                // Assessment complete
                $this->completeAssessment($request->assessment_id);
                
                // Get category for redirect URL
                $assessment = DB::table('assessments')
                    ->where('assessment_id', $request->assessment_id)
                    ->first();
                
                $category = $assessment ? ucfirst(str_replace('_', '_', $assessment->competency)) : 'Number_Algebra';
                
                return response()->json([
                    'success' => true,
                    'is_correct' => $isCorrect,
                    'correct_answer' => $question->correct_answer,
                    'explanation' => $question->explanation,
                    'assessment_complete' => true,
                    'redirect_url' => route('student.assessments.review', ['category' => $category]),
                    'updated_mastery' => $newMasteryScore,
                    'difficulty_change' => $newDifficulty !== $mastery->current_difficulty ? $newDifficulty : null,
                    'final_results' => [
                        'total_questions' => $progress['total_questions'],
                        'correct_answers' => $newCorrectCount,
                        'accuracy' => round($newAccuracy * 100, 2),
                        'mastery_score' => round($newMasteryScore, 2),
                        'difficulty_level' => $newDifficulty
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
                'next_question' => $nextQuestion,
                'updated_mastery' => $newMasteryScore,
                'progress' => $progress,
                'next_question_number' => $progress['answered_questions'] + 1,
                'total_questions' => $progress['total_questions'],
                'assessment_complete' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('Answer submission failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process answer. Please try again.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate time score based on speed and correctness
     */
    private function calculateTimeScore($normalizedTime, $isCorrect)
    {
        if ($isCorrect) {
            if ($normalizedTime <= 0.5) return 1.0;  // Fast + correct
            if ($normalizedTime <= 0.8) return 0.8;  // Medium + correct
            return 0.6;  // Slow + correct
        } else {
            if ($normalizedTime <= 0.5) return 0.2;  // Fast + wrong
            return 0.1;  // Slow + wrong
        }
    }
    
    /**
     * Update BKT score
     */
    private function updateBKT($priorBkt, $isCorrect, $timeScore, $difficulty, $params)
    {
        $difficultyFactor = $this->getDifficultyFactor($difficulty);
        
        if ($isCorrect) {
            $numerator = $priorBkt * (1 - $params['slip_rate']) * $timeScore * $difficultyFactor;
            $denominator = $priorBkt * (1 - $params['slip_rate']) + (1 - $priorBkt) * $params['guess_rate'];
        } else {
            $numerator = $priorBkt * $params['slip_rate'];
            $denominator = $priorBkt * $params['slip_rate'] + (1 - $priorBkt) * (1 - $params['guess_rate']) * $timeScore * $difficultyFactor;
        }
        
        if ($denominator == 0) return $priorBkt;
        
        $newBkt = $numerator / $denominator;
        
        // Apply learning transition
        $newBkt = $newBkt + (1 - $newBkt) * $params['learn_rate'];
        
        return max(0, min(1, $newBkt));
    }
    
    /**
     * Get difficulty factor
     */
    private function getDifficultyFactor($difficulty)
    {
        $factors = [
            'beginner' => 0.8,
            'intermediate' => 1.0,
            'advanced' => 1.2
        ];
        
        return $factors[$difficulty] ?? 1.0;
    }
    
    /**
     * Get base points for difficulty
     */
    private function getBasePoints($difficulty)
    {
        $points = [
            'beginner' => 3,
            'intermediate' => 6,
            'advanced' => 10
        ];
        
        return $points[$difficulty] ?? 6;
    }
    
    /**
     * Get time bonus points based on speed
     */
    private function getTimeBonusPoints($normalizedTime)
    {
        if ($normalizedTime <= 0.5) return 15;  // Fast
        if ($normalizedTime <= 0.8) return 10; // Medium
        return 5; // Slow
    }
    
    /**
     * Calculate mastery score
     */
    private function calculateMasteryScore($accuracy, $bktScore)
    {
        return (0.55 * $accuracy + 0.45 * $bktScore) * 100;
    }
    
    /**
     * Check for difficulty level progression
     */
    private function checkDifficultyProgression($masteryScore, $currentDifficulty)
    {
        if ($masteryScore >= 85 && $currentDifficulty !== 'advanced') {
            return 'advanced';
        } elseif ($masteryScore >= 76 && $masteryScore <= 84 && $currentDifficulty !== 'intermediate') {
            return 'intermediate';
        } elseif ($masteryScore <= 75 && $currentDifficulty !== 'beginner') {
            return 'beginner';
        }
        
        return $currentDifficulty;
    }
    
    /**
     * Complete assessment
     */
    private function completeAssessment($assessmentId)
    {
        try {
            // Get assessment summary
            $summary = DB::table('question_responses')
                ->where('assessment_id', $assessmentId)
                ->selectRaw('
                    COUNT(*) as total_answered,
                    SUM(is_correct) as correct_answers,
                    AVG(response_time) as avg_response_time,
                    AVG(time_score) as avg_time_score,
                    SUM(total_points) as total_points_earned
                ')
                ->first();
            
            // Update assessment record
            DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'questions_answered' => $summary->total_answered,
                    'correct_answers' => $summary->correct_answers,
                    'incorrect_answers' => $summary->total_answered - $summary->correct_answers,
                    'accuracy_percentage' => ($summary->correct_answers / $summary->total_answered) * 100,
                    'average_response_time' => $summary->avg_response_time,
                    'time_performance_score' => $summary->avg_time_score
                ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to complete assessment: ' . $e->getMessage());
        }
    }
    
    /**
     * Get hint for current question
     */
    public function getHint(Request $request)
    {
        $request->validate([
            'question_id' => 'required|string'
        ]);
        
        try {
            $question = DB::table('questions')
                ->where('question_id', $request->question_id)
                ->first();
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found.'
                ]);
            }
            
            // Return the explanation as a hint (you can modify this logic)
            $hint = $question->explanation ?? 'No hint available for this question.';
            
            // You could also create a specific hint field in the database
            // or generate hints based on the question content
            
            return response()->json([
                'success' => true,
                'hint' => $hint
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get hint: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hint.'
            ]);
        }
    }

    /**
     * Cleanup assessment session
     */
    public function cleanupAssessment(Request $request)
    {
        try {
            $assessmentId = $request->input('assessment_id');
            $questionId = $request->input('question_id');
            $progress = $request->input('progress', []);
            
            // Log the cleanup request for debugging
            Log::info('Assessment cleanup requested', [
                'user_id' => Auth::id(),
                'assessment_id' => $assessmentId,
                'question_id' => $questionId,
                'progress' => $progress
            ]);
            
            // Here you can implement any cleanup logic needed
            // For example: save final progress, cleanup temporary data, etc.
            
            // You might want to save the last progress state
            if ($assessmentId && $questionId && !empty($progress)) {
                // Save progress logic (if needed)
                // This could be similar to your save-progress functionality
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Assessment session cleaned up successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to cleanup assessment session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup assessment session.'
            ]);
        }
    }

    /**
     * Save assessment progress
     */
    public function saveAssessmentProgress(Request $request)
    {
        try {
            $assessmentId = $request->input('assessment_id');
            $questionId = $request->input('question_id');
            $currentAnswer = $request->input('current_answer');
            $timeTaken = $request->input('time_taken', 0);
            $hintUsed = $request->input('hint_used', false);
            
            // Log the progress save request
            Log::info('Assessment progress save requested', [
                'user_id' => Auth::id(),
                'assessment_id' => $assessmentId,
                'question_id' => $questionId,
                'current_answer' => $currentAnswer,
                'time_taken' => $timeTaken,
                'hint_used' => $hintUsed
            ]);
            
            // Here you can implement progress saving logic
            // For example: save to database, update session, etc.
            // This could integrate with your existing progress tracking system
            
            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to save assessment progress: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress.'
            ]);
        }
    }
}