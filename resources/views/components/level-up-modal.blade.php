<style>
    /* =========================================================
   Level Up Modal - Button Text Shadows (10 Tiers)
   Only for button text (no titles, no XP)
   ========================================================= */

    /* Bronze (Lvl 1–10) */
    .text-shadow-bronze-btn {
        text-shadow: 0 3px 0 #4D2A12;
    }

    /* Silver (Lvl 11–20) */
    .text-shadow-silver-btn {
        text-shadow: 0 3px 0 #2E3642;
    }

    /* Gold (Lvl 21–30) */
    .text-shadow-gold-btn {
        text-shadow: 0 3px 0 #5C3A0F;
    }

    /* Topaz (Lvl 31–40) */
    .text-shadow-topaz-btn {
        text-shadow: 0 3px 0 #5A2503;
    }

    /* Emerald (Lvl 41–50) */
    .text-shadow-emerald-btn {
        text-shadow: 0 3px 0 #03361B;
    }

    /* Ruby (Lvl 51–60) */
    .text-shadow-ruby-btn {
        text-shadow: 0 3px 0 #4A0A0A;
    }

    /* Amethyst (Lvl 61–70) */
    .text-shadow-amethyst-btn {
        text-shadow: 0 3px 0 #2A0A3D;
    }

    /* Tanzite (Lvl 71–80) */
    .text-shadow-tanzite-btn {
        text-shadow: 0 3px 0 #121637;
    }

    /* Sapphire (Lvl 81–90) */
    .text-shadow-sapphire-btn {
        text-shadow: 0 3px 0 #071B38;
    }

    /* Prismatic (Lvl 91–100) */
    .text-shadow-prismatic-btn {
        text-shadow: 0 3px 0 #43185C;
    }
</style>
<!-- Level-Up Modal Component - Clean 10-Tier System -->
<div id="levelUpModal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4 z-[70]">
        <div class="modal-content relative w-full max-w-sm mx-auto">
            <!-- Dynamic Modal Card - Uses tier classes from config -->
            <div id="levelUpCard"
                class="relative rounded-3xl border-4 overflow-hidden p-8 text-center bg-level-bronze-card border-level-bronze-stroke">
                <!-- Floating particles animation -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-4 left-4 w-2 h-2 bg-white/30 rounded-full animate-ping"></div>
                    <div class="absolute top-8 right-6 w-1 h-1 bg-white/40 rounded-full animate-pulse"></div>
                    <div class="absolute bottom-6 left-8 w-1.5 h-1.5 bg-white/20 rounded-full animate-bounce"></div>
                    <div class="absolute bottom-10 right-4 w-1 h-1 bg-white/30 rounded-full animate-ping delay-1000">
                    </div>
                    <div class="absolute top-1/2 left-2 w-1 h-1 bg-white/25 rounded-full animate-pulse delay-2000">
                    </div>
                    <div class="absolute top-1/4 right-2 w-1.5 h-1.5 bg-white/35 rounded-full animate-bounce delay-500">
                    </div>
                </div>

                <!-- Content -->
                <div class="relative z-10">
                    <!-- Rank Badge -->
                    <div class="mb-6 flex justify-center">
                        <div class="relative">
                            <img id="modalRankImage" src="" alt="Rank" class="w-42 h-42 object-contain z-10">
                            <!-- Shine effect -->
                            <div
                                class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/30 to-transparent transform -translate-x-full animate-shimmer">
                            </div>
                        </div>
                    </div>

                    <!-- Level Badge with SVG -->
                    <div class="mb-4 flex justify-center">
                        <div class="relative">
                            <!-- SVG Octagon Background -->
                            <img id="modalLevelBadgeSVG" src="" alt="Level Badge"
                                class="w-[19.688rem] h-[6.25rem] object-contain">
                            <!-- Text Overlay -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <div class="text-white font-bold text-center">
                                    <div id="modalLevelNumber"
                                        class="text-4xl font-baloo font-extrabold text-level-bronze-title text-shadow-bronze-title drop-shadow-level-bronze-text">
                                        LEVEL 7</div>
                                    <div id="modalUnlocked"
                                        class="text-xl font-baloo font-bold text-level-bronze-labels">UNLOCKED</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- XP and Points -->
                    <div class="flex justify-center gap-8 mb-4">
                        <div class="text-center">
                            <div id="modalXPGained"
                                class="text-3xl font-extrabold font-baloo text-level-bronze-xp text-shadow-bronze-xp drop-shadow-level-bronze-text">
                                150 XP</div>
                            <div id="modalXPLabel" class="text-lg text-level-bronze-labels">Exp Earned</div>
                        </div>
                        <div class="text-center">
                            <div id="modalPointsGained"
                                class="text-3xl font-extrabold font-baloo text-level-bronze-xp text-shadow-bronze-xp drop-shadow-level-bronze-text">
                                200 PTS</div>
                            <div id="modalPointsLabel" class="text-lg text-level-bronze-labels">Points Earned
                            </div>
                        </div>
                    </div>

                    <!-- Motivational Message -->
                    <div class="mb-6">
                        <p id="modalMessage" class="text-base text-level-bronze-message">A great journey begins with
                            small
                            steps!</p>
                    </div>

                    <!-- Continue Button -->
                    <button id="modalContinueBtn" onclick="closeLevelUpModal()"
                        class="w-full py-3 px-6 rounded-xl font-bold bg-level-bronze-button border-2 border-level-bronze-button-stroke transition-all duration-200 hover:transform hover:-translate-y-1 text-level-bronze-title text-shadow-bronze-button drop-shadow-level-bronze-text">
                        Continue
                    </button>
                </div>
            </div>

            <!-- Go to Dashboard Link -->
            {{-- <div class="text-center mt-4">
                <a href="#" class="text-gray-600 text-sm hover:text-gray-800 transition-colors">
                    Go to Dashboard
                </a>
            </div> --}}
        </div>
    </div>
</div>

<script>
    const TIER_CONFIG = {
        bronze: {
            card: 'bg-level-bronze-card border-level-bronze-stroke',
            button: 'bg-level-bronze-button border-2 border-level-bronze-button-stroke drop-shadow-level-bronze-button text-level-bronze-title text-shadow-bronze-button drop-shadow-level-bronze-text text-shadow-bronze-btn',
            title: 'text-level-bronze-title text-shadow-bronze-title drop-shadow-level-bronze-text',
            labels: 'text-level-bronze-labels',
            xp: 'text-level-bronze-xp text-shadow-bronze-xp drop-shadow-level-bronze-text',
            message: 'text-level-bronze-message',
            svg: 'bronze-octagon.svg',
            rankImage: 'rank-1.png',
            rankTitle: 'Math Explorer',
            defaultMessage: 'A great journey begins with small steps!'
        },
        silver: {
            card: 'bg-level-silver-card border-level-silver-stroke',
            button: 'bg-level-silver-button border-2 border-level-silver-button-stroke drop-shadow-level-silver-button text-level-silver-title text-shadow-silver-button drop-shadow-level-silver-text text-shadow-silver-btn',
            title: 'text-level-silver-title text-shadow-silver-title drop-shadow-level-silver-text',
            labels: 'text-level-silver-labels',
            xp: 'text-level-silver-xp text-shadow-silver-xp drop-shadow-level-silver-text',
            message: 'text-level-silver-message',
            svg: 'silver-octagon.svg',
            rankImage: 'rank-2.png',
            rankTitle: 'Math Adventurer',
            defaultMessage: 'Your skills are developing nicely!'
        },
        gold: {
            card: 'bg-level-gold-card border-level-gold-stroke',
            button: 'bg-level-gold-button border-2 border-level-gold-button-stroke drop-shadow-level-gold-button text-level-gold-title text-shadow-gold-button drop-shadow-level-gold-text text-shadow-gold-btn',
            title: 'text-level-gold-title text-shadow-gold-title drop-shadow-level-gold-text',
            labels: 'text-level-gold-labels',
            xp: 'text-level-gold-xp text-shadow-gold-xp drop-shadow-level-gold-text',
            message: 'text-level-gold-message',
            svg: 'gold-octagon.svg',
            rankImage: 'rank-3.png',
            rankTitle: 'Math Seeker',
            defaultMessage: 'You are showing great potential!'
        },
        topaz: {
            card: 'bg-level-topaz-card border-level-topaz-stroke',
            button: 'bg-level-topaz-button border-2 border-level-topaz-button-stroke drop-shadow-level-topaz-button text-level-topaz-title text-shadow-topaz-button drop-shadow-level-topaz-text text-shadow-topaz-btn',
            title: 'text-level-topaz-title text-shadow-topaz-title drop-shadow-level-topaz-text',
            labels: 'text-level-topaz-labels',
            xp: 'text-level-topaz-xp text-shadow-topaz-xp drop-shadow-level-topaz-text',
            message: 'text-level-topaz-message',
            svg: 'topaz-octagon.svg',
            rankImage: 'rank-4.png',
            rankTitle: 'Math Strategist',
            defaultMessage: 'You are mastering advanced concepts!'
        },
        emerald: {
            card: 'bg-level-emerald-card border-level-emerald-stroke',
            button: 'bg-level-emerald-button border-2 border-level-emerald-button-stroke drop-shadow-level-emerald-button text-level-emerald-title text-shadow-emerald-button drop-shadow-level-emerald-text text-shadow-emerald-btn',
            title: 'text-level-emerald-title text-shadow-emerald-title drop-shadow-level-emerald-text',
            labels: 'text-level-emerald-labels',
            xp: 'text-level-emerald-xp text-shadow-emerald-xp drop-shadow-level-emerald-text',
            message: 'text-level-emerald-message',
            svg: 'emerald-octagon.svg',
            rankImage: 'rank-5.png',
            rankTitle: 'Math Innovator',
            defaultMessage: 'You are reaching new mathematical heights!'
        },
        ruby: {
            card: 'bg-level-ruby-card border-level-ruby-stroke',
            button: 'bg-level-ruby-button border-2 border-level-ruby-button-stroke drop-shadow-level-ruby-button text-level-ruby-title text-shadow-ruby-button drop-shadow-level-ruby-text text-shadow-ruby-btn',
            title: 'text-level-ruby-title text-shadow-ruby-title drop-shadow-level-ruby-text',
            labels: 'text-level-ruby-labels',
            xp: 'text-level-ruby-xp text-shadow-ruby-xp drop-shadow-level-ruby-text',
            message: 'text-level-ruby-message',
            svg: 'ruby-octagon.svg',
            rankImage: 'rank-6.png',
            rankTitle: 'Math Prodigy',
            defaultMessage: 'Your mathematical prowess is extraordinary!'
        },
        amethyst: {
            card: 'bg-level-amethyst-card border-level-amethyst-stroke',
            button: 'bg-level-amethyst-button border-2 border-level-amethyst-button-stroke drop-shadow-level-amethyst-button text-level-amethyst-title text-shadow-amethyst-button drop-shadow-level-amethyst-text text-shadow-amethyst-btn',
            title: 'text-level-amethyst-title text-shadow-amethyst-title drop-shadow-level-amethyst-text',
            labels: 'text-level-amethyst-labels',
            xp: 'text-level-amethyst-xp text-shadow-amethyst-xp drop-shadow-level-amethyst-text',
            message: 'text-level-amethyst-message',
            svg: 'amethyst-octagon.svg',
            rankImage: 'rank-7.png',
            rankTitle: 'Math Virtuoso',
            defaultMessage: 'You have achieved mathematical excellence!'
        },
        tanzite: {
            card: 'bg-level-tanzite-card border-level-tanzite-stroke',
            button: 'bg-level-tanzite-button border-2 border-level-tanzite-button-stroke drop-shadow-level-tanzite-button text-level-tanzite-title text-shadow-tanzite-button drop-shadow-level-tanzite-text text-shadow-tanzite-btn',
            title: 'text-level-tanzite-title text-shadow-tanzite-title drop-shadow-level-tanzite-text',
            labels: 'text-level-tanzite-labels',
            xp: 'text-level-tanzite-xp text-shadow-tanzite-xp drop-shadow-level-tanzite-text',
            message: 'text-level-tanzite-message',
            svg: 'tanzite-octagon.svg',
            rankImage: 'rank-8.png',
            rankTitle: 'Math Sage',
            defaultMessage: 'You are becoming a mathematical virtuoso!'
        },
        sapphire: {
            card: 'bg-level-sapphire-card border-level-sapphire-stroke',
            button: 'bg-level-sapphire-button border-2 border-level-sapphire-button-stroke drop-shadow-level-sapphire-button text-level-sapphire-title text-shadow-sapphire-button drop-shadow-level-sapphire-text text-shadow-sapphire-btn',
            title: 'text-level-sapphire-title text-shadow-sapphire-title drop-shadow-level-sapphire-text',
            labels: 'text-level-sapphire-labels',
            xp: 'text-level-sapphire-xp text-shadow-sapphire-xp drop-shadow-level-sapphire-text',
            message: 'text-level-sapphire-message',
            svg: 'blue-sapphire-octagon.svg',
            rankImage: 'rank-9.png',
            rankTitle: 'Math Champion',
            defaultMessage: 'You have reached the highest levels of mastery!'
        },
        prismatic: {
            card: 'bg-level-prismatic-card border-level-prismatic-stroke',
            button: 'bg-level-prismatic-button border-2 border-level-prismatic-button-stroke drop-shadow-level-prismatic-button text-level-prismatic-title text-shadow-prismatic-button drop-shadow-level-prismatic-text text-shadow-prismatic-btn ',
            title: 'text-level-prismatic-title text-shadow-prismatic-title drop-shadow-level-prismatic-text',
            labels: 'text-level-prismatic-labels',
            xp: 'text-level-prismatic-xp text-shadow-prismatic-xp drop-shadow-level-prismatic-text',
            message: 'text-level-prismatic-message',
            svg: 'prismatic-octagon.svg',
            rankImage: 'rank-10.png',
            rankTitle: 'Math Grandmaster',
            defaultMessage: 'You have achieved the ultimate mathematical pinnacle!'
        }
    };

    // Simplified modal system using config classes
    function showLevelUpModal(data) {
        const modal = document.getElementById('levelUpModal');
        const card = document.getElementById('levelUpCard');
        const rankImage = document.getElementById('modalRankImage');
        const levelBadgeSVG = document.getElementById('modalLevelBadgeSVG');
        const levelNumber = document.getElementById('modalLevelNumber');
        const unlocked = document.getElementById('modalUnlocked');
        const xpGained = document.getElementById('modalXPGained');
        const pointsGained = document.getElementById('modalPointsGained');
        const xpLabel = document.getElementById('modalXPLabel');
        const pointsLabel = document.getElementById('modalPointsLabel');
        const message = document.getElementById('modalMessage');
        const continueBtn = document.getElementById('modalContinueBtn');

        if (!data) return;

        const tierName = data.tier || 'bronze';
        const tierConfig = TIER_CONFIG[tierName] || TIER_CONFIG.bronze;

        // Apply tier styling using config classes
        applyTierClasses(card, `relative rounded-3xl border-4 overflow-hidden p-8 text-center ${tierConfig.card}`);
        applyTierClasses(continueBtn, `w-full py-3 px-6 rounded-xl text-xl font-baloo font-bold transition-all duration-200 hover:transform hover:-translate-y-1 ${tierConfig.button}`);
        applyTierClasses(levelNumber, `text-4xl font-baloo font-extrabold ${tierConfig.title}`);
        applyTierClasses(unlocked, `text-xl font-baloo font-bold ${tierConfig.labels}`);
        applyTierClasses(xpGained, `text-3xl font-extrabold font-baloo ${tierConfig.xp}`);
        applyTierClasses(pointsGained, `text-3xl font-extrabold font-baloo ${tierConfig.xp}`);
        applyTierClasses(xpLabel, `text-lg ${tierConfig.labels}`);
        applyTierClasses(pointsLabel, `text-lg ${tierConfig.labels}`);
        applyTierClasses(message, `text-base ${tierConfig.message}`);

        // Set content
        rankImage.src = `/images/rank_insignia/${data.rank_image || tierConfig.rankImage}`;
        rankImage.alt = data.rank_title || tierConfig.rankTitle;
        levelBadgeSVG.src = `/images/rank_insignia/${tierConfig.svg}`;
        levelNumber.textContent = `LEVEL ${data.new_level || 7}`;
        xpGained.textContent = `${data.xp_gained || 150} XP`;
        pointsGained.textContent = `${data.points_gained || 200} PTS`;
        message.textContent = data.message || tierConfig.defaultMessage;

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function applyTierClasses(element, classString) {
        if (!element || !classString) return;

        // Reset element classes to base classes
        const baseClasses = element.getAttribute('data-base-classes') || '';
        element.className = baseClasses;

        // Add tier-specific classes
        element.classList.add(...classString.split(' ').filter(cls => cls.trim()));
    }

    function getTierNameFromLevel(level) {
        if (level >= 1 && level <= 10) return 'bronze';
        if (level >= 11 && level <= 20) return 'silver';
        if (level >= 21 && level <= 30) return 'gold';
        if (level >= 31 && level <= 40) return 'topaz';
        if (level >= 41 && level <= 50) return 'emerald';
        if (level >= 51 && level <= 60) return 'ruby';
        if (level >= 61 && level <= 70) return 'amethyst';
        if (level >= 71 && level <= 80) return 'tanzite';
        if (level >= 81 && level <= 90) return 'sapphire';
        if (level >= 91 && level <= 100) return 'prismatic';
        return 'bronze';
    }

    function showLevelUpModalFromLevel(level, xpGained = 100, pointsGained = 150, message = null) {
        const tierName = getTierNameFromLevel(level);
        const tierConfig = TIER_CONFIG[tierName];

        const modalData = {
            tier: tierName,
            new_level: level,
            xp_gained: xpGained,
            points_gained: pointsGained,
            rank_image: tierConfig.rankImage,
            rank_title: tierConfig.rankTitle,
            message: message || tierConfig.defaultMessage
        };

        showLevelUpModal(modalData);
    }

    function closeLevelUpModal() {
        const modal = document.getElementById('levelUpModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Event listeners
    document.getElementById('levelUpModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeLevelUpModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('levelUpModal').classList.contains('hidden')) {
            closeLevelUpModal();
        }
    });

    // Store base classes for elements that will be dynamically styled
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('levelUpCard').setAttribute('data-base-classes', 'relative rounded-3xl border-4 overflow-hidden p-8 text-center');
        document.getElementById('modalLevelNumber').setAttribute('data-base-classes', '');
        document.getElementById('modalUnlocked').setAttribute('data-base-classes', '');
        document.getElementById('modalXPGained').setAttribute('data-base-classes', '');
        document.getElementById('modalPointsGained').setAttribute('data-base-classes', '');
        document.getElementById('modalXPLabel').setAttribute('data-base-classes', '');
        document.getElementById('modalPointsLabel').setAttribute('data-base-classes', '');
        document.getElementById('modalMessage').setAttribute('data-base-classes', '');
        document.getElementById('modalContinueBtn').setAttribute('data-base-classes', '');
    });

    // Export functions
    window.showLevelUpModal = showLevelUpModal;
    window.showLevelUpModalFromLevel = showLevelUpModalFromLevel;
    window.closeLevelUpModal = closeLevelUpModal;
</script>

<style>
    .modal-overlay {
        animation: fadeIn 0.3s ease-out;
    }

    .modal-content {
        animation: slideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(-50px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Custom animations for particle effects */
    .delay-500 {
        animation-delay: 0.5s;
    }

    .delay-1000 {
        animation-delay: 1s;
    }

    .delay-2000 {
        animation-delay: 2s;
    }
</style>