@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
    <div>
        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Teacher Dashboard</h2>
            <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with your classes.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Students -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900">247</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">+12% from last month</span>
                            <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑
                                12%</span>
                        </div>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <span class="material-symbols-outlined text-blue-600">school</span>
                    </div>
                </div>
            </div>

            <!-- Active Assessments -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Assessments</p>
                        <p class="text-3xl font-bold text-gray-900">18</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">+3 from last month</span>
                            <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑
                                3</span>
                        </div>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg">
                        <span class="material-symbols-outlined text-green-600">assignment</span>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Average Score</p>
                        <p class="text-3xl font-bold text-gray-900">78.5%</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">+5.2% from last month</span>
                            <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↑
                                5.2%</span>
                        </div>
                    </div>
                    <div class="p-2 bg-yellow-50 rounded-lg">
                        <span class="material-symbols-outlined text-yellow-600">trending_up</span>
                    </div>
                </div>
            </div>

            <!-- Response Time -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Response Time</p>
                        <p class="text-3xl font-bold text-gray-900">2.3 min</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">-0.5 min from last month</span>
                            <span class="ml-2 text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded">↓
                                0.5</span>
                        </div>
                    </div>
                    <div class="p-2 bg-purple-50 rounded-lg">
                        <span class="material-symbols-outlined text-purple-600">timer</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Quick Actions</h3>
            <p class="text-sm text-gray-600 mb-4">Common tasks and shortcuts</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Create Assessment -->
                <a href="{{ route('teacher.assessments.create') }}"
                    class="bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start space-x-4">
                        <div class="p-2 bg-gray-50 rounded-lg group-hover:bg-blue-50 transition-colors">
                            <span
                                class="material-symbols-outlined text-gray-600 group-hover:text-blue-600 transition-colors">add</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Create Assessment</h4>
                            <p class="text-sm text-gray-500 mt-1">Build a new assessment</p>
                        </div>
                    </div>
                </a>

                <!-- View Analytics -->
                <a href="{{ route('teacher.analytics') }}"
                    class="bg-white border border-gray-200 rounded-lg p-6 hover:border-green-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start space-x-4">
                        <div class="p-2 bg-gray-50 rounded-lg group-hover:bg-green-50 transition-colors">
                            <span
                                class="material-symbols-outlined text-gray-600 group-hover:text-green-600 transition-colors">analytics</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">View Analytics</h4>
                            <p class="text-sm text-gray-500 mt-1">Check performance</p>
                        </div>
                    </div>
                </a>

                <!-- Manage Sections -->
                <a href="{{ route('teacher.sections') }}"
                    class="bg-white border border-gray-200 rounded-lg p-6 hover:border-purple-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start space-x-4">
                        <div class="p-2 bg-gray-50 rounded-lg group-hover:bg-purple-50 transition-colors">
                            <span
                                class="material-symbols-outlined text-gray-600 group-hover:text-purple-600 transition-colors">groups</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Manage Sections</h4>
                            <p class="text-sm text-gray-500 mt-1">Organize classes</p>
                        </div>
                    </div>
                </a>

                <!-- Send Announcement -->
                <a href="#"
                    class="bg-white border border-gray-200 rounded-lg p-6 hover:border-orange-500 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start space-x-4">
                        <div class="p-2 bg-gray-50 rounded-lg group-hover:bg-orange-50 transition-colors">
                            <span
                                class="material-symbols-outlined text-gray-600 group-hover:text-orange-600 transition-colors">campaign</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Send Announcement</h4>
                            <p class="text-sm text-gray-500 mt-1">Communicate with students</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Analytics Section -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Section Performance Analytics</h3>
            <p class="text-sm text-gray-600 mb-4">Overview of section performance and accuracy</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Section Average Scores -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Average Scores by Section</h4>
                    <div class="space-y-4">
                        <!-- Section A -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Section A - Grade 7</span>
                                <span class="text-sm font-bold text-gray-900">85.2%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 85.2%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">Highest performing</span>
                                <span class="text-xs text-green-600 font-medium">+6.7% above average</span>
                            </div>
                        </div>

                        <!-- Section B -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Section B - Grade 8</span>
                                <span class="text-sm font-bold text-gray-900">78.5%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 78.5%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">Average performance</span>
                                <span class="text-xs text-gray-500">+0.0% from average</span>
                            </div>
                        </div>

                        <!-- Section C -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Section C - Grade 9</span>
                                <span class="text-sm font-bold text-gray-900">72.1%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 72.1%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">Needs improvement</span>
                                <span class="text-xs text-red-600 font-medium">-6.4% below average</span>
                            </div>
                        </div>

                        <!-- Section D -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Section D - Grade 10</span>
                                <span class="text-sm font-bold text-gray-900">81.3%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: 81.3%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">Good performance</span>
                                <span class="text-xs text-green-600 font-medium">+2.8% above average</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accuracy and Completion Rates -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Accuracy & Completion Rates</h4>
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Accuracy Chart -->
                        <div class="text-center">
                            <div class="relative inline-block">
                                <svg class="w-24 h-24" viewBox="0 0 36 36">
                                    <path d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB"
                                        stroke-width="3" stroke-dasharray="100, 100" />
                                    <path d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10B981"
                                        stroke-width="3" stroke-dasharray="88, 100" />
                                    <text x="18" y="20.5" text-anchor="middle" fill="#111827" font-size="8"
                                        font-weight="bold">88%</text>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mt-2">Overall Accuracy</p>
                            <p class="text-xs text-gray-500">Across all sections</p>
                        </div>

                        <!-- Completion Chart -->
                        <div class="text-center">
                            <div class="relative inline-block">
                                <svg class="w-24 h-24" viewBox="0 0 36 36">
                                    <path d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#E5E7EB"
                                        stroke-width="3" stroke-dasharray="100, 100" />
                                    <path d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#3B82F6"
                                        stroke-width="3" stroke-dasharray="92, 100" />
                                    <text x="18" y="20.5" text-anchor="middle" fill="#111827" font-size="8"
                                        font-weight="bold">92%</text>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mt-2">Assessment Completion</p>
                            <p class="text-xs text-gray-500">Rate across sections</p>
                        </div>

                        <!-- Top Performing Section -->
                        <div class="col-span-2 bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-green-900">Top Performing Section</p>
                                    <p class="text-xs text-green-700">Section A - Grade 7</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-900">85.2%</p>
                                    <p class="text-xs text-green-600">Average Score</p>
                                </div>
                            </div>
                        </div>

                        <!-- Most Improved -->
                        <div class="col-span-2 bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-blue-900">Most Improved</p>
                                    <p class="text-xs text-blue-700">Section D - Grade 10</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-900">+12.5%</p>
                                    <p class="text-xs text-blue-600">Since last month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Top Performers and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Performers - Larger container (2/3 width) -->
            <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Top Performers</h3>
                <p class="text-sm text-gray-600 mb-6">Students with highest recent scores</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Student Item -->
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div
                            class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-sm font-medium text-gray-700">
                            1</div>
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            <img src="{{ asset('images/profile/avatar1.png') }}" alt="Student"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Maria Santos</p>
                            <p class="text-xs text-gray-500 truncate">Section A</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">95%</p>
                            <p class="text-xs text-green-600 font-medium">+8%</p>
                        </div>
                    </div>

                    <!-- Student Item -->
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div
                            class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-sm font-medium text-gray-700">
                            2</div>
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            <img src="{{ asset('images/profile/avatar2.png') }}" alt="Student"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">John Doe</p>
                            <p class="text-xs text-gray-500 truncate">Section B</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">92%</p>
                            <p class="text-xs text-green-600 font-medium">+12%</p>
                        </div>
                    </div>

                    <!-- Student Item -->
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div
                            class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-sm font-medium text-gray-700">
                            3</div>
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            <img src="{{ asset('images/profile/avatar3.png') }}" alt="Student"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Sarah Wilson</p>
                            <p class="text-xs text-gray-500 truncate">Section A</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">89%</p>
                            <p class="text-xs text-green-600 font-medium">+5%</p>
                        </div>
                    </div>

                    <!-- Student Item -->
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div
                            class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-sm font-medium text-gray-700">
                            4</div>
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                            <img src="{{ asset('images/profile/avatar4.png') }}" alt="Student"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Mike Johnson</p>
                            <p class="text-xs text-gray-500 truncate">Section C</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">87%</p>
                            <p class="text-xs text-green-600 font-medium">+15%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity - Smaller container (1/3 width) -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Activity</h3>
                <p class="text-sm text-gray-600 mb-6">Latest updates from your classes</p>

                <div class="space-y-4">
                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <span class="material-symbols-outlined text-gray-600 text-sm">quiz</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Algebra Basics Quiz completed by Section A
                            </p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <span class="material-symbols-outlined text-gray-600 text-sm">check_circle</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Maria Santos achieved 95% in Geometry
                                Assessment</p>
                            <p class="text-xs text-gray-500">4 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <span class="material-symbols-outlined text-gray-600 text-sm">group_add</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">New section 'Advanced Math' created</p>
                            <p class="text-xs text-gray-500">1 day ago</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <span class="material-symbols-outlined text-gray-600 text-sm">assignment</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">Statistics Quiz assigned to Section B</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection