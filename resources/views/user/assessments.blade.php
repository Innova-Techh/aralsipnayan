@extends('layouts.user_layout')

@section('title', 'Assessments')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Assessments</h1>
        <p class="text-gray-600">Track your progress and test your knowledge</p>
    </div>

    <!-- Assessments Content -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Assessments Coming Soon</h3>
            <p class="text-gray-600">This feature is under development. Check back soon!</p>
        </div>
    </div>
</div>
@endsection 