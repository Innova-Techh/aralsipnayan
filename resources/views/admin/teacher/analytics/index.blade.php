@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
    <div>
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
            <p class="text-gray-600 mt-1">Track student performance and assessment insights</p>
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-blue-600 mr-2">info</span>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Your Assigned Sections</p>
                        <p class="text-sm text-blue-700">
                            @if(count($teacherSections) > 0)
                                You handle:
                                @foreach($teacherSections as $index => $section)
                                    <strong>Section {{ $section }}</strong>@if($index < count($teacherSections) - 1), @endif
                                @endforeach
                            @else
                                No sections assigned yet.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <span class="material-symbols-outlined text-blue-600">trending_up</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Average Accuracy</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['avg_accuracy'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <span class="material-symbols-outlined text-green-600">assignment_turned_in</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['completion_rate'] ?? 0 }}%</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <span class="material-symbols-outlined text-yellow-600">assessment</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Assessments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_assessments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <span class="material-symbols-outlined text-purple-600">people</span>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_students'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Comparison Charts -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <span class="material-symbols-outlined text-indigo-600 mr-3 text-3xl">bar_chart</span>
                    Section Performance Comparison
                </h3>
                <p class="text-sm text-gray-600 mt-2 ml-12">Compare metrics across all your assigned sections</p>
            </div>

            <!-- Metric Selector -->
            <div class="mb-8 flex flex-wrap gap-3">
                <button onclick="showChart('scores')" id="btn-scores"
                    class="metric-btn active group px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-105 bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md hover:shadow-xl">
                    <span class="flex items-center">
                        <span class="material-symbols-outlined mr-2 text-lg">workspace_premium</span>
                        Average Scores
                    </span>
                </button>
                <button onclick="showChart('accuracy')" id="btn-accuracy"
                    class="metric-btn group px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-105 bg-white text-gray-700 shadow-sm hover:shadow-md border border-gray-200">
                    <span class="flex items-center">
                        <span class="material-symbols-outlined mr-2 text-lg">verified</span>
                        Average Accuracy
                    </span>
                </button>
                <button onclick="showChart('time')" id="btn-time"
                    class="metric-btn group px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-105 bg-white text-gray-700 shadow-sm hover:shadow-md border border-gray-200">
                    <span class="flex items-center">
                        <span class="material-symbols-outlined mr-2 text-lg">schedule</span>
                        Average Time Taken
                    </span>
                </button>
            </div>

            <!-- Charts Container -->
            <div class="relative bg-white rounded-xl p-6 shadow-inner" style="height: 450px;">
                <!-- Average Scores Chart -->
                <div id="chart-scores" class="chart-container">
                    <canvas id="scoresChart"></canvas>
                </div>

                <!-- Average Accuracy Chart -->
                <div id="chart-accuracy" class="chart-container hidden">
                    <canvas id="accuracyChart"></canvas>
                </div>

                <!-- Average Time Chart -->
                <div id="chart-time" class="chart-container hidden">
                    <canvas id="timeChart"></canvas>
                </div>
            </div>

            <!-- Legend Info -->
            <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
                <span class="material-symbols-outlined text-lg mr-2">info</span>
                <span>Hover over bars to see detailed information</span>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Performance Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trends</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <p class="text-gray-500">Chart placeholder</p>
                </div>
            </div>

            <!-- Category Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Category</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Number & Algebra</span>
                            <span class="text-gray-900 font-medium">87%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 87%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Measurement & Geometry</span>
                            <span class="text-gray-900 font-medium">82%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 82%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Data & Probability</span>
                            <span class="text-gray-900 font-medium">79%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 79%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assessments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Assessment Results</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assessment Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Students Attempted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                                No. Of Questions Answered</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Latest Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentAssessments as $assessment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div>
                                        <div class="font-semibold">{{ $assessment->assessment_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($teacherSections as $section)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Section {{ $section }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $assessment->total_completed }} attempts
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $assessment->total_questions_answered ?? 0 }} questions
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assessment->formatted_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('teacher.analytics.assessment.details', $assessment->assessment_id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
                                        <span class="material-symbols-outlined text-sm mr-1">quiz</span>
                                        View Questions
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">assessment</span>
                                        <p>No completed assessments found</p>
                                        <p class="text-sm">Assessment results will appear here once students complete their
                                            tests</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script>
        // Sample data - Replace with actual data from your controller
        const sectionData = {
            sections: @json($teacherSections ?? []),
            avgScores: @json($sectionStats['avg_scores'] ?? []),
            avgAccuracy: @json($sectionStats['avg_accuracy'] ?? []),
            avgTime: @json($sectionStats['avg_time'] ?? [])
        };

        // Modern gradient colors for each section
        const gradientColors = [
            { start: 'rgba(99, 102, 241, 0.9)', end: 'rgba(99, 102, 241, 0.4)' },   // Indigo
            { start: 'rgba(16, 185, 129, 0.9)', end: 'rgba(16, 185, 129, 0.4)' },   // Green
            { start: 'rgba(245, 158, 11, 0.9)', end: 'rgba(245, 158, 11, 0.4)' },   // Amber
            { start: 'rgba(139, 92, 246, 0.9)', end: 'rgba(139, 92, 246, 0.4)' },   // Purple
            { start: 'rgba(236, 72, 153, 0.9)', end: 'rgba(236, 72, 153, 0.4)' },   // Pink
            { start: 'rgba(239, 68, 68, 0.9)', end: 'rgba(239, 68, 68, 0.4)' }      // Red
        ];

        const borderColors = [
            'rgb(99, 102, 241)',
            'rgb(16, 185, 129)',
            'rgb(245, 158, 11)',
            'rgb(139, 92, 246)',
            'rgb(236, 72, 153)',
            'rgb(239, 68, 68)'
        ];

        let scoresChart, accuracyChart, timeChart;

        // Create gradient for each bar
        function createGradient(ctx, chartArea, colorSet) {
            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            gradient.addColorStop(0, colorSet.end);
            gradient.addColorStop(1, colorSet.start);
            return gradient;
        }

        // Common chart options with modern styling
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 16,
                    borderRadius: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    displayColors: false,
                    callbacks: {
                        title: function (context) {
                            return context[0].label;
                        }
                    }
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        color: '#6B7280',
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        color: '#374151',
                        padding: 10
                    }
                }
            }
        };

        // Initialize charts
        function initCharts() {
            // Average Scores Chart
            const scoresCtx = document.getElementById('scoresChart').getContext('2d');
            scoresChart = new Chart(scoresCtx, {
                type: 'bar',
                data: {
                    labels: sectionData.sections.map(s => `Section ${s}`),
                    datasets: [{
                        label: 'Average Score',
                        data: sectionData.avgScores,
                        backgroundColor: function (context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;
                            if (!chartArea) return;
                            return createGradient(ctx, chartArea, gradientColors[context.dataIndex % gradientColors.length]);
                        },
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                ...commonOptions.plugins.tooltip.callbacks,
                                label: function (context) {
                                    return 'Average Score: ' + context.parsed.y.toFixed(2) + ' points';
                                }
                            }
                        }
                    },
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Score (points)',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        },
                        x: {
                            ...commonOptions.scales.x,
                            title: {
                                display: true,
                                text: 'Section',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        }
                    }
                }
            });

            // Average Accuracy Chart
            const accuracyCtx = document.getElementById('accuracyChart').getContext('2d');
            accuracyChart = new Chart(accuracyCtx, {
                type: 'bar',
                data: {
                    labels: sectionData.sections.map(s => `Section ${s}`),
                    datasets: [{
                        label: 'Average Accuracy',
                        data: sectionData.avgAccuracy,
                        backgroundColor: function (context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;
                            if (!chartArea) return;
                            return createGradient(ctx, chartArea, gradientColors[context.dataIndex % gradientColors.length]);
                        },
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                ...commonOptions.plugins.tooltip.callbacks,
                                label: function (context) {
                                    return 'Average Accuracy: ' + context.parsed.y.toFixed(2) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            max: 100,
                            ticks: {
                                ...commonOptions.scales.y.ticks,
                                callback: function (value) {
                                    return value + '%';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Accuracy (%)',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        },
                        x: {
                            ...commonOptions.scales.x,
                            title: {
                                display: true,
                                text: 'Section',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        }
                    }
                }
            });

            // Average Time Chart
            const timeCtx = document.getElementById('timeChart').getContext('2d');
            timeChart = new Chart(timeCtx, {
                type: 'bar',
                data: {
                    labels: sectionData.sections.map(s => `Section ${s}`),
                    datasets: [{
                        label: 'Average Time',
                        data: sectionData.avgTime,
                        backgroundColor: function (context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;
                            if (!chartArea) return;
                            return createGradient(ctx, chartArea, gradientColors[context.dataIndex % gradientColors.length]);
                        },
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                ...commonOptions.plugins.tooltip.callbacks,
                                label: function (context) {
                                    return 'Average Time: ' + context.parsed.y.toFixed(2) + ' minutes';
                                }
                            }
                        }
                    },
                    scales: {
                        ...commonOptions.scales,
                        y: {
                            ...commonOptions.scales.y,
                            ticks: {
                                ...commonOptions.scales.y.ticks,
                                callback: function (value) {
                                    return value.toFixed(0) + ' min';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Time (minutes)',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        },
                        x: {
                            ...commonOptions.scales.x,
                            title: {
                                display: true,
                                text: 'Section',
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        }
                    }
                }
            });
        }

        // Show specific chart with smooth transition
        function showChart(chartType) {
            // Hide all charts with fade out
            document.querySelectorAll('.chart-container').forEach(container => {
                container.style.opacity = '0';
                setTimeout(() => {
                    container.classList.add('hidden');
                }, 200);
            });

            // Remove active class from all buttons
            document.querySelectorAll('.metric-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'shadow-md');
                btn.classList.add('bg-white', 'text-gray-700', 'shadow-sm', 'border', 'border-gray-200');
            });

            // Show selected chart with fade in
            setTimeout(() => {
                const selectedChart = document.getElementById('chart-' + chartType);
                selectedChart.classList.remove('hidden');
                setTimeout(() => {
                    selectedChart.style.opacity = '1';
                }, 50);
            }, 200);

            // Add active class to selected button
            const activeBtn = document.getElementById('btn-' + chartType);
            activeBtn.classList.add('active', 'bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'shadow-md');
            activeBtn.classList.remove('bg-white', 'text-gray-700', 'shadow-sm', 'border', 'border-gray-200');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            initCharts();
        });
    </script>

    <style>
        .chart-container {
            height: 100%;
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }

        .metric-btn {
            position: relative;
            overflow: hidden;
        }

        .metric-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .metric-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Smooth scale animation on hover */
        .metric-btn:active {
            transform: scale(0.95);
        }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection