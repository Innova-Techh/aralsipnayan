@extends('admin.admin.layouts.app')

@section('title', 'Dashboard')


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
                    <p class="text-3xl font-bold text-gray-900">45</p>
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
                    <p class="text-3xl font-bold text-gray-900">1,950</p>
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
                    <p class="text-3xl font-bold text-gray-900">8</p>
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
                    <p class="text-3xl font-bold text-gray-900">342</p>
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
                        <p class="text-sm text-gray-500 mt-1">8 total admins</p>
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
                        <p class="text-sm text-gray-500 mt-1">45 registered teachers</p>
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
                        <p class="text-sm text-gray-500 mt-1">1,950 active students</p>
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
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Performance Analytics Overview</h3>
        <p class="text-sm text-gray-600 mb-6">Student performance metrics and insights</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Average Student Scores by Grade Level -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-base font-semibold text-gray-900">Average Scores by Grade Level</h4>
                        <p class="text-sm text-gray-600 mt-1">Performance across different grades</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-blue-600">78.5%</p>
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
                        <p class="text-2xl font-bold text-green-600">85.3%</p>
                        <p class="text-xs text-gray-500">Completion Rate</p>
                    </div>
                </div>
                <div id="completionRateChart"></div>
                <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Completed</p>
                        <p class="text-lg font-semibold text-green-600">1,663</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">In Progress</p>
                        <p class="text-lg font-semibold text-yellow-600">198</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500">Not Started</p>
                        <p class="text-lg font-semibold text-red-600">89</p>
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

            <!-- Improvement Trends Over Time -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="mb-4">
                    <h4 class="text-base font-semibold text-gray-900">Improvement Trends</h4>
                    <p class="text-sm text-gray-600 mt-1">6-month performance trajectory</p>
                </div>
                <div id="improvementTrendsChart"></div>
                <div class="mt-4 grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Avg. Monthly Growth</p>
                        <p class="text-lg font-semibold text-green-600">+3.2%</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Best Performing Month</p>
                        <p class="text-lg font-semibold text-blue-600">October</p>
                    </div>
                </div>
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
                                <p class="text-lg font-bold text-green-900 mt-1">Grade 5-A</p>
                                <p class="text-sm text-green-700 mt-1">Average Score: 86.2%</p>
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
                                <p class="text-lg font-bold text-red-900 mt-1">Grade 3-B</p>
                                <p class="text-sm text-red-700 mt-1">Average Score: 68.5%</p>
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
                                <p class="text-lg font-bold text-blue-900 mt-1">Grade 4-C</p>
                                <p class="text-sm text-blue-700 mt-1">Growth: +12.3%</p>
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
                                <p class="text-sm text-yellow-900 mt-1 font-medium">Higher grades show better performance in Algebra topics</p>
                                <p class="text-xs text-yellow-700 mt-2">Consider curriculum adjustment for lower grades</p>
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
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Grade 5-A</td>
                                <td class="px-4 py-3 text-sm text-gray-600">32</td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-600">86.2%</td>
                                <td class="px-4 py-3 text-sm text-gray-600">94%</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                                        <span class="font-medium">+5.2%</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Excellent</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Grade 4-C</td>
                                <td class="px-4 py-3 text-sm text-gray-600">28</td>
                                <td class="px-4 py-3 text-sm font-semibold text-blue-600">81.7%</td>
                                <td class="px-4 py-3 text-sm text-gray-600">89%</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                                        <span class="font-medium">+12.3%</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Good</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Grade 6-B</td>
                                <td class="px-4 py-3 text-sm text-gray-600">30</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700">78.4%</td>
                                <td class="px-4 py-3 text-sm text-gray-600">87%</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                                        <span class="font-medium">+2.1%</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Average</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Grade 2-A</td>
                                <td class="px-4 py-3 text-sm text-gray-600">25</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700">75.9%</td>
                                <td class="px-4 py-3 text-sm text-gray-600">82%</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-gray-600">
                                        <i class="fas fa-minus text-xs mr-1"></i>
                                        <span class="font-medium">+0.3%</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Average</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Grade 1-C</td>
                                <td class="px-4 py-3 text-sm text-gray-600">22</td>
                                <td class="px-4 py-3 text-sm font-semibold text-yellow-600">71.3%</td>
                                <td class="px-4 py-3 text-sm text-gray-600">78%</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-red-600">
                                        <i class="fas fa-arrow-down text-xs mr-1"></i>
                                        <span class="font-medium">-1.8%</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Below Avg</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Grade 3-B</td>
                                <td class="px-4 py-3 text-sm text-gray-600">27</td>
                                <td class="px-4 py-3 text-sm font-semibold text-red-600">68.5%</td>
                                <td class="px-4 py-3 text-sm text-gray-600">74%</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center text-red-600">
                                        <i class="fas fa-arrow-down text-xs mr-1"></i>
                                        <span class="font-medium">-4.2%</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Needs Help</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Growth and Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Platform Growth Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Platform Growth</h3>
            <p class="text-sm text-gray-600 mb-6">Monthly active students and assessments taken</p>

            <!-- Chart Container -->
            <div id="growthChart"></div>
        </div>

        <!-- Recent Activities -->
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
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Platform Growth Chart
            const growthOptions = {
                series: [{
                    name: 'Assessments',
                    data: [245, 268, 289, 312, 328, 342]
                }, {
                    name: 'Students',
                    data: [1650, 1720, 1805, 1860, 1920, 1950]
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
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
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
            const growthChart = new ApexCharts(document.querySelector("#growthChart"), growthOptions);
            growthChart.render();

            // 2. Average Scores by Grade Level Chart
            const scoresOptions = {
                series: [{
                    name: 'Average Score',
                    data: [82, 79, 76, 78, 75, 81]
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
                    categories: ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'],
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
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
            const scoresChart = new ApexCharts(document.querySelector("#averageScoresChart"), scoresOptions);
            scoresChart.render();

            // 3. Assessment Completion Rate Chart
            const completionOptions = {
                series: [1663, 198, 89],
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
                            const total = 1663 + 198 + 89;
                            const percentage = ((val / total) * 100).toFixed(1);
                            return val + ' (' + percentage + '%)';
                        }
                    }
                }
            };
            const completionChart = new ApexCharts(document.querySelector("#completionRateChart"), completionOptions);
            completionChart.render();

            // 4. Most Missed Topics Chart
            const missedTopicsOptions = {
                series: [{
                    name: 'Accuracy Rate',
                    data: [52, 58, 65, 71, 76]
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
                    categories: ['Fractions & Decimals', 'Algebraic Expressions', 'Geometry (Angles)', 'Data Interpretation', 'Word Problems'],
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
            const missedTopicsChart = new ApexCharts(document.querySelector("#missedTopicsChart"), missedTopicsOptions);
            missedTopicsChart.render();

            // 5. Improvement Trends Chart
            const improvementOptions = {
                series: [{
                    name: 'Overall Average Score',
                    data: [72.5, 74.2, 75.8, 76.5, 77.8, 78.5]
                }, {
                    name: 'Target Score',
                    data: [75, 75, 75, 75, 75, 75]
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#3B82F6', '#10B981'],
                stroke: {
                    curve: 'smooth',
                    width: [3, 2],
                    dashArray: [0, 5]
                },
                markers: {
                    size: [5, 0],
                    colors: ['#3B82F6'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: ['May', 'June', 'July', 'August', 'September', 'October'],
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    min: 70,
                    max: 85,
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
                legend: {
                    position: 'bottom',
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
            const improvementChart = new ApexCharts(document.querySelector("#improvementTrendsChart"), improvementOptions);
            improvementChart.render();

            // 6. Section Performance Trend Chart
            const sectionPerformanceOptions = {
                series: [{
                    name: 'Grade 5-A',
                    data: [80.5, 82.1, 83.7, 84.9, 85.5, 86.2]
                }, {
                    name: 'Grade 4-C',
                    data: [69.4, 72.8, 75.2, 77.5, 79.8, 81.7]
                }, {
                    name: 'Grade 6-B',
                    data: [76.3, 76.8, 77.2, 77.5, 78.0, 78.4]
                }, {
                    name: 'Grade 2-A',
                    data: [75.6, 75.4, 75.8, 75.9, 75.7, 75.9]
                }, {
                    name: 'Grade 1-C',
                    data: [73.1, 72.8, 71.9, 71.5, 71.0, 71.3]
                }, {
                    name: 'Grade 3-B',
                    data: [72.7, 71.5, 70.2, 69.8, 68.9, 68.5]
                }],
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
                colors: ['#10B981', '#3B82F6', '#6B7280', '#F59E0B', '#FCD34D', '#EF4444'],
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
                    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
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
            const sectionPerformanceChart = new ApexCharts(document.querySelector("#sectionPerformanceChart"), sectionPerformanceOptions);
            sectionPerformanceChart.render();
        });
    </script>
@endpush