@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center px-4 sm:px-0 z-50">
    <div class="w-full max-w-lg bg-[#FFF7E6] rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#2077AF] to-[#4720AF] p-4 flex items-center gap-2 shadow-[0_8px_16px_rgba(0,0,0,0.3)] border-b-4 border-[#1a5a8a] relative">
            <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none"></div>
            <span class="material-symbols-outlined text-white text-2xl relative z-10">quiz</span>
            <h2 class="text-xl font-extrabold text-white relative z-10 text-shadow-lg">
                {{ $assessment->title }}
            </h2>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto">
            <!-- Description -->
            <p class="text-gray-700 mb-4">{{ $assessment->description }}</p>

            <!-- Stat Cards -->
            <div class="flex gap-3 mb-5">
                <div class="flex-1 bg-gradient-to-b from-[#F5A623] to-[#F5D70B] rounded-xl shadow-lg p-3 text-white text-center">
                    <div class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">schedule</span>
                        <span class="text-sm font-medium">Time Limit</span>
                    </div>
                    <span class="text-base font-bold mt-1 block">{{ $assessment->time_limit }} minutes</span>
                </div>

                <div class="flex-1 bg-gradient-to-b from-[#34D399] to-[#059669] rounded-xl shadow-lg p-3 text-white text-center">
                    <div class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">help</span>
                        <span class="text-sm font-medium">Questions</span>
                    </div>
                    <span class="text-base font-bold mt-1 block">{{ $assessment->number_of_questions }}</span>
                </div>
            </div>

            <!-- Extra Info -->
            <div class="flex gap-3 mb-5">
                <div class="flex-1 bg-gradient-to-b from-[#8B5CF6] to-[#6D28D9] rounded-xl shadow-lg p-3 text-white text-center">
                    <div class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">speed</span>
                        <span class="text-sm font-medium">Difficulty</span>
                    </div>
                    <span class="text-base font-bold mt-1 block">{{ ucfirst($assessment->difficulty) }}</span>
                </div>

                <div class="flex-1 bg-gradient-to-b from-[#F97316] to-[#EA580C] rounded-xl shadow-lg p-3 text-white text-center">
                    <div class="flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">category</span>
                        <span class="text-sm font-medium">Category</span>
                    </div>
                    <span class="text-base font-bold mt-1 block">{{ $assessment->category }}</span>
                </div>
            </div>

            <!-- Live Quiz Badge -->
            @if($assessment->is_live_quiz)
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-5">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">live_tv</span>
                        <span class="text-blue-800 font-medium">Live Quiz Mode Active</span>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            <div class="mb-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Instructions</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1 text-sm">
                    <li>Read each question carefully before answering</li>
                    <li>You have {{ $assessment->time_limit }} minutes to complete this assessment</li>
                    <li>Once started, the timer will begin and cannot be paused</li>
                    <li>Do not refresh the page during the assessment</li>
                    @if($assignment->accommodations)
                        <li>Accommodations are enabled for this assessment</li>
                    @endif
                </ul>
            </div>

            <!-- Warning -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-5">
                <p class="text-blue-800 text-sm font-medium">
                    Once started, this assessment cannot be paused. Make sure you have enough time to complete it.
                </p>
            </div>

            <!-- Buttons -->
            @if($assignment->status === 'Assigned')
                <button onclick="startAssessment()"
                    class="w-full bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white text-lg font-semibold py-3 rounded-2xl border-b-4 border-[#922f26] shadow-[0_6px_12px_rgba(0,0,0,0.3)] hover:scale-[1.03] hover:shadow-[0_8px_16px_rgba(0,0,0,0.4)] active:scale-[0.98] transition-all duration-200 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none"></div>
                    <span class="relative z-10">Start Assessment</span>
                </button>
            @elseif($assignment->status === 'In Progress')
                <button onclick="continueAssessment()"
                    class="w-full bg-gradient-to-b from-[#F5A623] to-[#F59E0B] text-white text-lg font-semibold py-3 rounded-2xl border-b-4 border-[#b45309] shadow-[0_6px_12px_rgba(0,0,0,0.3)] hover:scale-[1.03] transition-all duration-200">
                    Continue Assessment
                </button>
            @elseif($assignment->status === 'Completed')
                <div class="bg-green-50 border border-green-200 rounded-xl p-5 text-center">
                    <span class="material-symbols-outlined text-green-600 text-5xl mb-2">check_circle</span>
                    <h3 class="text-xl font-semibold text-green-800 mb-2">Assessment Completed</h3>
                    <p class="text-green-700 mb-4">You have successfully completed this assessment.</p>

                    @if($quizResult)
                        <div class="bg-white border border-green-300 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $quizResult->score }}/{{ $quizResult->total_questions }}</div>
                                    <div class="text-sm text-gray-600">Score</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ $quizResult->percentage }}%</div>
                                    <div class="text-sm text-gray-600">Percentage</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        @if($quizResult->time_taken >= 3600)
                                            {{ gmdate('H:i:s', $quizResult->time_taken) }}
                                        @else
                                            {{ gmdate('i:s', $quizResult->time_taken) }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600">Time Taken</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button onclick="viewResults()"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        <span class="material-symbols-outlined text-sm mr-1">visibility</span>
                        View Detailed Results
                    </button>
                </div>
            @endif

            <!-- Cancel -->
            <button onclick="window.location.href='{{ route('sections.index') }}'"
                class="w-full text-center bg-gray-200 text-gray-700 mt-3 hover:bg-gray-300 py-2 rounded-xl transition-all duration-200 shadow-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
function startAssessment() {
    window.location.href = "{{ route('teacher-assessments.quiz', $assessment->id) }}";
}
function continueAssessment() {
    window.location.href = "{{ route('teacher-assessments.quiz', $assessment->id) }}";
}
function viewResults() {
    @if($quizResult)
        const timeDisplay = {{ $quizResult->time_taken }} >= 3600
            ? '{{ gmdate("H:i:s", $quizResult->time_taken) }}'
            : '{{ gmdate("i:s", $quizResult->time_taken) }}';
        alert(`Your Assessment Results:\n\nScore: {{ $quizResult->score }}/{{ $quizResult->total_questions }}\nPercentage: {{ $quizResult->percentage }}%\nTime Taken: ${timeDisplay}`);
    @endif
}
</script>
@endsection