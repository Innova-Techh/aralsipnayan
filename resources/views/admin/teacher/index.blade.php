@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Dashboard
        </h1>
        <p class="text-gray-600">
            Welcome back! Here's what's happening with your classes.
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Students -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <span class="material-symbols-outlined text-blue-600">school</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900">247</p>
                    <p class="text-sm text-green-600">+12% from last month</p>
                    <p class="text-xs text-gray-500">Active students across all sections</p>
                </div>
            </div>
        </div>

        <!-- Active Assessments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <span class="material-symbols-outlined text-green-600">assignment</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Assessments</p>
                    <p class="text-2xl font-bold text-gray-900">18</p>
                    <p class="text-sm text-blue-600">+3 from last month</p>
                    <p class="text-xs text-gray-500">Currently assigned assessments</p>
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <span class="material-symbols-outlined text-yellow-600">trending_up</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Score</p>
                    <p class="text-2xl font-bold text-gray-900">78.5%</p>
                    <p class="text-sm text-green-600">+5.2% from last month</p>
                    <p class="text-xs text-gray-500">Across all recent assessments</p>
                </div>
            </div>
        </div>

        <!-- Response Time -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <span class="material-symbols-outlined text-purple-600">timer</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Response Time</p>
                    <p class="text-2xl font-bold text-gray-900">2.3 min</p>
                    <p class="text-sm text-gray-600">-0.5 min from last month</p>
                    <p class="text-xs text-gray-500">Average time per question</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <p class="text-sm text-gray-600 mb-4">Common tasks and shortcuts</p>
            <div class="space-y-3">
                <a href="{{ route('teacher.assessments.create') }}" 
                   class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-white text-sm">add</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">Create Assessment</span>
                        <p class="text-xs text-gray-600">Build a new assessment for your stu...</p>
                    </div>
                </a>
                <a href="{{ route('teacher.analytics') }}" 
                   class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-white text-sm">analytics</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">View Analytics</span>
                        <p class="text-xs text-gray-600">Check student performance and pro...</p>
                    </div>
                </a>
                <a href="{{ route('teacher.sections') }}" 
                   class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="w-8 h-8 bg-purple-600 rounded flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-white text-sm">groups</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">Manage Sections</span>
                        <p class="text-xs text-gray-600">Organize and manage your class se...</p>
                    </div>
                </a>
                <a href="#" 
                   class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <div class="w-8 h-8 bg-orange-600 rounded flex items-center justify-center mr-3">
                        <span class="material-symbols-outlined text-white text-sm">campaign</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-900">Send Announcement</span>
                        <p class="text-xs text-gray-600">Communicate with your students</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <p class="text-sm text-gray-600 mb-4">Latest updates from your classes</p>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-blue-600 text-sm">quiz</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Algebra Basics Quiz completed by Section A</p>
                        <p class="text-xs text-gray-500">2 hours ago</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Maria Santos achieved 95% in Geometry Assessment</p>
                        <p class="text-xs text-gray-500">4 hours ago</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-purple-600 text-sm">group_add</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New section 'Advanced Math' created</p>
                        <p class="text-xs text-gray-500">1 day ago</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-orange-600 text-sm">assignment</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Statistics Quiz assigned to Section B</p>
                        <p class="text-xs text-gray-500">2 days ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="lg:col-span-3 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Performers</h2>
            <p class="text-sm text-gray-600 mb-4">Students with highest recent scores</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full text-sm font-medium">1</div>
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        <img src="{{ asset('images/profile/avatar1.png') }}" alt="Student" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Maria Santos</p>
                        <p class="text-xs text-gray-500">Section A</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">95%</p>
                        <p class="text-xs text-green-600">+8%</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full text-sm font-medium">2</div>
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        <img src="{{ asset('images/profile/avatar2.png') }}" alt="Student" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">John Doe</p>
                        <p class="text-xs text-gray-500">Section B</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">92%</p>
                        <p class="text-xs text-green-600">+12%</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full text-sm font-medium">3</div>
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        <img src="{{ asset('images/profile/avatar3.png') }}" alt="Student" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Sarah Wilson</p>
                        <p class="text-xs text-gray-500">Section A</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">89%</p>
                        <p class="text-xs text-green-600">+5%</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full text-sm font-medium">4</div>
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        <img src="{{ asset('images/profile/avatar4.png') }}" alt="Student" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Mike Johnson</p>
                        <p class="text-xs text-gray-500">Section C</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">87%</p>
                        <p class="text-xs text-green-600">+15%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


