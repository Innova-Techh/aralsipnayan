@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')
@include('components.level-up-modal')
<!-- Confetti Canvas -->
<canvas id="confetti-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-50"></canvas>

<!-- Celebration Particles -->
<div id="celebration-particles" class="fixed inset-0 pointer-events-none z-40"></div>

<!-- Simple Level Up Notification (inspired by levelup.webp) -->
<div id="simple-level-up" class="fixed inset-0 z-[70] hidden flex items-center justify-center">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Level Up Content -->
    <div class="relative z-10 text-center">
        <!-- Shield with Wings Badge (matching levelup.webp) -->
        <div class="relative mb-6">
            <!-- Outer glow effect -->
            <div class="absolute inset-0 w-48 h-36 mx-auto bg-yellow-400 blur-3xl opacity-60 animate-pulse"></div>

            <!-- Shield with Wings Container -->
            <div class="relative w-48 h-36 mx-auto">
                <!-- Left Wing -->
                <div class="absolute top-4 left-0 w-16 h-24 bg-gradient-to-br from-yellow-300 to-yellow-500 transform -rotate-12 shadow-lg opacity-90" style="clip-path: polygon(0% 0%, 70% 0%, 100% 50%, 70% 100%, 0% 80%, 10% 40%);">
                    <!-- Wing feather details -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                    <div class="absolute top-1/4 left-1/4 w-2 h-8 bg-yellow-200 opacity-30 transform rotate-45"></div>
                    <div class="absolute top-1/2 left-1/3 w-1.5 h-6 bg-yellow-200 opacity-25 transform rotate-30"></div>
                </div>

                <!-- Right Wing -->
                <div class="absolute top-4 right-0 w-16 h-24 bg-gradient-to-bl from-yellow-300 to-yellow-500 transform rotate-12 shadow-lg opacity-90" style="clip-path: polygon(30% 0%, 100% 0%, 90% 40%, 100% 80%, 30% 100%, 0% 50%);">
                    <!-- Wing feather details -->
                    <div class="absolute inset-0 bg-gradient-to-bl from-white/20 to-transparent"></div>
                    <div class="absolute top-1/4 right-1/4 w-2 h-8 bg-yellow-200 opacity-30 transform -rotate-45"></div>
                    <div class="absolute top-1/2 right-1/3 w-1.5 h-6 bg-yellow-200 opacity-25 transform -rotate-30"></div>
                </div>

                <!-- Main Shield -->
                <div class="absolute top-2 left-1/2 transform -translate-x-1/2 w-28 h-32 bg-gradient-to-b from-yellow-300 via-yellow-400 to-orange-500 shadow-2xl relative overflow-hidden" style="clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);">
                    <!-- Inner glow -->
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent"></div>

                    <!-- Shine effect -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/40 to-transparent transform -translate-x-full animate-shimmer"></div>

                    <!-- Level number -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="simple-level-number" class="text-4xl font-black text-white drop-shadow-2xl" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">3</span>
                    </div>
                </div>

                <!-- Additional decorative elements -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-yellow-200 rounded-full shadow-lg animate-pulse"></div>
                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-orange-300 rounded-full shadow-lg animate-pulse delay-500"></div>
            </div>
        </div>

        <!-- Text Messages -->
        <div class="space-y-3">
            <h2 class="text-3xl font-bold text-white drop-shadow-lg" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">You have reached new level!</h2>
            <p id="simple-new-level-text" class="text-2xl font-bold text-yellow-300 drop-shadow-md" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Level 3</p>
            <p class="text-xl font-bold text-white drop-shadow-lg" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">LEVEL UP!</p>
        </div>

        <!-- Continue button -->
        <button onclick="closeSimpleLevelUp()" class="mt-8 bg-white text-blue-600 font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-colors shadow-xl">
            Continue
        </button>
    </div>
</div>

<div class="min-h-screen flex items-center justify-center px-4 relative">
    <div class="bg-white rounded-3xl shadow-xl p-8 max-w-md w-full text-center relative z-30 celebration-card">
        <!-- Trophy Image with Animation -->
        <div class="mb-4 trophy-container">
            <img src="{{ asset('images/assessments/trophy.png') }}" alt="Trophy" class="mx-auto w-24 h-24 trophy-bounce">
            <!-- Sparkle elements around trophy -->
            <div class="sparkle-container">
                <div class="sparkle sparkle-1">✨</div>
                <div class="sparkle sparkle-2">🌟</div>
                <div class="sparkle sparkle-3">⭐</div>
                {{-- <div class="sparkle sparkle-4">💫</div> --}}
                <div class="sparkle sparkle-5">✨</div>
                {{-- <div class="sparkle sparkle-6">🎉</div> --}}
            </div>
        </div>

        <!-- Header with Animation -->
        <h1 class="text-2xl font-bold mb-2 text-bounce">Assessment Complete!</h1>
        <p class="text-gray-600 mb-6 fade-in-up">Great job on completing the assessment!</p>

        <!-- Points and Score with Pulse Animation -->
        <div class="flex justify-around mb-6 stats-container">
            <div class="bg-gradient-to-br from-green-400 to-green-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg points-card scale-in">
                <div class="text-2xl font-bold" id="points-counter" data-target="{{ $assessment->total_points_earned ?? 0 }}">0</div>
                <span class="text-sm font-normal opacity-90">Points Earned</span>
                {{-- <div class="celebration-burst">🏆</div> --}}
            </div>
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white px-6 py-4 rounded-xl font-semibold shadow-lg score-card scale-in">
                <div class="text-2xl font-bold">{{ $assessment->correct_answers ?? 0 }}/{{ $assessment->total_questions ?? 15 }}</div>
                <span class="text-sm font-normal opacity-90">Score</span>
                {{-- <div class="celebration-burst">🏆</div> --}}
            </div>
        </div>

        <!-- Buttons -->
        <div class="space-y-3">
            <!-- Review Assessment -->
            @if(isset($from_regular_quiz) && $from_regular_quiz)
                <a href="{{ route('student.results.review', $category) }}?assessment_id={{ $assessment_id }}"
                   class="w-full inline-block bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white text-lg font-semibold py-3 rounded-2xl border-b-4 border-[#922f26] shadow-lg hover:scale-[1.03] transition-all duration-300">
                    Review Assessment
                </a>
            @else
                <a href="{{ route('student.assessments.review', $category) }}"
                   class="w-full inline-block bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white text-lg font-semibold py-3 rounded-2xl border-b-4 border-[#922f26] shadow-lg hover:scale-[1.03] transition-all duration-300">
                    Review Assessment
                </a>
            @endif

            <!-- Back to Assessments -->
            <a href="{{ route('student.assessments.category', $category) }}"
               class="block w-full text-white py-3 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)] text-center"
               style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                Back to Assessments
            </a>

            <!-- Go to Dashboard -->
            <a href="{{ route('student.dashboard') }}"
               class="block w-full py-3 px-4 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-all duration-300 text-center">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>


<style>
/* Celebration Animations */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translateY(0);
    }
    40%, 43% {
        transform: translateY(-30px);
    }
    70% {
        transform: translateY(-15px);
    }
    90% {
        transform: translateY(-4px);
    }
}

@keyframes textBounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0) scale(1);
    }
    40% {
        transform: translateY(-10px) scale(1.05);
    }
    60% {
        transform: translateY(-5px) scale(1.02);
    }
}

@keyframes scaleIn {
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

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes sparkleFloat {
    0% {
        opacity: 0;
        transform: translateY(0) scale(0.5) rotate(0deg);
    }
    25% {
        opacity: 1;
        transform: translateY(-20px) scale(1) rotate(90deg);
    }
    50% {
        opacity: 1;
        transform: translateY(-40px) scale(1.2) rotate(180deg);
    }
    75% {
        opacity: 1;
        transform: translateY(-30px) scale(0.8) rotate(270deg);
    }
    100% {
        opacity: 0;
        transform: translateY(-60px) scale(0.3) rotate(360deg);
    }
}

@keyframes celebrationBurst {
    0% {
        opacity: 0;
        transform: scale(0);
    }
    50% {
        opacity: 1;
        transform: scale(1.5);
    }
    100% {
        opacity: 0;
        transform: scale(2);
    }
}

@keyframes cardPulse {
    0% {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    50% {
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        transform: translateY(-2px);
    }
    100% {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(0);
    }
}

/* Animation Classes */
.trophy-bounce {
    animation: bounce 2s infinite;
}

.text-bounce {
    animation: textBounce 1.5s ease-in-out infinite;
}

.scale-in {
    animation: scaleIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    animation-delay: 0.3s;
    animation-fill-mode: both;
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
    animation-delay: 0.2s;
    animation-fill-mode: both;
}

.celebration-card {
    animation: scaleIn 0.6s ease-out;
}

/* Sparkle Container */
.sparkle-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.sparkle {
    position: absolute;
    font-size: 1.5rem;
    animation: sparkleFloat 3s ease-in-out infinite;
    pointer-events: none;
}

.sparkle-1 {
    top: 10%;
    left: 10%;
    animation-delay: 0.1s;
}

.sparkle-2 {
    top: 20%;
    right: 10%;
    animation-delay: 0.3s;
}

.sparkle-3 {
    bottom: 20%;
    left: 15%;
    animation-delay: 0.5s;
}

.sparkle-4 {
    bottom: 10%;
    right: 15%;
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

/* Celebration Burst */
.celebration-burst {
    position: absolute;
    top: -10px;
    right: -10px;
    font-size: 1.2rem;
    animation: celebrationBurst 2s ease-in-out infinite;
    animation-delay: 1s;
}

/* Card Hover Effects */
.points-card, .score-card {
    transition: all 0.3s ease;
    animation: cardPulse 2s ease-in-out infinite;
    position: relative;
    overflow: hidden;
}

/* Points Counter Animation */
#points-counter {
    transition: transform 0.1s ease-in-out;
    font-weight: bold;
}

.points-card {
    animation-delay: 0.5s;
}

.score-card {
    animation-delay: 0.7s;
}

.points-card:hover, .score-card:hover {
    transform: scale(1.05) translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.3);
}

/* Floating Particles */
.floating-particle {
    position: absolute;
    font-size: 1.5rem;
    animation: floatUp 4s linear infinite;
    pointer-events: none;
}

@keyframes floatUp {
    0% {
        opacity: 1;
        transform: translateY(100vh) rotate(0deg);
    }
    100% {
        opacity: 0;
        transform: translateY(-100px) rotate(360deg);
    }
}

/* Button Enhancements */
.space-y-3 > a {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.space-y-3 > a:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.space-y-3 > a:hover:before {
    left: 100%;
}

/* Shimmer effect for level up badge */
@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.animate-shimmer {
    animation: shimmer 2s ease-in-out infinite;
}

/* Delay animation classes */
.delay-500 {
    animation-delay: 0.5s;
}

.delay-1000 {
    animation-delay: 1s;
}

/* Custom rotation classes for wing feathers */
.rotate-30 {
    transform: rotate(30deg);
}

.-rotate-30 {
    transform: rotate(-30deg);
}

/* Responsive Design */
@media (max-width: 640px) {
    .sparkle {
        font-size: 1.2rem;
    }

    .celebration-burst {
        font-size: 1rem;
    }
}
</style>

<script>
// Check if this is from a regular quiz (gamification enabled) or diagnostic (gamification disabled)
const isRegularQuiz = {{ isset($from_regular_quiz) && $from_regular_quiz ? 'true' : 'false' }};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize celebration effects
    initializeConfetti();
    createFloatingParticles();

    // Only initialize gamification features for regular quizzes
    if (isRegularQuiz) {
        // Initialize points counter with audio
        initializePointsCounter();
    // Only initialize gamification features for regular quizzes
    if (isRegularQuiz) {
        // Initialize points counter with audio
        initializePointsCounter();

        // Check for level up after a delay to let the points counter finish
        setTimeout(() => {
            checkForLevelUp();
        }, 3000);
    } else {
        // For diagnostic quizzes, just show the points without animation
        const pointsCounter = document.getElementById('points-counter');
        const targetPoints = parseInt(pointsCounter.getAttribute('data-target')) || 0;
        pointsCounter.textContent = targetPoints;
    }
        // Check for level up after a delay to let the points counter finish
        setTimeout(() => {
            checkForLevelUp();
        }, 3000);
    } else {
        // For diagnostic quizzes, just show the points without animation
        const pointsCounter = document.getElementById('points-counter');
        const targetPoints = parseInt(pointsCounter.getAttribute('data-target')) || 0;
        pointsCounter.textContent = targetPoints;
    }
});

// Points Counter with Audio
function initializePointsCounter() {
    const pointsCounter = document.getElementById('points-counter');
    const targetPoints = parseInt(pointsCounter.getAttribute('data-target')) || 0;

    // Initialize score counter audio
    const scoreCounterAudio = new Audio('{{ asset("audio/scorecounter.mp3") }}');
    scoreCounterAudio.volume = 0.8;

    // Start counter animation after initial delay
    setTimeout(() => {
        startPointsCounter(pointsCounter, targetPoints, scoreCounterAudio);
    }, 1000);
}

function startPointsCounter(element, target, audio) {
    let current = 0;
    const increment = Math.ceil(target / 50); // Adjust speed by changing divisor
    const duration = 2000; // 2 seconds total duration
    const stepTime = duration / (target / increment);

    // Play the score counter audio
    audio.play().catch(error => {
        console.log('Score counter audio playback failed:', error);
    });

    const timer = setInterval(() => {
        current += increment;

        if (current >= target) {
            current = target;
            element.textContent = current;
            clearInterval(timer);

            // Play assessment completion audio when counter finishes
            setTimeout(() => {
                const assessmentCompleteAudio = new Audio('{{ asset("audio/assessmentcomplete.mp3") }}');
                assessmentCompleteAudio.volume = 0.7;
                assessmentCompleteAudio.play().catch(error => {
                    console.log('Assessment complete audio playback failed:', error);
                });
            }, 300);

        } else {
            element.textContent = current;
        }

        // Add pulse effect during counting
        element.style.transform = 'scale(1.1)';
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 100);

    }, stepTime);
}

// Level Up Detection and Display Functions
function checkForLevelUp() {
    // Only check for level up if this is a regular quiz (not diagnostic)
    if (!isRegularQuiz) {
        console.log('Skipping level up check - this is a diagnostic quiz');
        return;
    }

    // Since the API endpoint might not exist, use the user progress data we have
    // and simulate level up detection based on points earned
    simulateLevelUpForTesting();
}

// Fallback function for testing purposes
function simulateLevelUpForTesting() {
    const pointsEarned = parseInt(document.getElementById('points-counter').getAttribute('data-target')) || 0;

    // Get current user progress data
    const userData = @json([
        'current_level' => $userProgress->current_level ?? 1,
        'total_points' => $userProgress->total_points ?? 0
    ]);

    const currentTotalPoints = userData.total_points;
    const currentLevel = userData.current_level;

    // Calculate the previous total points (before this assessment)
    const previousTotalPoints = currentTotalPoints - pointsEarned;

    // Calculate the previous level using 60 points per level formula
    const pointsPerLevel = 60;
    const previousLevel = Math.floor(previousTotalPoints / pointsPerLevel) + 1;

    console.log('=== LEVEL UP CALCULATION ===');
    console.log('Points earned this assessment:', pointsEarned);
    console.log('Current total points:', currentTotalPoints);
    console.log('Previous total points:', previousTotalPoints);
    console.log('Previous level (calculated):', previousLevel);
    console.log('Current level (from DB):', currentLevel);

    // Only trigger level up if there was actually a level change
    if (currentLevel > previousLevel) {
        console.log('Level up detected! Calling handleLevelUp...');
        handleLevelUp(previousLevel, currentLevel, pointsEarned);
    } else {
        console.log('No level up occurred');
    }
}

function handleLevelUp(previousLevel, newLevel, pointsGained) {
    // Define rank boundaries - split Math Explorer into two to show modal at level 6
    // Define rank boundaries based on gamification system
    const rankRanges = [
        { min: 1, max: 10, name: 'Math Explorer' },      // Levels 1-10
        { min: 11, max: 20, name: 'Math Adventurer' },   // Levels 11-20
        { min: 21, max: 30, name: 'Math Seeker' },       // Levels 21-30
        { min: 31, max: 40, name: 'Math Strategist' },   // Levels 31-40
        { min: 41, max: 50, name: 'Math Innovator' },    // Levels 41-50
        { min: 51, max: 60, name: 'Math Prodigy' },      // Levels 51-60
        { min: 61, max: 70, name: 'Math Virtuoso' },     // Levels 61-70
        { min: 71, max: 80, name: 'Math Sage' },         // Levels 71-80
        { min: 81, max: 90, name: 'Math Champion' },     // Levels 81-90
        { min: 91, max: 100, name: 'Math Grandmaster' }  // Levels 91-100
    ];

    // Find which ranks the previous and new levels belong to
    const previousRank = rankRanges.find(rank => previousLevel >= rank.min && previousLevel <= rank.max);
    const newRank = rankRanges.find(rank => newLevel >= rank.min && newLevel <= rank.max);

    console.log('Previous level:', previousLevel, 'Previous rank:', previousRank);
    console.log('New level:', newLevel, 'New rank:', newRank);

    // Special case: If crossing from level 1-5 to 6+ (entering Math Explorer proper)
    const crossedIntoMathExplorer = previousLevel <= 5 && newLevel >= 6 && newLevel <= 10;

    // Simplified rank crossing logic
    let isRankUp = false;

    // Case 1: Entering a rank for the first time (from no rank to any rank)
    if (!previousRank && newRank) {
        console.log('Entering rank for first time:', newRank.name);
        isRankUp = true;
    }
    // Case 2: Special - Crossing into Math Explorer (levels 6-10)
    else if (crossedIntoMathExplorer) {
        console.log('Crossed into Math Explorer! (levels 1-5 → 6-10)');
        isRankUp = true;
    }
    // Case 3: Crossing between different defined ranks
    else if (previousRank && newRank && previousRank.name !== newRank.name) {
        console.log('Crossing between ranks:', previousRank.name, '->', newRank.name);
        isRankUp = true;
    }
    // Case 4: All other cases show simple level up
    else {
        console.log('No rank crossing detected, showing simple level up');
        showSimpleLevelUp(newLevel);
        return;
    }

    console.log('Final decision - Is rank up?', isRankUp);

    if (isRankUp) {
        // Show the fancy modal for rank ups (crossing rank boundaries)
        showRankUpModal(newLevel, pointsGained);
    } else {
        // Show the simple level up notification for regular levels within same rank
        showSimpleLevelUp(newLevel);
    }
}

function showSimpleLevelUp(newLevel) {
    const modal = document.getElementById('simple-level-up');
    const levelNumber = document.getElementById('simple-level-number');
    const levelText = document.getElementById('simple-new-level-text');

    levelNumber.textContent = newLevel;
    levelText.textContent = `Level ${newLevel}`;

    // Play level up audio
    try {
        const levelUpAudio = new Audio('{{ asset("audio/levelup.mp3") }}');
        levelUpAudio.volume = 0.8;
        levelUpAudio.play().catch(error => {
            console.log('Level up audio playback failed:', error);
        });
    } catch (error) {
        console.error('Error initializing level up audio:', error);
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Auto close after 8 seconds (longer display)
    setTimeout(() => {
        closeSimpleLevelUp();
    }, 8000);
}

function showRankUpModal(newLevel, pointsGained) {
    // Play level up audio for rank ups too
    try {
        const levelUpAudio = new Audio('{{ asset("audio/levelup.mp3") }}');
        levelUpAudio.volume = 0.8;
        levelUpAudio.play().catch(error => {
            console.log('Level up audio playback failed:', error);
        });
    } catch (error) {
        console.error('Error initializing level up audio:', error);
    }

    // Use the existing level up modal system from the component
    if (typeof showLevelUpModalFromLevel === 'function') {
        // Determine which rank the new level belongs to (based on gamification.md)
        let rankMessage = "Congratulations on reaching a new rank!";

        if (newLevel >= 6 && newLevel <= 10) {
            rankMessage = "You've become a Math Explorer! Your journey begins now!";
        } else if (newLevel >= 11 && newLevel <= 20) {
        } else if (newLevel >= 11 && newLevel <= 20) {
            rankMessage = "You're now a Math Adventurer! Ready for bigger challenges!";
        } else if (newLevel >= 21 && newLevel <= 30) {
        } else if (newLevel >= 21 && newLevel <= 30) {
            rankMessage = "You've achieved Math Seeker status! Keep exploring!";
        } else if (newLevel >= 31 && newLevel <= 40) {
        } else if (newLevel >= 31 && newLevel <= 40) {
            rankMessage = "You're a Math Strategist now! Think critically!";
        } else if (newLevel >= 41 && newLevel <= 50) {
        } else if (newLevel >= 41 && newLevel <= 50) {
            rankMessage = "Math Innovator unlocked! Create your own solutions!";
        } else if (newLevel >= 51 && newLevel <= 60) {
        } else if (newLevel >= 51 && newLevel <= 60) {
            rankMessage = "You're a Math Prodigy! Exceptional skills!";
        } else if (newLevel >= 61 && newLevel <= 70) {
        } else if (newLevel >= 61 && newLevel <= 70) {
            rankMessage = "Math Virtuoso achieved! Masterful performance!";
        } else if (newLevel >= 71 && newLevel <= 80) {
        } else if (newLevel >= 71 && newLevel <= 80) {
            rankMessage = "You're a Math Sage! Wisdom beyond measure!";
        } else if (newLevel >= 81 && newLevel <= 90) {
        } else if (newLevel >= 81 && newLevel <= 90) {
            rankMessage = "Math Champion status! Elite level reached!";
        } else if (newLevel >= 91 && newLevel <= 100) {
        } else if (newLevel >= 91 && newLevel <= 100) {
            rankMessage = "Math Grandmaster! Ultimate achievement!";
        }

        showLevelUpModalFromLevel(newLevel, pointsGained * 2, pointsGained, rankMessage);
    }
}

function closeSimpleLevelUp() {
    const modal = document.getElementById('simple-level-up');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Confetti Animation
function initializeConfetti() {
    const canvas = document.getElementById('confetti-canvas');
    const ctx = canvas.getContext('2d');

    // Set canvas size
    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Confetti particles
    const confettiParticles = [];
    const colors = [
        '#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24',
        '#f0932b', '#eb4d4b', '#6c5ce7', '#a29bfe',
        '#fd79a8', '#fdcb6e', '#e17055', '#00b894',
        '#00cec9', '#0984e3', '#6c5ce7', '#a29bfe'
    ];

    // Confetti class
    class Confetti {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = -10;
            this.width = Math.random() * 8 + 4;
            this.height = Math.random() * 8 + 4;
            this.color = colors[Math.floor(Math.random() * colors.length)];
            this.speedX = Math.random() * 6 - 3;
            this.speedY = Math.random() * 3 + 2;
            this.rotation = Math.random() * 360;
            this.rotationSpeed = Math.random() * 10 - 5;
            this.opacity = 1;
            this.gravity = 0.1;
        }

        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            this.speedY += this.gravity;
            this.rotation += this.rotationSpeed;

            // Fade out as particles fall
            if (this.y > canvas.height * 0.8) {
                this.opacity -= 0.02;
            }
        }

        draw() {
            ctx.save();
            ctx.globalAlpha = this.opacity;
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation * Math.PI / 180);
            ctx.fillStyle = this.color;
            ctx.fillRect(-this.width / 2, -this.height / 2, this.width, this.height);
            ctx.restore();
        }
    }

    // Create initial burst of confetti
    function createConfettiBurst() {
        for (let i = 0; i < 150; i++) {
            confettiParticles.push(new Confetti());
        }
    }

    // Continuous confetti creation
    function addConfetti() {
        if (confettiParticles.length < 200) {
            for (let i = 0; i < 5; i++) {
                confettiParticles.push(new Confetti());
            }
        }
    }

    // Animation loop
    function animateConfetti() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let i = confettiParticles.length - 1; i >= 0; i--) {
            const particle = confettiParticles[i];
            particle.update();
            particle.draw();

            // Remove particles that are off screen or faded
            if (particle.y > canvas.height + 10 || particle.opacity <= 0) {
                confettiParticles.splice(i, 1);
            }
        }

        requestAnimationFrame(animateConfetti);
    }

    // Start confetti
    createConfettiBurst();
    animateConfetti();

    // Add continuous confetti
    setInterval(addConfetti, 300);

    // Stop adding new confetti after 10 seconds
    setTimeout(() => {
        clearInterval(addConfetti);
    }, 10000);
}

// Floating Celebration Particles
function createFloatingParticles() {
    const particleContainer = document.getElementById('celebration-particles');
    const particles = ['🎉', '🎊', '🌟', '⭐', '💫', '✨', '🎈', '🏆', '🎯', '🔥'];

    function createParticle() {
        const particle = document.createElement('div');
        particle.className = 'floating-particle';
        particle.textContent = particles[Math.floor(Math.random() * particles.length)];

        // Random position
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
        particle.style.animationDelay = Math.random() * 2 + 's';

        particleContainer.appendChild(particle);

        // Remove particle after animation
        setTimeout(() => {
            if (particle.parentNode) {
                particle.parentNode.removeChild(particle);
            }
        }, 6000);
    }

    // Create initial particles
    for (let i = 0; i < 20; i++) {
        setTimeout(createParticle, i * 200);
    }

    // Continue creating particles
    const particleInterval = setInterval(createParticle, 500);

    // Stop after 15 seconds
    setTimeout(() => {
        clearInterval(particleInterval);
    }, 15000);
}

// Add celebration effects to score cards
function addCardCelebrations() {
    const pointsCard = document.querySelector('.points-card');
    const scoreCard = document.querySelector('.score-card');

    [pointsCard, scoreCard].forEach(card => {
        if (card) {
            card.addEventListener('mouseenter', function() {
                // Create mini burst effect
                createMiniBurst(this);
            });
        }
    });
}

// Mini burst effect for cards
function createMiniBurst(element) {
    const rect = element.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;

    for (let i = 0; i < 10; i++) {
        const burst = document.createElement('div');
        burst.textContent = ['✨', '⭐', '💫'][Math.floor(Math.random() * 3)];
        burst.style.position = 'fixed';
        burst.style.left = centerX + 'px';
        burst.style.top = centerY + 'px';
        burst.style.fontSize = '1rem';
        burst.style.pointerEvents = 'none';
        burst.style.zIndex = '1000';
        burst.style.animation = `burstOut 1s ease-out forwards`;

        // Random direction
        const angle = (i / 10) * Math.PI * 2;
        const distance = 50;
        const endX = centerX + Math.cos(angle) * distance;
        const endY = centerY + Math.sin(angle) * distance;

        burst.style.setProperty('--end-x', endX + 'px');
        burst.style.setProperty('--end-y', endY + 'px');

        document.body.appendChild(burst);

        setTimeout(() => {
            if (burst.parentNode) {
                burst.parentNode.removeChild(burst);
            }
        }, 1000);
    }
}

// Add burst animation
const style = document.createElement('style');
style.textContent = `
    @keyframes burstOut {
        0% {
            opacity: 1;
            transform: translate(-50%, -50%) scale(0);
        }
        50% {
            opacity: 1;
            transform: translate(calc(var(--end-x) - 50%), calc(var(--end-y) - 50%)) scale(1);
        }
        100% {
            opacity: 0;
            transform: translate(calc(var(--end-x) - 50%), calc(var(--end-y) - 50%)) scale(0);
        }
    }
`;
document.head.appendChild(style);

// Initialize card celebrations after page load
setTimeout(addCardCelebrations, 1000);

// Essential level up functions (NOT testing functions)
function showSimpleLevelUp(newLevel) {
    const modal = document.getElementById('simple-level-up');
    const levelNumber = document.getElementById('simple-level-number');
    const levelText = document.getElementById('simple-new-level-text');

    levelNumber.textContent = newLevel;
    levelText.textContent = `Level ${newLevel}`;

    // Play level up audio
    try {
        const levelUpAudio = new Audio('{{ asset("audio/levelup.mp3") }}');
        levelUpAudio.volume = 0.8;
        levelUpAudio.play().catch(error => {
            console.log('Level up audio playback failed:', error);
        });
    } catch (error) {
        console.error('Error initializing level up audio:', error);
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Auto close after 8 seconds (longer display)
    setTimeout(() => {
        closeSimpleLevelUp();
    }, 8000);
}

function showRankUpModal(newLevel, pointsGained) {
    // Play level up audio for rank ups too
    try {
        const levelUpAudio = new Audio('{{ asset("audio/levelup.mp3") }}');
        levelUpAudio.volume = 0.8;
        levelUpAudio.play().catch(error => {
            console.log('Level up audio playback failed:', error);
        });
    } catch (error) {
        console.error('Error initializing level up audio:', error);
    }

    // Use the existing level up modal system from the component
    if (typeof showLevelUpModalFromLevel === 'function') {
        // Determine which rank the new level belongs to (based on gamification.md)
        let rankMessage = "Congratulations on reaching a new rank!";

        if (newLevel >= 6 && newLevel <= 10) {
            rankMessage = "You've become a Math Explorer! Your journey begins now!";
        } else if (newLevel >= 11 && newLevel <= 20) {
        } else if (newLevel >= 11 && newLevel <= 20) {
            rankMessage = "You're now a Math Adventurer! Ready for bigger challenges!";
        } else if (newLevel >= 21 && newLevel <= 30) {
        } else if (newLevel >= 21 && newLevel <= 30) {
            rankMessage = "You've achieved Math Seeker status! Keep exploring!";
        } else if (newLevel >= 31 && newLevel <= 40) {
        } else if (newLevel >= 31 && newLevel <= 40) {
            rankMessage = "You're a Math Strategist now! Think critically!";
        } else if (newLevel >= 41 && newLevel <= 50) {
        } else if (newLevel >= 41 && newLevel <= 50) {
            rankMessage = "Math Innovator unlocked! Create your own solutions!";
        } else if (newLevel >= 51 && newLevel <= 60) {
        } else if (newLevel >= 51 && newLevel <= 60) {
            rankMessage = "You're a Math Prodigy! Exceptional skills!";
        } else if (newLevel >= 61 && newLevel <= 70) {
        } else if (newLevel >= 61 && newLevel <= 70) {
            rankMessage = "Math Virtuoso achieved! Masterful performance!";
        } else if (newLevel >= 71 && newLevel <= 80) {
        } else if (newLevel >= 71 && newLevel <= 80) {
            rankMessage = "You're a Math Sage! Wisdom beyond measure!";
        } else if (newLevel >= 81 && newLevel <= 90) {
        } else if (newLevel >= 81 && newLevel <= 90) {
            rankMessage = "Math Champion status! Elite level reached!";
        } else if (newLevel >= 91 && newLevel <= 100) {
        } else if (newLevel >= 91 && newLevel <= 100) {
            rankMessage = "Math Grandmaster! Ultimate achievement!";
        }

        showLevelUpModalFromLevel(newLevel, pointsGained * 2, pointsGained, rankMessage);
    }
}

function closeSimpleLevelUp() {
    const modal = document.getElementById('simple-level-up');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

</script>
@endsection
