<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AdminDashboardMetricsService
{
    private function applyDateRange($query, string $column, $from = null, $to = null)
    {
        if ($from && $to) {
            return $query->whereBetween($column, [$from, $to]);
        }
        if ($from) {
            return $query->where($column, '>=', $from);
        }
        if ($to) {
            return $query->where($column, '<=', $to);
        }
        return $query;
    }

    public function getAverageScoresBySection($from = null, $to = null): array
    {
        $rows = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('student_profile.section')
            ->when($from || $to, function ($q) use ($from, $to) {
                $this->applyDateRange($q, 'assessment_sessions.completed_at', $from, $to);
            })
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

    public function getAssessmentCompletionRate($from = null, $to = null): array
    {
        $totalStudentsQuery = DB::table('student_profile');
        $this->applyDateRange($totalStudentsQuery, 'created_at', $from, $to);
        $totalStudents = (int) $totalStudentsQuery->count();

        $completedUsersQuery = DB::table('assessment_sessions')
            ->where('status', 'completed')
            ->distinct('user_id');
        $this->applyDateRange($completedUsersQuery, 'completed_at', $from, $to);
        $completedUsers = $completedUsersQuery->count('user_id');

        $inProgressUsersQuery = DB::table('assessment_sessions')
            ->where('status', 'in_progress')
            ->distinct('user_id');
        $this->applyDateRange($inProgressUsersQuery, 'started_at', $from, $to);
        $inProgressUsers = $inProgressUsersQuery->count('user_id');

        $startedUsersQuery = DB::table('assessment_sessions')
            ->distinct('user_id');
        $this->applyDateRange($startedUsersQuery, 'started_at', $from, $to);
        $startedUsers = $startedUsersQuery->count('user_id');

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

    public function getMostMissedTopics(int $limit = 5, $from = null, $to = null): array
    {
        $rows = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->when($from || $to, function ($q) use ($from, $to) {
                $this->applyDateRange($q, 'question_responses.answered_at', $from, $to);
            })
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

    public function getPerformanceByCompetency($from = null, $to = null): array
    {
        $rows = DB::table('question_responses')
            ->join('questions', 'question_responses.question_id', '=', 'questions.question_id')
            ->join('student_profile', 'question_responses.user_id', '=', 'student_profile.user_id')
            ->whereNotNull('student_profile.section')
            ->when($from || $to, function ($q) use ($from, $to) {
                $this->applyDateRange($q, 'question_responses.answered_at', $from, $to);
            })
            ->select(
                'student_profile.section',
                'questions.competency',
                DB::raw('SUM(CASE WHEN question_responses.is_correct = 1 THEN 1 ELSE 0 END) as correct'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('student_profile.section', 'questions.competency')
            ->orderBy('student_profile.section')
            ->get();

        $sections = DB::table('student_profile')
            ->whereNotNull('section')
            ->distinct()
            ->orderBy('section')
            ->pluck('section')
            ->all();

        if (empty($sections)) {
            $sections = $rows->pluck('section')->unique()->values()->all();
        }
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

    public function getSectionPerformanceTrend(int $weeks = 6, $from = null, $to = null): array
    {
        $end = $to ? $to->copy()->startOfWeek() : now()->startOfWeek();
        $start = $from ? $from->copy()->startOfWeek() : (clone $end)->subWeeks($weeks - 1);
        $diffWeeks = max(1, $start->diffInWeeks($end) + 1);
        $weeks = min($weeks, $diffWeeks);

        $rows = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('assessment_sessions.completed_at')
            ->whereNotNull('student_profile.section')
            ->whereBetween('assessment_sessions.completed_at', [$start, (clone $end)->endOfWeek()])
            ->select(
                'student_profile.section',
                DB::raw("YEARWEEK(assessment_sessions.completed_at, 1) as yearweek"),
                DB::raw('AVG(COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage)) as avg_score')
            )
            ->groupBy('student_profile.section', DB::raw("YEARWEEK(assessment_sessions.completed_at, 1)"))
            ->get();

        $weeksList = [];
        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = (clone $start)->addWeeks($i);
            $weeksList[] = [
                'label' => 'Week ' . ($i + 1),
                'yearweek' => (int) $weekStart->format('oW'),
            ];
        }

        $sections = $rows->pluck('section')->unique()->values()->all();
        $series = [];

        foreach ($sections as $section) {
            $data = [];
            foreach ($weeksList as $week) {
                $row = $rows->first(function ($r) use ($section, $week) {
                    return $r->section === $section && (int) $r->yearweek === $week['yearweek'];
                });
                $data[] = $row ? round((float) $row->avg_score, 1) : 0;
            }
            $series[] = [
                'name' => $section,
                'data' => $data,
            ];
        }

        return [
            'labels' => array_column($weeksList, 'label'),
            'series' => $series,
        ];
    }

    public function getSectionInsights($from = null, $to = null): array
    {
        $rows = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('assessment_sessions.completed_at')
            ->whereNotNull('student_profile.section')
            ->when($from || $to, function ($q) use ($from, $to) {
                $this->applyDateRange($q, 'assessment_sessions.completed_at', $from, $to);
            })
            ->select(
                'student_profile.section',
                DB::raw('AVG(COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage)) as avg_score')
            )
            ->groupBy('student_profile.section')
            ->get();

        $top = $rows->sortByDesc('avg_score')->first();
        $bottom = $rows->sortBy('avg_score')->first();

        $rangeStart = $from ? $from->copy()->startOfDay() : now()->startOfWeek()->subWeeks(6);
        $rangeEnd = $to ? $to->copy()->endOfDay() : now()->endOfWeek();
        $mid = $rangeStart->copy()->addSeconds(intval($rangeStart->diffInSeconds($rangeEnd) / 2));

        $trendRows = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('assessment_sessions.completed_at')
            ->whereNotNull('student_profile.section')
            ->whereBetween('assessment_sessions.completed_at', [$rangeStart, $rangeEnd])
            ->select(
                'student_profile.section',
                DB::raw('AVG(CASE WHEN assessment_sessions.completed_at < "' . $mid->toDateTimeString() . '" THEN COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage) END) as prev_avg'),
                DB::raw('AVG(CASE WHEN assessment_sessions.completed_at >= "' . $mid->toDateTimeString() . '" THEN COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage) END) as recent_avg')
            )
            ->groupBy('student_profile.section')
            ->get();

        $mostImproved = $trendRows->map(function ($r) {
            $prev = $r->prev_avg ?? 0;
            $recent = $r->recent_avg ?? 0;
            $growth = ($recent ?? 0) - ($prev ?? 0);
            return [
                'section' => $r->section,
                'growth' => round($growth, 1),
                'recent' => round((float) $recent, 1),
            ];
        })->sortByDesc('growth')->first();

        return [
            'top' => [
                'section' => $top->section ?? 'N/A',
                'avg' => isset($top->avg_score) ? round((float) $top->avg_score, 1) : 0,
            ],
            'needs_attention' => [
                'section' => $bottom->section ?? 'N/A',
                'avg' => isset($bottom->avg_score) ? round((float) $bottom->avg_score, 1) : 0,
            ],
            'most_improved' => [
                'section' => $mostImproved['section'] ?? 'N/A',
                'growth' => $mostImproved['growth'] ?? 0,
                'recent' => $mostImproved['recent'] ?? 0,
            ],
            'insight' => 'Higher sections show better performance in Algebra topics',
            'insight_detail' => 'Consider curriculum adjustment for lower sections',
        ];
    }

    public function getSectionStatisticsSummary($from = null, $to = null): array
    {
        $sections = DB::table('student_profile')
            ->whereNotNull('section')
            ->distinct()
            ->orderBy('section')
            ->pluck('section')
            ->all();

        $studentCountsQuery = DB::table('student_profile')
            ->whereNotNull('section')
            ->select('section', DB::raw('COUNT(*) as students'));
        $this->applyDateRange($studentCountsQuery, 'created_at', $from, $to);
        $studentCounts = $studentCountsQuery->groupBy('section')->pluck('students', 'section')->all();

        $avgScoresQuery = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('student_profile.section')
            ->select(
                'student_profile.section',
                DB::raw('AVG(COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage)) as avg_score')
            );
        $this->applyDateRange($avgScoresQuery, 'assessment_sessions.completed_at', $from, $to);
        $avgScores = $avgScoresQuery->groupBy('student_profile.section')->pluck('avg_score', 'section')->all();

        $completedUsersQuery = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('student_profile.section')
            ->select('student_profile.section', 'assessment_sessions.user_id')
            ->distinct();
        $this->applyDateRange($completedUsersQuery, 'assessment_sessions.completed_at', $from, $to);
        $completedUsers = $completedUsersQuery->get()->groupBy('section')->map(fn($g) => $g->count())->all();

        $rangeStart = $from ? $from->copy()->startOfDay() : now()->startOfWeek()->subWeeks(6);
        $rangeEnd = $to ? $to->copy()->endOfDay() : now()->endOfWeek();
        $mid = $rangeStart->copy()->addSeconds(intval($rangeStart->diffInSeconds($rangeEnd) / 2));

        $trendRows = DB::table('assessment_sessions')
            ->join('student_profile', 'assessment_sessions.user_id', '=', 'student_profile.user_id')
            ->where('assessment_sessions.status', 'completed')
            ->whereNotNull('assessment_sessions.completed_at')
            ->whereNotNull('student_profile.section')
            ->whereBetween('assessment_sessions.completed_at', [$rangeStart, $rangeEnd])
            ->select(
                'student_profile.section',
                DB::raw('AVG(CASE WHEN assessment_sessions.completed_at < "' . $mid->toDateTimeString() . '" THEN COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage) END) as prev_avg'),
                DB::raw('AVG(CASE WHEN assessment_sessions.completed_at >= "' . $mid->toDateTimeString() . '" THEN COALESCE(assessment_sessions.final_mastery_score, assessment_sessions.accuracy_percentage) END) as recent_avg')
            )
            ->groupBy('student_profile.section')
            ->get()
            ->keyBy('section');

        $rows = [];
        foreach ($sections as $section) {
            $students = (int) ($studentCounts[$section] ?? 0);
            $avg = isset($avgScores[$section]) ? round((float) $avgScores[$section], 1) : 0;
            $completed = (int) ($completedUsers[$section] ?? 0);
            $completion = $students > 0 ? round(($completed / $students) * 100, 1) : 0;

            $trendRow = $trendRows[$section] ?? null;
            $prev = $trendRow->prev_avg ?? 0;
            $recent = $trendRow->recent_avg ?? 0;
            $trend = round(($recent ?? 0) - ($prev ?? 0), 1);

            if ($avg >= 85) {
                $status = 'Excellent';
            } elseif ($avg >= 80) {
                $status = 'Good';
            } elseif ($avg >= 75) {
                $status = 'Average';
            } elseif ($avg >= 70) {
                $status = 'Below Avg';
            } else {
                $status = 'Needs Help';
            }

            $rows[] = [
                'section' => $section,
                'students' => $students,
                'avg_score' => $avg,
                'completion' => $completion,
                'trend' => $trend,
                'status' => $status,
            ];
        }

        return $rows;
    }

    public function getPlatformGrowth($from = null, $to = null): array
    {
        if ($from || $to) {
            $start = ($from ? $from->copy() : now()->startOfMonth()->subMonths(5))->startOfMonth();
            $end = ($to ? $to->copy() : now()->endOfMonth())->endOfMonth();
            $monthCount = max(1, $start->diffInMonths($end) + 1);
            $monthCount = min(12, $monthCount);
            $months = collect(range(0, $monthCount - 1))
                ->map(function ($i) use ($start) {
                    $mStart = (clone $start)->addMonths($i)->startOfMonth();
                    $mEnd = (clone $mStart)->endOfMonth();
                    return [
                        'label' => $mStart->format('M'),
                        'start' => $mStart,
                        'end' => $mEnd,
                    ];
                })
                ->all();
        } else {
            $months = collect(range(0, 5))
                ->map(function ($i) {
                    $start = now()->startOfMonth()->subMonths(5 - $i);
                    $end = (clone $start)->endOfMonth();
                    return [
                        'label' => $start->format('M'),
                        'start' => $start,
                        'end' => $end,
                    ];
                })
                ->all();
        }

        $labels = [];
        $students = [];
        $assessments = [];

        foreach ($months as $m) {
            $labels[] = $m['label'];

            $students[] = DB::table('student_profile')
                ->whereBetween('created_at', [$m['start'], $m['end']])
                ->count();

            $assessments[] = DB::table('assessment_sessions')
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$m['start'], $m['end']])
                ->count();
        }

        return [
            'labels' => $labels,
            'students' => $students,
            'assessments' => $assessments,
        ];
    }

    public function getSummaryCards($from = null, $to = null): array
    {
        $teachersQuery = DB::table('users')->where('role', 'Teacher');
        $adminsQuery = DB::table('users')->where('role', 'Admin');
        $studentsQuery = DB::table('student_profile');
        $assessmentsQuery = DB::table('assessment_sessions')->distinct('session_id');

        $this->applyDateRange($teachersQuery, 'created_at', $from, $to);
        $this->applyDateRange($adminsQuery, 'created_at', $from, $to);
        $this->applyDateRange($studentsQuery, 'created_at', $from, $to);
        $this->applyDateRange($assessmentsQuery, 'completed_at', $from, $to);

        $teachers = (int) $teachersQuery->count();
        $admins = (int) $adminsQuery->count();
        $students = (int) $studentsQuery->count();
        $assessments = (int) $assessmentsQuery->count('session_id');

        return [
            'teachers' => $teachers,
            'students' => $students,
            'admins' => $admins,
            'assessments' => $assessments,
        ];
    }
}
