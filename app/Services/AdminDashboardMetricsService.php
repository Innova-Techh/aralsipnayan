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
}
