@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')

<div class="space-y-8 font-baloo mt-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-2 sm:mb-2 md:mb-4 gap-2 sm:gap-4">
        
        <!-- Question Counter -->
        <div class="bg-purple-700 backdrop-blur-sm rounded-full
                    px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4
                    border-b-6 border-[#4a1377]"
             style="box-shadow: 0 6px 0 #4a1377;">
            <span class="text-white font-semibold
                         text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl"
                  id="question-counter">
                Question <span id="current-question-number">{{ $currentQuestion }}</span> of <span id="total-questions-number">{{ $totalQuestions }}</span>
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
                         text-xs sm:text-sm md:text-base lg:text-lg xl:text-lg"
                  id="timer-display">30:00</span>
        </div>
    </div>

    <!-- Quiz Card -->
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl md:rounded-3xl p-6 md:p-5 lg:p-10 shadow-2xl">
        
        <!-- Controls Row -->
        <div class="flex justify-between items-center mb-6">
            <button id="hint-btn" class="bg-gradient-to-r from-orange-400 to-orange-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-orange-500 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#cc4713] shadow-lg">
                💡 HINT
            </button>
            
            <!-- Audio Toggle Button -->
            <button id="audio-toggle" class="bg-gradient-to-r from-purple-400 to-purple-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-purple-500 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#6d1f7d] shadow-lg">
                🔊 AUDIO ON
            </button>
        </div>

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

        <!-- Hint Display Section (initially hidden) -->
        <div id="hint-section" class="hidden mt-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
            <div class="flex items-start gap-3">
                <div class="bg-blue-500 text-white rounded-full p-2 flex-shrink-0">
                    💡
                </div>
                <div>
                    <h4 class="font-semibold text-blue-800 mb-2">Hint:</h4>
                    <p id="hint-text" class="text-blue-700"></p>
                </div>
            </div>
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
            <div id="feedback-message" class="font-extrabold mb-2 font-baloo text-lg sm:text-xl md:text-2xl"></div>
            <div id="explanation-text" class="text-base sm:text-lg md:text-xl text-gray-700 font-baloo leading-relaxed"></div>
        </div>
    </div>
</div>

<!-- Assessment Loader (initially hidden) -->
<div id="assessment-loader" class="hidden fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50">
    <div class="text-center">
        <!-- Spinner -->
        <svg class="pl mx-auto mb-6" width="200" height="200" viewBox="0 0 240 240">
            <circle class="pl__ring pl__ring--a" cx="120" cy="120" r="105" fill="none"></circle>
            <circle class="pl__ring pl__ring--b" cx="120" cy="120" r="35" fill="none"></circle>
            <circle class="pl__ring pl__ring--c" cx="85" cy="120" r="70" fill="none"></circle>
            <circle class="pl__ring pl__ring--d" cx="155" cy="120" r="70" fill="none"></circle>
        </svg>

        <!-- Loading Text -->
        <div class="text-white">
            <h3 class="text-2xl font-bold mb-2">Processing Assessment Results</h3>
            <p class="text-base opacity-80" id="loader-status">Calculating your performance...</p>
        </div>
    </div>
</div>

<style>
    /* Assessment Loader Styles */
    .pl {
        width: 6em;
        height: 6em;
    }

    @media (max-width: 480px) {
        .pl {
            width: 4em;
            height: 4em;
        }
    }

    @media (min-width: 1024px) {
        .pl {
            width: 7em;
            height: 7em;
        }
    }

    .pl__ring {
        animation: ringA 2s linear infinite;
        stroke-width: 20;
        stroke-linecap: round;
    }

    .pl__ring--a {
        stroke: #f42f25;
    }

    .pl__ring--b {
        animation-name: ringB;
        stroke: #f49725;
    }

    .pl__ring--c {
        animation-name: ringC;
        stroke: #255ff4;
    }

    .pl__ring--d {
        animation-name: ringD;
        stroke: #f42582;
    }

    /* Spinner Animations */
    @keyframes ringA {
        from, 4% {
            stroke-dasharray: 0 660;
            stroke-dashoffset: -330;
        }
        12% {
            stroke-dasharray: 60 600;
            stroke-dashoffset: -335;
        }
        32% {
            stroke-dasharray: 60 600;
            stroke-dashoffset: -595;
        }
        40%, 54% {
            stroke-dasharray: 0 660;
            stroke-dashoffset: -660;
        }
        62% {
            stroke-dasharray: 60 600;
            stroke-dashoffset: -665;
        }
        82% {
            stroke-dasharray: 60 600;
            stroke-dashoffset: -925;
        }
        90%, to {
            stroke-dasharray: 0 660;
            stroke-dashoffset: -990;
        }
    }

    @keyframes ringB {
        from, 12% {
            stroke-dasharray: 0 220;
            stroke-dashoffset: -110;
        }
        20% {
            stroke-dasharray: 20 200;
            stroke-dashoffset: -115;
        }
        40% {
            stroke-dasharray: 20 200;
            stroke-dashoffset: -195;
        }
        48%, 62% {
            stroke-dasharray: 0 220;
            stroke-dashoffset: -220;
        }
        70% {
            stroke-dasharray: 20 200;
            stroke-dashoffset: -225;
        }
        90% {
            stroke-dasharray: 20 200;
            stroke-dashoffset: -305;
        }
        98%, to {
            stroke-dasharray: 0 220;
            stroke-dashoffset: -330;
        }
    }

    @keyframes ringC {
        from {
            stroke-dasharray: 0 440;
            stroke-dashoffset: 0;
        }
        8% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -5;
        }
        28% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -175;
        }
        36%, 58% {
            stroke-dasharray: 0 440;
            stroke-dashoffset: -220;
        }
        66% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -225;
        }
        86% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -395;
        }
        94%, to {
            stroke-dasharray: 0 440;
            stroke-dashoffset: -440;
        }
    }

    @keyframes ringD {
        from, 8% {
            stroke-dasharray: 0 440;
            stroke-dashoffset: 0;
        }
        16% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -5;
        }
        36% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -175;
        }
        44%, 50% {
            stroke-dasharray: 0 440;
            stroke-dashoffset: -220;
        }
        58% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -225;
        }
        78% {
            stroke-dasharray: 40 400;
            stroke-dashoffset: -395;
        }
        86%, to {
            stroke-dasharray: 0 440;
            stroke-dashoffset: -440;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quiz-wide timer settings (get time limit from database, default 30 minutes)
    const quizTimeLimit = {{ $timeLimit ?? 30 }} * 60; // Convert minutes to seconds
    
    // Get quiz start time from controller (JavaScript timestamp in milliseconds)
    const quizStartTime = {{ $quizStartTime ?? 'Date.now()' }};
    const elapsedQuizTime = Math.floor((Date.now() - quizStartTime) / 1000);
    let quizTimeRemaining = Math.max(0, quizTimeLimit - elapsedQuizTime);
    
    // Validate quiz time remaining - if negative or zero, end quiz
    if (quizTimeRemaining <= 0) {
        setTimeout(() => {
            endQuizDueToTimeout();
        }, 100);
        return;
    }
    
    // Per-question timing (for BKT calculation only)
    let questionMaxTime = {{ $question->max_time ?? 30 }};
    let questionStartTime = Date.now();
    
    let timerInterval;
    let questionSubmitted = false;
    let autoSaveInterval;
    let retryAttempts = 0;
    const maxRetryAttempts = 3;
    const nextBtn = document.getElementById('next-btn');

    // Quiz progress state
    const quizState = {
        sessionId: '{{ session("quiz_session_id") ?? "quiz_" . time() }}',
        questionId: '{{ $question->question_id }}',
        competency: '{{ $category ?? "" }}',
        quizStartTime: quizStartTime,
        questionMaxTime: questionMaxTime,
        currentAnswer: '',
        questionTimeTaken: 0,
        questionIndex: {{ $currentQuestion ?? 1 }} - 1, // Convert to 0-based index
        totalQuestions: {{ $totalQuestions ?? 15 }},
        quizTimeRemaining: quizTimeRemaining,
        audioEnabled: true
    };
    
    // Debug log quiz state
    console.log('Quiz state initialized:', quizState);
    console.log('Current question from PHP:', {{ $currentQuestion ?? 1 }});
    console.log('Total questions from PHP:', {{ $totalQuestions ?? 15 }});
    
    // Initialize quiz
    initializeQuiz();
    
    // Check for resumable quiz session on page load
    checkForResumableSession();
    
    function checkForResumableSession() {
        // Check if there's an incomplete assessment session for this user and category
        const assessmentId = '{{ $assessmentId ?? "" }}';
        const category = '{{ $category ?? "" }}';
        
        if (assessmentId && category) {
            // Check session storage for resume data
            const resumeKey = `quiz_resume_${assessmentId}`;
            const resumeData = sessionStorage.getItem(resumeKey);
            
            if (resumeData) {
                try {
                    const data = JSON.parse(resumeData);
                    
                    // Check if resume is still valid (within time limit)
                    const timeElapsed = Math.floor((Date.now() - data.quizStartTime) / 1000);
                    if (timeElapsed < quizTimeLimit) {
                        showResumeDialog(data);
                        return;
                    } else {
                        // Session expired, clear it
                        sessionStorage.removeItem(resumeKey);
                    }
                } catch (error) {
                    console.error('Error parsing resume data:', error);
                    sessionStorage.removeItem(resumeKey);
                }
            }
        }
    }
    
    function showResumeDialog(resumeData) {
        const dialog = document.createElement('div');
        dialog.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
        dialog.innerHTML = `
            <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
                <div class="mb-6">
                    <div class="text-6xl mb-4">⏰</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Resume Quiz Session?</h3>
                    <p class="text-gray-600">
                        You have an incomplete quiz session. Would you like to continue where you left off?
                    </p>
                    <div class="mt-4 text-sm text-gray-500">
                        <p>Time remaining: <span class="font-semibold">${formatTime(resumeData.timeRemaining)}</span></p>
                        <p>Progress: <span class="font-semibold">${resumeData.currentQuestion} of ${resumeData.totalQuestions} questions</span></p>
                    </div>
                </div>
                <div class="flex gap-4 justify-center">
                    <button id="resume-btn" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full font-semibold transition-all duration-200">
                        📚 Resume Quiz
                    </button>
                    <button id="restart-btn" 
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-full font-semibold transition-all duration-200">
                        🔄 Start Over
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(dialog);
        
        // Handle resume
        document.getElementById('resume-btn').addEventListener('click', function() {
            resumeQuizSession(resumeData);
            document.body.removeChild(dialog);
        });
        
        // Handle restart
        document.getElementById('restart-btn').addEventListener('click', function() {
            clearQuizSession();
            document.body.removeChild(dialog);
        });
    }
    
    function resumeQuizSession(resumeData) {
        try {
            // Update quiz state with resume data
            quizState.quizStartTime = resumeData.quizStartTime;
            quizState.questionIndex = resumeData.currentQuestion - 1; // Convert to 0-based
            quizState.totalQuestions = resumeData.totalQuestions;
            quizTimeRemaining = resumeData.timeRemaining;
            
            // Update UI
            updateQuestionCounterDisplay();
            updateQuizTimerDisplay();
            
            // Restore any saved answer for current question
            if (resumeData.currentAnswer) {
                restoreAnswer(resumeData.currentAnswer);
            }
            
            console.log('Quiz session resumed successfully');
            showConnectionStatus('Quiz session resumed!', 'success');
            
        } catch (error) {
            console.error('Error resuming quiz session:', error);
            showConnectionStatus('Error resuming session, starting fresh', 'error');
        }
    }
    
    function clearQuizSession() {
        const assessmentId = '{{ $assessmentId ?? "" }}';
        if (assessmentId) {
            const resumeKey = `quiz_resume_${assessmentId}`;
            sessionStorage.removeItem(resumeKey);
            localStorage.removeItem(`quiz_progress_${quizState.sessionId}`);
        }
        console.log('Quiz session cleared, starting fresh');
    }
    
    function saveQuizSession() {
        const assessmentId = '{{ $assessmentId ?? "" }}';
        if (assessmentId) {
            const resumeKey = `quiz_resume_${assessmentId}`;
            const resumeData = {
                quizStartTime: quizState.quizStartTime,
                currentQuestion: quizState.questionIndex + 1,
                totalQuestions: quizState.totalQuestions,
                timeRemaining: quizTimeRemaining,
                currentAnswer: quizState.currentAnswer,
                lastSaved: Date.now()
            };
            
            try {
                sessionStorage.setItem(resumeKey, JSON.stringify(resumeData));
                console.log('Quiz session saved for resume');
            } catch (error) {
                console.error('Failed to save quiz session:', error);
            }
        }
    }
    
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
    
    function initializeQuiz() {
        // Restore progress from localStorage if available
        restoreProgress();
        
        // Restore audio preference
        restoreAudioPreference();
        
        // Initialize quiz-wide timer (30 minutes for entire quiz)
        startQuizTimer();
        
        // Initialize answer selection
        initializeAnswerSelection();
        
        // Initialize auto-save
        startAutoSave();
        
        // Add online/offline detection
        addConnectionMonitoring();
        
        // Initialize audio system
        initializeAudio();
        
        // Submit button handler
        document.getElementById('submit-btn').addEventListener('click', submitAnswer);
        
        // Audio toggle handler
        document.getElementById('audio-toggle').addEventListener('click', toggleAudio);
        
        // Hint button handler
        const hintBtn = document.getElementById('hint-btn');
        if (hintBtn) {
            hintBtn.addEventListener('click', getHint);
        }
        
        // Save progress when user selects answer
        addProgressSaveListeners();
        
        // Update question counter display
        updateQuestionCounterDisplay();
    }
    
    function saveProgressToLocalStorage() {
        try {
            quizState.questionTimeTaken = Math.floor((Date.now() - questionStartTime) / 1000);
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
                    return;
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
                    
                    // Adjust quiz timer based on elapsed time
                    const timeSinceLastSave = Date.now() - saved.lastSaved;
                    if (timeSinceLastSave < 60000) { // If less than 1 minute ago
                        const elapsedQuizTime = Math.floor((Date.now() - quizState.quizStartTime) / 1000);
                        quizTimeRemaining = Math.max(0, quizTimeLimit - elapsedQuizTime);
                        quizState.quizTimeRemaining = quizTimeRemaining;
                    }
                    
                    console.log('Progress restored from localStorage:', saved);
                }
            }
        } catch (error) {
            console.error('Failed to restore progress:', error);
        }
    }
    
    function restoreAudioPreference() {
        try {
            const savedAudioPreference = localStorage.getItem('quiz_audio_enabled');
            if (savedAudioPreference !== null) {
                quizState.audioEnabled = savedAudioPreference === 'true';
                updateAudioButtonDisplay();
            }
        } catch (error) {
            console.error('Failed to restore audio preference:', error);
        }
    }
    
    function updateAudioButtonDisplay() {
        const audioToggleBtn = document.getElementById('audio-toggle');
        if (quizState.audioEnabled) {
            audioToggleBtn.innerHTML = '🔊 AUDIO ON';
            audioToggleBtn.className = 'bg-gradient-to-r from-purple-400 to-purple-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-purple-500 hover:to-purple-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#6d1f7d] shadow-lg';
        } else {
            audioToggleBtn.innerHTML = '🔇 AUDIO OFF';
            audioToggleBtn.className = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-gray-500 hover:to-gray-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#5d5d5d] shadow-lg';
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
                    label.click();
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
                saveQuizSession(); // Save session for resume functionality
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
            time_taken: quizState.questionTimeTaken,
            question_index: quizState.questionIndex,
            _token: '{{ csrf_token() }}'
        };
        
        fetch('{{ route("student.quiz.save-progress") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    function initializeAudio() {
        try {
            window.correctAudio = new Audio('{{ asset("audio/correct.mp3") }}');
            window.incorrectAudio = new Audio('{{ asset("audio/incorrect.mp3") }}');
            
            window.correctAudio.volume = 0.7;
            window.incorrectAudio.volume = 0.7;
            window.correctAudio.preload = 'auto';
            window.incorrectAudio.preload = 'auto';
            
            console.log('Audio system initialized');
        } catch (error) {
            console.error('Error initializing audio system:', error);
        }
        
        function enableAudioOnFirstInteraction() {
            try {
                if (window.correctAudio) {
                    window.correctAudio.play().then(() => {
                        window.correctAudio.pause();
                        window.correctAudio.currentTime = 0;
                    }).catch(() => {});
                }
                if (window.incorrectAudio) {
                    window.incorrectAudio.play().then(() => {
                        window.incorrectAudio.pause();
                        window.incorrectAudio.currentTime = 0;
                    }).catch(() => {});
                }
                
                document.removeEventListener('click', enableAudioOnFirstInteraction);
                document.removeEventListener('touchstart', enableAudioOnFirstInteraction);
                console.log('Audio enabled after user interaction');
            } catch (error) {
                console.error('Error enabling audio:', error);
            }
        }
        
        document.addEventListener('click', enableAudioOnFirstInteraction);
        document.addEventListener('touchstart', enableAudioOnFirstInteraction);
    }
    
    function toggleAudio() {
        quizState.audioEnabled = !quizState.audioEnabled;
        updateAudioButtonDisplay();
        
        if (quizState.audioEnabled && window.correctAudio) {
            window.correctAudio.currentTime = 0;
            window.correctAudio.play().catch(() => {});
        }
        
        try {
            localStorage.setItem('quiz_audio_enabled', quizState.audioEnabled);
        } catch (error) {
            console.error('Failed to save audio preference:', error);
        }
    }
    
    function getHint() {
        const hintBtn = document.getElementById('hint-btn');
        const originalText = hintBtn.innerHTML;
        
        hintBtn.disabled = true;
        hintBtn.innerHTML = '⏳ Loading...';
        
        fetch('{{ route("student.quiz.hint") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                question_id: quizState.questionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showHintInline(data.hint);
            } else {
                alert('Sorry, no hint is available for this question.');
            }
        })
        .catch(error => {
            console.error('Error getting hint:', error);
            alert('Failed to get hint. Please try again.');
        })
        .finally(() => {
            hintBtn.disabled = false;
            hintBtn.innerHTML = originalText;
        });
    }
    
    function showHintInline(hint) {
        const hintSection = document.getElementById('hint-section');
        const hintText = document.getElementById('hint-text');
        const hintBtn = document.getElementById('hint-btn');
        
        hintText.textContent = hint;
        hintSection.classList.remove('hidden');
        
        hintBtn.innerHTML = '✅ HINT SHOWN';
        hintBtn.disabled = true;
        hintBtn.className = 'bg-gradient-to-r from-gray-400 to-gray-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base cursor-not-allowed border-b-4 border-[#5d5d5d] shadow-lg';
        
        hintSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function showConnectionStatus(message, type) {
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
        
        if (type === 'success') {
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        }
    }
    
    function submitAnswerWithRetry(requestData, attempt = 1) {
        const url = '{{ route("student.quiz.submit") }}';
        
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    // Timer functions
    function startQuizTimer() {
        // Update timer display immediately
        updateQuizTimerDisplay();
        
        timerInterval = setInterval(function() {
            if (quizTimeRemaining > 0) {
                quizTimeRemaining--;
                updateQuizTimerDisplay();
                
                quizState.quizTimeRemaining = quizTimeRemaining;
                
                if (quizTimeRemaining <= 0) {
                    clearInterval(timerInterval);
                    endQuizDueToTimeout();
                }
            } else {
                clearInterval(timerInterval);
                updateQuizTimerDisplay();
            }
        }, 1000);
    }
    
    function updateQuizTimerDisplay() {
        const displayTime = Math.max(0, quizTimeRemaining);
        const minutes = Math.floor(displayTime / 60);
        const seconds = displayTime % 60;
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        const timerDisplay = document.getElementById('timer-display');
        if (timerDisplay) {
            timerDisplay.textContent = display;
        }
        
        // Change color when time is running low (5 minutes remaining)
        const timerElement = document.querySelector('.bg-gradient-to-r.from-orange-500');
        if (timerElement) {
            if (quizTimeRemaining <= 300) { // 5 minutes
                timerElement.classList.remove('from-orange-500', 'to-red-500');
                timerElement.classList.add('from-red-600', 'to-red-700');
            }
        }
    }
    
    function updateQuestionCounterDisplay() {
        const currentQuestion = quizState.questionIndex + 1;
        const totalQuestions = quizState.totalQuestions;
        
        // Update the counter display using the new IDs
        const currentQuestionElement = document.getElementById('current-question-number');
        const totalQuestionsElement = document.getElementById('total-questions-number');
        
        if (currentQuestionElement) {
            currentQuestionElement.textContent = currentQuestion;
            console.log(`Updated current question number to: ${currentQuestion}`);
        } else {
            console.error('Could not find current-question-number element');
        }
        
        if (totalQuestionsElement) {
            totalQuestionsElement.textContent = totalQuestions;
            console.log(`Updated total questions number to: ${totalQuestions}`);
        } else {
            console.error('Could not find total-questions-number element');
        }
        
        console.log(`Question counter updated: ${currentQuestion} of ${totalQuestions}`);
        console.log(`Quiz timer remaining: ${Math.floor(quizTimeRemaining / 60)}:${(quizTimeRemaining % 60).toString().padStart(2, '0')}`);
        console.log(`Elapsed quiz time: ${Math.floor((Date.now() - quizState.quizStartTime) / 1000)} seconds`);
    }
    
    function endQuizDueToTimeout() {
        questionSubmitted = true;
        clearInterval(timerInterval);
        clearInterval(autoSaveInterval);
        
        alert('Quiz time has ended! Your current progress will be submitted.');
        
        showAssessmentLoader('Quiz Time Ended', 'Processing your final answers...');
        
        // Force submit current answer or empty answer
        let answerValue = '';
        @if($question->type === 'fill_blanks')
            const fillAnswer = document.getElementById('fill-answer');
            if (fillAnswer && fillAnswer.value.trim()) {
                answerValue = fillAnswer.value.trim();
            }
        @else
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (selectedAnswer) {
                answerValue = selectedAnswer.value;
            }
        @endif
        
        const requestData = {
            question_id: '{{ $question->question_id }}',
            answer: answerValue,
            time_taken: Math.max(1, Math.floor((Date.now() - questionStartTime) / 1000)),
            quiz_timeout: true,
            assessment_id: '{{ $assessmentId ?? "" }}',
            _token: '{{ csrf_token() }}'
        };
        
        submitAnswerWithRetry(requestData)
        .then(data => {
            setTimeout(() => {
                hideAssessmentLoader();
                alert('Quiz has ended due to timeout. Redirecting to results...');
                window.location.href = '{{ route("student.assessments") }}';
            }, 2000);
        })
        .catch(error => {
            console.error('Final timeout submit error:', error);
            hideAssessmentLoader();
            alert('Quiz has ended. Redirecting...');
            window.location.href = '{{ route("student.assessments") }}';
        });
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
        // Don't clear the quiz timer - it should continue running for the entire quiz
        // Only clear the auto-save interval
        clearInterval(autoSaveInterval);
        
        // Calculate actual time taken (ensure it's at least 1 second)
        const actualTimeElapsed = Math.floor((Date.now() - questionStartTime) / 1000);
        const questionTimeTaken = Math.max(1, actualTimeElapsed);
        
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
        nextBtn.classList.remove('hidden');

        const requestData = {
            question_id: '{{ $question->question_id }}',
            answer: answerValue,
            time_taken: questionTimeTaken,
            assessment_id: '{{ $assessmentId ?? "" }}',
            _token: '{{ csrf_token() }}'
        };
        
        // Update quiz state
        quizState.currentAnswer = answerValue;
        quizState.questionTimeTaken = questionTimeTaken;
        saveProgressToLocalStorage();
        
        // Submit with retry mechanism
        submitAnswerWithRetry(requestData)
        .then(data => {
            if (data.success) {
                // Update question counter for the next question
                if (!data.assessment_complete) {
                    quizState.questionIndex++;
                    updateQuestionCounterDisplay();
                }
                
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
                // Restart auto-save since we cleared it earlier
                startAutoSave();
                submitAnswer();
            };
            
            submitBtn.parentNode.appendChild(retryBtn);
            
            alert('Submission failed. Your progress is saved. You can retry or refresh the page.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'SUBMIT ANSWER';
            questionSubmitted = false;
            // Restart auto-save since we cleared it earlier
            startAutoSave();
        });
    }
    
    function showAssessmentLoader(title = 'Processing Assessment Results', status = 'Calculating your performance...') {
        const loader = document.getElementById('assessment-loader');
        const loaderTitle = loader.querySelector('h3');
        const loaderStatus = document.getElementById('loader-status');
        
        loaderTitle.textContent = title;
        loaderStatus.textContent = status;
        
        loader.classList.remove('hidden');
        loader.style.display = 'flex';
        
        if (title === 'Processing Assessment Results') {
            setTimeout(() => {
                loaderStatus.textContent = 'Running BKT algorithm...';
            }, 1000);
            
            setTimeout(() => {
                loaderStatus.textContent = 'Analyzing your knowledge state...';
            }, 2000);
            
            setTimeout(() => {
                loaderStatus.textContent = 'Finalizing results...';
            }, 2500);
        }
    }
    
    function hideAssessmentLoader() {
        const loader = document.getElementById('assessment-loader');
        loader.classList.add('hidden');
        loader.style.display = 'none';
    }
    
    function playAudioFeedback(isCorrect, isTimeout = false) {
        if (!quizState.audioEnabled || isTimeout) return;
        
        try {
            let audio;
            if (isCorrect) {
                audio = window.correctAudio;
            } else {
                audio = window.incorrectAudio;
            }
            
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(error => {
                    console.log('Audio playback failed:', error);
                });
            } else {
                console.log('Audio not available - creating new instance');
                const audioPath = isCorrect ? 
                    '{{ asset("audio/correct.mp3") }}' : 
                    '{{ asset("audio/incorrect.mp3") }}';
                
                const fallbackAudio = new Audio(audioPath);
                fallbackAudio.volume = 0.7;
                fallbackAudio.play().catch(error => {
                    console.log('Fallback audio playback failed:', error);
                });
            }
            
        } catch (error) {
            console.error('Error playing audio feedback:', error);
        }
    }
    
    function showFeedback(data, isTimeout = false) {
        const feedbackSection = document.getElementById('feedback-section');
        const feedbackMessage = document.getElementById('feedback-message');
        const explanationText = document.getElementById('explanation-text');
        const nextBtn = document.getElementById('next-btn');
        
        // Play audio feedback
        playAudioFeedback(data.is_correct, isTimeout);
        
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
        if (data.assessment_complete) {
            nextBtn.textContent = 'View Results';
            nextBtn.onclick = function() {
                showAssessmentLoader();
                
                setTimeout(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '{{ route("student.assessments") }}';
                    }
                }, 3000);
            };
        } else {
            nextBtn.textContent = 'Next Question';
            nextBtn.onclick = function() {
                // The page will reload to show the next question
                // The quiz timer will continue because it's based on the stored start time
                window.location.reload();
            };
        }
    }
    
    function updateQuestionCounter(currentQuestion, totalQuestions) {
        const progressElement = document.querySelector('.text-center h2');
        if (progressElement) {
            progressElement.textContent = `Question ${currentQuestion} of ${totalQuestions}`;
        }
        
        window.quizState = window.quizState || {};
        window.quizState.currentQuestion = currentQuestion;
        window.quizState.totalQuestions = totalQuestions;
        
        console.log(`Updated counter: Question ${currentQuestion} of ${totalQuestions}`);
    }
});
</script>

@endsection