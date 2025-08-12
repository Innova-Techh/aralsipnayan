@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            Welcome back, {{ Auth::user() ? Auth::user()->name : 'Juan Dela Cruz' }}! 👋
        </h1>
        <p class="text-gray-600">Ready to continue your math journey?</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Progress and Continue Learning -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Learning Progress -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Your Learning Progress</h2>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Grade 6 Mathematics</span>
                        <span class="text-sm text-gray-500">2/8 Lessons</span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm text-gray-500">25% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-orange-500 rounded-xl p-4 text-white text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">2</div>
                        <div class="text-sm opacity-90">Completed</div>
                    </div>
                    
                    <div class="bg-green-500 rounded-xl p-4 text-white text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">1250</div>
                        <div class="text-sm opacity-90">Points</div>
                    </div>
                    
                    <div class="bg-blue-700 rounded-xl p-4 text-white text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">A</div>
                        <div class="text-sm opacity-90">Section</div>
                    </div>
                    
                    <div class="bg-cyan-500 rounded-xl p-4 text-white text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="text-2xl font-bold">#4</div>
                        <div class="text-sm opacity-90">Rank</div>
                    </div>
                </div>
            </div>

            <!-- Continue Learning -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Continue Learning</h2>
                    </div>
                    <a href="{{ route('lessons.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All Lessons</a>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Evaluate Exponents</h3>
                            <p class="text-sm text-gray-600">Learn how to calculate and evaluate expressions with exponents</p>
                            <div class="flex items-center mt-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium mr-2">Grade 6</span>
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">120 points</span>
                            </div>
                        </div>
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        Start Lesson
                    </button>
                </div>
                
                <div class="text-sm text-gray-500 mt-4">Pick up where you left off</div>
            </div>
        </div>

        <!-- Right Column - Leaderboard and Achievements -->
        <div class="space-y-8">
            <!-- Grade 6 Leaderboard -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Grade 6 Leaderboard</h2>
                    </div>
                    <span class="text-blue-600 hover:text-blue-700 text-sm font-medium cursor-pointer">View All</span>
                </div>

                <div class="space-y-3">
                    <!-- 1st Place -->
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Maria Santos</div>
                                <div class="text-sm text-gray-600">3 lessons • 1580 pts</div>
                            </div>
                        </div>
                        <div class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-bold">1st</div>
                    </div>

                    <!-- 2nd Place -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-400 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Carlos Reyes</div>
                                <div class="text-sm text-gray-600">2 lessons • 1420 pts</div>
                            </div>
                        </div>
                        <div class="bg-gray-400 text-white px-2 py-1 rounded-full text-xs font-bold">2nd</div>
                    </div>

                    <!-- 3rd Place -->
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Ana Garcia</div>
                                <div class="text-sm text-gray-600">2 lessons • 1350 pts</div>
                            </div>
                        </div>
                        <div class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-bold">3rd</div>
                    </div>

                    <!-- Current User -->
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border-2 border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-sm">J</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Juan Dela Cruz <span class="text-blue-600">(You)</span></div>
                                <div class="text-sm text-gray-600">2 lessons • 1250 pts</div>
                            </div>
                        </div>
                        <div class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-bold">#4</div>
                    </div>

                    <!-- 5th Place -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-300 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-gray-600 font-bold text-sm">M</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Miguel Torres</div>
                                <div class="text-sm text-gray-600">1 lessons • 1190 pts</div>
                            </div>
                        </div>
                        <div class="bg-gray-300 text-gray-700 px-2 py-1 rounded-full text-xs font-bold">#5</div>
                    </div>
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7H3v2h1v11c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6h1V4zm-3 13H5V6h14v11z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Recent Achievements</h2>
                    </div>
                    <span class="text-blue-600 hover:text-blue-700 text-sm font-medium cursor-pointer">View All</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">First Steps</div>
                    </div>
                    
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Math Whiz</div>
                    </div>
                    
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Grade Champion</div>
                    </div>
                    
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7H3v2h1v11c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6h1V4zm-3 13H5V6h14v11z"/>
                            </svg>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Point Collector</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection