@extends('layouts.user_layout')

@section('title', 'Review Assessment')

@section('content')

<div class="min-h-screen px-4 py-8 font-baloo">
    <!-- Assessment Header -->
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl p-6 md:p-10 mb-8">
        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-800 mb-2">Review Assessment</h1>
        <p class="text-gray-600 text-sm md:text-xl">Go through each question and review your answers. You can see which answers were correct and incorrect.</p>
        
        <!-- Summary Stats -->
        <div class="mt-4 flex flex-wrap gap-4 text-sm">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                {{ $correctAnswers }}/{{ $totalQuestions }} Correct ({{ $scorePercentage }}%)
            </span>
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                Category: {{ str_replace('_', ' ', $category) }}
            </span>
        </div>
    </div>

    <!-- Question Display -->
    <div class="max-w-5xl mx-auto space-y-6">
        @foreach($questionResponses as $index => $response)
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <!-- Question Header -->
            <div class="mb-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-gray-700 text-sm md:text-base">Question {{ $index + 1 }}</span>
                    @if($response->is_correct)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">✓ Correct</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">✗ Incorrect</span>
                    @endif
                </div>
                <div class="text-right text-xs md:text-sm text-gray-500">
                    <div>{{ ucfirst($response->difficulty_level) }}</div>
                    <div>{{ $response->topic }}</div>
                </div>
            </div>

            <!-- Question Text -->
            <p class="text-gray-800 mb-4 text-sm md:text-base font-medium">
                {{ $response->question_text }}
            </p>

            <!-- Options Display -->
            @if($response->question_type === 'multiple_choice')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach(['choice_a' => 'A', 'choice_b' => 'B', 'choice_c' => 'C', 'choice_d' => 'D'] as $choiceField => $choiceLetter)
                        @if($response->$choiceField)
                        <div class="flex items-center p-3 rounded-xl border 
                                    @if($choiceLetter === $response->correct_answer) 
                                        bg-green-100 border-green-400 text-green-800
                                    @elseif($choiceLetter === $response->user_answer && $choiceLetter !== $response->correct_answer)
                                        bg-red-100 border-red-400 text-red-800
                                    @else
                                        border-gray-300
                                    @endif
                                    ">
                            <span class="font-semibold mr-3">{{ $choiceLetter }}.</span>
                            <span>{{ $response->$choiceField }}</span>
                            @if($choiceLetter === $response->user_answer && $choiceLetter !== $response->correct_answer)
                                <span class="ml-auto text-xs">(Your answer)</span>
                            @elseif($choiceLetter === $response->correct_answer)
                                <span class="ml-auto text-xs">(Correct answer)</span>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            @elseif($response->question_type === 'true_false')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach(['A' => 'True', 'B' => 'False'] as $choiceLetter => $choiceText)
                    <div class="flex items-center p-3 rounded-xl border 
                                @if($choiceLetter === $response->correct_answer) 
                                    bg-green-100 border-green-400 text-green-800
                                @elseif($choiceLetter === $response->user_answer && $choiceLetter !== $response->correct_answer)
                                    bg-red-100 border-red-400 text-red-800
                                @else
                                    border-gray-300
                                @endif
                                ">
                        <span class="font-semibold mr-3">{{ $choiceLetter }}.</span>
                        <span>{{ $choiceText }}</span>
                        @if($choiceLetter === $response->user_answer && $choiceLetter !== $response->correct_answer)
                            <span class="ml-auto text-xs">(Your answer)</span>
                        @elseif($choiceLetter === $response->correct_answer)
                            <span class="ml-auto text-xs">(Correct answer)</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            @elseif($response->question_type === 'fill_blanks')
                <div class="space-y-2">
                    <div class="p-3 rounded-xl bg-gray-50 border">
                        <strong>Your answer:</strong> 
                        <span class="@if($response->is_correct) text-green-600 @else text-red-600 @endif">
                            {{ $response->user_answer }}
                        </span>
                    </div>
                    @if(!$response->is_correct)
                    <div class="p-3 rounded-xl bg-green-50 border border-green-200">
                        <strong>Correct answer:</strong> 
                        <span class="text-green-600">{{ $response->correct_answer }}</span>
                    </div>
                    @endif
                </div>
            @endif

            <!-- Explanation -->
            @if($response->explanation)
            <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-2">Explanation:</h4>
                <p class="text-blue-700 text-sm">{{ $response->explanation }}</p>
            </div>
            @endif

            <!-- Response Time -->
            <div class="mt-3 text-xs text-gray-500">
                Response time: {{ $response->response_time ?? 0 }} seconds
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary Card -->
    <div class="max-w-5xl mx-auto mt-8 bg-white rounded-3xl shadow-xl p-6 text-center">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Assessment Summary</h2>
        <p class="text-gray-600 mb-4">
            You answered <strong>{{ $correctAnswers }}</strong> out of <strong>{{ $totalQuestions }}</strong> questions correctly 
            ({{ $scorePercentage }}%).
        </p>
        
        <!-- Difficulty Breakdown -->
        @if($questionsByDifficulty->count() > 1)
        <div class="mb-4">
            <h3 class="font-semibold mb-2">Performance by Difficulty:</h3>
            <div class="flex justify-center gap-4 text-sm">
                @foreach($questionsByDifficulty as $difficulty => $questions)
                    @php
                        $difficultyCorrect = $questions->where('is_correct', 1)->count();
                        $difficultyTotal = $questions->count();
                        $difficultyPercent = $difficultyTotal > 0 ? round(($difficultyCorrect / $difficultyTotal) * 100) : 0;
                    @endphp
                    <span class="bg-gray-100 px-3 py-1 rounded-full">
                        {{ ucfirst($difficulty) }}: {{ $difficultyCorrect }}/{{ $difficultyTotal }} ({{ $difficultyPercent }}%)
                    </span>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('student.assessments.category', $category) }}" 
               class="px-6 py-3 rounded-xl bg-blue-500 text-white font-semibold hover:bg-blue-600 transition">
                Back to Assessments
            </a>
            <a href="{{ route('student.dashboard') }}" 
               class="px-6 py-3 rounded-xl bg-gray-500 text-white font-semibold hover:bg-gray-600 transition">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>

@endsection
