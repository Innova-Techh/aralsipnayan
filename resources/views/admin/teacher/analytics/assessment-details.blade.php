@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <!-- Header with Back Button -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center mb-2">
                <a href="{{ route('teacher.analytics') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Question Analysis</h1>
            </div>
            <div class="flex items-center text-sm text-gray-600">
                <span class="material-symbols-outlined text-sm mr-1">category</span>
                <span class="mr-4">{{ ucfirst(str_replace('_', ' & ', $competency)) }}</span>
                <span class="material-symbols-outlined text-sm mr-1">quiz</span>
                <span class="mr-4">{{ ucfirst($assessmentType) }} Assessment</span>
                <span class="material-symbols-outlined text-sm mr-1">schedule</span>
                <span>{{ \Carbon\Carbon::parse($assessment->latest_completed_at)->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold text-gray-900">
                {{ $assessmentStats['total_assessments'] }}
            </div>
            <div class="text-sm text-gray-500">Total Attempts</div>
        </div>
    </div>

    <!-- Section Filter and Sorting Controls -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Section Filter -->
                <div class="flex items-center">
                    <label class="text-sm font-medium text-gray-700 mr-3">Filter by Section:</label>
                    <form method="GET" class="flex items-center">
                        <input type="hidden" name="sort" value="{{ $sortBy }}">
                        <select name="section" onchange="this.form.submit()" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" {{ $selectedSection === 'all' ? 'selected' : '' }}>All Sections</option>
                            @foreach($teacherSections as $section)
                                <option value="{{ $section }}" {{ $selectedSection === $section ? 'selected' : '' }}>
                                    Section {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            
            <!-- Sorting Options -->
            <div class="flex items-center">
                <label class="text-sm font-medium text-gray-700 mr-3">Sort by:</label>
                <form method="GET" class="flex items-center">
                    <input type="hidden" name="section" value="{{ $selectedSection }}">
                    <select name="sort" onchange="this.form.submit()" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="accuracy" {{ $sortBy === 'accuracy' ? 'selected' : '' }}>Accuracy</option>
                        <option value="difficulty" {{ $sortBy === 'difficulty' ? 'selected' : '' }}>Difficulty Level</option>
                        <option value="time" {{ $sortBy === 'time' ? 'selected' : '' }}>Response Time</option>
                        <option value="bkt_improvement" {{ $sortBy === 'bkt_improvement' ? 'selected' : '' }}>BKT Improvement</option>
                    </select>
                </form>
            </div>
        </div>
        
        @if($selectedSection !== 'all')
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">info</span>
                <p class="text-sm text-blue-800">
                    Showing data for <strong>Section {{ $selectedSection }}</strong> only. 
                    <a href="?section=all&sort={{ $sortBy }}" class="text-blue-600 hover:text-blue-800 underline">View all sections</a>
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Assessment Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="material-symbols-outlined text-blue-600">quiz</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Questions Analyzed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($questionDetails) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="material-symbols-outlined text-green-600">trending_up</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg. Accuracy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $assessmentStats['accuracy'] }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <span class="material-symbols-outlined text-yellow-600">timer</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg. Response Time</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $assessmentStats['avg_time_per_question'] }}s</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <span class="material-symbols-outlined text-purple-600">people</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Attempts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $assessmentStats['total_assessments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- BKT Overview -->
    @php
        $totalBktBefore = 0;
        $totalBktAfter = 0;
        $bktQuestionCount = 0;
        foreach ($questionDetails as $details) {
            if (isset($details['avg_bkt_before']) && isset($details['avg_bkt_after'])) {
                $totalBktBefore += $details['avg_bkt_before'];
                $totalBktAfter += $details['avg_bkt_after'];
                $bktQuestionCount++;
            }
        }
        $avgBktBefore = $bktQuestionCount > 0 ? $totalBktBefore / $bktQuestionCount : 0;
        $avgBktAfter = $bktQuestionCount > 0 ? $totalBktAfter / $bktQuestionCount : 0;
        $overallBktImprovement = $avgBktAfter - $avgBktBefore;
    @endphp
    
    @if($bktQuestionCount > 0)
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow p-6 mb-8 border border-blue-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <span class="material-symbols-outlined text-blue-600 mr-2">psychology</span>
            BKT Knowledge Tracing Overview
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($avgBktBefore, 4) }}</div>
                <div class="text-sm font-medium text-gray-700">Average BKT Before</div>
                <div class="text-xs text-gray-500">Initial knowledge level</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($avgBktAfter, 4) }}</div>
                <div class="text-sm font-medium text-gray-700">Average BKT After</div>
                <div class="text-xs text-gray-500">Final knowledge level</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold {{ $overallBktImprovement > 0 ? 'text-green-600' : ($overallBktImprovement < 0 ? 'text-red-600' : 'text-gray-600') }}">
                    {{ $overallBktImprovement > 0 ? '+' : '' }}{{ number_format($overallBktImprovement, 4) }}
                </div>
                <div class="text-sm font-medium text-gray-700">Overall Improvement</div>
                <div class="text-xs text-gray-500">
                    {{ $overallBktImprovement > 0 ? 'Knowledge gained' : ($overallBktImprovement < 0 ? 'Knowledge lost' : 'No change') }}
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Overall Knowledge Progress</span>
                <span>{{ number_format($avgBktAfter * 100, 1) }}% mastery</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full" 
                     style="width: {{ min(max($avgBktAfter * 100, 0), 100) }}%"></div>
            </div>
            <div class="text-xs text-gray-500 mt-2">
                BKT scores range from 0.0 (no mastery) to 1.0 (complete mastery). This assessment covered {{ $bktQuestionCount }} questions with BKT tracking.
            </div>
        </div>
    </div>
    @endif

    <!-- Difficulty Breakdown -->
    @if($assessmentStats['difficulty_breakdown']->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Difficulty Level</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($assessmentStats['difficulty_breakdown'] as $difficulty => $stats)
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 capitalize">{{ $difficulty }}</h4>
                    <span class="text-sm text-gray-500">{{ $stats->total }} questions</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Accuracy:</span>
                    <span class="font-medium 
                        {{ round(($stats->correct / $stats->total) * 100) >= 80 ? 'text-green-600' : 
                           (round(($stats->correct / $stats->total) * 100) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ round(($stats->correct / $stats->total) * 100, 1) }}%
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Avg. Time:</span>
                    <span class="font-medium">{{ round($stats->avg_time, 1) }}s</span>
                </div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" 
                         style="width: {{ round(($stats->correct / $stats->total) * 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Section Performance Breakdown -->
    @if($selectedSection === 'all' && count($teacherSections) > 1)
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Section</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($teacherSections as $section)
            @php
                // Get section-specific stats
                $sectionStats = \DB::table('assessments')
                    ->join('student_profile', 'assessments.user_id', '=', 'student_profile.user_id')
                    ->where('assessments.competency', $competency)
                    ->where('assessments.difficulty_level', $assessmentType)
                    ->where('assessments.assessment_type', 'regular')
                    ->where('assessments.status', 'completed')
                    ->where('student_profile.section', $section)
                    ->selectRaw('
                        COUNT(*) as total_assessments,
                        AVG(assessments.accuracy_percentage) as avg_accuracy,
                        AVG(assessments.average_response_time) as avg_response_time
                    ')
                    ->first();
            @endphp
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-900">Section {{ $section }}</h4>
                    <span class="text-sm text-gray-500">{{ $sectionStats->total_assessments ?? 0 }} attempts</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Avg. Accuracy:</span>
                        <span class="font-medium 
                            {{ ($sectionStats->avg_accuracy ?? 0) >= 80 ? 'text-green-600' : 
                               (($sectionStats->avg_accuracy ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ round($sectionStats->avg_accuracy ?? 0, 1) }}%
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Avg. Time:</span>
                        <span class="font-medium">{{ round($sectionStats->avg_response_time ?? 0, 1) }}s</span>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="?section={{ $section }}&sort={{ $sortBy }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Section Details →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Question Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Question-by-Question Analysis</h3>
        <div class="space-y-8">
            @foreach($questionDetails as $questionId => $details)
            <div class="border-l-4 {{ $details['accuracy_rate'] >= 70 ? 'border-green-500' : ($details['accuracy_rate'] >= 50 ? 'border-yellow-500' : 'border-red-500') }} pl-6 py-4">
                <!-- Question Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            @if($details['accuracy_rate'] < 20)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class=" text-xs mr-1">⚠︎</span>
                                High Difficulty
                            </span>
                            @endif
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $details['accuracy_rate'] >= 70 ? 'bg-green-100 text-green-800' : ($details['accuracy_rate'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $details['accuracy_rate'] }}% Accuracy
                            </span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $details['question']->difficulty_level === 'beginner' ? 'bg-blue-100 text-blue-800' : 
                                   ($details['question']->difficulty_level === 'intermediate' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }} capitalize">
                                {{ $details['question']->difficulty_level }}
                            </span>
                            <span class="ml-2 text-sm text-gray-500">
                                {{ $details['question']->topic_tag }}
                            </span>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">
                            {{ $details['question']->question_text }}
                        </h4>
                        @if($details['accuracy_rate'] < 20)
                        <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start">
                                <span class="material-symbols-outlined text-red-600 mr-2 mt-0.5">info</span>
                                <div>
                                    <p class="text-sm font-medium text-red-800">High Difficulty Alert</p>
                                    <p class="text-sm text-red-700 mt-1">Many students find this question difficult. Consider reviewing the question clarity, explanation, or prerequisite knowledge requirements.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="ml-4 text-right">
                        <div class="text-sm text-gray-500">Avg. Response Time</div>
                        <div class="font-semibold">{{ $details['avg_response_time'] }}s</div>
                    </div>
                </div>

                <!-- Answer Choices (for multiple choice) -->
                @if($details['question']->question_type === 'multiple_choice')
                <div class="mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(['choice_a' => 'A', 'choice_b' => 'B', 'choice_c' => 'C', 'choice_d' => 'D'] as $choice => $label)
                            @if($details['question']->$choice)
                            <div class="flex items-center p-3 border rounded-lg {{ $details['question']->correct_answer === $label ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                <span class="font-semibold mr-3">{{ $label }}.</span>
                                <span>{{ $details['question']->$choice }}</span>
                                @if($details['question']->correct_answer === $label)
                                    <span class="ml-auto text-green-600">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                    </span>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Correct Answer (for non-multiple choice) -->
                @if($details['question']->question_type !== 'multiple_choice')
                <div class="mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correct Answer:</label>
                        <div class="p-3 border border-green-500 bg-green-50 rounded-lg">
                            {{ $details['question']->correct_answer }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Overall Statistics for this Question -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h5 class="font-medium text-gray-900 mb-3">Overall Performance Statistics</h5>
                    <div class="grid grid-cols-3 md:grid-cols-3 gap-2">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $details['total_attempts'] }}</div>
                            <div class="text-sm text-gray-500">Total Attempts</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $details['correct_answers'] }}</div>
                            <div class="text-sm text-gray-500">Correct Answers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $details['wrong_answers'] }}</div>
                            <div class="text-sm text-gray-500">Wrong Answers</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Overall Performance</span>
                            <span>{{ $details['accuracy_rate'] }}% correct</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $details['accuracy_rate'] >= 70 ? 'bg-green-600' : ($details['accuracy_rate'] >= 50 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                 style="width: {{ $details['accuracy_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Unique Students:</span>
                            <span class="font-medium">{{ $details['unique_students'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Avg. Time Score:</span>
                            <span class="font-medium">{{ $details['avg_time_score'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- BKT Analysis Section -->
                @if(isset($details['avg_bkt_before']) && isset($details['avg_bkt_after']))
                <div class="bg-blue-50 rounded-lg p-4 mb-4 border border-blue-200">
                    <h5 class="font-medium text-gray-900 mb-3 flex items-center">
                        <span class="material-symbols-outlined text-blue-600 mr-2">psychology</span>
                        BKT (Bayesian Knowledge Tracing) Analysis
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ number_format($details['avg_bkt_before'], 4) }}</div>
                            <div class="text-sm text-gray-600">Avg. BKT Before</div>
                            <div class="text-xs text-gray-500">Knowledge before question</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ number_format($details['avg_bkt_after'], 4) }}</div>
                            <div class="text-sm text-gray-600">Avg. BKT After</div>
                            <div class="text-xs text-gray-500">Knowledge after question</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold {{ $details['bkt_improvement'] > 0 ? 'text-green-600' : ($details['bkt_improvement'] < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                {{ $details['bkt_improvement'] > 0 ? '+' : '' }}{{ number_format($details['bkt_improvement'], 4) }}
                            </div>
                            <div class="text-sm text-gray-600">BKT Improvement</div>
                            <div class="text-xs text-gray-500">
                                {{ $details['bkt_improvement'] > 0 ? 'Knowledge increased' : ($details['bkt_improvement'] < 0 ? 'Knowledge decreased' : 'No change') }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Knowledge Progress</span>
                            <span>{{ $details['bkt_improvement'] > 0 ? '+' : '' }}{{ number_format($details['bkt_improvement'] * 100, 2) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ min(max(($details['avg_bkt_after'] * 100), 0), 100) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            BKT Score represents the probability (0-1) that a student has mastered this concept
                        </div>
                    </div>
                </div>
                @endif

                <!-- Explanation -->
                @if($details['question']->explanation)
                <div class="mt-4">
                    <h5 class="font-medium text-gray-900 mb-2">Explanation:</h5>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-gray-700">{{ $details['question']->explanation }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
