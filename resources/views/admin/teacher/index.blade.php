@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
    <div>
        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Teacher Dashboard</h2>
            <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with your classes.</p>
        </div>


           <!-- Quick Actions Section -->
           <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Quick Actions</h3>
            <p class="text-sm text-gray-600 mb-4">Common tasks and shortcuts</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Create Assessment -->
                <a href="{{ route('teacher.assessments.create') }}"
                    class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-blue-500 hover:shadow-md transition-all duration-200 group">
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
                    class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-green-500 hover:shadow-md transition-all duration-200 group">
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
                    class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-purple-500 hover:shadow-md transition-all duration-200 group">
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
                <a href="{{ route('teacher.announcements.index') }}"
                    class="bg-white border border-gray-200 rounded-2xl p-6 hover:border-orange-500 hover:shadow-md transition-all duration-200 group">
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
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Average Accuracy by Section</h4>
                    <div class="space-y-4">
                        @forelse($sectionPerformance as $index => $section)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ $section['section'] }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ $section['averageScore'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $section['color'] }}-500 h-2 rounded-full" style="width: {{ $section['averageScore'] }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500">
                                    @if($index === 0)
                                        Highest performing
                                    @elseif($index === count($sectionPerformance) - 1)
                                        Needs improvement
                                    @else
                                        Average performance
                                    @endif
                                </span>
                                <span class="text-xs {{ $section['averageScore'] >= 80 ? 'text-green-600' : ($section['averageScore'] >= 70 ? 'text-yellow-600' : 'text-red-600') }} font-medium">
                                    @if($section['averageScore'] >= 80)
                                        +{{ round($section['averageScore'] - 80, 1) }}% above target
                                    @elseif($section['averageScore'] >= 70)
                                        {{ round($section['averageScore'] - 80, 1) }}% below target
                                    @else
                                        {{ round($section['averageScore'] - 80, 1) }}% below target
                                    @endif
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">No section data available</p>
                        </div>
                        @endforelse
                    </div>
                </div>

               <!-- Performance Accuracy Breakdown -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Performance Accuracy Breakdown</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                         <!-- Accuracy Chart + Total Students -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="flex flex-col items-center">
                                <div class="relative inline-block">
                                    <svg class="w-24 h-24" viewBox="0 0 36 36">
                                        <path d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none" stroke="#E5E7EB" stroke-width="3"
                                            stroke-dasharray="100, 100" />
                                        <path d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none" stroke="#10B981" stroke-width="3"
                                            stroke-dasharray="{{ $overallAccuracy }}, 100"
                                            stroke-linecap="round" />
                                        <text x="18" y="20.5" text-anchor="middle"
                                            fill="#111827" font-size="8" font-weight="bold">
                                            {{ number_format($overallAccuracy, 0) }}%
                                        </text>
                                    </svg>
                                </div>

                                <p class="text-sm font-medium text-gray-900 mt-2">Overall Accuracy</p>
                                <p class="text-xs text-gray-500 mb-2">Across all sections</p>

                                <!-- Total Students Count -->
                                <div class="bg-gray-100 rounded-md px-3 py-1 mt-1">
                                    <p class="text-xs font-medium text-gray-700">
                                         Total Students: {{ $totalStudents['count'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            @if($topPerformingSection)
                            <!-- Top Performing Section -->
                            <div class="bg-green-50 border border-green-100 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-green-900">Top Performing Section</p>
                                        <p class="text-xs text-green-700">{{ $topPerformingSection['section'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-green-900">
                                            {{ number_format($topPerformingSection['averageScore'], 0) }}%
                                        </p>
                                        <p class="text-xs text-green-600">Average Score</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($worstPerformingSection)
                            <!-- Worst Performing Section -->
                            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-red-900">Needs Attention</p>
                                        <p class="text-xs text-red-700">{{ $worstPerformingSection['section'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-red-900">
                                            {{ number_format($worstPerformingSection['averageScore'], 0) }}%
                                        </p>
                                        <p class="text-xs text-red-600">Average Score</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>



        <!-- Top Performers and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side (2/3 width) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Top Performers block -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Top Performers</h3>
                        <p class="text-sm text-gray-600 mb-6">Students with highest points</p>
                        @if(isset($topPerformers) && count($topPerformers))
                        <div class="space-y-3">
                            @foreach($topPerformers as $student)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-2xl">
                                <div
                                    class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-sm font-medium text-gray-700">
                                    {{ $student['rank'] }}
                                </div>
                                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                                    <img src="{{ asset($student['avatar']) }}" alt="Student" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $student['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $student['section'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">{{ $student['score'] }} pts</p>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No performance data available</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Second Top Performers block -->
                    <div>
                        <h3 class="invisible text-lg font-semibold text-gray-900 mb-2">Top Performers</h3>
                        <p class="text-sm text-gray-600 mb-6">Students with highest accuracy</p>
                        @if(isset($topAccuracyPerformers) && count($topAccuracyPerformers))
                        <div class="space-y-3">
                            @foreach($topAccuracyPerformers as $student)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-2xl">
                                <div
                                    class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full text-sm font-medium text-gray-700">
                                    {{ $student['rank'] }}
                                </div>
                                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                                    <img src="{{ asset($student['avatar']) }}" alt="Student" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $student['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $student['section'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-900">{{ number_format($student['score'], 1) }} %</p>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">No performance data available</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side (1/3 width) - Recent Activity -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Activity</h3>
                <p class="text-sm text-gray-600 mb-6">Latest updates from your classes</p>

                <div class="space-y-4">
                    @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-50 rounded-lg mt-1">
                            <span class="material-symbols-outlined text-gray-600 text-sm">{{ $activity['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection