@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')
@vite(['resources/css/app.css', 'resources/js/app.js'])

<div class="space-y-8 font-baloo mt-8 px-4 xs:px-4 sm:px-4 md:px-8 lg:px-12 pb-24 sm:pb-20 md:pb-16 lg:pb-20">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-2 sm:mb-2 md:mb-4 gap-2 sm:gap-4">

        <!-- Question Counter -->
        <div class="bg-phase-counter drop-shadow-phase-counter backdrop-blur-sm rounded-full px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4">
            <span class="text-white font-semibold text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl">
                Question <span id="current-question-number">{{ $currentQuestion }}</span> of <span id="total-questions-number">{{ $totalQuestions }}</span>
            </span>Your Progress in Number and Algebra
        </div>

        <!-- Timer -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4 flex items-center gap-2 sm:gap-3 md:gap-4 border-b-6 border-[#cc4713]"
             style="box-shadow: 0 6px 0 #cc4713;">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 lg:w-7 lg:h-7 xl:w-8 xl:h-8 text-white"
                 fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                      clip-rule="evenodd"/>
            </svg>
            <span class="text-white font-bold text-xs sm:text-sm md:text-base lg:text-lg xl:text-lg"
                  id="timer-display">30:00</span>
        </div>
    </div>

    <!-- Quiz Card -->
    <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-5 lg:p-10 shadow-2xl">

        <!-- Controls Row -->
        <div class="flex justify-between items-center mb-6">
            <button id="hint-btn" class="bg-gradient-to-r from-orange-400 to-orange-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-orange-500 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#cc4713] shadow-lg">
                💡 HINT
            </button>

            <!-- Settings Button -->
                        <button id="settings-btn" class="bg-phase-counter drop-shadow-phase-counter rounded-full px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4 flex items-center gap-2
                                        hover:bg-purple-800 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-white font-semibold text-xs sm:text-sm md:text-base">Settings</span>
                        </button>
        </div>

        <!-- Question -->
        <div class="mb-8 md:mb-10">
            <h2 class="text-gray-800 text-lg md:text-xl lg:text-2xl font-semibold leading-relaxed">
                {{ $currentQuestion }}. {{ $question->text }}
            </h2>
        </div>

        <!-- Answer Section -->
        <div class="mb-8 md:mb-10" id="answer-section">
            @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                <!-- Multiple Choice / True False Options -->
                <div class="space-y-3" id="multiple-choice-container">
                    @foreach($question->options as $index => $option)
                    <label class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer transition-all duration-200 option-label"
                           style="border-color: #E2E8F0; background-color: white;">
                        <input type="radio" name="answer" value="{{ chr(65 + (int)$index) }}" class="hidden" autocomplete="off">
                        <div class="flex items-center justify-center w-9 h-9 text-white rounded-xl font-bold text-sm mr-3 option-circle flex-shrink-0"
                             style="background-color: #1E293B; color: white;">
                            {{ chr(65 + (int)$index) }}
                        </div>
                        <span class="text-gray-700 font-normal text-base option-text">
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
                           class="w-full p-4 md:p-5 bg-white border-2 rounded-xl text-gray-700 font-medium text-base md:text-lg focus:border-blue-400 focus:bg-blue-50 focus:outline-none transition-all duration-200"
                           style="border-color: #E2E8F0;"
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
                class="bg-submit-answer drop-shadow-submit-answer text-white px-4 py-2 sm:px-6 sm:py-2.5 md:px-8 md:py-3 lg:px-12 lg:py-4 rounded-2xl font-bold text-xs sm:text-sm md:text-base lg:text-lg transition-all duration-200 transform hover:scale-105"
                style="text-shadow: -1px -1px 0 #094724, 1px -1px 0 #094724,-1px 1px 0 #094724, 1px 1px 0 #094724, 0 2px 0 #094724;">
                Submit Answer
            </button>

            <!-- Next Button -->
            <button id="next-btn"
                class="hidden bg-next-question drop-shadow-next-question text-white px-4 py-2 sm:px-6 sm:py-2.5 md:px-8 md:py-3 lg:px-12 lg:py-4 rounded-2xl font-bold text-xs sm:text-sm md:text-base lg:text-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105"
                style="text-shadow: -1px -1px 0 #0E3AB1, 1px -1px 0 #0E3AB1,-1px 1px 0 #0E3AB1, 1px 1px 0 #0E3AB1, 0 2px 0 #0E3AB1;">
                Next Question
            </button>
        </div>

        <!-- Feedback Section (initially hidden) -->
        <div id="feedback-section" class="hidden mt-6 p-5 md:p-6 rounded-xl">
            <div class="flex items-start gap-3 mb-3">
                <div id="feedback-icon"
                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-xl">
                </div>
                <div id="feedback-message" class="font-bold text-xl sm:text-2xl md:text-3xl"></div>
            </div>
            <div id="explanation-text" class="text-sm sm:text-base md:text-lg leading-relaxed"></div>
        </div>
    </div>
</div>

<!-- Points Earned Floating Animation -->
<div id="points-animation-overlay" class="fixed inset-0 pointer-events-none z-[100] hidden">
    <div class="relative w-full h-full">
        <!-- Main Points Display -->
        <div id="points-main-display" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <!-- Points Background Circle -->
            <div class="relative">
                <!-- Outer Glow Ring -->
                <div class="absolute inset-0 rounded-full animate-pulse"
                     style="background: radial-gradient(circle, rgba(255,215,0,0.4) 0%, rgba(255,165,0,0.3) 50%, transparent 100%);
                            width: 200px; height: 200px; transform: translate(-50%, -50%); top: 50%; left: 50%;"></div>

                <!-- Main Points Circle -->
                <div class="w-32 h-32 sm:w-40 sm:h-40 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-full
                           bg-gradient-to-br from-yellow-300 via-orange-400 to-red-500
                           border-4 border-yellow-200 shadow-2xl
                           flex flex-col items-center justify-center
                           transform scale-0 animate-bounce"
                     id="points-circle"
                     style="animation-duration: 0.6s; animation-fill-mode: forwards;">

                    <!-- Points Text -->
                    <div class="text-center">
                        <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-white drop-shadow-lg font-baloo"
                             id="points-earned-text">+0</div>
                        <div class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-yellow-100 uppercase tracking-wide"
                             id="points-label">Points</div>
                    </div>

                    <!-- Sparkle Elements -->
                    <div class="absolute inset-0 rounded-full overflow-hidden">
                        <div class="sparkle sparkle-1">✨</div>
                        <div class="sparkle sparkle-2">⭐</div>
                        <div class="sparkle sparkle-3">💫</div>
                        <div class="sparkle sparkle-4">✨</div>
                        <div class="sparkle sparkle-5">🌟</div>
                        <div class="sparkle sparkle-6">✨</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bonus Points Display (Time Bonus) -->
        <div id="bonus-points-display" class="absolute top-1/3 right-1/4 transform translate-x-1/2 -translate-y-1/2 opacity-0">
            <div class="bg-gradient-to-r from-green-400 to-blue-500
                       text-white font-bold py-2 px-4 rounded-full
                       border-2 border-green-300 shadow-lg
                       animate-bounce"
                 style="animation-delay: 0.3s;">
                <span class="text-sm md:text-base" id="bonus-points-text">+0 Time Bonus!</span>
            </div>
        </div>

        <!-- Achievement Notification -->
        <div id="achievement-notification" class="absolute bottom-1/4 left-1/2 transform -translate-x-1/2 translate-y-1/2 opacity-0">
            <div class="bg-gradient-to-r from-purple-500 to-pink-500
                       text-white font-bold py-3 px-6 rounded-2xl
                       border-2 border-purple-300 shadow-xl
                       animate-pulse"
                 id="achievement-card">
                <div class="flex items-center gap-3">
                    <span class="text-2xl" id="achievement-icon">🏆</span>
                    <div>
                        <div class="text-sm font-bold" id="achievement-title">Level Up!</div>
                        <div class="text-xs opacity-90" id="achievement-desc">You've reached a new level!</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Numbers Animation -->
        <div id="floating-numbers" class="absolute inset-0">
            <!-- Individual floating number elements will be dynamically created -->
        </div>
    </div>
</div>

<!-- Points Breakdown Popup -->
{{-- <div id="points-breakdown-popup" class="fixed bottom-4 right-4 bg-white rounded-2xl shadow-2xl border-2 border-yellow-300 p-4 transform translate-y-full opacity-0 transition-all duration-500 z-50 max-w-sm">
    <div class="flex items-center justify-between mb-3">
        <h4 class="font-bold text-gray-800 text-lg">Points Earned! 🎉</h4>
        <button onclick="hidePointsBreakdown()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>
    <div class="space-y-2 text-sm">
        <div class="flex justify-between items-center py-1">
            <span class="text-gray-600">Base Points:</span>
            <span class="font-bold text-blue-600" id="breakdown-base">+0</span>
        </div>
        <div class="flex justify-between items-center py-1" id="breakdown-bonus-row">
            <span class="text-gray-600">Time Bonus:</span>
            <span class="font-bold text-green-600" id="breakdown-bonus">+0</span>
        </div>
        <hr class="border-gray-200">
        <div class="flex justify-between items-center py-1 text-lg">
            <span class="font-bold text-gray-800">Total:</span>
            <span class="font-bold text-orange-600" id="breakdown-total">+0</span>
        </div>
        <div class="text-xs text-gray-500 mt-2" id="breakdown-stats">
            <!-- Additional stats will be populated here -->
        </div>
    </div>
</div> --}}

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

@include('components.music-setting-modal')
@include('components.retry-modal')
@include('components.sweetalert-config')

<style>
    /* Points Animation Styles */
    .sparkle {
        position: absolute;
        font-size: 1.2rem;
        animation: sparkleFloat 2s ease-in-out infinite;
        opacity: 0;
    }

    .sparkle-1 {
        top: 10%;
        left: 20%;
        animation-delay: 0.1s;
    }

    .sparkle-2 {
        top: 20%;
        right: 15%;
        animation-delay: 0.3s;
    }

    .sparkle-3 {
        bottom: 15%;
        left: 15%;
        animation-delay: 0.5s;
    }

    .sparkle-4 {
        bottom: 25%;
        right: 20%;
        animation-delay: 0.7s;
    }

    .sparkle-5 {
        top: 50%;
        left: 5%;
        animation-delay: 0.9s;
    }

    .sparkle-6 {
        top: 50%;
        right: 5%;
        animation-delay: 1.1s;
    }

    @keyframes sparkleFloat {
        0% {
            opacity: 0;
            transform: translateY(0) scale(0.5);
        }
        20% {
            opacity: 1;
            transform: translateY(-10px) scale(1);
        }
        80% {
            opacity: 1;
            transform: translateY(-20px) scale(1.2);
        }
        100% {
            opacity: 0;
            transform: translateY(-30px) scale(0.8);
        }
    }

    @keyframes pointsScaleIn {
        0% {
            transform: scale(0) rotate(-180deg);
            opacity: 0;
        }
        50% {
            transform: scale(1.2) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
    }

    @keyframes floatingNumber {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(-100px) scale(1.5);
        }
    }

    @keyframes achievementSlideIn {
        0% {
            opacity: 0;
            transform: translateX(-100%) scale(0.8);
        }
        50% {
            opacity: 1;
            transform: translateX(0) scale(1.1);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    .points-circle-animate {
        animation: pointsScaleIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .floating-number {
        position: absolute;
        font-weight: bold;
        font-size: 1.5rem;
        color: #f59e0b;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        animation: floatingNumber 2s ease-out forwards;
        pointer-events: none;
        z-index: 105;
    }

    .achievement-animate {
        animation: achievementSlideIn 0.6s ease-out forwards;
    }

    /* Celebration confetti */
    .confetti {
        position: absolute;
        width: 8px;
        height: 8px;
        background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #f9ca24);
        animation: confettiDrop 3s linear infinite;
    }

    @keyframes confettiDrop {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .floating-number {
            font-size: 1.2rem;
        }

        .sparkle {
            font-size: 1rem;
        }
    }

    /* Audio feedback visual */
    .audio-pulse {
        animation: audioPulse 0.3s ease-in-out;
    }

    @keyframes audioPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

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
    // Force clear all radio buttons immediately (Firefox fix)
    document.querySelectorAll('input[type="radio"][name="answer"]').forEach(radio => {
        radio.checked = false;
    });

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

    // Quiz progress state (make it globally accessible)
    window.quizState = {
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
    
    // Alias for backward compatibility
    const quizState = window.quizState;
    
    // Debug log quiz state
    console.log('Quiz state initialized:', quizState);
    console.log('Current question from PHP:', {{ $currentQuestion ?? 1 }});
    console.log('Total questions from PHP:', {{ $totalQuestions ?? 15 }});
    
    // Initialize quiz
    initializeQuiz();
    
    // Resume functionality is now handled at the assessment list level
    // checkForResumableSession();
    
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
                    
                    // Restore question counter state
                    if (saved.questionIndex !== undefined) {
                        quizState.questionIndex = saved.questionIndex;
                        console.log('Restored question index:', saved.questionIndex);
                    }
                    
                    // Restore total questions count
                    if (saved.totalQuestions !== undefined) {
                        quizState.totalQuestions = saved.totalQuestions;
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
            const savedAudioPreference = localStorage.getItem('sound_effects_enabled');
            if (savedAudioPreference !== null) {
                quizState.audioEnabled = savedAudioPreference !== 'false'; // Default to true
            }
        } catch (error) {
            console.error('Failed to restore audio preference:', error);
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
            // Initialize arrays for randomized sound effects
            window.correctSounds = [
                new Audio('{{ asset("audio/correct.mp3") }}'),      // Original correct sound
                new Audio('{{ asset("audio/awesome.mp3") }}'),
                new Audio('{{ asset("audio/excellent.mp3") }}'),
                new Audio('{{ asset("audio/terrific.mp3") }}')
            ];

            window.incorrectSounds = [
                new Audio('{{ asset("audio/incorrect.mp3") }}'),    // Original incorrect sound
                new Audio('{{ asset("audio/goodeffort.mp3") }}'),
                new Audio('{{ asset("audio/nicetry.mp3") }}')
            ];

            window.levelUpAudio = new Audio('{{ asset("audio/levelup.mp3") }}');

            // Set volume for all correct sounds
            window.correctSounds.forEach(audio => {
                audio.volume = 0.7;
                audio.preload = 'auto';
            });

            // Set volume for all incorrect sounds
            window.incorrectSounds.forEach(audio => {
                audio.volume = 0.7;
                audio.preload = 'auto';
            });

            window.levelUpAudio.volume = 0.8;
            window.levelUpAudio.preload = 'auto';

            console.log('Audio system initialized with randomized sounds including originals');
        } catch (error) {
            console.error('Error initializing audio system:', error);
        }
        
        function enableAudioOnFirstInteraction() {
            try {
                // Enable correct sounds
                if (window.correctSounds && window.correctSounds.length > 0) {
                    window.correctSounds[0].play().then(() => {
                        window.correctSounds[0].pause();
                        window.correctSounds[0].currentTime = 0;
                    }).catch(() => {});
                }
                // Enable incorrect sounds
                if (window.incorrectSounds && window.incorrectSounds.length > 0) {
                    window.incorrectSounds[0].play().then(() => {
                        window.incorrectSounds[0].pause();
                        window.incorrectSounds[0].currentTime = 0;
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
        const url = '{{ route("student.quiz.submit-regular") }}';
        
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
            // Add hover effect
            label.addEventListener('mouseenter', function () {
                const radio = this.querySelector('input[type="radio"]');
                if (!radio.checked) {
                    this.style.backgroundColor = '#F8FAFC';
                    this.style.borderColor = '#CBD5E1';
                }
            });

            label.addEventListener('mouseleave', function () {
                const radio = this.querySelector('input[type="radio"]');
                if (!radio.checked) {
                    this.style.backgroundColor = 'white';
                    this.style.borderColor = '#E2E8F0';
                }
            });

            label.addEventListener('click', function () {
                // Reset all labels to default state
                labels.forEach(l => {
                    l.style.backgroundColor = 'white';
                    l.style.borderColor = '#E2E8F0';
                    const circle = l.querySelector('.option-circle');
                    circle.style.backgroundColor = '#1E293B';
                    circle.style.color = 'white';
                });

                // Set selected label state
                this.style.backgroundColor = '#DBEAFE'; // light blue
                this.style.borderColor = '#3B82F6'; // blue border
                const circle = this.querySelector('.option-circle');
                circle.style.backgroundColor = '#3B82F6'; // blue circle
                circle.style.color = 'white';

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
                showNoAnswerToast('fill_blanks');
                return;
            }
            answerValue = fillAnswer.value.trim();
        @else
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                showNoAnswerToast('multiple_choice');
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
                // Note: Question counter will be updated when user clicks "Next"
                // Don't increment here to avoid double-counting
                
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

            // Show retry modal instead of button
            showRetryModal();

            // Reset submit button state
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

    function showRetryModal() {
        const modal = document.getElementById('retry-modal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';

        // Add event listener to reload button
        const reloadBtn = document.getElementById('reload-page-btn');
        reloadBtn.onclick = function() {
            window.location.reload();
        };
    }

    function hideRetryModal() {
        const modal = document.getElementById('retry-modal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    
    function playAudioFeedback(isCorrect, isTimeout = false) {
        if (!quizState.audioEnabled || isTimeout) return;

        try {
            if (isCorrect) {
                // For correct answers: select from awesome, excellent, terrific (indices 1-3)
                // Index 0 is correct.mp3 which we'll use as base layer
                const specialSounds = window.correctSounds ? window.correctSounds.slice(1) : null;
                const baseSound = window.correctSounds ? window.correctSounds[0] : null; // correct.mp3

                if (specialSounds && specialSounds.length > 0 && baseSound) {
                    // Select a random special sound (awesome, excellent, terrific)
                    const randomIndex = Math.floor(Math.random() * specialSounds.length);
                    const selectedSpecialSound = specialSounds[randomIndex];

                    console.log(`Playing overlayed correct sounds: correct.mp3 + ${['awesome.mp3', 'excellent.mp3', 'terrific.mp3'][randomIndex]}`);

                    // Play correct.mp3 as base layer
                    baseSound.currentTime = 0;
                    baseSound.play().catch(error => {
                        console.log('Base correct audio playback failed:', error);
                    });

                    // Play special sound overlayed (with slight delay for better effect)
                    setTimeout(() => {
                        selectedSpecialSound.currentTime = 0;
                        selectedSpecialSound.play().catch(error => {
                            console.log('Special correct audio playback failed:', error);
                        });
                    }, 100); // 100ms delay for layered effect

                } else {
                    // Fallback: create audio instances and play them
                    console.log('Sound array not available - creating overlayed instances');
                    const baseAudio = new Audio('{{ asset("audio/correct.mp3") }}');
                    const specialSounds = [
                        '{{ asset("audio/awesome.mp3") }}',
                        '{{ asset("audio/excellent.mp3") }}',
                        '{{ asset("audio/terrific.mp3") }}'
                    ];

                    const randomIndex = Math.floor(Math.random() * specialSounds.length);
                    const specialAudio = new Audio(specialSounds[randomIndex]);

                    baseAudio.volume = 0.6; // Slightly lower volume for base
                    specialAudio.volume = 0.7;

                    baseAudio.play().catch(error => {
                        console.log('Fallback base audio playback failed:', error);
                    });

                    setTimeout(() => {
                        specialAudio.play().catch(error => {
                            console.log('Fallback special audio playback failed:', error);
                        });
                    }, 100);
                }

            } else {
                // For incorrect answers: overlay incorrect.mp3 with goodeffort.mp3 or nicetry.mp3
                // Index 0 is incorrect.mp3 which we'll use as base layer
                const specialSounds = window.incorrectSounds ? window.incorrectSounds.slice(1) : null;
                const baseSound = window.incorrectSounds ? window.incorrectSounds[0] : null; // incorrect.mp3

                if (specialSounds && specialSounds.length > 0 && baseSound) {
                    // Select a random special sound (goodeffort, nicetry)
                    const randomIndex = Math.floor(Math.random() * specialSounds.length);
                    const selectedSpecialSound = specialSounds[randomIndex];

                    console.log(`Playing overlayed incorrect sounds: incorrect.mp3 + ${['goodeffort.mp3', 'nicetry.mp3'][randomIndex]}`);

                    // Play incorrect.mp3 as base layer
                    baseSound.currentTime = 0;
                    baseSound.play().catch(error => {
                        console.log('Base incorrect audio playback failed:', error);
                    });

                    // Play special sound overlayed (with slight delay for better effect)
                    setTimeout(() => {
                        selectedSpecialSound.currentTime = 0;
                        selectedSpecialSound.play().catch(error => {
                            console.log('Special incorrect audio playback failed:', error);
                        });
                    }, 100); // 100ms delay for layered effect

                } else {
                    // Fallback: create audio instances and play them
                    console.log('Sound array not available - creating overlayed instances');
                    const baseAudio = new Audio('{{ asset("audio/incorrect.mp3") }}');
                    const specialSounds = [
                        '{{ asset("audio/goodeffort.mp3") }}',
                        '{{ asset("audio/nicetry.mp3") }}'
                    ];

                    const randomIndex = Math.floor(Math.random() * specialSounds.length);
                    const specialAudio = new Audio(specialSounds[randomIndex]);

                    baseAudio.volume = 0.6; // Slightly lower volume for base
                    specialAudio.volume = 0.7;

                    baseAudio.play().catch(error => {
                        console.log('Fallback base audio playback failed:', error);
                    });

                    setTimeout(() => {
                        specialAudio.play().catch(error => {
                            console.log('Fallback special audio playback failed:', error);
                        });
                    }, 100);
                }
            }

        } catch (error) {
            console.error('Error playing audio feedback:', error);
        }
    }

    // Points Animation Functions
    function showPointsAnimation(data) {
        const overlay = document.getElementById('points-animation-overlay');
        const pointsCircle = document.getElementById('points-circle');
        const pointsText = document.getElementById('points-earned-text');
        const bonusDisplay = document.getElementById('bonus-points-display');
        const bonusText = document.getElementById('bonus-points-text');
        const achievementNotification = document.getElementById('achievement-notification');

        // Set up points data
        const totalPoints = data.points_earned || 0;
        const basePoints = data.base_points || 0;
        const bonusPoints = data.bonus_points || 0;
        const gamification = data.gamification || {};

        // Show main points
        pointsText.textContent = `+${totalPoints}`;

        // Reset animations
        pointsCircle.classList.remove('points-circle-animate');
        overlay.classList.remove('hidden');

        // Trigger main animation
        setTimeout(() => {
            pointsCircle.classList.add('points-circle-animate');
        }, 100);

        // Show bonus points if any
        if (bonusPoints > 0) {
            bonusText.textContent = `+${bonusPoints} Time Bonus!`;
            setTimeout(() => {
                bonusDisplay.style.opacity = '1';
                bonusDisplay.style.transform = 'translate(50%, -50%) scale(1)';
            }, 600);
        }

        // Show achievement notifications
        if (gamification.level_up || gamification.rank_up) {
            showAchievementNotification(gamification);
        }

        // Create floating numbers
        createFloatingNumbers(totalPoints, basePoints, bonusPoints);

        // Show confetti for good scores
        if (data.is_correct && totalPoints >= 15) {
            createConfetti();
        }

        // Show points breakdown popup
        setTimeout(() => {
            showPointsBreakdown(data);
        }, 1500);

        // Auto-hide after duration
        setTimeout(() => {
            hidePointsAnimation();
        }, 4000);
    }

    function showAchievementNotification(gamification) {
        const achievementNotification = document.getElementById('achievement-notification');
        const achievementIcon = document.getElementById('achievement-icon');
        const achievementTitle = document.getElementById('achievement-title');
        const achievementDesc = document.getElementById('achievement-desc');

        if (gamification.level_up) {
            achievementIcon.textContent = '🆙';
            achievementTitle.textContent = 'Level Up!';
            achievementDesc.textContent = `You reached level ${gamification.current_level}!`;

            // Play level up audio
            if (quizState.audioEnabled && window.levelUpAudio) {
                window.levelUpAudio.currentTime = 0;
                window.levelUpAudio.play().catch(error => {
                    console.log('Level up audio playback failed:', error);
                });
            }
        } else if (gamification.rank_up) {
            achievementIcon.textContent = '🏆';
            achievementTitle.textContent = 'Rank Up!';
            achievementDesc.textContent = `New rank: ${gamification.current_rank.name}!`;

            // Play level up audio for rank up too
            if (quizState.audioEnabled && window.levelUpAudio) {
                window.levelUpAudio.currentTime = 0;
                window.levelUpAudio.play().catch(error => {
                    console.log('Rank up audio playback failed:', error);
                });
            }
        }

        setTimeout(() => {
            achievementNotification.style.opacity = '1';
            achievementNotification.classList.add('achievement-animate');
        }, 1000);
    }

    function createFloatingNumbers(total, base, bonus) {
        const floatingContainer = document.getElementById('floating-numbers');

        // Clear existing floating numbers
        floatingContainer.innerHTML = '';

        // Create floating number for total
        if (total > 0) {
            createFloatingNumber(floatingContainer, `+${total}`, 'center');
        }

        // Create floating numbers for base points
        if (base > 0) {
            setTimeout(() => {
                createFloatingNumber(floatingContainer, `+${base}`, 'left', '#3B82F6');
            }, 300);
        }

        // Create floating numbers for bonus
        if (bonus > 0) {
            setTimeout(() => {
                createFloatingNumber(floatingContainer, `+${bonus}`, 'right', '#10B981');
            }, 600);
        }
    }

    function createFloatingNumber(container, text, position, color = '#f59e0b') {
        const number = document.createElement('div');
        number.className = 'floating-number font-baloo';
        number.textContent = text;
        number.style.color = color;

        // Position based on parameter
        if (position === 'center') {
            number.style.left = '50%';
            number.style.top = '40%';
            number.style.transform = 'translateX(-50%)';
        } else if (position === 'left') {
            number.style.left = '25%';
            number.style.top = '35%';
        } else if (position === 'right') {
            number.style.right = '25%';
            number.style.top = '35%';
        }

        container.appendChild(number);

        // Remove after animation
        setTimeout(() => {
            if (number.parentNode) {
                number.parentNode.removeChild(number);
            }
        }, 2000);
    }

    function createConfetti() {
        const overlay = document.getElementById('points-animation-overlay');
        const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#a55eea', '#26de81'];

        for (let i = 0; i < 30; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 2 + 's';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';

                overlay.appendChild(confetti);

                // Remove after animation
                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.parentNode.removeChild(confetti);
                    }
                }, 5000);
            }, i * 100);
        }
    }

    function showPointsBreakdown(data) {
        const popup = document.getElementById('points-breakdown-popup');
        const breakdownBase = document.getElementById('breakdown-base');
        const breakdownBonus = document.getElementById('breakdown-bonus');
        const breakdownBonusRow = document.getElementById('breakdown-bonus-row');
        const breakdownTotal = document.getElementById('breakdown-total');
        const breakdownStats = document.getElementById('breakdown-stats');

        // Populate breakdown data
        breakdownBase.textContent = `+${data.base_points || 0}`;
        breakdownBonus.textContent = `+${data.bonus_points || 0}`;
        breakdownTotal.textContent = `+${data.points_earned || 0}`;

        // Hide bonus row if no bonus
        if (!data.bonus_points || data.bonus_points === 0) {
            breakdownBonusRow.style.display = 'none';
        } else {
            breakdownBonusRow.style.display = 'flex';
        }

        // Add gamification stats if available
        if (data.gamification) {
            const stats = [];
            if (data.gamification.total_points) {
                stats.push(`Total: ${data.gamification.total_points} pts`);
            }
            if (data.gamification.current_level) {
                stats.push(`Level ${data.gamification.current_level}`);
            }
            if (data.gamification.streak > 1) {
                stats.push(`${data.gamification.streak} day streak!`);
            }
            breakdownStats.textContent = stats.join(' • ');
        }

        // Animate popup
        popup.style.transform = 'translateY(0)';
        popup.style.opacity = '1';

        // Auto-hide after 5 seconds
        setTimeout(() => {
            hidePointsBreakdown();
        }, 5000);
    }

    function hidePointsBreakdown() {
        const popup = document.getElementById('points-breakdown-popup');
        popup.style.transform = 'translateY(100%)';
        popup.style.opacity = '0';
    }

    function hidePointsAnimation() {
        const overlay = document.getElementById('points-animation-overlay');
        const bonusDisplay = document.getElementById('bonus-points-display');
        const achievementNotification = document.getElementById('achievement-notification');

        // Reset all elements
        bonusDisplay.style.opacity = '0';
        bonusDisplay.style.transform = 'translate(50%, -50%) scale(0.8)';
        achievementNotification.style.opacity = '0';
        achievementNotification.classList.remove('achievement-animate');

        // Hide overlay
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, 500);
    }

    // SweetAlert toast for empty answers
    function showNoAnswerToast(questionType) {
        if (questionType === 'fill_blanks') {
            showWarningToast('Please type your answer in the text box before submitting! 📝');
        } else {
            showWarningToast('Please select one of the answer choices before submitting! 🎯');
        }
    }
    
    function showFeedback(data, isTimeout = false) {
        const feedbackSection = document.getElementById('feedback-section');
        const feedbackMessage = document.getElementById('feedback-message');
        const feedbackIcon = document.getElementById('feedback-icon');
        const explanationText = document.getElementById('explanation-text');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');

        // Update submit button to show submission is complete
        submitBtn.textContent = 'Submitted ✓';
        submitBtn.classList.remove('bg-submit-answer', 'drop-shadow-submit-answer', 'hover:scale-105');
        submitBtn.classList.add('cursor-not-allowed');
        submitBtn.style.background = 'linear-gradient(to right, #9ca3af, #6b7280)';
        submitBtn.style.boxShadow = '0 4px 0 #4b5563';

        // Play audio feedback
        playAudioFeedback(data.is_correct, isTimeout);

        // Show points animation if points were earned
        if (!isTimeout && data.points_earned && data.points_earned > 0) {
            showPointsAnimation(data);
        }

        // Show feedback
        if (isTimeout) {
            feedbackSection.className = 'mt-6 p-5 md:p-6 rounded-xl bg-orange-600';
            feedbackIcon.innerHTML = '⏰';
            feedbackMessage.textContent = 'Time\'s up!';
        } else if (data.is_correct) {
            feedbackSection.className = 'mt-6 p-5 md:p-6 rounded-xl bg-[#065F46]';
            feedbackSection.style.boxShadow = '0 6px 0 #054835';
            feedbackIcon.innerHTML = '✓';
            feedbackIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-white text-green-600 text-2xl font-bold';
            feedbackMessage.style.color = '#A7F3D0';
            feedbackMessage.textContent = 'You are correct!';
            explanationText.style.color = '#E2E8F0';
        } else {
            feedbackSection.className = 'mt-6 p-5 md:p-6 rounded-xl bg-[#7F1D1D]';
            feedbackSection.style.boxShadow = '0 6px 0 #630E0E';
            feedbackIcon.innerHTML = '✕';
            feedbackIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-white text-red-700 text-2xl font-bold';
            feedbackMessage.style.color = '#F87171';
            feedbackMessage.textContent = 'You are wrong!';
            explanationText.style.color = '#F3F4F6';
        }

        if (data.explanation) {
            explanationText.textContent = data.explanation;
        }

        if (!isTimeout && data.correct_answer) {
            explanationText.innerHTML += `<br><strong class="text-green-200">Correct answer: ${data.correct_answer}</strong>`;
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
                // Update question counter before reloading
                quizState.questionIndex++;
                updateQuestionCounterDisplay();

                // Save the updated state to localStorage before reload
                saveProgressToLocalStorage();

                // Clear all form inputs before reload (Firefox fix)
                document.querySelectorAll('input[type="radio"][name="answer"]').forEach(radio => {
                    radio.checked = false;
                });

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