<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GamificationApiController extends Controller
{
    protected $gamificationController;

    public function __construct()
    {
        $this->gamificationController = new GamificationController();
    }

    /**
     * Get user's gamification dashboard data
     */
    public function getDashboard(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        try {
            $status = $this->gamificationController->getUserGamificationStatus($user->id);
            $badges = $this->gamificationController->getUserBadges($user->id);
            $pointsHistory = $this->gamificationController->getUserPointsHistory($user->id, 10);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $status,
                    'badges' => $badges,
                    'recent_points' => $pointsHistory
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load gamification dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get leaderboard data
     */
    public function getLeaderboard(Request $request)
    {
        $request->validate([
            'type' => 'in:weekly,monthly,all-time',
            'limit' => 'integer|min:5|max:100'
        ]);

        $type = $request->get('type', 'weekly');
        $limit = $request->get('limit', 10);
        
        try {
            $leaderboard = $this->gamificationController->getLeaderboard($type, $limit);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'leaderboard' => $leaderboard
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load leaderboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's points history
     */
    public function getPointsHistory(Request $request)
    {
        $request->validate([
            'limit' => 'integer|min:5|max:100',
            'competency' => 'in:number_algebra,measurement_geometry,data_probability'
        ]);

        $user = Auth::guard('student')->user();
        $limit = $request->get('limit', 50);
        $competency = $request->get('competency');
        
        try {
            $query = DB::table('points_transactions')
                ->where('user_id', $user->id)
                ->orderBy('earned_at', 'desc')
                ->limit($limit);
                
            if ($competency) {
                $query->where('competency', $competency);
            }
            
            $history = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'history' => $history,
                    'total_count' => $history->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load points history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's badges
     */
    public function getBadges(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        try {
            $badges = $this->gamificationController->getUserBadges($user->id);
            $status = $this->gamificationController->getUserGamificationStatus($user->id);
            
            // Get available badges that user hasn't earned yet
            $availableBadges = collect([
                'first_steps' => ['points' => 0, 'name' => 'First Steps', 'icon' => '🎯', 'description' => 'Complete your first assessment'],
                'quick_learner' => ['points' => 50, 'name' => 'Quick Learner', 'icon' => '⚡', 'description' => 'Earn 50 total points'],
                'on_fire' => ['points' => 150, 'name' => 'On Fire', 'icon' => '🔥', 'description' => 'Earn 150 total points'],
                'math_explorer' => ['points' => 300, 'name' => 'Math Explorer', 'icon' => '🚀', 'description' => 'Earn 300 total points'],
                'math_whiz' => ['points' => 500, 'name' => 'Math Whiz', 'icon' => '🌟', 'description' => 'Earn 500 total points'],
                'grade_champion' => ['points' => 750, 'name' => 'Grade Champion', 'icon' => '💎', 'description' => 'Earn 750 total points'],
                'sapphire' => ['points' => 1000, 'name' => 'Sapphire', 'icon' => '♦️', 'description' => 'Earn 1,000 total points'],
                'ruby' => ['points' => 1250, 'name' => 'Ruby', 'icon' => '🔶', 'description' => 'Earn 1,250 total points'],
                'crown' => ['points' => 1500, 'name' => 'Crown', 'icon' => '👑', 'description' => 'Earn 1,500 total points']
            ]);
            
            $earnedBadgeKeys = $badges->pluck('badge_key')->toArray();
            $upcomingBadges = $availableBadges->filter(function($badge, $key) use ($earnedBadgeKeys, $status) {
                return !in_array($key, $earnedBadgeKeys) && $status['total_points'] < $badge['points'];
            })->take(3);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'earned_badges' => $badges,
                    'upcoming_badges' => $upcomingBadges->values(),
                    'total_points' => $status['total_points']
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load badges',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get class/school leaderboard position for user
     */
    public function getMyRanking(Request $request)
    {
        $request->validate([
            'type' => 'in:weekly,monthly,all-time'
        ]);

        $user = Auth::guard('student')->user();
        $type = $request->get('type', 'weekly');
        
        try {
            // Get user's current position in leaderboard
            $query = DB::table('user_progress')
                ->join('users', 'user_progress.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'user_progress.total_points'
                );

            if ($type === 'weekly') {
                $weekStart = \Carbon\Carbon::now()->startOfWeek();
                $query->addSelect(DB::raw('COALESCE(SUM(pt.points_earned), 0) as period_points'))
                    ->leftJoin('points_transactions as pt', function($join) use ($weekStart) {
                        $join->on('pt.user_id', '=', 'users.id')
                             ->where('pt.earned_at', '>=', $weekStart);
                    })
                    ->groupBy('users.id', 'user_progress.total_points')
                    ->orderBy('period_points', 'desc');
            } elseif ($type === 'monthly') {
                $monthStart = \Carbon\Carbon::now()->startOfMonth();
                $query->addSelect(DB::raw('COALESCE(SUM(pt.points_earned), 0) as period_points'))
                    ->leftJoin('points_transactions as pt', function($join) use ($monthStart) {
                        $join->on('pt.user_id', '=', 'users.id')
                             ->where('pt.earned_at', '>=', $monthStart);
                    })
                    ->groupBy('users.id', 'user_progress.total_points')
                    ->orderBy('period_points', 'desc');
            } else {
                $query->addSelect('user_progress.total_points as period_points')
                    ->orderBy('user_progress.total_points', 'desc');
            }

            $rankings = $query->get();
            $userRank = $rankings->search(function ($item) use ($user) {
                return $item->id == $user->id;
            });

            $myPosition = $userRank !== false ? $userRank + 1 : null;
            $myPoints = $rankings->where('id', $user->id)->first()->period_points ?? 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'position' => $myPosition,
                    'points' => $myPoints,
                    'total_participants' => $rankings->count(),
                    'type' => $type
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get ranking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get gamification statistics
     */
    public function getStats(Request $request)
    {
        $user = Auth::guard('student')->user();
        
        try {
            $status = $this->gamificationController->getUserGamificationStatus($user->id);
            
            // Get points breakdown by type
            $pointsBreakdown = DB::table('points_transactions')
                ->where('user_id', $user->id)
                ->select('points_type', DB::raw('SUM(points_earned) as total_points'), DB::raw('COUNT(*) as count'))
                ->groupBy('points_type')
                ->get();
                
            // Get competency breakdown
            $competencyBreakdown = DB::table('points_transactions')
                ->where('user_id', $user->id)
                ->whereNotNull('competency')
                ->select('competency', DB::raw('SUM(points_earned) as total_points'), DB::raw('COUNT(*) as count'))
                ->groupBy('competency')
                ->get();
                
            // Get streak stats
            $streakStats = DB::table('user_progress')
                ->where('user_id', $user->id)
                ->select('current_streak', 'longest_streak', 'last_assessment_date')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $status,
                    'points_breakdown' => $pointsBreakdown,
                    'competency_breakdown' => $competencyBreakdown,
                    'streak_stats' => $streakStats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}