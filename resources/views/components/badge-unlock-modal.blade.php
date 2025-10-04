<!-- Badge Unlock Modal Component -->
<div id="badge-unlock-modal" class="fixed inset-0 bg-black bg-opacity-75 z-[9999] hidden items-center justify-center p-4 font-baloo">
    <!-- Modal Container with Transparent Box -->
    <div class="relative max-w-md sm:max-w-lg md:max-w-xl w-full bg-transparent rounded-3xl p-6 sm:p-8 md:p-10" id="modal-content">
        <!-- Close Button -->
        <button onclick="closeBadgeModal()" class="absolute -top-4 -right-4 z-10 bg-white rounded-full p-2 shadow-lg hover:scale-110 transition-transform duration-200">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Header with Animation -->
        <div class="text-center mb-8 relative">
            <!-- Floating Stars Background -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="star star-1">⭐</div>
                <div class="star star-2">✨</div>
                <div class="star star-3">🌟</div>
                <div class="star star-4">💫</div>
                <div class="star star-5">⭐</div>
                <div class="star star-6">✨</div>
            </div>

            <!-- Title -->
            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4 drop-shadow-2xl relative z-10 animate-bounce-slow">
                Achievement Unlocked!
            </h2>
            <p class="text-xl sm:text-2xl md:text-3xl text-white relative z-10 drop-shadow-lg">
                Amazing work! You've earned new badges!
            </p>
        </div>

        <!-- Badges Container - 40% bigger on desktop -->
        <div id="badges-container" class="flex flex-wrap justify-center gap-4 mb-8">
            <!-- Badges will be inserted here dynamically -->
        </div>

        <!-- Continue Button with 3D Effect -->
        <div class="text-center">
            <a href="{{ route('achievements.index') }}" class="inline-block bg-white text-purple-600 font-bold text-xl sm:text-2xl px-16 sm:px-16 py-4 sm:py-5 rounded-full hover:translate-y-1 active:translate-y-2 transition-all duration-150 border-t-4 border-l-4 border-r-4 border-purple-300 shadow-3d-button hover:shadow-3d-button-hover active:shadow-none no-underline">
                View My Badges
            </a>
        </div>
    </div>
</div>

<!-- Badge Card Template (hidden) -->
<template id="badge-card-template">
    <div class="badge-card rounded-2xl text-center transform hover:scale-105 transition-all duration-300 shadow-2xl relative overflow-hidden aspect-[4/5] w-40 sm:w-44 md:w-48">
        <!-- Card will have background color applied dynamically -->

        <!-- Shining Line Effect -->
        <div class="shine-effect"></div>

        <!-- Badge Icon -->
        <div class="relative mb-2 z-10 pt-6">
            <div class="badge-icon-container relative w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 mx-auto rounded-full flex items-center justify-center border-4 shadow-2xl">
                <img class="badge-icon w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 object-contain" src="" alt="Badge">
            </div>
        </div>

        <!-- Badge Info -->
        <div class="absolute bottom-2 left-2 right-2 z-10">
            <h3 class="badge-name text-sm sm:text-base font-bold text-white mb-1 drop-shadow-lg truncate"></h3>
            <p class="badge-description text-xs text-white opacity-90 mb-2 truncate"></p>

            <!-- Points Display -->
            <div class="inline-block bg-white bg-opacity-20 backdrop-blur-sm text-white font-bold px-2 py-1 rounded-full text-xs border-2 border-white border-opacity-30">
                <span class="badge-points"></span> Points
            </div>
        </div>
    </div>
</template>

<style>
    /* Star animations */
    .star {
        position: absolute;
        font-size: 2rem;
        animation: twinkle 3s ease-in-out infinite;
        opacity: 0;
    }

    .star-1 { top: 10%; left: 15%; animation-delay: 0s; }
    .star-2 { top: 20%; right: 10%; animation-delay: 0.5s; }
    .star-3 { bottom: 30%; left: 10%; animation-delay: 1s; }
    .star-4 { bottom: 15%; right: 15%; animation-delay: 1.5s; }
    .star-5 { top: 50%; left: 5%; animation-delay: 2s; }
    .star-6 { top: 40%; right: 5%; animation-delay: 2.5s; }

    @keyframes twinkle {
        0%, 100% { opacity: 0; transform: scale(0.5) rotate(0deg); }
        50% { opacity: 1; transform: scale(1.5) rotate(180deg); }
    }

    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }

    .animate-bounce-slow {
        animation: bounce-slow 2s ease-in-out infinite;
    }

    /* Shining line effect */
    .shine-effect {
        position: absolute;
        top: -50%;
        left: -100%;
        width: 50%;
        height: 200%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.3),
            rgba(255, 255, 255, 0.5),
            rgba(255, 255, 255, 0.3),
            transparent
        );
        transform: skewX(-25deg);
        animation: shine 3s infinite;
        z-index: 1;
    }

    @keyframes shine {
        0% {
            left: -100%;
        }
        50%, 100% {
            left: 150%;
        }
    }

    /* Modal entrance animation */
    #badge-unlock-modal.show #modal-content {
        animation: modalEnter 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes modalEnter {
        0% {
            transform: scale(0.3) rotate(-10deg);
            opacity: 0;
        }
        100% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
    }

    /* Confetti animation */
    .confetti-piece {
        position: absolute;
        width: 12px;
        height: 12px;
        animation: confetti-fall 3s linear forwards;
        pointer-events: none;
    }

    @keyframes confetti-fall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }

    /* 3D Button Effect */
    .shadow-3d-button {
        box-shadow: 0 6px 0 0 #7c3aed, 0 8px 12px rgba(0, 0, 0, 0.3);
    }

    .shadow-3d-button-hover {
        box-shadow: 0 4px 0 0 #7c3aed, 0 6px 10px rgba(0, 0, 0, 0.25);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .star { font-size: 1.5rem; }
        #badge-unlock-modal { padding: 1rem; }
    }
</style>

<script>
    // Badge color mapping based on badge key (matching AchievementController.php)
    const badgeColorMap = {
        'first_steps': {
            background: '#646565',  // Gray (Common)
            borderColor: '#525555'
        },
        'quick_learner': {
            background: '#1E8646',  // Green (Uncommon)
            borderColor: '#166D38'
        },
        'on_fire': {
            background: '#913311',  // Brown/Orange (Rare)
            borderColor: '#591E09'
        },
        'math_whiz': {
            background: '#2C1B68',  // Purple (Epic)
            borderColor: '#21125C'
        },
        'grade_champion': {
            background: '#D17A09',  // Gold (Legendary)
            borderColor: '#804B03'
        },
        'math_explorer': {
            background: '#165A9A',  // Blue
            borderColor: '#104373'
        },
        'sapphire': {
            background: '#165A9A',  // Blue
            borderColor: '#104373'
        },
        'ruby': {
            background: '#913311',  // Brown/Orange
            borderColor: '#591E09'
        },
        'crown': {
            background: '#D17A09',  // Gold
            borderColor: '#804B03'
        }
    };

    // Global functions for badge modal
    function showBadgeModal(badges) {
        const modal = document.getElementById('badge-unlock-modal');
        const container = document.getElementById('badges-container');
        const template = document.getElementById('badge-card-template');

        if (!modal || !container || !template) return;

        // Clear existing badges
        container.innerHTML = '';

        // Add each badge
        badges.forEach((badge, index) => {
            const clone = template.content.cloneNode(true);
            const card = clone.querySelector('.badge-card');
            const iconContainer = clone.querySelector('.badge-icon-container');

            // Get badge colors
            const colors = badgeColorMap[badge.badge_key] || badgeColorMap['first_steps'];

            // Apply solid background color matching AchievementController
            card.style.backgroundColor = colors.background;

            // Apply border color to icon container
            iconContainer.style.borderColor = colors.borderColor;

            // Set badge data
            clone.querySelector('.badge-icon').src = getBadgeImagePath(badge.badge_key);
            clone.querySelector('.badge-icon').alt = badge.badge_name;
            clone.querySelector('.badge-name').textContent = badge.badge_name;
            clone.querySelector('.badge-description').textContent = badge.badge_description;
            clone.querySelector('.badge-points').textContent = badge.points_required;

            // Add entrance animation delay
            card.style.animationDelay = `${index * 0.2}s`;
            card.classList.add('badge-entrance');

            container.appendChild(clone);
        });

        // Show modal with animation
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'show');

        // Create confetti effect
        createConfetti();

        // Play celebration sound if audio is enabled
        playBadgeSound();
    }

    function closeBadgeModal() {
        const modal = document.getElementById('badge-unlock-modal');
        if (modal) {
            modal.classList.remove('flex', 'show');
            modal.classList.add('hidden');
        }
    }

    function getBadgeImagePath(badgeKey) {
        // Map badge keys to image paths
        const badgeImages = {
            'first_steps': '{{ asset("images/achievements/a/firststep.png") }}',
            'quick_learner': '{{ asset("images/achievements/a/quicklearner.png") }}',
            'on_fire': '{{ asset("images/achievements/a/onfire.png") }}',
            'math_whiz': '{{ asset("images/achievements/a/mathwhiz.png") }}',
            'grade_champion': '{{ asset("images/achievements/a/gradechampion.png") }}',
            'math_explorer': '{{ asset("images/achievements/a/firststep.png") }}', // Placeholder
            'sapphire': '{{ asset("images/achievements/a/firststep.png") }}', // Placeholder
            'ruby': '{{ asset("images/achievements/a/firststep.png") }}', // Placeholder
            'crown': '{{ asset("images/achievements/a/firststep.png") }}'  // Placeholder
        };

        return badgeImages[badgeKey] || '{{ asset("images/achievements/a/firststep.png") }}';
    }

    function createConfetti() {
        const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#FF69B4'];
        const confettiCount = 60;

        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti-piece';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';

            document.getElementById('badge-unlock-modal').appendChild(confetti);

            // Remove confetti after animation
            setTimeout(() => confetti.remove(), 5000);
        }
    }

    function playBadgeSound() {
        try {
            const audio = new Audio('{{ asset("audio/achievementunlocked.mp3") }}');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Audio play failed:', e));
        } catch (error) {
            console.log('Could not play badge sound:', error);
        }
    }

    // Add entrance animation CSS
    const style = document.createElement('style');
    style.textContent = `
        .badge-entrance {
            animation: badgeEnter 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            opacity: 0;
        }

        @keyframes badgeEnter {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
</script>
