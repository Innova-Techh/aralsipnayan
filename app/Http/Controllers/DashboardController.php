<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\AssessmentAssignment;
use App\Models\QuizResult;
use Illuminate\Support\Facades\DB;
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
        
        $user = Auth::guard('student')->user();
        
        // Get user profile with avatar
        $userProfile = $user ? $user->studentProfile : null;
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
                    'background_light' => $achievement['background_light'],
                    'rarity' => $achievement['rarity'], // Added rarity for drop shadow determination
                    'id' => $achievement['id'] // Added id for reference if needed
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

        // REMOVED: Level-Up System Data (for UI-only version)
        // This will be added back when we implement the full backend

        // Get student's section from student_profile
        $studentProfileData = DB::table('student_profile')->where('user_id', $user->id)->first();
        $studentSection = $studentProfileData ? $studentProfileData->section : null;

        // Get assessments assigned to this student
        $allAssignments = AssessmentAssignment::where(function($query) use ($user, $studentSection) {
            // First check for assignments specifically to this student
            $query->where('student_id', $user->id)
                  // Then check for section-wide assignments (where student_id is null)
                  ->orWhere(function($q) use ($studentSection) {
                      // Handle both "A" and "Section A" formats
                      $q->where(function($subQ) use ($studentSection) {
                          $subQ->where('section', $studentSection)
                               ->orWhere('section', 'Section ' . $studentSection);
                      })
                      ->whereNull('student_id'); // Only section-wide assignments
                  });
        })
        ->with(['assessment' => function($query) {
            $query->where('status', 'Active');
        }])
        ->whereHas('assessment', function($query) {
            $query->where('status', 'Active');
        })
        ->get();

        // Check for completed assessments and filter for pending only
        $completedAssessmentIds = QuizResult::where('student_id', $user->id)
            ->pluck('assessment_id')
            ->toArray();

        $pendingAssessments = $allAssignments->filter(function ($assignment) use ($completedAssessmentIds) {
            return !in_array($assignment->assessment_id, $completedAssessmentIds);
        });

        
        return view('student.dashboard', compact(
            'recentAchievements', 
            'leaderboardData', 
            'leaderboardTop5', 
            'userAvatarUrl',
            'userProfile',   // Add user profile for dashboard stats
            'pendingAssessments'
        ));
    }
    
    /**
     * Get user statistics for API calls
     */
    public function getStats()
    {
        $user = Auth::guard('student')->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated'
            ], 401);
        }
        
        // Basic stats without level-up system (for now)
        return response()->json([
            'completed_lessons' => 2,
            'total_points' => $user->studentProfile?->total_points ?? 0,
            'current_streak' => $user->studentProfile?->current_streak ?? 0,
            'current_rank' => 4,
            'grade_average' => 'A',
            // Mock level data for UI
            'current_level' => 3,
            'current_xp' => 460,
        ]);
    }
}