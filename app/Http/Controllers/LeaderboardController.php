<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the leaderboard page.
     */
    public function index(): View
    {
        return view('student.leaderboard');
    }

    /**
     * Get section leaderboard data
     */
    public function getSectionLeaderboard(Request $request)
    {
        $user = Auth::guard('student')->user();
        $profile = $user->studentProfile;

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $section = $profile->section;
        $gradeLevel = $profile->grade_level;
        $schoolName = $profile->school_name;

        // Get all students in the same section ordered by total_points from user_progress
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->where('student_profile.section', $section)
            ->where('student_profile.grade_level', $gradeLevel)
            ->where('student_profile.school_name', $schoolName)
            ->where('users.status', 'active')
            ->select(
                'student_profile.id',
                'student_profile.user_id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.avatar_url',
                'student_profile.section',
                'student_profile.grade_level',
                DB::raw('COALESCE(user_progress.total_points, 0) as total_points')
            )
            ->orderBy('total_points', 'desc')
            ->get();

        // Add rank to each student
        $rankedStudents = $students->map(function ($student, $index) use ($user) {
            return [
                'rank' => $index + 1,
                'user_id' => $student->user_id,
                'name' => trim($student->firstname . ' ' . $student->lastname),
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'points' => $student->total_points,
                'section' => $student->section,
                'grade_level' => $student->grade_level,
                'avatar_url' => $student->avatar_url ? asset($student->avatar_url) : asset('images/profile/default.png'),
                'is_current_user' => $student->user_id == $user->id,
            ];
        });

        // Get top 3 students
        $topThree = $rankedStudents->take(3)->values();

        // Get students ranked 4-10
        $rankedList = $rankedStudents->slice(3, 7)->values();

        // Find current user's rank
        $currentUserRank = $rankedStudents->firstWhere('is_current_user', true);

        return response()->json([
            'type' => 'section',
            'section_name' => "Grade {$gradeLevel} - {$section}",
            'top_three' => $topThree,
            'ranked_list' => $rankedList,
            'current_user' => $currentUserRank,
            'total_students' => $students->count(),
        ]);
    }

    /**
     * Get school-wide leaderboard data
     */
    public function getSchoolLeaderboard(Request $request)
    {
        $user = Auth::guard('student')->user();
        $profile = $user->studentProfile;

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $gradeLevel = $profile->grade_level;
        $schoolName = $profile->school_name;

        // Get all students in the same school and grade level ordered by total_points from user_progress
        $students = DB::table('student_profile')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->leftJoin('user_progress', 'student_profile.user_id', '=', 'user_progress.user_id')
            ->where('student_profile.grade_level', $gradeLevel)
            ->where('student_profile.school_name', $schoolName)
            ->where('users.status', 'active')
            ->select(
                'student_profile.id',
                'student_profile.user_id',
                'student_profile.firstname',
                'student_profile.lastname',
                'student_profile.avatar_url',
                'student_profile.section',
                'student_profile.grade_level',
                DB::raw('COALESCE(user_progress.total_points, 0) as total_points')
            )
            ->orderBy('total_points', 'desc')
            ->get();

        // Add rank to each student
        $rankedStudents = $students->map(function ($student, $index) use ($user) {
            return [
                'rank' => $index + 1,
                'user_id' => $student->user_id,
                'name' => trim($student->firstname . ' ' . $student->lastname),
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'points' => $student->total_points,
                'section' => $student->section,
                'grade_level' => $student->grade_level,
                'avatar_url' => $student->avatar_url ? asset($student->avatar_url) : asset('images/profile/default.png'),
                'is_current_user' => $student->user_id == $user->id,
            ];
        });

        // Get top 3 students
        $topThree = $rankedStudents->take(3)->values();

        // Get students ranked 4-10
        $rankedList = $rankedStudents->slice(3, 7)->values();

        // Find current user's rank
        $currentUserRank = $rankedStudents->firstWhere('is_current_user', true);

        return response()->json([
            'type' => 'school',
            'school_name' => $schoolName,
            'top_three' => $topThree,
            'ranked_list' => $rankedList,
            'current_user' => $currentUserRank,
            'total_students' => $students->count(),
        ]);
    }
} 