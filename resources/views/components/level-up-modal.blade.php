<!-- Level-Up Modal Component -->
<div id="levelUpModal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4 z-[70]">
        <div class="modal-content relative w-full max-w-sm mx-auto">
            <!-- Dynamic Modal Card - Changes based on tier -->
            <div id="levelUpCard" class="relative rounded-3xl border-4 overflow-hidden p-8 text-center">
                <!-- Floating particles animation -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-4 left-4 w-2 h-2 bg-white/30 rounded-full animate-ping"></div>
                    <div class="absolute top-8 right-6 w-1 h-1 bg-white/40 rounded-full animate-pulse"></div>
                    <div class="absolute bottom-6 left-8 w-1.5 h-1.5 bg-white/20 rounded-full animate-bounce"></div>
                    <div class="absolute bottom-10 right-4 w-1 h-1 bg-white/30 rounded-full animate-ping"
                        style="animation-delay: 1s;"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10">
                    <!-- Rank Badge -->
                    <div class="mb-6 flex justify-center">
                        <div class="relative">
                            <div id="modalRankBadge"
                                class="w-20 h-20 rounded-2xl border-4 flex items-center justify-center relative overflow-hidden">
                                <img id="modalRankImage" src="" alt="Rank" class="w-12 h-12 object-contain z-10">
                                <!-- Shine effect -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/30 to-transparent transform -translate-x-full animate-shimmer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Level Badge -->
                    <div class="mb-4">
                        <div id="modalLevelBadge" class="inline-block px-6 py-2 rounded-full border-4">
                            <div class="text-white font-bold">
                                <div id="modalLevelNumber" class="text-lg">LEVEL 7</div>
                                <div class="text-xs opacity-90">UNLOCKED</div>
                            </div>
                        </div>
                    </div>

                    <!-- XP and Points -->
                    <div class="flex justify-center gap-8 mb-4">
                        <div class="text-center">
                            <div id="modalXPGained" class="text-2xl font-bold text-white">150 XP</div>
                            <div class="text-xs opacity-80 text-white">Exp Earned</div>
                        </div>
                        <div class="text-center">
                            <div id="modalPointsGained" class="text-2xl font-bold text-white">200 PTS</div>
                            <div class="text-xs opacity-80 text-white">Points Earned</div>
                        </div>
                    </div>

                    <!-- Motivational Message -->
                    <div class="mb-6">
                        <p id="modalMessage" class="text-sm text-white/80">A great journey begins with small steps!</p>
                    </div>

                    <!-- Continue Button -->
                    <button id="modalContinueBtn" onclick="closeLevelUpModal()"
                        class="w-full py-3 px-6 rounded-xl font-bold text-white border-2 transition-all duration-200 hover:transform hover:-translate-y-1">
                        Continue
                    </button>
                </div>
            </div>

            <!-- Go to Dashboard Link -->
            <div class="text-center mt-4">
                <a href="#" class="text-gray-600 text-sm hover:text-gray-800 transition-colors">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

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
</style>

<script>
    // Level-Up Modal Functions
    function showLevelUpModal(data) {
        const modal = document.getElementById('levelUpModal');
        const card = document.getElementById('levelUpCard');
        const rankBadge = document.getElementById('modalRankBadge');
        const levelBadge = document.getElementById('modalLevelBadge');
        const rankImage = document.getElementById('modalRankImage');
        const levelNumber = document.getElementById('modalLevelNumber');
        const xpGained = document.getElementById('modalXPGained');
        const pointsGained = document.getElementById('modalPointsGained');
        const message = document.getElementById('modalMessage');
        const continueBtn = document.getElementById('modalContinueBtn');

        if (!data) return;

        // Apply tier-based styling to modal
        applyModalTierStyling(data.tier || 'bronze', card, rankBadge, levelBadge, continueBtn);

        // Set content
        rankImage.src = `/images/rank_insignia/${data.rank_image || 'rank-1.png'}`;
        rankImage.alt = data.rank_title || 'Math Explorer';
        levelNumber.textContent = `LEVEL ${data.new_level || 7}`;
        xpGained.textContent = `${data.xp_gained || 150} XP`;
        pointsGained.textContent = `${data.points_gained || 200} PTS`;
        message.textContent = data.message || 'A great journey begins with small steps!';

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function applyModalTierStyling(tierName, card, rankBadge, levelBadge, continueBtn) {
        // Remove existing classes
        card.className = 'relative rounded-3xl border-4 overflow-hidden p-8 text-center';
        rankBadge.className = 'w-20 h-20 rounded-2xl border-4 flex items-center justify-center relative overflow-hidden';
        levelBadge.className = 'inline-block px-6 py-2 rounded-full border-4';
        continueBtn.className = 'w-full py-3 px-6 rounded-xl font-bold text-white border-2 transition-all duration-200 hover:transform hover:-translate-y-1';

        // Apply tier-specific styling
        const tierStyles = {
            bronze: {
                card: 'bg-gradient-to-b from-[#C77C3E] to-[#5A2E12] border-[#3B1F0C]',
                rankBadge: 'bg-gradient-to-b from-[#E69B56] to-[#5A2E12] border-[#3B1F0C] drop-shadow-[0_4px_0_#4D2A12]',
                levelBadge: 'bg-gradient-to-b from-[#E69B56] to-[#5A2E12] border-[#4D2A12]',
                continueBtn: 'bg-gradient-to-b from-[#E69B56] to-[#5A2E12] border-[#4D2A12] drop-shadow-[0_4px_0_#4D2A12]'
            },
            silver: {
                card: 'bg-gradient-to-b from-[#D9E3F2] to-[#3C4757] border-[#2A313D]',
                rankBadge: 'bg-gradient-to-b from-[#F2F6FA] to-[#3C4757] border-[#2A313D] drop-shadow-[0_4px_0_#2E3642]',
                levelBadge: 'bg-gradient-to-b from-[#F2F6FA] to-[#3C4757] border-[#2E3642]',
                continueBtn: 'bg-gradient-to-b from-[#F2F6FA] to-[#3C4757] border-[#2E3642] drop-shadow-[0_4px_0_#2E3642]'
            },
            gold: {
                card: 'bg-gradient-to-b from-[#FFD55C] to-[#7A4B0E] border-[#4D3009]',
                rankBadge: 'bg-gradient-to-b from-[#FFE58A] to-[#7A4B0E] border-[#4D3009] drop-shadow-[0_4px_0_#5C3A0F]',
                levelBadge: 'bg-gradient-to-b from-[#FFE58A] to-[#7A4B0E] border-[#5C3A0F]',
                continueBtn: 'bg-gradient-to-b from-[#FFE58A] to-[#7A4B0E] border-[#5C3A0F] drop-shadow-[0_4px_0_#5C3A0F]'
            },
            topaz: {
                card: 'bg-gradient-to-b from-[#F6A43B] to-[#A64906] border-[#7C3304]',
                rankBadge: 'bg-gradient-to-b from-[#FFD59E] to-[#B65A0B] border-[#9C5B0C] drop-shadow-[0_4px_0_#663308]',
                levelBadge: 'bg-gradient-to-b from-[#FFB74A] to-[#A64906] border-[#5A2503]',
                continueBtn: 'bg-gradient-to-b from-[#FFB74A] to-[#A64906] border-[#5A2503] drop-shadow-[0_4px_0_#5A2503]'
            }
        };

        const styles = tierStyles[tierName] || tierStyles.bronze;

        card.classList.add(...styles.card.split(' '));
        rankBadge.classList.add(...styles.rankBadge.split(' '));
        levelBadge.classList.add(...styles.levelBadge.split(' '));
        continueBtn.classList.add(...styles.continueBtn.split(' '));
    }

    function closeLevelUpModal() {
        const modal = document.getElementById('levelUpModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('levelUpModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeLevelUpModal();
        }
    });

    // Close with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('levelUpModal').classList.contains('hidden')) {
            closeLevelUpModal();
        }
    });
</script>