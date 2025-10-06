<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaderboardBadgeController extends Controller
{
    // Leaderboard Badge Definitions
    private const LEADERBOARD_BADGES = [
        // Weekly Section Badges (Ranks 1-10)
        'section_weekly_1' => [
            'name' => 'Section Champion',
            'description' => 'Rank #1 in your section this week',
            'icon' => '👑',
            'rarity' => 'Legendary',
            'background_light' => '#D17A09',
            'rank' => 1,
            'period' => 'weekly',
            'scope' => 'section'
        ],
        'section_weekly_2' => [
            'name' => 'Section Silver Star',
            'description' => 'Rank #2 in your section this week',
            'icon' => '🥈',
            'rarity' => 'Epic',
            'background_light' => '#2C1B68',
            'rank' => 2,
            'period' => 'weekly',
            'scope' => 'section'
        ],
        'section_weekly_3' => [
            'name' => 'Section Bronze Medal',
            'description' => 'Rank #3 in your section this week',
            'icon' => '🥉',
            'rarity' => 'Epic',
            'background_light' => '#913311',
            'rank' => 3,
            'period' => 'weekly',
            'scope' => 'section'
        ],
        'section_weekly_top5' => [
            'name' => 'Section Top 5',
            'description' => 'Rank #4-5 in your section this week',
            'icon' => '⭐',
            'rarity' => 'Rare',
            'background_light' => '#913311',
            'rank_min' => 4,
            'rank_max' => 5,
            'period' => 'weekly',
            'scope' => 'section'
        ],
        'section_weekly_top10' => [
            'name' => 'Section Top 10',
            'description' => 'Rank #6-10 in your section this week',
            'icon' => '🌟',
            'rarity' => 'Uncommon',
            'background_light' => '#1E8646',
            'rank_min' => 6,
            'rank_max' => 10,
            'period' => 'weekly',
            'scope' => 'section'
        ],

        // Monthly School Badges (Ranks 1-10)
        'school_monthly_1' => [
            'name' => 'School Grandmaster',
            'description' => 'Rank #1 in your school this month',
            'icon' => '🏆',
            'rarity' => 'Legendary',
            'background_light' => '#D17A09',
            'rank' => 1,
            'period' => 'monthly',
            'scope' => 'school'
        ],
        'school_monthly_2' => [
            'name' => 'School Elite',
            'description' => 'Rank #2 in your school this month',
            'icon' => '🥈',
            'rarity' => 'Epic',
            'background_light' => '#2C1B68',
            'rank' => 2,
            'period' => 'monthly',
            'scope' => 'school'
        ],
        'school_monthly_3' => [
            'name' => 'School Achiever',
            'description' => 'Rank #3 in your school this month',
            'icon' => '🥉',
            'rarity' => 'Epic',
            'background_light' => '#913311',
            'rank' => 3,
            'period' => 'monthly',
            'scope' => 'school'
        ],
        'school_monthly_top5' => [
            'name' => 'School Top 5',
            'description' => 'Rank #4-5 in your school this month',
            'icon' => '⭐',
            'rarity' => 'Rare',
            'background_light' => '#913311',
            'rank_min' => 4,
            'rank_max' => 5,
            'period' => 'monthly',
            'scope' => 'school'
        ],
        'school_monthly_top10' => [
            'name' => 'School Top 10',
            'description' => 'Rank #6-10 in your school this month',
            'icon' => '🌟',
            'rarity' => 'Uncommon',
            'background_light' => '#1E8646',
            'rank_min' => 6,
            'rank_max' => 10,
            'period' => 'monthly',
            'scope' => 'school'
        ]
    ];

    /**
     * Award weekly section leaderboard badges (Top 10)
     * Called by scheduler every Sunday at 11:59 PM
     */
    public function awardWeeklySectionBadges()
    {
        try {
            DB::beginTransaction();

            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            Log::info('Starting weekly section badge awards', [
                'week_start' => $weekStart,
                'week_end' => $weekEnd
            ]);

            // Get all sections
            $sections = DB::table('student_profile')
                ->select('section', 'grade_level', 'school_name')
                ->distinct()
                ->get();

            $badgesAwarded = 0;

            foreach ($sections as $section) {
                // Get top 10 students in this section for the week
                $topStudents = $this->getWeeklyTopStudents(
                    $section->section,
                    $section->grade_level,
                    $section->school_name,
                    $weekStart,
                    $weekEnd
                );

                // Award badges to top 10
                foreach ($topStudents as $index => $student) {
                    $rank = $index + 1;

                    if ($rank > 10) break;

                    $badgeKey = $this->getSectionWeeklyBadgeKey($rank);

                    if ($badgeKey) {
                        $this->awardBadge($student->user_id, $badgeKey, [
                            'rank' => $rank,
                            'section' => $section->section,
                            'week_start' => $weekStart,
                            'week_end' => $weekEnd,
                            'points' => $student->weekly_points
                        ]);
                        $badgesAwarded++;
                    }
                }
            }

            DB::commit();

            Log::info('Weekly section badges awarded successfully', [
                'total_badges' => $badgesAwarded
            ]);

            return [
                'success' => true,
                'badges_awarded' => $badgesAwarded
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award weekly section badges: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Award monthly school leaderboard badges (Top 10)
     * Called by scheduler on last day of month at 11:59 PM
     */
    public function awardMonthlySchoolBadges()
    {
        try {
            DB::beginTransaction();

            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();

            Log::info('Starting monthly school badge awards', [
                'month_start' => $monthStart,
                'month_end' => $monthEnd
            ]);

            // Get all schools
            $schools = DB::table('student_profile')
                ->select('school_name', 'grade_level')
                ->distinct()
                ->get();

            $badgesAwarded = 0;

            foreach ($schools as $school) {
                // Get top 10 students in this school for the month
                $topStudents = $this->getMonthlyTopStudents(
                    $school->school_name,
                    $school->grade_level,
                    $monthStart,
                    $monthEnd
                );

                // Award badges to top 10
                foreach ($topStudents as $index => $student) {
                    $rank = $index + 1;

                    if ($rank > 10) break;

                    $badgeKey = $this->getSchoolMonthlyBadgeKey($rank);

                    if ($badgeKey) {
                        $this->awardBadge($student->user_id, $badgeKey, [
                            'rank' => $rank,
                            'school' => $school->school_name,
                            'month_start' => $monthStart,
                            'month_end' => $monthEnd,
                            'points' => $student->monthly_points
                        ]);
                        $badgesAwarded++;
                    }
                }
            }

            DB::commit();

            Log::info('Monthly school badges awarded successfully', [
                'total_badges' => $badgesAwarded
            ]);

            return [
                'success' => true,
                'badges_awarded' => $badgesAwarded
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award monthly school badges: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get top students in a section for the week
     */
    private function getWeeklyTopStudents($section, $gradeLevel, $schoolName, $weekStart, $weekEnd)
    {
        return DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('points_transactions', function($join) use ($weekStart, $weekEnd) {
                $join->on('points_transactions.user_id', '=', 'student_profile.user_id')
                    ->whereBetween('points_transactions.earned_at', [$weekStart, $weekEnd]);
            })
            ->where('student_profile.section', $section)
            ->where('student_profile.grade_level', $gradeLevel)
            ->where('student_profile.school_name', $schoolName)
            ->where('users.status', 'active')
            ->select(
                'student_profile.user_id',
                'student_profile.firstname',
                'student_profile.lastname',
                DB::raw('COALESCE(SUM(points_transactions.points_earned), 0) as weekly_points')
            )
            ->groupBy('student_profile.user_id', 'student_profile.firstname', 'student_profile.lastname')
            ->orderBy('weekly_points', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top students in a school for the month
     */
    private function getMonthlyTopStudents($schoolName, $gradeLevel, $monthStart, $monthEnd)
    {
        return DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('points_transactions', function($join) use ($monthStart, $monthEnd) {
                $join->on('points_transactions.user_id', '=', 'student_profile.user_id')
                    ->whereBetween('points_transactions.earned_at', [$monthStart, $monthEnd]);
            })
            ->where('student_profile.school_name', $schoolName)
            ->where('student_profile.grade_level', $gradeLevel)
            ->where('users.status', 'active')
            ->select(
                'student_profile.user_id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.section',
                DB::raw('COALESCE(SUM(points_transactions.points_earned), 0) as monthly_points')
            )
            ->groupBy('student_profile.user_id', 'student_profile.firstname', 'student_profile.lastname', 'student_profile.section')
            ->orderBy('monthly_points', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get badge key for section weekly rank
     */
    private function getSectionWeeklyBadgeKey($rank)
    {
        if ($rank == 1) return 'section_weekly_1';
        if ($rank == 2) return 'section_weekly_2';
        if ($rank == 3) return 'section_weekly_3';
        if ($rank >= 4 && $rank <= 5) return 'section_weekly_top5';
        if ($rank >= 6 && $rank <= 10) return 'section_weekly_top10';
        return null;
    }

    /**
     * Get badge key for school monthly rank
     */
    private function getSchoolMonthlyBadgeKey($rank)
    {
        if ($rank == 1) return 'school_monthly_1';
        if ($rank == 2) return 'school_monthly_2';
        if ($rank == 3) return 'school_monthly_3';
        if ($rank >= 4 && $rank <= 5) return 'school_monthly_top5';
        if ($rank >= 6 && $rank <= 10) return 'school_monthly_top10';
        return null;
    }

    /**
     * Award a leaderboard badge to a user
     */
    private function awardBadge($userId, $badgeKey, $metadata = [])
    {
        $badge = self::LEADERBOARD_BADGES[$badgeKey] ?? null;

        if (!$badge) {
            Log::warning("Badge key not found: {$badgeKey}");
            return false;
        }

        // Check if user already has this EXACT badge with same metadata
        // (Allow multiple badges of same type for different weeks/months)
        $existingBadge = DB::table('user_badges')
            ->where('user_id', $userId)
            ->where('badge_key', $badgeKey)
            ->where('badge_metadata', json_encode($metadata))
            ->exists();

        if ($existingBadge) {
            Log::info("User already has this badge", [
                'user_id' => $userId,
                'badge_key' => $badgeKey
            ]);
            return false;
        }

        // Award the badge
        DB::table('user_badges')->insert([
            'user_id' => $userId,
            'badge_key' => $badgeKey,
            'badge_name' => $badge['name'],
            'badge_icon' => $badge['icon'],
            'badge_description' => $badge['description'],
            'points_required' => 0, // Leaderboard badges don't require points
            'badge_metadata' => json_encode($metadata), // Store rank, period info
            'awarded_at' => now(),
            'is_viewed' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::info("Badge awarded", [
            'user_id' => $userId,
            'badge_key' => $badgeKey,
            'metadata' => $metadata
        ]);

        return true;
    }

    /**
     * Get all leaderboard badge definitions (for achievements page)
     */
    public static function getLeaderboardBadgeDefinitions()
    {
        return self::LEADERBOARD_BADGES;
    }

    /**
     * Manual trigger for testing weekly badges
     */
    public function testWeeklyAward(Request $request)
    {
        if (app()->environment('local')) {
            return $this->awardWeeklySectionBadges();
        }

        return response()->json(['error' => 'Only available in local environment'], 403);
    }

    /**
     * Manual trigger for testing monthly badges
     */
    public function testMonthlyAward(Request $request)
    {
        if (app()->environment('local')) {
            return $this->awardMonthlySchoolBadges();
        }

        return response()->json(['error' => 'Only available in local environment'], 403);
    }
}
