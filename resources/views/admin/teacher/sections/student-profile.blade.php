@extends('admin.teacher.layouts.app')

@section('title', 'Student Profile - Aralsipnayan')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <!-- Back button -->
                <button onclick="window.location.href='{{ route('teacher.sections.show', ['section' => $section->name ?? $student->section_id ?? '']) }}'"
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
    <!-- Rank Badge - Positioned at bottom right -->
                                <div class="absolute -bottom-2 -right-2 bg-yellow-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold shadow-lg border-2 border-white">
                                    #1
                                </div>
                    </div>
                        
                        <!-- Student Details -->
                        <div class="space-y-3">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ $student->name ?? 'Alice Johnson' }}</h1>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="material-symbols-outlined text-gray-500 text-lg">mail</span>
                                    <span class="text-gray-600">{{ $student->email ?? 'alicejohnson@school.edu' }}</span>
                                </div>
                            </div>
                            
                            <!-- Contact and Rank -->
                            <div class="flex items-center gap-6">
                            
                                <div class="flex items-center gap-2 bg-white px-3 py-1 rounded-full border border-gray-200">
                                    <span class="material-symbols-outlined text-yellow-500 text-lg">emoji_events</span>
                                    <span class="text-sm font-semibold text-gray-700">Rank #1 in {{ $section->name ?? 'MATH101-A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Stats -->
                    <div class="grid grid-cols-2 gap-6 text-center">
                        <!-- Overall Score -->
                        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 min-w-[140px]">
                            <div class="text-2xl font-bold text-green-600">{{ $student->overall_score ?? 94.5 }}%</div>
                            <div class="text-sm text-gray-600 mt-1">Overall Score</div>
                        </div>
                        
                        <!-- Avg Time per Question -->
                        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 min-w-[140px]">
                            <div class="text-2xl font-bold text-blue-600">{{ $student->avg_time_per_question ?? 1.8 }} min</div>
                            <div class="text-sm text-gray-600 mt-1">Avg Time/Q</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-4 gap-6">
                <!-- Total Points -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($student->total_points ?? 2847) }}</div>
                    <div class="text-sm text-gray-600 mt-2">Total Points</div>
                    <div class="flex items-center justify-center gap-1 text-green-600 text-xs mt-1">
                        <span class="material-symbols-outlined text-sm">trending_up</span>
                        <span>+12% this week</span>
                    </div>
                </div>

                <!-- Assessments -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->assessment_count ?? 12 }}</div>
                    <div class="text-sm text-gray-600 mt-2">Assessments</div>
                    <div class="text-xs text-gray-500 mt-1">8 completed</div>
                </div>

                <!-- Best Streak -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->best_streak ?? 15 }}</div>
                    <div class="text-sm text-gray-600 mt-2">Best Streak</div>
                    <div class="text-xs text-gray-500 mt-1">Current: {{ $student->current_streak ?? 5 }}</div>
                </div>

                <!-- Time Spent -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $student->time_spent ?? 47.5 }}</div>
                    <div class="text-sm text-gray-600 mt-2">Time Spent</div>
                    <div class="text-xs text-gray-500 mt-1">hours</div>
                </div>
            </div>
        </div>

        <!-- Main Content - Full Width -->
        <div class="px-8 py-6">
            <div class="space-y-6">
                <!-- Navigation Tabs -->
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px" id="profileTabs">
                            <button data-tab="overview" class="tab-button px-6 py-4 border-b-2 border-blue-600 text-blue-600 font-medium text-sm">
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
                                <!-- Left Column - Competencies -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Competency Scores</h3>
                                    <div class="space-y-4">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="font-medium text-gray-900">Numerical Literacy</span>
                                                <span class="text-lg font-bold text-green-600">{{ $student->numerical_literacy_score ?? 96 }}%</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Basic arithmetic and number operations</p>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +6% improvement
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="font-medium text-gray-900">Algebraic Thinking</span>
                                                <span class="text-lg font-bold text-green-600">{{ $student->algebraic_thinking_score ?? 89 }}%</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Patterns, equations, and relationships</p>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +12% improvement
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="font-medium text-gray-900">Geometric Reasoning</span>
                                                <span class="text-lg font-bold text-green-600">{{ $student->geometric_reasoning_score ?? 94 }}%</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Shapes, space, and measurement</p>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +5% improvement
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="font-medium text-gray-900">Problem Solving</span>
                                                <span class="text-lg font-bold text-green-600">{{ $student->problem_solving_score ?? 91 }}%</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">Critical thinking and strategy</p>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +7% improvement
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Last Assessment & Performance -->
                                <div class="space-y-6">
                                    <!-- Last Assessment -->
                                    <div class="bg-blue-50 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Last Assessment</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <div class="text-sm text-gray-600 mb-1">Assessment Name</div>
                                                <div class="text-xl font-bold text-gray-900">{{ $student->last_assessment_name ?? 'Algebra Basics Quiz' }}</div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <div class="text-sm text-gray-600 mb-1">Score</div>
                                                    <div class="text-2xl font-bold text-gray-900">{{ $student->last_assessment_score ?? 92 }}%</div>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-600 mb-1">Date Taken</div>
                                                    <div class="text-lg font-semibold text-gray-900">{{ $student->last_assessment_date ?? '2024-01-18' }}</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $student->last_assessment_time ?? '14.5 min' }} • {{ $student->last_assessment_accuracy ?? '88%' }} Accuracy
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Performance Trends -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trends</h3>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 mb-2">Consistency</div>
                                                <div class="text-lg font-bold text-green-600">Excellent</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 mb-2">Time Management</div>
                                                <div class="text-lg font-bold text-green-600">Improved</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 mb-2">Engagement Level</div>
                                                <div class="text-lg font-bold text-green-600">Very High</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- This replaces the Assessments Content section in student-profile.blade.php -->
<!-- Find and replace the entire "Assessments Content" div -->

<!-- Assessments Content -->
<div id="assessments-content" class="tab-content hidden">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Assessment History</h3>
        <p class="text-gray-600 mb-6">Complete record of all assessments taken - Click to view detailed results</p>
        
        <div class="space-y-4">
            @forelse($student->assessments ?? [] as $assessment)
                <a href="{{ route('teacher.assessments.review', ['student' => $student->id, 'assessment' => $assessment['id'] ?? 0]) }}" 
                   class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:border-blue-200 border border-gray-200 transition-all cursor-pointer group">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $assessment['name'] }}</div>
                        <div class="text-sm text-gray-500">{{ $assessment['date'] }} • {{ $assessment['time'] }} • {{ $assessment['type'] }}</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">{{ $assessment['score'] }}</div>
                            <div class="text-xs text-gray-500">Score</div>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">chevron_right</span>
                    </div>
                </a>
            @empty
                @foreach([
                        ['id' => 1, 'name' => 'Algebra Basics Quiz', 'date' => '2024-01-18', 'time' => '14.5 min', 'type' => 'Quiz', 'score' => '92%'],
                        ['id' => 2, 'name' => 'Geometric Shapes Test', 'date' => '2024-01-15', 'time' => '22.3 min', 'type' => 'Test', 'score' => '88%'],
                        ['id' => 3, 'name' => 'Fractions Review', 'date' => '2024-01-12', 'time' => '11.8 min', 'type' => 'Quiz', 'score' => '95%'],
                        ['id' => 4, 'name' => 'Problem Solving Strategies', 'date' => '2024-01-10', 'time' => '28.7 min', 'type' => 'Assessment', 'score' => '87%'],
                        ['id' => 5, 'name' => 'Data Interpretation', 'date' => '2024-01-08', 'time' => '19.2 min', 'type' => 'Quiz', 'score' => '83%']
                    ] as $assessment)
                    <a href="{{ route('teacher.assessments.review', ['student' => $student->id, 'assessment' => $assessment['id']]) }}" 
                       class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:border-blue-200 border border-gray-200 transition-all cursor-pointer group">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $assessment['name'] }}</div>
                            <div class="text-sm text-gray-500">{{ $assessment['date'] }} • {{ $assessment['time'] }} • {{ $assessment['type'] }}</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">{{ $assessment['score'] }}</div>
                                <div class="text-xs text-gray-500">Score</div>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">chevron_right</span>
                        </div>
                    </a>
                @endforeach
            @endforelse
        </div>
    </div>
</div>

                        <!-- Competencies Content -->
                        <div id="competencies-content" class="tab-content hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Competency Breakdown</h3>
                            
                            <div class="space-y-6">
                                <!-- Numerical Literacy -->
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Numerical Literacy</h4>
                                            <p class="text-sm text-gray-600">Basic arithmetic and number operations</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600">96%</div>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +5% improvement
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 96%"></div>
                                    </div>
                                </div>

                                <!-- Algebraic Thinking -->
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Algebraic Thinking</h4>
                                            <p class="text-sm text-gray-600">Patterns, equations, and relationships</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600">89%</div>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +12% improvement
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 89%"></div>
                                    </div>
                                </div>

                                <!-- Geometric Reasoning -->
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Geometric Reasoning</h4>
                                            <p class="text-sm text-gray-600">Shapes, space, and measurement</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600">94%</div>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +5% improvement
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 94%"></div>
                                    </div>
                                </div>

                                <!-- Problem Solving -->
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Problem Solving</h4>
                                            <p class="text-sm text-gray-600">Critical thinking and strategy</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600">91%</div>
                                            <div class="flex items-center text-xs text-green-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                +7% improvement
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 91%"></div>
                                    </div>
                                </div>

                                <!-- Data Analysis -->
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Data Analysis</h4>
                                            <p class="text-sm text-gray-600">Statistics and data interpretation</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-red-600">87%</div>
                                            <div class="flex items-center text-xs text-red-600">
                                                <span class="material-symbols-outlined text-sm mr-1">trending_down</span>
                                                -2% decline
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-600 h-2 rounded-full" style="width: 87%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Struggling Areas Content -->
                        <div id="struggling-areas-content" class="tab-content hidden">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Areas Needing Attention</h3>
                            <p class="text-gray-600 mb-6">Topics where the student could benefit from additional support</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @forelse($student->struggling_areas ?? [] as $area)
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
                                    @foreach([
                                            ['topic' => 'Quadratic Equations', 'area' => 'Algebraic Thinking', 'score' => '65%', 'change' => '-5%', 'attempts' => '8 attempts'],
                                            ['topic' => 'Complex Fractions', 'area' => 'Numerical Literacy', 'score' => '72%', 'change' => '-13%', 'attempts' => '12 attempts'],
                                            ['topic' => 'Statistical Analysis', 'area' => 'Data Analysis', 'score' => '68%', 'change' => '-4%', 'attempts' => '6 attempts']
                                        ] as $area)
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
                                    @endforeach
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
                                        @forelse($student->achievements ?? [] as $achievement)
                                            <div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-yellow-600 text-xl">emoji_events</span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900 text-lg">{{ $achievement['name'] }}</div>
                                                    <div class="text-sm text-gray-600">{{ $achievement['desc'] }}</div>
                                                    <div class="text-xs text-gray-500 mt-2">Earned {{ $achievement['date'] }}</div>
                                                </div>
                                            </div>
                                        @empty
                                            @foreach([
                                                    ['name' => 'Perfect Score', 'desc' => 'Achieved 100% on an assessment', 'date' => '2024-01-15'],
                                                    ['name' => 'Speed Demon', 'desc' => 'Completed assessment in under 10 minutes', 'date' => '2024-01-12'],
                                                    ['name' => 'Streak Master', 'desc' => '15 correct answers in a row', 'date' => '2024-01-10'],
                                                    ['name' => 'Consistent Performer', 'desc' => 'Maintained 85%+ average for 5 assessments', 'date' => '2024-01-08']
                                                ] as $achievement)
                                                <div class="flex items-start gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-yellow-600 text-xl">emoji_events</span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="font-medium text-gray-900 text-lg">{{ $achievement['name'] }}</div>
                                                        <div class="text-sm text-gray-600">{{ $achievement['desc'] }}</div>
                                                        <div class="text-xs text-gray-500 mt-2">Earned {{ $achievement['date'] }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Goals to Unlock -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Goals to Unlock</h3>
                                    <p class="text-gray-600 mb-6">Achievements available to earn</p>
                                    
                                    <div class="space-y-4">
                                        @foreach([
                                                ['name' => 'Math Genius', 'desc' => 'Score 95%+ on 10 consecutive assessments', 'progress' => '7/10'],
                                                ['name' => 'Time Master', 'desc' => 'Complete 20 assessments under target time', 'progress' => '12/20'],
                                                ['name' => 'Team Player', 'desc' => 'Help 5 classmates improve their scores', 'progress' => '2/5']
                                            ] as $goal)
                                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="font-medium text-gray-900">{{ $goal['name'] }}</div>
                                                    <div class="text-sm font-semibold text-blue-600">{{ $goal['progress'] }}</div>
                                                </div>
                                                <div class="text-sm text-gray-600 mb-3">{{ $goal['desc'] }}</div>
                                                @php
                                                    $progressParts = explode('/', $goal['progress']);
                                                    $current = intval($progressParts[0]);
                                                    $total = intval($progressParts[1]);
                                                    $percentage = ($current / $total) * 100;
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
        .tab-button.active {
            border-bottom-color: #2563eb;
            color: #2563eb;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            // Function to switch tabs
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
        });
    </script>
@endsection