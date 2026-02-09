<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardMetricsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request, AdminDashboardMetricsService $metrics)
    {
        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : null;

        $averageScores = $metrics->getAverageScoresBySection($from, $to);
        $completionRate = $metrics->getAssessmentCompletionRate($from, $to);
        $mostMissedTopics = $metrics->getMostMissedTopics(5, $from, $to);
        $performanceByCompetency = $metrics->getPerformanceByCompetency($from, $to);
        $sectionPerformanceTrend = $metrics->getSectionPerformanceTrend(6, $from, $to);
        $sectionInsights = $metrics->getSectionInsights($from, $to);
        $sectionStatsSummary = $metrics->getSectionStatisticsSummary($from, $to);
        $platformGrowth = $metrics->getPlatformGrowth($from, $to);
        $summaryCards = $metrics->getSummaryCards($from, $to);

        return view('admin.admin.index', [
            'averageScores' => $averageScores,
            'completionRate' => $completionRate,
            'mostMissedTopics' => $mostMissedTopics,
            'performanceByCompetency' => $performanceByCompetency,
            'sectionPerformanceTrend' => $sectionPerformanceTrend,
            'sectionInsights' => $sectionInsights,
            'sectionStatsSummary' => $sectionStatsSummary,
            'platformGrowth' => $platformGrowth,
            'summaryCards' => $summaryCards,
            'filterFrom' => $from ? $from->toDateString() : null,
            'filterTo' => $to ? $to->toDateString() : null,
        ]);
    }
}
