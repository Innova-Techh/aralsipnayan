<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Three.js (required for Vanta) - from josh-branch -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Vanta.js FOG Effect - from josh-branch -->
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.fog.min.js"></script>

    <style>
        /* Math symbols floating animation - from josh-branch */
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

        /* Glassmorphism for mobile/tablet - from josh-branch */
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

        /* Mobile/Tablet full-screen background - from josh-branch */
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

        /* Ensure mobile vanta covers fully on tablets */
        @media (max-width: 1023px) {
            .mobile-vanta-container {
                position: fixed;
                width: 100vw;
                height: 100vh;
            }
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

<body class="min-h-screen font-baloo overflow-x-hidden">
    <!-- Mobile/Tablet layout with full-screen background (from josh-branch) -->
    <div class="lg:hidden">
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

            <!-- Glassmorphism Login Card (from josh-branch) -->
            <div class="glass-card rounded-2xl p-8 w-full max-w-sm shadow-2xl">
                <h2 class="text-white text-4xl xl:text-3xl font-bold text-center mb-8 font-baloo">Login</h2>
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf
                    <div class="mb-5 font-baloo">
                        <label for="username"
                            class="block text-white text-sm font-medium mb-2 font-baloo">Username</label>
                        <input type="text"
                            class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40 font-baloo"
                            name="username" id="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-6 font-baloo">
                        <label for="password"
                            class="block text-white text-sm font-medium mb-2 font-baloo">Password</label>
                        <div class="relative font-baloo">
                            <input type="password"
                                class="glass-input w-full px-4 py-3 pr-12 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40 font-baloo"
                                name="password" id="password" placeholder="••••••••" required>
                            <button type="button" data-toggle-password="password"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-600 hover:text-gray-800 font-baloo"
                                aria-label="Toggle password visibility">
                                <span class="password-icon-on hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 1l22 22"></path>
                                        <path
                                            d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.74-1.67 1.82-3.17 3.17-4.39">
                                        </path>
                                        <path
                                            d="M9.9 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8a11.07 11.07 0 0 1-2.6 4.02">
                                        </path>
                                        <path d="M9.88 9.88A3 3 0 0 0 12 15a3 3 0 0 0 2.12-.88"></path>
                                    </svg>
                                </span>
                                <span class="password-icon-off">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="captcha" class="block text-white text-base font-medium mb-2 font-baloo">Security
                            Code</label>
                        <div class="flex flex-col gap-3 font-baloo">
                            <div class="glass-input rounded-xl p-3 w-full font-baloo">
                                <div class="flex items-center justify-start gap-8 font-baloo">
                                    <div
                                        class="w-auto max-w-[280px] min-h-[64px] flex items-center justify-center [&>img]:!w-[260px] [&>img]:!h-[60px] [&>img]:!object-contain [&>img]:[image-rendering:auto] sm:max-w-[240px] sm:[&>img]:!w-[220px] sm:[&>img]:!h-[52px]">
                                        {!! $captchaHtml !!}
                                    </div>
                                    <button type="button" onclick="refreshCaptcha()"
                                        class="text-primary-blue text-4xl leading-none hover:opacity-80 whitespace-nowrap bg-white/90 rounded-full p-1"
                                        aria-label="Refresh captcha">&#x21bb;</button>
                                </div>
                            </div>
                            <input type="text"
                                class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40 font-baloo"
                                name="captcha" placeholder="Enter security code">
                        </div>
                    </div>
                    <button type="submit" id="loginBtn" class="w-full bg-primary-blue text-white font-semibold py-3.5 rounded-xl
                        transition-all duration-150
                        shadow-[0_8px_0_0_rgba(29,78,216,1),0_12px_20px_rgba(0,0,0,0.25)]
                        hover:-translate-y-1
                        hover:shadow-[0_10px_0_0_rgba(29,78,216,1),0_16px_25px_rgba(0,0,0,0.3)]
                        active:translate-y-2
                        active:shadow-[0_2px_0_0_rgba(29,78,216,1)]
                        focus:outline-none focus:ring-4 focus:ring-blue-300
                        font-baloo">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Desktop layout (combining both branches) -->
    <div class="hidden lg:flex min-h-screen">
        <!-- Left Section with Vanta.js Background (from josh-branch) -->
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

        <!-- Right Section (from main-branch with all functionality intact) -->
        <div class="flex-1 flex items-center justify-center"
            style="background-image: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.2)), url('{{ asset('images/login/login-form-bg-latest.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: bottom top;">
            {{-- <img src="{{ asset('images/login/login-form-bg.png') }}" alt=""> --}}

            <div class="bg-white/60 rounded-2xl p-12 xl:p-16 w-full max-w-md xl:max-w-lg shadow-xl">

                <h2 class="text-gray-800 text-4xl xl:text-3xl font-bold text-center mb-8 font-baloo">Login</h2>
                <form method="POST" action="{{ route('login.submit') }}" id="desktopLoginForm" class="font-baloo">
                    @csrf
                    <div class="mb-6">
                        <label for="desktop-username"
                            class="block text-gray-700 text-base font-medium mb-2 font-baloo">Username or
                            Email</label>
                        <input type="text"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/45 text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue font-baloo"
                            name="username" id="desktop-username" required>
                    </div>
                    <div class="mb-8">
                        <label for="desktop-password"
                            class="block text-gray-700 text-base font-medium mb-2 font-baloo">Password</label>
                        <div class="relative font-baloo">
                            <input type="password"
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 bg-white/45 text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue font-baloo"
                                name="password" id="desktop-password" required>
                            <button type="button" data-toggle-password="desktop-password"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700 font-baloo"
                                aria-label="Toggle password visibility">
                                <span class="password-icon-on hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 1l22 22"></path>
                                        <path
                                            d="M17.94 17.94A10.94 10.94 0 0 1 12 20C7 20 2.73 16.11 1 12c.74-1.67 1.82-3.17 3.17-4.39">
                                        </path>
                                        <path
                                            d="M9.9 4.24A10.94 10.94 0 0 1 12 4c5 0 9.27 3.89 11 8a11.07 11.07 0 0 1-2.6 4.02">
                                        </path>
                                        <path d="M9.88 9.88A3 3 0 0 0 12 15a3 3 0 0 0 2.12-.88"></path>
                                    </svg>
                                </span>
                                <span class="password-icon-off">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="mb-8">
                        <label for="captcha" class="block text-gray-700 text-base font-medium mb-2">Security
                            Code</label>
                        <div class="border border-gray-300 rounded-xl p-3 bg-gray-50">
                            <div class="flex items-center justify-start gap-8 font-baloo">
                                <div
                                    class="w-auto max-w-[300px] min-h-[64px] flex items-center justify-center [&>img]:!w-[280px] [&>img]:!h-[60px] [&>img]:!object-contain [&>img]:[image-rendering:auto]">
                                    {!! $captchaHtml !!}
                                </div>
                                <button type="button" onclick="refreshCaptcha()"
                                    class="text-primary-blue text-4xl leading-none hover:opacity-80 whitespace-nowrap"
                                    aria-label="Refresh captcha">&#x21bb;</button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="text"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/45 text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue font-baloo"
                                name="captcha" placeholder="Enter security code">
                        </div>
                    </div>
                    <button type="submit" id="loginBtn" class="w-full bg-primary-blue text-white font-semibold py-3.5 rounded-xl
                        transition-all duration-150
                        shadow-[0_8px_0_0_rgba(29,78,216,1),0_12px_20px_rgba(0,0,0,0.25)]
                        hover:-translate-y-1
                        hover:shadow-[0_10px_0_0_rgba(29,78,216,1),0_16px_25px_rgba(0,0,0,0.3)]
                        active:translate-y-2
                        active:shadow-[0_2px_0_0_rgba(29,78,216,1)]
                        focus:outline-none focus:ring-4 focus:ring-blue-300
                        font-baloo">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordToggles = document.querySelectorAll('[data-toggle-password]');
            passwordToggles.forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-toggle-password');
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    const willShow = input.type === 'password';
                    input.type = willShow ? 'text' : 'password';
                    const iconOn = this.querySelector('.password-icon-on');
                    const iconOff = this.querySelector('.password-icon-off');
                    if (iconOn && iconOff) {
                        iconOn.classList.toggle('hidden', !willShow);
                        iconOff.classList.toggle('hidden', willShow);
                    }
                });
            });
            // Initialize Vanta.js FOG effect for all screen sizes
            const isMobile = window.innerWidth < 1024;

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

            // Form submission logic (from main-branch - all functionality preserved)
            const forms = document.querySelectorAll("form");
            const loaderWrapper = document.querySelector("#loader-wrapper");

            forms.forEach(form => {
                form.addEventListener("submit", async function (e) {
                    e.preventDefault(); // Prevent default submission initially

                    // Fetch fresh CSRF token from server
                    try {
                        const response = await fetch('/login', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const html = await response.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const freshToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                            if (freshToken) {
                                // Update meta tag
                                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                                if (csrfMeta) {
                                    csrfMeta.setAttribute('content', freshToken);
                                }

                                // Update form input
                                const csrfInput = this.querySelector('input[name="_token"]');
                                if (csrfInput) {
                                    csrfInput.value = freshToken;
                                }
                            }
                        }
                    } catch (error) {
                        console.log('Could not refresh CSRF token, proceeding with existing token');
                    }

                    // Show loader
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
                    const mobileLayout = document.querySelector('.lg\\:hidden');
                    const desktopLayout = document.querySelector('.hidden.lg\\:flex');
                    if (mobileLayout) mobileLayout.style.display = 'none';
                    if (desktopLayout) desktopLayout.style.display = 'none';

                    // Submit the form
                    this.submit();
                });
            });

            // Hide loader if there are validation errors (page reloads with errors)
            const errorModal = document.querySelector("#errorModal");

            if (errorModal && loaderWrapper) {
                loaderWrapper.style.display = "none";

                // Show the appropriate form based on screen size
                const mobileLayout = document.querySelector('.lg\\:hidden');
                const desktopLayout = document.querySelector('.hidden.lg\\:flex');

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

<!-- Error Modal (from main-branch - all functionality preserved) -->
@if ($errors->any())
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-lg w-96 p-6 relative">
            <!-- Close button - moved to upper right -->
            <button onclick="document.getElementById('errorModal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-4xl">&times;</button>

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