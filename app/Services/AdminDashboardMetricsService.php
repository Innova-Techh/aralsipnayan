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

    public function getMostMissedTopics(int $limit = 5): array
    {
        $rows = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->select(
                'questions.topic_tag as topic',
                DB::raw('SUM(CASE WHEN question_responses.is_correct = 1 THEN 1 ELSE 0 END) as correct'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('questions.topic_tag')
            ->havingRaw('COUNT(*) > 0')
            ->get()
            ->map(function ($row) {
                $accuracy = $row->total > 0 ? round(($row->correct / $row->total) * 100, 1) : 0;
                return [
                    'topic' => $row->topic ?: 'Unknown',
                    'accuracy' => $accuracy,
                ];
            })
            ->sortBy('accuracy')
            ->take($limit)
            ->values();

        return [
            'topics' => $rows->pluck('topic')->all(),
            'accuracy' => $rows->pluck('accuracy')->all(),
        ];
    }

    public function getPerformanceByCompetency(): array
    {
        $rows = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->join('student_profile', 'question_responses.user_id', '=', 'student_profile.user_id')
            ->whereNotNull('student_profile.section')
            ->select(
                'student_profile.section',
                'questions.competency',
                DB::raw('SUM(CASE WHEN question_responses.is_correct = 1 THEN 1 ELSE 0 END) as correct'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('student_profile.section', 'questions.competency')
            ->orderBy('student_profile.section')
            ->get();

        $sections = $rows->pluck('section')->unique()->values()->all();
        $competencies = ['number_algebra', 'measurement_geometry', 'data_probability'];

        $series = [];
        foreach ($competencies as $competency) {
            $data = [];
            foreach ($sections as $section) {
                $row = $rows->first(function ($r) use ($section, $competency) {
                    return $r->section === $section && $r->competency === $competency;
                });
                $accuracy = ($row && $row->total > 0)
                    ? round(($row->correct / $row->total) * 100, 1)
                    : 0;
                $data[] = $accuracy;
            }

            $name = match ($competency) {
                'number_algebra' => 'Number and Algebra',
                'measurement_geometry' => 'Measurement and Geometry',
                'data_probability' => 'Data and Probability',
                default => ucwords(str_replace('_', ' ', $competency)),
            };

            $series[] = [
                'name' => $name,
                'data' => $data,
            ];
        }

        return [
            'sections' => $sections,
            'series' => $series,
        ];
    }
}
