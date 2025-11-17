@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')
@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <!-- Back button -->
                <button onclick="window.location.href='{{ route('teacher.sections.show', ['section' => $section->name ?? $student->section]) }}'"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-medium">Back to Section</span>
                </button>
            </div>

            <!-- Student Header Card -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl border border-blue-100 p-8 mb-6">
                <div class="flex items-start justify-between">
                    <!-- Left Side - Student Info -->
                    <div class="flex items-start gap-6">
                        <!-- Student Avatar -->
                        <div class="relative">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-200 to-purple-200 flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="material-symbols-outlined text-3xl text-blue-600">person</span>
                            </div>
                        </div>
                        
                        <!-- Student Details -->
                        <div class="space-y-3">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ $student->name }}</h1>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="material-symbols-outlined text-gray-500 text-lg">mail</span>
                                    <span class="text-gray-600">{{ $student->email }}</span>
                                </div>
                            </div>
                            
                            <!-- Contact and Rank -->
                            <div class="flex items-center gap-6">
                                <div class="flex items-center gap-2 bg-white px-3 py-1 rounded-full border border-gray-200">
                                    <span class="material-symbols-outlined text-yellow-500 text-lg">emoji_events</span>
                                    <span class="text-sm font-semibold text-gray-700">Rank {{ $student->section_rank ?? 'Inactive' }} in {{ $section->name ?? $student->section_id }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Stats -->
                    <div class="grid grid-cols-3 gap-6 text-center">
                         <!-- Total Points -->
                         <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 min-w-[140px]">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($student->total_points) }}</div>
                            <div class="text-sm text-gray-600 mt-1">Total Points</div>
                        </div>

                        <!-- Overall Competency Score -->
                        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 min-w-[140px]">
                            <div class="text-2xl font-bold text-green-600">{{ $student->overall_score }}%</div>
                            <div class="text-sm text-gray-600 mt-1">Overall Competency Mastery</div>
                        </div>
                        
                        <!-- Avg Time per Question -->
                        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 min-w-[140px]">
                            <div class="text-2xl font-bold text-blue-600">{{ $student->avg_time_per_question }} min</div>
                            <div class="text-sm text-gray-600 mt-1">Avg Time/Q</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-4 gap-6">
                 <!-- Diagnostics Completed -->
                 <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->diagnostic_count }}/3</div>
                    <div class="text-sm text-gray-600 mt-2">Diagnostics</div>
                    <div class="text-xs text-gray-500 mt-1">
                        @foreach($student->diagnostics_completed as $name => $completed)
                            <span class="inline-flex items-center">
                                @if($completed)
                                    <span class="text-green-600">✓</span>
                                @else
                                    <span class="text-gray-400">✗</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            
                 <!-- Regular Assessments Completed -->
                 <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->regular_count }}</div>
                    <div class="text-sm text-gray-600 mt-2">Assessments</div>
                    <div class="text-xs text-gray-500 mt-1">Regular completed</div>
                </div>

                <!-- Best Streak -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->best_streak }}</div>
                    <div class="text-sm text-gray-600 mt-2">Best Streak</div>
                    <div class="text-xs text-gray-500 mt-1">Current: {{ $student->current_streak ?? 0 }}</div>
                </div>

                <!-- Time Spent -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->time_spent }}</div>
                    <div class="text-sm text-gray-600 mt-2">Time Spent</div>
                    <div class="text-xs text-gray-500 mt-1">hours</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-8 py-6">
            <div class="space-y-6">
                <!-- Navigation Tabs -->
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px" id="profileTabs">
                            <button data-tab="overview" class="tab-button px-6 py-4 border-b-2 border-blue-600 text-blue-600 font-medium text-sm active">
                                Overview
                            </button>
                            <button data-tab="assessments" class="tab-button px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                Assessments
                            </button>
                            <button data-tab="competencies" class="tab-button px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                Competencies
                            </button>
                            <button data-tab="struggling-areas" class="tab-button px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                Struggling Areas
                            </button>
                            <button data-tab="achievements" class="tab-button px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                Achievements
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Overview Content -->
                        <div id="overview-content" class="tab-content active">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">    
                                @php
                                    function getDifficultyDetails($difficulty) {
                                        switch ($difficulty) {
                                            case 'advanced':
                                                return [
                                                    'level' => 'Advanced',
                                                    'color' => 'text-green-600',
                                                    'bg' => 'bg-green-50',
                                                    'icon' => 'military_tech',
                                                    'status' => 'Excellent mastery'
                                                ];
                                            case 'intermediate':
                                                return [
                                                    'level' => 'Intermediate',
                                                    'color' => 'text-yellow-600',
                                                    'bg' => 'bg-yellow-50',
                                                    'icon' => 'lightbulb',
                                                    'status' => 'Making good progress'
                                                ];
                                            case 'beginner':
                                            default:
                                                return [
                                                    'level' => 'Beginner',
                                                    'color' => 'text-red-600',
                                                    'bg' => 'bg-red-50',
                                                    'icon' => 'school',
                                                    'status' => 'Needs improvement'
                                                ];
                                        }
                                    }
                                @endphp

                                <div class="space-y-4">
                                    @foreach ([
                                        ['label' => 'Number & Algebra', 'desc' => 'Basic arithmetic, number operations, and algebraic thinking', 'score' => $student->numerical_literacy_score, 'competency' => 'number_algebra'],
                                        ['label' => 'Measurement & Geometry', 'desc' => 'Shapes, space, and measurement concepts', 'score' => $student->geometric_reasoning_score, 'competency' => 'measurement_geometry'],
                                        ['label' => 'Data & Probability', 'desc' => 'Data analysis, statistics, and probability', 'score' => $student->problem_solving_score, 'competency' => 'data_probability'],
                                    ] as $item)
                                        @php
                                            $currentDifficulty = $student->competency_details[$item['competency']]['current_difficulty'] ?? 'beginner';
                                            $details = getDifficultyDetails($currentDifficulty);
                                        @endphp

                                        <div class="{{ $details['bg'] }} rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="font-medium text-gray-900">{{ $item['label'] }} Mastery</span>
                                                <span class="text-lg font-bold {{ $details['color'] }}">{{ $item['score'] }}%</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">{{ $item['desc'] }}</p>
                                            <div class="flex items-center text-xs {{ $details['color'] }}">
                                                <span class="material-symbols-outlined text-sm mr-1">{{ $details['icon'] }}</span>
                                                {{ $details['status'] }} ({{ $details['level'] }})
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Right Column - Last Assessment & Performance -->
                                <div class="space-y-6">
                                    <!-- Last Assessment -->
                                    <div class="bg-blue-50 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Last Assessment</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <div class="text-sm text-gray-600 mb-1">Assessment Name</div>
                                                <div class="text-xl font-bold text-gray-900">{{ $student->last_assessment_name }}</div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <div class="text-sm text-gray-600 mb-1">Score</div>
                                                    <div class="text-2xl font-bold text-gray-900">{{ $student->last_assessment_score }}%</div>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-600 mb-1">Date Taken</div>
                                                    <div class="text-lg font-semibold text-gray-900">{{ $student->last_assessment_date ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $student->last_assessment_time }} • {{ $student->last_assessment_accuracy }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Performance Trends -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trends</h3>
                                        <div class="grid grid-cols-3 gap-4">

                                            <!-- Mastery -->
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 mb-2">Mastery</div>
                                                @php
                                                    if ($student->overall_score == 0) {
                                                        $masteryText = 'None';
                                                        $masteryColor = 'text-black';
                                                    }elseif ($student->overall_score >= 85) {
                                                        $masteryText = 'Excellent';
                                                        $masteryColor = 'text-green-600';
                                                    } elseif ($student->overall_score >= 70) {
                                                        $masteryText = 'Good';
                                                        $masteryColor = 'text-blue-600';
                                                    } else {
                                                        $masteryText = 'Developing';
                                                        $masteryColor = 'text-yellow-600';
                                                    }
                                                @endphp
                                                <div class="text-lg font-bold {{ $masteryColor }}">
                                                    {{ $masteryText }}
                                                </div>
                                            </div>

                                            <!-- Speed -->
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 mb-2">Speed</div>
                                                @php
                                                    if ($student->avg_time_per_question == 0) {
                                                        $speedText = 'None';
                                                        $speedColor = 'text-black';
                                                    } elseif ($student->avg_time_per_question < 1.5) {
                                                        $speedText = 'Excellent';
                                                        $speedColor = 'text-green-600';
                                                    } elseif ($student->avg_time_per_question < 2.5) {
                                                        $speedText = 'Good';
                                                        $speedColor = 'text-blue-600';
                                                    } else {
                                                        $speedText = 'Improving';
                                                        $speedColor = 'text-orange-600';
                                                    }
                                                @endphp
                                                <div class="text-lg font-bold {{ $speedColor }}">
                                                    {{ $speedText }}
                                                </div>
                                            </div>

                                            <!-- Consistency -->
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 mb-2">Consistency</div>
                                                @php
                                                    if ($student->current_streak == 0) {
                                                        $consistencyText = 'None';
                                                        $consistencyColor = 'text-black';
                                                    } elseif ($student->current_streak >= 10) {
                                                        $consistencyText = 'Very High';
                                                        $consistencyColor = 'text-green-600';
                                                    } elseif ($student->current_streak >= 5) {
                                                        $consistencyText = 'High';
                                                        $consistencyColor = 'text-blue-600';
                                                    } else {
                                                        $consistencyText = 'Moderate';
                                                        $consistencyColor = 'text-yellow-600';
                                                    }
                                                @endphp
                                                <div class="text-lg font-bold {{ $consistencyColor }}">
                                                    {{ $consistencyText }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assessments Content -->
                        <div id="assessments-content" class="tab-content hidden">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Assessment History</h3>
                                <p class="text-gray-600 mb-6">Complete record of all assessments taken - Click to view detailed results</p>
                                
                                <!-- Assessment Type Tabs -->
                                <div class="bg-white rounded-xl border border-gray-200 mb-6">
                                    <div class="border-b border-gray-200">
                                        <nav class="flex -mb-px" id="assessmentTabs">
                                            <button data-assessment-tab="all" class="assessment-tab-button px-6 py-4 border-b-2 border-blue-600 text-blue-600 font-medium text-sm active">
                                                All Assessments
                                            </button>
                                            <button data-assessment-tab="regular" class="assessment-tab-button px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                                Regular ({{ $student->assessments->where('type', '!=', 'Diagnostic')->count() }})
                                            </button>
                                            <button data-assessment-tab="diagnostic" class="assessment-tab-button px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                                Diagnostic ({{ $student->assessments->where('type', 'Diagnostic')->count() }})
                                            </button>
                                        </nav>
                                    </div>

                                    <!-- Assessment Tab Content -->
                                    <div class="p-6">
                                        <!-- All Assessments -->
                                        <div id="all-assessments-content" class="assessment-tab-content active">
                                            <div class="space-y-4">
                                                @forelse($student->assessments as $assessment)
                                                    <a href="{{ route('teacher.assessments.review', ['student' => $student->id, 'assessment' => $assessment->id]) }}" 
                                                       class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:border-blue-200 border border-gray-200 transition-all cursor-pointer group">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $assessment->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $assessment->date }} • {{ $assessment->time }} • {{ $assessment->type }}</div>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <div class="text-right">
                                                                <div class="text-lg font-bold text-gray-900">{{ $assessment->score }}</div>
                                                                <div class="text-xs text-gray-500">Score</div>
                                                            </div>
                                                            <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">chevron_right</span>
                                                        </div>
                                                    </a>
                                                @empty
                                                    <div class="text-center py-8 text-gray-500">
                                                        <span class="material-symbols-outlined text-4xl mb-2">assignment</span>
                                                        <p>No assessments completed yet</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <!-- Regular Assessments -->
                                        <div id="regular-assessments-content" class="assessment-tab-content hidden">
                                            <div class="space-y-4">
                                                @forelse($student->assessments->where('type', '!=', 'Diagnostic') as $assessment)
                                                    <a href="{{ route('teacher.assessments.review', ['student' => $student->id, 'assessment' => $assessment->id]) }}" 
                                                       class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:border-blue-200 border border-gray-200 transition-all cursor-pointer group">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $assessment->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $assessment->date }} • {{ $assessment->time }} • {{ $assessment->type }}</div>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <div class="text-right">
                                                                <div class="text-lg font-bold text-gray-900">{{ $assessment->score }}</div>
                                                                <div class="text-xs text-gray-500">Score</div>
                                                            </div>
                                                            <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">chevron_right</span>
                                                        </div>
                                                    </a>
                                                @empty
                                                    <div class="text-center py-8 text-gray-500">
                                                        <span class="material-symbols-outlined text-4xl mb-2">assignment</span>
                                                        <p>No regular assessments completed yet</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <!-- Diagnostic Assessments -->
                                        <div id="diagnostic-assessments-content" class="assessment-tab-content hidden">
                                            <div class="space-y-4">
                                                @forelse($student->assessments->where('type', 'Diagnostic') as $assessment)
                                                    <a href="{{ route('teacher.assessments.review', ['student' => $student->id, 'assessment' => $assessment->id]) }}" 
                                                       class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:border-blue-200 border border-gray-200 transition-all cursor-pointer group">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $assessment->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $assessment->date }} • {{ $assessment->time }} • {{ $assessment->type }}</div>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <div class="text-right">
                                                                <div class="text-lg font-bold text-gray-900">{{ $assessment->score }}</div>
                                                                <div class="text-xs text-gray-500">Score</div>
                                                            </div>
                                                            <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">chevron_right</span>
                                                        </div>
                                                    </a>
                                                @empty
                                                    <div class="text-center py-8 text-gray-500">
                                                        <span class="material-symbols-outlined text-4xl mb-2">assignment</span>
                                                        <p>No diagnostic assessments completed yet</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Competencies Content -->
                        <div id="competencies-content" class="tab-content hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Competency Breakdown</h3>
                            <p class="text-gray-600 mb-6">Detailed performance metrics for each competency area</p>
                            
                            <div class="space-y-6">
                                @foreach(['number_algebra', 'measurement_geometry', 'data_probability'] as $competency)
                                    @php
                                        $details = $student->competency_details[$competency] ?? null;
                                        $competencyName = $details['name'] ?? ucfirst(str_replace('_', ' & ', $competency));
                                        $accuracy = $details['accuracy'] ?? 0;
                                        $bktScore = $details['bkt_score'] ?? 0;
                                        $avgTime = $details['avg_response_time'] ?? 0;
                                        $totalQuestions = $details['total_questions'] ?? 0;
                                        $correctAnswers = $details['correct_answers'] ?? 0;
                                        
                                        // Get the appropriate score for progress bar
                                        $displayScore = 0;
                                        switch($competency) {
                                            case 'number_algebra':
                                                $displayScore = $student->numerical_literacy_score ?? 0;
                                                break;
                                            case 'measurement_geometry':
                                                $displayScore = $student->geometric_reasoning_score ?? 0;
                                                break;
                                            case 'data_probability':
                                                $displayScore = $student->problem_solving_score ?? 0;
                                                break;
                                        }
                                       
                                        // Get current difficulty from database
                                        $currentDifficulty = $details['current_difficulty'] ?? 'beginner';
                                        
                                        // Determine color based on difficulty level
                                        $scoreColor = $currentDifficulty === 'advanced' ? 'text-green-600' : ($currentDifficulty === 'intermediate' ? 'text-yellow-600' : 'text-red-600');
                                        $barColor = $currentDifficulty === 'advanced' ? 'bg-green-600' : ($currentDifficulty === 'intermediate' ? 'bg-yellow-600' : 'bg-red-600');
                                    @endphp
                                    
                                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $competencyName }} Mastery</h4>
                                                <p class="text-sm text-gray-600">
                                                    @if($competency === 'number_algebra')
                                                        Basic arithmetic, number operations, and algebraic thinking
                                                    @elseif($competency === 'measurement_geometry')
                                                        Shapes, space, and measurement concepts
                                                    @else
                                                        Data analysis, statistics, and probability
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold {{ $scoreColor }}">{{ $displayScore }}%</div>
                                                <div class="text-xs text-gray-500">Mastery Score</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $displayScore }}%"></div>
                                        </div>
                                        
                                        <!-- Detailed Metrics Grid -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <!-- Accuracy -->
                                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                                <div class="text-2xl font-bold text-blue-600">{{ $accuracy }}%</div>
                                                <div class="text-sm text-gray-600 mt-1">Accuracy</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $correctAnswers }}/{{ $totalQuestions }} correct</div>
                                            </div>
                                            
                                            <!-- BKT Score -->
                                            <div class="bg-purple-50 rounded-lg p-4 text-center">
                                                <div class="text-2xl font-bold text-purple-600">{{ $bktScore }}%</div>
                                                <div class="text-sm text-gray-600 mt-1">BKT Score</div>
                                                <div class="text-xs text-gray-500 mt-1"></div>
                                            </div>
                                            
                                            <!-- Average Response Time -->
                                            <div class="bg-orange-50 rounded-lg p-4 text-center">
                                                <div class="text-2xl font-bold text-orange-600">{{ $avgTime }} min</div>
                                                <div class="text-sm text-gray-600 mt-1">Avg Response Time</div>
                                                <div class="text-xs text-gray-500 mt-1">Per Question</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Performance Indicators -->
                                        <div class="mt-4 flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-4">
                                                <span class="flex items-center gap-1">
                                                    @php
                                                        if ($accuracy >= 90) {
                                                            $accuracyLevel = 'Highly Proficient';
                                                            $accuracyColor = 'text-green-500';
                                                        } elseif ($accuracy >= 75) {
                                                            $accuracyLevel = 'Proficient';
                                                            $accuracyColor = 'text-blue-500';
                                                        } elseif ($accuracy >= 50) {
                                                            $accuracyLevel = 'Nearly Proficient';
                                                            $accuracyColor = 'text-yellow-500';
                                                        } elseif ($accuracy >= 25) {
                                                            $accuracyLevel = 'Low Proficient';
                                                            $accuracyColor = 'text-orange-500';
                                                        } else {
                                                            $accuracyLevel = 'Not Proficient';
                                                            $accuracyColor = 'text-red-500';
                                                        }
                                                    @endphp
                                                    <span class="inline-block w-2.5 h-2.5 rounded-full {{ $accuracyColor }}"></span>
                                                    <span class="text-gray-700">
                                                        Accuracy: <span class="font-medium {{ $accuracyColor }}">{{ $accuracyLevel }}</span>
                                                    </span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    @php
                                                        switch (strtolower($currentDifficulty)) {
                                                            case 'beginner':
                                                                $difficultyColor = 'text-green-600';
                                                                break;
                                                            case 'intermediate':
                                                                $difficultyColor = 'text-yellow-600';
                                                                break;
                                                            case 'advanced':
                                                                $difficultyColor = 'text-red-600';
                                                                break;
                                                            default:
                                                                $difficultyColor = 'text-gray-600';
                                                                break;
                                                        }
                                                    @endphp

                                                    <span>
                                                        Mastery: <span class="{{ $difficultyColor }}"> {{ ucfirst($currentDifficulty) }}</span>
                                                    </span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <span class="text-gray-600">Speed: {{ $avgTime <= 1.5 ? 'Fast' : ($avgTime <= 3 ? 'Moderate' : 'Slow') }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Struggling Areas Content -->
                        <div id="struggling-areas-content" class="tab-content hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Areas Needing Attention</h3>
                            <p class="text-gray-600 mb-6">Topics where the student could benefit from additional support</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @forelse($student->struggling_areas as $area)
                                    <div class="p-6 bg-red-50 rounded-lg border border-red-100">
                                        <div class="mb-4">
                                            <div class="font-medium text-gray-900 text-lg mb-1">{{ $area['topic'] }}</div>
                                            <div class="text-sm text-gray-600">{{ $area['area'] }}</div>
                                        </div>
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="text-2xl font-bold text-red-600">{{ $area['score'] }}</div>
                                            <div class="text-sm text-red-600">{{ $area['change'] }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-3">{{ $area['attempts'] }}</div>
                                        <div class="w-full bg-red-200 rounded-full h-2">
                                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ str_replace('%', '', $area['score']) }}%"></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-3 text-center py-8 text-gray-500">
                                        <span class="material-symbols-outlined text-4xl mb-2">check_circle</span>
                                        <p>No struggling areas identified - Great performance!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Achievements Content -->
                        <div id="achievements-content" class="tab-content hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Earned Achievements -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Earned Achievements</h3>
                                    <p class="text-gray-600 mb-6">Accomplishments unlocked by the student</p>
                                    
                                    <div class="space-y-4">
                                        @forelse($student->achievements as $achievement)
                                            <div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-yellow-600 text-xl">emoji_events</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900 text-lg">{{ $achievement['name'] }}</div>
                                                    <div class="text-sm text-gray-600">{{ $achievement['desc'] }}</div>
                                                    <div class="text-xs text-gray-500 mt-2">{{ $achievement['date'] }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8 text-gray-500">
                                                <span class="material-symbols-outlined text-4xl mb-2">workspace_premium</span>
                                                <p>No achievements earned yet - Keep practicing!</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Goals to Unlock -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Goals to Unlock</h3>
                                    <p class="text-gray-600 mb-6">Achievements available to earn</p>
                                    
                                    <div class="space-y-4">
                                        @php
                                        $goals = [
                                            [
                                                'name' => 'Math Genius',
                                                'desc' => 'Score 95%+ on 10 consecutive assessments',
                                                'current' => min(10, floor($student->assessment_count * 0.7)),
                                                'total' => 10
                                            ],
                                            [
                                                'name' => 'Time Master',
                                                'desc' => 'Complete 20 assessments under target time',
                                                'current' => min(20, floor($student->assessment_count * 0.6)),
                                                'total' => 20
                                            ],
                                            [
                                                'name' => 'Streak Champion',
                                                'desc' => 'Maintain a 30-day learning streak',
                                                'current' => $student->longest_streak ?? 0,
                                                'total' => 30
                                            ]
                                        ];
                                        @endphp
                                        
                                        @foreach($goals as $goal)
                                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="font-medium text-gray-900">{{ $goal['name'] }}</div>
                                                    <div class="text-sm font-semibold text-blue-600">{{ $goal['current'] }}/{{ $goal['total'] }}</div>
                                                </div>
                                                <div class="text-sm text-gray-600 mb-3">{{ $goal['desc'] }}</div>
                                                @php
                                                    $percentage = ($goal['current'] / $goal['total']) * 100;
                                                @endphp
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-button {
            border-bottom: 2px solid transparent;
        }
        .tab-button.active {
            border-bottom-color: #2563eb;
            color: #2563eb;
        }
        .assessment-tab-content {
            display: none;
        }
        .assessment-tab-content.active {
            display: block;
        }
        .assessment-tab-button {
            border-bottom: 2px solid transparent;
        }
        .assessment-tab-button.active {
            border-bottom-color: #2563eb;
            color: #2563eb;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            function switchTab(tabName) {
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });
                
                // Remove active class from all tabs
                tabs.forEach(tab => {
                    tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    tab.classList.add('text-gray-500');
                });
                
                // Show selected tab content
                const activeContent = document.getElementById(`${tabName}-content`);
                if (activeContent) {
                    activeContent.classList.add('active');
                    activeContent.classList.remove('hidden');
                }
                
                // Activate selected tab
                const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
                if (activeTab) {
                    activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
                    activeTab.classList.remove('text-gray-500');
                }
            }
            
            // Add click event listeners to tabs
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    switchTab(tabName);
                });
            });
            
            // Initialize with overview tab active
            switchTab('overview');
            
            // Assessment tabs functionality
            const assessmentTabs = document.querySelectorAll('.assessment-tab-button');
            const assessmentTabContents = document.querySelectorAll('.assessment-tab-content');
            
            function switchAssessmentTab(tabName) {
                // Hide all assessment tab contents
                assessmentTabContents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });
                
                // Remove active class from all assessment tabs
                assessmentTabs.forEach(tab => {
                    tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    tab.classList.add('text-gray-500');
                });
                
                // Show selected assessment tab content
                const activeContent = document.getElementById(`${tabName}-assessments-content`);
                if (activeContent) {
                    activeContent.classList.add('active');
                    activeContent.classList.remove('hidden');
                }
                
                // Activate selected assessment tab
                const activeTab = document.querySelector(`[data-assessment-tab="${tabName}"]`);
                if (activeTab) {
                    activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
                    activeTab.classList.remove('text-gray-500');
                }
            }
            
            // Add click event listeners to assessment tabs
            assessmentTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-assessment-tab');
                    switchAssessmentTab(tabName);
                });
            });
            
            // Initialize with all assessments tab active
            switchAssessmentTab('all');
        });
    </script>
@endsection