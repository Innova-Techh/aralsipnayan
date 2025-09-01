<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    /**
     * Display the leaderboard page.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Sample data for Grade 6 Leaderboard
        $leaderboardData = [
            'grade' => 'Grade 6',
            'title' => 'Grade 6 Leaderboard',
            'subtitle' => 'See how you rank among your classmates!',
            'top_students' => [
                [
                    'rank' => 1,
                    'name' => 'Maria Santos',
                    'points' => 1580,
                    'lessons' => 3,
                    'icon' => 'crown',
                    'bg_color' => 'bg-gradient-to-br from-yellow-400 to-orange-500',
                    'text_color' => 'text-white',
                ],
                [
                    'rank' => 2,
                    'name' => 'Carlos Reyes',
                    'points' => 1420,
                    'lessons' => 2,
                    'icon' => 'medal',
                    'bg_color' => 'bg-gradient-to-br from-blue-400 to-gray-500',
                    'text_color' => 'text-white',
                ],
                [
                    'rank' => 3,
                    'name' => 'Ana Garcia',
                    'points' => 1350,
                    'lessons' => 2,
                    'icon' => 'trophy',
                    'bg_color' => 'bg-gradient-to-br from-orange-400 to-brown-500',
                    'text_color' => 'text-white',
                ],
            ],
            'ranking_list' => [
                [
                    'rank' => 4,
                    'name' => $user ? $user->name : 'Juan Dela Cruz',
                    'points' => 1250,
                    'lessons' => 2,
                    'grade' => 'Grade 6',
                    'is_current_user' => true,
                    'highlight' => true,
                ],
                [
                    'rank' => 5,
                    'name' => 'Miguel Torres',
                    'points' => 1180,
                    'lessons' => 1,
                    'grade' => 'Grade 6',
                    'is_current_user' => false,
                    'highlight' => false,
                ],
                [
                    'rank' => 6,
                    'name' => 'Sofia Mendoza',
                    'points' => 1090,
                    'lessons' => 1,
                    'grade' => 'Grade 6',
                    'is_current_user' => false,
                    'highlight' => false,
                ],
                [
                    'rank' => 7,
                    'name' => 'Diego Fernandez',
                    'points' => 980,
                    'lessons' => 1,
                    'grade' => 'Grade 6',
                    'is_current_user' => false,
                    'highlight' => false,
                ],
                [
                    'rank' => 8,
                    'name' => 'Isabella Cruz',
                    'points' => 890,
                    'lessons' => 1,
                    'grade' => 'Grade 6',
                    'is_current_user' => false,
                    'highlight' => false,
                ],
                [
                    'rank' => 9,
                    'name' => 'Lucas Rodriguez',
                    'points' => 820,
                    'lessons' => 1,
                    'grade' => 'Grade 6',
                    'is_current_user' => false,
                    'highlight' => false,
                ],
                [
                    'rank' => 10,
                    'name' => 'Emma Santos',
                    'points' => 750,
                    'lessons' => 1,
                    'grade' => 'Grade 6',
                    'is_current_user' => false,
                    'highlight' => false,
                ],
            ],
            'current_user_rank' => 4,
            'total_students' => 25,
        ];
        
        return view('student.leaderboard', compact('leaderboardData'));
    }
    
    /**
     * Get leaderboard data for API calls
     */
    public function getLeaderboardData()
    {
        $user = Auth::user();
        
        // Sample API response
        return response()->json([
            'grade' => 'Grade 6',
            'current_user_rank' => 4,
            'total_students' => 25,
            'top_students' => [
                ['name' => 'Maria Santos', 'points' => 1580, 'rank' => 1],
                ['name' => 'Carlos Reyes', 'points' => 1420, 'rank' => 2],
                ['name' => 'Ana Garcia', 'points' => 1350, 'rank' => 3],
            ],
            'user_stats' => [
                'rank' => 4,
                'points' => 1250,
                'lessons_completed' => 2,
            ],
        ]);
    }
} 