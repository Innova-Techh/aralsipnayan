<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\LeaderboardController;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home page.
     */
    public function index(): View
    {
        // Optional: Add delay to simulate data fetching (remove in production)
        sleep(1);
        
        $user = Auth::user();
        
        // Get user profile with avatar
        $userProfile = $user->studentProfile;
        $userAvatarUrl = $userProfile && $userProfile->avatar_url 
            ? asset($userProfile->avatar_url)
            : asset('images/profile/avatar5.png'); // Default avatar
        
        // Get recent achievements data
        $achievementController = new AchievementController();
        $allAchievements = collect($achievementController->getAllAchievements());
        
        // Filter to get only owned/earned achievements and take the first 6
        $recentAchievements = $allAchievements
            ->where('is_earned', true)
            ->take(6)
            ->map(function ($achievement) {
                return [
                    'title' => $achievement['title'],
                    'front_image' => $achievement['front_image'],
                    'background_dark' => $achievement['background_dark'],
                    'background_light' => $achievement['background_light']
                ];
            })
            ->values()
            ->toArray();
        
        // Get leaderboard data
        $leaderboardController = new LeaderboardController();
        $leaderboardData = $leaderboardController->getLeaderboardDataForView();
        
        // Build a Top 5 list (top_students 1-3 + next 2 from ranking_list)
        $leaderboardTop5 = array_merge(
            $leaderboardData['top_students'] ?? [],
            array_slice($leaderboardData['ranking_list'] ?? [], 0, 2)
        );

        return view('student.dashboard', compact('recentAchievements', 'leaderboardData', 'leaderboardTop5', 'userAvatarUrl'));
    }
    
    /**
     * Get user statistics for API calls
     */
    public function getStats()
    {
        $user = Auth::user();
        
        // Replace with actual database queries
        return response()->json([
            'completed_lessons' => 2,
            'total_points' => 1250,
            'current_rank' => 4,
            'grade_average' => 'A',
        ]);
    }
}