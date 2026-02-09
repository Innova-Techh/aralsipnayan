<footer class="text-white py-12 relative overflow-hidden" style="background-color: #3B73ED;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <!-- Animation Section - Far Left -->
            <div class="flex-shrink-0 mx-auto md:mx-0">
                <dotlottie-player 
                    src="{{ asset('anim/pro-anim.json') }}" 
                    background="transparent" 
                    speed="1" 
                    style="width: 280px; height: 280px;" 
                    loop 
                    autoplay>
                </dotlottie-player>
            </div>

            <!-- Content Grid -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Logo and Description -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo" class="w-12 h-12 rounded-lg">
                        <h3 class="text-3xl font-baloo font-bold">
                            <span class="text-yellow-300">Aral</span><span class="text-white">Sipnayan</span>
                        </h3>
                    </div>
                    <p class="text-white/80 text-sm leading-relaxed">
                        A fun math adventure for elementary students. Learn math through games and exciting challenges!
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                <h4 class="font-baloo font-bold text-xl mb-4 text-yellow-300">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="#about" class="text-white/90 hover:text-yellow-300 hover:font-bold transition-all duration-200 text-base">About</a></li>
                    <li><a href="#features" class="text-white/90 hover:text-yellow-300 hover:font-bold transition-all duration-200 text-base">Features</a></li>
                    {{-- <li><a href="#media" class="text-white/90 hover:text-yellow-300 hover:font-bold transition-all duration-200 text-base">Media</a></li> --}}
                    <li><a href="#ourteam" class="text-white/90 hover:text-yellow-300 hover:font-bold transition-all duration-200 text-base">Our Team</a></li>
                </ul>
                </div>

                <!-- Contact -->
                <div>
                <h4 class="font-baloo font-bold text-xl mb-4 text-yellow-300">Get in Touch</h4>
                <ul class="space-y-3 text-white/90">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 mt-0.5 flex-shrink-0 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-base">info@aralsipnayan.com</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 mt-0.5 flex-shrink-0 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span class="text-base">+63 123 456 7890</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 mt-0.5 flex-shrink-0 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-base">Philippines</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

        <!-- Bottom Bar -->
        <div class="mt-10 pt-6 border-t border-white/20">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-white/80 text-sm text-center md:text-left font-baloo">
                    © {{ date('Y') }} AralSipnayan. All rights reserved.
                </p>
                <div class="flex gap-6 text-sm">
                    <a href="#" onclick="openModal('privacyModal'); return false;" class="text-white/80 hover:text-yellow-300 hover:font-bold transition-all duration-200">Privacy Policy</a>
                    <a href="#" onclick="openModal('termsModal'); return false;" class="text-white/80 hover:text-yellow-300 hover:font-bold transition-all duration-200">Terms of Service</a>
                    <a href="#" onclick="openModal('cookieModal'); return false;" class="text-white/80 hover:text-yellow-300 hover:font-bold transition-all duration-200">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Privacy Policy Modal -->
<div id="privacyModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden animate-modal-enter">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-5 flex items-center justify-between">
            <h3 class="text-2xl font-baloo font-bold text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Privacy Policy
            </h3>
            <button onclick="closeModal('privacyModal')" class="text-white hover:text-yellow-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)] text-gray-700">
            <p class="mb-4 text-lg">We care about keeping your information safe!</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">What Information We Collect</h4>
            <p class="mb-4">We collect your name, grade level, and learning progress to help you learn math better. We keep this information private and secure.</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">How We Use Your Information</h4>
            <p class="mb-4">We use your information to create fun lessons just for you, track your awesome progress, and give you cool badges and rewards!</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Keeping Your Information Safe</h4>
            <p class="mb-4">We use special protection to keep your information safe. We never share your personal information with anyone without permission from your parents or teachers.</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Parent Rights</h4>
            <p class="mb-2">Parents can:</p>
            <ul class="list-disc list-inside mb-4 space-y-1">
                <li>See what information we have about their child</li>
                <li>Ask us to delete information</li>
                <li>Tell us to stop collecting information</li>
            </ul>
            
            <p class="text-sm text-gray-500 mt-4">Last updated: {{ date('F Y') }}</p>
        </div>
    </div>
</div>

<!-- Terms of Service Modal -->
<div id="termsModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden animate-modal-enter">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-5 flex items-center justify-between">
            <h3 class="text-2xl font-baloo font-bold text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Terms of Service
            </h3>
            <button onclick="closeModal('termsModal')" class="text-white hover:text-yellow-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)] text-gray-700">
            <p class="mb-4 text-lg">Welcome to AralSipnayan! Let's learn the rules of our math adventure.</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">How to Use AralSipnayan</h4>
            <p class="mb-4">AralSipnayan is a learning platform for students. You can play math games, complete challenges, and earn badges. Always be respectful and honest when using our platform.</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Your Account</h4>
            <p class="mb-4">Your teacher or parent will create an account for you. Keep your password safe and don't share it with friends. If someone else uses your account, we might think they are you!</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Playing Fair</h4>
            <p class="mb-2">To make learning fun for everyone:</p>
            <ul class="list-disc list-inside mb-4 space-y-1">
                <li>Do your own work and don't copy answers</li>
                <li>Be kind to other students</li>
                <li>Tell a teacher if you see something wrong</li>
                <li>Have fun while learning!</li>
            </ul>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Parent Supervision</h4>
            <p class="mb-4">We recommend that parents and teachers guide students while using AralSipnayan. This helps make learning even more fun and effective!</p>
            
            <p class="text-sm text-gray-500 mt-4">Last updated: {{ date('F Y') }}</p>
        </div>
    </div>
</div>

<!-- Cookie Policy Modal -->
<div id="cookieModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden animate-modal-enter">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-5 flex items-center justify-between">
            <h3 class="text-2xl font-baloo font-bold text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                Cookie Policy
            </h3>
            <button onclick="closeModal('cookieModal')" class="text-white hover:text-yellow-300 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)] text-gray-700">
            <p class="mb-4 text-lg">What are cookies? They're not the yummy kind you eat!</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">What Are Cookies?</h4>
            <p class="mb-4">Cookies are tiny pieces of information that help our website remember you when you come back. They help us make AralSipnayan work better for you!</p>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Types of Cookies We Use</h4>
            
            <div class="mb-3">
                <p class="font-semibold text-blue-500">Essential Cookies</p>
                <p>These help you log in and use AralSipnayan. Without these, the website won't work properly.</p>
            </div>
            
            <div class="mb-3">
                <p class="font-semibold text-blue-500">Learning Progress Cookies</p>
                <p>These remember where you left off in your lessons and save your progress so you don't lose your achievements!</p>
            </div>
            
            <div class="mb-4">
                <p class="font-semibold text-blue-500">Performance Cookies</p>
                <p>These help us understand how students use AralSipnayan so we can make it even better and more fun.</p>
            </div>
            
            <h4 class="font-bold text-blue-600 mb-2 text-lg">Managing Cookies</h4>
            <p class="mb-4">Your parent or teacher can turn off cookies in the browser settings, but some parts of AralSipnayan might not work as well.</p>
            
            <p class="text-sm text-gray-500 mt-4">Last updated: {{ date('F Y') }}</p>
        </div>
    </div>
</div>

<style>
    @keyframes modal-enter {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .animate-modal-enter {
        animation: modal-enter 0.2s ease-out;
    }
</style>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('bg-black/60')) {
            const modals = ['privacyModal', 'termsModal', 'cookieModal'];
            modals.forEach(modalId => {
                if (!document.getElementById(modalId).classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = ['privacyModal', 'termsModal', 'cookieModal'];
            modals.forEach(modalId => {
                if (!document.getElementById(modalId).classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });
</script>

<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
