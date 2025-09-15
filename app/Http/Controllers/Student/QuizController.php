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
     * Show quiz interface
     */
    public function show(Request $request, $category)
    {
        $user = Auth::user();
        
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
        $user = Auth::user();
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
        
        // Get current question
        $currentQuestion = $this->getCurrentQuestion($assessment->assessment_id);
        
        if (!$currentQuestion) {
            return redirect()->route('student.assessments.category', $category)
                ->with('error', 'No questions available for assessment.');
        }
        
        // Get progress
        $progress = $this->getAssessmentProgress($assessment->assessment_id);
        
        return view('student.quiz', [
            'category' => $category,
            'question' => $currentQuestion,
            'currentQuestion' => $progress['answered_questions'] + 1,
            'totalQuestions' => $progress['total_questions'] ?? 15,
            'diagnosticMode' => false,
            'assessmentId' => $assessment->assessment_id
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
            $scriptPath = base_path('public/algorithm/question_cooldown.py');
            $command = "python \"{$scriptPath}\" get_available {$userId} {$competency} {$difficultyLevel} {$questionCount}";
            
            $output = shell_exec($command);
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
            $scriptPath = base_path('public/algorithm/question_cooldown.py');
            $command = "python \"{$scriptPath}\" get_available {$userId} {$competency} {$difficultyLevel} 15";
            
            $output = shell_exec($command);
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
        $shuffleScript = base_path('public/algorithm/fisher_yates.py');
        $questionsJson = json_encode($questions);
        $shuffleCommand = "python \"{$shuffleScript}\" create_pool {$assessmentId} '" . addslashes($questionsJson) . "'";
        
        $shuffleOutput = shell_exec($shuffleCommand);
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
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" next_question {$assessmentId}";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success'] && isset($result['question'])) {
                $q = $result['question'];
                return (object) [
                    'question_id' => $q['question_id'],
                    'text' => $q['question_text'],
                    'type' => $q['question_type'] ?? 'multiple_choice',
                    'options' => json_decode($q['options'] ?? '[]', true),
                    'max_time' => $q['max_time_seconds'] ?? 30,
                    'difficulty_level' => $q['difficulty_level'] ?? 'beginner'
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
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" progress {$assessmentId}";
            
            $output = shell_exec($command);
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
        $request->validate([
            'question_id' => 'required|string',
            'answer' => 'required|string',
            'time_taken' => 'required|integer|min:1',
            'assessment_id' => 'required|string'
        ]);
        
        $user = Auth::user();
        
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
            $normalizedTime = $request->time_taken / $question->max_time_seconds;
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
            
            // Record response
            $responseId = "RESP_{$request->assessment_id}_{$request->question_id}_" . time();
            
            DB::table('question_responses')->insert([
                'response_id' => $responseId,
                'assessment_id' => $request->assessment_id,
                'user_id' => $user->id,
                'question_id' => $request->question_id,
                'user_answer' => $request->answer,
                'is_correct' => $isCorrect,
                'response_time' => $request->time_taken,
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
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" mark_answered {$request->assessment_id} {$request->question_id}";
            shell_exec($command);
            
            // Get next question
            $nextQuestion = $this->getCurrentQuestion($request->assessment_id);
            $progress = $this->getAssessmentProgress($request->assessment_id);
            
            if (!$nextQuestion || $progress['answered_questions'] >= $progress['total_questions']) {
                // Assessment complete
                $this->completeAssessment($request->assessment_id);
                
                return response()->json([
                    'success' => true,
                    'is_correct' => $isCorrect,
                    'correct_answer' => $question->correct_answer,
                    'explanation' => $question->explanation,
                    'assessment_complete' => true,
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
                'assessment_complete' => false
            ]);
            
        } catch (\Exception $e) {
            Log::error('Answer submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process answer. Please try again.'
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
        if ($masteryScore >= 75 && $currentDifficulty !== 'advanced') {
            return 'advanced';
        } elseif ($masteryScore >= 50 && $masteryScore < 75 && $currentDifficulty !== 'intermediate') {
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
}