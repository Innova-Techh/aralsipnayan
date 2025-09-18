@extends('admin.teacher.layouts.app')

@section('title', 'Analytics')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
        <p class="text-gray-600 mt-1">Track student performance and assessment insights</p>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="material-symbols-outlined text-blue-600">trending_up</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Overall Performance</p>
                    <p class="text-2xl font-bold text-gray-900">85.2%</p>
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
                    <p class="text-2xl font-bold text-gray-900">92.7%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <span class="material-symbols-outlined text-yellow-600">timer</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg. Time</p>
                    <p class="text-2xl font-bold text-gray-900">3.4 min</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <span class="material-symbols-outlined text-purple-600">help</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Help Requests</p>
                    <p class="text-2xl font-bold text-gray-900">23</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Performance Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trends</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">Chart placeholder - Performance over time</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Algebra Basics Quiz</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Section A</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">89%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">100%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Sep 17, 2024</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Statistics Quiz</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Section B</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">76%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">95%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Sep 16, 2024</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection