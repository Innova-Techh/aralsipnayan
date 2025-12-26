<?php

namespace App\Services;

use App\Models\RadmDetection;
use App\Models\QuestionResponse;
use App\Models\AssessmentSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Random Answering Detection Model (RADM) Service
 * 
 * Implements the RADM algorithm to detect non-deliberate answering patterns
 * based on time behavior, accuracy, and BKT contradiction indicators.
 * 
 * Evaluates every 5 consecutive answered questions.
 */
class RadmService
{
    /**
     * Window size for evaluation
     */
    private const WINDOW_SIZE = 5;

    /**
     * Expected response times by difficulty (in seconds)
     * Based on Kennette & McGuckin (2025) baseline of 39 seconds
     */
    private const EXPECTED_TIMES = [
        'beginner' => 23,      // 0.6 x 39
        'intermediate' => 31,  // 0.8 x 39
        'advanced' => 39,      // 1.0 x 39
    ];

    /**
     * Time threshold multiplier (avg_time < 0.4 x texp)
     */
    private const TIME_THRESHOLD_MULTIPLIER = 0.4;

    /**
     * Accuracy threshold (accuracy < 0.30)
     */
    private const ACCURACY_THRESHOLD = 0.30;

    /**
     * BKT probability threshold for contradiction (PK >= 0.70)
     */
    private const BKT_THRESHOLD = 0.70;

    /**
     * Consecutive wrong answers threshold (w >= 3)
     */
    private const CONSECUTIVE_WRONG_THRESHOLD = 3;

    /**
     * RAI component weights
     */
    private const TIME_WEIGHT = 0.35;
    private const ACCURACY_WEIGHT = 0.35;
    private const BKT_WEIGHT = 0.30;

    /**
     * RAI intervention threshold (RAI >= 0.60)
     */
    private const RAI_INTERVENTION_THRESHOLD = 0.60;

    /**
     * Evaluate RADM for a session's recent responses
     * 
     * @param string $sessionId
     * @param int $studentId
     * @param string $difficultyLevel
     * @return RadmDetection|null Returns detection record if window is complete, null otherwise
     */
    public function evaluateSession(string $sessionId, int $studentId, string $difficultyLevel): ?RadmDetection
    {
        \Log::info('[RADM] Starting evaluation', [
            'session_id' => $sessionId,
            'student_id' => $studentId,
            'difficulty_level' => $difficultyLevel
        ]);

        // Get the assessment session from assessment_sessions table (for regular quizzes)
        $session = DB::table('assessment_sessions')
            ->where('session_id', $sessionId)
            ->where('user_id', $studentId)
            ->first();

        if (!$session) {
            \Log::warning('[RADM] Session not found', [
                'session_id' => $sessionId,
                'student_id' => $studentId
            ]);
            return null;
        }

        \Log::info('[RADM] Session found', [
            'session_id' => $session->session_id,
            'assessment_id' => $session->assessment_id,
            'questions_answered' => $session->questions_answered
        ]);

        // Get responses for this session from question_responses table
        // Match by assessment_id and user_id since question_responses doesn't have session_id
        $responses = DB::table('question_responses')
            ->where('assessment_id', $session->assessment_id)
            ->where('user_id', $studentId)
            ->orderBy('answered_at', 'asc')
            ->get();

        $totalResponses = $responses->count();

        \Log::info('[RADM] Responses found', [
            'total_responses' => $totalResponses,
            'window_size' => self::WINDOW_SIZE,
            'at_boundary' => $totalResponses % self::WINDOW_SIZE === 0
        ]);

        // Only evaluate if we have at least 5 responses
        if ($totalResponses < self::WINDOW_SIZE) {
            \Log::info('[RADM] Not enough responses', [
                'total' => $totalResponses,
                'needed' => self::WINDOW_SIZE
            ]);
            return null;
        }

        // Calculate which window to evaluate
        // Evaluate every 5 questions: at response 5, 10, 15, etc.
        if ($totalResponses % self::WINDOW_SIZE !== 0) {
            \Log::info('[RADM] Not at window boundary', [
                'total_responses' => $totalResponses,
                'remainder' => $totalResponses % self::WINDOW_SIZE
            ]);
            return null; // Not at a window boundary
        }

        // Get the last 5 responses
        $windowResponses = $responses->slice(-self::WINDOW_SIZE);

        \Log::info('[RADM] Evaluating window', [
            'window_start' => $totalResponses - self::WINDOW_SIZE + 1,
            'window_end' => $totalResponses,
            'response_times' => $windowResponses->pluck('response_time')->toArray(),
            'correctness' => $windowResponses->pluck('is_correct')->toArray()
        ]);

        return $this->evaluateWindow($sessionId, $studentId, $windowResponses, $difficultyLevel);
    }

    /**
     * Evaluate a specific window of responses
     * 
     * @param string $sessionId
     * @param int $studentId
     * @param Collection $responses Collection of 5 QuestionResponse models
     * @param string $difficultyLevel
     * @return RadmDetection
     */
    public function evaluateWindow(
        string $sessionId, 
        int $studentId, 
        Collection $responses, 
        string $difficultyLevel
    ): RadmDetection {
        // Extract data from responses
        $responseTimes = $responses->pluck('response_time')->toArray(); // question_responses uses response_time
        $correctness = $responses->pluck('is_correct')->toArray();
        $questionIds = $responses->pluck('question_id')->toArray();
        $lastBktProbability = $responses->last()->bkt_after ?? 0; // question_responses uses bkt_after

        // Calculate window indices
        $windowEndIndex = $responses->count();
        $windowStartIndex = $windowEndIndex - self::WINDOW_SIZE + 1;

        // 1. Calculate Time Behavior Indicator (T)
        $timeData = $this->calculateTimeIndicator($responseTimes, $difficultyLevel);

        // 2. Calculate Accuracy Indicator (A)
        $accuracyData = $this->calculateAccuracyIndicator($correctness);

        // 3. Calculate BKT Contradiction Indicator (B)
        $bktData = $this->calculateBktIndicator($correctness, $lastBktProbability);

        // 4. Calculate Random Answering Index (RAI)
        $raiScore = $this->calculateRAI(
            $timeData['flag'],
            $accuracyData['flag'],
            $bktData['flag']
        );

        // Determine if intervention should be triggered
        $interventionTriggered = $raiScore >= self::RAI_INTERVENTION_THRESHOLD;

        \Log::info('[RADM] RAI Calculation Complete', [
            'time_flag' => $timeData['flag'],
            'accuracy_flag' => $accuracyData['flag'],
            'bkt_flag' => $bktData['flag'],
            'rai_score' => $raiScore,
            'intervention_triggered' => $interventionTriggered,
            'threshold' => self::RAI_INTERVENTION_THRESHOLD
        ]);

        // Get assessment ID from session (we already queried this at the start of evaluateSession)
        $session = DB::table('assessment_sessions')
            ->where('session_id', $sessionId)
            ->where('user_id', $studentId)
            ->first();
        
        $assessmentId = $session ? $session->assessment_id : null;

        // Create detection record
        $detection = RadmDetection::create([
            'session_id' => $sessionId,
            'student_id' => $studentId,
            'assessment_id' => $assessmentId,
            'window_start_index' => $windowStartIndex,
            'window_end_index' => $windowEndIndex,
            'question_ids' => $questionIds,
            'avg_response_time' => $timeData['avg_time'],
            'expected_time' => $timeData['expected_time'],
            'time_threshold' => $timeData['threshold'],
            'time_flag' => $timeData['flag'],
            'correct_count' => $accuracyData['correct_count'],
            'total_count' => $accuracyData['total_count'],
            'accuracy' => $accuracyData['accuracy'],
            'accuracy_flag' => $accuracyData['flag'],
            'bkt_probability' => $bktData['probability'],
            'consecutive_wrong' => $bktData['consecutive_wrong'],
            'bkt_flag' => $bktData['flag'],
            'rai_score' => $raiScore,
            'intervention_triggered' => $interventionTriggered,
            'difficulty_level' => $difficultyLevel,
            'response_times' => $responseTimes,
            'correctness' => $correctness,
        ]);

        \Log::info('[RADM] Detection record created', [
            'detection_id' => $detection->id,
            'session_id' => $sessionId,
            'student_id' => $studentId,
            'rai_score' => $detection->rai_score,
            'intervention_triggered' => $detection->intervention_triggered
        ]);

        return $detection;
    }

    /**
     * Calculate Time Behavior Indicator (T)
     * 
     * T = 1 if avg_time < 0.4 × texp
     * T = 0 otherwise
     */
    private function calculateTimeIndicator(array $responseTimes, string $difficultyLevel): array
    {
        $expectedTime = self::EXPECTED_TIMES[$difficultyLevel] ?? self::EXPECTED_TIMES['intermediate'];
        $avgTime = array_sum($responseTimes) / count($responseTimes);
        $threshold = $expectedTime * self::TIME_THRESHOLD_MULTIPLIER;
        $flag = $avgTime < $threshold;

        return [
            'avg_time' => round($avgTime, 2),
            'expected_time' => $expectedTime,
            'threshold' => round($threshold, 2),
            'flag' => $flag,
        ];
    }

    /**
     * Calculate Accuracy Indicator (A)
     * 
     * A = 1 if accuracy < 0.30
     * A = 0 otherwise
     */
    private function calculateAccuracyIndicator(array $correctness): array
    {
        $correctCount = count(array_filter($correctness));
        $totalCount = count($correctness);
        $accuracy = $totalCount > 0 ? $correctCount / $totalCount : 0;
        $flag = $accuracy < self::ACCURACY_THRESHOLD;

        return [
            'correct_count' => $correctCount,
            'total_count' => $totalCount,
            'accuracy' => round($accuracy, 4),
            'flag' => $flag,
        ];
    }

    /**
     * Calculate BKT Contradiction Indicator (B)
     * 
     * B = 1 if PK >= 0.70 AND w >= 3
     * B = 0 otherwise
     */
    private function calculateBktIndicator(array $correctness, float $bktProbability): array
    {
        // Count consecutive wrong answers from the end
        $consecutiveWrong = 0;
        for ($i = count($correctness) - 1; $i >= 0; $i--) {
            if (!$correctness[$i]) {
                $consecutiveWrong++;
            } else {
                break;
            }
        }

        $flag = $bktProbability >= self::BKT_THRESHOLD && 
                $consecutiveWrong >= self::CONSECUTIVE_WRONG_THRESHOLD;

        return [
            'probability' => round($bktProbability, 4),
            'consecutive_wrong' => $consecutiveWrong,
            'flag' => $flag,
        ];
    }

    /**
     * Calculate Random Answering Index (RAI)
     * 
     * RAI = (0.35 × T) + (0.35 × A) + (0.30 × B)
     */
    private function calculateRAI(bool $timeFlag, bool $accuracyFlag, bool $bktFlag): float
    {
        $rai = (self::TIME_WEIGHT * ($timeFlag ? 1 : 0)) +
               (self::ACCURACY_WEIGHT * ($accuracyFlag ? 1 : 0)) +
               (self::BKT_WEIGHT * ($bktFlag ? 1 : 0));

        return round($rai, 4);
    }

    /**
     * Check if there's an unacknowledged intervention for a session
     */
    public function hasUnacknowledgedIntervention(string $sessionId, int $studentId): bool
    {
        return RadmDetection::forSession($sessionId)
            ->forStudent($studentId)
            ->unacknowledged()
            ->exists();
    }

    /**
     * Get the latest unacknowledged intervention for a session
     */
    public function getLatestUnacknowledgedIntervention(string $sessionId, int $studentId): ?RadmDetection
    {
        return RadmDetection::forSession($sessionId)
            ->forStudent($studentId)
            ->unacknowledged()
            ->latest()
            ->first();
    }

    /**
     * Acknowledge an intervention
     */
    public function acknowledgeIntervention(int $detectionId): void
    {
        $detection = RadmDetection::find($detectionId);
        if ($detection) {
            $detection->acknowledge();
        }
    }

    /**
     * Get detection statistics for a session
     */
    public function getSessionStatistics(string $sessionId, int $studentId): array
    {
        $detections = RadmDetection::forSession($sessionId)
            ->forStudent($studentId)
            ->get();

        return [
            'total_evaluations' => $detections->count(),
            'interventions_triggered' => $detections->where('intervention_triggered', true)->count(),
            'interventions_acknowledged' => $detections->where('intervention_acknowledged', true)->count(),
            'average_rai' => $detections->avg('rai_score'),
            'time_flags' => $detections->where('time_flag', true)->count(),
            'accuracy_flags' => $detections->where('accuracy_flag', true)->count(),
            'bkt_flags' => $detections->where('bkt_flag', true)->count(),
        ];
    }
}
