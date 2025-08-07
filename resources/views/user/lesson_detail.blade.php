@extends('layouts.user_layout')

@section('title', $lesson->title)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $lesson->title }}</h1>
        <p class="text-gray-600">{{ $lesson->description }}</p>
    </div>

    <!-- Lesson Content -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Lesson Content Coming Soon</h3>
            <p class="text-gray-600">This lesson will contain video content, interactive exercises, and assessments.</p>
            
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('courses') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Back to Courses
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 