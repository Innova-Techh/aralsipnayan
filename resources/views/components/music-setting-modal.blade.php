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
    // Background Music Audio Element
    window.bgMusicAudio = null;

    // Settings Modal Functions
    function openSettingsModal() {
        const modal = document.getElementById('music-settings-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Add flex when opening

        // Load saved settings
        const bgMusicEnabled = localStorage.getItem('bg_music_enabled') === 'true';
        const soundEffectsEnabled = localStorage.getItem('sound_effects_enabled') !== 'false'; // Default to true

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

    // Initialize background music
    function initBackgroundMusic() {
        if (!window.bgMusicAudio) {
            window.bgMusicAudio = new Audio('{{ asset("audio/bgmusic.mp3") }}');
            window.bgMusicAudio.loop = false; // Start with loop disabled, enable it after seeking
            window.bgMusicAudio.volume = 0.3; // Set background music volume to 30%
            window.bgMusicAudio.preload = 'auto'; // Preload the full audio for faster playback
            
            // Store the Audio object globally to prevent recreation
            window.bgMusicAudioInitialized = true;
            
            // Save playback position periodically
            window.bgMusicAudio.addEventListener('timeupdate', function() {
                if (window.bgMusicAudio && !window.bgMusicAudio.paused) {
                    localStorage.setItem('bg_music_position', window.bgMusicAudio.currentTime);
                }
            });

            // Save position before page unload
            window.addEventListener('beforeunload', function() {
                if (window.bgMusicAudio && !window.bgMusicAudio.paused) {
                    localStorage.setItem('bg_music_position', window.bgMusicAudio.currentTime);
                }
            });
        }
    }

    // Play background music
    function playBackgroundMusic() {
        if (window.bgMusicAudio) {
            // Restore saved playback position if available
            const savedPosition = localStorage.getItem('bg_music_position');
            
            if (savedPosition && !isNaN(savedPosition)) {
                const position = parseFloat(savedPosition);
                
                // Load the audio file
                window.bgMusicAudio.load();
                
                // Wait for loadeddata event which means we can seek
                window.bgMusicAudio.addEventListener('loadeddata', function onLoadedData() {
                    // Set position while audio is still paused
                    window.bgMusicAudio.currentTime = position;
                    
                    // Now start playing
                    window.bgMusicAudio.play().then(() => {
                        // Enable loop
                        window.bgMusicAudio.loop = true;
                    }).catch(error => {
                        console.error('Error playing background music:', error);
                    });
                    
                    // Remove this one-time listener
                    window.bgMusicAudio.removeEventListener('loadeddata', onLoadedData);
                }, { once: true });
                
            } else {
                // No saved position, just play normally
                window.bgMusicAudio.loop = true;
                window.bgMusicAudio.play().catch(error => {
                    console.error('Error playing background music:', error);
                });
            }
        }
    }

    // Pause background music
    function pauseBackgroundMusic() {
        if (window.bgMusicAudio) {
            // Save current position before pausing
            localStorage.setItem('bg_music_position', window.bgMusicAudio.currentTime);
            window.bgMusicAudio.pause();
        }
    }

    // Initialize settings modal handlers on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize background music
        initBackgroundMusic();

        // Restore background music state
        const bgMusicEnabled = localStorage.getItem('bg_music_enabled') === 'true';
        if (bgMusicEnabled) {
            playBackgroundMusic();
        }

        // Make sure quizState audio is synced with localStorage
        const soundEffectsEnabled = localStorage.getItem('sound_effects_enabled') !== 'false'; // Default to true
        if (window.quizState) {
            window.quizState.audioEnabled = soundEffectsEnabled;
        }

        // Initialize settings modal handlers
        const settingsBtn = document.getElementById('settings-btn');
        const closeModalBtn = document.getElementById('close-settings-modal');
        const bgMusicToggle = document.getElementById('bg-music-toggle');
        const soundEffectsToggle = document.getElementById('sound-effects-toggle');
        const modal = document.getElementById('music-settings-modal');

        if (settingsBtn) {
            settingsBtn.addEventListener('click', openSettingsModal);
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeSettingsModal);
        }

        // Background Music Toggle
        if (bgMusicToggle) {
            bgMusicToggle.addEventListener('click', function () {
                const isActive = this.querySelector('div').classList.contains('toggle-active');
                const newState = !isActive;

                updateToggleState('bg-music-toggle', newState);
                localStorage.setItem('bg_music_enabled', newState);

                // Control background music
                if (newState) {
                    playBackgroundMusic();
                } else {
                    pauseBackgroundMusic();
                    // Clear saved position when user manually turns off music
                    localStorage.removeItem('bg_music_position');
                }
            });
        }

        // Sound Effects Toggle
        if (soundEffectsToggle) {
            soundEffectsToggle.addEventListener('click', function () {
                const isActive = this.querySelector('div').classList.contains('toggle-active');
                const newState = !isActive;

                updateToggleState('sound-effects-toggle', newState);
                localStorage.setItem('sound_effects_enabled', newState);
                
                // Update quiz state if available
                if (window.quizState) {
                    window.quizState.audioEnabled = newState;
                }
            });
        }

        // Close modal when clicking outside
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    closeSettingsModal();
                }
            });
        }
    });
</script>