<!-- Music Settings Modal -->
<div id="music-settings-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50 p-4">
    <div class="bg-gradient-to-b from-[#1e3a8a] to-[#1e40af] rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <!-- Modal Header -->
        <h2 class="text-white text-4xl font-bold text-center mb-8">OPTIONS</h2>

        <!-- Background Music Toggle -->
        <div class="flex items-center justify-between mb-6 bg-[#1e40af] rounded-2xl p-4">
            <span class="text-white text-xl font-bold">Background Music</span>
            <button id="bg-music-toggle" class="relative inline-flex items-center cursor-pointer">
                <div class="w-20 h-10 bg-gray-700 rounded-full relative flex items-center px-2">
                    <span class="toggle-text text-xs font-bold text-white absolute right-3">OFF</span>
                    <div class="dot w-8 h-8 bg-white rounded-full transition-all duration-300 shadow-lg"></div>
                </div>
            </button>
        </div>

        <!-- Sound Effects Toggle -->
        <div class="flex items-center justify-between mb-8 bg-[#1e40af] rounded-2xl p-4">
            <span class="text-white text-xl font-bold">Sound Effects</span>
            <button id="sound-effects-toggle" class="relative inline-flex items-center cursor-pointer">
                <div class="w-20 h-10 bg-gray-700 rounded-full relative flex items-center px-2">
                    <span class="toggle-text text-xs font-bold text-white absolute right-3">OFF</span>
                    <div class="dot w-8 h-8 bg-white rounded-full transition-all duration-300 shadow-lg"></div>
                </div>
            </button>
        </div>

        <!-- Close Button -->
        <button id="close-settings-modal"
            class="w-full bg-[#1e3a8a] hover:bg-[#1e2f6a] text-white text-2xl font-bold py-4 rounded-3xl transition-all duration-200 transform hover:scale-105">
            Close
        </button>
    </div>
</div>

<style>
    #bg-music-toggle .dot,
    #sound-effects-toggle .dot {
        transform: translateX(0);
    }

    .toggle-active .dot {
        transform: translateX(2.5rem) !important;
    }

    .toggle-active {
        background-color: #10b981 !important;
    }

    .toggle-active .toggle-text {
        right: auto !important;
        left: 0.75rem !important;
    }
</style>
<script>
    // Settings Modal Functions
    function openSettingsModal() {
        const modal = document.getElementById('music-settings-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Add flex when opening

        // Load saved settings
        const bgMusicEnabled = localStorage.getItem('bg_music_enabled') === 'true';
        const soundEffectsEnabled = localStorage.getItem('sound_effects_enabled') === 'true';

        updateToggleState('bg-music-toggle', bgMusicEnabled);
        updateToggleState('sound-effects-toggle', soundEffectsEnabled);
    }

    function closeSettingsModal() {
        const modal = document.getElementById('music-settings-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex'); // Remove flex when closing
    }

    function updateToggleState(toggleId, isActive) {
        const toggle = document.getElementById(toggleId);
        const toggleDiv = toggle.querySelector('div');
        const label = toggleDiv.querySelector('.toggle-text');

        if (isActive) {
            toggleDiv.classList.add('toggle-active');
            toggleDiv.classList.remove('bg-gray-700');
            toggleDiv.classList.add('bg-green-500');
            label.textContent = 'ON';
        } else {
            toggleDiv.classList.remove('toggle-active');
            toggleDiv.classList.add('bg-gray-700');
            toggleDiv.classList.remove('bg-green-500');
            label.textContent = 'OFF';
        }
    }

    // Initialize settings modal handlers
    document.getElementById('settings-btn').addEventListener('click', openSettingsModal);
    document.getElementById('close-settings-modal').addEventListener('click', closeSettingsModal);

    // Background Music Toggle
    document.getElementById('bg-music-toggle').addEventListener('click', function () {
        const isActive = this.querySelector('div').classList.contains('toggle-active');
        const newState = !isActive;

        updateToggleState('bg-music-toggle', newState);
        localStorage.setItem('bg_music_enabled', newState);

        // Add your background music logic here
        if (newState) {
            // Play background music
            console.log('Background music enabled');
        } else {
            // Stop background music
            console.log('Background music disabled');
        }
    });

    // Sound Effects Toggle
    document.getElementById('sound-effects-toggle').addEventListener('click', function () {
        const isActive = this.querySelector('div').classList.contains('toggle-active');
        const newState = !isActive;

        updateToggleState('sound-effects-toggle', newState);
        localStorage.setItem('sound_effects_enabled', newState);
        quizState.audioEnabled = newState;

        console.log('Sound effects:', newState ? 'enabled' : 'disabled');
    });

    // Close modal when clicking outside
    document.getElementById('music-settings-modal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeSettingsModal();
        }
    });
</script>