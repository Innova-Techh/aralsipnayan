@extends('layouts.user_layout')

@section('title', 'Resources')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Resources</h1>
        <p class="text-gray-600">Access additional learning materials and tools</p>
    </div>

    <!-- Resources Content -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Resources Coming Soon</h3>
            <p class="text-gray-600">This feature is under development. Check back soon!</p>
        </div>
    </div>
</div>
@endsection 