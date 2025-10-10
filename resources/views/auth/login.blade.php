<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Three.js (required for Vanta) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Vanta.js FOG Effect -->
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.fog.min.js"></script>

    <style>
        /* Math symbols floating animation */
        .math-symbol {
            position: absolute;
            color: rgba(255, 255, 255, 0.15);
            font-size: 2.5rem;
            font-weight: bold;
            pointer-events: none;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
            animation: float-symbol 20s infinite ease-in-out;
        }

        @keyframes float-symbol {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.15;
            }

            25% {
                transform: translateY(-30px) rotate(5deg);
                opacity: 0.25;
            }

            50% {
                transform: translateY(-15px) rotate(-5deg);
                opacity: 0.2;
            }

            75% {
                transform: translateY(-40px) rotate(3deg);
                opacity: 0.18;
            }
        }

        .math-symbol:nth-child(1) {
            top: 10%;
            left: 15%;
            animation-delay: 0s;
            font-size: 3rem;
        }

        .math-symbol:nth-child(2) {
            top: 25%;
            left: 75%;
            animation-delay: 2s;
            font-size: 2.5rem;
        }

        .math-symbol:nth-child(3) {
            top: 45%;
            left: 25%;
            animation-delay: 4s;
            font-size: 2.8rem;
        }

        .math-symbol:nth-child(4) {
            top: 65%;
            left: 70%;
            animation-delay: 6s;
            font-size: 2.3rem;
        }

        .math-symbol:nth-child(5) {
            top: 80%;
            left: 30%;
            animation-delay: 8s;
            font-size: 3.2rem;
        }

        .math-symbol:nth-child(6) {
            top: 35%;
            left: 85%;
            animation-delay: 10s;
            font-size: 2.6rem;
        }

        .math-symbol:nth-child(7) {
            top: 55%;
            left: 10%;
            animation-delay: 12s;
            font-size: 2.9rem;
        }

        .math-symbol:nth-child(8) {
            top: 15%;
            left: 50%;
            animation-delay: 14s;
            font-size: 2.4rem;
        }

        .math-symbol:nth-child(9) {
            top: 75%;
            left: 60%;
            animation-delay: 16s;
            font-size: 3.1rem;
        }

        .math-symbol:nth-child(10) {
            top: 90%;
            left: 80%;
            animation-delay: 18s;
            font-size: 2.7rem;
        }

        #vanta-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .content-overlay {
            position: relative;
            z-index: 1;
        }

        /* Glassmorphism for mobile/tablet */
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Mobile/Tablet full-screen background */
        .mobile-vanta-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 0;
        }

        .mobile-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }
    </style>
</head>

@include('loaders.loader')

@php
    $captchaHtml = captcha_img('default');
@endphp

<script>
    function refreshCaptcha() {
        const captchaImages = document.querySelectorAll('img[src*="captcha"]');
        captchaImages.forEach(img => {
            img.src = img.src.split('?')[0] + '?' + Date.now();
        });
    }
</script>

<body class="min-h-screen font-sans overflow-x-hidden">
    <!-- Mobile/Tablet layout with full-screen background -->
    <div class="md:hidden">
        <!-- Vanta.js Background for Mobile/Tablet -->
        <div class="mobile-vanta-container" id="mobile-vanta-bg">
            <!-- Floating Math Symbols for Mobile -->
            <div class="math-symbol">+</div>
            <div class="math-symbol">−</div>
            <div class="math-symbol">×</div>
            <div class="math-symbol">÷</div>
            <div class="math-symbol">=</div>
            <div class="math-symbol">π</div>
            <div class="math-symbol">√</div>
            <div class="math-symbol">∑</div>
            <div class="math-symbol">∞</div>
            <div class="math-symbol">∫</div>
        </div>

        <!-- Mobile Content Overlay -->
        <div class="mobile-content flex flex-col justify-center items-center px-5 py-8">
            <!-- Logo and Brand -->
            <div class="text-center mb-8">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <img src="{{ asset('images/Icons/Icon4.png') }}" alt="AralSipnayan Logo"
                        class="w-14 h-14 rounded-xl">
                    <h1 class="text-3xl font-bold">
                        <span class="text-white">Aral</span><span class="text-primary-yellow">Sipnayan</span>
                    </h1>
                </div>
                <p class="text-white text-opacity-90 text-sm">Math learning made fun!</p>
            </div>

            <!-- Glassmorphism Login Card -->
            <div class="glass-card rounded-2xl p-8 w-full max-w-sm shadow-2xl">
                <h2 class="text-white text-xl font-semibold text-center mb-6">Student Login</h2>
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf
                    <div class="mb-5">
                        <label for="username" class="block text-white text-sm font-medium mb-2">Username</label>
                        <input type="text"
                            class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40"
                            name="username" id="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-white text-sm font-medium mb-2">Password</label>
                        <input type="password"
                            class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40"
                            name="password" id="password" placeholder="••••••••" required>
                    </div>
                    <div class="mb-6">
                        <label for="captcha" class="block text-white text-base font-medium mb-2">Security Code</label>
                        <div class="flex flex-col gap-3">
                            <input type="text"
                                class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40"
                                name="captcha" placeholder="Enter security code">
                            <div class="glass-input rounded-xl p-3 w-full flex items-center justify-center">
                                <div
                                    class="w-full max-w-[140px] min-h-[40px] flex items-center justify-center [&>img]:!w-full [&>img]:!h-auto [&>img]:!min-h-[40px] sm:max-w-[120px] sm:[&>img]:!min-h-[35px]">
                                    {!! $captchaHtml !!}
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="refreshCaptcha()"
                            class="text-white text-sm mt-2 underline hover:no-underline">
                            Refresh Code
                        </button>
                    </div>
                    <button type="submit" id="loginBtn"
                        class="w-full bg-primary-yellow text-primary-blue font-semibold py-3.5 rounded-xl transition-all duration-200 hover:bg-yellow-400 hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-yellow-300 shadow-lg">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Desktop layout -->
    <div class="hidden md:flex min-h-screen">
        <!-- Left Section with Vanta.js Background -->
        <div class="flex-1 relative overflow-hidden">
            <!-- Vanta.js container -->
            <div id="vanta-bg"></div>

            <!-- Floating Math Symbols -->
            <div class="math-symbol">+</div>
            <div class="math-symbol">−</div>
            <div class="math-symbol">×</div>
            <div class="math-symbol">÷</div>
            <div class="math-symbol">=</div>
            <div class="math-symbol">π</div>
            <div class="math-symbol">√</div>
            <div class="math-symbol">∑</div>
            <div class="math-symbol">∞</div>
            <div class="math-symbol">∫</div>

            <!-- Content Overlay -->
            <div class="content-overlay flex items-center justify-center w-full h-full">
                <div class="text-center text-white">
                    <div class="flex items-center justify-center gap-4 mb-2">
                        <img src="{{ asset('images/Icons/Icon4.png') }}" alt="AralSipnayan Logo"
                            class="w-20 h-20 rounded-xl">
                        <h1 class="text-4xl xl:text-5xl font-bold">
                            <span class="text-white">Aral</span><span class="text-primary-yellow">Sipnayan</span>
                        </h1>
                    </div>
                    <p class="text-white text-opacity-80 text-lg xl:text-xl">Math learning made fun!</p>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex-1 bg-gray-100 flex items-center justify-center">
            <div class="bg-white rounded-2xl p-12 xl:p-16 w-full max-w-md xl:max-w-lg shadow-xl">
                <h2 class="text-gray-800 text-2xl xl:text-3xl font-semibold text-center mb-8">Student Login</h2>
                <form method="POST" action="{{ route('login.submit') }}" id="desktopLoginForm">
                    @csrf
                    <div class="mb-6">
                        <label for="desktop-username" class="block text-gray-700 text-base font-medium mb-2">Student
                            Number</label>
                        <input type="text"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue"
                            name="username" id="desktop-username" required>
                    </div>
                    <div class="mb-8">
                        <label for="desktop-password"
                            class="block text-gray-700 text-base font-medium mb-2">Password</label>
                        <input type="password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue"
                            name="password" id="desktop-password" required>
                    </div>
                    <div class="mb-8">
                        <label for="captcha" class="block text-gray-700 text-base font-medium mb-2">Security
                            Code</label>
                        <div class="flex gap-3 items-center">
                            <input type="text"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue"
                                name="captcha" placeholder="Enter security code">
                            <div class="border border-gray-300 rounded-xl p-2 bg-gray-50">
                                {!! $captchaHtml !!}
                            </div>
                        </div>
                        <button type="button" onclick="refreshCaptcha()"
                            class="text-primary-blue text-sm mt-2 underline hover:no-underline">
                            Refresh Code
                        </button>
                    </div>
                    <button type="submit" id="desktopLoginBtn"
                        class="w-full bg-primary-yellow text-primary-blue font-semibold py-3.5 rounded-xl transition-all duration-200 hover:bg-yellow-400 hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-yellow-300">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Vanta.js FOG effect for all screen sizes
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                // Mobile/Tablet Vanta.js initialization
                VANTA.FOG({
                    el: "#mobile-vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    highlightColor: 0x3b82f6,
                    midtoneColor: 0x1e3a8a,
                    lowlightColor: 0x1e293b,
                    baseColor: 0x1e3a8a,
                    blurFactor: 0.6,
                    speed: 1.5,
                    zoom: 1.2
                });
            } else {
                // Desktop Vanta.js initialization
                VANTA.FOG({
                    el: "#vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    highlightColor: 0x3b82f6,
                    midtoneColor: 0x1e3a8a,
                    lowlightColor: 0x1e293b,
                    baseColor: 0x1e3a8a,
                    blurFactor: 0.6,
                    speed: 1.5,
                    zoom: 1.2
                });
            }

            const forms = document.querySelectorAll("form");
            const loaderWrapper = document.querySelector("#loader-wrapper");

            forms.forEach(form => {
                form.addEventListener("submit", function (e) {
                    // Show loader immediately when form is submitted
                    if (loaderWrapper) {
                        loaderWrapper.style.display = "flex";
                    }

                    // Disable all submit buttons to prevent double submission
                    const submitBtns = document.querySelectorAll('button[type="submit"]');
                    submitBtns.forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = 'Logging in...';
                    });

                    // Hide the forms to show only the loader
                    const mobileLayout = document.querySelector('.md\\:hidden');
                    const desktopLayout = document.querySelector('.hidden.md\\:flex');
                    if (mobileLayout) mobileLayout.style.display = 'none';
                    if (desktopLayout) desktopLayout.style.display = 'none';
                });
            });

            // Hide loader if there are validation errors (page reloads with errors)
            const errorModal = document.querySelector("#errorModal");

            if (errorModal && loaderWrapper) {
                loaderWrapper.style.display = "none";

                // Show the appropriate form based on screen size
                const mobileLayout = document.querySelector('.md\\:hidden');
                const desktopLayout = document.querySelector('.hidden.md\\:flex');

                // Re-enable submit buttons
                const submitBtns = document.querySelectorAll('button[type="submit"]');
                submitBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = 'Login';
                });
            }
        });
    </script>
</body>

</html>

@if ($errors->any())
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-lg w-96 p-6 relative">
            <!-- Close button - moved to upper right -->
            <button onclick="document.getElementById('errorModal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>

            <!-- Logo -->
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/Icons/Icon1.png') }}" alt="AralSipnayan Logo" class="w-12 h-12 rounded-xl mb-2">
                <h2 class="text-lg font-bold text-primary-blue mb-3">Login Failed</h2>

                <!-- Error Messages -->
                <ul class="text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif