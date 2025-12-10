<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\AssessmentGenerationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    protected $assessmentGenerator;

    public function __construct()
    {
        $this->assessmentGenerator = new AssessmentGenerationController();
    }
    
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
     * Execute Python script with proper error handling
     */
    private function executePythonScript($scriptPath, $args = [])
    {
        $python = $this->getPythonCommand();
        $fullScriptPath = base_path('public/algorithm/' . $scriptPath);
        
        // Build command with proper escaping
        // On Windows, use quotes around the full path; on Linux, use escapeshellarg
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        if ($isWindows) {
            $command = escapeshellarg($python) . ' ' . escapeshellarg($fullScriptPath);
        } else {
            $command = escapeshellarg($python) . ' ' . escapeshellarg($fullScriptPath);
        }
        
        foreach ($args as $arg) {
            $command .= ' ' . escapeshellarg($arg);
        }
        
        // Capture both stdout and stderr
        $command .= ' 2>&1';
        
        Log::info("Executing Python script", [
            'command' => $command,
            'script' => $fullScriptPath,
            'python' => $python,
            'args' => $args
        ]);
        
        $output = shell_exec($command);
        
        if ($output === null || trim($output) === '') {
            Log::error("Python script returned no output", [
                'command' => $command,
                'script' => $fullScriptPath,
                'python' => $python
            ]);
            throw new \Exception('Python script returned no output. Command: ' . $command);
        }
        
        // Filter out ERROR lines that are printed to stderr (captured via 2>&1)
        // The Python script outputs ERROR messages to stderr which get mixed with JSON output
        $lines = explode("\n", $output);
        $jsonLines = [];
        $errorLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            // Check if line starts with "ERROR:" (from Python's log_error function)
            if (strpos($line, 'ERROR:') === 0) {
                $errorLines[] = $line;
                // Log the error but don't include it in JSON parsing
                Log::warning("Python script error output", [
                    'error' => $line,
                    'script' => $fullScriptPath
                ]);
            } else {
                // Keep non-error lines for JSON parsing
                $jsonLines[] = $line;
            }
        }
        
        // Try to find JSON in the output
        // JSON should be the last complete JSON object in the output
        $jsonOutput = implode("\n", $jsonLines);
        
        // If no JSON lines found, try the original output
        if (empty($jsonOutput)) {
            $jsonOutput = $output;
        }
        
        // Try to extract JSON from the output (look for JSON object boundaries)
        // Find the last occurrence of { and } to extract the JSON
        // This handles cases where ERROR messages are mixed with JSON
        $lastOpenBrace = strrpos($jsonOutput, '{');
        $lastCloseBrace = strrpos($jsonOutput, '}');
        
        if ($lastOpenBrace !== false && $lastCloseBrace !== false && $lastCloseBrace > $lastOpenBrace) {
            $potentialJson = substr($jsonOutput, $lastOpenBrace, $lastCloseBrace - $lastOpenBrace + 1);
            // Verify it's valid JSON before using it
            $testDecode = json_decode($potentialJson, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $jsonOutput = $potentialJson;
            }
        }
        
        $result = json_decode($jsonOutput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Invalid JSON from Python script", [
                'command' => $command,
                'raw_output' => $output,
                'filtered_output' => $jsonOutput,
                'error_lines' => $errorLines,
                'json_error' => json_last_error_msg()
            ]);
            throw new \Exception('Invalid JSON response from Python script: ' . json_last_error_msg() . '. Output: ' . substr($output, 0, 500));
        }
        
        return $result;
    }

    /**
     * Show assessments page
     */
    public function index()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user->isStudent()) {
            return redirect()->route('login');
        }
        
        $student = $user->studentProfile;
        
        // Check if onboarding is completed
        if (!$student || !$student->has_completed_onboarding) {
            return redirect()->route('student.onboarding.welcome');
        }
        
        // Get student mastery data for all competencies
        $masteryData = [];
        $incompleteSessionData = [];
        $competencies = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        
        foreach ($competencies as $competency) {
            $dbCompetency = strtolower(str_replace('_', '_', $competency));
            
            // Check for incomplete diagnostic session
            $incompleteSession = DB::table('diagnostic_sessions')
                ->where('user_id', $user->id)
                ->where('competency', $dbCompetency)
                ->where('status', '!=', 'completed')
                ->orderBy('started_at', 'desc')
                ->first();
            
            $incompleteSessionData[$competency] = $incompleteSession;
            
            $mastery = DB::table('student_mastery')
                ->where('user_id', $user->id)
                ->where('competency', $dbCompetency)
                ->first();
            
            if ($mastery) {
                $masteryData[$competency] = (object) [
                    'current_difficulty_level' => ucfirst($mastery->current_difficulty),
                    'mastery_probability' => $mastery->final_mastery_score / 100,
                    'correct_answers' => $mastery->correct_answers,
                    'total_questions_answered' => $mastery->total_questions_answered,
                    'has_taken_diagnostic' => $mastery->has_taken_diagnostic
                ];
            } else {
                // Default values if no mastery record exists
                $masteryData[$competency] = (object) [
                    'current_difficulty_level' => 'Beginner',
                    'mastery_probability' => 0.30,
                    'correct_answers' => 0,
                    'total_questions_answered' => 0,
                    'has_taken_diagnostic' => false
                ];
            }
        }
        
        return view('student.assessments', [
            'student' => $student,
            'masteryData' => $masteryData,
            'incompleteSessionData' => $incompleteSessionData
        ]);
    }
    
    /**
     * Show assessment category page
     */
    public function showCategory($category)
    {
        Log::info("showCategory method called", ['category' => $category]);
        
        $user = Auth::guard('student')->user();
        $student = $user->studentProfile;
        
        // Check if onboarding is completed
        if (!$student || !$student->has_completed_onboarding) {
            return redirect()->route('student.onboarding.welcome');
        }
        
        // Validate category
        $validCategories = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('student.assessments')->with('error', 'Invalid assessment category.');
        }
        
        // Convert to database format
        $dbCompetency = strtolower($category);
        
        // Get mastery data for this category
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->first();
        
        // Check if diagnostic is needed
        if (!$mastery || !$mastery->has_taken_diagnostic) {
            // Redirect to diagnostic
            return redirect()->route('student.quiz.diagnostic', $category);
        }
        
        // Get category display data
        $categoryData = $this->getCategoryDisplayData($category);
        
        // Generate dynamic assessments based on current mastery level
        $currentDifficulty = $mastery->current_difficulty;
        $assessmentData = $this->assessmentGenerator->generateAssessments($dbCompetency, $currentDifficulty, $user->id);
        
        return view('student.assessment-list', [
            'student' => $student,
            'category' => $category,
            'mastery' => $mastery,
            'data' => $categoryData,
            'availableQuestions' => $assessmentData['availableQuestions'],
            'questionsInCooldown' => $assessmentData['questionsInCooldown'],
            'assessmentOptions' => $assessmentData['assessmentOptions']
        ]);
    }
    
    /**
     * Start diagnostic assessment
     */
    public function startDiagnostic($category)
    {
        $user = Auth::guard('student')->user();
        

        
        // Validate category
        $validCategories = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('student.assessments')->with('error', 'Invalid category.');
        }
        
        // Convert to database format
        $dbCompetency = strtolower(str_replace('_', '_', $category));
        
        // Check if diagnostic already taken
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->first();
        
        if ($mastery && $mastery->has_taken_diagnostic) {
            return redirect()->route('student.assessments.category', $category);
        }
        
        // Check for incomplete diagnostic session first
        $incompleteSession = DB::table('diagnostic_sessions')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->where('status', '!=', 'completed')
            ->orderBy('started_at', 'desc')
            ->first();
        
        if ($incompleteSession) {
            // Resume the incomplete diagnostic session
            Log::info("Resuming incomplete diagnostic session", [
                'session_id' => $incompleteSession->session_id,
                'user_id' => $user->id,
                'competency' => $dbCompetency,
                'current_phase' => $incompleteSession->current_phase
            ]);
            
            try {
                // Get current questions for the incomplete session
                $result = $this->executePythonScript('bkt_algorithm.py', [
                    'resume_diagnostic',
                    $user->id,
                    $dbCompetency,
                    $incompleteSession->session_id
                ]);
                
                if ($result && isset($result['success']) && $result['success']) {
                    // Restore session variables
                    session([
                        'diagnostic_session_id' => $incompleteSession->session_id,
                        'diagnostic_competency' => $category,
                        'diagnostic_phase' => $result['phase'],
                        'diagnostic_questions' => $result['questions'],
                        'current_question_index' => $result['current_question_index'] ?? 0
                    ]);
                    
                    return redirect()->route('student.quiz.show', $category)
                        ->with('diagnostic_mode', true)
                        ->with('resumed_session', true);
                } else {
                    // If resume fails, clean up the incomplete session from database
                    $errorMessage = $result['message'] ?? ($result['error'] ?? 'Unknown error');
                    Log::warning("Failed to resume diagnostic session, starting new one", [
                        'session_id' => $incompleteSession->session_id,
                        'error' => $errorMessage,
                        'result' => $result
                    ]);
                    
                    // Clean up the failed session from database
                    try {
                        $this->executePythonScript('bkt_algorithm.py', [
                            'cleanup_diagnostic',
                            $incompleteSession->session_id
                        ]);
                    } catch (\Exception $cleanupException) {
                        Log::warning('Failed to cleanup diagnostic session', [
                            'session_id' => $incompleteSession->session_id,
                            'error' => $cleanupException->getMessage()
                        ]);
                    }
                    
                    // Clear PHP session data as well
                    session()->forget([
                        'diagnostic_session_id', 
                        'diagnostic_competency', 
                        'diagnostic_phase', 
                        'diagnostic_questions', 
                        'current_question_index'
                    ]);
                    
                    Log::info("Cleaned up failed diagnostic session before starting new one", [
                        'old_session_id' => $incompleteSession->session_id,
                        'user_id' => $user->id,
                        'competency' => $dbCompetency
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to resume diagnostic session: ' . $e->getMessage());
                
                // Clean up the incomplete session if resume throws an exception
                if (isset($incompleteSession)) {
                    try {
                        $this->executePythonScript('bkt_algorithm.py', [
                            'cleanup_diagnostic',
                            $incompleteSession->session_id
                        ]);
                        
                        // Clear PHP session data as well
                        session()->forget([
                            'diagnostic_session_id', 
                            'diagnostic_competency', 
                            'diagnostic_phase', 
                            'diagnostic_questions', 
                            'current_question_index'
                        ]);
                        
                        Log::info("Cleaned up incomplete diagnostic session after exception", [
                            'old_session_id' => $incompleteSession->session_id,
                            'user_id' => $user->id,
                            'competency' => $dbCompetency
                        ]);
                    } catch (\Exception $cleanupException) {
                        Log::error('Failed to cleanup diagnostic session: ' . $cleanupException->getMessage());
                    }
                }
                // Continue to start new diagnostic if resume fails
            }
        }
        
        try {
            // Clear any existing diagnostic session data from PHP session first
            $existingSessionId = session('diagnostic_session_id');
            if ($existingSessionId) {
                // Cleanup existing session in database
                try {
                    $this->executePythonScript('bkt_algorithm.py', [
                        'cleanup_diagnostic',
                        $existingSessionId
                    ]);
                } catch (\Exception $cleanupException) {
                    Log::warning('Failed to cleanup existing diagnostic session', [
                        'session_id' => $existingSessionId,
                        'error' => $cleanupException->getMessage()
                    ]);
                }
                
                // Clear session data
                session()->forget([
                    'diagnostic_session_id', 
                    'diagnostic_competency', 
                    'diagnostic_phase', 
                    'diagnostic_questions', 
                    'current_question_index'
                ]);
                
                Log::info("Cleared existing diagnostic session before starting new one", [
                    'old_session_id' => $existingSessionId,
                    'user_id' => $user->id,
                    'competency' => $dbCompetency
                ]);
            }
            
            // Call Python script to start diagnostic
            $result = $this->executePythonScript('bkt_algorithm.py', [
                'start_diagnostic',
                $user->id,
                $dbCompetency
            ]);
            
            if ($result && isset($result['success']) && $result['success']) {
                // Store session in session
                session([
                    'diagnostic_session_id' => $result['session_id'],
                    'diagnostic_competency' => $category,
                    'diagnostic_phase' => $result['phase'],
                    'diagnostic_questions' => $result['questions'],
                    'current_question_index' => 0
                ]);
                
                return redirect()->route('student.quiz.show', $category)
                    ->with('diagnostic_mode', true);
            } else {
                $errorMessage = $result['message'] ?? ($result['error'] ?? 'Failed to start diagnostic');
                Log::error('Diagnostic start failed', [
                    'user_id' => $user->id,
                    'competency' => $dbCompetency,
                    'error' => $errorMessage,
                    'result' => $result
                ]);
                throw new \Exception($errorMessage);
            }
            
        } catch (\Exception $e) {
            Log::error('Diagnostic start failed: ' . $e->getMessage());
            
            return redirect()->route('student.assessments')
                ->with('error', 'Failed to start diagnostic. Please try again.');
        }
    }
    
    /**
     * Submit diagnostic answer
     */
    public function submitDiagnosticAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|string',
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:1',
        ]);
        
        $user = Auth::guard('student')->user();
        $sessionId = session('diagnostic_session_id');
        $questions = session('diagnostic_questions', []);
        $currentIndex = session('current_question_index', 0);
        
        if (!$sessionId || empty($questions)) {
            return response()->json([
                'success' => false,
                'message' => 'No active diagnostic session found.'
            ]);
        }
        
        $currentQuestion = $questions[$currentIndex] ?? null;
        if (!$currentQuestion) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found.'
            ]);
        }
        
        try {
            // Check if answer is correct
            $isCorrect = $request->answer === $currentQuestion['correct_answer'];
            
            // Get user and competency info
            $userId = Auth::guard('student')->id();
            $competency = session('diagnostic_competency');
            
            // Record answer via Python script
            $result = $this->executePythonScript('bkt_algorithm.py', [
                'record_answer',
                $userId,
                $competency,
                $sessionId,
                $request->question_id,
                $request->answer,
                $request->time_taken,
                $isCorrect ? 'true' : 'false'
            ]);
            
            // Move to next question
            $nextIndex = $currentIndex + 1;
            session(['current_question_index' => $nextIndex]);
            
            if ($nextIndex >= count($questions)) {
                // Phase complete - check for next phase
                $phaseResult = $this->completeDiagnosticPhase($sessionId);
                
                // Check if the diagnostic is complete
                $isDiagnosticComplete = isset($phaseResult['diagnostic_complete']) ? $phaseResult['diagnostic_complete'] : false;
                
                if ($isDiagnosticComplete) {
                    // Get competency before clearing session
                    $competency = session('diagnostic_competency');
                    
                    // Store final results in session for the complete page
                    session(['diagnostic_final_results' => $phaseResult['final_results'] ?? $phaseResult]);
                    
                    // Clear diagnostic session data
                    session()->forget(['diagnostic_session_id', 'diagnostic_competency', 'diagnostic_phase', 'diagnostic_questions', 'current_question_index']);
                    
                    return response()->json([
                        'success' => true,
                        'is_correct' => $isCorrect,
                        'correct_answer' => $currentQuestion['correct_answer'],
                        'explanation' => $currentQuestion['explanation'] ?? null,
                        'phase_complete' => true,
                        'diagnostic_complete' => true,
                        'final_results' => $phaseResult,
                        'redirect_url' => route('student.assessments.complete', $competency)
                    ]);
                } else {
                    // Move to next phase - extract data safely
                    $nextPhase = $phaseResult['next_phase'] ?? 2;
                    $phaseName = $phaseResult['phase_name'] ?? 'intermediate';
                    $questions = $phaseResult['questions'] ?? [];
                    
                    session([
                        'diagnostic_phase' => $nextPhase,
                        'diagnostic_questions' => $questions,
                        'current_question_index' => 0
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'is_correct' => $isCorrect,
                        'correct_answer' => $currentQuestion['correct_answer'],
                        'explanation' => $currentQuestion['explanation'] ?? null,
                        'phase_complete' => true,
                        'diagnostic_complete' => false,
                        'next_phase' => $phaseName,
                        'next_question' => $questions[0] ?? null
                    ]);
                }
            } else {
                // Continue with next question in same phase
                $nextQuestion = $questions[$nextIndex];
                
                return response()->json([
                    'success' => true,
                    'is_correct' => $isCorrect,
                    'correct_answer' => $currentQuestion['correct_answer'],
                    'explanation' => $currentQuestion['explanation'] ?? null,
                    'phase_complete' => false,
                    'next_question' => $nextQuestion
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Diagnostic answer submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process answer. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Complete diagnostic phase
     */
    private function completeDiagnosticPhase($sessionId)
    {
        try {
            $userId = Auth::guard('student')->id();
            $competency = session('diagnostic_competency');
            
            if (!$userId || !$competency) {
                Log::error('Missing user ID or competency for diagnostic phase completion');
                return ['success' => false, 'message' => 'Missing session data'];
            }
            
            $result = $this->executePythonScript('bkt_algorithm.py', [
                'complete_phase',
                $userId,
                $competency,
                $sessionId
            ]);
            
            // Check if result is valid and has required keys
            if (!$result || !isset($result['success'])) {
                Log::error('Invalid response from Python script', ['result' => $result]);
                return ['success' => false, 'message' => 'Invalid script response'];
            }
            
            // Ensure diagnostic_complete key exists
            if (!isset($result['diagnostic_complete'])) {
                $result['diagnostic_complete'] = false;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Failed to complete diagnostic phase: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get category display data
     */
    private function getCategoryDisplayData($category)
    {
        $categoryData = [
            'Number_Algebra' => [
                'title' => 'Number and Algebra',
                'icon' => '🔢',
                'description' => 'Test your knowledge of numbers, operations, and algebraic concepts'
            ],
            'Measurement_Geometry' => [
                'title' => 'Measurement and Geometry', 
                'icon' => '📐',
                'description' => 'Test your knowledge of shapes, angles, and spatial relationships'
            ],
            'Data_Probability' => [
                'title' => 'Data and Probability',
                'icon' => '📊', 
                'description' => 'Explore data tables, bar graphs, line plots, mean, and chance events'
            ]
        ];
        
        return $categoryData[$category] ?? [];
    }
    
    /**
     * Show assessment complete page
     */
    public function showComplete($category)
    {
        $user = Auth::guard('student')->user();
        
        // Get the final results from session or database
        $finalResults = session('diagnostic_final_results');
        $pointsEarned = $finalResults['final_master_score'] ?? 0;
        $score = $finalResults['final_master_score'] ?? 0;
        
        // Get correct answers and total questions from database
        $dbCompetency = strtolower(str_replace('_', '_', $category));
        
        // Get the most recent diagnostic session for this user and competency
        $diagnosticSession = DB::table('diagnostic_sessions')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->orderBy('started_at', 'desc')
            ->first();
        
        $correctAnswers = 0;
        $totalQuestions = 15; // Default for diagnostic (5 per phase x 3 phases)
        
        if ($diagnosticSession) {
            // Get question responses for this diagnostic session
            $responses = DB::table('question_responses')
                ->where('assessment_id', $diagnosticSession->session_id)
                ->get();
            
            $totalQuestions = $responses->count();
            $correctAnswers = $responses->where('is_correct', 1)->count();
        }
        
        // Clear the diagnostic results from session
        session()->forget('diagnostic_final_results');
        
        // Create assessment object for the view
        $assessment = (object) [
            'total_points_earned' => round($pointsEarned),
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'accuracy' => $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0
        ];
        
        return view('student.assessment-complete', [
            'category' => $category,
            'assessment' => $assessment,
            'assessment_id' => $diagnosticSession->session_id ?? 'diagnostic_' . time(),
            'from_regular_quiz' => false // This is a diagnostic, not regular quiz
        ]);
    }

    /**
     * Generate dynamic assessments based on student's mastery level
     */
    private function generateAssessments($competency, $currentDifficulty, $userId)
    {
        try {
            // Define question counts per difficulty
            $questionCounts = [
                'beginner' => 15,
                'intermediate' => 20,
                'advanced' => 25
            ];
            
            $questionsPerAssessment = $questionCounts[$currentDifficulty];
            
            // Get available questions for current difficulty
            $availableQuestions = DB::table('questions')
                ->where('competency', $competency)
                ->where('difficulty_level', $currentDifficulty)
                ->where('is_active', 1)
                ->get();
            
            if ($availableQuestions->count() < $questionsPerAssessment) {
                return [
                    'availableQuestions' => 0,
                    'questionsInCooldown' => 0,
                    'assessmentOptions' => []
                ];
            }
            
            // Filter out questions in cooldown (recently answered)
            $questionsNotInCooldown = $this->filterQuestionsInCooldown($availableQuestions, $userId);
            $questionsInCooldown = $availableQuestions->count() - $questionsNotInCooldown->count();
            
            // Generate multiple assessment options if we have enough questions
            $assessmentOptions = [];
            // Calculate how many full assessments we can create with available questions
            // Allow up to 10 assessments maximum to keep UI manageable
            $maxAssessments = 10;
            $possibleAssessments = floor($questionsNotInCooldown->count() / $questionsPerAssessment);
            $assessmentCount = min($maxAssessments, $possibleAssessments);
            
            // Convert collection to array and shuffle it to ensure unique question sets
            $availableQuestionsArray = $questionsNotInCooldown->shuffle()->toArray();
            $questionsUsed = 0;
            
            // Convert collection to array and shuffle it to ensure unique question sets
            $availableQuestionsArray = $questionsNotInCooldown->shuffle()->toArray();
            $questionsUsed = 0;
            
            for ($i = 1; $i <= $assessmentCount; $i++) {
                // Create unique assessment ID
                $assessmentId = "ASSESS_{$userId}_{$competency}_{$currentDifficulty}_{$i}_" . time();
                
                // Get next batch of questions (no overlap between assessments)
                $questionsForThisAssessment = array_slice(
                    $availableQuestionsArray, 
                    $questionsUsed, 
                    $questionsPerAssessment
                );
                $questionsUsed += $questionsPerAssessment;
                
                // Convert array elements back to objects for the Fisher-Yates function
                $questionsAsObjects = array_map(function($questionArray) {
                    return (object) $questionArray;
                }, $questionsForThisAssessment);
                
                // Shuffle questions using Fisher-Yates algorithm
                $shuffledQuestions = $this->shuffleQuestionsWithFisherYates(
                    $questionsAsObjects,
                    $assessmentId
                );
                
                if ($shuffledQuestions['success']) {
                    $assessmentOptions[] = [
                        'assessment_id' => $assessmentId,
                        'title' => ucfirst($currentDifficulty) . " Assessment #$i",
                        'question_count' => $questionsPerAssessment,
                        'time_limit' => $this->getTimeLimitForDifficulty($currentDifficulty),
                        'difficulty' => $currentDifficulty,
                        'topics' => $this->getTopicsForCompetency($competency, $currentDifficulty),
                        'estimated_points' => $questionsPerAssessment * 10, // Add estimated points
                        'best_completion_time' => 'Not attempted', // Add completion time
                        'created_at' => now()
                    ];
                }
            }
            
            return [
                'availableQuestions' => $questionsNotInCooldown->count(),
                'questionsInCooldown' => $questionsInCooldown,
                'assessmentOptions' => $assessmentOptions
            ];
            
        } catch (\Exception $e) {
            Log::error('Assessment generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'availableQuestions' => 0,
                'questionsInCooldown' => 0,
                'assessmentOptions' => []
            ];
        }
    }

    /**
     * Filter out questions that are in cooldown period
     */
    private function filterQuestionsInCooldown($questions, $userId)
    {
        // Get questions in cooldown from question_cooldowns table
        $questionsInCooldown = DB::table('question_cooldowns')
            ->where('user_id', $userId)
            ->where('cooldown_until', '>', now())
            ->pluck('question_id')
            ->toArray();
        
        // If no cooldown table entries, fall back to question_responses with time-based cooldown
        // IMPORTANT: Only include assessment responses, NOT diagnostic responses
        if (empty($questionsInCooldown)) {
            // Get questions answered correctly (30 min cooldown)
            $correctAnswers = DB::table('question_responses')
                ->where('user_id', $userId)
                ->where('assessment_id', 'NOT LIKE', 'DIAG%') // Exclude diagnostic questions
                ->where('assessment_id', 'LIKE', 'ASSESS%') // Only include assessment questions
                ->where('is_correct', 1)
                ->where('answered_at', '>=', now()->subMinutes($this->getCorrectAnswerCooldown()))
                ->pluck('question_id')
                ->toArray();
            
            // Get questions answered incorrectly (60 min cooldown)
            $incorrectAnswers = DB::table('question_responses')
                ->where('user_id', $userId)
                ->where('assessment_id', 'NOT LIKE', 'DIAG%') // Exclude diagnostic questions
                ->where('assessment_id', 'LIKE', 'ASSESS%') // Only include assessment questions
                ->where('is_correct', 0)
                ->where('answered_at', '>=', now()->subMinutes($this->getIncorrectAnswerCooldown()))
                ->pluck('question_id')
                ->toArray();
            
            $questionsInCooldown = array_merge($correctAnswers, $incorrectAnswers);
        }
        
        // Filter out questions in cooldown
        return $questions->filter(function($question) use ($questionsInCooldown) {
            return !in_array($question->question_id, $questionsInCooldown);
        });
    }

    /**
     * Shuffle questions using Fisher-Yates algorithm
     */
    private function shuffleQuestionsWithFisherYates($questions, $assessmentId)
    {
        try {
            // Prepare questions data for Fisher-Yates algorithm
            $questionsData = array_map(function($question) {
                return [
                    'question_id' => $question->question_id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'correct_answer' => $question->correct_answer,
                    'difficulty_level' => $question->difficulty_level,
                    'max_time_seconds' => $question->max_allowed_time ?? 30,
                    'topic' => $question->topic_tag ?? 'General'
                ];
            }, $questions);
            
            // Call Fisher-Yates Python script using temporary file to avoid JSON escaping issues
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $tempFile = storage_path('temp_questions_' . uniqid() . '.json');
            
            // Write questions data to temporary file
            file_put_contents($tempFile, json_encode($questionsData));
            
            $command = "python \"{$scriptPath}\" create_pool_file \"{$assessmentId}\" \"{$tempFile}\"";
            
            $output = shell_exec($command);
            
            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return $result;
            } else {
                Log::error('Fisher-Yates shuffle failed: ' . ($result['message'] ?? 'Unknown error'));
                return ['success' => false, 'message' => 'Shuffle failed'];
            }
            
        } catch (\Exception $e) {
            Log::error('Fisher-Yates shuffle error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Shuffle error'];
        }
    }

    /**
     * Refresh assessments by checking what questions are available after cooldown
     */
    public function refreshAssessments($category)
    {
        $user = Auth::guard('student')->user();
        $userId = $user->id;
        
        // Convert URL format to database format
        $dbSubject = strtolower($category);
        
        // Get user's mastery level for this subject
        $mastery = DB::table('student_mastery')
            ->where('user_id', $userId)
            ->where('competency', $dbSubject)
            ->first();
        
        if (!$mastery || !$mastery->has_taken_diagnostic) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete the diagnostic test first.'
            ]);
        }
        
        // Generate fresh assessments
        $assessmentData = $this->assessmentGenerator->generateAssessments($dbSubject, $mastery->current_difficulty, $userId);
        
        return response()->json([
            'success' => true,
            'data' => $assessmentData,
            'message' => 'Assessments refreshed successfully'
        ]);
    }

    /**
     * Check if enough questions are available for assessment generation
     */
    private function checkAvailableQuestionsForDifficulty($competency, $difficulty, $userId)
    {
        $questionCounts = [
            'beginner' => 15,
            'intermediate' => 20,
            'advanced' => 25
        ];
        
        $required = $questionCounts[$difficulty];
        
        // Get all questions for this difficulty
        $allQuestions = DB::table('questions')
            ->where('competency', $competency)
            ->where('difficulty_level', $difficulty)
            ->where('is_active', 1)
            ->get();
        
        // Filter out questions in cooldown
        $availableQuestions = $this->filterQuestionsInCooldown($allQuestions, $userId);
        
        return [
            'available' => $availableQuestions->count(),
            'required' => $required,
            'can_generate' => $availableQuestions->count() >= $required,
            'in_cooldown' => $allQuestions->count() - $availableQuestions->count()
        ];
    }

    /**
     * Get cooldown time for correct answers (in minutes)
     * Can be easily modified here to change cooldown behavior
     */
    private function getCorrectAnswerCooldown()
    {
        return 30; // 30 minutes for correct answers
    }

    /**
     * Get cooldown time for incorrect answers (in minutes)
     * Can be easily modified here to change cooldown behavior
     */
    private function getIncorrectAnswerCooldown()
    {
        return 60; // 60 minutes for incorrect answers
    }

    /**
     * Get time limit based on difficulty
     */
    private function getTimeLimitForDifficulty($difficulty)
    {
        $timeLimits = [
            'beginner' => 25,      // 25 minutes for 15 questions
            'intermediate' => 35,   // 35 minutes for 20 questions  
            'advanced' => 45        // 45 minutes for 25 questions
        ];
        
        return $timeLimits[$difficulty] ?? 30;
    }

    /**
     * Get topics covered for a competency and difficulty
     */
    private function getTopicsForCompetency($competency, $difficulty)
    {
        // Get unique topics for this competency and difficulty
        $topics = DB::table('questions')
            ->where('competency', $competency)
            ->where('difficulty_level', $difficulty)
            ->where('is_active', 1)
            ->distinct()
            ->pluck('topic_tag')
            ->filter()
            ->take(3) // Show max 3 topics
            ->toArray();
            
        return $topics ?: ['General Mathematics'];
    }

    /**
     * Show assessment review page
     */
    public function showReview($category)
    {
        $user = Auth::guard('student')->user();
        
        // Validate category
        $validCategories = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('student.assessments')->with('error', 'Invalid assessment category.');
        }
        
        // Convert to database format
        $dbCompetency = strtolower(str_replace('_', '_', $category));
        
        // Get the most recent completed assessment for this user and competency
        $latestAssessment = DB::table('assessments')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->first();
        
        // If no regular assessment found, check for diagnostic session
        if (!$latestAssessment) {
            $diagnosticSession = DB::table('diagnostic_sessions')
                ->where('user_id', $user->id)
                ->where('competency', $dbCompetency)
                ->orderBy('started_at', 'desc')
                ->first();
            
            if ($diagnosticSession) {
                $assessmentId = $diagnosticSession->session_id;
                $assessmentType = 'diagnostic';
            } else {
                return redirect()->route('student.assessments.category', $category)
                    ->with('error', 'No assessment found to review.');
            }
        } else {
            $assessmentId = $latestAssessment->assessment_id;
            $assessmentType = $latestAssessment->assessment_type ?? 'regular';
        }
        
        // Get detailed question responses with question data
        $questionResponses = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->where('question_responses.assessment_id', $assessmentId)
            ->select([
                'question_responses.*',
                'questions.question_text',
                'questions.question_type',
                'questions.choice_a',
                'questions.choice_b', 
                'questions.choice_c',
                'questions.choice_d',
                'questions.correct_answer',
                'questions.explanation',
                'questions.difficulty_level',
                'questions.topic_tag as topic'
            ])
            ->orderBy('question_responses.answered_at')
            ->get();
        
        // Calculate summary statistics
        $totalQuestions = $questionResponses->count();
        $correctAnswers = $questionResponses->where('is_correct', 1)->count();
        $scorePercentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        
        // Group questions by difficulty for additional analysis
        $questionsByDifficulty = $questionResponses->groupBy('difficulty_level');

        // Get user's current mastery data for this competency
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->first();

        return view('student.assessment-review', [
            'category' => $category,
            'questionResponses' => $questionResponses,
            'totalQuestions' => $totalQuestions,
            'correctAnswers' => $correctAnswers,
            'scorePercentage' => $scorePercentage,
            'questionsByDifficulty' => $questionsByDifficulty,
            'assessmentType' => $assessmentType,
            'assessmentData' => $latestAssessment,
            'mastery' => $mastery
        ]);
    }
    
    /**
     * Check for active diagnostic sessions for a user
     */
    public function checkActiveDiagnostics(Request $request)
    {
        $request->validate([
            'category' => 'required|string'
        ]);
        
        $user = Auth::guard('student')->user();
        $category = $request->category;
        $dbCompetency = strtolower($category);
        
        try {
            $activeDiagnosticData = [];
            
            // Check for active diagnostic sessions across ALL competencies (not just the requested one)
            // This prevents starting a new diagnostic when any diagnostic is in progress
            $activeDiagnostics = DB::table('diagnostic_sessions')
                ->where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->where('started_at', '>=', now()->subDays(1)) // Check diagnostics from last 24 hours
                ->orderBy('started_at', 'desc')
                ->get();
            
            foreach ($activeDiagnostics as $diagnostic) {
                // Get phase information
                $phaseNames = [1 => 'Beginner', 2 => 'Intermediate', 3 => 'Advanced'];
                $currentPhaseName = $phaseNames[$diagnostic->current_phase] ?? 'Unknown';
                
                // Calculate progress (each phase has 5 questions)
                $questionsPerPhase = 5;
                $completedPhases = max(0, $diagnostic->current_phase - 1);
                $estimatedProgress = $completedPhases * $questionsPerPhase;
                
                // Get actual response count for current phase
                $responseCount = DB::table('question_responses')
                    ->where('assessment_id', $diagnostic->session_id)
                    ->count();
                
                $actualProgress = $responseCount;
                $totalQuestions = 15; // 3 phases × 5 questions each
                
                // Convert competency back to display format
                $displayCategory = ucfirst(str_replace('_', ' & ', $diagnostic->competency));
                
                $activeDiagnosticData[] = [
                    'session_id' => $diagnostic->session_id,
                    'type' => 'diagnostic',
                    'progress' => $actualProgress,
                    'total_questions' => $totalQuestions,
                    'started_at' => $diagnostic->started_at,
                    'current_phase' => $diagnostic->current_phase,
                    'phase_name' => $currentPhaseName,
                    'competency' => $diagnostic->competency,
                    'title' => $displayCategory . ' Diagnostic',
                    'can_resume' => true
                ];
            }
            
            return response()->json([
                'success' => true,
                'has_active_diagnostics' => !empty($activeDiagnosticData),
                'active_diagnostics' => $activeDiagnosticData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking active diagnostics', [
                'user_id' => $user->id,
                'category' => $category,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'has_active_diagnostics' => false,
                'active_diagnostics' => [],
                'message' => 'Error checking for active diagnostics'
            ]);
        }
    }

    /**
     * Clear diagnostic session when user navigates away
     */
    public function clearDiagnosticSession(Request $request)
    {
        try {
            $sessionId = session('diagnostic_session_id');
            $competency = session('diagnostic_competency');
            
            if ($sessionId && $competency) {
                // Call Python script to cleanup diagnostic session
                try {
                    $result = $this->executePythonScript('bkt_algorithm.py', [
                        'cleanup_diagnostic',
                        $sessionId
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to cleanup diagnostic session via Python script', [
                        'session_id' => $sessionId,
                        'error' => $e->getMessage()
                    ]);
                    $result = null;
                }
                
                // Clear session data regardless of Python script result
                session()->forget([
                    'diagnostic_session_id', 
                    'diagnostic_competency', 
                    'diagnostic_phase', 
                    'diagnostic_questions', 
                    'current_question_index'
                ]);
                
                Log::info("Diagnostic session cleared for abandonment", [
                    'session_id' => $sessionId,
                    'competency' => $competency,
                    'user_id' => Auth::guard('student')->id(),
                    'cleanup_result' => $result
                ]);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Failed to clear diagnostic session: ' . $e->getMessage());
            
            // Still clear session data to prevent stuck sessions
            session()->forget([
                'diagnostic_session_id', 
                'diagnostic_competency', 
                'diagnostic_phase', 
                'diagnostic_questions', 
                'current_question_index'
            ]);
            
            return response()->json(['success' => true]); // Return success to avoid blocking user
        }
    }
}