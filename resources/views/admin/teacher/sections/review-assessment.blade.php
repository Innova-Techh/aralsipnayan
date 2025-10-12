@extends('admin.teacher.layouts.app')

@section('title', 'Assessment Review - Aralsipnayan')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200 px-8 py-6">
            <!-- Back button -->
            <button onclick="window.history.back()"
                class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors mb-6">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="font-medium">Back</span>
            </button>

            <!-- Assessment Title -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $assessment->title ?? 'Algebra Basics Quiz' }}</h1>
                <p class="text-gray-600 mt-1">Assessment Review - {{ $student->name ?? 'Student' }} (ID: {{ $result->student_id ?? '2024001' }})</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-4 gap-6">
                <!-- Score Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-blue-600 text-3xl">emoji_events</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $result->score ?? 92 }}/100</div>
                    <div class="text-sm text-gray-600 mt-2">Score</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $result->accuracy ?? 92 }}% Accuracy</div>
                </div>

                <!-- Correct Answers Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                    </div>
                    <div class="text-3xl font-bold text-green-600">{{ $result->correct_count ?? 23 }}/{{ $result->total_questions ?? 25 }}</div>
                    <div class="text-sm text-gray-600 mt-2">Correct Answers</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $result->correct_rate ?? 92 }}% correct rate</div>
                </div>

                <!-- Wrong Answers Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-red-600 text-3xl">cancel</span>
                    </div>
                    <div class="text-3xl font-bold text-red-600">{{ $result->wrong_count ?? 2 }}/{{ $result->total_questions ?? 25 }}</div>
                    <div class="text-sm text-gray-600 mt-2">Wrong Answers</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $result->wrong_rate ?? 8 }}% incorrect rate</div>
                </div>

                <!-- Time Card -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-blue-600 text-3xl">schedule</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $result->time_spent ?? '14 min 30 sec' }}</div>
                    <div class="text-sm text-gray-600 mt-2">Total Time</div>
                    <div class="text-xs text-gray-500 mt-1">Avg: {{ $result->avg_time ?? '34' }} sec/question</div>
                </div>
            </div>
        </div>

        <!-- Assessment Information -->
        <div class="px-8 py-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assessment Information</h3>
                <div class="grid grid-cols-4 gap-6">
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Date Taken</div>
                        <div class="font-semibold text-gray-900">{{ $result->completed_at ?? '2024-01-18' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Subject</div>
                        <div class="font-semibold text-gray-900">{{ $assessment->subject ?? 'Algebra' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Total Questions</div>
                        <div class="font-semibold text-gray-900">{{ $result->total_questions ?? 25 }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Status</div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                            <span class="font-semibold text-green-600">Passed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Question Review -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Detailed Question Review</h3>
                <p class="text-gray-600 mb-6">Review each question with student responses and explanations</p>

                <div class="space-y-6">
                    @forelse($questions ?? [] as $index => $question)
                        <div class="border border-gray-200 rounded-lg p-6">
                            <!-- Question Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-lg font-semibold text-gray-900">Question {{ $index + 1 }}</h4>
                                    @if($question['is_correct'])
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            Correct
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">cancel</span>
                                            Incorrect
                                        </span>
                                    @endif
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                        {{ $question['category'] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    <span class="text-sm">{{ $question['time_spent'] ?? '28 sec' }}</span>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-4">
                                <p class="text-gray-900 font-medium">{{ $question['question_text'] }}</p>
                            </div>

                            <!-- Answer Options -->
                            <div class="space-y-3">
                                @foreach($question['options'] as $optionKey => $option)
                                    @php
                                        $isStudentAnswer = ($question['student_answer'] === $optionKey);
                                        $isCorrectAnswer = ($question['correct_answer'] === $optionKey);
                                        
                                        $bgClass = 'bg-white';
                                        $borderClass = 'border-gray-200';
                                        $iconClass = '';
                                        
                                        if ($isCorrectAnswer) {
                                            $bgClass = 'bg-green-50';
                                            $borderClass = 'border-green-200';
                                            $iconClass = 'text-green-600';
                                        } elseif ($isStudentAnswer && !$isCorrectAnswer) {
                                            $bgClass = 'bg-red-50';
                                            $borderClass = 'border-red-200';
                                            $iconClass = 'text-red-600';
                                        }
                                    @endphp
                                    
                                    <div class="flex items-center gap-3 p-4 {{ $bgClass }} rounded-lg border {{ $borderClass }}">
                                        <div class="font-semibold text-gray-700">{{ $optionKey }}.</div>
                                        <div class="flex-1 text-gray-900">{{ $option }}</div>
                                        @if($isCorrectAnswer)
                                            <span class="material-symbols-outlined {{ $iconClass }}">check_circle</span>
                                        @elseif($isStudentAnswer)
                                            <span class="material-symbols-outlined {{ $iconClass }}">cancel</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Explanation (for incorrect answers) -->
                            @if(!$question['is_correct'] && isset($question['explanation']))
                                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-blue-600 mt-0.5">lightbulb</span>
                                        <div>
                                            <div class="font-semibold text-blue-900 mb-1">Explanation:</div>
                                            <div class="text-blue-800 text-sm">{{ $question['explanation'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <!-- Default sample questions when no data -->
                        @foreach([
                            [
                                'is_correct' => true,
                                'category' => 'Linear Equations',
                                'question_text' => 'Solve for x: 2x + 5 = 15',
                                'options' => ['A' => 'x = 5', 'B' => 'x = 10', 'C' => 'x = 7.5', 'D' => 'x = 2.5'],
                                'student_answer' => 'A',
                                'correct_answer' => 'A',
                                'time_spent' => '28 sec'
                            ],
                            [
                                'is_correct' => true,
                                'category' => 'Algebraic Expressions',
                                'question_text' => 'Simplify: 3x + 2x - x',
                                'options' => ['A' => '4x', 'B' => '5x', 'C' => '6x', 'D' => '3x'],
                                'student_answer' => 'A',
                                'correct_answer' => 'A',
                                'time_spent' => '22 sec'
                            ],
                            [
                                'is_correct' => false,
                                'category' => 'Quadratic Equations',
                                'question_text' => 'What is the value of x in: x² = 16',
                                'options' => ['A' => 'x = 4', 'B' => 'x = ±4', 'C' => 'x = 8', 'D' => 'x = 2'],
                                'student_answer' => 'A',
                                'correct_answer' => 'B',
                                'explanation' => 'When solving x² = 16, we must consider both positive and negative square roots. Therefore, x = ±4 (both +4 and -4 are valid solutions). The student only considered the positive root.',
                                'time_spent' => '45 sec'
                            ],
                            [
                                'is_correct' => true,
                                'category' => 'Factoring',
                                'question_text' => 'Factor: x² + 5x + 6',
                                'options' => ['A' => '(x + 2)(x + 3)', 'B' => '(x + 1)(x + 6)', 'C' => '(x + 2)(x - 3)', 'D' => '(x - 2)(x - 3)'],
                                'student_answer' => 'A',
                                'correct_answer' => 'A',
                                'time_spent' => '38 sec'
                            ]
                        ] as $index => $question)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <!-- Question Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-lg font-semibold text-gray-900">Question {{ $index + 1 }}</h4>
                                        @if($question['is_correct'])
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                                Correct
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">cancel</span>
                                                Incorrect
                                            </span>
                                        @endif
                                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                            {{ $question['category'] }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-500">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        <span class="text-sm">{{ $question['time_spent'] }}</span>
                                    </div>
                                </div>

                                <!-- Question Text -->
                                <div class="mb-4">
                                    <p class="text-gray-900 font-medium">{{ $question['question_text'] }}</p>
                                </div>

                                <!-- Answer Options -->
                                <div class="space-y-3">
                                    @foreach($question['options'] as $optionKey => $option)
                                        @php
                                            $isStudentAnswer = ($question['student_answer'] === $optionKey);
                                            $isCorrectAnswer = ($question['correct_answer'] === $optionKey);
                                            
                                            $bgClass = 'bg-white';
                                            $borderClass = 'border-gray-200';
                                            $iconClass = '';
                                            
                                            if ($isCorrectAnswer) {
                                                $bgClass = 'bg-green-50';
                                                $borderClass = 'border-green-200';
                                                $iconClass = 'text-green-600';
                                            } elseif ($isStudentAnswer && !$isCorrectAnswer) {
                                                $bgClass = 'bg-red-50';
                                                $borderClass = 'border-red-200';
                                                $iconClass = 'text-red-600';
                                            }
                                        @endphp
                                        
                                        <div class="flex items-center gap-3 p-4 {{ $bgClass }} rounded-lg border {{ $borderClass }}">
                                            <div class="font-semibold text-gray-700">{{ $optionKey }}.</div>
                                            <div class="flex-1 text-gray-900">{{ $option }}</div>
                                            @if($isCorrectAnswer)
                                                <span class="material-symbols-outlined {{ $iconClass }}">check_circle</span>
                                            @elseif($isStudentAnswer)
                                                <span class="material-symbols-outlined {{ $iconClass }}">cancel</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Explanation (for incorrect answers) -->
                                @if(!$question['is_correct'] && isset($question['explanation']))
                                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="flex items-start gap-3">
                                            <span class="material-symbols-outlined text-blue-600 mt-0.5">lightbulb</span>
                                            <div>
                                                <div class="font-semibold text-blue-900 mb-1">Explanation:</div>
                                                <div class="text-blue-800 text-sm">{{ $question['explanation'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Performance Summary</h3>
                <p class="text-gray-600 mb-6">Overall analysis of student performance</p>

                <div class="space-y-4">
                    <!-- Strong Areas -->
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-green-600 mt-0.5">trending_up</span>
                            <div>
                                <div class="font-semibold text-green-900 mb-1">Strong Areas</div>
                                <div class="text-green-800 text-sm">
                                    {{ $summary->strong_areas ?? 'Linear Equations, Algebraic Expressions, Factoring, Radicals' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Areas for Improvement -->
                    <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-orange-600 mt-0.5">warning</span>
                            <div>
                                <div class="font-semibold text-orange-900 mb-1">Areas for Improvement</div>
                                <div class="text-orange-800 text-sm">
                                    {{ $summary->improvement_areas ?? 'Quadratic Equations, Exponents' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teacher Recommendation -->
                    <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-600">
                        <div class="font-semibold text-blue-900 mb-2">Teacher Recommendation:</div>
                        <div class="text-blue-800 text-sm">
                            {{ $summary->recommendation ?? 'Alice demonstrates excellent foundational skills in algebra with very fast solving times. However, she should review the concept of positive and negative roots in quadratic equations and the rules for multiplying exponents. Her quick pace suggests strong understanding, but attention to detail in these specific areas will help achieve perfect scores.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection