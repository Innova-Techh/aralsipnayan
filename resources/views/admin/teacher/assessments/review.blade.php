@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('teacher.assessments') }}" class="hover:text-blue-600 transition-colors">Quiz Management</a>
                <span class="mx-2">/</span>
                <a href="{{ route('teacher.assessments.results', $session->teacher_assessment_id) }}" class="hover:text-blue-600 transition-colors">Results</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Review</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Review Attempt</h1>
            <p class="text-gray-600 mt-1">
                {{ $session->user->studentProfile->firstname ?? 'Student' }} • 
                {{ $session->created_at->format('M d, Y h:i A') }}
            </p>
        </div>
        <div>
            <a href="{{ route('teacher.assessments.results', $session->teacher_assessment_id) }}" 
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <span class="material-symbols-outlined mr-2">arrow_back</span>
                Back to Results
            </a>
        </div>
    </div>

    <!-- Score Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="text-center">
            <h2 class="text-lg font-medium text-gray-500 uppercase tracking-wider mb-2">Status: {{ ucfirst($session->status) }}</h2>
            <div class="text-5xl font-bold text-gray-900 mb-2">{{ number_format($session->accuracy_percentage, 1) }}%</div>
            <p class="text-gray-600">{{ $session->total_points_earned }} / {{ $session->total_questions }} Points</p>
        </div>
    </div>

    <!-- Questions Review -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Question Analysis</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($session->responses as $index => $response)
                @php
                    // Find question text from session metadata or json if available
                    // For now, we rely on what we have. 
                    // If questions_json is available in session, we decode it.
                    $questions = $session->questions_json ?? [];
                    $questionData = collect($questions)->where('question_id', $response->question_id)->first();
                @endphp
                <div class="p-6 {{ $response->is_correct ? 'bg-green-50' : 'bg-red-50' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="text-md font-medium text-gray-900 mb-2">
                                Question {{ $loop->iteration }} 
                                <span class="ml-2 text-sm font-normal text-gray-500">({{ $response->difficulty_level }})</span>
                            </h4>
                            <p class="text-gray-800 mb-4">{!! $questionData['question_text'] ?? 'Question text not available' !!}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase">Student Answer</span>
                                    <p class="mt-1 {{ $response->is_correct ? 'text-green-700 font-medium' : 'text-red-700 font-medium' }}">
                                        {{ $response->student_answer }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase">Correct Answer</span>
                                    <p class="mt-1 text-green-700 font-medium">
                                        {{ $response->correct_answer }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            @if($response->is_correct)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="material-symbols-outlined text-sm mr-1">check_circle</span> Correct
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <span class="material-symbols-outlined text-sm mr-1">cancel</span> Incorrect
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
