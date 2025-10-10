<!-- user_layout.blade.php -->
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>


    <!-- Tailwind (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- Material Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,0"
        rel="stylesheet">


    <!-- Add this updated CSS section to your user_layout.blade.php -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');


        body {
            font-family: 'Inter', sans-serif;
        }


        .material-symbols-outlined {
            transition: all 0.25s ease;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }


        /* Desktop Navigation Effects */
        .nav-link:hover .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 28;
            transform: scale(1.2);
            color: #2563eb;
        }


        .nav-link.active .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 28;
            transform: scale(1.2);
            color: #2563eb;
        }


        /* Remove active background (desktop + mobile) */
        .nav-link.active {
            background-color: transparent !important;
        }


        /* Mobile/Tablet Navigation Effects - Apply to all screen sizes below xl */
        @media (max-width: 1279px) {


            /* Default state: outlined & gray */
            .nav-link .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                /* gray-500 */
            }


            /* Active: filled & blue */
            .nav-link.active .material-symbols-outlined {
                font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 28;
                color: #2563eb !important;
                /* blue-600 */
            }








        }




        /* Baloo 2 Font Faces */
        @font-face {
            font-family: 'Baloo 2';
            src: url('{{ asset("fonts/baloo2/Baloo2-Regular.ttf") }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }


        @font-face {
            font-family: 'Baloo 2';
            src: url('{{ asset("fonts/baloo2/Baloo2-Bold.ttf") }}') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }


        @font-face {
            font-family: 'Baloo 2';
            src: url('{{ asset("fonts/baloo2/Baloo2-ExtraBold.ttf") }}') format('truetype');
            font-weight: 800;
            font-style: normal;
            font-display: swap;
        }


        /* Show global background image only on large screens and up */
        @media (min-width: 1024px) {
            body.page-bg {
                background-image: url('{{ asset('images/global/bg.svg') }}');
            }
        }


        /* Custom scrollbar for mobile navigation */
        .mobile-nav-scroll::-webkit-scrollbar {
            display: none;
        }


        .mobile-nav-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>


    @stack('styles')
</head>


<body
    class="min-h-screen {{ request()->routeIs('profile.edit') || request()->routeIs('leaderboard.*') || request()->routeIs('sections.*') || request()->routeIs('achievements.*') ? '' : 'bg-no-repeat bg-center sm:bg-contain lg:bg-cover page-bg' }}">
    <audio id="hoverSound" preload="auto">
        <source src="{{ asset('audio/buttonhover.mp3') }}" type="audio/mpeg">
    </audio>

    <audio id="clickSound" preload="auto">
        <source src="{{ asset('audio/buttonclick.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b-0 sticky top-0 z-50 shadow-sm">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18 md:h-20">
                <!-- Left: Brand -->
                <div class="flex items-center flex-shrink-0">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo"
                                class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-xl">
                        </div>
                        <h1
                            class="text-base sm:text-lg md:text-xl lg:text-2xl font-extrabold text-gray-900 whitespace-nowrap">
                            Aral<span class="text-red-600">Sipnayan</span>
                        </h1>
                    </div>
                </div>


                <!-- Center: Desktop Navigation Links -->
                <div class="hidden xl:flex xl:items-center xl:space-x-1">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link flex items-center px-3 py-2 rounded-md text-sm xl:text-base font-medium transition-all duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('student.dashboard') ? 'text-blue-600 active' : 'text-gray-500 hover:text-blue-600' }}">
                        <span class="material-symbols-outlined mr-2 text-xl">home</span>
                        Dashboard
                    </a>


                    <a href="{{ route('assessments.index') }}"
                        class="nav-link flex items-center px-3 py-2 rounded-md text-sm xl:text-base font-medium transition-all duration-200 {{ request()->routeIs('assessments.*') ? 'text-blue-600 active' : 'text-gray-500 hover:text-blue-600' }}">
                        <span class="material-symbols-outlined mr-2 text-xl">assignment</span>
                        Assessments
                    </a>


                    <a href="{{ route('achievements.index') }}"
                        class="nav-link flex items-center px-3 py-2 rounded-md text-sm xl:text-base font-medium transition-all duration-200 {{ request()->routeIs('achievements.*') ? 'text-blue-600 active' : 'text-gray-500 hover:text-blue-600' }}">
                        <span class="material-symbols-outlined mr-2 text-xl">emoji_events</span>
                        Badges
                    </a>


                    <a href="{{ route('sections.index') }}"
                        class="nav-link flex items-center px-3 py-2 rounded-md text-sm xl:text-base font-medium transition-all duration-200 {{ request()->routeIs('sections.index') ? 'text-blue-600 active' : 'text-gray-500 hover:text-blue-600' }}">
                        <span class="material-symbols-outlined mr-2 text-xl">groups</span>
                        Section
                    </a>


                    <a href="{{ route('leaderboard.index') }}"
                        class="nav-link flex items-center px-3 py-2 rounded-md text-sm xl:text-base font-medium transition-all duration-200 {{ request()->routeIs('leaderboard.*') ? 'text-blue-600 active' : 'text-gray-500 hover:text-blue-600' }}">
                        <span class="material-symbols-outlined mr-2 text-xl">leaderboard</span>
                        Leaderboard
                    </a>
                </div>


                <!-- Right: User Profile Section -->
                <div class="flex items-center relative">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <!-- User Info - Hidden on very small screens -->
                        <div class="hidden xs:flex flex-col items-end">
                            <span class="text-xs sm:text-sm font-medium text-gray-900 truncate max-w-20 sm:max-w-none">
                                {{ Auth::guard('student')->user()?->studentProfile?->firstname }}
                            </span>
                
                            <div class="flex items-center space-x-1">
                                <span class="text-lg">🔥</span>
                                <span id="navStreakCounter"
                                    class="streak w-8 h-5 sm:w-10 sm:h-6 flex items-center justify-center text-xs text-white bg-orange-500 px-2 sm:px-1.5 py-0.5 rounded-xl">
                                    {{ Auth::guard('student')->user()?->studentProfile?->current_streak ?? 0 }}
                                </span>
                            </div>

                        </div>


                        <!-- Profile Avatar Button -->
                        <button
                            class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full overflow-hidden flex items-center justify-center border-2 border-gray-200 hover:border-blue-500 transition-all duration-200 flex-shrink-0"
                            id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <img src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}"
                                alt="{{ Auth::guard('student')->user()?->studentProfile?->firstname }}"
                                class="w-full h-full object-cover">
                        </button>
                    </div>


                    <!-- User Dropdown Menu -->
                    <div id="userDropdown"
                        class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-200 transform transition-all duration-200">
                        <div class="px-4 py-2 border-b border-gray-100 xs:hidden">
                            <p class="text-sm font-medium text-gray-900">
                                {{ Auth::guard('student')->user()?->studentProfile?->firstname }}
                            </p>
                            <p class="text-xs text-gray-500">Streak: <span id="dropdownStreakCounter">{{ Auth::guard('student')->user()?->studentProfile?->current_streak ?? 0 }}</span></p>
                        </div>
                        <a href="{{ route('student.profile') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>


    <!-- Main Content with proper padding -->
    <main>
        @yield('content')
    </main>


    <!-- Bottom Navigation (Mobile & Tablet) -->
    <nav
        class="xl:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] z-40">
        <!-- Safe area padding for devices with bottom notch -->
        <div class="pb-safe">
            <div class="grid grid-cols-5 gap-0 px-2 py-1">
                <a href="{{ route('dashboard') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('student.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">home</span>
                    <span class="text-xs sm:text-sm font-medium mt-0.5">Dashboard</span>
                </a>


                <a href="{{ route('assessments.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 {{ request()->routeIs('assessments.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">assignment</span>
                    <span class="text-xs sm:text-sm font-medium mt-0.5">Assessment</span>
                </a>


                <a href="{{ route('achievements.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 {{ request()->routeIs('achievements.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">emoji_events</span>
                    <span class="text-xs sm:text-sm font-medium mt-0.5">Badges</span>
                </a>


                <a href="{{ route('sections.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 {{ request()->routeIs('sections.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">groups</span>
                    <span class="text-xs sm:text-sm font-medium mt-0.5">Section</span>
                </a>


                <a href="{{ route('leaderboard.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 {{ request()->routeIs('leaderboard.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">leaderboard</span>
                    <span class="text-xs sm:text-sm font-medium mt-0.5">Leaderboard</span>
                </a>
            </div>
        </div>
    </nav>


    <!-- JavaScript for dropdown functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userMenuButton = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('userDropdown');

            // Handle logout form submission with fresh CSRF token
            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    // Update CSRF token from meta tag before submitting
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const csrfInput = this.querySelector('input[name="_token"]');
                    if (csrfToken && csrfInput) {
                        csrfInput.value = csrfToken.getAttribute('content');
                    }
                });
            }


            if (userMenuButton && dropdown) {
                // Toggle dropdown on button click
                userMenuButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isHidden = dropdown.classList.contains('hidden');
                    dropdown.classList.toggle('hidden');
                    userMenuButton.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                });


                // Close dropdown when clicking outside
                document.addEventListener('click', function (event) {
                    if (!userMenuButton.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                        userMenuButton.setAttribute('aria-expanded', 'false');
                    }
                });


                // Close dropdown on escape key
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && !dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('hidden');
                        userMenuButton.setAttribute('aria-expanded', 'false');
                        userMenuButton.focus();
                    }
                });
            }


            // Handle safe area for devices with notches
            if (window.CSS && window.CSS.supports && window.CSS.supports('padding-bottom', 'env(safe-area-inset-bottom)')) {
                document.documentElement.style.setProperty('--safe-area-inset-bottom', 'env(safe-area-inset-bottom)');
            }
        });


        // Global function for inline onclick (backup)
        function toggleDropdown() {
                    const dropdown = document.getElementById('userDropdown');
                    const button = document.getElementById('user-menu-button');
                    if (dropdown && button) {
                        const isHidden = dropdown.classList.contains('hidden');
                        dropdown.classList.toggle('hidden');
                        button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                    }
                }

                //Global button sound Effects
                document.addEventListener("DOMContentLoaded", () => {
            const hoverSound = document.getElementById("hoverSound");
            const clickSound = document.getElementById("clickSound");

            // Function to safely play sound (handles autoplay restrictions)
            function playSound(sound) {
                sound.currentTime = 0; // restart each time
                sound.play().catch(() => {}); // ignore autoplay errors
            }

            // Select all links and buttons
            const interactiveElements = document.querySelectorAll("a, button");

            interactiveElements.forEach(el => {
                el.addEventListener("mouseenter", () => playSound(hoverSound));
                el.addEventListener("click", () => playSound(clickSound));
            });
        });
    </script>


    @stack('scripts')
</body>


</html>