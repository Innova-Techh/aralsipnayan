@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')

<div class="space-y-8 font-baloo mt-10">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6 md:mb-8">
        <!-- Question Counter -->
        <div class="bg-purple-700 backdrop-blur-sm rounded-full px-6 py-3 md:px-9 md:py-4 border-b-6 border-[#4a1377]"
             style="box-shadow: 0 8px 0 #4a1377;">
            <span class="text-white font-semibold text-base md:text-lg">
                @if(isset($diagnosticMode) && $diagnosticMode)
                    Phase {{ $diagnosticPhase ?? 1 }} - Question {{ $currentQuestion }} of {{ $totalQuestions }}
                @else
                    Question {{ $currentQuestion }} of {{ $totalQuestions }}
                @endif


            </span>
        </div>

        <!-- Timer -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-full px-6 py-3 md:px-9 md:py-4 flex items-center gap-3 border-b-6 border-[#cc4713]"
             style="box-shadow: 0 8px 0 #cc4713;">
            <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            <span class="text-white font-bold text-base md:text-lg" id="timer-display">{{ $question->max_time ?? 30 }}:00</span>
        </div>
    </div>

    <!-- Diagnostic Mode Banner (if applicable) -->
    @if(isset($diagnosticMode) && $diagnosticMode)
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white text-center shadow-lg">
        <h3 class="font-bold text-lg mb-2">🔬 Diagnostic Assessment</h3>
        <p class="text-sm">This diagnostic test helps us understand your current skill level. Take your time and do your best!</p>
    </div>
    @endif

    <!-- Quiz Card -->
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl md:rounded-3xl p-6 md:p-8 lg:p-10 shadow-2xl">
        
        <!-- Hint Button (if not diagnostic) -->
        @if(!isset($diagnosticMode) || !$diagnosticMode)
        <div class="flex justify-start mb-6">
            <button id="hint-btn" class="bg-gradient-to-r from-orange-400 to-orange-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-orange-500 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#cc4713] shadow-lg">
                💡 HINT
            </button>
        </div>
        @endif

        <!-- Question -->
        <div class="mb-8 md:mb-10">
            <h2 class="text-gray-800 text-lg md:text-xl lg:text-2xl font-semibold leading-relaxed">
                {{ $question->text }}
            </h2>
        </div>

        <!-- Answer Section -->
        <div class="mb-8 md:mb-10" id="answer-section">
            @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                <!-- Multiple Choice / True False Options -->
                <div class="space-y-4 md:space-y-5" id="multiple-choice-container">
                    @foreach($question->options as $index => $option)
                    <label class="flex items-center p-4 md:p-5 bg-gray-100 border-2 border-transparent rounded-xl md:rounded-2xl cursor-pointer hover:bg-gray-200 transition-all duration-200 option-label">
                        <input type="radio" name="answer" value="{{ chr(65 + $index) }}" class="hidden">
                        <div class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-gray-500 text-white rounded-full font-bold text-sm md:text-base mr-4 md:mr-5 option-circle">
                            {{ chr(65 + $index) }}
                        </div>
                        <span class="text-gray-700 font-medium text-base md:text-lg">
                            {{ $option }}
                        </span>
                    </label>
                    @endforeach
                </div>
            @elseif($question->type === 'fill_blanks')
                <!-- Fill in the Blanks -->
                <div class="space-y-4">
                    <input type="text" 
                           name="answer" 
                           id="fill-answer"
                           class="w-full p-4 md:p-5 bg-gray-100 border-2 border-gray-300 rounded-xl md:rounded-2xl text-gray-700 font-medium text-base md:text-lg focus:border-blue-400 focus:bg-blue-50 focus:outline-none transition-all duration-200"
                           placeholder="Type your answer here..."
                           autocomplete="off">
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button id="submit-btn" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-3 md:px-12 md:py-4 rounded-2xl font-bold text-base md:text-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#0b830b] shadow-lg">
                SUBMIT ANSWER
            </button>
        </div>

        <!-- Feedback Section (initially hidden) -->
        <div id="feedback-section" class="hidden mt-6 p-4 rounded-lg">
            <div id="feedback-message" class="font-semibold mb-2"></div>
            <div id="explanation-text" class="text-sm text-gray-700"></div>
            <div class="mt-4">
                <button id="next-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold">
                    Next Question
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let startTime = Date.now();
    let maxTime = {{ $question->max_time ?? 30 }};
    let timeRemaining = maxTime;
    let timerInterval;
    let questionSubmitted = false;
    
    // Initialize timer
    startTimer();
    
    // Initialize answer selection
    initializeAnswerSelection();
    
    // Submit button handler
    document.getElementById('submit-btn').addEventListener('click', submitAnswer);
    
    function startTimer() {
        timerInterval = setInterval(function() {
            timeRemaining--;
            updateTimerDisplay();
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                if (!questionSubmitted) {
                    timeoutSubmission();
                }
            }
        }, 1000);
    }
    
    function updateTimerDisplay() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('timer-display').textContent = display;
        
        // Change color when time is running low
        const timerElement = document.querySelector('.bg-gradient-to-r.from-orange-500');
        if (timeRemaining <= 10) {
            timerElement.classList.remove('from-orange-500', 'to-red-500');
            timerElement.classList.add('from-red-600', 'to-red-700');
        }
    }
    
    function initializeAnswerSelection() {
        const labels = document.querySelectorAll('.option-label');
        labels.forEach((label) => {
            label.addEventListener('click', function() {
                // Reset all labels
                labels.forEach(l => {
                    l.classList.remove('bg-blue-100', 'border-blue-400');
                    l.classList.add('bg-gray-100', 'border-transparent');
                    const circle = l.querySelector('.option-circle');
                    circle.classList.remove('bg-blue-500');
                    circle.classList.add('bg-gray-500');
                });
                
                // Set selected label
                this.classList.remove('bg-gray-100', 'border-transparent');
                this.classList.add('bg-blue-100', 'border-blue-400');
                const circle = this.querySelector('.option-circle');
                circle.classList.remove('bg-gray-500');
                circle.classList.add('bg-blue-500');
                
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
    }
    
    function submitAnswer() {
        if (questionSubmitted) return;
        
        let answerValue = '';
        
        // Get answer based on question type
        @if($question->type === 'fill_blanks')
            const fillAnswer = document.getElementById('fill-answer');
            if (!fillAnswer.value.trim()) {
                alert('Please enter an answer before submitting.');
                return;
            }
            answerValue = fillAnswer.value.trim();
        @else
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                alert('Please select an answer before submitting.');
                return;
            }
            answerValue = selectedAnswer.value;
        @endif
        
        questionSubmitted = true;
        clearInterval(timerInterval);
        
        const timeTaken = maxTime - timeRemaining;
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        
        const requestData = {
            question_id: '{{ $question->question_id }}',
            answer: answerValue,
            time_taken: timeTaken,
            _token: '{{ csrf_token() }}'
        };
        
        @if(isset($diagnosticMode) && $diagnosticMode)
            // Diagnostic mode submission
            fetch('{{ route("student.quiz.diagnostic.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
        @else
            // Regular assessment submission
            requestData.assessment_id = '{{ $assessmentId ?? "" }}';
            fetch('{{ route("student.quiz.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
        @endif
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFeedback(data);
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.textContent = 'SUBMIT ANSWER';
                questionSubmitted = false;
                startTimer();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'SUBMIT ANSWER';
            questionSubmitted = false;
            startTimer();
        });
    }
    
    function timeoutSubmission() {
        questionSubmitted = true;
        
        // Auto-submit with no answer (timeout)
        const requestData = {
            question_id: '{{ $question->question_id }}',
            answer: '',
            time_taken: maxTime,
            _token: '{{ csrf_token() }}'
        };
        
        @if(isset($diagnosticMode) && $diagnosticMode)
            requestData._method = 'POST';
            fetch('{{ route("student.quiz.diagnostic.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
        @else
            requestData.assessment_id = '{{ $assessmentId ?? "" }}';
            fetch('{{ route("student.quiz.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
        @endif
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFeedback(data, true);
            } else {
                alert('Session timeout. Redirecting...');
                window.location.href = '{{ route("student.assessments") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Session timeout. Redirecting...');
            window.location.href = '{{ route("student.assessments") }}';
        });
    }
    
    function showFeedback(data, isTimeout = false) {
        const feedbackSection = document.getElementById('feedback-section');
        const feedbackMessage = document.getElementById('feedback-message');
        const explanationText = document.getElementById('explanation-text');
        const nextBtn = document.getElementById('next-btn');
        
        // Show feedback
        if (isTimeout) {
            feedbackSection.className = 'mt-6 p-4 rounded-lg bg-orange-50 border border-orange-200';
            feedbackMessage.className = 'font-semibold mb-2 text-orange-800';
            feedbackMessage.textContent = '⏰ Time\'s up!';
        } else if (data.is_correct) {
            feedbackSection.className = 'mt-6 p-4 rounded-lg bg-green-50 border border-green-200';
            feedbackMessage.className = 'font-semibold mb-2 text-green-800';
            feedbackMessage.textContent = '✅ Correct!';
        } else {
            feedbackSection.className = 'mt-6 p-4 rounded-lg bg-red-50 border border-red-200';
            feedbackMessage.className = 'font-semibold mb-2 text-red-800';
            feedbackMessage.textContent = '❌ Incorrect';
        }
        
        if (data.explanation) {
            explanationText.textContent = data.explanation;
        }
        
        if (!isTimeout && data.correct_answer) {
            explanationText.innerHTML += `<br><strong>Correct answer:</strong> ${data.correct_answer}`;
        }
        
        feedbackSection.classList.remove('hidden');
        
        // Handle next question or completion
        console.log('Quiz response data:', data); // DEBUG
        console.log('diagnostic_complete:', data.diagnostic_complete); // DEBUG
        console.log('redirect_url:', data.redirect_url); // DEBUG
        
        if (data.assessment_complete || data.diagnostic_complete) {
            console.log('Setting View Results button'); // DEBUG
            nextBtn.textContent = 'View Results';
            nextBtn.onclick = function() {
                console.log('View Results button clicked, redirecting to:', data.redirect_url); // DEBUG
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    console.log('No redirect_url, going to assessments page'); // DEBUG
                    window.location.href = '{{ route("student.assessments") }}';
                }
            };
        } else if (data.phase_complete && !data.diagnostic_complete) {
            console.log('Phase complete but not diagnostic complete, continuing to next phase'); // DEBUG
            nextBtn.textContent = `Continue to ${data.next_phase || 'Next Phase'}`;
            nextBtn.onclick = function() {
                window.location.reload();
            };
        } else {
            console.log('Regular next question'); // DEBUG
            nextBtn.textContent = 'Next Question';
            nextBtn.onclick = function() {
                window.location.reload();
            };
        }
    }
});
</script>

@endsection