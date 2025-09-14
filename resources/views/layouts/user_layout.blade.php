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

    <!-- Custom CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            transition: all 0.2s ease;
        }

        .nav-link:hover .material-symbols-outlined {
            font-variation-settings: 'FILL' 1;
            color: #2563eb;
        }

        .nav-link:active .material-symbols-outlined:active {
            font-variation-settings: 'FILL' 1;
            color: #2563eb;
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
    class="min-h-screen {{ request()->routeIs('profile.edit') || request()->routeIs('leaderboard.*') || request()->routeIs('sections.*') || request()->routeIs('achievements.*') ? '' : 'bg-no-repeat bg-center sm:bg-contain lg:bg-cover page-bg' }}">
    <div class="min-h-screen">
        <nav class="bg-white border-b border-gray-200">
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-24 sm:h-20 md:h-24">
                    <!-- Left: Brand -->
                    <div class="flex items-center">
                        <div class="flex items-center space-x-">
                            <div class="flex items-center justify-center">
                                <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo"
                                    class="w-10 h-10 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-xl">
                            </div>
                            <h1 class="block text-lg sm:text-lg md:text-xl lg:text-2xl font-extrabold text-gray-900">
                                Aral<span class="text-red-600">Sipnayan</span>
                            </h1>
                        </div>
                    </div>

                    <!-- Center: Navigation Links -->
                    <div class="hidden md:flex md:items-center md:space-x-1">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link flex items-center px-3 py-3 rounded-md text-lg md:text-base font-medium transition-all duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('student.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                            <span class="material-symbols-outlined mr-2">home</span>
                            Dashboard
                        </a>

                        <a href="{{ route('assessments.index') }}"
                            class="nav-link flex items-center px-3 py-3 rounded-md text-lg md:text-base font-medium transition-all duration-200 {{ request()->routeIs('assessments.*') ? 'text-blue-600' : 'text-gray-500'  }}">
                            <span class="material-symbols-outlined mr-2">assignment</span>
                            Assessments
                        </a>

                        <a href="{{ route('achievements.index') }}"
                            class="nav-link flex items-center px-3 py-3 rounded-md text-lg md:text-base font-medium transition-all duration-200 {{ request()->routeIs('achievements.*') ? 'text-blue-600' : 'text-gray-500' }}">
                            <span class="material-symbols-outlined mr-2">emoji_events</span>
                            Achievements
                        </a>

                        <a href="{{ route('sections.index') }}"
                            class="nav-link flex items-center px-3 py-3 rounded-md text-lg md:text-base font-medium transition-all duration-200 {{ request()->routeIs('sections.index') ? 'text-blue-600' : 'text-gray-500' }}">
                            <span class="material-symbols-outlined mr-2">groups</span>
                            My Section
                        </a>

                        <a href="{{ route('leaderboard.index') }}"
                            class="nav-link flex items-center px-3 py-3 rounded-md text-lg md:text-base font-medium transition-all duration-200 {{ request()->routeIs('leaderboard.*') ? 'text-blue-600' : 'text-gray-500' }}">
                            <span class="material-symbols-outlined mr-2">leaderboard</span>
                            Leaderboard
                        </a>
                    </div>

                    <!-- Right: User menu -->
                    <div class="flex items-center">
                        <div class="relative">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-sm sm:text-base font-medium text-gray-900">{{ Auth::user()->username }}</span>
                                    <span
                                        class="streak w-12 h-6 flex items-center justify-center text-xs sm:text-sm text-white bg-orange-500 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-xl">4</span>
                                </div>
                                <button
                                    class="w-10 h-10 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full overflow-hidden flex items-center justify-center border-2 border-gray-200 hover:border-blue-500 transition-all duration-200"
                                    id="user-menu-button">
                                    <img src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}"
                                        alt="{{ Auth::user()->username ?? 'Student' }}"
                                        class="w-full h-full object-cover">
                                </button>
                            </div>

                            <!-- User dropdown menu -->
                            <div id="userDropdown"
                                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-200 transform transition-all duration-200">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
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
            </div>
        </nav>



        <!-- Main Content -->
        <main class="max-w-8xl mx-auto px-6 sm:px-8 lg:px-12 pb-24 md:pb-8">
            @yield('content')
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav
            class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.04)] z-40 py-1">
            <div class="grid grid-cols-5 gap-0.5 px-1">
                <a href="{{ route('dashboard') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 relative {{ request()->routeIs('dashboard') || request()->routeIs('student.dashboard') ? 'text-blue-600 ' : 'text-gray-500' }}">
                    <span class="material-symbols-outlined">home</span>
                    <span class="text-[11px]">Dashboard</span>
                </a>
                <a href="{{ route('assessments.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 relative {{ request()->routeIs('assessments.*') ? 'text-blue-600 ' : 'text-gray-500' }}">
                    <span class="material-symbols-outlined bg-red-700">assignment</span>
                    <span class="text-[11px]">Assessment</span>
                </a>
                <a href="{{ route('achievements.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 relative {{ request()->routeIs('achievements.*') ? 'text-blue-600 ' : 'text-gray-500' }}">
                    <span class="material-symbols-outlined">emoji_events</span>
                    <span class="text-[11px]">Achievements</span>
                </a>
                <a href="{{ route('sections.index') }}"
                    class="nav-link flex flex-col items-center justify-center py-2 relative {{ request()->routeIs('sections.*') ? 'text-blue-600 ' : 'text-gray-500' }}">
                    <span class="material-symbols-outlined">groups</span>
                    <span class="text-[11px]">My Section</span>
                </a>
                <a href="{{ route('leaderboard.index') }}"
                    class="nav-link flex flex-col items-c enter justify-center py-2 relative {{ request()->routeIs('leaderboard.*') ? 'text-blue-600 ' : 'text-gray-500' }}">
                    <span class="material-symbols-outlined">leaderboard</span>
                    <span class="text-[11px]">Leaderboard</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- JavaScript for dropdown functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Simple dropdown functionality
            const userMenuButton = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('userDropdown');

            if (userMenuButton && dropdown) {
                // Toggle dropdown on button click
                userMenuButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function (event) {
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