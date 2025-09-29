@extends('layouts.user_layout')

@section('content')
    @php
        use App\Http\Controllers\RankController;

        // Initialize the RankController
        $rankController = new RankController();

        // Get user's current XP (replace with your actual user XP logic)
        $userXP = auth()->guard('student')->user()->xp ?? 460; // Example: 460 XP
        $progressInfo = $rankController->getProgressInfo($userXP);
    @endphp
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Student Profile</h1>
                <p class="text-gray-600">Track your learning journey</p>
            </div>

            <!-- Dynamic Level Card -->
            <div class="mb-5 sm:mb-6">
                <div class="text-white rounded-xl p-4 sm:p-5 shadow-inner"
                    style="background: linear-gradient(to right, #101093, #931093); box-shadow: inset 0 -4px 4px #42045C, inset 0 2px 2px #CC39F6; box-shadow: 0 6px 0 #0A0A62;">
                    <div class="flex items-center gap-4">
                        <!-- Dynamic Rank image -->
                        <div class="flex-shrink-0">
                            <img src="{{ asset('images/rank_insignia/' . $progressInfo['rank_info']['image']) }}"
                                alt="{{ $progressInfo['rank_info']['title'] }}"
                                class="w-32 h-32 sm:w-18 sm:h-18 rounded-xl object-contain">
                        </div>

                        <!-- Content area -->
                        <div class="flex-1 min-w-0 mr-2">
                            <!-- Group 1: Title and Level -->
                            <div class="mb-2">
                                <h3 class="text-xl sm:text-xl font-bold">
                                    {{ $progressInfo['rank_info']['title'] }}
                                </h3>
                                <p class="text-sm text-blue-200">Level
                                    {{ $progressInfo['current_level'] }}
                                </p>
                            </div>

                            <!-- Group 2: XP Text (standalone) -->
                            <div class="mb-2 mr-4 text-right">
                                @if($progressInfo['is_max_level'])
                                    <p class="text-xs sm:text-sm text-yellow-300 font-bold">MAX LEVEL
                                        ACHIEVED!
                                    </p>
                                @else
                                    <p class="text-xs sm:text-sm text-blue-200">
                                        {{ number_format($progressInfo['current_xp']) }} XP /
                                        {{ number_format($progressInfo['rank_info']['xp_required']) }}
                                        XP
                                    </p>
                                @endif
                            </div>

                            <!-- Group 3: Custom Progress bar and XP remaining -->
                            <div class="mr-4">
                                <div class="mb-1">
                                    {{-- Custom Level Progress Bar with Handle --}}
                                    <div class="relative">
                                        <div class="level-progress-track rounded-full h-3 sm:h-4 relative overflow-visible">
                                            <div class="level-progress-fill h-3 sm:h-4 rounded-full transition-all duration-500 ease-out relative overflow-visible"
                                                style="width: {{ $progressInfo['progress_percentage'] }}%">
                                                {{-- Progress Handle/Thumb for Level --}}
                                                <div class="level-progress-handle"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($progressInfo['is_max_level'])
                                    <p class="text-xs sm:text-sm text-yellow-300">🏆 Grandmaster Status
                                    </p>
                                @else
                                    <p class="text-xs sm:text-sm text-blue-200">
                                        {{ number_format($progressInfo['xp_remaining']) }} XP remaining
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid with Live Data -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-12">
                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-green drop-shadow-stats-green">

                    <!-- Books Pattern (scattered icons) -->
                    <div class="absolute top-0 left-0 w-full h-24 opacity-80 blur-[1px]">

                    </div>

                    <!-- Icon -->
                    <div class="flex items-center justify-center mb-2 relative z-10">
                        <img src="{{ asset('images/dashboard/book.png') }}" alt="Completed"
                            class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
                    </div>

                    <!-- Number -->
                    <div class="text-xl sm:text-2xl font-bold relative z-10">2</div>

                    <!-- Label -->
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Completed</div>
                </div>



                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-yellow drop-shadow-stats-yellow">
                    <div class="flex items-center justify-center mb-2">
                        <img src="{{ asset('images/dashboard/points.png') }}" alt="Points"
                            class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                    </div>
                    <div class="text-xl sm:text-2xl font-bold relative z-10" id="dashboardPoints">
                        {{ $profile?->total_points ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Points</div>
                </div>


                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-red drop-shadow-stats-red">
                    <div class="flex items-center justify-center mb-2">
                        <img src="{{ asset('images/dashboard/streak.png') }}" alt="Streak"
                            class="w-9 h-9 sm:w-11 sm:h-11 object-contain relative z-10">
                    </div>
                    <div class="text-xl sm:text-2xl font-bold relative z-10" id="dashboardStreak">
                        {{ $profile?->current_streak ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Streak</div>
                </div>


                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-blue drop-shadow-stats-blue">
                    <div class="flex items-center justify-center mb-2">
                        <img src="{{ asset('images/dashboard/star.png') }}" alt="Level"
                            class="w-12 h-8 sm:w-14 sm:h-10 object-contain relative z-10">
                    </div>
                    <div class="text-xl sm:text-2xl font-bold relative z-10">3</div>
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Level</div>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="bg-white rounded-3xl shadow-xl p-6 mb-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800">Player Statistics</h2>
                </div>

                <!-- Competency Level -->
                <div class="bg-blue-50 rounded-2xl p-5 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                            <span class="font-semibold text-gray-700">Competency Level</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">5/5</span>
                    </div>
                    <div class="flex space-x-2">
                        <div class="flex-1 h-3 bg-blue-600 rounded-full"></div>
                        <div class="flex-1 h-3 bg-blue-600 rounded-full"></div>
                        <div class="flex-1 h-3 bg-blue-600 rounded-full"></div>
                        <div class="flex-1 h-3 bg-blue-600 rounded-full"></div>
                        <div class="flex-1 h-3 bg-blue-600 rounded-full"></div>
                    </div>
                </div>

                <!-- Accuracy Rate -->
                <div class="bg-cyan-50 rounded-2xl p-5 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-cyan-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-gray-700">Accuracy Rate</span>
                        </div>
                        <span class="text-2xl font-bold text-cyan-600">97%</span>
                    </div>
                    <div class="w-full h-3 bg-cyan-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-full" style="width: 97%">
                        </div>
                    </div>
                </div>

                <!-- Login Streak -->
                <div class="bg-orange-50 rounded-2xl p-5 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-gray-700">Login Streak</span>
                        </div>
                        <span class="text-2xl font-bold text-orange-600">10 days</span>
                    </div>
                </div>

                <!-- Assessments Taken -->
                <div class="bg-purple-50 rounded-2xl p-5 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-gray-700">Assessments Taken</span>
                        </div>
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                </div>

                <!-- Average Score -->
                <div class="bg-indigo-50 rounded-2xl p-5 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-semibold text-gray-700">Average Score</span>
                        </div>
                        <span class="text-2xl font-bold text-indigo-600">93%</span>
                    </div>
                </div>

                <!-- Current Level -->
                <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-gray-700">Current Level</span>
                        </div>
                        <span class="text-2xl font-bold text-purple-600">Level 13</span>
                    </div>
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="bg-white rounded-3xl shadow-xl p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Recent Achievements</h2>
                <div class="space-y-3">
                    <div
                        class="flex items-center bg-yellow-50 rounded-xl p-4 transform hover:scale-105 transition-transform">
                        <div class="bg-yellow-400 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">Perfect Week</h3>
                            <p class="text-sm text-gray-600">7 days streak achieved!</p>
                        </div>
                        <span class="text-xs text-gray-500">2 days ago</span>
                    </div>

                    <div
                        class="flex items-center bg-green-50 rounded-xl p-4 transform hover:scale-105 transition-transform">
                        <div class="bg-green-400 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">Level Up!</h3>
                            <p class="text-sm text-gray-600">Reached Level 13</p>
                        </div>
                        <span class="text-xs text-gray-500">5 days ago</span>
                    </div>

                    <div class="flex items-center bg-blue-50 rounded-xl p-4 transform hover:scale-105 transition-transform">
                        <div class="bg-blue-400 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">Assessment Master</h3>
                            <p class="text-sm text-gray-600">Scored 100% on Math Quiz</p>
                        </div>
                        <span class="text-xs text-gray-500">1 week ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection