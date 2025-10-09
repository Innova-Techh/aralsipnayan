@extends('admin.teacher.layouts.app')

@section('title', 'Student Profile - Aralsipnayan')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between mb-4">
                <!-- FIXED: Back button using proper route with section parameter -->
                <button onclick="window.location.href='{{ route('teacher.sections.show', ['section' => $section->id ?? $student->section_id ?? '']) }}'"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-medium">Back to Section</span>
                </button>
                <div class="flex items-center gap-3">
                    <button
                        class="flex items-center gap-2 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined">print</span>
                        Print Report
                    </button>
                    <button
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span class="material-symbols-outlined">mail</span>
                        Contact Parent
                    </button>
                </div>
            </div>

            <!-- Student Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-start gap-6">
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center border-4 border-white shadow-lg">
                        <span class="material-symbols-outlined text-4xl text-blue-600">person</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $student->name ?? 'Student Name' }}</h1>
                        <div class="flex items-center gap-4 mt-2 text-gray-600">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">mail</span>
                                <span>{{ $student->email ?? 'student@school.edu' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">phone</span>
                                <span>{{ $student->phone ?? '+1 (555) 123-4567' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-3">
                            <span
                                class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                Grade {{ $student->grade_level ?? '7' }}
                            </span>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                Section {{ $section->name ?? 'A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-5 gap-6 bg-gray-50 rounded-lg p-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($student->total_points ?? 2847) }}</div>
                    <div class="text-sm text-gray-600 mt-1">Total Points</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $student->assessment_count ?? 12 }}</div>
                    <div class="text-sm text-gray-600 mt-1">Assessments</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $student->best_streak ?? 15 }}</div>
                    <div class="text-sm text-gray-600 mt-1">Best Streak</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $student->time_spent ?? 47.5 }}</div>
                    <div class="text-sm text-gray-600 mt-1">Time Spent</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $student->avg_time_per_question ?? 1.8 }} min</div>
                    <div class="text-sm text-gray-600 mt-1">Avg Time/Q</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Navigation Tabs -->
                    <div class="bg-white rounded-xl border border-gray-200">
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px">
                                <button class="px-6 py-4 border-b-2 border-blue-600 text-blue-600 font-medium text-sm">
                                    Overview
                                </button>
                                <button class="px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                    Assessments
                                </button>
                                <button class="px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                    Competencies
                                </button>
                                <button class="px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                    Struggling Areas
                                </button>
                                <button class="px-6 py-4 text-gray-500 hover:text-gray-700 font-medium text-sm">
                                    Achievements
                                </button>
                            </nav>
                        </div>

                        <!-- Overview Content -->
                        <div class="p-6">
                            <!-- Competency Scores -->
                            <div class="grid grid-cols-2 gap-6 mb-8">
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

                            <!-- Last Assessment -->
                            <div class="bg-blue-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Last Assessment</h3>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">{{ $student->last_assessment_name ?? 'Algebra Basics Quiz' }}</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ $student->last_assessment_score ?? 92 }}%</div>
                                        <div class="text-xs text-gray-500">Score</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600 mb-1">Date Taken</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $student->last_assessment_date ?? '2024-01-18' }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->last_assessment_time ?? '14.5 min' }} • {{ $student->last_assessment_accuracy ?? '88%' }} Accuracy</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Trends -->
                            <div>
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

                    <!-- Assessment History -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assessment History</h3>
                        <div class="space-y-4">
                            @forelse($student->assessments ?? [] as $assessment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $assessment['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $assessment['date'] }} • {{ $assessment['time'] }} • {{ $assessment['type'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">{{ $assessment['score'] }}</div>
                                        <div class="text-xs text-gray-500">Score</div>
                                    </div>
                                </div>
                            @empty
                                @foreach([
                                        ['name' => 'Algebra Basics Quiz', 'date' => '2024-01-18', 'time' => '14.5 min', 'type' => 'Quiz', 'score' => '92%'],
                                        ['name' => 'Geometric Shapes Test', 'date' => '2024-01-15', 'time' => '22.3 min', 'type' => 'Test', 'score' => '88%'],
                                        ['name' => 'Fractions Review', 'date' => '2024-01-12', 'time' => '11.8 min', 'type' => 'Quiz', 'score' => '95%'],
                                        ['name' => 'Problem Solving Strategies', 'date' => '2024-01-10', 'time' => '28.7 min', 'type' => 'Assessment', 'score' => '87%'],
                                        ['name' => 'Data Interpretation', 'date' => '2024-01-08', 'time' => '19.2 min', 'type' => 'Quiz', 'score' => '83%']
                                    ] as $assessment)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $assessment['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $assessment['date'] }} • {{ $assessment['time'] }} • {{ $assessment['type'] }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-gray-900">{{ $assessment['score'] }}</div>
                                            <div class="text-xs text-gray-500">Score</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Areas Needing Attention -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Areas Needing Attention</h3>
                        <div class="space-y-4">
                            @forelse($student->struggling_areas ?? [] as $area)
                                <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                                    <div class="font-medium text-gray-900">{{ $area['topic'] }}</div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $area['area'] }}</div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-lg font-bold text-red-600">{{ $area['score'] }}</div>
                                        <div class="text-xs text-red-600">{{ $area['change'] }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $area['attempts'] }}</div>
                                </div>
                            @empty
                                @foreach([
                                        ['topic' => 'Quadratic Equations', 'area' => 'Algebraic Thinking', 'score' => '65%', 'change' => '-5%', 'attempts' => '8 attempts'],
                                        ['topic' => 'Complex Fractions', 'area' => 'Numerical Literacy', 'score' => '72%', 'change' => '-13%', 'attempts' => '12 attempts'],
                                        ['topic' => 'Statistical Analysis', 'area' => 'Data Analysis', 'score' => '68%', 'change' => '-4%', 'attempts' => '6 attempts']
                                    ] as $area)
                                    <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                                        <div class="font-medium text-gray-900">{{ $area['topic'] }}</div>
                                        <div class="text-sm text-gray-600 mb-2">{{ $area['area'] }}</div>
                                        <div class="flex items-center justify-between">
                                            <div class="text-lg font-bold text-red-600">{{ $area['score'] }}</div>
                                            <div class="text-xs text-red-600">{{ $area['change'] }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $area['attempts'] }}</div>
                                    </div>
                                @endforeach
                            @endforelse
                        </div>
                    </div>

                    <!-- Achievements -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Earned Achievements</h3>
                        <div class="space-y-3">
                            @forelse($student->achievements ?? [] as $achievement)
                                <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-yellow-600 text-sm">emoji_events</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $achievement['name'] }}</div>
                                        <div class="text-sm text-gray-600">{{ $achievement['desc'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">Earned {{ $achievement['date'] }}</div>
                                    </div>
                                </div>
                            @empty
                                @foreach([
                                        ['name' => 'Perfect Score', 'desc' => 'Achieved 100% on an assessment', 'date' => '2024-01-15'],
                                        ['name' => 'Speed Demon', 'desc' => 'Completed assessment in under 10 minutes', 'date' => '2024-01-12'],
                                        ['name' => 'Streak Master', 'desc' => '15 correct answers in a row', 'date' => '2024-01-10'],
                                        ['name' => 'Consistent Performer', 'desc' => 'Maintained 85%+ average for 5 assessments', 'date' => '2024-01-08']
                                    ] as $achievement)
                                    <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-yellow-600 text-sm">emoji_events</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $achievement['name'] }}</div>
                                            <div class="text-sm text-gray-600">{{ $achievement['desc'] }}</div>
                                            <div class="text-xs text-gray-500 mt-1">Earned {{ $achievement['date'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforelse
                        </div>

                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Goals to Unlock</h4>
                            <div class="space-y-3">
                                @foreach([
                                        ['name' => 'Math Genius', 'desc' => 'Score 95%+ on 10 consecutive assessments', 'progress' => '7/10'],
                                        ['name' => 'Time Master', 'desc' => 'Complete 20 assessments under target time', 'progress' => '12/20'],
                                        ['name' => 'Team Player', 'desc' => 'Help 5 classmates improve their scores', 'progress' => '2/5']
                                    ] as $goal)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="font-medium text-gray-900">{{ $goal['name'] }}</div>
                                        <div class="text-sm text-gray-600 mb-2">{{ $goal['desc'] }}</div>
                                        <div class="flex items-center justify-between">
                                            <div class="text-xs text-gray-500">Progress</div>
                                            <div class="text-sm font-semibold text-blue-600">{{ $goal['progress'] }}</div>
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

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('nav button');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-600', 'text-blue-600');
                        t.classList.add('text-gray-500');
                    });

                    // Add active class to clicked tab
                    this.classList.add('border-blue-600', 'text-blue-600');
                    this.classList.remove('text-gray-500');
                });
            });
        });
    </script>
@endsection