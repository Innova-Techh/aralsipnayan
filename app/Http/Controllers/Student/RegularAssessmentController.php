<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\AssessmentGenerationController;
use App\Http\Controllers\Student\GamificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegularAssessmentController extends Controller
{
    protected $assessmentGenerator;
    protected $gamificationController;

    // Max allowed time per difficulty (seconds) - matching bkt_algorithm.py
    private $maxTimesPerDifficulty = [
        'beginner' => 30,
        'intermediate' => 45,
        'advanced' => 60
    ];

    public function __construct()
    {
        $this->assessmentGenerator = new AssessmentGenerationController();
        $this->gamificationController = new GamificationController();
    }

    /**
     * Start a regular assessment session
     */
   public function startAssessment(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string',
            'category' => 'required|string',
        ]);

        $user = Auth::guard('student')->user();
        $assessmentId = $request->assessment_id;
        $category = $request->category;
        
        // Convert category format to database format
        $dbCompetency = strtolower($category);
        
        // Get user's mastery level
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->first();
        
        if (!$mastery || !$mastery->has_taken_diagnostic) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete the diagnostic test first.',
                'redirect' => route('student.quiz.diagnostic', $category)
            ]);
        }
        
        try {
            // First, check if there's already an active assessment for this user and category
            $existingAssessment = DB::table('assessments')
                ->where('user_id', $user->id)
                ->where('competency', $dbCompetency)
                ->where('status', 'in_progress')
                ->where('started_at', '>=', now()->subHours(2)) // Only check recent assessments
                ->orderBy('started_at', 'desc')
                ->first();
            
            if ($existingAssessment) {
                // Check if this assessment has an active session
                $existingSession = DB::table('assessment_sessions')
                    ->where('assessment_id', $existingAssessment->assessment_id)
                    ->where('user_id', $user->id)
                    ->where('status', 'in_progress')
                    ->first();
                
                if ($existingSession) {
                    // Return the existing assessment instead of creating a new one
                    $redirectUrl = route('student.quiz.show', $category) . '?assessment_id=' . $existingAssessment->assessment_id;
                    $redirectUrl .= '&session_id=' . $existingSession->session_id;
                    
                    return response()->json([
                        'success' => true,
                        'assessment_exists' => true,
                        'assessment_id' => $existingAssessment->assessment_id,
                        'session_id' => $existingSession->session_id,
                        'question_count' => $existingAssessment->total_questions,
                        'time_limit' => $existingAssessment->time_limit,
                        'redirect' => $redirectUrl,
                        'message' => 'Resuming existing assessment'
                    ]);
                }
            }
            
            // Extract difficulty from assessment_id
            $difficulty = $mastery->current_difficulty;
            
            // Create actual assessment using the generator
            $result = $this->assessmentGenerator->createActualAssessment(
                $assessmentId,
                $user->id,
                $dbCompetency,
                $difficulty
            );
            
            if ($result['success']) {
                // Use the actual assessment ID from the result (which may be different from the passed ID)
                $actualAssessmentId = $result['assessment']->assessment_id ?? $assessmentId;
                $sessionId = $result['session_id'] ?? null;
                
                $redirectUrl = route('student.quiz.show', $category) . '?assessment_id=' . $actualAssessmentId;
                if ($sessionId) {
                    $redirectUrl .= '&session_id=' . $sessionId;
                }
                
                return response()->json([
                    'success' => true,
                    'assessment_exists' => $result['exists'],
                    'assessment_id' => $actualAssessmentId,
                    'session_id' => $sessionId,
                    'question_count' => $result['question_count'] ?? $result['assessment']->total_questions,
                    'time_limit' => $result['time_limit'] ?? $result['assessment']->time_limit,
                    'redirect' => $redirectUrl
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to create assessment'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to start regular assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'category' => $category,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to start assessment. Please try again.'
            ]);
        }
    }
    
    /**
     * Create assessment using Fisher-Yates algorithm
     */
    private function createAssessmentWithFisherYates($assessmentId, $userId, $competency, $difficulty)
    {
        try {
            // Define question counts per difficulty
            $questionCounts = [
                'beginner' => 15,
                'intermediate' => 20,
                'advanced' => 25
            ];
            
            $questionCount = $questionCounts[$difficulty] ?? 15;
            
            // Call Fisher-Yates Python script to create assessment pool
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" create_pool \"{$assessmentId}\" {$userId} \"{$competency}\" \"{$difficulty}\" {$questionCount}";
            
            Log::info("Creating assessment with Fisher-Yates", [
                'command' => $command,
                'assessment_id' => $assessmentId
            ]);
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                // Create assessment record in Laravel
                $timeLimit = $this->getTimeLimitForDifficulty($difficulty);
                
                // Get initial BKT score from student_mastery
                $mastery = DB::table('student_mastery')
                    ->where('user_id', $userId)
                    ->where('competency', $competency)
                    ->first();
                
                $initialBktScore = $mastery ? $mastery->bkt_score : 0.5;
                
                DB::table('assessments')->insert([
                    'assessment_id' => $assessmentId,
                    'user_id' => $userId,
                    'competency' => $competency,
                    'assessment_type' => 'regular',
                    'difficulty_level' => $difficulty,
                    'total_questions' => $result['total_questions'],
                    'time_limit' => $timeLimit,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'is_diagnostic_phase' => 0,
                    'correct_answers' => 0,
                    'incorrect_answers' => 0,
                    'questions_answered' => 0,
                    'total_time_spent' => 0,
                    'cumulative_time_score' => 0.0000,
                    'bkt_score_before' => $initialBktScore,
                    'initial_bkt_probability' => $initialBktScore
                ]);
                
                Log::info("Assessment created successfully", [
                    'assessment_id' => $assessmentId,
                    'question_count' => $result['total_questions']
                ]);
                
                return [
                    'success' => true,
                    'question_count' => $result['total_questions'],
                    'time_limit' => $timeLimit,
                    'excluded_cooldown' => $result['excluded_cooldown'] ?? 0,
                    'excluded_recent' => $result['excluded_recent'] ?? 0
                ];
            } else {
                Log::error("Fisher-Yates assessment creation failed", [
                    'assessment_id' => $assessmentId,
                    'error' => $result['message'] ?? 'Unknown error',
                    'output' => $output
                ]);
                
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to create question pool'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Fisher-Yates assessment creation error: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return [
                'success' => false,
                'message' => 'Assessment creation error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get next question from assessment
     */
    public function getNextQuestion(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found or already completed'
            ]);
        }
        
        try {
            // Get next question using Fisher-Yates algorithm
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" next_question \"{$assessmentId}\"";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success'] && isset($result['question'])) {
                $question = $result['question'];
                
                // Format question for frontend
                $formattedQuestion = $this->formatQuestionForFrontend($question);
                
                // Get progress
                $progress = $this->getAssessmentProgress($assessmentId);
                
                return response()->json([
                    'success' => true,
                    'question' => $formattedQuestion,
                    'progress' => $progress
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No more questions available'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to get next question: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get next question'
            ]);
        }
    }
    
    /**
     * Submit answer and update assessment
     */
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string',
            'question_id' => 'required|string',
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:1'
        ]);
        
        $user = Auth::guard('student')->user();
        $assessmentId = $request->assessment_id;
        $questionId = $request->question_id;
        $userAnswer = $request->answer;
        $timeTaken = $request->time_taken;
        
        try {
            // Get question details
            $question = DB::table('questions')
                ->where('question_id', $questionId)
                ->first();
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found'
                ]);
            }
            
            $isCorrect = $userAnswer === $question->correct_answer;
            
            // Get assessment details
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found'
                ]);
            }
            
            // Calculate scores using difficulty-based max time
            $maxAllowedTime = $this->getMaxTimeForDifficulty($assessment->difficulty_level);
            $normalizedTime = $timeTaken / $maxAllowedTime;
            $timeScore = $this->calculateTimeScore($normalizedTime, $isCorrect);
            
            // Record gamification points using new system
            $gamificationResult = $this->gamificationController->recordQuestionPoints(
                $user->id,
                $assessmentId,
                $questionId,
                $isCorrect,
                $timeTaken,
                $maxAllowedTime,
                $assessment->difficulty_level,
                $assessment->competency
            );
            
            // Use gamification points for consistency
            $basePoints = $gamificationResult['success'] ? $gamificationResult['base_points'] : $this->getBasePoints($assessment->difficulty_level);
            $bonusPoints = $gamificationResult['success'] ? $gamificationResult['time_bonus'] : ($isCorrect ? $this->getTimeBonusPoints($normalizedTime) : 0);
            $totalPoints = $gamificationResult['success'] ? $gamificationResult['total_points'] : ($basePoints + $bonusPoints);
            
            // Get BKT score before answering - chain from previous question in THIS assessment
            // This ensures proper sequential BKT progression: L0 → L1 → L2 → ... → Ln
            $previousResponse = DB::table('question_responses')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->orderBy('answered_at', 'desc')
                ->first();

            if ($previousResponse) {
                // Use the previous question's bkt_after as this question's bkt_before
                $bktBefore = $previousResponse->bkt_after;
            } else {
                // First question in assessment - use initial BKT from student_mastery
                $mastery = DB::table('student_mastery')
                    ->where('user_id', $user->id)
                    ->where('competency', $assessment->competency)
                    ->first();
                $bktBefore = $mastery ? $mastery->bkt_score : 0.5;
            }

            // Calculate what the BKT score would be after this question (for tracking)
            $bktAfter = $this->calculateBKTUpdate($bktBefore, $isCorrect, $timeScore, $assessment->difficulty_level);

            // Calculate cumulative time factor for this response
            $ftimeFactor = $this->calculateCurrentTimeFactor($user->id, $assessment->competency, $timeScore);
            
            // Record response with BKT tracking
            $responseId = $this->generateResponseId($assessmentId, $questionId);
            
            DB::table('question_responses')->insert([
                'response_id' => $responseId,
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'question_id' => $questionId,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'response_time' => $timeTaken,
                'max_allowed_time' => $maxAllowedTime,
                'normalized_time' => $normalizedTime,
                'time_score' => $timeScore,
                'bkt_before' => $bktBefore,
                'bkt_after' => $bktAfter,
                'difficulty_factor' => $this->getDifficultyFactor($assessment->difficulty_level),
                'time_factor' => $timeScore,
                'ftime_factor' => $ftimeFactor,
                'base_points' => $basePoints,
                'time_bonus_points' => $bonusPoints,
                'total_points' => $totalPoints,
                'answered_at' => now()
            ]);
            
            // Create cooldown entry
            $this->createCooldownEntry($user->id, $questionId, $assessment->competency, $isCorrect, $timeTaken);
            
            // Mark question as answered in Fisher-Yates
            $this->markQuestionAnswered($assessmentId, $questionId);
            
            // Update assessment statistics
            $this->updateAssessmentStats($assessmentId, $isCorrect, $timeTaken, $timeScore);
            
            // Check if assessment is complete
            $progress = $this->getAssessmentProgress($assessmentId);
            $isComplete = (int)$progress['answered_questions'] >= (int)$progress['total_questions'];
            
            // Get updated gamification status
            $gamificationStatus = $this->gamificationController->getUserGamificationStatus($user->id);
            
            $responseData = [
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
                'points_earned' => $totalPoints,
                'base_points' => $basePoints,
                'bonus_points' => $bonusPoints,
                'assessment_complete' => $isComplete,
                'progress' => $progress,
                'gamification' => [
                    'level_up' => $gamificationResult['success'] && isset($gamificationResult['level_up']) ? $gamificationResult['level_up'] : false,
                    'rank_up' => $gamificationResult['success'] && isset($gamificationResult['rank_up']) ? $gamificationResult['rank_up'] : false,
                    'current_level' => $gamificationStatus['current_level'],
                    'current_rank' => $gamificationStatus['current_rank'],
                    'total_points' => $gamificationStatus['total_points'],
                    'streak' => $gamificationStatus['current_streak']
                ]
            ];
            
            if ($isComplete) {
                $completionResult = $this->completeAssessment($assessmentId);
                
                // Record assessment completion bonus
                if ($completionResult && isset($completionResult['accuracy'])) {
                    $completionBonus = $this->gamificationController->recordAssessmentCompletionBonus(
                        $user->id,
                        $assessmentId,
                        $assessment->competency,
                        $completionResult['accuracy'],
                        $progress['answered_questions']
                    );
                    
                    if ($completionBonus > 0) {
                        $responseData['completion_bonus'] = $completionBonus;
                    }
                }
                
                // Add redirect URL for completed assessment
                $categoryForUrl = $assessment->competency;
                $responseData['redirect_url'] = route('student.results.complete', $categoryForUrl) . '?assessment_id=' . $assessmentId;
            }
            
            return response()->json($responseData);
            
        } catch (\Exception $e) {
            Log::error('Failed to submit answer: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId,
                'question_id' => $questionId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit answer'
            ]);
        }
    }
    
    /**
     * Get assessment progress
     */
    public function getProgress(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found'
            ]);
        }
        
        try {
            $progress = $this->getAssessmentProgress($assessmentId);
            
            return response()->json([
                'success' => true,
                'progress' => $progress
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get assessment progress: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get progress'
            ]);
        }
    }
    
    /**
     * Validate assessment before starting
     */
    public function validateAssessment(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Check if assessment exists and belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found or access denied.'
            ]);
        }
        
        // Check if assessment has questions
        $questionCount = DB::table('assessment_questions')
            ->where('assessment_id', $assessmentId)
            ->count();
        
        if ($questionCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment has no questions. Please refresh and try again.'
            ]);
        }
        
        // Check time limit
        $timeLimit = ($assessment->time_limit ?? 30) * 60;
        $elapsedTime = time() - strtotime($assessment->started_at);
        
        if ($elapsedTime > $timeLimit && $assessment->status === 'in_progress') {
            // Auto-complete due to timeout
            DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                
            return response()->json([
                'success' => false,
                'message' => 'Assessment has expired due to time limit.',
                'timeout' => true
            ]);
        }
        
        return response()->json([
            'success' => true,
            'assessment' => [
                'assessment_id' => $assessment->assessment_id,
                'competency' => $assessment->competency,
                'difficulty_level' => $assessment->difficulty_level,
                'total_questions' => $assessment->total_questions,
                'time_limit' => $assessment->time_limit,
                'status' => $assessment->status,
                'started_at' => $assessment->started_at
            ],
            'question_count' => $questionCount,
            'time_remaining' => max(0, $timeLimit - $elapsedTime)
        ]);
    }
    
    // Helper Methods
    
    /**
     * Get time limit based on difficulty
     */
    private function getTimeLimitForDifficulty($difficulty)
    {
        // All assessments have 30-minute time duration as requested
        $timeLimits = [
            'beginner' => 30,      // 30 minutes for 15 questions
            'intermediate' => 30,   // 30 minutes for 20 questions
            'advanced' => 30        // 30 minutes for 25 questions
        ];

        return $timeLimits[$difficulty] ?? 30;
    }

    /**
     * Get max allowed time per question based on difficulty
     */
    private function getMaxTimeForDifficulty($difficulty)
    {
        return $this->maxTimesPerDifficulty[$difficulty] ?? 30;
    }
    
    /**
     * Format question for frontend
     */
    private function formatQuestionForFrontend($question)
    {
        // Convert individual choice fields to options array
        $options = [];
        if (!empty($question['choice_a'])) $options[0] = $question['choice_a'];
        if (!empty($question['choice_b'])) $options[1] = $question['choice_b'];
        if (!empty($question['choice_c'])) $options[2] = $question['choice_c'];
        if (!empty($question['choice_d'])) $options[3] = $question['choice_d'];

        $difficulty = $question['difficulty_level'] ?? 'beginner';
        $maxTime = $this->getMaxTimeForDifficulty($difficulty);

        return [
            'question_id' => $question['question_id'],
            'text' => $question['question_text'],
            'type' => $question['question_type'] ?? 'multiple_choice',
            'options' => $options,
            'max_time' => $maxTime,
            'difficulty_level' => $difficulty,
            'topic' => $question['topic_tag'] ?? '',
            'explanation' => $question['explanation'] ?? '',
            'correct_answer' => $question['correct_answer'] ?? ''
        ];
    }
    
    /**
     * Get assessment progress from Fisher-Yates
     */
    private function getAssessmentProgress($assessmentId)
    {
        try {
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" progress \"{$assessmentId}\"";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return $result['progress'];
            }
            
            // Fallback to database query
            return DB::table('assessment_questions')
                ->where('assessment_id', $assessmentId)
                ->selectRaw('
                    COUNT(*) as total_questions,
                    SUM(CASE WHEN is_answered = TRUE THEN 1 ELSE 0 END) as answered_questions,
                    MIN(CASE WHEN is_current = TRUE THEN question_order ELSE NULL END) as current_question_number
                ')
                ->first();
            
        } catch (\Exception $e) {
            Log::error('Failed to get assessment progress: ' . $e->getMessage());
            return [
                'total_questions' => 15, // Default fallback, actual count determined by assessment
                'answered_questions' => 0,
                'current_question_number' => 1
            ];
        }
    }
    
    /**
     * Mark question as answered in Fisher-Yates
     */
    private function markQuestionAnswered($assessmentId, $questionId)
    {
        try {
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" mark_answered \"{$assessmentId}\" \"{$questionId}\"";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if (!$result || !$result['success']) {
                Log::warning('Failed to mark question answered in Fisher-Yates', [
                    'assessment_id' => $assessmentId,
                    'question_id' => $questionId
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error marking question answered: ' . $e->getMessage());
        }
    }
    
    /**
     * Create cooldown entry
     */
    private function createCooldownEntry($userId, $questionId, $competency, $wasCorrect, $responseTime)
    {
        try {
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" create_cooldown {$userId} \"{$questionId}\" \"{$competency}\" " . ($wasCorrect ? 'true' : 'false') . " {$responseTime}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if (!$result || !$result['success']) {
                Log::warning('Failed to create cooldown entry', [
                    'user_id' => $userId,
                    'question_id' => $questionId
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error creating cooldown entry: ' . $e->getMessage());
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
     * Get base points for difficulty
     */
    private function getBasePoints($difficulty)
    {
        $points = [
            'beginner' => 8,
            'intermediate' => 12,
            'advanced' => 15
        ];
        
        return $points[$difficulty] ?? 10;
    }
    
    /**
     * Get time bonus points based on speed
     */
    private function getTimeBonusPoints($normalizedTime)
    {
        if ($normalizedTime <= 0.5) return 5;  // Fast
        if ($normalizedTime <= 0.8) return 3;  // Medium
        return 1; // Slow
    }
    
    /**
     * Generate unique response ID
     */
    private function generateResponseId($assessmentId, $questionId)
    {
        $timestamp = time();
        $randomSuffix = substr(md5($assessmentId . $questionId . $timestamp), 0, 6);
        return "RESP_{$randomSuffix}_{$timestamp}";
    }
    
    /**
     * Update assessment statistics
     */
    private function updateAssessmentStats($assessmentId, $isCorrect, $timeTaken, $timeScore)
    {
        try {
            $assessment = DB::table('assessments')->where('assessment_id', $assessmentId)->first();
            
            if ($assessment) {
                $newCorrectAnswers = $assessment->correct_answers + ($isCorrect ? 1 : 0);
                $newIncorrectAnswers = $assessment->incorrect_answers + ($isCorrect ? 0 : 1);
                $newQuestionsAnswered = $assessment->questions_answered + 1;
                $newTotalTimeSpent = $assessment->total_time_spent + $timeTaken;
                $newCumulativeTimeScore = $assessment->cumulative_time_score + $timeScore;
                
                DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->update([
                        'correct_answers' => $newCorrectAnswers,
                        'incorrect_answers' => $newIncorrectAnswers,
                        'questions_answered' => $newQuestionsAnswered,
                        'total_time_spent' => $newTotalTimeSpent,
                        'cumulative_time_score' => $newCumulativeTimeScore
                    ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to update assessment stats: ' . $e->getMessage());
        }
    }
    
    /**
     * Calculate BKT update without modifying student_mastery table
     */
    private function calculateBKTUpdate($priorBkt, $isCorrect, $timeScore, $difficulty)
    {
        return $this->updateBKT($priorBkt, $isCorrect, $timeScore, $difficulty, []);
    }

    /**
     * Calculate current time factor based on previous responses
     */
    private function calculateCurrentTimeFactor($userId, $competency, $currentTimeScore)
    {
        try {
            // Get cumulative time score from previous responses in this assessment
            $cumulativeTimeScore = DB::table('question_responses as qr')
                ->join('assessments as a', 'qr.assessment_id', '=', 'a.assessment_id')
                ->where('a.user_id', $userId)
                ->where('a.competency', $competency)
                ->where('a.status', 'in_progress')
                ->sum('qr.time_score');

            // Count total responses including current one
            $totalResponses = DB::table('question_responses as qr')
                ->join('assessments as a', 'qr.assessment_id', '=', 'a.assessment_id')
                ->where('a.user_id', $userId)
                ->where('a.competency', $competency)
                ->where('a.status', 'in_progress')
                ->count() + 1; // +1 for current response

            $totalTimeScore = $cumulativeTimeScore + $currentTimeScore;
            return $totalResponses > 0 ? $totalTimeScore / $totalResponses : $currentTimeScore;

        } catch (\Exception $e) {
            Log::error('Failed to calculate time factor: ' . $e->getMessage());
            return $currentTimeScore;
        }
    }

    /**
     * Update mastery record with BKT and cumulative time tracking (DEPRECATED - only used for completion)
     */
    private function updateMasteryRecord($userId, $competency, $isCorrect, $timeScore, $difficulty)
    {
        try {
            $mastery = DB::table('student_mastery')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->first();

            if (!$mastery) {
                Log::warning('Mastery record not found for BKT update', [
                    'user_id' => $userId,
                    'competency' => $competency
                ]);
                return ['ftime_factor' => $timeScore];
            }

            // Calculate cumulative time tracking (like Python implementation)
            $totalTimeScore = $mastery->cumulative_time_score + $timeScore;
            $totalResponses = $mastery->total_questions_answered + 1;
            $ftimeFactor = $totalResponses > 0 ? $totalTimeScore / $totalResponses : $timeScore;

            // Calculate new BKT score using pure BKT calculation
            $newBktScore = $this->updateBKT(
                $mastery->bkt_score,
                $isCorrect,
                $timeScore,
                $difficulty,
                [] // Parameters are now hardcoded in updateBKT method
            );

            // Update counts and accuracy
            $newCorrectCount = $mastery->correct_answers + ($isCorrect ? 1 : 0);
            $newTotalCount = $mastery->total_questions_answered + 1;
            $newAccuracy = $newTotalCount > 0 ? $newCorrectCount / $newTotalCount : 0;

            // Calculate mastery score with cumulative time factor
            $newMasteryScore = $this->calculateMasteryScore($newAccuracy, $newBktScore, $ftimeFactor);

            // Check for difficulty level progression
            $newDifficulty = $this->checkDifficultyProgression($newMasteryScore, $mastery->current_difficulty);

            DB::table('student_mastery')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->update([
                    'bkt_score' => $newBktScore,
                    'accuracy_score' => $newAccuracy,
                    'final_mastery_score' => $newMasteryScore,
                    'current_difficulty' => $newDifficulty,
                    'correct_answers' => $newCorrectCount,
                    'total_questions_answered' => $newTotalCount,
                    'cumulative_time_score' => $totalTimeScore,
                    'current_ftime_factor' => $ftimeFactor,
                    'average_time_factor' => $ftimeFactor,
                    'updated_at' => now()
                ]);

            return ['ftime_factor' => $ftimeFactor];

        } catch (\Exception $e) {
            Log::error('Failed to update mastery record: ' . $e->getMessage());
            return ['ftime_factor' => $timeScore];
        }
    }
    
    /**
     * Update BKT score using exact same calculation as bkt_algorithm.py
     * Pure BKT calculation without time or difficulty factors - those are applied only in final mastery score.
     */
    private function updateBKT($priorBkt, $isCorrect, $timeScore, $difficulty, $params)
    {
        // Use default aggressive parameters to match Python implementation
        $bktParams = [
            'prior_knowledge' => 0.15,    // Very low baseline for dramatic learning detection
            'learn_rate' => 0.65,         // Near-maximum learning rate for instant responsiveness
            'slip_rate' => 0.05,         // More academically realistic slip rate
            'guess_rate' => 0.15         // More academically realistic guess rate
        ];

        $P_L = $priorBkt;
        $P_T = $bktParams['learn_rate'];
        $P_S = $bktParams['slip_rate'];
        $P_G = $bktParams['guess_rate'];

        if ($isCorrect) {
            // For correct answers - Academic Equation 3
            $numerator = $P_L * (1 - $P_S);
            $denominator = $P_L * (1 - $P_S) + (1 - $P_L) * $P_G;

            if ($denominator == 0) {
                return $P_L;  // Return prior if denominator is zero
            }

            // Calculate posterior probability
            $posterior = $numerator / $denominator;

            // Apply learning enhancement for correct answers only
            $P_L_new = $posterior + (1 - $posterior) * $P_T;
        } else {
            // For incorrect answers - Academic Equation 4
            $numerator = $P_L * $P_S;
            $denominator = $P_L * $P_S + (1 - $P_L) * (1 - $P_G);

            if ($denominator == 0) {
                return $P_L;  // Return prior if denominator is zero
            }

            // Calculate posterior probability
            $posterior = $numerator / $denominator;

            // No learning enhancement for incorrect answers (standard BKT)
            $P_L_new = $posterior;
        }

        // Apply HYPER-MINIMAL dampening for theoretical maximum discrimination
        $dampening_factor = 0.995;  // Theoretical maximum sensitivity (99.5% of raw BKT change)
        $P_L_dampened = $P_L + ($P_L_new - $P_L) * $dampening_factor;

        return max(0.02, min(0.96, $P_L_dampened));  // HYPER-WIDE range [0.02, 0.96] for maximum AUC-ROC separation
    }
    
    /**
     * Get difficulty factor (now only used for points calculation, not BKT)
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
     * Calculate mastery score using exact same formula as bkt_algorithm.py
     * Formula: Mastery_Score = (0.55 × Accuracy) + (0.45 × Final_BKT × Average_Time_Factor)
     * Final_Percentage = Mastery_Score × 100
     */
    private function calculateMasteryScore($accuracy, $bktScore, $timeFactor = 1.0)
    {
        // Use exact weights from Python implementation
        $weightAccuracy = 0.60;  // 60%
        $weightBKT = 0.40;       // 40%

        $accuracyComponent = $weightAccuracy * $accuracy;
        $bktComponent = $weightBKT * $bktScore * $timeFactor;
        $finalScore = ($accuracyComponent + $bktComponent) * 100;

        return min(100, max(0, $finalScore));
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
     * Complete assessment with final BKT calculations
     */
    private function completeAssessment($assessmentId)
    {
        try {
            // Get assessment info
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->first();
                
            if (!$assessment) {
                Log::error('Assessment not found for completion', ['assessment_id' => $assessmentId]);
                return;
            }
            
            // Get final assessment statistics
            $responses = DB::table('question_responses')
                ->where('assessment_id', $assessmentId)
                ->selectRaw('
                    COUNT(*) as total_answered,
                    SUM(is_correct) as correct_answers,
                    AVG(response_time) as avg_response_time,
                    AVG(time_score) as avg_time_score,
                    SUM(total_points) as total_points_earned
                ')
                ->first();
            
            if ($responses) {
                $accuracy = $responses->total_answered > 0 ? 
                    ($responses->correct_answers / $responses->total_answered) * 100 : 0;
                
                // Get current BKT scores from student_mastery
                $mastery = DB::table('student_mastery')
                    ->where('user_id', $assessment->user_id)
                    ->where('competency', $assessment->competency)
                    ->first();
                
                $initialBktScore = $mastery ? $mastery->bkt_score : 0.5;
                
                // Calculate final mastery metrics from all question responses
                $this->calculateFinalMasteryFromResponses($assessment, $responses);
                
                // Get final BKT scores after consolidation
                $finalMastery = DB::table('student_mastery')
                    ->where('user_id', $assessment->user_id)
                    ->where('competency', $assessment->competency)
                    ->first();
                
                $finalBktScore = $finalMastery ? $finalMastery->bkt_score : $initialBktScore;
                $finalMasteryScore = $finalMastery ? $finalMastery->final_mastery_score : 0;
                
                // Update assessment record with complete BKT tracking
                DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'questions_answered' => $responses->total_answered,
                        'correct_answers' => $responses->correct_answers,
                        'incorrect_answers' => $responses->total_answered - $responses->correct_answers,
                        'accuracy_percentage' => $accuracy,
                        'accuracy_component' => $accuracy / 100,
                        'bkt_score_before' => $assessment->bkt_score_before ?? $initialBktScore,
                        'bkt_score_after' => $finalBktScore,
                        'bkt_final_score' => $finalBktScore,
                        'bkt_component' => $finalBktScore,
                        'final_mastery_score' => $finalMasteryScore,
                        'average_response_time' => $responses->avg_response_time,
                        'time_performance_score' => $responses->avg_time_score,
                        'cumulative_time_score' => $responses->avg_time_score * $responses->total_answered,
                        'average_time_factor' => $responses->avg_time_score,
                        'total_time_spent' => $responses->avg_response_time * $responses->total_answered,
                        'total_points_earned' => $responses->total_points_earned
                    ]);
                
                // Get suggested difficulty level based on final mastery score
                $suggestedDifficulty = $this->getSuggestedDifficultyLevel($finalMasteryScore);
                
                // Also update assessment_sessions table if it exists for this assessment
                $sessionExists = DB::table('assessment_sessions')
                    ->where('assessment_id', $assessmentId)
                    ->where('user_id', $assessment->user_id)
                    ->exists();
                
                if ($sessionExists) {
                    DB::table('assessment_sessions')
                        ->where('assessment_id', $assessmentId)
                        ->where('user_id', $assessment->user_id)
                        ->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'questions_answered' => $responses->total_answered,
                            'correct_answers' => $responses->correct_answers,
                            'incorrect_answers' => $responses->total_answered - $responses->correct_answers,
                            'total_points_earned' => $responses->total_points_earned,
                            'accuracy_percentage' => $accuracy,
                            'accuracy_component' => $accuracy / 100,
                            'average_response_time' => $responses->avg_response_time,
                            'time_performance_score' => $responses->avg_time_score,
                            'cumulative_time_score' => $responses->avg_time_score * $responses->total_answered,
                            'initial_bkt_probability' => $assessment->bkt_score_before ?? $initialBktScore,
                            'final_bkt_probability' => $finalBktScore,
                            'bkt_component' => $finalBktScore,
                            'final_mastery_score' => $finalMasteryScore,
                            'suggested_difficulty_level' => $suggestedDifficulty
                        ]);
                }
                
                // Update assessments table with suggested difficulty as well
                DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->update([
                        'suggested_difficulty_level' => $suggestedDifficulty
                    ]);

                // Update student_mastery with total_assessments_taken increment (matching Python implementation)
                DB::table('student_mastery')
                    ->where('user_id', $assessment->user_id)
                    ->where('competency', $assessment->competency)
                    ->increment('total_assessments_taken');

                Log::info("Assessment completed successfully", [
                    'assessment_id' => $assessmentId,
                    'accuracy' => $accuracy,
                    'total_points' => $responses->total_points_earned,
                    'bkt_before' => $assessment->bkt_score_before ?? $initialBktScore,
                    'bkt_after' => $finalBktScore,
                    'final_mastery' => $finalMasteryScore
                ]);
                
                return [
                    'accuracy' => $accuracy / 100, // Return as decimal for gamification
                    'questions_answered' => $responses->total_answered,
                    'correct_answers' => $responses->correct_answers,
                    'total_points' => $responses->total_points_earned,
                    'final_mastery_score' => $finalMasteryScore
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to complete assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
        }
        
        return null;
    }
    
    /**
     * Calculate final mastery metrics from all question responses
     */
    private function calculateFinalMasteryFromResponses($assessment, $responseSummary)
    {
        try {
            // Get all question responses for this assessment
            $questionResponses = DB::table('question_responses')
                ->where('assessment_id', $assessment->assessment_id)
                ->orderBy('answered_at')
                ->get();

            if ($questionResponses->isEmpty()) {
                Log::warning('No question responses found for assessment completion');
                return;
            }

            // Get current mastery record
            $mastery = DB::table('student_mastery')
                ->where('user_id', $assessment->user_id)
                ->where('competency', $assessment->competency)
                ->first();

            $initialBktScore = $mastery ? $mastery->bkt_score : 0.5;
            $currentBktScore = $initialBktScore;

            // Calculate BKT progression through all questions
            foreach ($questionResponses as $response) {
                $currentBktScore = $this->updateBKT(
                    $currentBktScore,
                    (bool)$response->is_correct,
                    $response->time_score,
                    $assessment->difficulty_level,
                    []
                );
            }

            // Calculate final metrics
            $accuracy = $responseSummary->total_answered > 0 ?
                $responseSummary->correct_answers / $responseSummary->total_answered : 0;

            $averageTimeFactor = $responseSummary->avg_time_score ?? 0.5;
            $finalMasteryScore = $this->calculateMasteryScore($accuracy, $currentBktScore, $averageTimeFactor);
            $newDifficulty = $this->checkDifficultyProgression($finalMasteryScore, $mastery->current_difficulty ?? 'beginner');

            // Update student_mastery with final calculated values
            DB::table('student_mastery')
                ->where('user_id', $assessment->user_id)
                ->where('competency', $assessment->competency)
                ->update([
                    'bkt_score' => $currentBktScore,
                    'accuracy_score' => $accuracy,
                    'final_mastery_score' => $finalMasteryScore,
                    'current_difficulty' => $newDifficulty,
                    'correct_answers' => ($mastery->correct_answers ?? 0) + $responseSummary->correct_answers,
                    'total_questions_answered' => ($mastery->total_questions_answered ?? 0) + $responseSummary->total_answered,
                    'cumulative_time_score' => ($mastery->cumulative_time_score ?? 0) + ($responseSummary->avg_time_score * $responseSummary->total_answered),
                    'current_ftime_factor' => $averageTimeFactor,
                    'average_time_factor' => $averageTimeFactor,
                    'updated_at' => now()
                ]);

            Log::info("Final mastery calculated from responses", [
                'assessment_id' => $assessment->assessment_id,
                'initial_bkt' => $initialBktScore,
                'final_bkt' => $currentBktScore,
                'final_mastery' => $finalMasteryScore,
                'new_difficulty' => $newDifficulty
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to calculate final mastery from responses: ' . $e->getMessage());
        }
    }

    /**
     * Apply final BKT consolidation after assessment completion (DEPRECATED)
     */
    private function applyFinalBKTConsolidation($userId, $competency, $overallAccuracy, $avgTimeScore, $difficulty, $questionCount)
    {
        try {
            $mastery = DB::table('student_mastery')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->first();
                
            if (!$mastery) return;
            
            // Apply a consolidation factor based on assessment performance
            $consolidationFactor = min(0.05, $questionCount * 0.003); // Max 5% adjustment
            
            if ($overallAccuracy >= 0.7) {
                // Good performance - slight positive consolidation
                $bktAdjustment = $consolidationFactor * $avgTimeScore;
            } else {
                // Poor performance - slight negative consolidation
                $bktAdjustment = -$consolidationFactor * (1 - $avgTimeScore);
            }
            
            $newBktScore = max(0.02, min(0.96, $mastery->bkt_score + $bktAdjustment));
            $newMasteryScore = $this->calculateMasteryScore($mastery->accuracy_score, $newBktScore, $avgTimeScore);
            
            // Check for difficulty progression
            $newDifficulty = $this->checkDifficultyProgression($newMasteryScore, $mastery->current_difficulty);
            
            DB::table('student_mastery')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->update([
                    'bkt_score' => $newBktScore,
                    'final_mastery_score' => $newMasteryScore,
                    'current_difficulty' => $newDifficulty,
                    'updated_at' => now()
                ]);
                
            Log::info("Final BKT consolidation applied", [
                'user_id' => $userId,
                'competency' => $competency,
                'bkt_adjustment' => $bktAdjustment,
                'new_mastery_score' => $newMasteryScore
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to apply final BKT consolidation: ' . $e->getMessage());
        }
    }
    
    /**
     * Get assessment summary
     */
    public function getAssessmentSummary(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found'
            ]);
        }
        
        try {
            // Get detailed responses
            $responses = DB::table('question_responses')
                ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
                ->where('question_responses.assessment_id', $assessmentId)
                ->select([
                    'question_responses.*',
                    'questions.question_text',
                    'questions.question_type',
                    'questions.correct_answer as question_correct_answer',
                    'questions.explanation',
                    'questions.difficulty_level as question_difficulty',
                    'questions.topic_tag'
                ])
                ->orderBy('question_responses.answered_at')
                ->get();
            
            // Calculate statistics
            $totalQuestions = $responses->count();
            $correctAnswers = $responses->where('is_correct', 1)->count();
            $totalPoints = $responses->sum('total_points');
            $avgResponseTime = $responses->avg('response_time');
            $accuracy = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
            
            // Group by difficulty
            $difficultyStats = $responses->groupBy('question_difficulty')
                ->map(function ($group) {
                    return [
                        'total' => $group->count(),
                        'correct' => $group->where('is_correct', 1)->count(),
                        'accuracy' => $group->count() > 0 ? ($group->where('is_correct', 1)->count() / $group->count()) * 100 : 0,
                        'avg_time' => $group->avg('response_time'),
                        'points' => $group->sum('total_points')
                    ];
                });
            
            // Group by topic
            $topicStats = $responses->groupBy('topic_tag')
                ->map(function ($group) {
                    return [
                        'total' => $group->count(),
                        'correct' => $group->where('is_correct', 1)->count(),
                        'accuracy' => $group->count() > 0 ? ($group->where('is_correct', 1)->count() / $group->count()) * 100 : 0,
                        'avg_time' => $group->avg('response_time'),
                        'points' => $group->sum('total_points')
                    ];
                });
            
            return response()->json([
                'success' => true,
                'assessment' => [
                    'assessment_id' => $assessment->assessment_id,
                    'competency' => $assessment->competency,
                    'difficulty_level' => $assessment->difficulty_level,
                    'status' => $assessment->status,
                    'started_at' => $assessment->started_at,
                    'completed_at' => $assessment->completed_at,
                    'time_limit' => $assessment->time_limit
                ],
                'summary' => [
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'incorrect_answers' => $totalQuestions - $correctAnswers,
                    'accuracy_percentage' => round($accuracy, 2),
                    'total_points' => $totalPoints,
                    'average_response_time' => round($avgResponseTime, 2),
                    'total_time_spent' => $responses->sum('response_time')
                ],
                'difficulty_breakdown' => $difficultyStats,
                'topic_breakdown' => $topicStats,
                'responses' => $responses
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get assessment summary: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get assessment summary'
            ]);
        }
    }
    
    /**
     * Pause assessment (save current state)
     */
    public function pauseAssessment(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string',
            'current_question_id' => 'nullable|string',
            'current_answer' => 'nullable|string',
            'time_spent_on_question' => 'nullable|integer'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found or already completed'
            ]);
        }
        
        try {
            // Update assessment session to mark as paused
            $sessionUpdated = DB::table('assessment_sessions')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->update([
                    'is_paused' => true,
                    'paused_at' => now(),
                    'last_activity_at' => now()
                ]);
            
            // Save pause state to database
            DB::table('assessment_pauses')->updateOrInsert(
                [
                    'assessment_id' => $assessmentId,
                    'user_id' => $user->id
                ],
                [
                    'current_question_id' => $request->current_question_id,
                    'current_answer' => $request->current_answer,
                    'time_spent_on_question' => $request->time_spent_on_question ?? 0,
                    'paused_at' => now(),
                    'updated_at' => now()
                ]
            );
            
            Log::info("Assessment paused successfully", [
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'session_updated' => $sessionUpdated > 0
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Assessment paused successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to pause assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to pause assessment'
            ]);
        }
    }
    
    /**
     * Resume assessment from paused state
     */
    public function resumeAssessment(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found or already completed'
            ]);
        }
        
        try {
            // Update assessment session to mark as unpaused
            $sessionUpdated = DB::table('assessment_sessions')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->update([
                    'is_paused' => false,
                    'paused_at' => null,
                    'last_activity_at' => now()
                ]);
            
            // Get pause state
            $pauseState = DB::table('assessment_pauses')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->first();
            
            $resumeData = [];
            
            if ($pauseState) {
                $resumeData = [
                    'current_question_id' => $pauseState->current_question_id,
                    'current_answer' => $pauseState->current_answer,
                    'time_spent_on_question' => $pauseState->time_spent_on_question,
                    'paused_at' => $pauseState->paused_at
                ];
                
                // Clean up pause record
                DB::table('assessment_pauses')
                    ->where('assessment_id', $assessmentId)
                    ->where('user_id', $user->id)
                    ->delete();
            }
            
            // Get current progress
            $progress = $this->getAssessmentProgress($assessmentId);
            
            Log::info("Assessment resumed successfully", [
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'had_pause_state' => !empty($pauseState),
                'session_updated' => $sessionUpdated > 0
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Assessment resumed successfully',
                'resume_data' => $resumeData,
                'progress' => $progress
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to resume assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resume assessment'
            ]);
        }
    }
    
    /**
     * Abandon assessment (mark as abandoned, don't complete)
     */
    public function abandonAssessment(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string'
        ]);
        
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found or already completed'
            ]);
        }
        
        try {
            // Mark as abandoned
            DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->update([
                    'status' => 'abandoned',
                    'completed_at' => now()
                ]);
            
            // Clean up pause state if exists
            DB::table('assessment_pauses')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->delete();
            
            // Clean up Fisher-Yates assessment data
            try {
                $scriptPath = base_path('public/algorithm/fisher_yates.py');
                $command = "python \"{$scriptPath}\" cleanup_assessment \"{$assessmentId}\"";
                shell_exec($command);
            } catch (\Exception $e) {
                Log::warning('Failed to cleanup Fisher-Yates data for abandoned assessment: ' . $e->getMessage());
            }
            
            Log::info("Assessment abandoned successfully", [
                'assessment_id' => $assessmentId,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Assessment abandoned successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to abandon assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to abandon assessment'
            ]);
        }
    }
    
    /**
     * Get hint for current question
     */
    public function getHint(Request $request)
    {
        $request->validate([
            'question_id' => 'required|string',
            'assessment_id' => 'required|string'
        ]);
        
        $questionId = $request->question_id;
        $assessmentId = $request->assessment_id;
        $user = Auth::guard('student')->user();
        
        // Verify assessment belongs to user
        $assessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$assessment) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment not found'
            ]);
        }
        
        try {
            $question = DB::table('questions')
                ->where('question_id', $questionId)
                ->first();
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question not found'
                ]);
            }
            
            // Log hint usage for analytics
            DB::table('hint_usage')->insert([
                'assessment_id' => $assessmentId,
                'question_id' => $questionId,
                'user_id' => $user->id,
                'used_at' => now()
            ]);
            
            $hint = $question->explanation ?? 'No hint available for this question.';
            
            return response()->json([
                'success' => true,
                'hint' => $hint
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get hint: ' . $e->getMessage(), [
                'question_id' => $questionId,
                'assessment_id' => $assessmentId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hint'
            ]);
        }
    }
    
    /**
     * Get available assessment options for user
     */
    public function getAvailableAssessments(Request $request)
    {
        $request->validate([
            'category' => 'required|string'
        ]);
        
        $category = $request->category;
        $user = Auth::guard('student')->user();
        $dbCompetency = strtolower($category);
        
        // Get user's mastery level
        $mastery = DB::table('student_mastery')
            ->where('user_id', $user->id)
            ->where('competency', $dbCompetency)
            ->first();
        
        if (!$mastery || !$mastery->has_taken_diagnostic) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete the diagnostic test first.',
                'needs_diagnostic' => true
            ]);
        }
        
        try {
            $currentDifficulty = $mastery->current_difficulty;
            
            // Check question availability using Fisher-Yates
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" get_available {$user->id} \"{$dbCompetency}\" \"{$currentDifficulty}\" 100";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if (!$result || !$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to check question availability'
                ]);
            }
            
            // Define question counts per difficulty
            $questionCounts = [
                'beginner' => 15,
                'intermediate' => 20,
                'advanced' => 25
            ];
            
            $questionsPerAssessment = $questionCounts[$currentDifficulty];
            $availableQuestions = count($result['available_questions']);
            
            // Generate assessment options
            $maxAssessments = 5;
            $possibleAssessments = floor($availableQuestions / $questionsPerAssessment);
            $assessmentCount = min($maxAssessments, $possibleAssessments);
            
            $assessmentOptions = [];
            
            for ($i = 1; $i <= $assessmentCount; $i++) {
                $assessmentId = $this->generateUniqueAssessmentId($user->id, $dbCompetency, $currentDifficulty, $i);
                
                $assessmentOptions[] = [
                    'assessment_id' => $assessmentId,
                    'title' => ucfirst($currentDifficulty) . " Assessment #$i",
                    'question_count' => $questionsPerAssessment,
                    'time_limit' => $this->getTimeLimitForDifficulty($currentDifficulty),
                    'difficulty' => $currentDifficulty,
                    'topics' => $this->getTopicsForCompetency($dbCompetency, $currentDifficulty),
                    'estimated_points' => $this->calculateEstimatedPoints($questionsPerAssessment, $currentDifficulty),
                    'best_completion_time' => $this->getBestCompletionTime($user->id, $dbCompetency),
                    'created_at' => now()
                ];
            }
            
            return response()->json([
                'success' => true,
                'current_difficulty' => $currentDifficulty,
                'available_questions' => $availableQuestions,
                'questions_in_cooldown' => $result['excluded_cooldown'] ?? 0,
                'assessment_options' => $assessmentOptions,
                'mastery_score' => round($mastery->final_mastery_score, 2)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get available assessments: ' . $e->getMessage(), [
                'category' => $category,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve assessment options'
            ]);
        }
    }
    
    /**
     * Generate unique assessment ID (max 50 chars for DB)
     */
    private function generateUniqueAssessmentId($userId, $competency, $difficulty, $index)
    {
        // Use current time with microseconds for better uniqueness
        $timestamp = time() . substr(microtime(), 2, 6); // Unix timestamp + microseconds
        $randomBytes = bin2hex(random_bytes(6)); // 12 char random string
        
        // Format: ASS_[userId]_[timestamp]_[random] (keeps it under 50 chars)
        $assessmentId = "ASS_{$userId}_{$timestamp}_{$randomBytes}";
        
        // Double-check for existing assessment with same ID
        $existingAssessment = DB::table('assessments')
            ->where('assessment_id', $assessmentId)
            ->first();
        
        // If duplicate found, add extra randomness
        if ($existingAssessment) {
            $extraRandom = bin2hex(random_bytes(4)); // 8 more chars
            $assessmentId = "ASS_{$userId}_{$timestamp}{$extraRandom}";
            
            // Final check - if still duplicate, use completely random approach
            $retryCount = 0;
            while ($existingAssessment && $retryCount < 5) {
                $fullRandom = bin2hex(random_bytes(10)); // 20 char random
                $assessmentId = "ASS_{$userId}_{$fullRandom}";
                $existingAssessment = DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->first();
                $retryCount++;
            }
        }
        
        return $assessmentId;
    }
    
    /**
     * Calculate estimated points
     */
    private function calculateEstimatedPoints($questionCount, $difficulty)
    {
        $pointsPerQuestion = [
            'beginner' => 8,
            'intermediate' => 12,
            'advanced' => 15
        ];
        
        $basePoints = ($pointsPerQuestion[$difficulty] ?? 10) * $questionCount;
        $bonusPoints = $questionCount * 5; // Time bonus potential
        
        return $basePoints + $bonusPoints;
    }
    
    /**
     * Get best completion time for user
     */
    private function getBestCompletionTime($userId, $competency)
    {
        $bestTime = DB::table('assessments')
            ->where('user_id', $userId)
            ->where('competency', $competency)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->selectRaw('MIN(TIMESTAMPDIFF(MINUTE, started_at, completed_at)) as best_minutes')
            ->value('best_minutes');
        
        if ($bestTime) {
            return "{$bestTime} minutes";
        }
        
        return 'Not attempted';
    }
    
    /**
     * Get topics covered for a competency and difficulty
     */
    private function getTopicsForCompetency($competency, $difficulty)
    {
        $topics = DB::table('questions')
            ->where('competency', $competency)
            ->where('difficulty_level', $difficulty)
            ->where('is_active', 1)
            ->distinct()
            ->pluck('topic_tag')
            ->filter()
            ->take(3)
            ->toArray();
            
        return $topics ?: ['General Mathematics'];
    }
    
    /**
     * Check for active assessment sessions for a user
     */
    public function checkActiveAssessments(Request $request)
    {
        $request->validate([
            'category' => 'required|string'
        ]);
        
        $user = Auth::guard('student')->user();
        $category = $request->category;
        $dbCompetency = strtolower($category);
        
        try {
            $activeAssessmentData = [];
            
            // First, check for active assessments in the assessments table
            $activeAssessments = DB::table('assessments')
                ->where('user_id', $user->id)
                ->where('competency', $dbCompetency)
                ->where('status', 'in_progress')
                ->where('started_at', '>=', now()->subHours(2)) // Only check recent assessments
                ->orderBy('started_at', 'desc')
                ->get();
            
            foreach ($activeAssessments as $assessment) {
                // Check if this assessment has an active session
                $session = DB::table('assessment_sessions')
                    ->where('assessment_id', $assessment->assessment_id)
                    ->where('user_id', $user->id)
                    ->first();
                
                if ($session && $session->status === 'in_progress') {
                    // Check if assessment has questions in Fisher-Yates pool
                    $scriptPath = base_path('public/algorithm/fisher_yates.py');
                    $command = "python \"{$scriptPath}\" check_pool {$assessment->assessment_id}";
                    
                    $output = shell_exec($command);
                    $result = json_decode($output, true);
                    
                    if ($result && $result['success'] && isset($result['total_questions'])) {
                        $activeAssessmentData[] = [
                            'assessment_id' => $assessment->assessment_id,
                            'session_id' => $session->session_id,
                            'type' => 'assessment',
                            'progress' => $assessment->questions_answered,
                            'total_questions' => $assessment->total_questions,
                            'started_at' => $assessment->started_at,
                            'difficulty' => $assessment->difficulty_level,
                            'time_limit' => $assessment->time_limit ?? 30,
                            'is_paused' => $session->is_paused ?? false,
                            'title' => ucfirst($assessment->difficulty_level) . ' Assessment',
                            'can_resume' => true
                        ];
                    }
                }
            }
            
            // If no assessments found, also check standalone sessions (shouldn't happen in normal flow)
            if (empty($activeAssessmentData)) {
                $activeSessions = DB::table('assessment_sessions')
                    ->where('user_id', $user->id)
                    ->where('competency', $dbCompetency)
                    ->where('status', 'in_progress')
                    ->where('started_at', '>=', now()->subHours(2))
                    ->whereNotNull('assessment_id') // Only sessions linked to assessments
                    ->orderBy('started_at', 'desc')
                    ->get();
                
                foreach ($activeSessions as $session) {
                    $activeAssessmentData[] = [
                        'assessment_id' => $session->assessment_id,
                        'session_id' => $session->session_id,
                        'type' => 'session',
                        'progress' => $session->questions_answered,
                        'total_questions' => $session->total_questions,
                        'started_at' => $session->started_at,
                        'difficulty' => $session->difficulty_level,
                        'time_limit' => $session->time_limit_minutes ?? 30,
                        'is_paused' => $session->is_paused ?? false,
                        'title' => ucfirst($session->difficulty_level) . ' Assessment',
                        'can_resume' => true
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'has_active_assessments' => !empty($activeAssessmentData),
                'active_assessments' => $activeAssessmentData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error checking active assessments: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'category' => $category,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking active assessments',
                'has_active_assessments' => false,
                'active_assessments' => []
            ]);
        }
    }
    
    /**
     * Resume an active assessment from assessment list
     */
    public function resumeAssessmentFromList(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|string',
            'category' => 'required|string'
        ]);
        
        $user = Auth::guard('student')->user();
        $assessmentId = $request->assessment_id;
        $category = $request->category;
        
        try {
            // Verify the assessment belongs to the user and is active
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->first();
            
            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or not active'
                ]);
            }
            
            // Check if assessment has questions
            $questionCount = DB::table('assessment_questions')
                ->where('assessment_id', $assessmentId)
                ->count();
                
            if ($questionCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment has no questions'
                ]);
            }
            
            // Get the next question to continue from
            $nextQuestion = DB::table('assessment_questions as aq')
                ->join('questions as q', 'aq.question_id', '=', 'q.question_id')
                ->where('aq.assessment_id', $assessmentId)
                ->where('aq.is_answered', false)
                ->orderBy('aq.question_order')
                ->first();
            
            if (!$nextQuestion) {
                // All questions answered, redirect to results page
                return response()->json([
                    'success' => true,
                    'redirect' => route('student.results.complete', $category) . '?assessment_id=' . $assessmentId
                ]);
            }
            
            // Calculate current progress
            $currentQuestionNumber = $assessment->questions_answered + 1;
            
            return response()->json([
                'success' => true,
                'redirect' => route('student.quiz.show', [
                    'category' => $category
                ]) . '?assessment_id=' . $assessmentId . '&question=' . $currentQuestionNumber
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error resuming assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resume assessment: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Show quiz completion page
     */
    public function showQuizComplete($category, Request $request)
    {
        $user = Auth::guard('student')->user();
        $assessmentId = $request->query('assessment_id');
        
        if (!$assessmentId) {
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'Assessment ID is required to view results.');
        }
        
        try {
            // Get assessment details
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$assessment) {
                return redirect()->route('student.assessments.category', $category)
                    ->with('error', 'Assessment not found.');
            }
            
            // Get user's mastery data
            $mastery = DB::table('student_mastery')
                ->where('user_id', $user->id)
                ->where('competency', strtolower($category))
                ->first();
            
            // Format category data
            $categoryData = $this->getCategoryData($category);

            // Get user progress data for level up functionality
            $userProgress = DB::table('user_progress')
                ->where('user_id', $user->id)
                ->first();

            return view('student.assessment-complete', [
                'category' => $category,
                'data' => $categoryData,
                'mastery' => $mastery,
                'assessment' => $assessment,
                'assessment_id' => $assessmentId,
                'from_regular_quiz' => true,
                'userProgress' => $userProgress
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to show quiz completion page: ' . $e->getMessage());
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'Unable to load results page.');
        }
    }
    
    /**
     * Show quiz review page
     */
    public function showQuizReview($category, Request $request)
    {
        $user = Auth::guard('student')->user();
        $assessmentId = $request->query('assessment_id');
        
        if (!$assessmentId) {
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'Assessment ID is required to view review.');
        }
        
        try {
            // Get assessment details
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$assessment) {
                return redirect()->route('student.assessments.category', $category)
                    ->with('error', 'Assessment not found.');
            }
            
            // Get question responses for review
            $responses = DB::table('question_responses as qr')
                ->join('questions as q', 'qr.question_id', '=', 'q.question_id')
                ->where('qr.assessment_id', $assessmentId)
                ->select([
                    'q.question_text',
                    'q.question_type',
                    'q.choice_a',
                    'q.choice_b', 
                    'q.choice_c',
                    'q.choice_d',
                    'q.correct_answer',
                    'q.explanation',
                    'q.difficulty_level',
                    'q.topic_tag as topic',
                    'qr.user_answer',
                    'qr.is_correct',
                    'qr.response_time',
                    'qr.answered_at'
                ])
                ->orderBy('qr.answered_at')
                ->get();
            
            // Get user's mastery data
            $mastery = DB::table('student_mastery')
                ->where('user_id', $user->id)
                ->where('competency', strtolower($category))
                ->first();
            
            // Calculate statistics for the view
            $correctAnswers = $responses->where('is_correct', true)->count();
            $totalQuestions = $responses->count();
            $scorePercentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
            
            // Group responses by difficulty level for breakdown
            $questionsByDifficulty = $responses->groupBy('difficulty_level');
            
            // Format category data
            $categoryData = $this->getCategoryData($category);
            
            return view('student.assessment-review', [
                'category' => $category,
                'data' => $categoryData,
                'mastery' => $mastery,
                'assessment' => $assessment,
                'responses' => $responses,
                'questionResponses' => $responses, // For compatibility with view
                'correctAnswers' => $correctAnswers,
                'totalQuestions' => $totalQuestions,
                'scorePercentage' => $scorePercentage,
                'questionsByDifficulty' => $questionsByDifficulty,
                'assessment_id' => $assessmentId,
                'from_regular_quiz' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to show quiz review page: ' . $e->getMessage());
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'Unable to load review page.');
        }
    }
    
    /**
     * Get quiz results data (API endpoint)
     */
    public function getQuizResultsData($assessmentId)
    {
        $user = Auth::guard('student')->user();
        
        try {
            // Get assessment details
            $assessment = DB::table('assessments')
                ->where('assessment_id', $assessmentId)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$assessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found.'
                ]);
            }
            
            // Get detailed response statistics
            $responses = DB::table('question_responses')
                ->where('assessment_id', $assessmentId)
                ->selectRaw('
                    COUNT(*) as total_questions,
                    SUM(is_correct) as correct_answers,
                    AVG(response_time) as avg_response_time,
                    SUM(total_points) as total_points,
                    AVG(time_score) as avg_time_score
                ')
                ->first();
            
            return response()->json([
                'success' => true,
                'assessment' => $assessment,
                'responses' => $responses,
                'accuracy' => $responses->total_questions > 0 ? 
                    round(($responses->correct_answers / $responses->total_questions) * 100, 1) : 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get quiz results data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get results data'
            ]);
        }
    }
    
    
    /**
     * Get category data for display
     */
    private function getCategoryData($category)
    {
        $categoryMap = [
            'number_algebra' => [
                'title' => 'Number and Algebra',
                'icon' => '🔢',
                'description' => 'Master mathematical expressions, equations, and number relationships'
            ],
            'measurement_geometry' => [
                'title' => 'Measurement and Geometry', 
                'icon' => '📐',
                'description' => 'Explore shapes, measurements, and spatial relationships'
            ],
            'data_probability' => [
                'title' => 'Data and Probability',
                'icon' => '📊', 
                'description' => 'Analyze data patterns and understand probability concepts'
            ]
        ];
        
        return $categoryMap[$category] ?? [
            'title' => ucfirst(str_replace('_', ' ', $category)),
            'icon' => '📚',
            'description' => 'Mathematical concepts and problem solving'
        ];
    }
    
    /**
     * Get suggested difficulty level based on mastery score
     * Uses the same thresholds as diagnostic: beginner (≤75), intermediate (76-84), advanced (≥85)
     */
    private function getSuggestedDifficultyLevel($masteryScore)
    {
        if ($masteryScore >= 85) {
            return 'advanced';
        } elseif ($masteryScore >= 76 && $masteryScore <= 84) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }
}