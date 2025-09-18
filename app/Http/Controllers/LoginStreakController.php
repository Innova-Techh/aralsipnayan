<?php
// app/Http/Controllers/LoginStreakController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginStreakController extends Controller
{
    public function checkStreak()
    {
        $user = Auth::user();
        
        // Only process streaks for students
        if ($user->role !== 'Student') {
            return response()->json(['show_modal' => false]);
        }

        $profile = $user->studentProfile;

        if (!$profile) {
            return response()->json(['show_modal' => false, 'error' => 'Student profile not found']);
        }

        if ($profile->shouldShowModal()) {
            $result = $profile->updateStreak();
            
            if (!$result['already_logged']) {
                return response()->json([
                    'show_modal' => true,
                    'data' => [
                        'current_streak' => $result['current_streak'],
                        'points_earned' => $result['points_earned'],
                        'total_points' => $result['total_points'],
                        'message' => $result['current_streak'] == 1 ? 
                            "Welcome back! You've started a new streak!" : 
                            "Amazing! You're on a {$result['current_streak']}-day streak!"
                    ]
                ]);
            }
        }

        return response()->json(['show_modal' => false]);
    }

    public function getStreakData()
    {
        $user = Auth::user();
        
        if ($user->role !== 'Student') {
            return response()->json([
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_points' => 0,
                'points_today' => 0
            ]);
        }

        $profile = $user->studentProfile;

        if (!$profile) {
            return response()->json([
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_points' => 0,
                'points_today' => 10
            ]);
        }

        return response()->json([
            'current_streak' => $profile->current_streak,
            'longest_streak' => $profile->longest_streak,
            'total_points' => $profile->total_points,
            'points_today' => $profile->getPointsForDay($profile->current_streak + 1),
            'last_activity' => $profile->last_activity_date
        ]);
    }

    public function showStreakPage()
    {
        $user = Auth::user();
        
        // Redirect non-students to dashboard
        if ($user->role !== 'Student') {
            return redirect()->route('dashboard')->with('error', 'This page is only available for students.');
        }

        $profile = $user->studentProfile;
        
        if (!$profile) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        // Calculate streak statistics
        $streakStats = [
            'current_streak' => $profile->current_streak,
            'longest_streak' => $profile->longest_streak,
            'total_points' => $profile->total_points,
            'last_activity' => $profile->last_activity_date,
            'next_points' => $profile->getPointsForDay($profile->current_streak + 1),
        ];

        // Generate streak calendar data (last 30 days)
        $calendarData = $this->generateStreakCalendar($profile);

        return view('student.login-streak', compact('streakStats', 'calendarData', 'profile'));
    }

    private function generateStreakCalendar($profile)
    {
        $calendar = [];
        $today = now();
        
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $isActive = false;
            
            // Check if user was active on this day
            if ($profile->last_activity_date && $profile->current_streak > 0) {
                $streakStart = $profile->last_activity_date->copy()->subDays($profile->current_streak - 1);
                $isActive = $date->greaterThanOrEqualTo($streakStart) && $date->lessThanOrEqualTo($profile->last_activity_date);
            }
            
            $calendar[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('j'),
                'is_today' => $date->isToday(),
                'is_active' => $isActive,
                'is_future' => $date->isFuture(),
            ];
        }
        
        return $calendar;
    }
}