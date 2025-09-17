<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\AssessmentGenerationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegularAssessmentController extends Controller
{
    protected $assessmentGenerator;

    public function __construct()
    {
        $this->assessmentGenerator = new AssessmentGenerationController();
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

        $user = Auth::user();
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
                $redirectUrl = route('student.quiz.show', $category) . '?assessment_id=' . $assessmentId;
                
                return response()->json([
                    'success' => true,
                    'assessment_exists' => $result['exists'],
                    'assessment_id' => $assessmentId,
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
                    'cumulative_time_score' => 0.0000
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
        $user = Auth::user();
        
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
        
        $user = Auth::user();
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
            
            // Calculate scores
            $normalizedTime = $timeTaken / $question->max_allowed_time;
            $timeScore = $this->calculateTimeScore($normalizedTime, $isCorrect);
            $basePoints = $this->getBasePoints($assessment->difficulty_level);
            $bonusPoints = $isCorrect ? $this->getTimeBonusPoints($normalizedTime) : 0;
            $totalPoints = $basePoints + $bonusPoints;
            
            // Record response
            $responseId = $this->generateResponseId($assessmentId, $questionId);
            
            DB::table('question_responses')->insert([
                'response_id' => $responseId,
                'assessment_id' => $assessmentId,
                'user_id' => $user->id,
                'question_id' => $questionId,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'response_time' => $timeTaken,
                'max_allowed_time' => $question->max_allowed_time,
                'normalized_time' => $normalizedTime,
                'time_score' => $timeScore,
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
            
            // Update BKT and mastery
            $this->updateMasteryRecord($user->id, $assessment->competency, $isCorrect, $timeScore, $assessment->difficulty_level);
            
            // Check if assessment is complete
            $progress = $this->getAssessmentProgress($assessmentId);
            $isComplete = $progress['answered_questions'] >= $progress['total_questions'];
            
            if ($isComplete) {
                $this->completeAssessment($assessmentId);
            }
            
            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
                'points_earned' => $totalPoints,
                'assessment_complete' => $isComplete,
                'progress' => $progress
            ]);
            
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
        $user = Auth::user();
        
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
        $user = Auth::user();
        
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
        
        return [
            'question_id' => $question['question_id'],
            'text' => $question['question_text'],
            'type' => $question['question_type'] ?? 'multiple_choice',
            'options' => $options,
            'max_time' => $question['max_allowed_time'] ?? 30,
            'difficulty_level' => $question['difficulty_level'] ?? 'beginner',
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
     * Update mastery record with BKT
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
                return;
            }
            
            // Calculate new BKT score
            $newBktScore = $this->updateBKT(
                $mastery->bkt_score,
                $isCorrect,
                $timeScore,
                $difficulty,
                [
                    'prior_knowledge' => $mastery->prior_knowledge,
                    'learn_rate' => $mastery->learn_rate,
                    'slip_rate' => $mastery->slip_rate,
                    'guess_rate' => $mastery->guess_rate
                ]
            );
            
            // Update counts and accuracy
            $newCorrectCount = $mastery->correct_answers + ($isCorrect ? 1 : 0);
            $newTotalCount = $mastery->total_questions_answered + 1;
            $newAccuracy = $newTotalCount > 0 ? $newCorrectCount / $newTotalCount : 0;
            $newMasteryScore = $this->calculateMasteryScore($newAccuracy, $newBktScore);
            
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
                    'updated_at' => now()
                ]);
                
        } catch (\Exception $e) {
            Log::error('Failed to update mastery record: ' . $e->getMessage());
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
        } elseif ($masteryScore >= 75 && $masteryScore < 85 && $currentDifficulty !== 'intermediate') {
            return 'intermediate';
        } elseif ($masteryScore < 50 && $currentDifficulty !== 'beginner') {
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
                
                DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'questions_answered' => $responses->total_answered,
                        'correct_answers' => $responses->correct_answers,
                        'incorrect_answers' => $responses->total_answered - $responses->correct_answers,
                        'accuracy_percentage' => $accuracy,
                        'average_response_time' => $responses->avg_response_time,
                        'time_performance_score' => $responses->avg_time_score,
                        'total_points_earned' => $responses->total_points_earned
                    ]);
                    
                Log::info("Assessment completed successfully", [
                    'assessment_id' => $assessmentId,
                    'accuracy' => $accuracy,
                    'total_points' => $responses->total_points_earned
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to complete assessment: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
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
        $user = Auth::user();
        
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
        $user = Auth::user();
        
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
                'user_id' => $user->id
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
        $user = Auth::user();
        
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
                'had_pause_state' => !empty($pauseState)
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
        $user = Auth::user();
        
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
        $user = Auth::user();
        
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
        $user = Auth::user();
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
     * Generate unique assessment ID
     */
    private function generateUniqueAssessmentId($userId, $competency, $difficulty, $index)
    {
        $timestamp = time();
        $microtime = microtime(true);
        $randomSuffix = substr(md5($microtime), 0, 6);
        
        return "ASSESS_{$userId}_{$competency}_{$difficulty}_{$index}_{$timestamp}_{$randomSuffix}";
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
}