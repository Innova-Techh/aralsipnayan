@props(['masteryProgress'])

@php
    $stats = $masteryProgress['stats'] ?? ['current_week' => 0, 'best_week' => 0, 'growth' => 0, 'growth_sign' => ''];
    $nowWeekStart = \Carbon\Carbon::now()->startOfWeek();
    $currentWeekLabel = $nowWeekStart->format('M d') . ' - ' . $nowWeekStart->copy()->endOfWeek()->format('M d');

    // Temp UI fallback (used for chart + empty stats cards)
    $fallbackMastery = [66, 68, 70, 72, 71, 74];
    $fallbackCurrentWeek = $fallbackMastery[count($fallbackMastery) - 1];
    $fallbackBestWeek = max($fallbackMastery);
    $fallbackGrowthSigned = $fallbackMastery[count($fallbackMastery) - 1] - $fallbackMastery[count($fallbackMastery) - 2];
    $fallbackGrowthSign = $fallbackGrowthSigned > 0 ? '+' : ($fallbackGrowthSigned < 0 ? '-' : '');
    $fallbackGrowthAbs = abs($fallbackGrowthSigned);

    $cardCurrentWeek = (is_numeric($stats['current_week'] ?? null) && (float) $stats['current_week'] > 0)
        ? $stats['current_week']
        : $fallbackCurrentWeek;

    $cardBestWeek = (is_numeric($stats['best_week'] ?? null) && (float) $stats['best_week'] > 0)
        ? $stats['best_week']
        : $fallbackBestWeek;

    $growthSigned = (is_numeric($stats['growth'] ?? null) && (float) $stats['growth'] != 0.0)
        ? (float) $stats['growth']
        : (float) $fallbackGrowthSigned;

    $cardGrowthSign = (string) ($stats['growth_sign'] ?? '');
    if ($cardGrowthSign === '') {
        $cardGrowthSign = $growthSigned > 0 ? '+' : ($growthSigned < 0 ? '-' : '');
    }

    $cardGrowthAbs = abs($growthSigned);

    $fallbackWeeks = [];
    $fallbackStart = $nowWeekStart->copy()->subWeeks(5);
    for ($i = 0; $i < 6; $i++) {
        $weekStart = $fallbackStart->copy()->addWeeks($i);
        $weeksSince = $nowWeekStart->diffInWeeks($weekStart);
        if ($weeksSince === 0) {
            $fallbackWeeks[] = 'This Week';
        } elseif ($weeksSince === 1) {
            $fallbackWeeks[] = 'Last Week';
        } else {
            $fallbackWeeks[] = $weekStart->format('M d');
        }
    }
@endphp

<!-- Mastery Progress Chart Component (Teacher View) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center mb-6">
        <div class="bg-blue-600 rounded-lg p-3 mr-3">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Weekly Mastery Progress</h3>
            <p class="text-sm text-gray-600 mt-1">Student's learning journey over the past 6 weeks</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <!-- Current Week -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-700">{{ $cardCurrentWeek }}%</div>
            <div class="text-xs font-medium text-blue-600 mt-1">{{ $currentWeekLabel }}</div>
        </div>

        <!-- Best Week -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-amber-700">{{ $cardBestWeek }}%</div>
            <div class="text-xs font-medium text-amber-600 mt-1">Best Week</div>
        </div>

        <!-- Growth -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-700">{{ $cardGrowthSign }}{{ $cardGrowthAbs }}%</div>
            <div class="text-xs font-medium text-green-600 mt-1">Growth</div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
        <div id="masteryProgressChartTeacher"></div>
    </div>

    <!-- Performance Insight -->
    @if($stats['current_week'] > 0)
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-blue-600 text-xl mt-0.5">info</span>
                <div>
                    <p class="text-sm font-medium text-gray-900">Performance Insight</p>
                    <p class="text-xs text-gray-600 mt-1">
                        @if($stats['growth'] > 10)
                            Student shows strong improvement with {{ $stats['growth'] }}% growth. Continue with current learning pace.
                        @elseif($stats['growth'] > 0)
                            Student is making steady progress. Consider providing additional practice materials.
                        @elseif($stats['growth'] < 0)
                            Student's performance has decreased by {{ abs($stats['growth']) }}%. May need intervention or additional support.
                        @else
                            Student's performance is stable. Monitor for consistent engagement.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if chart container exists
        if (!document.querySelector("#masteryProgressChartTeacher")) {
            return;
        }

        // Dynamic data from backend
        let masteryData = @json($masteryProgress);

        // Temporary UI data when there is no real mastery data yet
        const fallbackWeeks = @json($fallbackWeeks);
        const fallbackMastery = @json($fallbackMastery);
        const fallbackTopics = [
            { name: 'Number and Algebra', data: [62, 64, 66, 69, 68, 71] },
            { name: 'Measurement and Geometry', data: [58, 60, 63, 65, 64, 67] },
            { name: 'Data and Probability', data: [61, 63, 65, 67, 66, 70] }
        ];

        const hasSeriesData = arr => Array.isArray(arr) && arr.some(v => Number(v) > 0);

        if (!hasSeriesData(masteryData?.mastery)) {
            masteryData.mastery = fallbackMastery;
        }

        if (!Array.isArray(masteryData?.topics) || masteryData.topics.length < 3) {
            masteryData.topics = fallbackTopics;
        } else {
            masteryData.topics = masteryData.topics.map((t, i) => {
                const fallback = fallbackTopics[i];
                if (!hasSeriesData(t?.data)) {
                    return fallback;
                }
                return t;
            });
        }

        if (!Array.isArray(masteryData?.weeks) || masteryData.weeks.length !== 6) {
            masteryData.weeks = fallbackWeeks;
        }

        // Mastery Progress Chart Configuration (Teacher View - Solid Colors)
        const masteryOptions = {
            series: [{
                name: 'Overall Mastery',
                data: masteryData.mastery,
                type: 'area'
            }, {
                name: masteryData.topics[0].name,
                data: masteryData.topics[0].data,
                type: 'line'
            }, {
                name: masteryData.topics[1].name,
                data: masteryData.topics[1].data,
                type: 'line'
            }, {
                name: masteryData.topics[2].name,
                data: masteryData.topics[2].data,
                type: 'line'
            }],
            chart: {
                height: 350,
                type: 'line',
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
                fontFamily: 'Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: ['#2563EB', '#059669', '#D97706', '#DC2626'],
            stroke: {
                curve: 'smooth',
                width: [0, 3, 3, 3]
            },
            fill: {
                type: ['solid', 'solid', 'solid', 'solid'],
                opacity: [0.3, 1, 1, 1]
            },
            markers: {
                size: [0, 5, 5, 5],
                colors: ['#2563EB', '#059669', '#D97706', '#DC2626'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 7,
                    sizeOffset: 3
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: masteryData.weeks,
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                axisBorder: {
                    show: true,
                    color: '#E5E7EB'
                }
            },
            yaxis: {
                min: 0,
                max: 100,
                tickAmount: 5,
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    formatter: function(val) {
                        return val.toFixed(0) + '%';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontWeight: 500,
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
                borderColor: '#E5E7EB',
                strokeDashArray: 3,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                padding: {
                    top: 0,
                    right: 10,
                    bottom: 0,
                    left: 10
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                },
                y: {
                    formatter: function(val) {
                        return val.toFixed(1) + '% mastery';
                    }
                },
                marker: {
                    show: true
                }
            }
        };

        const masteryChart = new ApexCharts(document.querySelector("#masteryProgressChartTeacher"), masteryOptions);
        masteryChart.render();
    });
</script>
@endpush
