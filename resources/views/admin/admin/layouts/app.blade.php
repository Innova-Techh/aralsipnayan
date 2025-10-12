<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

<body class="bg-gray-100" x-data="{ sidebarOpen: true}">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="relative flex flex-col h-full bg-white shadow-lg transition-all duration-300 ease-in-out">

            <!-- Logo Section -->
            <div class="flex items-center h-16 px-4 border-b border-gray-200"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <div class="flex items-center" :class="sidebarOpen ? 'space-x-3' : ''">
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
                            <span class="text-gray-800 font-bold text-xl">Aral</span><span
                                class="text-red-500 font-bold text-xl">Sipnayan</span>
                        </h1>
                        <p class="text-xs text-gray-500 whitespace-nowrap">Admin Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto sidebar-scrollbar py-4">

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
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Dashboard
                    </div>
                </a>

                <!-- Admin Management -->
                <a href="{{ route('admin.management.admins') }}"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.management.admins') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fa-solid fa-user-gear text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Admin Management</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Admin Management
                    </div>
                </a>

                <!-- Teacher Management -->
                <a href="{{ route('admin.management.teachers') }}"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.management.teachers') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Teacher Management</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Teacher Management
                    </div>
                </a>

                <!-- Section Management -->
                <a href="{{ route('admin.management.sections') }}"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.management.sections') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-th-list text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Section Management</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Section Management
                    </div>
                </a>

                <!-- Student Management -->
                <a href="{{ route('admin.management.students') }}"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.management.students') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Student Management</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Student Management
                    </div>
                </a>

                <!-- Question Bank -->
                <a href="#"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-question-circle text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Question Bank</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Question Bank
                    </div>
                </a>


                <!-- Profile -->
                <a href="{{ route('admin.profile.index') }}"
                    class="relative flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('admin.profile.*') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : '' }}">
                    <div class="flex items-center justify-center w-8">
                        <i class="fas fa-user-circle text-lg"></i>
                    </div>
                    <span x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="ml-3 font-medium whitespace-nowrap">Profile</span>

                    <!-- Tooltip for collapsed state -->
                    <div x-show="!sidebarOpen"
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
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
                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                        Settings
                    </div>
                </a>

            </nav>

            <!-- User Profile & Logout Section -->
            <div class="border-t border-gray-200 p-4">
                <!-- User Info -->
                <a href="{{ route('admin.profile.index') }}"
                    class="flex items-center mb-3 hover:bg-gray-50 rounded-lg p-2 transition-colors"
                    :class="sidebarOpen ? '' : 'justify-center'">
                    @php
                        $admin = Auth::guard('admin')->user();
                        $adminProfile = $admin->adminProfile ?? null;
                        $fullName = $adminProfile ? trim($adminProfile->firstname . ' ' . $adminProfile->lastname) : $admin->username;
                        $initials = collect(explode(' ', $fullName))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');
                        $profilePhoto = $adminProfile && $adminProfile->profile_photo
                            ? asset('storage/' . $adminProfile->profile_photo)
                            : "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=3B82F6&color=fff";
                    @endphp
                    <img src="{{ $profilePhoto }}" alt="Admin Avatar"
                        class="w-10 h-10 rounded-full flex-shrink-0 object-cover">
                    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300 delay-100"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition-opacity ease-in duration-100"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="ml-3">
                        <p class="text-sm font-semibold text-gray-800">{{ $fullName }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($admin->role) }}</p>
                    </div>
                </a>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout', [], false) }}" id="admin-logout-form">
                    @csrf
                    <button type="submit"
                        class="relative w-full flex items-center justify-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'">
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
                            class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-sm rounded opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition-opacity z-50">
                            Logout
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Header -->
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
                            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Admin Dashboard')</h2>
                        </div>
                    </div>

                    <!-- Center: Search Bar -->
                    <div class="flex-1 max-w-2xl mx-8">
                        <div class="relative">
                            <input type="text" placeholder="Search..."
                                class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Right Side: User Info and Actions -->
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
                                        <p class="text-sm text-gray-800">New teacher registration pending</p>
                                        <p class="text-xs text-gray-500 mt-1">5 minutes ago</p>
                                    </a>
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <p class="text-sm text-gray-800">System backup completed</p>
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

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen"
                                class="flex items-center space-x-3 hover:bg-gray-50 rounded-lg p-2 transition-colors">
                                @php
                                    $admin = Auth::guard('admin')->user();
                                    $adminProfile = $admin->adminProfile ?? null;
                                    $fullName = $adminProfile ? trim($adminProfile->firstname . ' ' . $adminProfile->lastname) : $admin->username;
                                    $initials = collect(explode(' ', $fullName))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->implode('');
                                    $profilePhoto = $adminProfile && $adminProfile->profile_photo
                                        ? asset('storage/' . $adminProfile->profile_photo)
                                        : "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=3B82F6&color=fff";
                                @endphp
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-800">{{ $fullName }}</p>
                                    <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                                </div>
                                <img src="{{ $profilePhoto }}" alt="Admin Avatar"
                                    class="w-10 h-10 rounded-full object-cover">
                            </button>

                            <!-- Profile Dropdown -->
                            <div x-show="profileOpen" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" @click.away="profileOpen = false"
                                class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-200">
                                    <p class="text-sm font-semibold text-gray-800">{{ $fullName }}</p>
                                    <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Role: {{ ucfirst($admin->role) }}</p>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('admin.profile.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>
                                        My Profile
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                        Settings
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-question-circle w-5 mr-3 text-gray-400"></i>
                                        Help & Support
                                    </a>
                                </div>
                                <div class="border-t border-gray-200 py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                            Logout
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle CSRF token refresh for logout form
            const adminLogoutForm = document.getElementById('admin-logout-form');

            if (adminLogoutForm) {
                adminLogoutForm.addEventListener('submit', function (e) {
                    // Update CSRF token from meta tag before submitting
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    const csrfInput = this.querySelector('input[name="_token"]');
                    if (csrfToken && csrfInput) {
                        csrfInput.value = csrfToken.getAttribute('content');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>