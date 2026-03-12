@props(['masteryProgress'])

@php
    $stats = $masteryProgress['stats'] ?? ['current_week' => 0, 'best_week' => 0, 'growth' => 0, 'growth_sign' => ''];
    $nowWeekStart = \Carbon\Carbon::now()->startOfWeek();
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

<!-- Mastery Progress Chart Component -->
<div class="bg-white rounded-3xl shadow-xl p-6 mb-6">
    <div class="flex items-center mb-6">
        <div class="bg-gradient-to-br from-purple-400 to-pink-400 rounded-2xl p-3 mr-3 shadow-lg">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
        </div>
        <div>
            <h2 class="text-2xl font-bold font-poppins text-gray-800">Weekly Mastery Progress</h2>
            <p class="text-sm text-gray-600 mt-1">Your learning journey over the past 6 weeks</p>
        </div>
    </div>

    <!-- Fun Stats Cards -->
    <div class="grid grid-cols-3 gap-3 mb-6">
        <!-- Current Week -->
        <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl p-4 text-center transform hover:scale-105 transition-transform shadow-md"
            style="box-shadow: 0 4px 0 0 #2563EB;">
            {{-- <div class="text-3xl mb-1">📊</div> --}}
            <div class="text-2xl font-bold text-blue-700">{{ $stats['current_week'] }}%</div>
            <div class="text-xs font-semibold text-blue-600">This Week</div>
        </div>

        <!-- Best Week -->
        <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-2xl p-4 text-center transform hover:scale-105 transition-transform shadow-md"
            style="box-shadow: 0 4px 0 0 #F59E0B;">
            {{-- <div class="text-3xl mb-1">🏆</div> --}}
            <div class="text-2xl font-bold text-yellow-700">{{ $stats['best_week'] }}%</div>
            <div class="text-xs font-semibold text-yellow-600">Best Week</div>
        </div>

        <!-- Growth -->
        <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-2xl p-4 text-center transform hover:scale-105 transition-transform shadow-md"
            style="box-shadow: 0 4px 0 0 #10B981;">
            {{-- <div class="text-3xl mb-1">📈</div> --}}
            <div class="text-2xl font-bold text-green-700">{{ $stats['growth_sign'] }}{{ $stats['growth'] }}%</div>
            <div class="text-xs font-semibold text-green-600">Growth</div>
        </div>
    </div>

    <!-- Chart Container -->
    <div class="bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 rounded-2xl p-6 shadow-inner">
        <div id="masteryProgressChart"></div>
    </div>

    {{-- <!-- Fun Progress Indicator -->
    <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-bold text-gray-700">🌟 Overall Progress</span>
            <span class="text-sm font-bold text-purple-600">85/100</span>
        </div>
        <div class="relative w-full h-4 bg-gray-200 rounded-full overflow-hidden shadow-inner">
            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 rounded-full transition-all duration-1000 ease-out"
                style="width: 85%; box-shadow: 0 0 10px rgba(168, 85, 247, 0.5);">
            </div>
        </div>
        <p class="text-xs text-gray-600 mt-2 text-center">🎉 You're doing amazing! Keep up the great work!</p>
    </div> --}}
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic data from backend
        let masteryData = @json($masteryProgress);

        // Temporary UI data when there is no real mastery data yet
        const fallbackWeeks = @json($fallbackWeeks);
        const fallbackMastery = [72, 75, 73, 78, 76, 80];
        const fallbackTopics = [
            { name: 'Number and Algebra', data: [68, 70, 69, 74, 72, 77] },
            { name: 'Measurement and Geometry', data: [65, 68, 67, 72, 70, 74] },
            { name: 'Data and Probability', data: [70, 73, 71, 76, 74, 79] }
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

        // Mastery Progress Chart Configuration
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
                fontFamily: 'Poppins, sans-serif',
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
            colors: ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B'],
            stroke: {
                curve: 'smooth',
                width: [0, 3, 3, 3]
            },
            fill: {
                type: ['gradient', 'solid', 'solid', 'solid'],
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#EC4899'],
                    inverseColors: false,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            },
            markers: {
                size: [0, 5, 5, 5],
                colors: ['#8B5CF6', '#3B82F6', '#10B981', '#F59E0B'],
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
                        fontSize: '13px',
                        fontWeight: 600
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
                        fontSize: '13px',
                        fontWeight: 600
                    },
                    formatter: function(val) {
                        return val.toFixed(0) + '%';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '13px',
                fontWeight: 600,
                markers: {
                    width: 12,
                    height: 12,
                    radius: 3
                },
                itemMargin: {
                    horizontal: 12,
                    vertical: 5
                }
            },
            grid: {
                borderColor: '#E5E7EB',
                strokeDashArray: 4,
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
                    fontSize: '13px',
                    fontFamily: 'Poppins, sans-serif'
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

        const masteryChart = new ApexCharts(document.querySelector("#masteryProgressChart"), masteryOptions);
        masteryChart.render();
    });
</script>
@endpush
