@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('teacher-assessments.index') }}" class="text-gray-600 hover:text-gray-800">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $assessment->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $assessment->description }}</p>
            </div>
        </div>
    </div>

    <!-- Assessment Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Assessment Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $assessment->number_of_questions }}</div>
                <div class="text-sm text-gray-600">Questions</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $assessment->time_limit }}</div>
                <div class="text-sm text-gray-600">Minutes</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $assessment->difficulty }}</div>
                <div class="text-sm text-gray-600">Difficulty</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $assessment->category }}</div>
                <div class="text-sm text-gray-600">Category</div>
            </div>
        </div>
        
        @if($assessment->is_live_quiz)
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-blue-600 mr-2">live_tv</span>
                    <span class="font-medium text-blue-800">Live Quiz Mode</span>
                </div>
                <p class="text-blue-700 text-sm mt-1">This is a live quiz session. Questions will appear in real-time.</p>
            </div>
        @endif
    </div>

    <!-- Instructions -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Instructions</h3>
        <ul class="text-yellow-700 space-y-1">
            <li>• Read each question carefully before answering</li>
            <li>• You have {{ $assessment->time_limit }} minutes to complete this assessment</li>
            <li>• Once you start, the timer will begin and cannot be paused</li>
            <li>• Make sure you have a stable internet connection</li>
            <li>• Do not refresh the page during the assessment</li>
            @if($assignment->accommodations)
                <li>• Accommodations are enabled for this assessment</li>
            @endif
        </ul>
    </div>

    <!-- Start Assessment Button -->
    <div class="text-center">
        @if($assignment->status === 'Assigned')
            <button onclick="startAssessment()" 
                    class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 transition-colors text-lg font-semibold">
                Start Assessment
            </button>
        @elseif($assignment->status === 'In Progress')
            <button onclick="continueAssessment()" 
                    class="bg-yellow-600 text-white px-8 py-4 rounded-lg hover:bg-yellow-700 transition-colors text-lg font-semibold">
                Continue Assessment
            </button>
        @elseif($assignment->status === 'Completed')
            <div class="bg-green-100 border border-green-200 rounded-lg p-6">
                <span class="material-symbols-outlined text-green-600 text-4xl mb-2">check_circle</span>
                <h3 class="text-lg font-semibold text-green-800 mb-2">Assessment Completed</h3>
                <p class="text-green-700">You have successfully completed this assessment.</p>
                
                @if($quizResult)
                    <div class="mt-4 p-4 bg-white rounded-lg border border-green-300">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Your Results</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $quizResult->score }}/{{ $quizResult->total_questions }}</div>
                                <div class="text-sm text-gray-600">Score</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $quizResult->percentage }}%</div>
                                <div class="text-sm text-gray-600">Percentage</div>
                            </div>
                            <div class="text-center">
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
                        
                        @if($quizResult->percentage >= 80)
                            <div class="mt-3 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <span class="material-symbols-outlined text-sm mr-1">star</span>
                                    Excellent Work!
                                </span>
                            </div>
                        @elseif($quizResult->percentage >= 60)
                            <div class="mt-3 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <span class="material-symbols-outlined text-sm mr-1">thumb_up</span>
                                    Good Job!
                                </span>
                            </div>
                        @else
                            <div class="mt-3 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                    Keep Practicing!
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
                
                <div class="mt-4 text-center">
                    <button onclick="viewResults()" 
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <span class="material-symbols-outlined text-sm mr-2">visibility</span>
                        View Detailed Results
                    </button>
                </div>
            </div>
        @endif
    </div>

</div>

<script>
function startAssessment() {
    // Redirect to the quiz interface
    window.location.href = "{{ route('teacher-assessments.quiz', $assessment->id) }}";
}

function continueAssessment() {
    // Redirect to the quiz interface to continue
    window.location.href = "{{ route('teacher-assessments.quiz', $assessment->id) }}";
}

function viewResults() {
    // For now, we'll show an alert with the results
    // In the future, this could redirect to a detailed results page
    @if($quizResult)
        const timeDisplay = {{ $quizResult->time_taken }} >= 3600 ? 
            '{{ gmdate("H:i:s", $quizResult->time_taken) }}' : 
            '{{ gmdate("i:s", $quizResult->time_taken) }}';
        alert(`Your Assessment Results:\n\nScore: {{ $quizResult->score }}/{{ $quizResult->total_questions }}\nPercentage: {{ $quizResult->percentage }}%\nTime Taken: ${timeDisplay}`);
    @endif
}
</script>
@endsection
