<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardMetricsService;

class AdminDashboardController extends Controller
{
    public function index(AdminDashboardMetricsService $metrics)
    {
        $averageScores = $metrics->getAverageScoresBySection();

        return view('admin.admin.index', [
            'averageScores' => $averageScores,
        ]);
    }
}
