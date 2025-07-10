@extends('layouts.user_layout')

@section('title', 'User Dashboard - AralSipnayan')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="flex items-center space-x-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
            <span class="text-2xl">🤖</span>
        </div>
        <div>
            <h1 class="text-4xl font-bold text-white">Welcome back, {{ auth()->user()->name ?? 'Juan Dela Cruz' }}! 👋</h1>
            <p class="text-gray-300 text-lg">Ready to continue your math adventure?</p>
        </div>
    </div>
</div>

<!-- Daily Challenge -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <span class="text-2xl">👑</span>
                </div>
                <div>
                    <div class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold inline-block mb-2">
                        Daily Mini
                    </div>
                    <h2 class="text-2xl font-bold text-white">Daily Challenge ✨</h2>
                    <p class="text-white/80">Complete today's math challenge and earn bonus XP!</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <svg class="w-4 h-4 text-white/60" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="text-white/80 text-sm">Available: Every Mon - Sat</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-white font-bold text-xl mb-2">+50 XP</div>
                <button class="bg-white/20 backdrop-blur-sm text-white px-6 py-2 rounded-xl hover:bg-white/30 transition-colors">
                    Start Challenge
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- My Courses -->
    <div class="bg-blue-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
            </div>
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">My Courses</h3>
        <p class="text-gray-300 text-sm mb-4">Continue your learning journey</p>
        <div class="text-gray-300 text-sm">
            <span class="text-white font-semibold">2/20</span> completed
        </div>
    </div>

    <!-- Skill Tests -->
    <div class="bg-purple-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7h-3V2h-2v2H8V2H6v2H3v2h18V4zm0 4H3v12h18V8z"/>
                </svg>
            </div>
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Skill Tests</h3>
        <p class="text-gray-300 text-sm mb-4">Test your knowledge & skills</p>
        <div class="text-gray-300 text-sm">
            <span class="text-white font-semibold">60+</span> assessments
        </div>
    </div>

    <!-- Leaderboard -->
    <div class="bg-green-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                </svg>
            </div>
            <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-bold text-black">🏆</span>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Leaderboard</h3>
        <p class="text-gray-300 text-sm mb-4">Compete with classmates</p>
        <div class="text-gray-300 text-sm">
            <span class="text-white font-semibold">#2</span> your rank
        </div>
    </div>

    <!-- Profile -->
    <div class="bg-gray-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gray-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Profile</h3>
        <p class="text-gray-300 text-sm mb-4">Settings</p>
    </div>
</div>

<!-- Additional Cards Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Achievements -->
    <div class="bg-yellow-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                <span class="text-xl">🏅</span>
            </div>
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Achievements</h3>
        <p class="text-gray-300 text-sm mb-4">15 earned</p>
    </div>

    <!-- Progress -->
    <div class="bg-cyan-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-cyan-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67z"/>
                </svg>
            </div>
            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-bold text-white">📊</span>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Progress</h3>
        <p class="text-gray-300 text-sm mb-4">Level 5</p>
    </div>

    <!-- Resources -->
    <div class="bg-pink-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-pink-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                </svg>
            </div>
            <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-bold text-white">📚</span>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Resources</h3>
        <p class="text-gray-300 text-sm mb-4">Study materials</p>
    </div>

    <!-- Additional Stats -->
    <div class="bg-indigo-500/20 backdrop-blur-sm rounded-2xl p-6 card-hover transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Study Time</h3>
        <p class="text-gray-300 text-sm mb-4">2.5 hours today</p>
    </div>
</div>
@endsection