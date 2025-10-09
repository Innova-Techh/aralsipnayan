<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aralsipnayan')</title>

    <!-- Vite (Tailwind + app JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Material Icons (teacher icons preserved) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,0"
        rel="stylesheet">

    <!-- Font Awesome (for header toggle/search/notifications to match admin header) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS (keeps teacher font + small tweaks) -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            overflow-x: hidden;
        }

        .material-symbols-outlined {
            transition: all 0.25s ease;
            font-variation-settings: 'FILL' 0, 'wght' 400;
        }

        /* small tooltip helper for collapsed sidebar entries (kept minimal) */
        [x-cloak] {
            display: none !important;
        }

        /* Sidebar scrollbar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 3px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.12);
        }

        /* Ensure submenu items are visible when sidebar is collapsed */
        .submenu-item {
            position: relative;
        }

        /* Prevent horizontal overflow and constrain tooltips */
        .sidebar-container {
            flex-shrink: 0;
            overflow: visible;
        }

        .main-content {
            min-width: 0;
            overflow: hidden;
        }

        /* Constrain tooltips to prevent horizontal scroll */
        .tooltip-container {
            position: relative;
        }

        .tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 0.5rem;
            pointer-events: none;
            z-index: 50;
            white-space: nowrap;
        }

        /* Hide tooltips that would cause overflow */
        @media (max-width: 1024px) {
            .tooltip {
                display: none !important;
            }
        }

        /* Ensure the main container doesn't overflow */
        .flex.h-screen {
            overflow: hidden;
        }
    </style>

    @stack('styles')
</head>

<body x-data="{ sidebarOpen: true }" class="min-h-screen bg-gray-50" style="overflow-x: hidden;">
    <div class="flex h-screen overflow-hidden" style="overflow-x: hidden;">

        <!-- Sidebar (teacher icons kept as material-symbols) -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="sidebar-container relative flex flex-col h-full bg-white shadow-lg transition-all duration-300 ease-in-out"
            style="overflow: visible;">
            <!-- Logo/Brand -->
            <div class="flex items-center h-16 px-4 border-b border-gray-200"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <div class="flex items-center" :class="sidebarOpen ? 'space-x-3' : ''">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo"
                            class="w-10 h-10 rounded-xl">
                    </div>

                    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <h1
                            class="text-base sm:text-lg md:text-xl lg:text-2xl font-extrabold text-gray-900 whitespace-nowrap">
                            Aral<span class="text-red-600">Sipnayan</span>
                        </h1>
                        <p class="text-sm text-gray-500">Faculty Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-2 py-4 sidebar-scrollbar overflow-y-auto" style="overflow-x: hidden;">
                <ul class="space-y-1">
                    <li class="tooltip-container">
                        <a href="{{ route('teacher.dashboard') }}"
                            class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">dashboard</span>
                            <span x-show="sidebarOpen">Dashboard</span>

                            <!-- tooltip when collapsed -->
                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Dashboard
                            </div>
                        </a>
                    </li>

                    <!-- Assessment Management with submenu - FIXED -->
                    <li x-data="{ openSub: {{ request()->routeIs('teacher.assessments*') ? 'true' : 'false' }} }"
                        class="tooltip-container">
                        <button @click="openSub = !openSub"
                            class="w-full group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 transition">
                            <span class="material-symbols-outlined mr-3">assignment</span>
                            <span x-show="sidebarOpen">Assessment Management</span>
                            <span x-show="sidebarOpen" class="material-symbols-outlined ml-auto"
                                :class="openSub ? 'rotate-180' : ''">expand_more</span>

                            <!-- Tooltip for main button when collapsed -->
                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Assessment Management
                            </div>
                        </button>

                        <ul x-show="openSub" x-collapse :class="sidebarOpen ? 'ml-10' : 'ml-2'" class="mt-1 space-y-1">
                            <li class="submenu-item tooltip-container">
                                <a href="{{ route('teacher.assessments.create') }}"
                                    class="group relative flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined mr-2 text-sm">add_circle</span>
                                    <span x-show="sidebarOpen">Create Assessment</span>

                                    <!-- Tooltip for submenu item when collapsed -->
                                    <div x-cloak x-show="!sidebarOpen"
                                        class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                        Create Assessment
                                    </div>
                                </a>
                            </li>
                            <li class="submenu-item tooltip-container">
                                <a href="{{ route('teacher.assessments') }}"
                                    class="group relative flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined mr-2 text-sm">list</span>
                                    <span x-show="sidebarOpen">Manage Assessments</span>

                                    <!-- Tooltip for submenu item when collapsed -->
                                    <div x-cloak x-show="!sidebarOpen"
                                        class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                        Manage Assessments
                                    </div>
                                </a>
                            </li>
                            <li class="submenu-item tooltip-container">
                                <a href="#"
                                    class="group relative flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined mr-2 text-sm">quiz</span>
                                    <span x-show="sidebarOpen">Assessment Templates</span>

                                    <!-- Tooltip for submenu item when collapsed -->
                                    <div x-cloak x-show="!sidebarOpen"
                                        class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                        Assessment Templates
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="tooltip-container">
                        <a href="{{ route('teacher.sections') }}"
                            class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.sections*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">groups</span>
                            <span x-show="sidebarOpen">Section Management</span>

                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Section Management
                            </div>
                        </a>
                    </li>

                    <li class="tooltip-container">
                        <a href="{{ route('teacher.students') }}"
                            class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.students*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">school</span>
                            <span x-show="sidebarOpen">Student Management</span>
                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Student Management
                            </div>
                        </a>
                    </li>

                    <li class="tooltip-container">
                        <a href="{{ route('teacher.analytics') }}"
                            class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.analytics*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">analytics</span>
                            <span x-show="sidebarOpen">Analytics</span>
                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Analytics
                            </div>
                        </a>
                    </li>

                    <li class="tooltip-container">
                        <a href="{{ route('teacher.profile') }}"
                            class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('teacher.profile*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="material-symbols-outlined mr-3">person</span>
                            <span x-show="sidebarOpen">Profile</span>
                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Profile
                            </div>
                        </a>
                    </li>

                    <li class="tooltip-container">
                        <a href="#"
                            class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                            <span class="material-symbols-outlined mr-3">settings</span>
                            <span x-show="sidebarOpen">Settings</span>
                            <div x-cloak x-show="!sidebarOpen"
                                class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                Settings
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile & Logout Section (preserved logic) -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center mb-3 tooltip-container" :class="sidebarOpen ? '' : 'justify-center'">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Teacher') }}&background=3B82F6&color=fff"
                        alt="Teacher Avatar" class="w-10 h-10 rounded-full flex-shrink-0">
                    <div x-show="sidebarOpen" x-transition class="ml-3">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Teacher' }}</p>
                        <p class="text-xs text-gray-500">Educator</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('teacher.logout') }}">
                    @csrf
                    <button type="submit"
                        class="relative w-full tooltip-container flex items-center justify-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                        <div class="flex items-center justify-center w-8">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </div>
                        <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap">Logout</span>

                        <div x-cloak x-show="!sidebarOpen"
                            class="tooltip px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity">
                            Logout
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content flex-1 flex flex-col overflow-hidden">
            <!-- Top Header (now matches admin header layout exactly) -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Left Side: Toggle Button and Title -->
                    <div class="flex items-center space-x-4">
                        <!-- Sidebar Toggle Button -->
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <i
                                :class="sidebarOpen ? 'fas fa-arrow-left text-gray-600 text-lg' : 'fas fa-arrow-right text-gray-600 text-lg'"></i>
                        </button>

                        <!-- Page Title -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Teacher</h2>
                        </div>
                    </div>

                    <!-- Center: Search Bar (matches admin spacing) -->
                    <div class="flex-1 max-w-2xl mx-8">
                        <div class="relative">
                            <input type="text" placeholder="Search..."
                                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Right Side: Notifications & User (matches admin layout) -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ notificationOpen: false }">
                            <button @click="notificationOpen = !notificationOpen"
                                class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 relative">
                                <i class="fas fa-bell text-gray-600"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div x-show="notificationOpen" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" @click.away="notificationOpen = false"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <p class="text-sm text-gray-800">Exam grading completed</p>
                                        <p class="text-xs text-gray-500 mt-1">5 minutes ago</p>
                                    </a>
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <p class="text-sm text-gray-800">New student joined your section</p>
                                        <p class="text-xs text-gray-500 mt-1">1 hour ago</p>
                                    </a>
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50">
                                        <p class="text-sm text-gray-800">15 new students enrolled</p>
                                        <p class="text-xs text-gray-500 mt-1">3 hours ago</p>
                                    </a>
                                </div>
                                <div class="p-3 border-t border-gray-200">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View all
                                        notifications</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile (keeps your JS dropdown IDs intact) -->
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <!-- preserved original teacher profile display call -->
                                <p class="text-sm font-medium text-gray-900">
                                    {{ Auth::guard('admin')->user()?->teacherProfile?->firstname }}
                                </p>
                                <p class="text-xs text-gray-500">Grade 6 Teacher</p>
                            </div>
                            <div class="relative">
                                <button id="user-menu-button"
                                    class="w-10 h-10 rounded-full overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition-colors">
                                    <img src="{{ asset('images/profile/avatar1.png') }}" alt="Profile"
                                        class="w-full h-full object-cover">
                                </button>

                                <!-- Dropdown Menu (your existing JS toggles this) -->
                                <div id="userDropdown"
                                    class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                                    <a href="{{ route('teacher.profile') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile
                                        Settings</a>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
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

    <!-- KEEP ORIGINAL JS LOGIC (unchanged) -->
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