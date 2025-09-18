<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentGenerationController extends Controller
{
    /**
     * Generate dynamic assessments based on student's mastery level
     */
    public function generateAssessments($competency, $currentDifficulty, $userId)
    {
        try {
            // First, cleanup any expired assessments and cooldowns
            $this->cleanupExpiredAssessments($userId, $competency);
            $this->cleanupExpiredCooldowns();
            
            // Define question counts per difficulty
            $questionCounts = [
                'beginner' => 15,
                'intermediate' => 20,
                'advanced' => 25
            ];
            
            $questionsPerAssessment = $questionCounts[$currentDifficulty];
            
            // Check for cached assessments in session to prevent duplicates on reload
            $sessionKey = "assessments_{$userId}_{$competency}_{$currentDifficulty}";
            $cachedAssessments = session($sessionKey);
            
            // Clear session cache to force fresh generation with unique IDs
            session()->forget($sessionKey);
            $cachedAssessments = null;
            
            if ($cachedAssessments && isset($cachedAssessments['generated_at']) && 
                time() - $cachedAssessments['generated_at'] < 300) { // Cache for 5 minutes
                Log::info("Using cached assessments to prevent duplicates", [
                    'user_id' => $userId,
                    'competency' => $competency,
                    'cache_age' => time() - $cachedAssessments['generated_at']
                ]);
                return $cachedAssessments['data'];
            }
            
            // Get available questions count using Fisher-Yates algorithm
            $availabilityCheck = $this->checkQuestionAvailability($userId, $competency, $currentDifficulty);
            
            if ($availabilityCheck['availableCount'] < $questionsPerAssessment) {
                $result = [
                    'availableQuestions' => $availabilityCheck['availableCount'],
                    'questionsInCooldown' => $availabilityCheck['cooldownCount'],
                    'assessmentOptions' => []
                ];
                
                // Cache the result
                session([$sessionKey => [
                    'data' => $result,
                    'generated_at' => time()
                ]]);
                
                return $result;
            }
            
            // Check for existing valid assessments first
            $existingAssessments = $this->getExistingValidAssessments($userId, $competency, $currentDifficulty);
            
            // Calculate how many assessments we should have
            $maxAssessments = 8; // Increased limit to allow more assessments
            $possibleAssessments = floor($availabilityCheck['availableCount'] / $questionsPerAssessment);
            $targetAssessmentCount = min($maxAssessments, $possibleAssessments);
            
            // Only use existing assessments if we have enough, otherwise generate new ones
            if (!empty($existingAssessments) && count($existingAssessments) >= $targetAssessmentCount) {
                Log::info("Using existing valid assessments", [
                    'user_id' => $userId,
                    'competency' => $competency,
                    'count' => count($existingAssessments)
                ]);
                
                $result = [
                    'availableQuestions' => $availabilityCheck['availableCount'],
                    'questionsInCooldown' => $availabilityCheck['cooldownCount'],
                    'assessmentOptions' => $existingAssessments
                ];
                
                // Cache the result
                session([$sessionKey => [
                    'data' => $result,
                    'generated_at' => time()
                ]]);
                
                return $result;
            } else {
                // Clear any insufficient existing assessments and generate fresh ones
                if (!empty($existingAssessments)) {
                    Log::info("Insufficient existing assessments, generating fresh ones", [
                        'existing_count' => count($existingAssessments),
                        'target_count' => $targetAssessmentCount
                    ]);
                }
            }
            
            // Generate new assessments only if none exist or insufficient existing ones
            $assessmentCount = $targetAssessmentCount;
            
            Log::info("Assessment generation calculation", [
                'available_questions' => $availabilityCheck['availableCount'],
                'questions_per_assessment' => $questionsPerAssessment,
                'possible_assessments' => $possibleAssessments,
                'max_assessments' => $maxAssessments,
                'target_assessment_count' => $targetAssessmentCount,
                'final_assessment_count' => $assessmentCount
            ]);
            
            $assessmentOptions = [];
            
            for ($i = 1; $i <= $assessmentCount; $i++) {
                // Create unique assessment ID with better randomization
                $assessmentId = $this->generateUniqueAssessmentId($userId, $competency, $currentDifficulty, $i);
                
                // Double-check for duplicates before creation
                $existingAssessment = DB::table('assessments')
                    ->where('assessment_id', $assessmentId)
                    ->first();
                
                if ($existingAssessment) {
                    Log::warning("Duplicate assessment ID detected, regenerating", [
                        'assessment_id' => $assessmentId,
                        'user_id' => $userId
                    ]);
                    // Regenerate with additional randomization
                    $assessmentId = $this->generateUniqueAssessmentId($userId, $competency, $currentDifficulty, $i . '_retry');
                }
                
                // Create assessment metadata only (not the full assessment yet)
                $assessmentOption = [
                    'assessment_id' => $assessmentId,
                    'title' => ucfirst($currentDifficulty) . " Assessment",
                    'question_count' => $questionsPerAssessment,
                    'time_limit' => $this->getTimeLimitForDifficulty($currentDifficulty),
                    'difficulty' => $currentDifficulty,
                    'topics' => $this->getTopicsForCompetency($competency, $currentDifficulty),
                    'estimated_points' => $this->calculateEstimatedPoints($questionsPerAssessment, $currentDifficulty),
                    'best_completion_time' => $this->getBestCompletionTime($userId, $competency),
                    'created_at' => now(),
                    'is_generated' => false // Flag to indicate this is just metadata
                ];
                
                $assessmentOptions[] = $assessmentOption;
                
                Log::info("Generated assessment metadata", [
                    'assessment_id' => $assessmentId,
                    'user_id' => $userId,
                    'title' => $assessmentOption['title']
                ]);
            }
            
            $result = [
                'availableQuestions' => $availabilityCheck['availableCount'],
                'questionsInCooldown' => $availabilityCheck['cooldownCount'],
                'assessmentOptions' => $assessmentOptions
            ];
            
            // Cache the result to prevent duplicates on page reload
            session([$sessionKey => [
                'data' => $result,
                'generated_at' => time()
            ]]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Assessment generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
                'competency' => $competency,
                'difficulty' => $currentDifficulty
            ]);
            
            return [
                'availableQuestions' => 0,
                'questionsInCooldown' => 0,
                'assessmentOptions' => []
            ];
        }
    }
    
    /**
     * Create actual assessment when user starts it (not during page load)
     */
    public function createActualAssessment($assessmentId, $userId, $competency, $difficulty)
    {
        try {
            // First, check if there's already an active assessment for this user and competency
            $existingAssessment = DB::table('assessments')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->where('status', 'in_progress')
                ->where('started_at', '>=', now()->subHours(2)) // Only check recent assessments
                ->orderBy('started_at', 'desc')
                ->first();
            
            if ($existingAssessment) {
                // Check if this assessment has an active session
                $existingSession = DB::table('assessment_sessions')
                    ->where('assessment_id', $existingAssessment->assessment_id)
                    ->where('user_id', $userId)
                    ->where('status', 'in_progress')
                    ->first();
                
                if ($existingSession) {
                    Log::info("Returning existing assessment instead of creating new one", [
                        'existing_assessment_id' => $existingAssessment->assessment_id,
                        'existing_session_id' => $existingSession->session_id,
                        'user_id' => $userId
                    ]);
                    
                    return [
                        'success' => true,
                        'exists' => true, // Mark as existing
                        'assessment' => $existingAssessment,
                        'session_id' => $existingSession->session_id,
                        'question_count' => $existingAssessment->total_questions,
                        'time_limit' => $existingAssessment->time_limit
                    ];
                }
            }
            
            // ALWAYS generate a new unique ID instead of using the passed one
            // This prevents duplicate key violations from cached/reused IDs
            $newAssessmentId = $this->generateUniqueAssessmentId($userId, $competency, $difficulty, rand(1, 999));
            
            // Double-check that the new ID is truly unique
            $retryCount = 0;
            while (DB::table('assessments')->where('assessment_id', $newAssessmentId)->exists() && $retryCount < 10) {
                $newAssessmentId = $this->generateUniqueAssessmentId($userId, $competency, $difficulty, rand(1000, 9999));
                $retryCount++;
            }
            
            if ($retryCount >= 10) {
                Log::error("Failed to generate unique assessment ID after 10 attempts", [
                    'user_id' => $userId,
                    'competency' => $competency,
                    'difficulty' => $difficulty
                ]);
                return [
                    'success' => false,
                    'message' => 'Unable to generate unique assessment ID. Please try again.'
                ];
            }
            
            Log::info("Generated new unique assessment ID", [
                'original_id' => $assessmentId,
                'new_id' => $newAssessmentId,
                'user_id' => $userId
            ]);
            
            // Define question counts
            $questionCounts = [
                'beginner' => 15,
                'intermediate' => 20,
                'advanced' => 25
            ];
            
            $questionCount = $questionCounts[$difficulty] ?? 15;
            $timeLimit = $this->getTimeLimitForDifficulty($difficulty);
            
            // Create assessment record FIRST before any other database operations
            $assessmentData = [
                'assessment_id' => $newAssessmentId,
                'user_id' => $userId,
                'competency' => $competency,
                'assessment_type' => 'regular',
                'difficulty_level' => $difficulty,
                'total_questions' => $questionCount,
                'time_limit' => $timeLimit,
                'status' => 'in_progress',
                'started_at' => now(),
                'is_diagnostic_phase' => 0,
                'correct_answers' => 0,
                'incorrect_answers' => 0,
                'questions_answered' => 0,
                'total_time_spent' => 0,
                'cumulative_time_score' => 0.0000
            ];
            
            // Insert assessment record first
            DB::table('assessments')->insert($assessmentData);
            
            Log::info("Assessment record created successfully", [
                'assessment_id' => $newAssessmentId,
                'user_id' => $userId
            ]);
            
            // Now create assessment pool using Fisher-Yates algorithm with existing assessment ID
            $poolResult = $this->createAssessmentPoolWithFisherYates(
                $newAssessmentId, 
                $userId, 
                $competency, 
                $difficulty, 
                $questionCount
            );
            
            if (!$poolResult['success']) {
                // Clean up assessment record if pool creation fails
                DB::table('assessments')->where('assessment_id', $newAssessmentId)->delete();
                return $poolResult;
            }
            
            // Create assessment session after pool creation
            $questionsArray = $poolResult['shuffled_questions'] ?? ($poolResult['questions'] ?? ($poolResult['available_questions'] ?? []));
            $sessionResult = $this->createAssessmentSession($newAssessmentId, $userId, $competency, $difficulty, $questionCount, $questionsArray);
            
            if (!$sessionResult['success']) {
                // Clean up if session creation fails
                DB::table('assessments')->where('assessment_id', $newAssessmentId)->delete();
                Log::error("Failed to create assessment session", [
                    'assessment_id' => $newAssessmentId,
                    'error' => $sessionResult['message']
                ]);
                return $sessionResult;
            }
            
            
            Log::info("Assessment created successfully", [
                'assessment_id' => $newAssessmentId,
                'assessment_id_length' => strlen($newAssessmentId),
                'question_count' => $poolResult['total_questions']
            ]);
            
            return [
                'success' => true,
                'exists' => false,
                'assessment' => (object) $assessmentData,
                'session_id' => $sessionResult['session_id'],
                'question_count' => $poolResult['total_questions'],
                'time_limit' => $timeLimit
            ];
            
        } catch (\Exception $e) {
            Log::error('Assessment creation failed: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to create assessment: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create assessment session record
     */
    private function createAssessmentSession($assessmentId, $userId, $competency, $difficulty, $questionCount, $questions)
    {
        try {
            // Generate unique session ID
            $sessionId = 'SESS_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4));
            
            // Ensure session ID is unique
            while (DB::table('assessment_sessions')->where('session_id', $sessionId)->exists()) {
                $sessionId = 'SESS_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4));
            }
            
            $timeLimit = $this->getTimeLimitForDifficulty($difficulty);
            $now = now();
            
            // Ensure questions is an array and properly formatted
            if (!is_array($questions)) {
                $questions = [];
            }
            
            // Extract just the question IDs if questions have full data
            $questionIds = [];
            foreach ($questions as $question) {
                if (is_array($question) && isset($question['question_id'])) {
                    $questionIds[] = $question['question_id'];
                } elseif (is_string($question) || is_numeric($question)) {
                    $questionIds[] = $question;
                }
            }
            
            $sessionData = [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'assessment_id' => $assessmentId,
                'session_type' => 'adaptive',
                'competency' => $competency,
                'difficulty_level' => $difficulty,
                'total_questions' => $questionCount,
                'questions_json' => json_encode($questionIds),
                'current_question_index' => 0,
                'time_limit_minutes' => $timeLimit,
                'total_time_allowed_seconds' => $timeLimit * 60,
                'countdown_started_at' => $now,
                'countdown_expires_at' => $now->copy()->addMinutes($timeLimit),
                'time_remaining_seconds' => $timeLimit * 60,
                'auto_submit_on_timeout' => true,
                'beginner_question_time_limit' => 30,
                'intermediate_question_time_limit' => 45,
                'advanced_question_time_limit' => 60,
                'current_question_time_limit' => $difficulty === 'beginner' ? 30 : ($difficulty === 'intermediate' ? 45 : 60),
                'is_paused' => false,
                'questions_answered' => 0,
                'correct_answers' => 0,
                'incorrect_answers' => 0,
                'total_points_earned' => 0,
                'is_adaptive' => true,
                'status' => 'in_progress',
                'started_at' => $now,
                'last_activity_at' => $now
            ];
            
            DB::table('assessment_sessions')->insert($sessionData);
            
            Log::info("Assessment session created successfully", [
                'session_id' => $sessionId,
                'assessment_id' => $assessmentId,
                'user_id' => $userId,
                'question_count' => count($questionIds)
            ]);
            
            return [
                'success' => true,
                'session_id' => $sessionId,
                'session_data' => $sessionData
            ];
            
        } catch (\Exception $e) {
            Log::error('Assessment session creation failed: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to create assessment session: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get existing valid assessments for user
     */
    private function getExistingValidAssessments($userId, $competency, $difficulty)
    {
        try {
            // Get existing assessments that are still valid (not expired, not completed)
            $existingAssessments = DB::table('assessments')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->where('difficulty_level', $difficulty)
                ->where('assessment_type', 'regular')
                ->where('status', 'in_progress')
                ->where('started_at', '>=', now()->subHours(2)) // Not older than 2 hours
                ->orderBy('started_at', 'desc')
                ->take(3) // Limit to 3 assessments
                ->get();
            
            $assessmentOptions = [];
            $index = 1;
            
            foreach ($existingAssessments as $assessment) {
                // Check if assessment has questions
                $questionCount = DB::table('assessment_questions')
                    ->where('assessment_id', $assessment->assessment_id)
                    ->count();
                
                if ($questionCount > 0) {
                    $assessmentOptions[] = [
                        'assessment_id' => $assessment->assessment_id,
                        'title' => ucfirst($difficulty) . " Assessment",
                        'question_count' => $assessment->total_questions,
                        'time_limit' => $assessment->time_limit,
                        'difficulty' => $difficulty,
                        'topics' => $this->getTopicsForCompetency($competency, $difficulty),
                        'estimated_points' => $this->calculateEstimatedPoints($assessment->total_questions, $difficulty),
                        'best_completion_time' => $this->getBestCompletionTime($userId, $competency),
                        'created_at' => $assessment->started_at,
                        'is_generated' => true, // Flag to indicate this is from database
                        'status' => $assessment->status
                    ];
                    $index++;
                }
            }
            
            return $assessmentOptions;
            
        } catch (\Exception $e) {
            Log::error('Failed to get existing assessments: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cleanup expired assessments
     */
    private function cleanupExpiredAssessments($userId, $competency)
    {
        try {
            // Mark old in_progress assessments as abandoned if they're older than 3 hours
            $expiredCount = DB::table('assessments')
                ->where('user_id', $userId)
                ->where('competency', $competency)
                ->where('status', 'in_progress')
                ->where('started_at', '<', now()->subHours(3))
                ->update([
                    'status' => 'abandoned',
                    'completed_at' => now()
                ]);
            
            if ($expiredCount > 0) {
                Log::info("Cleaned up $expiredCount expired assessments for user $userId");
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to cleanup expired assessments: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate unique assessment ID with better randomization (max 50 chars for DB)
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
    
    // ... rest of the existing methods remain the same ...
    
    /**
     * Check question availability using Fisher-Yates algorithm
     */
    private function checkQuestionAvailability($userId, $competency, $difficulty)
    {
        try {
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" get_available {$userId} {$competency} {$difficulty} 100";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                return [
                    'availableCount' => $result['total_found'] ?? count($result['available_questions']),
                    'cooldownCount' => $result['excluded_cooldown'] ?? 0,
                    'recentCount' => $result['excluded_recent'] ?? 0
                ];
            }
            
            // Fallback to database query if script fails
            return $this->fallbackAvailabilityCheck($userId, $competency, $difficulty);
            
        } catch (\Exception $e) {
            Log::error('Question availability check failed: ' . $e->getMessage());
            return $this->fallbackAvailabilityCheck($userId, $competency, $difficulty);
        }
    }
    
    /**
     * Fallback availability check using direct database queries
     */
    private function fallbackAvailabilityCheck($userId, $competency, $difficulty)
    {
        // Get total questions for difficulty
        $totalQuestions = DB::table('questions')
            ->where('competency', $competency)
            ->where('difficulty_level', $difficulty)
            ->where('is_active', 1)
            ->count();
        
        // Get questions in cooldown
        $cooldownQuestions = DB::table('question_cooldowns')
            ->where('user_id', $userId)
            ->where('competency', $competency)
            ->where('is_active', true)
            ->where('cooldown_until', '>', now())
            ->count();
        
        return [
            'availableCount' => max(0, $totalQuestions - $cooldownQuestions),
            'cooldownCount' => $cooldownQuestions,
            'recentCount' => 0
        ];
    }
    
    /**
     * Create assessment pool using Fisher-Yates algorithm
     */
    private function createAssessmentPoolWithFisherYates($assessmentId, $userId, $competency, $difficulty, $questionCount)
    {
        try {
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" create_pool \"{$assessmentId}\" {$userId} \"{$competency}\" \"{$difficulty}\" {$questionCount}";
            
            Log::info("Creating assessment pool", [
                'command' => $command,
                'assessment_id' => $assessmentId
            ]);
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                Log::info("Assessment pool created successfully", [
                    'assessment_id' => $assessmentId,
                    'question_count' => $result['total_questions']
                ]);
                return $result;
            } else {
                Log::error("Fisher-Yates pool creation failed", [
                    'assessment_id' => $assessmentId,
                    'error' => $result['message'] ?? 'Unknown error',
                    'output' => $output
                ]);
                return ['success' => false, 'message' => $result['message'] ?? 'Unknown error'];
            }
            
        } catch (\Exception $e) {
            Log::error('Fisher-Yates pool creation error: ' . $e->getMessage(), [
                'assessment_id' => $assessmentId
            ]);
            return ['success' => false, 'message' => 'Pool creation error'];
        }
    }
    
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
     * Calculate estimated points based on difficulty and question count
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
     * Get best completion time for user in this competency
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
     * Refresh assessments - regenerate available assessments
     */
    public function refreshAssessments(Request $request, $category)
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Convert category format
        $dbCompetency = strtolower($category);
        
        // Get user's mastery level
        $mastery = DB::table('student_mastery')
            ->where('user_id', $userId)
            ->where('competency', $dbCompetency)
            ->first();
        
        if (!$mastery || !$mastery->has_taken_diagnostic) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete the diagnostic test first.'
            ]);
        }
        
        // Clear cached assessments for refresh
        $sessionKey = "assessments_{$userId}_{$dbCompetency}_{$mastery->current_difficulty}";
        session()->forget($sessionKey);
        
        // Clean up expired cooldowns and assessments first
        $this->cleanupExpiredAssessments($userId, $dbCompetency);
        $this->cleanupExpiredCooldowns();
        
        // Generate fresh assessments
        $assessmentData = $this->generateAssessments($dbCompetency, $mastery->current_difficulty, $userId);
        
        return response()->json([
            'success' => true,
            'data' => $assessmentData,
            'message' => 'Assessments refreshed successfully'
        ]);
    }
    
    /**
     * Clean up expired cooldowns
     */
    private function cleanupExpiredCooldowns()
    {
        try {
            $scriptPath = base_path('public/algorithm/fisher_yates.py');
            $command = "python \"{$scriptPath}\" cleanup_cooldowns";
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && $result['success']) {
                Log::info("Cooldowns cleaned up successfully");
            }
        } catch (\Exception $e) {
            Log::error('Cooldown cleanup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Validate assessment before starting
     */
    public function validateAssessment(Request $request)
    {
        $assessmentId = $request->input('assessment_id');
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
        
        return response()->json([
            'success' => true,
            'assessment' => $assessment,
            'question_count' => $questionCount
        ]);
    }
}