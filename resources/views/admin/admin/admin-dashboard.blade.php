@extends('admin.admin.layouts.app')

@section('title', 'AralSipnayan')


@section('breadcrumb', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Admin Dashboard</h2>
        <p class="text-gray-600 mt-1">Welcome back! Here's an overview of your learning platform.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Registered Teachers -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Registered Teachers</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $summaryCards['teachers'] ?? 0 }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+3 from last month</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 7%</span>
                    </div>
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
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+12% from last month</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 12%</span>
                    </div>
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
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+1 from last month</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 14%</span>
                    </div>
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
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-500">+8 new this week</span>
                        <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑ 5%</span>
                    </div>
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
            <a href="#"
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
                <div class="flex space-x-4">
                    <!-- Month Selector -->
                    <div>
                        <label for="monthSelector" class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                        <select id="monthSelector" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="january">January</option>
                            <option value="february">February</option>
                            <option value="march">March</option>
                            <option value="april">April</option>
                            <option value="may">May</option>
                            <option value="june">June</option>
                            <option value="july">July</option>
                            <option value="august">August</option>
                            <option value="september">September</option>
                            <option value="october">October</option>
                            <option value="november">November</option>
                            <option value="december">December</option>
                        </select>
                    </div>
                </div>
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
                            <p class="text-2xl font-bold text-blue-600">{{ $averageScores['overall'] ?? 0 }}%</p>
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
                            <p class="text-2xl font-bold text-green-600">{{ $completionRate['completion_rate'] ?? 0 }}%</p>
                            <p class="text-xs text-gray-500">Completion Rate</p>
                        </div>
                    </div>
                    <div id="completionRateChart"></div>
                    <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Completed</p>
                            <p class="text-lg font-semibold text-green-600">{{ $completionRate['completed'] ?? 0 }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">In Progress</p>
                            <p class="text-lg font-semibold text-yellow-600">{{ $completionRate['in_progress'] ?? 0 }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-500">Not Started</p>
                            <p class="text-lg font-semibold text-red-600">{{ $completionRate['not_started'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Most Missed Topics -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="mb-4">
                        <h4 class="text-base font-semibold text-gray-900">Most Missed Topics</h4>
                        <p class="text-sm text-gray-600 mt-1">Topics with lowest accuracy rates</p>
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
                                    <p class="text-lg font-bold text-green-900 mt-1">{{ $sectionInsights['top']['section'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-green-700 mt-1">Average Score: {{ $sectionInsights['top']['avg'] ?? 0 }}%</p>
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
                                    <p class="text-lg font-bold text-red-900 mt-1">{{ $sectionInsights['needs_attention']['section'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-red-700 mt-1">Average Score: {{ $sectionInsights['needs_attention']['avg'] ?? 0 }}%</p>
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
                                    <p class="text-lg font-bold text-blue-900 mt-1">{{ $sectionInsights['most_improved']['section'] ?? 'N/A' }}</p>
                                    <p class="text-sm text-blue-700 mt-1">Growth: {{ $sectionInsights['most_improved']['growth'] ?? 0 }}%</p>
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
                                    <p class="text-sm text-yellow-900 mt-1 font-medium">{{ $sectionInsights['insight'] ?? 'No insight available' }}</p>
                                    <p class="text-xs text-yellow-700 mt-2">{{ $sectionInsights['insight_detail'] ?? '' }}</p>
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
                                @forelse($sectionStatsSummary ?? [] as $row)
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
                $averageScoresData = $averageScores ?? ['sections' => [], 'scores' => [], 'overall' => 0];
                $completionRateData = $completionRate ?? [
                    'completed' => 0,
                    'in_progress' => 0,
                    'not_started' => 0,
                    'completion_rate' => 0,
                    'total_students' => 0,
                ];
                $mostMissedTopicsData = $mostMissedTopics ?? ['topics' => [], 'accuracy' => []];
                $performanceByCompetencyData = $performanceByCompetency ?? ['sections' => [], 'series' => []];
                $sectionPerformanceTrendData = $sectionPerformanceTrend ?? ['labels' => ['Week 1','Week 2','Week 3','Week 4','Week 5','Week 6'], 'series' => []];
                $platformGrowthData = $platformGrowth ?? ['labels' => [], 'students' => [], 'assessments' => []];
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
                        min: 65,
                        max: 90,
                        labels: {
                            style: {
                                colors: '#6B7280',
                                fontSize: '12px'
                            },
                            formatter: function(val) {
                                return val.toFixed(0) + '%';
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

            // Month selector event listener
            document.getElementById('monthSelector').addEventListener('change', function() {
                const selectedMonth = this.value;
                initializeCharts(selectedMonth);
            });
        });
    </script>
@endpush
