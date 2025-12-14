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
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
                <div class="bg-green-50 border border-green-200 rounded-xl p-5 text-center mb-6">
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
                                    <div class="text-2xl font-bold text-purple-600">
                                        @if(isset($quizResult->formatted_time))
                                            {{ $quizResult->formatted_time }}
                                        @elseif($quizResult->time_taken >= 3600)
                                            {{ gmdate('H:i:s', $quizResult->time_taken) }}
                                        @else
                                            {{ gmdate('i:s', $quizResult->time_taken) }}
                                        @endif
                                    </div>
                                    </div>
                                    <div class="text-sm text-gray-600">Time Taken</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="window.location.href='{{ route('teacher-assessments.results', $assessment->id) }}'"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition shadow-sm flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm mr-2">visibility</span>
                            View Detailed Results
                        </button>
                        
                        <form action="{{ route('teacher-assessments.retake', $assessment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to retake this quiz? Your previous progress will be archived.');" class="inline-block">
                            @csrf
                            <button type="submit" 
                                class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center justify-center">
                                <span class="material-symbols-outlined text-sm mr-2">refresh</span>
                                Retake Quiz
                            </button>
                        </form>
                    </div>
                </div>

                @if(isset($history) && $history->count() > 0)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-500">history</span>
                            Attempt History
                        </h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date Taken</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Score</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Percentage</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($history as $attempt)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('M d, Y • h:i A') : 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 font-bold text-center">
                                                {{ $attempt->score }}/{{ $attempt->total_questions }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-bold text-center">
                                                <span class="px-2 py-1 rounded-full {{ $attempt->percentage >= 75 ? 'bg-green-100 text-green-700' : ($attempt->percentage >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                    {{ $attempt->percentage }}%
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500 text-center">
                                                {{ $attempt->time_taken }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-center">
                                                <a href="{{ route('teacher-assessments.results', ['assessment' => $assessment->id, 'session_id' => $attempt->id]) }}" 
                                                   class="inline-flex items-center justify-center bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-full text-xs font-semibold transition-colors">
                                                    <span class="material-symbols-outlined text-xs mr-1">visibility</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
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
    window.location.href = "{{ route('teacher-assessments.start', $assessment->id) }}";
}
function continueAssessment() {
    window.location.href = "{{ route('teacher-assessments.start', $assessment->id) }}";
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