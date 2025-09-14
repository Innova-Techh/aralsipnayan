@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')

<div class="space-y-8 font-baloo mt-8">
<!-- Header Section -->
<!-- Header Section -->
<div class="flex justify-between items-center mb-4 sm:mb-6 md:mb-8 gap-2 sm:gap-4">
    
    <!-- Question Counter -->
    <div class="bg-purple-700 backdrop-blur-sm rounded-full
                px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4
                border-b-6 border-[#4a1377]"
         style="box-shadow: 0 6px 0 #4a1377;">
        <span class="text-white font-semibold
                     text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl">
            @if(isset($diagnosticMode) && $diagnosticMode)
                Phase {{ $diagnosticPhase ?? 1 }} - Question {{ $currentQuestion }} of {{ $totalQuestions }}
            @else
                Question {{ $currentQuestion }} of {{ $totalQuestions }}
            @endif
        </span>
    </div>

    <!-- Timer -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-full
                px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4
                flex items-center gap-2 sm:gap-3 md:gap-4
                border-b-6 border-[#cc4713]"
         style="box-shadow: 0 6px 0 #cc4713;">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 lg:w-7 lg:h-7 xl:w-8 xl:h-8 text-white"
             fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                  clip-rule="evenodd"/>
        </svg>
        <span class="text-white font-bold
                     text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl"
              id="timer-display">{{ $question->max_time ?? 30 }}:00</span>
    </div>
</div>



    <!-- Diagnostic Mode Banner (if applicable) -->
    @if(isset($diagnosticMode) && $diagnosticMode)
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl 
                p-2 sm:p-3 md:p-4 lg:p-4 
                text-white text-center shadow-lg">
        
        <h3 class="font-bold 
                text-sm sm:text-base md:text-md lg:text-md xl:text-xl mb-1 sm:mb-2">
            🔬 Diagnostic Assessment
        </h3>
        
        <!-- Optional description -->
        <!--
        <p class="text-xs sm:text-sm md:text-base lg:text-lg">
            This diagnostic test helps us understand your current skill level. 
            Take your time and do your best!
        </p>
        -->
    </div>
    @endif


    <!-- Quiz Card -->
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl md:rounded-3xl p-6 md:p-5 lg:p-10 shadow-2xl">
        
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
                        <input type="radio" name="answer" value="{{ chr(65 + (int)$index) }}" class="hidden">
                        <div class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-gray-500 text-white rounded-full font-bold text-sm md:text-base mr-4 md:mr-5 option-circle">
                            {{ chr(65 + (int)$index) }}
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

        <!-- Buttons Row -->
        <div class="flex justify-between items-center gap-2 sm:gap-4 mt-6">
            <!-- Submit Button -->
            <button id="submit-btn" 
                class="bg-gradient-to-r from-green-500 to-green-600 text-white 
                    px-4 py-2 sm:px-6 sm:py-2.5 md:px-8 md:py-3 lg:px-12 lg:py-4 
                    rounded-2xl font-bold 
                    text-xs sm:text-sm md:text-base lg:text-lg
                    hover:from-green-600 hover:to-green-700 
                    transition-all duration-200 transform hover:scale-105 
                    border-b-4 border-[#0b830b] shadow-lg">
                SUBMIT ANSWER
            </button>

            <!-- Next Button -->
            <button id="next-btn" 
                class="hidden bg-gradient-to-r from-blue-500 to-blue-600 text-white 
                    px-4 py-2 sm:px-6 sm:py-2.5 md:px-8 md:py-3 lg:px-12 lg:py-4 
                    rounded-2xl font-bold 
                    text-xs sm:text-sm md:text-base lg:text-lg
                    hover:from-blue-600 hover:to-blue-700 
                    transition-all duration-200 transform hover:scale-105 
                    border-b-4 border-[#0b2783] shadow-lg">
                Next Question
            </button>
        </div>


        <!-- Feedback Section (initially hidden) -->
        <div id="feedback-section" class="hidden mt-6 p-4 rounded-lg">
            <div id="feedback-message" class="font-extrabold mb-2 font-baloo"></div>
            <div id="explanation-text" class="text-xl text-gray-700 font-baloo"></div>
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
    let autoSaveInterval;
    let retryAttempts = 0;
    const maxRetryAttempts = 3;
    const nextBtn = document.getElementById('next-btn');

    // Quiz progress state
    const quizState = {
        sessionId: '{{ session("diagnostic_session_id") ?? session("quiz_session_id") ?? "quiz_" . time() }}',
        questionId: '{{ $question->question_id }}',
        competency: '{{ session("diagnostic_competency") ?? $category ?? "" }}',
        isDiagnostic: {{ isset($diagnosticMode) && $diagnosticMode ? 'true' : 'false' }},
        startTime: startTime,
        maxTime: maxTime,
        currentAnswer: '',
        timeTaken: 0,
        questionIndex: {{ session('current_question_index', 0) }},
        totalQuestions: {{ $totalQuestions ?? 15 }}
    };
    
    // Initialize quiz
    initializeQuiz();
    
    function initializeQuiz() {
        // Restore progress from localStorage if available
        restoreProgress();
        
        // Initialize timer
        startTimer();
        
        // Initialize answer selection
        initializeAnswerSelection();
        
        // Initialize auto-save
        startAutoSave();
        
        // Add online/offline detection
        addConnectionMonitoring();
        
        // Submit button handler
        document.getElementById('submit-btn').addEventListener('click', submitAnswer);

        
        // Save progress when user selects answer
        addProgressSaveListeners();
    }
    
    function saveProgressToLocalStorage() {
        try {
            quizState.timeTaken = maxTime - timeRemaining;
            quizState.lastSaved = Date.now();
            
            const storageKey = `quiz_progress_${quizState.sessionId}`;
            localStorage.setItem(storageKey, JSON.stringify(quizState));
            
            console.log('Progress saved to localStorage:', quizState);
        } catch (error) {
            console.error('Failed to save progress to localStorage:', error);
        }
    }
    
    function restoreProgress() {
        try {
            // First try to restore from server-side saved progress
            @if(isset($savedProgress) && $savedProgress)
                const serverProgress = @json($savedProgress);
                if (serverProgress && serverProgress.current_answer) {
                    restoreAnswer(serverProgress.current_answer);
                    console.log('Progress restored from server:', serverProgress);
                    return; // Don't check localStorage if server has progress
                }
            @endif
            
            // Fall back to localStorage
            const storageKey = `quiz_progress_${quizState.sessionId}`;
            const savedProgress = localStorage.getItem(storageKey);
            
            if (savedProgress) {
                const saved = JSON.parse(savedProgress);
                
                // Check if this is the same question
                if (saved.questionId === quizState.questionId) {
                    // Restore answer if available
                    if (saved.currentAnswer) {
                        restoreAnswer(saved.currentAnswer);
                    }
                    
                    // Adjust timer if needed (don't let users get extra time)
                    const timeSinceLastSave = Date.now() - saved.lastSaved;
                    if (timeSinceLastSave < 60000) { // If less than 1 minute ago
                        timeRemaining = Math.max(0, maxTime - saved.timeTaken - Math.floor(timeSinceLastSave / 1000));
                    }
                    
                    console.log('Progress restored from localStorage:', saved);
                }
            }
        } catch (error) {
            console.error('Failed to restore progress:', error);
        }
    }
    
    function restoreAnswer(answer) {
        @if($question->type === 'fill_blanks')
            const fillAnswer = document.getElementById('fill-answer');
            if (fillAnswer) {
                fillAnswer.value = answer;
                quizState.currentAnswer = answer;
            }
        @else
            const option = document.querySelector(`input[name="answer"][value="${answer}"]`);
            if (option) {
                option.checked = true;
                const label = option.closest('.option-label');
                if (label) {
                    label.click(); // Trigger the visual selection
                }
                quizState.currentAnswer = answer;
            }
        @endif
    }
    
    function addProgressSaveListeners() {
        @if($question->type === 'fill_blanks')
            const fillAnswer = document.getElementById('fill-answer');
            if (fillAnswer) {
                fillAnswer.addEventListener('input', function() {
                    quizState.currentAnswer = this.value;
                    saveProgressToLocalStorage();
                });
            }
        @else
            const labels = document.querySelectorAll('.option-label');
            labels.forEach(label => {
                label.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        quizState.currentAnswer = radio.value;
                        saveProgressToLocalStorage();
                    }
                });
            });
        @endif
    }
    
    function startAutoSave() {
        // Auto-save progress every 10 seconds
        autoSaveInterval = setInterval(function() {
            if (!questionSubmitted) {
                saveProgressToLocalStorage();
                syncProgressToServer();
            }
        }, 10000);
    }
    
    function syncProgressToServer() {
        // Only sync if there's an answer to save
        if (!quizState.currentAnswer || questionSubmitted) return;
        
        const syncData = {
            session_id: quizState.sessionId,
            question_id: quizState.questionId,
            current_answer: quizState.currentAnswer,
            time_taken: quizState.timeTaken,
            question_index: quizState.questionIndex,
            _token: '{{ csrf_token() }}'
        };
        
        fetch('{{ route("student.quiz.save-progress") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(syncData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Progress synced to server');
            }
        })
        .catch(error => {
            console.error('Failed to sync progress to server:', error);
            // Keep in localStorage for later sync
        });
    }
    
    function addConnectionMonitoring() {
        window.addEventListener('online', function() {
            console.log('Connection restored - syncing progress');
            syncProgressToServer();
            showConnectionStatus('Connected', 'success');
        });
        
        window.addEventListener('offline', function() {
            console.log('Connection lost - using offline mode');
            showConnectionStatus('Offline - progress saved locally', 'warning');
        });
    }
    
    function showConnectionStatus(message, type) {
        // Create or update connection status indicator
        let statusDiv = document.getElementById('connection-status');
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.id = 'connection-status';
            statusDiv.className = 'fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-semibold z-50';
            document.body.appendChild(statusDiv);
        }
        
        statusDiv.textContent = message;
        statusDiv.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white text-sm font-semibold z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'warning' ? 'bg-yellow-500' : 'bg-red-500'
        }`;
        
        // Auto-hide after 3 seconds for success messages
        if (type === 'success') {
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        }
    }
    
    function submitAnswerWithRetry(requestData, attempt = 1) {
        const url = quizState.isDiagnostic ? 
            '{{ route("student.quiz.diagnostic.submit") }}' : 
            '{{ route("student.quiz.submit") }}';
        
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error(`Submit attempt ${attempt} failed:`, error);
            
            if (attempt < maxRetryAttempts) {
                // Exponential backoff: wait 2^attempt seconds
                const delay = Math.pow(2, attempt) * 1000;
                
                return new Promise((resolve, reject) => {
                    setTimeout(() => {
                        submitAnswerWithRetry(requestData, attempt + 1)
                            .then(resolve)
                            .catch(reject);
                    }, delay);
                });
            } else {
                throw error;
            }
        });
    }
    
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
        clearInterval(autoSaveInterval);
        
        const timeTaken = maxTime - timeRemaining;
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        nextBtn.classList.remove('hidden');

        const requestData = {
            question_id: '{{ $question->question_id }}',
            answer: answerValue,
            time_taken: timeTaken,
            _token: '{{ csrf_token() }}'
        };
        
        @if(!isset($diagnosticMode) || !$diagnosticMode)
            requestData.assessment_id = '{{ $assessmentId ?? "" }}';
        @endif
        
        // Update quiz state
        quizState.currentAnswer = answerValue;
        quizState.timeTaken = timeTaken;
        saveProgressToLocalStorage();
        
        // Submit with retry mechanism
        submitAnswerWithRetry(requestData)
        .then(data => {
            if (data.success) {
                // Clear progress from localStorage on successful submission
                const storageKey = `quiz_progress_${quizState.sessionId}`;
                localStorage.removeItem(storageKey);
                
                showFeedback(data);
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Final submit error:', error);
            
            // Show retry option to user
            const retryBtn = document.createElement('button');
            retryBtn.textContent = 'Retry Submission';
            retryBtn.className = 'ml-4 bg-orange-500 text-white px-4 py-2 rounded-lg';
            retryBtn.onclick = () => {
                retryBtn.remove();
                submitBtn.disabled = false;
                submitBtn.textContent = 'SUBMIT ANSWER';
                questionSubmitted = false;
                submitAnswer();
            };
            
            submitBtn.parentNode.appendChild(retryBtn);
            
            alert('Submission failed. Your progress is saved. You can retry or refresh the page.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'SUBMIT ANSWER';
            questionSubmitted = false;
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
            feedbackSection.className = 'mt-6 p-4 rounded-lg bg-green-200 border border-green-200';
            feedbackMessage.className = 'font-extrabold mb-2 text-green-800 text-2xl';
            feedbackMessage.textContent = '✅ Correct!';
        } else {
            feedbackSection.className = 'mt-6 p-4 rounded-lg bg-red-200 border border-red-200';
            feedbackMessage.className = 'font-extrabold mb-2 text-red-800 text-2xl';
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