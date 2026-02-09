<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardMetricsService;

class AdminDashboardController extends Controller
{
    public function index(AdminDashboardMetricsService $metrics)
    {
        $averageScores = $metrics->getAverageScoresBySection();
        $completionRate = $metrics->getAssessmentCompletionRate();
        $mostMissedTopics = $metrics->getMostMissedTopics();
        $performanceByCompetency = $metrics->getPerformanceByCompetency();

        return view('admin.admin.index', [
            'averageScores' => $averageScores,
            'completionRate' => $completionRate,
            'mostMissedTopics' => $mostMissedTopics,
            'performanceByCompetency' => $performanceByCompetency,
        ]);
    }
}
