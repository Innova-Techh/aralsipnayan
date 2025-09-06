<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    
    <!-- Tailwind (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    /* Baloo 2 Regular */
    @font-face {
        font-family: 'Baloo 2';
        src: url('{{ asset("fonts/baloo2/Baloo2-Regular.ttf") }}') format('truetype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }

    /* Baloo 2 Bold */
    @font-face {
        font-family: 'Baloo 2';
        src: url('{{ asset("fonts/baloo2/Baloo2-Bold.ttf") }}') format('truetype');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }

    /* Baloo 2 ExtraBold */
    @font-face {
        font-family: 'Baloo 2';
        src: url('{{ asset("fonts/baloo2/Baloo2-ExtraBold.ttf") }}') format('truetype');
        font-weight: 800;
        font-style: normal;
        font-display: swap;
    }
</style>

    
    @stack('styles')


</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-8xl mx-auto px-6 sm:px-8 lg:px-12">
                <div class="flex items-center justify-between h-16">
                    <!-- Left: Brand -->
                    <div class="flex items-center">
                        <!-- Brand -->
                        <div class="flex items-center">
                            <div class="w-10 h-10 flex items-center justify-center mr-3">
                                <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo" class="w-10 h-10 rounded-xl"> 
                            </div>
                            <h1 class="text-lg font-semibold text-gray-900">AralSipnayan</h1>
                        </div>
                    </div>

                    <!-- Center: Desktop Navigation Links -->
                    <div class="hidden lg:flex lg:items-center lg:justify-center lg:flex-1 lg:px-8">
                        <div class="flex space-x-6 xl:space-x-8 2xl:space-x-10">
                            <a href="{{ route('dashboard') }}" class="whitespace-nowrap border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} px-3 pt-1 pb-4 text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('assessments.index') }}" class="whitespace-nowrap border-b-2 {{ request()->routeIs('assessments.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-300' }} px-3 pt-1 pb-4 text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Assessments</span>
                            </a>
                            <a href="{{ route('achievements.index') }}" class="whitespace-nowrap border-b-2 {{ request()->routeIs('achievements.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-300' }} px-3 pt-1 pb-4 text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span>Achievements</span>
                            </a>

                            <a href="{{ route('leaderboard.index') }}" class="whitespace-nowrap border-b-2 {{ request()->routeIs('leaderboard.*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-300' }} px-3 pt-1 pb-4 text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <span>Leaderboard</span>
                            </a>

                        </div>
                    </div>

                    <!-- Right: User menu -->
                    <div class="flex items-center">
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-blue-600 font-semibold">{{ Auth::user() ? substr(Auth::user()->name, 0, 1) : 'J' }}</span>
                                </div>
                                <span class="hidden md:block">{{ Auth::user() ? Auth::user()->name : 'Juan Dela Cruz' }}</span>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <!-- User dropdown menu -->
                            <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>


        <!-- Mobile menu overlay -->
        <div id="mobile-overlay" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-40 md:hidden"></div>

        <!-- Main Content -->
        <main class="max-w-8xl mx-auto px-6 sm:px-8 lg:px-12 pb-24 md:pb-8">
            @yield('content')
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.04)] z-40">
            <div class="grid grid-cols-4 gap-1">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-2 {{ request()->routeIs('dashboard') || request()->routeIs('student.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                    <span class="text-[11px]">Dashboard</span>
                </a>
                <a href="{{ route('assessments.index') }}" class="flex flex-col items-center justify-center py-2 {{ request()->routeIs('assessments.*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9v10a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-[11px]">Assessment</span>
                </a>
                <a href="{{ route('achievements.index') }}" class="flex flex-col items-center justify-center py-2 {{ request()->routeIs('achievements.*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span class="text-[11px]">Achievements</span>
                </a>
                <a href="{{ route('leaderboard.index') }}" class="flex flex-col items-center justify-center py-2 {{ request()->routeIs('leaderboard.*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4v11H3V10zm7-6h4v17h-4V4zm7 9h4v8h-4v-8z"/>
                    </svg>
                    <span class="text-[11px]">Leaderboard</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- JavaScript for interactive functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu functionality
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileOverlay = document.getElementById('mobile-overlay');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');

            function openMobileMenu() {
                mobileMenu.classList.add('open');
                mobileOverlay.classList.remove('hidden');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            }

            function closeMobileMenu() {
                mobileMenu.classList.remove('open');
                mobileOverlay.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }

            mobileMenuButton.addEventListener('click', function() {
                if (mobileMenu.classList.contains('open')) {
                    closeMobileMenu();
                } else {
                    openMobileMenu();
                }
            });

            mobileMenuClose.addEventListener('click', closeMobileMenu);
            mobileOverlay.addEventListener('click', closeMobileMenu);

            // User dropdown functionality
            const userMenuButton = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');

            userMenuButton.addEventListener('click', function() {
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // Close mobile menu when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>