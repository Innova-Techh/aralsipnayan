@extends('layouts.user_layout')

@section('title', 'Progression')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Progression</h1>
        <p class="text-gray-600">Track your learning journey and growth</p>
    </div>

    <!-- Progression Content -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Progression Coming Soon</h3>
            <p class="text-gray-600">This feature is under development. Check back soon!</p>
        </div>
    </div>
</div>
@endsection 