@extends('admin.admin.layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard Overview')

@section('content')
    <!-- Dashboard Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Students Card -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">1,250</p>
                            <span class="ml-2 text-sm text-green-600 font-medium">↑ 12%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Sections Card -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Active Sections</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">42</p>
                            <span class="ml-2 text-sm text-green-600 font-medium">↑ 3%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Assessments Card -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Assessments</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">186</p>
                            <span class="ml-2 text-sm text-red-600 font-medium">↓ 2%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Score Card -->
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">Average Score</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">87.3%</p>
                            <span class="ml-2 text-sm text-green-600 font-medium">↑ 5.2%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Performance Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Performance Overview</h3>
                <select class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>Last 3 months</option>
                </select>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded">
                <p class="text-gray-500">Chart placeholder - Performance data visualization</p>
            </div>
        </div>

        <!-- Assessment Results -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Assessment Results</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Mathematics Quiz #5</p>
                        <p class="text-sm text-gray-500">Grade 10 - Section A</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full">85% Avg</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Science Module Test</p>
                        <p class="text-sm text-gray-500">Grade 10 - Section B</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-full">72% Avg</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">English Essay Review</p>
                        <p class="text-sm text-gray-500">Grade 10 - Section C</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full">91% Avg</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">New assessment created: <span class="font-medium">Physics Unit Test</span></p>
                        <p class="text-xs text-gray-500 mt-1">2 hours ago by Prof. Smith</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-green-600 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">15 students completed <span class="font-medium">Chemistry Quiz #3</span></p>
                        <p class="text-xs text-gray-500 mt-1">4 hours ago</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-yellow-600 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">New student enrolled: <span class="font-medium">Maria Garcia</span></p>
                        <p class="text-xs text-gray-500 mt-1">6 hours ago</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-purple-600 rounded-full mt-2"></div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Section B schedule updated for <span class="font-medium">next week</span></p>
                        <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="#" class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Assessment
                </a>
                <a href="#" class="block w-full px-4 py-3 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Add Student
                </a>
                <a href="#" class="block w-full px-4 py-3 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6"></path>
                    </svg>
                    Generate Report
                </a>
                <a href="#" class="block w-full px-4 py-3 border-2 border-gray-300 text-gray-700 text-center rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Add any page-specific JavaScript here
    console.log('Dashboard loaded successfully');
</script>
@endpush