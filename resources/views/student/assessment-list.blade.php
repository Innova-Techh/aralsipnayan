@extends('layouts.user_layout')

@section('title', 'Assessment List')

@section('content')

    <div class="space-y-8 max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
    <!-- My Assessments Header -->
    <div class="relative overflow-hidden mt-4 lg:mt-8">
<div class="mx-auto max-w-10xl text-white bg-center bg-no-repeat rounded-2xl flex items-center" 
     style="background-image: url('{{ asset('images/assessments/bg.png') }}'); 
            background-size: 95% clamp(120px, 10vw + 60px, 200px);
            min-height: clamp(120px, 10vw + 60px, 200px);
            padding-left: clamp(2rem, 8vw, 18rem);">
    <div class="relative z-10 pr-6">
        <!-- Title -->
        <h1 class="text-lg sm:text-xl md:text-5xl lg:text-5xl leading-tight font-baloo font-extrabold">
            {{ $data['title'] }} Assessments
        </h1>

        <!-- Description -->
        <p class="text-[10px] sm:text-xs md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">
            {{ $data['description'] }}
        </p>
    </div>
</div>

    </div>

    {{-- Return to Assessments --}}
    <div class="mb-1">
        <a href="{{ route('student.assessments') }}" 
        class="inline-flex items-center gap-2 p-3 rounded-full hover:scale-110 transition-transform duration-200">
            <!-- Arrow SVG -->
            <div class="w-6 h-6">
                <svg viewBox="0 0 24 24" class="w-full h-full">
                    <defs>
                        <linearGradient id="arrowGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#4338CA"/>
                            <stop offset="100%" stop-color="#9333EA"/>
                        </linearGradient>
                    </defs>
                    <path d="M15 18l-6-6 6-6" 
                        stroke="url(#arrowGradient)" 
                        stroke-width="3" 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        fill="none"/>
                </svg>
            </div>
            <span class="text-indigo-500 font-baloo font-extrabold text-xl">Back to Assessments</span>
        </a>
    </div>

    <!-- Category Card -->
    <div class="relative overflow-hidden">
        <div class="mx-auto max-w-10xl rounded-2xl text-white transition-all duration-300 
                    border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
            style="background: linear-gradient(to bottom, #4338CA, #9333EA); min-height: clamp(70px, 5vw + 30px, 100px);">
            
            <!-- Mobile Layout: Vertical Stack -->
            <div class="lg:hidden relative z-10 h-full flex flex-col justify-center pl-6 sm:pl-10 md:pl-16 lg:pl-24 pt-2 sm:pt-6 md:pt-8 lg:pt-6">
                <!-- Title Row -->
                <div class="flex items-center gap-3 sm:gap-4 mb-1">
                    <span class="text-xl sm:text-2xl md:text-3xl lg:text-4xl">{{ $data['icon'] }}</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl lg:text-4xl font-baloo font-extrabold leading-tight">
                        {{ $data['title'] }}
                    </h2>
                </div>
                
                <!-- Status Badge - Mobile -->
                <div class="ml-[2.5rem] mb-[1rem] sm:ml-[3rem] md:ml-[3.5rem] lg:ml-[4.5rem]">
                    <span class="inline-flex items-center justify-center px-4 py-1 rounded-full text-xs font-medium bg-white/20 text-white border border-white/30 lg:text-lg lg:px-6 lg:py-2 lg:min-w-[5.2rem] lg:h-10">
                        {{ ucfirst($mastery->current_difficulty ?? 'Beginner') }}
                    </span>
                </div>
            </div>

            <!-- Desktop Layout: Horizontal -->
            <div class="hidden lg:flex relative z-10 h-full items-center justify-between pl-6 sm:pl-10 md:pl-16 lg:pl-24 pr-6 pt-2 sm:pt-6 md:pt-8 lg:pt-6">
                <!-- Left: Title -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="text-xl sm:text-2xl md:text-3xl lg:text-4xl">{{ $data['icon'] }}</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl lg:text-4xl font-baloo font-extrabold leading-tight">
                        {{ $data['title'] }}
                    </h2>
                </div>
                
                <!-- Right: Status Badge -->
                <div>
                    <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium bg-white/20 text-white border border-white/30">
                        {{ ucfirst($mastery->current_difficulty ?? 'Beginner') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Progress Summary -->
    <div class="bg-white rounded-xl shadow-sm p-6 mx-auto max-w-10xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Progress in {{ $data['title'] }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Mastery Score -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Mastery Score</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ round($mastery->final_mastery_score ?? 0, 1) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Questions Answered -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Questions Answered</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ $mastery->total_questions_answered ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Accuracy Rate -->
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Accuracy Rate</p>
                        <p class="text-2xl font-bold text-violet-600">
                            {{ $mastery->total_questions_answered > 0 ? round(($mastery->correct_answers / $mastery->total_questions_answered) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-violet-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12a1 1 0 002 0V7a1 1 0 00-2 0v5zM9 15a1 1 0 112 0 1 1 0 01-2 0z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment List Content -->
<div class="mx-auto max-w-[1600px] px-2 sm:px-4 md:px-6 lg:px-8 pb-24">
    @if($availableQuestions > 0)
        <div class="grid 
                    grid-cols-1          <!-- all mobile: 1 column -->
                    md:grid-cols-2       <!-- tablet: 2 columns -->
                    lg:grid-cols-2       <!-- laptop: 3 columns -->
                    xl:grid-cols-3       <!-- desktop: 3 columns -->
                    2xl:grid-cols-4      <!-- large desktop: 5 columns -->
                    gap-4 sm:gap-5 lg:gap-6 font-baloo">

            @foreach($assessmentOptions as $index => $assessment)
            <div class="w-full 
                        bg-gradient-to-br from-[#2077AF] to-[#4720AF] 
                        rounded-xl border-b-4 border-[#0b1d30] 
                        shadow-lg transition-all 
                        p-3 sm:p-4 md:p-5 
                        flex flex-col justify-between 
                        h-auto">

                <!-- Title + Time -->
                <div class="flex flex-wrap justify-between items-center mb-3 gap-2">
                    <h3 class="text-base sm:text-lg md:text-xl font-bold text-white">
                        {{ $assessment['title'] }}
                    </h3>
                    <div class="flex items-center gap-1 text-white text-xs sm:text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-[#FF6B6B]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8a1 1 0 0 1 1 1v3.28l2.72 1.64a1 1 0 1 1-1.04 1.72l-3.2-1.92A1 1 0 0 1 11 13V9a1 1 0 0 1 1-1zm0-6a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/>
                        </svg>
                        <span class="font-semibold">{{ $assessment['time_limit'] }} mins</span>
                    </div>
                </div>

                <!-- Attributes -->
                <div class="space-y-3 mb-4">
                    <!-- Number of Questions -->
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 text-[#4ADE80]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 5h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2z"/>
                        </svg>
                        <span class="text-sm sm:text-lg text-white font-bold">{{ $assessment['question_count'] }} Questions</span>
                    </div>

                    <!-- Difficulty -->
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 text-[#FFD93D]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.524 8.229L12 18.897l-7.46 4.52 1.524-8.229L0 9.309l8.332-1.151z"/>
                        </svg>
                        <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-xs sm:text-sm font-bold bg-yellow-200 text-yellow-900 shadow-md">
                            {{ ucfirst($assessment['difficulty']) }}
                        </span>
                    </div>

                    <!-- Topics -->
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 text-[#8B5CF6] mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12a1 1 0 002 0V7a1 1 0 00-2 0v5zM9 15a1 1 0 112 0 1 1 0 01-2 0z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-white text-xs sm:text-sm">
                            <div class="font-semibold mb-1">Topics:</div>
                            <div class="text-[10px] sm:text-xs text-gray-200">{{ implode(', ', $assessment['topics']) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Assessment Details -->
                <div class="bg-white/10 rounded-lg p-2 sm:p-3 mb-4">
                    <div class="text-[10px] sm:text-xs text-white/80 space-y-1">
                        <div>Estimated Points: {{ $assessment['estimated_points'] }}</div>
                        <div>Best Time: {{ $assessment['best_completion_time'] ?? 'Not attempted' }}</div>
                    </div>
                </div>

                <!-- Button -->
                <button onclick="openAssessmentModal({{ $index }})" 
                    class="w-full text-xs sm:text-sm md:text-base bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white py-2 rounded-lg sm:rounded-xl font-semibold shadow-[0_4px_0_#c03f00] hover:scale-[1.03] transition-all duration-200">
                    Start Assessment
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @else

                <!-- No Questions Available -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <div class="text-yellow-600 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Assessments Available</h3>
                    <p class="text-yellow-700 mb-4">All questions for your current level are in cooldown period. Please wait or try a different competency.</p>
                    <div class="text-sm text-yellow-600 mb-4">
                        <p>Questions return to availability after:</p>
                        <p class="font-semibold">• 30 minutes (if answered correctly)</p>
                        <p class="font-semibold">• 60 minutes (if answered incorrectly)</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="refreshAssessments()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            🔄 Check for Available Questions
                        </button>
                        <a href="{{ route('student.assessments') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                            Try Other Competencies
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

<!-- Assessment Modal -->
<div id="assessmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4 sm:px-0">
    <div class="w-full max-w-lg bg-[#FFF7E6] rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#2077AF] to-[#4720AF] p-4 flex items-center gap-2">
            <span class="text-2xl" id="modal-icon">{{ $data['icon'] }}</span>
            <h2 class="text-xl font-extrabold text-white" id="modal-title">Assessment Preview</h2>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto">
            <!-- Assessment Info -->
            <div id="modal-content">
                <!-- Content will be populated by JavaScript -->
            </div>

            <!-- Time & Questions Cards -->
            <div class="flex gap-3 mb-5">
                <!-- Time Limit Card -->
                <div class="flex-1 bg-gradient-to-b from-[#F5A623] to-[#F5D70B] rounded-xl shadow-lg p-3 text-white flex flex-col items-center">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">Time Limit</span>
                    </div>
                    <span class="text-base font-bold mt-1" id="modal-time">0 minutes</span>
                </div>

                <!-- Number of Questions Card -->
                <div class="flex-1 bg-gradient-to-b from-[#34D399] to-[#059669] rounded-xl shadow-lg p-3 text-white flex flex-col items-center">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm font-medium">Questions</span>
                    </div>
                    <span class="text-base font-bold mt-1" id="modal-questions">0 Questions</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mb-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Instructions</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-2 text-sm">
                    <li>Answer all questions to the best of your ability</li>
                    <li>You cannot go back to previous questions</li>
                    <li>Your progress will be automatically saved</li>
                    <li>Complete within the time limit for best results</li>
                </ul>
            </div>

            <!-- Warning -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-5">
                <p class="text-blue-800 text-sm font-medium">
                    Once started, this assessment cannot be paused. Make sure you have enough time to complete it.
                </p>
            </div>

            <!-- Start Button -->
            <button id="modal-start-btn" 
                class="w-full bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white text-lg font-semibold py-3 rounded-2xl border-b-4 border-[#922f26] shadow-lg hover:scale-[1.03] transition-all duration-300">
                Start Assessment
            </button>

            <!-- Close Button -->
            <button onclick="closeAssessmentModal()" 
                class="w-full text-center text-sm text-gray-600 mt-3 hover:underline">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let assessmentOptions = @json($assessmentOptions ?? []);
let selectedAssessmentIndex = null;

function openAssessmentModal(index) {
    selectedAssessmentIndex = index;
    const assessment = assessmentOptions[index];
    
    // Populate modal content
    document.getElementById('modal-title').textContent = assessment.title;
    document.getElementById('modal-time').textContent = assessment.time_limit + ' minutes';
    document.getElementById('modal-questions').textContent = assessment.question_count + ' Questions';
    
    // Update start button
    const startBtn = document.getElementById('modal-start-btn');
    startBtn.onclick = function() {
        startAssessment(assessment.assessment_id);
    };
    
    // Show modal
    document.getElementById('assessmentModal').classList.remove('hidden');
}

function closeAssessmentModal() {
    document.getElementById('assessmentModal').classList.add('hidden');
    selectedAssessmentIndex = null;
}

// Start assessment function
function startAssessment(assessmentId) {
    // Show loading state
    const startBtn = document.getElementById('modal-start-btn');
    const originalText = startBtn.textContent;
    startBtn.disabled = true;
    startBtn.textContent = 'Starting...';
    
    // Create the assessment when user clicks start (not during page load)
    fetch('{{ route("student.quiz.start-assessment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            assessment_id: assessmentId,
            category: '{{ $category }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to quiz interface
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Failed to start assessment. Please try again.');
            startBtn.disabled = false;
            startBtn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error starting assessment:', error);
        alert('Failed to start assessment. Please try again.');
        startBtn.disabled = false;
        startBtn.textContent = originalText;
    });
}

// Close modal when clicking outside
document.getElementById('assessmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssessmentModal();
    }
});

// Refresh assessments function
function refreshAssessments() {
    // Show loading state
    const refreshBtn = event.target;
    const originalText = refreshBtn.innerHTML;
    refreshBtn.disabled = true;
    refreshBtn.innerHTML = '⏳ Checking...';
    
    // Make AJAX request to refresh assessments
    fetch(`{{ route('student.assessments.refresh', $category) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.assessmentOptions.length > 0) {
            // Refresh the page to show new assessments
            window.location.reload();
        } else {
            // Show message that no assessments are available yet
            alert('No assessments available yet. Questions are still in cooldown period.');
        }
    })
    .catch(error => {
        console.error('Error refreshing assessments:', error);
        alert('Error checking for available assessments. Please try again.');
    })
    .finally(() => {
        // Restore button state
        refreshBtn.disabled = false;
        refreshBtn.innerHTML = originalText;
    });
}
</script>

@endsection