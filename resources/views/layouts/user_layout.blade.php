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

    /* Show global background image only on large screens and up */
    @media (min-width: 1024px) {
        body.page-bg {
            background-image: url('{{ asset('images/global/bg.svg') }}');
        }
    }
</style>

    
    @stack('styles')


</head>
<body 
    class="min-h-screen {{ request()->routeIs('profile.edit') || request()->routeIs('leaderboard.*') || request()->routeIs('sections.*') || request()->routeIs('achievements.*') ? '' : 'bg-no-repeat bg-center sm:bg-contain lg:bg-cover page-bg' }}"
>
    <div class="min-h-screen">
    <nav class="bg-white border-b border-gray-200">
    <div class="w-full mx-auto px-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left: Brand -->
            <div class="flex items-center">
                <div class="flex items-center">
                    <div class="w-50 h-50 flex items-center justify-center mr-3">
                        <!-- Replace with your actual logo -->
                        <div class="w-full h-full flex items-center justify-center ">
                                <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo" class="w-10 h-10 rounded-xl"> 
                        </div>
                    </div>
                    <h1 class="text-lg font-semibold text-gray-900">
                        Aral<span class="text-red-600">Sipnayan</span>
                    </h1>
                </div>
            </div>

            <!-- Center: Navigation Links -->
            <div class="hidden md:flex md:items-center md:space-x-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('student.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('assessments.index') }}" class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('assessments.*') ? 'text-blue-600' : 'text-gray-500'  }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Assessments
                </a>

                <a href="{{ route('achievements.index') }}" class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('achievements.*') ?'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Achievements
                </a>

                <a href="{{ route('sections.index') }}" class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('sections.index') ?'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    My Section
                </a>

                <a href="{{ route('leaderboard.index') }}" class="flex items-center px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('leaderboard.*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Leaderboard
                </a>
            </div>

            <!-- Right: User menu -->
            <div class="flex items-center">
                <div class="relative">
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-medium text-gray-900">{{ Auth::user()->username }}</span>
                            <span class="text-xs text-gray-500 bg-green-200 px-2 py-1 rounded-full">Sampaguita</span>
                        </div>
                        <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border-2 border-gray-200">
                            <img src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}" 
                                 alt="{{ Auth::user()->username ?? 'Student' }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        <button class="ml-1 p-1 rounded hover:bg-gray-100 focus:outline-none transition-colors duration-200" id="user-menu-button">
                            <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- User dropdown menu -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-200">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>



        <!-- Main Content -->
        <main class="max-w-8xl mx-auto px-6 sm:px-8 lg:px-12 pb-24 md:pb-8">
            @yield('content')
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.04)] z-40">
            <div class="grid grid-cols-5 gap-1">
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
                <a href="{{ route('sections.index') }}" class="flex flex-col items-center justify-center py-2 {{ request()->routeIs('sections.*') ? 'text-blue-600' : 'text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-[11px]">My Section</span>
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

    <!-- JavaScript for dropdown functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple dropdown functionality
            const userMenuButton = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('userDropdown');

            if (userMenuButton && dropdown) {
                // Toggle dropdown on button click
                userMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });

        // Global function for inline onclick (backup)
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>