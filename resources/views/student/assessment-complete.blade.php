@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')
<!-- Confetti Canvas -->
<canvas id="confetti-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-50"></canvas>

<!-- Celebration Particles -->
<div id="celebration-particles" class="fixed inset-0 pointer-events-none z-40"></div>

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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize celebration effects
    initializeConfetti();
    createFloatingParticles();

    // Initialize points counter with audio
    initializePointsCounter();
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
</script>
@endsection
