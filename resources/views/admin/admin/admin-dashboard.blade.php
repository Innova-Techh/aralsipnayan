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
            <a href="#"
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
            <a href="#"
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
            <a href="#"
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

    <!-- Platform Growth and Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Platform Growth Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Platform Growth</h3>
            <p class="text-sm text-gray-600 mb-6">Monthly active students and assessments taken</p>

            <!-- Chart Container -->
            <div class="relative h-64">
                <canvas id="growthChart"></canvas>
            </div>

            <!-- Legend -->
            <div class="flex items-center justify-center mt-4 space-x-6">
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-orange-400 rounded mr-2"></span>
                    <span class="text-sm text-gray-600">Assessments</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded mr-2"></span>
                    <span class="text-sm text-gray-600">Students</span>
                </div>
            </div>
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