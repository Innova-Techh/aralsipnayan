<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard')</title>

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
            transition: all 0.25s ease;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* Hover effect (all devices) */
        .nav-link:hover .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 28;
            transform: scale(1.2);
            color: #2563eb;
        }

        /* Active effect (all devices) */
        .nav-link.active .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 28;
            transform: scale(1.2);
            color: #2563eb;
        }

        /* Remove active background (desktop + mobile) */
        .nav-link.active {
            background-color: transparent !important;
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

        /* Remove global background image */

        @media (max-width: 1279px) {
            .nav-link.active span:last-child {
                font-weight: 600;
                color: #2563eb;
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

<body class="min-h-screen bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg flex flex-col">
            <!-- Logo/Brand -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">AS</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">AralSip</h1>
                        <p class="text-sm text-gray-500">Teacher Portal</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-6">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('teacher.dashboard') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">dashboard</span>
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- Assessment Management with Submenu -->
                    <li>
                        <div class="space-y-1">
                            <a href="{{ route('teacher.assessments') }}"
                               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.assessments*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                <span class="material-symbols-outlined mr-3">assignment</span>
                                Assessment Management
                                <span class="material-symbols-outlined ml-auto">expand_more</span>
                            </a>
                            @if(request()->routeIs('teacher.assessments*'))
                            <ul class="ml-10 space-y-1">
                                <li>
                                    <a href="{{ route('teacher.assessments.create') }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                                        <span class="material-symbols-outlined mr-2 text-sm">add_circle</span>
                                        Create Assessment
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('teacher.assessments') }}"
                                       class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                                        <span class="material-symbols-outlined mr-2 text-sm">list</span>
                                        Manage Assessments
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                       class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                                        <span class="material-symbols-outlined mr-2 text-sm">quiz</span>
                                        Assessment Templates
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                       class="flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg">
                                        <span class="material-symbols-outlined mr-2 text-sm">help</span>
                                        Question Bank
                                    </a>
                                </li>
                            </ul>
                            @endif
                        </div>
                    </li>

                    <li>
                        <a href="#"
                           class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined mr-3">help</span>
                            Question Bank
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('teacher.sections') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.sections*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">groups</span>
                            Section Management
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('teacher.students') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.students*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">school</span>
                            Student Management
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('teacher.analytics') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.analytics*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">analytics</span>
                            Analytics
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('teacher.profile') }}"
                           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.profile*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">person</span>
                            Profile
                        </a>
                    </li>

                    <li>
                        <a href="#"
                           class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <span class="material-symbols-outlined mr-3">settings</span>
                            Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="material-symbols-outlined text-gray-400">menu</span>
                        <span class="text-gray-600 font-medium">Teacher</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Search Bar -->
                        <div class="relative">
                            <input type="text" placeholder="Search..." 
                                   class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                        </div>

                        <!-- User Profile -->
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->username ?? 'Maria Santos' }}</p>
                                <p class="text-xs text-gray-500">Grade 7 Teacher</p>
                            </div>
                            <div class="relative">
                                <button id="user-menu-button" class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition-colors">
                                    <img src="{{ asset('images/profile/avatar1.png') }}" alt="Profile" class="w-full h-full object-cover">
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="userDropdown" class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                                    <a href="{{ route('teacher.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile Settings</a>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JavaScript for dropdown functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userMenuButton = document.getElementById('user-menu-button');
            const dropdown = document.getElementById('userDropdown');

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
    </script>

    @stack('scripts')
</body>

</html>