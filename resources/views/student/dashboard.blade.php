@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<div class="space-y-6 sm:space-y-8">
    <!-- Welcome Header (Hero) -->
    <div class="relative -mx-6 sm:-mx-8 lg:-mx-12 p-5 sm:p-6 text-white overflow-hidden bg-center bg-cover" style="background-image: url('{{ asset('images/dashboard/bg.png') }}');">
        <div class="relative z-10 px-6 sm:px-8 lg:px-12">
            <h1 class="text-[22px] sm:text-base md:text-xl lg:text-3xl font-extrabold leading-tight">
                Welcome back, {{ Auth::user()->username }}! 👋
            </h1>
            <p class="text-[10px] sm:text-sm md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">Ready to continue your math journey?</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Progress and Continue Learning -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Learning Progress -->
            <div class="rounded-xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Your Learning Progress</h2>
                            <h4 class="text-xs sm:text-sm text-gray-900 mt-1">You're growing into a math master every day!</h3>
                        </div>
                    </div>
                </div>
                
                <!-- Level Card -->
                <div class="mb-5 sm:mb-6">
                    <div class="bg-[#0F1A5B] text-white rounded-xl p-4 sm:p-5 shadow-inner">
                        <div class="flex items-start gap-3">
                            <!-- Placeholder for rocket/level image -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 flex items-center justify-center">
                                <img src="{{ asset('images/dashboard/rocket.png') }}" alt="rocket" class="w-10 h-10 sm:w-10 sm:h-10 rounded-xl">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm sm:text-base font-bold">Level 3</div>
                                        <div class="text-[11px] sm:text-xs text-blue-200">Problem Solver</div>
                                    </div>
                                    <div class="text-[11px] sm:text-xs text-blue-200">460 XP / 1000 XP</div>
                                </div>
                                <!-- Progress bar stylized -->
                                <div class="mt-2 sm:mt-3">
                                    <div class="relative h-2.5 bg-white/15 rounded-full overflow-hidden">
                                        <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-orange-400 to-orange-500" style="width: 46%"></div>
                                    </div>
                                    <div class="mt-2 text-[11px] sm:text-xs text-blue-200">540 XP remaining</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/bookcard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/book.png') }}" alt="Completed" class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">2</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Completed</div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/pointscard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/points.png') }}" alt="Points" class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">1250</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Points</div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/streakcard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/streak.png') }}" alt="Streak" class="w-9 h-9 sm:w-11 sm:h-11 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">8</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Streak</div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/starcard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/star.png') }}" alt="Level" class="w-12 h-8 sm:w-14 sm:h-10 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">3</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Level</div>
                    </div>
                </div>
            </div>

            <!-- Assigned Assessments -->
            <div class="rounded-xl p-4 sm:p-6">
            <div class="bg-white rounded-xl shadow-sm p-0 overflow-hidden">
                <div class="flex items-center justify-between px-3 sm:px-4 py-3 bg-red-600 text-white">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center mr-2">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-white rounded-full flex items-center justify-center">
                                <span class="text-red-600 font-bold text-lg">🎯</span>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-xs sm:text-sm lg:text-base font-semibold truncate">Assigned Assessments</h2>
                            <h3 class="text-[10px] sm:text-xs lg:text-sm mt-1 line-clamp-2 sm:line-clamp-1">
                                Complete your assigned tasks to <br class="sm:hidden md:block lg:hidden">  earn points and level up!
                            </h3>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="text-[12px] sm:text-xs font-bold">2 Pending</span>
                    </div>
                    
                    <!-- Assessment Item -->   
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="bg-gray-50 rounded-lg p-3 flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900 text-xs sm:text-sm">Evaluate Exponents</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">New</span>
                                </div>
                                <p class="text-[10px] sm:text-[12px] text-gray-600 mt-1">Learn how to calculate and evaluate expressions with exponents</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">120 points</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700">Beginner</span>
                                </div>
                            </div>
                            <a href="{{ route('assessments.index') }}" class="self-center bg-primary-blue hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl">Start</a>
                        </div>
                        <!-- Assessment Item -->
                        <div class="bg-gray-50 rounded-lg p-3 flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-gray-900 text-xs sm:text-sm">Evaluate Exponents</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Due</span>
                                </div>
                                <p class="text-[10px] sm:text-[12px] text-gray-600 mt-1">Learn how to calculate and evaluate expressions with exponents</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">120 points</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700">Advanced</span>
                                </div>
                            </div>
                            <a href="{{ route('assessments.index') }}" class="self-center bg-primary-blue hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl">Start</a>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- Completed Assessments -->
        <div class="rounded-xl p-4 sm:p-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center mr-2 sm:mr-3">
                        <img src="{{ asset('images/dashboard/check.png') }}" alt="Check" class="w-5 h-5 sm:w-12 sm:h-12 object-contain relative z-10">
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Completed Assessments</h2>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Mistakes help you learn—keep going!</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <!-- Completed Assessment Item 1 -->
                    <div class="rounded-xl p-4 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/assessment1.png') }}');">
                        <div class="absolute top-0 right-0 bg-yellow-700/90 text-white text-xs font-bold px-3 py-1 rounded-bl-lg backdrop-blur-sm">17/30</div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white text-sm sm:text-base mb-1 drop-shadow-lg">Basic Arithmetic</h3>
                            <p class="text-yellow-100 text-xs sm:text-sm mb-3 drop-shadow-md">Lorem ipsum dolor sit amet</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/90 backdrop-blur-sm text-orange-600 text-xs px-2 py-1 rounded-full font-medium">Intermediate</span>
                                <div class="bg-gray-900/90 flex items-center gap-1 px-4 pr-8 rounded-full">
                                    <img src="{{ asset('images/dashboard/trophy.png') }}" alt="Trophy" class="w-5 h-5 sm:w-5 sm:h-5 object-contain relative z-10">
                                    <span class="text-white text-xs font-medium drop-shadow-md">+ 120 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Assessment Item 2 -->
                    <div class="rounded-xl p-4 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/assessment2.png') }}');">
                        <div class="absolute top-0 right-0 bg-green-700/90 text-white text-xs font-bold px-3 py-1 rounded-bl-lg backdrop-blur-sm">25/30</div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white text-sm sm:text-base mb-1 drop-shadow-lg">Basic Arithmetic</h3>
                            <p class="text-green-100 text-xs sm:text-sm mb-3 drop-shadow-md">Lorem ipsum dolor sit amet</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/90 backdrop-blur-sm text-green-600 text-xs px-2 py-1 rounded-full font-medium">Beginner</span>
                                <div class="bg-gray-900/90 flex items-center gap-1 px-4 pr-8 rounded-full">
                                    <img src="{{ asset('images/dashboard/trophy.png') }}" alt="Trophy" class="w-5 h-5 sm:w-5 sm:h-5 object-contain relative z-10">
                                    <span class="text-white text-xs font-medium drop-shadow-md">+ 120 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Assessment Item 3 -->
                    <div class="rounded-xl p-4 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/assessment3.png') }}');">
                        <div class="absolute top-0 right-0 bg-red-700/90 text-white text-xs font-bold px-3 py-1 rounded-bl-lg backdrop-blur-sm">9/30</div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white text-sm sm:text-base mb-1 drop-shadow-lg">Basic Arithmetic</h3>
                            <p class="text-red-100 text-xs sm:text-sm mb-3 drop-shadow-md">Lorem ipsum dolor sit amet</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/90 backdrop-blur-sm text-red-600 text-xs px-2 py-1 rounded-full font-medium">Advanced</span>
                                <div class="bg-gray-900/90 flex items-center gap-1 px-4 pr-8 rounded-full">
                                    <img src="{{ asset('images/dashboard/trophy.png') }}" alt="Trophy" class="w-5 h-5 sm:w-5 sm:h-5 object-contain relative z-10">
                                    <span class="text-white text-xs font-medium drop-shadow-md">+ 120 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Achievements -->
        </div>
        <!-- Right Column -->
        <div class ="rounded-xl p-4 sm:p-6">
            <div class="space-y-8">
                <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7H3v2h1v11c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6h1V4zm-3 13H5V6h14v11z"/>
                                </svg>
                            </div>
                            <h2 class="text-base sm:text-lg font-semibold text-gray-900">Recent Achievements</h2>
                        </div>
                        <span class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium cursor-pointer">View All</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="text-center p-3 sm:p-4 bg-blue-50 rounded-lg">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                                </svg>
                            </div>
                            <div class="font-semibold text-gray-900 text-xs sm:text-sm">First Steps</div>
                        </div>
                        
                        <div class="text-center p-3 sm:p-4 bg-green-50 rounded-lg">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div class="font-semibold text-gray-900 text-xs sm:text-sm">Math Whiz</div>
                        </div>
                        
                        <div class="text-center p-3 sm:p-4 bg-yellow-50 rounded-lg">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-500 rounded-lg flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <div class="font-semibold text-gray-900 text-xs sm:text-sm">Grade Champion</div>
                        </div>
                        
                        <div class="text-center p-3 sm:p-4 bg-orange-50 rounded-lg">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500 rounded-lg flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7H3v2h1v11c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6h1V4zm-3 13H5V6h14v11z"/>
                                </svg>
                            </div>
                            <div class="font-semibold text-gray-900 text-xs sm:text-sm">Point Collector</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection