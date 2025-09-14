<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Show assessments page
     */
    public function index()
    {
        $user = Auth::user();
        
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
        $competencies = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        
        foreach ($competencies as $competency) {
            $dbCompetency = strtolower(str_replace('_', '_', $competency));
            
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
            'masteryData' => $masteryData
        ]);
    }
    
    /**
     * Show assessment category page
     */
    public function showCategory($category)
    {
        $user = Auth::user();
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
        $dbCompetency = strtolower(str_replace('_', '_', $category));
        
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
        
        // Get available assessment options
        $availableQuestions = 15; // Default, will be calculated from available questions
        $questionsInCooldown = 0; // Default, will be calculated from cooldown questions
        $assessmentOptions = []; // Default empty array
        
        // You can add logic here to calculate actual available questions
        // For now, providing default values to prevent the error
        
        return view('student.assessment-list', [
            'student' => $student,
            'category' => $category,
            'mastery' => $mastery,
            'data' => $categoryData,
            'availableQuestions' => $availableQuestions,
            'questionsInCooldown' => $questionsInCooldown,
            'assessmentOptions' => $assessmentOptions
        ]);
    }
    
    /**
     * Start diagnostic assessment
     */
    public function startDiagnostic($category)
    {
        $user = Auth::user();
        
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
        
        // Check for existing incomplete diagnostic session
        $existingSession = DB::table('diagnostic_sessions')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->where('status', 'in_progress')
            ->orderBy('started_at', 'desc')
            ->first();
        
        if ($existingSession) {
            // Check for saved progress in quiz_progress table
            $savedProgress = DB::table('quiz_progress')
                ->where('user_id', $user->id)
                ->where('session_id', $existingSession->session_id)
                ->orderBy('question_index', 'desc')
                ->first();
            
            if ($savedProgress) {
                // Resume from saved progress
                Log::info("Resuming diagnostic session for user {$user->id}, competency {$dbCompetency}");
                
                return $this->resumeDiagnosticSession($existingSession, $savedProgress, $category);
            } else {
                // Session exists but no saved progress - resume from session data
                Log::info("Resuming diagnostic session without saved progress for user {$user->id}, competency {$dbCompetency}");
                
                // Get the current state from the database
                $answeredQuestions = DB::table('question_responses')
                    ->where('assessment_id', $existingSession->session_id)
                    ->orderBy('answered_at')
                    ->get();
                
                $currentQuestionIndex = $answeredQuestions->count();
                
                // If no questions answered yet, start from beginning
                if ($currentQuestionIndex == 0) {
                    // Get initial questions from database
                    try {
                        $questions = DB::table('questions')
                            ->where('competency', $dbCompetency)
                            ->where('difficulty_level', 'beginner')
                            ->where('is_active', 1)
                            ->inRandomOrder()
                            ->limit(5)
                            ->get([
                                'question_id', 'question_text', 'question_type', 'correct_answer', 
                                'explanation', 'difficulty_level', 'max_allowed_time', 'topic_tag as topic',
                                'choice_a', 'choice_b', 'choice_c', 'choice_d'
                            ])
                            ->toArray();
                        
                        if (!empty($questions)) {
                            // Convert to array format expected by frontend
                            $questionsArray = array_map(function($q) {
                                $questionArray = (array) $q;
                                $questionArray['options'] = json_encode([
                                    'A' => $questionArray['choice_a'],
                                    'B' => $questionArray['choice_b'], 
                                    'C' => $questionArray['choice_c'],
                                    'D' => $questionArray['choice_d']
                                ]);
                                $questionArray['max_time_seconds'] = $questionArray['max_allowed_time'] ?? 30;
                                return $questionArray;
                            }, $questions);
                            
                            session([
                                'diagnostic_session_id' => $existingSession->session_id,
                                'diagnostic_competency' => $category,
                                'diagnostic_phase' => 1,
                                'diagnostic_questions' => $questionsArray,
                                'current_question_index' => 0
                            ]);
                            
                            return redirect()->route('student.quiz.show', $category)
                                ->with('diagnostic_mode', true)
                                ->with('success', 'Continuing your diagnostic assessment.');
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to get initial questions for existing session: ' . $e->getMessage());
                    }
                }
                
                // If some questions answered, use resume method
                return $this->resumeDiagnosticSession($existingSession, null, $category);
            }
        }
        
        try {
            // Call Python script to start diagnostic
            $scriptPath = base_path('public/algorithm/bkt_algorithm.py');
            $command = "python \"{$scriptPath}\" start_diagnostic {$user->id} {$dbCompetency}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
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
                throw new \Exception($result['message'] ?? 'Failed to start diagnostic');
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
        
        $user = Auth::user();
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
            $userId = Auth::id();
            $competency = session('diagnostic_competency');
            
            // Record answer via Python script
            $scriptPath = base_path('public/algorithm/bkt_algorithm.py');
            $command = "python \"{$scriptPath}\" record_answer {$userId} {$competency} {$sessionId} {$request->question_id} \"{$request->answer}\" {$request->time_taken} " . ($isCorrect ? 'true' : 'false');
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
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
            $userId = Auth::id();
            $competency = session('diagnostic_competency');
            
            if (!$userId || !$competency) {
                Log::error('Missing user ID or competency for diagnostic phase completion');
                return ['success' => false, 'message' => 'Missing session data'];
            }
            
            $scriptPath = base_path('public/algorithm/bkt_algorithm.py');
            $command = "python \"{$scriptPath}\" complete_phase {$userId} {$competency} {$sessionId}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            // Check if result is valid and has required keys
            if (!$result || !isset($result['success'])) {
                Log::error('Invalid response from Python script: ' . $output);
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
        $user = Auth::user();
        
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
        
        return view('student.assessment-complete', [
            'category' => $category,
            'pointsEarned' => round($pointsEarned),
            'score' => round($score),
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions,
            'assessmentId' => 1 // You can modify this based on your needs
        ]);
    }

    /**
     * Show assessment review page
     */
    public function showReview($category)
    {
        $user = Auth::user();
        
        // Validate category
        $validCategories = ['Number_Algebra', 'Measurement_Geometry', 'Data_Probability'];
        if (!in_array($category, $validCategories)) {
            return redirect()->route('student.assessments')->with('error', 'Invalid assessment category.');
        }
        
        // Convert to database format
        $dbCompetency = strtolower(str_replace('_', '_', $category));
        
        // Get the most recent diagnostic session for this user and competency
        $diagnosticSession = DB::table('diagnostic_sessions')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->orderBy('started_at', 'desc')
            ->first();
        
        if (!$diagnosticSession) {
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'No assessment found to review.');
        }
        
        // Get detailed question responses with question data
        $questionResponses = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->where('question_responses.assessment_id', $diagnosticSession->session_id)
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
        
        return view('student.assessment-review', [
            'category' => $category,
            'questionResponses' => $questionResponses,
            'totalQuestions' => $totalQuestions,
            'correctAnswers' => $correctAnswers,
            'scorePercentage' => $scorePercentage,
            'questionsByDifficulty' => $questionsByDifficulty
        ]);
    }

    /**
     * Save quiz progress for restoration
     */
    public function saveProgress(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'question_id' => 'required|string',
            'current_answer' => 'nullable|string',
            'time_taken' => 'required|integer|min:0',
            'question_index' => 'required|integer|min:0'
        ]);

        $user = Auth::user();

        try {
            // Save or update progress in database
            DB::table('quiz_progress')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'session_id' => $request->session_id,
                    'question_id' => $request->question_id
                ],
                [
                    'current_answer' => $request->current_answer,
                    'time_taken' => $request->time_taken,
                    'question_index' => $request->question_index,
                    'saved_at' => now(),
                    'updated_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save quiz progress: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress'
            ], 500);
        }
    }

    /**
     * Get saved progress for a quiz session
     */
    public function getProgress($sessionId, $questionId)
    {
        $user = Auth::user();

        try {
            $progress = DB::table('quiz_progress')
                ->where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->where('question_id', $questionId)
                ->first();

            if ($progress) {
                return response()->json([
                    'success' => true,
                    'progress' => [
                        'current_answer' => $progress->current_answer,
                        'time_taken' => $progress->time_taken,
                        'question_index' => $progress->question_index,
                        'saved_at' => $progress->saved_at
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No progress found'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to get quiz progress: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve progress'
            ], 500);
        }
    }

    /**
     * Clear progress after successful completion
     */
    public function clearProgress($sessionId)
    {
        $user = Auth::user();

        try {
            DB::table('quiz_progress')
                ->where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Progress cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear quiz progress: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear progress'
            ], 500);
        }
    }

    /**
     * Resume diagnostic session from saved progress
     */
    private function resumeDiagnosticSession($existingSession, $savedProgress, $category)
    {
        $user = Auth::user();
        
        try {
            // Get all answered questions for this session to determine current question index
            $answeredQuestions = DB::table('question_responses')
                ->where('assessment_id', $existingSession->session_id)
                ->orderBy('answered_at')
                ->get();
            
            $currentQuestionIndex = $answeredQuestions->count();
            
            // Determine current phase and difficulty based on questions answered
            $currentPhase = 1;
            $difficulty = 'beginner';
            if ($currentQuestionIndex >= 5) {
                $currentPhase = 2;
                $difficulty = 'intermediate';
            }
            if ($currentQuestionIndex >= 10) {
                $currentPhase = 3;
                $difficulty = 'advanced';
            }
            
            // Get questions for the current phase directly from database
            $dbCompetency = strtolower(str_replace('_', '_', $category));
            $questions = DB::table('questions')
                ->where('competency', $dbCompetency)
                ->where('difficulty_level', $difficulty)
                ->where('is_active', 1)
                ->inRandomOrder()
                ->limit(5)
                ->get([
                    'question_id', 'question_text', 'question_type', 'correct_answer', 
                    'explanation', 'difficulty_level', 'max_allowed_time', 'topic_tag as topic',
                    'choice_a', 'choice_b', 'choice_c', 'choice_d'
                ])
                ->toArray();
            
            if (empty($questions)) {
                Log::warning("No questions found for competency {$dbCompetency}, difficulty {$difficulty}");
                return $this->startNewDiagnostic($user->id, $dbCompetency, $category);
            }
            
            // Convert to array format expected by frontend
            $questionsArray = array_map(function($q) {
                $questionArray = (array) $q;
                // Add options array for frontend compatibility
                $questionArray['options'] = json_encode([
                    'A' => $questionArray['choice_a'],
                    'B' => $questionArray['choice_b'], 
                    'C' => $questionArray['choice_c'],
                    'D' => $questionArray['choice_d']
                ]);
                // Add max_time_seconds for frontend compatibility
                $questionArray['max_time_seconds'] = $questionArray['max_allowed_time'] ?? 30;
                return $questionArray;
            }, $questions);
            
            // Calculate the index within the current phase
            $phaseQuestionIndex = $currentQuestionIndex % 5;
            
            // Store session data for resumption
            session([
                'diagnostic_session_id' => $existingSession->session_id,
                'diagnostic_competency' => $category,
                'diagnostic_phase' => $currentPhase,
                'diagnostic_questions' => $questionsArray,
                'current_question_index' => $phaseQuestionIndex
            ]);
            
            $message = $savedProgress ? 
                'Resuming your diagnostic assessment from where you left off.' :
                'Continuing your diagnostic assessment.';
            
            return redirect()->route('student.quiz.show', $category)
                ->with('diagnostic_mode', true)
                ->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Failed to resume diagnostic session: ' . $e->getMessage());
            
            // Fallback: start new diagnostic by clearing existing session
            return $this->startNewDiagnostic($user->id, $dbCompetency, $category);
        }
    }

    /**
     * Start a new diagnostic session (helper method)
     */
    private function startNewDiagnostic($userId, $dbCompetency, $category)
    {
        try {
            // First, clear any existing incomplete sessions for this competency
            DB::table('diagnostic_sessions')
                ->where('user_id', $userId)
                ->where('competency', $dbCompetency)
                ->where('status', 'in_progress')
                ->update(['status' => 'abandoned']);
            
            // Clear any saved progress for abandoned sessions
            DB::table('quiz_progress')
                ->where('user_id', $userId)
                ->whereIn('session_id', function($query) use ($userId, $dbCompetency) {
                    $query->select('session_id')
                          ->from('diagnostic_sessions')
                          ->where('user_id', $userId)
                          ->where('competency', $dbCompetency)
                          ->where('status', 'abandoned');
                })
                ->delete();
            
            $scriptPath = base_path('public/algorithm/bkt_algorithm.py');
            $command = "python \"{$scriptPath}\" start_diagnostic {$userId} {$dbCompetency}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
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
                throw new \Exception($result['message'] ?? 'Failed to start diagnostic');
            }
            
        } catch (\Exception $e) {
            Log::error('New diagnostic start failed: ' . $e->getMessage());
            
            return redirect()->route('student.assessments')
                ->with('error', 'Failed to start diagnostic. Please try again.');
        }
    }
}