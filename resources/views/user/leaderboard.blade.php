@extends('layouts.user_layout')

@section('title', 'Leaderboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $leaderboardData['title'] }}</h1>
                <p class="text-gray-600">{{ $leaderboardData['subtitle'] }}</p>
            </div>
            <button class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                Top 10 Students
            </button>
        </div>
    </div>

    <!-- Top 3 Students -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Top 3 Students</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($leaderboardData['top_students'] as $student)
            <div class="{{ $student['bg_color'] }} rounded-xl p-6 text-center {{ $student['text_color'] }}">
                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    @if($student['icon'] === 'crown')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L15.09 8.26L22 9L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9L8.91 8.26L12 2Z"/>
                        </svg>
                    @elseif($student['icon'] === 'medal')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    @elseif($student['icon'] === 'trophy')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    @endif
                </div>
                
                <!-- Rank -->
                <div class="text-lg font-semibold mb-2">
                    @if($student['rank'] === 1)
                        1st Place
                    @elseif($student['rank'] === 2)
                        2nd Place
                    @elseif($student['rank'] === 3)
                        3rd Place
                    @endif
                </div>
                
                <!-- Avatar Placeholder -->
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-xl font-bold">{{ substr($student['name'], 0, 1) }}</span>
                </div>
                
                <!-- Name -->
                <div class="font-bold text-lg mb-2">{{ $student['name'] }}</div>
                
                <!-- Stats -->
                <div class="flex flex-col gap-2">
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ number_format($student['points']) }} pts
                    </span>
                    <span class="text-sm opacity-90">{{ $student['lessons'] }} lessons</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Ranking List -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-6">
            <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L15.09 8.26L22 9L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9L8.91 8.26L12 2Z"/>
            </svg>
            <h2 class="text-xl font-semibold text-gray-900">Ranking</h2>
        </div>
        
        <div class="space-y-3">
            @foreach($leaderboardData['ranking_list'] as $student)
            <div class="flex items-center p-4 rounded-lg {{ $student['highlight'] ? 'bg-blue-50 border-l-4 border-blue-500' : 'hover:bg-gray-50' }}">
                <!-- Rank Number -->
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                    <span class="font-semibold text-gray-700">{{ $student['rank'] }}</span>
                </div>
                
                <!-- Avatar Placeholder -->
                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                    <span class="font-semibold text-gray-600">{{ substr($student['name'], 0, 1) }}</span>
                </div>
                
                <!-- Student Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-900">{{ $student['name'] }}</span>
                        @if($student['is_current_user'])
                            <span class="text-blue-600 text-sm font-medium">(You)</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $student['lessons'] }} lessons • {{ number_format($student['points']) }} pts
                    </div>
                </div>
                
                <!-- Grade -->
                <div class="text-sm text-gray-500 font-medium">
                    {{ $student['grade'] }}
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Load More Button -->
        <div class="text-center mt-6">
            <button class="text-blue-600 hover:text-blue-700 font-medium">
                Load More Students
            </button>
        </div>
    </div>
</div>
@endsection 