@extends('admin.admin.layouts.app')

@section('title', 'AralSipnayan')


@section('breadcrumb', 'Dashboard')

@section('content')
    @php
        $forceDemoAnalytics = request()->boolean('demo');
        $usingDemoAnalytics = false;

        $demoAverageScores = [
            'sections' => ['Einstein', 'Curie', 'Newton', 'Galileo', 'Tesla', 'Darwin', 'Turing', 'Lovelace', 'Faraday', 'Kepler', 'Bohr', 'Noether', 'Hopper', 'Shannon', 'Euler', 'Gauss', 'Riemann', 'Archimedes'],
            'scores' => [27.4, 24.8, 29.1, 21.6, 26.3, 23.9, 28.2, 25.7, 22.4, 26.8, 24.1, 27.9, 23.1, 26.0, 28.6, 29.4, 27.2, 24.5],
            'overall' => 26.1,
        ];

        $demoCompletionRate = [
            'completed' => 142,
            'in_progress' => 36,
            'not_started' => 22,
            'total_students' => 200,
            'completion_rate' => 71,
        ];

        $demoMostMissedTopics = [
            'topics' => ['Fractions', 'Linear Equations', 'Percentages', 'Angle Relationships', 'Probability Basics'],
            'accuracy' => [58, 61, 64, 66, 69],
        ];

        $demoPerformanceByCompetency = [
            'sections' => ['Einstein', 'Curie', 'Newton', 'Galileo', 'Tesla', 'Darwin', 'Turing', 'Lovelace', 'Faraday', 'Kepler', 'Bohr', 'Noether', 'Hopper', 'Shannon', 'Euler', 'Gauss', 'Riemann', 'Archimedes'],
            'series' => [
                ['name' => 'Number & Algebra', 'data' => [32.1, 28.7, 34.0, 25.4, 30.6, 27.9, 33.2, 29.8, 26.0, 31.1, 27.4, 32.6, 26.8, 30.9, 34.7, 35.4, 33.8, 28.3]],
                ['name' => 'Measurement & Geometry', 'data' => [26.8, 24.1, 28.5, 21.0, 25.7, 22.9, 27.6, 24.8, 22.0, 26.9, 23.7, 27.8, 22.4, 25.8, 29.1, 30.0, 28.4, 23.6]],
                ['name' => 'Data & Probability', 'data' => [23.4, 21.8, 25.0, 18.6, 22.7, 20.4, 24.2, 21.9, 19.7, 23.4, 21.1, 24.0, 20.2, 22.7, 25.6, 26.4, 24.9, 21.0]],
            ],
        ];

        $demoSectionPerformanceTrend = [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
            'series' => [
                ['name' => 'Einstein', 'data' => [21.4, 23.1, 24.0, 25.8, 26.6, 27.4]],
                ['name' => 'Curie', 'data' => [19.2, 20.1, 22.0, 22.6, 23.8, 24.8]],
                ['name' => 'Newton', 'data' => [22.8, 24.9, 26.0, 26.9, 28.2, 29.1]],
                ['name' => 'Galileo', 'data' => [17.9, 18.7, 19.3, 20.1, 20.9, 21.6]],
                ['name' => 'Tesla', 'data' => [20.4, 21.8, 23.2, 24.1, 25.2, 26.3]],
                ['name' => 'Darwin', 'data' => [18.6, 19.4, 20.7, 21.5, 22.8, 23.9]],
                ['name' => 'Turing', 'data' => [21.0, 22.2, 23.6, 25.1, 26.8, 28.2]],
                ['name' => 'Lovelace', 'data' => [19.9, 21.1, 22.0, 23.7, 24.8, 25.7]],
                ['name' => 'Faraday', 'data' => [17.6, 18.1, 19.0, 20.0, 21.2, 22.4]],
                ['name' => 'Kepler', 'data' => [20.1, 21.0, 22.4, 23.5, 25.1, 26.8]],
            ],
        ];

        $demoSectionInsights = [
            'top' => ['section' => 'Einstein', 'avg' => 27.4],
            'needs_attention' => ['section' => 'Faraday', 'avg' => 22.4],
            'most_improved' => ['section' => 'Einstein', 'growth' => 27.4],
            'insight' => 'Higher sections show better performance in Algebra topics',
            'insight_detail' => 'Consider curriculum adjustment for lower sections',
        ];

        $demoSectionStatsSummary = [
            ['section' => 'Einstein', 'students' => 38, 'avg_score' => 27.4, 'completion' => 71, 'trend' => 3.1, 'status' => 'Below Avg'],
            ['section' => 'Curie', 'students' => 36, 'avg_score' => 24.8, 'completion' => 64, 'trend' => 2.0, 'status' => 'Needs Help'],
            ['section' => 'Newton', 'students' => 41, 'avg_score' => 29.1, 'completion' => 69, 'trend' => 2.8, 'status' => 'Below Avg'],
            ['section' => 'Galileo', 'students' => 33, 'avg_score' => 21.6, 'completion' => 58, 'trend' => 1.4, 'status' => 'Needs Help'],
            ['section' => 'Tesla', 'students' => 39, 'avg_score' => 26.3, 'completion' => 66, 'trend' => 2.2, 'status' => 'Below Avg'],
            ['section' => 'Darwin', 'students' => 35, 'avg_score' => 23.9, 'completion' => 61, 'trend' => 1.7, 'status' => 'Needs Help'],
            ['section' => 'Turing', 'students' => 37, 'avg_score' => 28.2, 'completion' => 68, 'trend' => 2.6, 'status' => 'Below Avg'],
            ['section' => 'Lovelace', 'students' => 34, 'avg_score' => 25.7, 'completion' => 63, 'trend' => 1.9, 'status' => 'Needs Help'],
            ['section' => 'Faraday', 'students' => 32, 'avg_score' => 22.4, 'completion' => 57, 'trend' => -0.6, 'status' => 'Needs Help'],
            ['section' => 'Kepler', 'students' => 40, 'avg_score' => 26.8, 'completion' => 67, 'trend' => 1.2, 'status' => 'Below Avg'],
            ['section' => 'Bohr', 'students' => 31, 'avg_score' => 24.1, 'completion' => 60, 'trend' => 0.4, 'status' => 'Needs Help'],
            ['section' => 'Noether', 'students' => 43, 'avg_score' => 27.9, 'completion' => 73, 'trend' => 2.1, 'status' => 'Below Avg'],
            ['section' => 'Hopper', 'students' => 30, 'avg_score' => 23.1, 'completion' => 59, 'trend' => 0.7, 'status' => 'Needs Help'],
            ['section' => 'Shannon', 'students' => 44, 'avg_score' => 26.0, 'completion' => 70, 'trend' => 1.5, 'status' => 'Below Avg'],
            ['section' => 'Euler', 'students' => 46, 'avg_score' => 28.6, 'completion' => 74, 'trend' => 2.9, 'status' => 'Below Avg'],
            ['section' => 'Gauss', 'students' => 42, 'avg_score' => 29.4, 'completion' => 76, 'trend' => 3.4, 'status' => 'Below Avg'],
            ['section' => 'Riemann', 'students' => 28, 'avg_score' => 27.2, 'completion' => 65, 'trend' => 1.1, 'status' => 'Below Avg'],
            ['section' => 'Archimedes', 'students' => 29, 'avg_score' => 24.5, 'completion' => 62, 'trend' => 0.9, 'status' => 'Needs Help'],
        ];

        $demoPlatformGrowth = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'students' => [120, 132, 141, 155, 168, 180, 192, 205, 214, 228, 240, 255],
            'assessments' => [18, 22, 25, 28, 31, 34, 37, 40, 45, 48, 52, 56],
        ];

        $averageScoresView = (isset($averageScores) && is_array($averageScores)) ? $averageScores : null;
        $completionRateView = (isset($completionRate) && is_array($completionRate)) ? $completionRate : null;
        $mostMissedTopicsView = (isset($mostMissedTopics) && is_array($mostMissedTopics)) ? $mostMissedTopics : null;
        $performanceByCompetencyView = (isset($performanceByCompetency) && is_array($performanceByCompetency)) ? $performanceByCompetency : null;
        $sectionPerformanceTrendView = (isset($sectionPerformanceTrend) && is_array($sectionPerformanceTrend)) ? $sectionPerformanceTrend : null;
        $sectionInsightsView = (isset($sectionInsights) && is_array($sectionInsights)) ? $sectionInsights : null;
        $sectionStatsSummaryView = (isset($sectionStatsSummary) && is_array($sectionStatsSummary)) ? $sectionStatsSummary : null;
        $platformGrowthView = (isset($platformGrowth) && is_array($platformGrowth)) ? $platformGrowth : null;

        $averageScoresView = is_array($averageScoresView) ? $averageScoresView : [];
        $mostMissedTopicsView = is_array($mostMissedTopicsView) ? $mostMissedTopicsView : [];
        $performanceByCompetencyView = is_array($performanceByCompetencyView) ? $performanceByCompetencyView : [];
        $sectionPerformanceTrendView = is_array($sectionPerformanceTrendView) ? $sectionPerformanceTrendView : [];
        $sectionInsightsView = is_array($sectionInsightsView) ? $sectionInsightsView : [];
        $sectionStatsSummaryView = is_array($sectionStatsSummaryView) ? $sectionStatsSummaryView : [];
        $platformGrowthView = is_array($platformGrowthView) ? $platformGrowthView : [];

        $averageScoresEmpty = empty($averageScoresView['sections'] ?? []);
        $completionRateView = is_array($completionRateView) ? $completionRateView : [];
        $completionRateView = array_replace($completionRateView, $demoCompletionRate);
        $usingDemoAnalytics = true;

        $completionRateEmpty = false;
        if (empty($completionRateView) || (int) ($completionRateView['total_students'] ?? 0) === 0) {
            $completionRateEmpty = true;
        } else {
            $totalStudents = (int) ($completionRateView['total_students'] ?? 0);
            $completed = (int) ($completionRateView['completed'] ?? 0);
            $inProgress = (int) ($completionRateView['in_progress'] ?? 0);
            $notStarted = (int) ($completionRateView['not_started'] ?? 0);
            if ($totalStudents > 0 && $completed === 0 && $inProgress === 0 && $notStarted === $totalStudents) {
                $completionRateEmpty = true;
            }
        }
        $mostMissedTopicsEmpty = empty($mostMissedTopicsView['topics'] ?? []);
        $performanceByCompetencyEmpty = true;
        if (
            !empty($performanceByCompetencyView)
            && !empty($performanceByCompetencyView['sections'] ?? [])
            && !empty($performanceByCompetencyView['series'] ?? [])
        ) {
            $hasNonZero = false;
            foreach (($performanceByCompetencyView['series'] ?? []) as $s) {
                foreach (($s['data'] ?? []) as $v) {
                    if ((float) $v > 0) {
                        $hasNonZero = true;
                        break 2;
                    }
                }
            }
            $performanceByCompetencyEmpty = !$hasNonZero;
        }

        $sectionPerformanceTrendEmpty = true;
        if (!empty($sectionPerformanceTrendView) && !empty($sectionPerformanceTrendView['series'] ?? [])) {
            $hasNonZero = false;
            foreach (($sectionPerformanceTrendView['series'] ?? []) as $s) {
                foreach (($s['data'] ?? []) as $v) {
                    if ((float) $v > 0) {
                        $hasNonZero = true;
                        break 2;
                    }
                }
            }
            $sectionPerformanceTrendEmpty = !$hasNonZero;
        }
        $sectionInsightsEmpty = empty($sectionInsightsView['top']['section'] ?? null);
        $sectionStatsSummaryEmpty = empty($sectionStatsSummaryView);
        $platformGrowthEmpty = empty($platformGrowthView['labels'] ?? []);

        if ($completionRateEmpty) {
            $completionRateView = $demoCompletionRate;
            $usingDemoAnalytics = true;
        }
        if ($platformGrowthEmpty) {
            $platformGrowthView = $demoPlatformGrowth;
            $usingDemoAnalytics = true;
        }

        $sectionInsightsView = array_replace_recursive(
            $demoSectionInsights,
            is_array($sectionInsightsView) ? $sectionInsightsView : []
        );

        $mergeSectionValueSeries = function (array $demoLabels, array $demoValues, array $realLabels, array $realValues): array {
            $demoMap = [];
            foreach ($demoLabels as $idx => $label) {
                $demoMap[(string) $label] = $demoValues[$idx] ?? 0;
            }

            $realOrder = [];
            $realMap = [];
            foreach ($realLabels as $idx => $label) {
                $key = (string) $label;
                $realOrder[] = $key;
                $realMap[$key] = $realValues[$idx] ?? 0;
            }

            $mergedMap = $demoMap;
            foreach ($realMap as $k => $v) {
                $mergedMap[$k] = $v;
            }

            $labels = [];
            foreach ($realOrder as $k) {
                if (array_key_exists($k, $mergedMap)) {
                    $labels[] = $k;
                }
            }
            foreach (array_keys($mergedMap) as $k) {
                if (!in_array($k, $labels, true)) {
                    $labels[] = $k;
                }
            }

            $values = array_map(fn ($k) => $mergedMap[$k], $labels);
            return [$labels, $values];
        };

        [$mergedAverageSections, $mergedAverageScores] = $mergeSectionValueSeries(
            $demoAverageScores['sections'],
            $demoAverageScores['scores'],
            (array) ($averageScoresView['sections'] ?? []),
            (array) ($averageScoresView['scores'] ?? [])
        );
        $averageScoresView = [
            'sections' => $mergedAverageSections,
            'scores' => $mergedAverageScores,
            'overall' => (float) ($averageScoresView['overall'] ?? $demoAverageScores['overall']),
        ];

        $demoTopics = (array) ($demoMostMissedTopics['topics'] ?? []);
        $demoAccuracy = (array) ($demoMostMissedTopics['accuracy'] ?? []);
        $realTopics = (array) ($mostMissedTopicsView['topics'] ?? []);
        $realAccuracy = (array) ($mostMissedTopicsView['accuracy'] ?? []);

        $demoTopicMap = [];
        foreach ($demoTopics as $idx => $t) {
            $demoTopicMap[(string) $t] = (float) ($demoAccuracy[$idx] ?? 0);
        }
        $realTopicMap = [];
        foreach ($realTopics as $idx => $t) {
            $realTopicMap[(string) $t] = (float) ($realAccuracy[$idx] ?? 0);
        }

        $topics = [];
        foreach ($realTopics as $t) {
            $topics[] = (string) $t;
        }
        foreach ($demoTopics as $t) {
            $key = (string) $t;
            if (!in_array($key, $topics, true)) {
                $topics[] = $key;
            }
        }

        $accuracy = [];
        foreach ($topics as $t) {
            $accuracy[] = array_key_exists($t, $realTopicMap) ? $realTopicMap[$t] : ($demoTopicMap[$t] ?? 0);
        }
        $mostMissedTopicsView = ['topics' => $topics, 'accuracy' => $accuracy];

        $realCompetencySections = (array) ($performanceByCompetencyView['sections'] ?? []);
        $mergedCompetencySections = [];
        foreach ($realCompetencySections as $s) {
            $key = (string) $s;
            if (!in_array($key, $mergedCompetencySections, true)) {
                $mergedCompetencySections[] = $key;
            }
        }
        foreach (($demoPerformanceByCompetency['sections'] ?? []) as $s) {
            $key = (string) $s;
            if (!in_array($key, $mergedCompetencySections, true)) {
                $mergedCompetencySections[] = $key;
            }
        }

        $normalizeSeriesName = function ($name): string {
            $name = strtolower((string) $name);
            $name = str_replace('&', 'and', $name);
            $name = preg_replace('/[^a-z0-9 ]+/', '', $name);
            $name = preg_replace('/\\s+/', ' ', $name);
            return trim($name);
        };

        $demoSeriesByKey = [];
        foreach (($demoPerformanceByCompetency['series'] ?? []) as $s) {
            $k = $normalizeSeriesName($s['name'] ?? '');
            if ($k !== '') {
                $demoSeriesByKey[$k] = (array) $s;
            }
        }

        $realSeriesByKey = [];
        foreach (((array) ($performanceByCompetencyView['series'] ?? [])) as $s) {
            $k = $normalizeSeriesName($s['name'] ?? '');
            if ($k !== '') {
                $realSeriesByKey[$k] = (array) $s;
            }
        }

        $allSeriesKeys = array_values(array_unique(array_merge(array_keys($realSeriesByKey), array_keys($demoSeriesByKey))));

        $mergedSeries = [];
        foreach ($allSeriesKeys as $seriesKey) {
            $demoSeries = (array) ($demoSeriesByKey[$seriesKey] ?? []);
            $realSeries = (array) ($realSeriesByKey[$seriesKey] ?? []);

            $demoMap = [];
            foreach (($demoPerformanceByCompetency['sections'] ?? []) as $idx => $sec) {
                $demoMap[(string) $sec] = (float) (($demoSeries['data'][$idx] ?? 0));
            }

            $realMap = [];
            foreach ($realCompetencySections as $idx => $sec) {
                $realMap[(string) $sec] = (float) (($realSeries['data'][$idx] ?? 0));
            }

            $data = [];
            foreach ($mergedCompetencySections as $sec) {
                $key = (string) $sec;
                $data[] = array_key_exists($key, $realMap) ? $realMap[$key] : ($demoMap[$key] ?? 0);
            }

            $mergedSeries[] = [
                'name' => (string) (($realSeries['name'] ?? null) ?: ($demoSeries['name'] ?? 'Competency')),
                'data' => $data,
            ];
        }
        $performanceByCompetencyView = [
            'sections' => $mergedCompetencySections,
            'series' => $mergedSeries,
        ];

        $trendLabels = (array) (($sectionPerformanceTrendView['labels'] ?? []) ?: ($demoSectionPerformanceTrend['labels'] ?? []));
        $trendLabelCount = count($trendLabels);
        $trendSeriesByNameReal = collect((array) ($sectionPerformanceTrendView['series'] ?? []))->keyBy('name')->all();
        $trendSeriesByNameDemo = collect((array) ($demoSectionPerformanceTrend['series'] ?? []))->keyBy('name')->all();
        $trendSeriesNames = array_values(array_unique(array_merge(array_keys($trendSeriesByNameReal), array_keys($trendSeriesByNameDemo))));

        $trendSeries = [];
        foreach ($trendSeriesNames as $name) {
            $real = (array) ($trendSeriesByNameReal[$name] ?? []);
            $demo = (array) ($trendSeriesByNameDemo[$name] ?? []);
            $data = (array) (($real['data'] ?? []) ?: ($demo['data'] ?? []));
            $data = array_slice(array_pad($data, $trendLabelCount, 0), 0, $trendLabelCount);
            $trendSeries[] = ['name' => $name, 'data' => $data];
        }
        $sectionPerformanceTrendView = [
            'labels' => $trendLabels,
            'series' => $trendSeries,
        ];

        $demoStatsBySection = collect($demoSectionStatsSummary)->keyBy('section')->all();
        $realStats = is_array($sectionStatsSummaryView) ? $sectionStatsSummaryView : [];
        $realStatsBySection = collect($realStats)->keyBy('section')->all();
        $mergedStatsBySection = $demoStatsBySection;
        foreach ($realStatsBySection as $sec => $row) {
            $mergedStatsBySection[$sec] = array_replace($demoStatsBySection[$sec] ?? [], (array) $row);
        }

        $orderedSections = [];
        foreach ($realStats as $row) {
            $sec = $row['section'] ?? null;
            if ($sec !== null) {
                $orderedSections[] = (string) $sec;
            }
        }
        foreach (array_keys($demoStatsBySection) as $sec) {
            if (!in_array($sec, $orderedSections, true)) {
                $orderedSections[] = $sec;
            }
        }

        $sectionStatsSummaryView = array_values(array_filter(array_map(
            fn ($sec) => $mergedStatsBySection[$sec] ?? null,
            $orderedSections
        )));

        if ($forceDemoAnalytics) {
            $usingDemoAnalytics = true;
        }
    @endphp

    <!-- Welcome Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
        <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your learning platform.</p>
    </div>

    @if ($usingDemoAnalytics)
        {{-- <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-info-circle text-blue-700 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900">Showing sample analytics data</p>
                    <p class="text-sm text-blue-800 mt-1">Connect real dashboard metrics from the controller, or force sample mode with <span class="font-mono">?demo=1</span>.</p>
                </div>
            </div>
        </div> --}}
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Registered Teachers -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Registered Teachers</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $summaryCards['teachers'] ?? 0 }}</p>
                    {{-- <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+3 from last month</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 7%</span>
                    </div> --}}
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Active Students -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Active Students</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $summaryCards['students'] ?? 0 }}</p>
                    {{-- <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">+12% from last month</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 12%</span>
                    </div> --}}
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Admins -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Admins</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $summaryCards['admins'] ?? 0 }}</p>
                    {{-- <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+1 from last month</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 14%</span>
                    </div> --}}
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Assessments -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Assessments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $summaryCards['assessments'] ?? 0 }}</p>
                    {{-- <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+8 new this week</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 5%</span>
                    </div> --}}
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Quick Links</h3>
        <p class="text-sm text-gray-600 mb-4">Manage your platform efficiently</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Manage Admins -->
            <a href="{{ route('admin.management.admins') }}"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-4">
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <i class="fas fa-user-shield text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Manage Admins</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $summaryCards['admins'] ?? 0 }}</p>
                    </div>
                </div>
            </a>

            <!-- Manage Teachers -->
            <a href="{{ route('admin.management.teachers') }}"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-4">
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <i class="fas fa-chalkboard-teacher text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Manage Teachers</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $summaryCards['teachers'] ?? 0 }} registered teachers</p>
                    </div>
                </div>
            </a>

            <!-- Manage Students -->
            <a href="{{ route('admin.management.students') }}"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-4">
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <i class="fas fa-users text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Manage Students</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $summaryCards['students'] ?? 0 }} active students</p>
                    </div>
                </div>
            </a>

            <!-- Manage Sections -->
            <a href="{{ route('admin.management.sections') }}"
                class="bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-4">
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <i class="fas fa-th-large text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Manage Sections</h4>
                        <p class="text-sm text-gray-500 mt-1">View all sections</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Performance Analytics Overview -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Performance Analytics Overview</h3>
                    <p class="text-sm text-gray-600 mt-1">Section performance metrics and insights</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="fromDate" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <input id="fromDate" name="from" type="date" value="{{ $filterFrom ?? '' }}"
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="toDate" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <input id="toDate" name="to" type="date" value="{{ $filterTo ?? '' }}"
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <button type="submit"
                        class="h-[38px] px-4 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors">
                        Apply
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Average Student Scores by Section -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900">Average Scores by Section</h4>
                            <p class="text-sm text-gray-600 mt-1">Performance across different sections</p>
                         </div>
                         <div class="text-right">
                             <p class="text-2xl font-bold text-blue-600">{{ $averageScoresView['overall'] ?? 0 }}%</p>
                             <p class="text-xs text-gray-500">Overall Average</p>
                         </div>
                     </div>
                     <div id="averageScoresChart"></div>
                </div>

                <!-- Assessment Completion Rate -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900">Assessment Completion Rate</h4>
                            <p class="text-sm text-gray-600 mt-1">Student completion statistics</p>
                         </div>
                         <div class="text-right">
                             <p class="text-2xl font-bold text-green-600">{{ $completionRateView['completion_rate'] ?? 0 }}%</p>
                             <p class="text-xs text-gray-500">Completion Rate</p>
                         </div>
                     </div>
                     <div id="completionRateChart"></div>
                     <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                         <div class="text-center">
                             <p class="text-xs text-gray-500">Completed</p>
                             <p class="text-lg font-semibold text-green-600">{{ $completionRateView['completed'] ?? 0 }}</p>
                         </div>
                         <div class="text-center">
                             <p class="text-xs text-gray-500">In Progress</p>
                             <p class="text-lg font-semibold text-yellow-600">{{ $completionRateView['in_progress'] ?? 0 }}</p>
                         </div>
                         <div class="text-center">
                             <p class="text-xs text-gray-500">Not Started</p>
                             <p class="text-lg font-semibold text-red-600">{{ $completionRateView['not_started'] ?? 0 }}</p>
                         </div>
                     </div>
                 </div>
             </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Average Accuracy by Topic -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="mb-4">
                        <h4 class="text-base font-semibold text-gray-900">Average Accuracy by Topic</h4>
                        <p class="text-sm text-gray-600 mt-1">Average performance across different topics</p>
                    </div>
                    <div id="missedTopicsChart"></div>
                </div>

                <!-- Performance by Competency -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="mb-4">
                        <h4 class="text-base font-semibold text-gray-900">Performance by Competency</h4>
                        <p class="text-sm text-gray-600 mt-1">Section performance across competencies</p>
                    </div>
                    <div id="competencyChart"></div>
                </div>
            </div>

            <!-- Performance Trend per Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="mb-6">
                    <h4 class="text-base font-semibold text-gray-900">Performance Trend per Section</h4>
                    <p class="text-sm text-gray-600 mt-1">Comparative analysis across all sections</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Chart -->
                    <div class="lg:col-span-2">
                        <div id="sectionPerformanceLegend" class="flex flex-wrap items-center gap-3 mb-3"></div>
                        <div id="sectionPerformanceChart"></div>
                    </div>

                    <!-- Insights Panel -->
                    <div class="space-y-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <i class="fas fa-trophy text-green-600 text-sm"></i>
                                </div>
                                 <div>
                                      <p class="text-xs font-semibold text-green-800 uppercase tracking-wide">Top Performer</p>
                                      <p class="text-lg font-bold text-green-900 mt-1">{{ $sectionInsightsView['top']['section'] ?? 'N/A' }}</p>
                                      <p class="text-sm text-green-700 mt-1">Average Score: {{ $sectionInsightsView['top']['avg'] ?? 0 }}%</p>
                                      {{-- <p class="text-xs text-green-700 mt-1">Sample: {{ $demoSectionInsights['top']['section'] ?? 'Einstein' }} ({{ $demoSectionInsights['top']['avg'] ?? 27.4 }}%)</p> --}}
                                      <p class="text-xs text-green-600 mt-2">Consistent improvement over 3 months</p>
                                  </div>
                              </div>
                          </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                                </div>
                                 <div>
                                      <p class="text-xs font-semibold text-red-800 uppercase tracking-wide">Needs Attention</p>
                                      <p class="text-lg font-bold text-red-900 mt-1">{{ $sectionInsightsView['needs_attention']['section'] ?? 'N/A' }}</p>
                                      <p class="text-sm text-red-700 mt-1">Average Score: {{ $sectionInsightsView['needs_attention']['avg'] ?? 0 }}%</p>
                                      {{-- <p class="text-xs text-red-700 mt-1">Sample: {{ $demoSectionInsights['needs_attention']['section'] ?? 'Einstein' }} ({{ $demoSectionInsights['needs_attention']['avg'] ?? 27.4 }}%)</p> --}}
                                      <p class="text-xs text-red-600 mt-2">Declining trend in recent weeks</p>
                                  </div>
                              </div>
                          </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fas fa-chart-line text-blue-600 text-sm"></i>
                                </div>
                                 <div>
                                      <p class="text-xs font-semibold text-blue-800 uppercase tracking-wide">Most Improved</p>
                                      <p class="text-lg font-bold text-blue-900 mt-1">{{ $sectionInsightsView['most_improved']['section'] ?? 'N/A' }}</p>
                                      <p class="text-sm text-blue-700 mt-1">Growth: {{ $sectionInsightsView['most_improved']['growth'] ?? 0 }}%</p>
                                      {{-- <p class="text-xs text-blue-700 mt-1">Sample: {{ $demoSectionInsights['most_improved']['section'] ?? 'Einstein' }} (+{{ $demoSectionInsights['most_improved']['growth'] ?? 27.4 }}%)</p> --}}
                                      <p class="text-xs text-blue-600 mt-2">Significant progress this quarter</p>
                                  </div>
                              </div>
                          </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="p-2 bg-yellow-100 rounded-lg">
                                    <i class="fas fa-lightbulb text-yellow-600 text-sm"></i>
                                </div>
                                 <div>
                                      <p class="text-xs font-semibold text-yellow-800 uppercase tracking-wide">Insight</p>
                                      <p class="text-sm text-yellow-900 mt-1 font-medium">{{ $sectionInsightsView['insight'] ?? 'No insight available' }}</p>
                                      <p class="text-xs text-yellow-700 mt-2">{{ $sectionInsightsView['insight_detail'] ?? '' }}</p>
                                      <div class="mt-2 pt-2 border-t border-yellow-200">
                                          <p class="text-xs font-semibold text-yellow-800">Sample</p>
                                          <p class="text-xs text-yellow-800 mt-1">{{ $demoSectionInsights['insight'] ?? '' }}</p>
                                          <p class="text-xs text-yellow-700 mt-1">{{ $demoSectionInsights['insight_detail'] ?? '' }}</p>
                                      </div>
                                 </div>
                              </div>
                          </div>
                     </div>
                </div>

                <!-- Section Statistics Table -->
                <div class="border-t border-gray-200 pt-6">
                    <h5 class="text-sm font-semibold text-gray-900 mb-4">Section Statistics Summary</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Section</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Students</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg. Score</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Completion</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trend</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sectionStatsSummaryView ?? [] as $row)
                                    @php
                                        $trend = $row['trend'] ?? 0;
                                        $trendClass = $trend > 0 ? 'text-green-600' : ($trend < 0 ? 'text-red-600' : 'text-gray-600');
                                        $trendIcon = $trend > 0 ? 'fa-arrow-up' : ($trend < 0 ? 'fa-arrow-down' : 'fa-minus');
                                        $status = $row['status'] ?? 'Average';
                                        $statusClass = match ($status) {
                                            'Excellent' => 'bg-green-100 text-green-800',
                                            'Good' => 'bg-blue-100 text-blue-800',
                                            'Average' => 'bg-gray-100 text-gray-800',
                                            'Below Avg' => 'bg-yellow-100 text-yellow-800',
                                            'Needs Help' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['section'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $row['students'] }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold {{ ($row['avg_score'] ?? 0) >= 85 ? 'text-green-600' : (($row['avg_score'] ?? 0) >= 75 ? 'text-blue-600' : (($row['avg_score'] ?? 0) >= 70 ? 'text-yellow-600' : 'text-red-600')) }}">{{ $row['avg_score'] }}%</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $row['completion'] }}%</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center {{ $trendClass }}">
                                                <i class="fas {{ $trendIcon }} text-xs mr-1"></i>
                                                <span class="font-medium">{{ $trend > 0 ? '+' : '' }}{{ $trend }}%</span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-sm text-gray-500 text-center">No section statistics available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Growth and Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
            <!-- Platform Growth Chart -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Platform Growth</h3>
                <p class="text-sm text-gray-600 mb-6">Monthly active students and assessments taken</p>

                <!-- Chart Container -->
                <div id="growthChart"></div>
            </div>

            {{-- <!-- Recent Activities -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Activities</h3>
                <p class="text-sm text-gray-600 mb-6">Latest system activities</p>

                <div class="space-y-4">
                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <i class="fas fa-user-plus text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">New teacher registered</p>
                            <p class="text-xs text-gray-500">Maria Santos joined Grade 7 section</p>
                        </div>
                        <span class="text-xs text-gray-400">2m ago</span>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <i class="fas fa-folder-plus text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Section created</p>
                            <p class="text-xs text-gray-500">Grade 8-A section added</p>
                        </div>
                        <span class="text-xs text-gray-400">5m ago</span>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <i class="fas fa-user-shield text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Admin account updated</p>
                            <p class="text-xs text-gray-500">John Doe profile modified</p>
                        </div>
                        <span class="text-xs text-gray-400">12m ago</span>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <i class="fas fa-users text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Student enrolled</p>
                            <p class="text-xs text-gray-500">25 new students added to system</p>
                        </div>
                        <span class="text-xs text-gray-400">1h ago</span>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <i class="fas fa-clipboard-check text-gray-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Assessment created</p>
                            <p class="text-xs text-gray-500">New Algebra assessment published</p>
                        </div>
                        <span class="text-xs text-gray-400">2h ago</span>
                    </div>
                </div>
            </div> --}}
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
         document.addEventListener('DOMContentLoaded', function() {
             @php
                $averageScoresData = $averageScoresView ?? ['sections' => [], 'scores' => [], 'overall' => 0];
                $completionRateData = $completionRateView ?? [
                    'completed' => 0,
                    'in_progress' => 0,
                    'not_started' => 0,
                    'completion_rate' => 0,
                    'total_students' => 0,
                ];
                $mostMissedTopicsData = $mostMissedTopicsView ?? ['topics' => [], 'accuracy' => []];
                $performanceByCompetencyData = $performanceByCompetencyView ?? ['sections' => [], 'series' => []];
                $sectionPerformanceTrendData = $sectionPerformanceTrendView ?? ['labels' => ['Week 1','Week 2','Week 3','Week 4','Week 5','Week 6'], 'series' => []];
                $platformGrowthData = $platformGrowthView ?? ['labels' => [], 'students' => [], 'assessments' => []];
             @endphp
            const averageScoresData = @json($averageScoresData);
            const completionRateData = @json($completionRateData);
            const mostMissedTopicsData = @json($mostMissedTopicsData);
            const performanceByCompetencyData = @json($performanceByCompetencyData);
            const sectionPerformanceTrendData = @json($sectionPerformanceTrendData);
            const platformGrowthData = @json($platformGrowthData);
            const sections = averageScoresData.sections || [];
            const currentScores = averageScoresData.scores || [];
            const monthlyData = {
                january: currentScores,
                february: currentScores,
                march: currentScores,
                april: currentScores,
                may: currentScores,
                june: currentScores,
                july: currentScores,
                august: currentScores,
                september: currentScores,
                october: currentScores,
                november: currentScores,
                december: currentScores
            };

            // Competency data for sections
            const competencyData = {
                january: {
                    'Number and Algebra': [85, 82, 78, 80, 77, 83, 79, 82, 76, 81],
                    'Measurement and Geometry': [80, 77, 74, 76, 73, 79, 75, 78, 72, 77],
                    'Data and Probability': [75, 72, 69, 71, 68, 74, 70, 73, 67, 72]
                },
                february: {
                    'Number and Algebra': [86, 83, 79, 81, 78, 84, 80, 83, 77, 82],
                    'Measurement and Geometry': [81, 78, 75, 77, 74, 80, 76, 79, 73, 78],
                    'Data and Probability': [76, 73, 70, 72, 69, 75, 71, 74, 68, 73]
                },
                march: {
                    'Number and Algebra': [87, 84, 80, 82, 79, 85, 81, 84, 78, 83],
                    'Measurement and Geometry': [82, 79, 76, 78, 75, 81, 77, 80, 74, 79],
                    'Data and Probability': [77, 74, 71, 73, 70, 76, 72, 75, 69, 74]
                },
                april: {
                    'Number and Algebra': [88, 85, 81, 83, 80, 86, 82, 85, 79, 84],
                    'Measurement and Geometry': [83, 80, 77, 79, 76, 82, 78, 81, 75, 80],
                    'Data and Probability': [78, 75, 72, 74, 71, 77, 73, 76, 70, 75]
                },
                may: {
                    'Number and Algebra': [89, 86, 82, 84, 81, 87, 83, 86, 80, 85],
                    'Measurement and Geometry': [84, 81, 78, 80, 77, 83, 79, 82, 76, 81],
                    'Data and Probability': [79, 76, 73, 75, 72, 78, 74, 77, 71, 76]
                },
                june: {
                    'Number and Algebra': [90, 87, 83, 85, 82, 88, 84, 87, 81, 86],
                    'Measurement and Geometry': [85, 82, 79, 81, 78, 84, 80, 83, 77, 82],
                    'Data and Probability': [80, 77, 74, 76, 73, 79, 75, 78, 72, 77]
                },
                july: {
                    'Number and Algebra': [91, 88, 84, 86, 83, 89, 85, 88, 82, 87],
                    'Measurement and Geometry': [86, 83, 80, 82, 79, 85, 81, 84, 78, 83],
                    'Data and Probability': [81, 78, 75, 77, 74, 80, 76, 79, 73, 78]
                },
                august: {
                    'Number and Algebra': [92, 89, 85, 87, 84, 90, 86, 89, 83, 88],
                    'Measurement and Geometry': [87, 84, 81, 83, 80, 86, 82, 85, 79, 84],
                    'Data and Probability': [82, 79, 76, 78, 75, 81, 77, 80, 74, 79]
                },
                september: {
                    'Number and Algebra': [93, 90, 86, 88, 85, 91, 87, 90, 84, 89],
                    'Measurement and Geometry': [88, 85, 82, 84, 81, 87, 83, 86, 80, 85],
                    'Data and Probability': [83, 80, 77, 79, 76, 82, 78, 81, 75, 80]
                },
                october: {
                    'Number and Algebra': [94, 91, 87, 89, 86, 92, 88, 91, 85, 90],
                    'Measurement and Geometry': [89, 86, 83, 85, 82, 88, 84, 87, 81, 86],
                    'Data and Probability': [84, 81, 78, 80, 77, 83, 79, 82, 76, 81]
                },
                november: {
                    'Number and Algebra': [95, 92, 88, 90, 87, 93, 89, 92, 86, 91],
                    'Measurement and Geometry': [90, 87, 84, 86, 83, 89, 85, 88, 82, 87],
                    'Data and Probability': [85, 82, 79, 81, 78, 84, 80, 83, 77, 82]
                },
                december: {
                    'Number and Algebra': [96, 93, 89, 91, 88, 94, 90, 93, 87, 92],
                    'Measurement and Geometry': [91, 88, 85, 87, 84, 90, 86, 89, 83, 88],
                    'Data and Probability': [86, 83, 80, 82, 79, 85, 81, 84, 78, 83]
                }
            };

            // Chart instances
            let scoresChart, completionChart, missedTopicsChart, competencyChart, 
                sectionPerformanceChart, growthChart;

            // Initialize all charts
            function initializeCharts(month = 'january') {
                // 1. Platform Growth Chart
                const growthOptions = {
                    series: [{
                        name: 'Assessments',
                        data: platformGrowthData.assessments || []
                    }, {
                        name: 'Students',
                        data: platformGrowthData.students || []
                    }],
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#FB923C', '#3B82F6'],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.4,
                            opacityTo: 0.1,
                        }
                    },
                    xaxis: {
                        categories: platformGrowthData.labels || [],
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        fontSize: '12px',
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 2
                        }
                    },
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                };

                if (growthChart) {
                    growthChart.destroy();
                }
                growthChart = new ApexCharts(document.querySelector("#growthChart"), growthOptions);
                growthChart.render();

                // 2. Average Scores by Section Chart
                const scoresOptions = {
                    series: [{
                        name: 'Average Score',
                        data: monthlyData[month]
                    }],
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#3B82F6'],
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '50%',
                            distributed: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: sections,
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            rotate: -45
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    },
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
                };

                if (scoresChart) {
                    scoresChart.destroy();
                }
                scoresChart = new ApexCharts(document.querySelector("#averageScoresChart"), scoresOptions);
                scoresChart.render();

                // 3. Assessment Completion Rate Chart
                const completionOptions = {
                    series: [
                        completionRateData.completed || 0,
                        completionRateData.in_progress || 0,
                        completionRateData.not_started || 0
                    ],
                    chart: {
                        type: 'donut',
                        height: 250,
                        fontFamily: 'Inter, sans-serif'
                    },
                    labels: ['Completed', 'In Progress', 'Not Started'],
                    colors: ['#10B981', '#F59E0B', '#EF4444'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            return opts.w.config.series[opts.seriesIndex];
                        },
                        style: {
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: false
                                }
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                const total = completionRateData.total_students || 0;
                                const percentage = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                return val + ' (' + percentage + '%)';
                            }
                        }
                    }
                };

                if (completionChart) {
                    completionChart.destroy();
                }
                completionChart = new ApexCharts(document.querySelector("#completionRateChart"), completionOptions);
                completionChart.render();

                // 4. Most Missed Topics Chart
                const missedTopicsOptions = {
                    series: [{
                        name: 'Accuracy Rate',
                        data: mostMissedTopicsData.accuracy || []
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#EF4444', '#F97316', '#EAB308', '#3B82F6', '#10B981'],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            distributed: true,
                            barHeight: '70%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val + '%';
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 600,
                            colors: ['#fff']
                        }
                    },
                    xaxis: {
                        categories: mostMissedTopicsData.topics || [],
                        min: 0,
                        max: 100,
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#374151',
                                fontSize: '12px'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                return val + '% accuracy';
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
                };

                if (missedTopicsChart) {
                    missedTopicsChart.destroy();
                }
                missedTopicsChart = new ApexCharts(document.querySelector("#missedTopicsChart"), missedTopicsOptions);
                missedTopicsChart.render();

                // 5. Performance by Competency Chart
                const competencyOptions = {
                    series: performanceByCompetencyData.series || [],
                    chart: {
                        type: 'bar',
                        height: 350,
                        stacked: false,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#3B82F6', '#10B981', '#F59E0B'],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: performanceByCompetencyData.sections || [],
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            rotate: -45
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'center',
                        fontSize: '12px',
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 2
                        }
                    },
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                return val + '%';
                            }
                        }
                    }
                };

                if (competencyChart) {
                    competencyChart.destroy();
                }
                competencyChart = new ApexCharts(document.querySelector("#competencyChart"), competencyOptions);
                competencyChart.render();

                // 6. Section Performance Trend Chart
                const sectionTrendColors = ['#10B981', '#3B82F6', '#6B7280', '#F59E0B', '#FCD34D', '#EF4444'];
                const sectionTrendValues = (sectionPerformanceTrendData.series || [])
                    .flatMap(s => (s && Array.isArray(s.data)) ? s.data : [])
                    .map(v => Number(v))
                    .filter(v => Number.isFinite(v));
                const sectionTrendMin = sectionTrendValues.length ? Math.max(0, Math.floor(Math.min(...sectionTrendValues) - 5)) : 0;
                const sectionTrendMax = sectionTrendValues.length ? Math.min(100, Math.ceil(Math.max(...sectionTrendValues) + 5)) : 100;
                const sectionPerformanceOptions = {
                    series: sectionPerformanceTrendData.series || [],
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false
                            }
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: sectionTrendColors,
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 4,
                        strokeWidth: 2,
                        strokeColors: '#fff',
                        hover: {
                            size: 6
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: sectionPerformanceTrendData.labels || ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        min: sectionTrendMin,
                        max: sectionTrendMax,
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            formatter: function(val) {
                                return Number(val).toFixed(1) + '%';
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        fontSize: '12px',
                        markers: {
                            width: 10,
                            height: 10,
                            radius: 2
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 5
                        }
                    },
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: false
                            }
                        }
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                return val.toFixed(1) + '%';
                            }
                        }
                    }
                };

                if (sectionPerformanceChart) {
                    sectionPerformanceChart.destroy();
                }
                sectionPerformanceChart = new ApexCharts(document.querySelector("#sectionPerformanceChart"), sectionPerformanceOptions);
                sectionPerformanceChart.render();

                const legendEl = document.getElementById('sectionPerformanceLegend');
                if (legendEl) {
                    legendEl.innerHTML = '';
                    (sectionPerformanceTrendData.series || []).forEach((s, idx) => {
                        const item = document.createElement('div');
                        item.className = 'flex items-center gap-2 text-xs text-gray-700';
                        const dot = document.createElement('span');
                        dot.className = 'inline-block w-2.5 h-2.5 rounded-full';
                        dot.style.backgroundColor = sectionTrendColors[idx % sectionTrendColors.length];
                        const label = document.createElement('span');
                        label.textContent = s.name || 'Section';
                        item.appendChild(dot);
                        item.appendChild(label);
                        legendEl.appendChild(item);
                    });
                }
            }

            // Initialize charts with default month
            initializeCharts();

            const monthSelector = document.getElementById('monthSelector');
            if (monthSelector) {
                monthSelector.addEventListener('change', function() {
                    const selectedMonth = this.value;
                    initializeCharts(selectedMonth);
                });
            }
        });
    </script>
@endpush
