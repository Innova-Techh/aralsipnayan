<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AdminDashboardMetricsService
{
    public function getAverageScoresBySection(): array
    {
        $rows = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('student_profile.section')
            ->select(
                'student_profile.section',
                DB::raw('AVG(COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage)) as avg_score')
            )
            ->groupBy('student_profile.section')
            ->orderBy('student_profile.section')
            ->get();

        $sections = [];
        $scores = [];

        foreach ($rows as $row) {
            $sections[] = $row->section;
            $scores[] = round((float) $row->avg_score, 1);
        }

        $overall = count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0.0;

        return [
            'sections' => $sections,
            'scores' => $scores,
            'overall' => $overall,
        ];
    }

    public function getAssessmentCompletionRate(): array
    {
        $totalStudents = (int) DB::table('student_profile')->count();

        $completedUsers = DB::table('assessment_sessions')
            ->where('status', 'completed')
            ->distinct('user_id')
            ->count('user_id');

        $inProgressUsers = DB::table('assessment_sessions')
            ->where('status', 'in_progress')
            ->distinct('user_id')
            ->count('user_id');

        $startedUsers = DB::table('assessment_sessions')
            ->distinct('user_id')
            ->count('user_id');

        $notStartedUsers = max(0, $totalStudents - $startedUsers);

        $completionRate = $totalStudents > 0
            ? round(($completedUsers / $totalStudents) * 100, 1)
            : 0.0;

        return [
            'completed' => $completedUsers,
            'in_progress' => $inProgressUsers,
            'not_started' => $notStartedUsers,
            'completion_rate' => $completionRate,
            'total_students' => $totalStudents,
        ];
    }
}
