<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home page.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Sample data
        $dashboardData = [
            'user' => $user,
            'progress' => [
                'completed_lessons' => 2,
                'total_lessons' => 8,
                'percentage' => 25,
                'points' => 1250,
                'grade' => 'A',
                'rank' => 4,
            ],
            'current_lesson' => [
                'title' => 'Evaluate Exponents',
                'description' => 'Learn how to calculate and evaluate expressions with exponents',
                'grade' => 'Grade 6',
                'points' => 120,
            ],
            'leaderboard' => [
                [
                    'name' => 'Maria Santos',
                    'lessons' => 3,
                    'points' => 1580,
                    'rank' => 1,
                    'initial' => 'M',
                ],
                [
                    'name' => 'Carlos Reyes',
                    'lessons' => 2,
                    'points' => 1420,
                    'rank' => 2,
                    'initial' => 'C',
                ],
                [
                    'name' => 'Ana Garcia',
                    'lessons' => 2,
                    'points' => 1350,
                    'rank' => 3,
                    'initial' => 'A',
                ],
                [
                    'name' => $user ? $user->name : 'Juan Dela Cruz',
                    'lessons' => 2,
                    'points' => 1250,
                    'rank' => 4,
                    'initial' => $user ? substr($user->name, 0, 1) : 'J',
                    'is_current_user' => true,
                ],
                [
                    'name' => 'Miguel Torres',
                    'lessons' => 1,
                    'points' => 1190,
                    'rank' => 5,
                    'initial' => 'M',
                ],
            ],
            'achievements' => [
                [
                    'title' => 'First Steps',
                    'icon' => 'book',
                    'color' => 'blue',
                ],
                [
                    'title' => 'Math Whiz',
                    'icon' => 'check',
                    'color' => 'green',
                ],
                [
                    'title' => 'Grade Champion',
                    'icon' => 'star',
                    'color' => 'yellow',
                ],
                [
                    'title' => 'Point Collector',
                    'icon' => 'trophy',
                    'color' => 'orange',
                ],
            ],
        ];
        
        return view('student.dashboard', compact('dashboardData'));
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