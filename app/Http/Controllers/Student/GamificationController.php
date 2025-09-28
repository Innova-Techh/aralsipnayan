<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GamificationController extends Controller
{
    // Gamification Configuration Constants
    private const POINTS_PER_LEVEL = 60;
    private const MAX_LEVEL = 100;
    private const MAX_POINTS = 6000;
    private const DAILY_STREAK_BONUS = 5;

    // Base Points per Difficulty (from gamification.md)
    private const BASE_POINTS = [
        'beginner' => 3,
        'intermediate' => 6,
        'advanced' => 8
    ];

    // Time Bonus Points (from gamification.md)
    private const TIME_BONUS = [
        'fast_correct' => 15,
        'medium_correct' => 13,
        'slow_correct' => 10,
        'incorrect' => 5
    ];

    // Rank Configuration (from gamification.md)
    private const RANKS = [
        10 => ['name' => 'Math Explorer', 'icon' => '🌱', 'points' => 540],
        20 => ['name' => 'Math Adventurer', 'icon' => '🚀', 'points' => 1140],
        30 => ['name' => 'Math Seeker', 'icon' => '🔍', 'points' => 1740],
        40 => ['name' => 'Math Strategist', 'icon' => '🧠', 'points' => 2340],
        50 => ['name' => 'Math Innovator', 'icon' => '💡', 'points' => 2940],
        60 => ['name' => 'Math Prodigy', 'icon' => '⭐', 'points' => 3540],
        70 => ['name' => 'Math Virtuoso', 'icon' => '🎭', 'points' => 4140],
        80 => ['name' => 'Math Sage', 'icon' => '🧙', 'points' => 4740],
        90 => ['name' => 'Math Champion', 'icon' => '🏅', 'points' => 5340],
        100 => ['name' => 'Math Grandmaster', 'icon' => '👑', 'points' => 5940]
    ];

    // Milestone Badges (from gamification.md)
    private const BADGES = [
        'first_steps' => ['points' => 0, 'name' => 'First Steps', 'icon' => '🎯', 'description' => 'Complete your first assessment'],
        'quick_learner' => ['points' => 50, 'name' => 'Quick Learner', 'icon' => '⚡', 'description' => 'Earn 50 total points'],
        'on_fire' => ['points' => 150, 'name' => 'On Fire', 'icon' => '🔥', 'description' => 'Earn 150 total points'],
        'math_explorer' => ['points' => 300, 'name' => 'Math Explorer', 'icon' => '🚀', 'description' => 'Earn 300 total points'],
        'math_whiz' => ['points' => 500, 'name' => 'Math Whiz', 'icon' => '🌟', 'description' => 'Earn 500 total points'],
        'grade_champion' => ['points' => 750, 'name' => 'Grade Champion', 'icon' => '💎', 'description' => 'Earn 750 total points'],
        'sapphire' => ['points' => 1000, 'name' => 'Sapphire', 'icon' => '♦️', 'description' => 'Earn 1,000 total points'],
        'ruby' => ['points' => 1250, 'name' => 'Ruby', 'icon' => '🔶', 'description' => 'Earn 1,250 total points'],
        'crown' => ['points' => 1500, 'name' => 'Crown', 'icon' => '👑', 'description' => 'Earn 1,500 total points']
    ];

    /**
     * Record question points from assessment
     * Main method called by RegularAssessmentController
     */
    public function recordQuestionPoints($userId, $assessmentId, $questionId, $isCorrect, $timeTaken, $maxAllowedTime, $difficulty, $competency)
    {
        try {
            DB::beginTransaction();

            // Calculate points according to gamification system
            $basePoints = $this->calculateBasePoints($difficulty);
            $timeBonus = $this->calculateTimeBonus($isCorrect, $timeTaken, $maxAllowedTime);
            $totalPoints = $basePoints + $timeBonus;

            // Record base question points transaction
            $this->recordPointsTransaction(
                $userId, 
                $basePoints, 
                'base_question', 
                $questionId, 
                $assessmentId, 
                $competency, 
                $difficulty,
                "Base points for {$difficulty} question"
            );

            // Record time bonus transaction (if any)
            if ($timeBonus > 0) {
                $bonusType = $isCorrect ? 'time_bonus_correct' : 'time_bonus_incorrect';
                $this->recordPointsTransaction(
                    $userId, 
                    $timeBonus, 
                    'time_bonus', 
                    $questionId, 
                    $assessmentId, 
                    $competency, 
                    $difficulty,
                    "Time bonus for " . ($isCorrect ? 'correct' : 'incorrect') . " answer"
                );
            }

            // Update user progress
            $this->updateUserProgress($userId, $totalPoints, $competency);

            DB::commit();

            return [
                'success' => true,
                'base_points' => $basePoints,
                'time_bonus' => $timeBonus,
                'total_points' => $totalPoints
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to record gamification points: ' . $e->getMessage(), [
                'user_id' => $userId,
                'question_id' => $questionId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate base points based on difficulty
     */
    private function calculateBasePoints($difficulty)
    {
        return self::BASE_POINTS[$difficulty] ?? self::BASE_POINTS['beginner'];
    }

    /**
     * Calculate time bonus based on response speed and correctness
     */
    private function calculateTimeBonus($isCorrect, $timeTaken, $maxAllowedTime)
    {
        $normalizedTime = $timeTaken / $maxAllowedTime;

        if ($isCorrect) {
            if ($normalizedTime <= 0.5) {
                return self::TIME_BONUS['fast_correct']; // Fast + correct: +15
            } elseif ($normalizedTime <= 0.8) {
                return self::TIME_BONUS['medium_correct']; // Medium + correct: +13
            } else {
                return self::TIME_BONUS['slow_correct']; // Slow + correct: +10
            }
        } else {
            return self::TIME_BONUS['incorrect']; // Any incorrect: +5
        }
    }

    /**
     * Record a points transaction
     */
    private function recordPointsTransaction($userId, $points, $pointsType, $questionId = null, $assessmentId = null, $competency = null, $difficulty = null, $description = '')
    {
        $transactionId = $this->generateTransactionId($userId, $pointsType);

        DB::table('points_transactions')->insert([
            'transaction_id' => $transactionId,
            'user_id' => $userId,
            'points_earned' => $points,
            'points_type' => $pointsType,
            'question_id' => $questionId,
            'assessment_id' => $assessmentId,
            'competency' => $competency,
            'difficulty_level' => $difficulty,
            'description' => $description,
            'metadata' => json_encode([
                'timestamp' => now()->toISOString(),
                'system_version' => '1.0'
            ]),
            'earned_at' => now()
        ]);
    }

    /**
     * Update user progress with new points
     */
    private function updateUserProgress($userId, $pointsEarned, $competency = null)
    {
        // Get or create user progress record
        $progress = DB::table('user_progress')->where('user_id', $userId)->first();

        if (!$progress) {
            // Create new progress record
            $progress = $this->createUserProgress($userId);
        }

        // Calculate new totals
        $newTotalPoints = $progress->total_points + $pointsEarned;
        $newLevel = $this->calculateLevel($newTotalPoints);
        $pointsInCurrentLevel = $newTotalPoints % self::POINTS_PER_LEVEL;

        // Check for rank advancement
        $newRank = $this->calculateRank($newLevel);
        $rankChanged = $progress->current_rank !== $newRank['name'];

        // Update daily streak if needed
        $streakData = $this->updateDailyStreak($userId, $progress, $competency);

        // Update user progress
        DB::table('user_progress')
            ->where('user_id', $userId)
            ->update([
                'total_points' => $newTotalPoints,
                'current_level' => $newLevel,
                'points_in_current_level' => $pointsInCurrentLevel,
                'current_rank' => $newRank['name'],
                'rank_level_threshold' => $newRank['level'],
                'current_streak' => $streakData['current_streak'],
                'longest_streak' => $streakData['longest_streak'],
                'last_assessment_date' => $streakData['last_assessment_date'],
                'daily_competencies_completed' => json_encode($streakData['daily_competencies']),
                'last_daily_reset' => $streakData['last_daily_reset'],
                'updated_at' => now()
            ]);

        // Record rank advancement bonus if applicable
        if ($rankChanged && $newLevel >= 10) {
            $this->recordRankAdvancementBonus($userId, $newRank);
        }

        // Check and award badges
        $this->checkAndAwardBadges($userId, $newTotalPoints);

        return [
            'level_up' => $newLevel > $progress->current_level,
            'rank_up' => $rankChanged,
            'new_level' => $newLevel,
            'new_rank' => $newRank,
            'total_points' => $newTotalPoints,
            'streak_bonus' => $streakData['streak_bonus_earned']
        ];
    }

    /**
     * Create initial user progress record
     */
    private function createUserProgress($userId)
    {
        DB::table('user_progress')->insert([
            'user_id' => $userId,
            'total_points' => 0,
            'current_level' => 1,
            'points_in_current_level' => 0,
            'current_rank' => 'Math Explorer',
            'rank_level_threshold' => 10,
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_assessment_date' => null,
            'daily_competencies_completed' => json_encode([]),
            'last_daily_reset' => Carbon::today(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return DB::table('user_progress')->where('user_id', $userId)->first();
    }

    /**
     * Calculate level based on total points
     */
    private function calculateLevel($totalPoints)
    {
        $level = intval($totalPoints / self::POINTS_PER_LEVEL) + 1;
        return min($level, self::MAX_LEVEL);
    }

    /**
     * Calculate rank based on level
     */
    private function calculateRank($level)
    {
        $rankLevel = 10;
        foreach (array_keys(self::RANKS) as $threshold) {
            if ($level >= $threshold) {
                $rankLevel = $threshold;
            }
        }

        return [
            'level' => $rankLevel,
            'name' => self::RANKS[$rankLevel]['name'],
            'icon' => self::RANKS[$rankLevel]['icon'],
            'required_points' => self::RANKS[$rankLevel]['points']
        ];
    }

    /**
     * Update daily streak and competency tracking
     */
    private function updateDailyStreak($userId, $progress, $competency)
    {
        $today = Carbon::today();
        $lastAssessmentDate = $progress->last_assessment_date ? Carbon::parse($progress->last_assessment_date) : null;
        $lastDailyReset = $progress->last_daily_reset ? Carbon::parse($progress->last_daily_reset) : null;

        // Parse daily competencies
        $dailyCompetencies = $progress->daily_competencies_completed 
            ? json_decode($progress->daily_competencies_completed, true) 
            : [];

        // Reset daily tracking if new day
        if (!$lastDailyReset || $lastDailyReset->lt($today)) {
            $dailyCompetencies = [];
        }

        // Add current competency to today's completed list
        if ($competency && !in_array($competency, $dailyCompetencies)) {
            $dailyCompetencies[] = $competency;
        }

        // Calculate streak
        $currentStreak = $progress->current_streak;
        $longestStreak = $progress->longest_streak;
        $streakBonusEarned = 0;

        if (!$lastAssessmentDate || $lastAssessmentDate->lt($today)) {
            // First assessment of the day
            if ($lastAssessmentDate && $lastAssessmentDate->eq($today->copy()->subDay())) {
                // Consecutive day - increase streak
                $currentStreak++;
                $streakBonusEarned = self::DAILY_STREAK_BONUS;
                
                // Record streak bonus
                $this->recordPointsTransaction(
                    $userId,
                    self::DAILY_STREAK_BONUS,
                    'daily_streak',
                    null,
                    null,
                    null,
                    null,
                    "Daily streak bonus - Day {$currentStreak}"
                );
            } elseif (!$lastAssessmentDate) {
                // First ever assessment
                $currentStreak = 1;
            } else {
                // Streak broken - reset to 1
                $currentStreak = 1;
            }

            // Update longest streak if needed
            $longestStreak = max($longestStreak, $currentStreak);
        }

        return [
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'last_assessment_date' => $today,
            'daily_competencies' => $dailyCompetencies,
            'last_daily_reset' => $today,
            'streak_bonus_earned' => $streakBonusEarned
        ];
    }

    /**
     * Record rank advancement bonus
     */
    private function recordRankAdvancementBonus($userId, $rank)
    {
        $bonusPoints = 50; // Bonus points for rank advancement

        $this->recordPointsTransaction(
            $userId,
            $bonusPoints,
            'rank_advancement',
            null,
            null,
            null,
            null,
            "Rank advancement to {$rank['name']}"
        );

        // Add bonus to user progress
        DB::table('user_progress')
            ->where('user_id', $userId)
            ->increment('total_points', $bonusPoints);
    }

    /**
     * Check and award badges based on total points
     */
    private function checkAndAwardBadges($userId, $totalPoints)
    {
        foreach (self::BADGES as $badgeKey => $badge) {
            if ($totalPoints >= $badge['points']) {
                // Check if badge already awarded
                $existingBadge = DB::table('user_badges')
                    ->where('user_id', $userId)
                    ->where('badge_key', $badgeKey)
                    ->exists();

                if (!$existingBadge) {
                    // Award badge
                    DB::table('user_badges')->insert([
                        'user_id' => $userId,
                        'badge_key' => $badgeKey,
                        'badge_name' => $badge['name'],
                        'badge_icon' => $badge['icon'],
                        'badge_description' => $badge['description'],
                        'points_required' => $badge['points'],
                        'awarded_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId($userId, $pointsType)
    {
        $timestamp = time();
        $randomSuffix = substr(md5($userId . $pointsType . $timestamp), 0, 8);
        return "PT_{$userId}_{$timestamp}_{$randomSuffix}";
    }

    /**
     * Get user's current gamification status
     */
    public function getUserGamificationStatus($userId)
    {
        $progress = DB::table('user_progress')->where('user_id', $userId)->first();

        if (!$progress) {
            return $this->getDefaultGamificationStatus();
        }

        $currentRank = $this->calculateRank($progress->current_level);
        $nextRankLevel = $this->getNextRankLevel($progress->current_level);
        $nextRank = $nextRankLevel ? $this->calculateRank($nextRankLevel) : null;

        return [
            'total_points' => $progress->total_points,
            'current_level' => $progress->current_level,
            'points_in_current_level' => $progress->points_in_current_level,
            'points_to_next_level' => self::POINTS_PER_LEVEL - $progress->points_in_current_level,
            'current_rank' => $currentRank,
            'next_rank' => $nextRank,
            'current_streak' => $progress->current_streak,
            'longest_streak' => $progress->longest_streak,
            'last_assessment_date' => $progress->last_assessment_date,
            'progress_percentage' => ($progress->points_in_current_level / self::POINTS_PER_LEVEL) * 100
        ];
    }

    /**
     * Get default gamification status for new users
     */
    private function getDefaultGamificationStatus()
    {
        return [
            'total_points' => 0,
            'current_level' => 1,
            'points_in_current_level' => 0,
            'points_to_next_level' => self::POINTS_PER_LEVEL,
            'current_rank' => $this->calculateRank(1),
            'next_rank' => $this->calculateRank(10),
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_assessment_date' => null,
            'progress_percentage' => 0
        ];
    }

    /**
     * Get next rank level
     */
    private function getNextRankLevel($currentLevel)
    {
        foreach (array_keys(self::RANKS) as $threshold) {
            if ($currentLevel < $threshold) {
                return $threshold;
            }
        }
        return null;
    }

    /**
     * Get leaderboard data
     */
    public function getLeaderboard($type = 'weekly', $limit = 10)
    {
        $query = DB::table('user_progress')
            ->join('users', 'user_progress.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'user_progress.total_points',
                'user_progress.current_level',
                'user_progress.current_rank',
                'user_progress.current_streak'
            );

        if ($type === 'weekly') {
            // Get points earned this week
            $weekStart = Carbon::now()->startOfWeek();
            $query->addSelect(DB::raw('COALESCE(SUM(pt.points_earned), 0) as weekly_points'))
                ->leftJoin('points_transactions as pt', function($join) use ($weekStart) {
                    $join->on('pt.user_id', '=', 'users.id')
                         ->where('pt.earned_at', '>=', $weekStart);
                })
                ->groupBy('users.id', 'users.first_name', 'users.last_name', 
                         'user_progress.total_points', 'user_progress.current_level',
                         'user_progress.current_rank', 'user_progress.current_streak')
                ->orderBy('weekly_points', 'desc');
        } elseif ($type === 'monthly') {
            // Get points earned this month
            $monthStart = Carbon::now()->startOfMonth();
            $query->addSelect(DB::raw('COALESCE(SUM(pt.points_earned), 0) as monthly_points'))
                ->leftJoin('points_transactions as pt', function($join) use ($monthStart) {
                    $join->on('pt.user_id', '=', 'users.id')
                         ->where('pt.earned_at', '>=', $monthStart);
                })
                ->groupBy('users.id', 'users.first_name', 'users.last_name', 
                         'user_progress.total_points', 'user_progress.current_level',
                         'user_progress.current_rank', 'user_progress.current_streak')
                ->orderBy('monthly_points', 'desc');
        } else {
            // All-time leaderboard
            $query->orderBy('user_progress.total_points', 'desc');
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get user's badges
     */
    public function getUserBadges($userId)
    {
        return DB::table('user_badges')
            ->where('user_id', $userId)
            ->orderBy('awarded_at', 'desc')
            ->get();
    }

    /**
     * Get user's points history
     */
    public function getUserPointsHistory($userId, $limit = 50)
    {
        return DB::table('points_transactions')
            ->where('user_id', $userId)
            ->orderBy('earned_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Record assessment completion bonus
     */
    public function recordAssessmentCompletionBonus($userId, $assessmentId, $competency, $accuracy, $questionsAnswered)
    {
        $bonusPoints = 0;

        // Perfect score bonus (100% accuracy)
        if ($accuracy >= 1.0) {
            $bonusPoints += 25;
            $this->recordPointsTransaction(
                $userId,
                25,
                'perfect_assessment',
                null,
                $assessmentId,
                $competency,
                null,
                "Perfect assessment completion bonus"
            );
        }

        // Competency completion bonus (if applicable)
        if ($questionsAnswered >= 15) { // Minimum questions for completion bonus
            $bonusPoints += 10;
            $this->recordPointsTransaction(
                $userId,
                10,
                'competency_completion',
                null,
                $assessmentId,
                $competency,
                null,
                "Competency completion bonus"
            );
        }

        if ($bonusPoints > 0) {
            // Update user progress with bonus points
            DB::table('user_progress')
                ->where('user_id', $userId)
                ->increment('total_points', $bonusPoints);
        }

        return $bonusPoints;
    }
}