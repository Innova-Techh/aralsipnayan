@extends('layouts.user_layout')

@section('title', 'Assessment Results - AralSipnayan')

@section('content')
<div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center px-4 sm:px-0 z-50">
    <div class="w-full max-w-4xl bg-[#FFF7E6] rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#2077AF] to-[#4720AF] p-4 flex items-center justify-between shadow-[0_8px_16px_rgba(0,0,0,0.3)] border-b-4 border-[#1a5a8a] relative">
            <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none"></div>
            <div class="flex items-center gap-2 relative z-10">
                <span class="material-symbols-outlined text-white text-2xl">analytics</span>
                <h2 class="text-xl font-extrabold text-white text-shadow-lg">
                    Results: {{ $assessment->title }}
                </h2>
            </div>
            <button onclick="window.location.href='{{ route('teacher-assessments.show', $assessment->id) }}'" 
                class="text-white hover:text-gray-200 transition relative z-10">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto">
            
            <!-- Score Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Score Card -->
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-wider mb-1">Score</div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-gray-800">{{ $quizResult->score }}</span>
                        <span class="text-lg text-gray-500 mb-1">/ {{ $quizResult->total_questions }}</span>
                    </div>
                </div>

                <!-- Percentage Card -->
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 {{ $quizResult->percentage >= 75 ? 'border-green-500' : ($quizResult->percentage >= 50 ? 'border-yellow-500' : 'border-red-500') }}">
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-wider mb-1">Percentage</div>
                    <div class="flex items-center gap-2">
                        <span class="text-3xl font-bold {{ $quizResult->percentage >= 75 ? 'text-green-600' : ($quizResult->percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $quizResult->percentage }}%
                        </span>
                    </div>
                </div>

                <!-- Time Taken Card -->
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-wider mb-1">Time Taken</div>
                    <div class="flex items-center gap-2">
                        <span class="text-3xl font-bold text-gray-800">
                            @if($quizResult->time_taken >= 3600)
                                {{ gmdate('H:i:s', $quizResult->time_taken) }}
                            @else
                                {{ gmdate('i:s', $quizResult->time_taken) }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detailed Questions -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Detailed Breakdown</h3>

                @foreach($detailedResponses as $index => $response)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Question Header -->
                        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-start">
                            <div class="flex gap-3">
                                <span class="bg-gray-200 text-gray-700 font-bold px-2 py-1 rounded text-sm h-fit">Q{{ $index + 1 }}</span>
                                <div class="text-gray-800 font-medium">
                                    {!! $response['question_text'] !!}
                                </div>
                            </div>
                            <div class="flex-shrink-0 ml-4">
                                @if($response['is_correct'])
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">
                                        <span class="material-symbols-outlined text-sm">check_circle</span> Correct
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-bold">
                                        <span class="material-symbols-outlined text-sm">cancel</span> Incorrect
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Answers -->
                        <div class="p-4 space-y-3">
                            <!-- Student Answer -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                <span class="text-sm font-medium text-gray-500 w-32">Your Answer:</span>
                                <span class="font-medium {{ $response['is_correct'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $response['student_answer'] }}
                                </span>
                            </div>

                            <!-- Correct Answer (if incorrect) -->
                            @if(!$response['is_correct'])
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="text-sm font-medium text-gray-500 w-32">Correct Answer:</span>
                                    <span class="font-medium text-green-600">
                                        {{ $response['correct_answer'] }}
                                    </span>
                                </div>
                            @endif

                            <!-- Explanation -->
                            @if(!empty($response['explanation']))
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-bold text-blue-600">Explanation:</span> {{ $response['explanation'] }}
                                    </p>
                                </div>
                            @endif
                            
                            <!-- Points -->
                            <div class="mt-2 text-xs text-gray-400 text-right">
                                Points earned: {{ $response['points_earned'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.location.href='{{ route('teacher-assessments.show', $assessment->id) }}'"
                    class="px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition shadow-sm">
                    Back to Assessment
                </button>
                
                <form action="{{ route('teacher-assessments.retake', $assessment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to retake this quiz? Your previous progress will be archived.');">
                    @csrf
                    <button type="submit" 
                        class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl shadow-lg hover:scale-105 transition transform">
                        <span class="material-symbols-outlined align-bottom mr-1">refresh</span> Retake Quiz
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
