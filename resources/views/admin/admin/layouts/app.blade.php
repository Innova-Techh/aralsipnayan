<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - @yield('title', 'Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Prevent layout shift during transitions */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="relative z-30 flex flex-col h-full bg-white shadow-lg transition-all duration-300 ease-in-out hidden lg:flex">

            <!-- Logo Section -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <div class="flex items-center" :class="sidebarOpen ? 'space-x-3' : 'justify-center w-full'">
                    <!-- Logo -->
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo"
                            class="w-10 h-10 sm:w-10 sm:h-10 md:w-10 md:h-10 rounded-xl">
                    </div>

                    <!-- Title - Hidden when collapsed -->
                    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="overflow-hidden">
                        <h1 class="text-xl font-semibold whitespace-nowrap">
                            <span class="text-gray-800">Aral</span><span class="text-red-500">Sipnayan</span>
                        </h1>
                        <p class="text-xs text-gray-500 whitespace-nowrap">Faculty Dashboard</p>
                    </div>
                </div>

                <!-- Desktop Toggle Button - Always visible -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors duration-200 absolute right-2"
                    :class="!sidebarOpen ? 'right-5' : ''">
                    <i class="fas text-gray-600" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto sidebar-scrollbar py-4" x-data="{ activeDropdown: null }">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard', [], false) }}"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-th-large text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Dashboard</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                        Dashboard
                    </div>
                </a>

                <!-- Assessment Management (Dropdown) -->
                <div class="mb-1">
                    <button
                        @click="sidebarOpen ? activeDropdown = activeDropdown === 'assessment' ? null : 'assessment' : ''"
                        class="relative w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group"
                        :class="{ 'bg-blue-50 text-blue-600': activeDropdown === 'assessment' }">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8">
                                <i class="fas fa-clipboard-list text-lg"></i>
                            </div>
                            <span x-show="sidebarOpen"
                                x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity ease-in duration-100"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="ml-3 font-medium whitespace-nowrap">Assessment Management</span>
                        </div>
                        <i x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity ease-in duration-100"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': activeDropdown === 'assessment' }"></i>

                        <!-- Tooltip for collapsed state -->
                        <div x-show="!sidebarOpen"
                            class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                            Assessment Management
                        </div>
                    </button>

                    <!-- Dropdown Items -->
                    <div x-show="activeDropdown === 'assessment' && sidebarOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="bg-gray-50">
                        <a href="#"
                            class="flex items-center pl-16 pr-4 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            Create Assessment
                        </a>
                        <a href="#"
                            class="flex items-center pl-16 pr-4 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            View Assessments
                        </a>
                        <a href="#"
                            class="flex items-center pl-16 pr-4 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            Results & Analytics
                        </a>
                    </div>
                </div>

                <!-- Section Management -->
                <a href="#"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-users text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Section Management</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                        Section Management
                    </div>
                </a>

                <!-- Student Management -->
                <a href="#"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-graduation-cap text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Student Management</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                        Student Management
                    </div>
                </a>

                <!-- Analytics -->
                <a href="#"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-chart-bar text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Analytics</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                        Analytics
                    </div>
                </a>

                <!-- Profile -->
                <a href="#"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-user text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Profile</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                        Profile
                    </div>
                </a>

                <!-- Settings -->
                <a href="#"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-cog text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Settings</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                        Settings
                    </div>
                </a>

            </nav>

            <!-- Logout Button -->
            <div class="border-t border-gray-200 p-4">
                <form method="POST" action="{{ route('admin.logout', [], false) }}">
                    @csrf
                    <button type="submit"
                        class="relative w-full flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group">
                        <div class="flex items-center justify-center w-8">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </div>
                        <span x-show="sidebarOpen"
                            x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity ease-in duration-100"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="ml-3 font-medium whitespace-nowrap">Logout</span>

                        <!-- Tooltip for collapsed state -->
                        <div x-show="!sidebarOpen"
                            class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity">
                            Logout
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-lg lg:hidden" @click.away="mobileMenuOpen = false">

            <!-- Mobile menu content (same as desktop but always expanded) -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-lg">
                        <span class="text-white font-bold text-xl">A</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold">
                            <span class="text-gray-800">Aral</span><span class="text-red-500">Sipnayan</span>
                        </h1>
                        <p class="text-xs text-gray-500">Faculty Dashboard</p>
                    </div>
                </div>
                <button @click="mobileMenuOpen = false" class="p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>

            <!-- Mobile Navigation (simplified) -->
            <nav class="flex-1 overflow-y-auto py-4">
                <a href="{{ route('admin.dashboard', [], false) }}"
                    class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-th-large w-8"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-clipboard-list w-8"></i>
                    <span class="ml-3">Assessment Management</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-users w-8"></i>
                    <span class="ml-3">Section Management</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-graduation-cap w-8"></i>
                    <span class="ml-3">Student Management</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-chart-bar w-8"></i>
                    <span class="ml-3">Analytics</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-user w-8"></i>
                    <span class="ml-3">Profile</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-cog w-8"></i>
                    <span class="ml-3">Settings</span>
                </a>
            </nav>
        </aside>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-bars text-gray-600"></i>
                    </button>

                    <!-- Breadcrumb or Page Title -->
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 relative">
                            <i class="fas fa-bell text-gray-600"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User Avatar -->
                        <div class="flex items-center space-x-3" x-data="{ userMenuOpen: false }">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Admin User' }}
                                </p>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                            <button @click="userMenuOpen = !userMenuOpen"
                                class="relative w-10 h-10 bg-gray-300 rounded-full hover:ring-2 hover:ring-blue-500 transition-all duration-200">
                                <img src="https://ui-avatars.com/api/?name=Admin+User&background=3B82F6&color=fff"
                                    alt="User Avatar" class="w-full h-full rounded-full">
                            </button>

                            <!-- User Dropdown Menu -->
                            <div x-show="userMenuOpen" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" @click.away="userMenuOpen = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My
                                    Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account
                                    Settings</a>
                                <hr class="border-gray-200">
                                <form method="POST" action="{{ route('admin.logout', [], false) }}" class="block">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
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

    @stack('scripts')
</body>

</html>