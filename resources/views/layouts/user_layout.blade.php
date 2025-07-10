<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AralSipnayan - Math Learning Platform')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        purple: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7c3aed',
                            800: '#6b21a8',
                            900: '#581c87',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-purple-900 via-blue-900 to-purple-800 min-h-screen">
    <!-- Header -->
    <header class="bg-gray-900/50 backdrop-blur-sm border-b border-white/10">
        <div class="flex items-center justify-between px-6 py-4">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg">AralSipnayan</h1>
                    <p class="text-gray-300 text-sm">Math Learning Platform</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-white hover:text-purple-300 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-300 hover:text-purple-300 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span>My Courses</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-300 hover:text-purple-300 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7h-3V2h-2v2H8V2H6v2H3v2h18V4zm0 4H3v12h18V8z"/>
                    </svg>
                    <span>Assessments</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-300 hover:text-purple-300 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                    </svg>
                    <span>Leaderboard</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-300 hover:text-purple-300 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span>Achievements</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-gray-300 hover:text-purple-300 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
                    </svg>
                    <span>Progress</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="flex items-center space-x-4">
                <div class="bg-yellow-500 text-black px-3 py-1 rounded-full text-sm font-semibold">
                    Lvl 5
                </div>
                <div class="text-right">
                    <div class="text-white font-semibold">1250 XP</div>
                    <div class="text-gray-300 text-sm">Total Points</div>
                </div>
                <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900/30 backdrop-blur-sm border-r border-white/10 min-h-screen p-6">
            <!-- User Profile -->
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">👤</span>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">{{ auth()->user()->name ?? 'Juan Dela Cruz' }}</h3>
                        <div class="bg-yellow-500 text-black px-2 py-1 rounded text-xs font-semibold inline-block">
                            Lvl 5
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="bg-blue-500/20 backdrop-blur-sm rounded-lg p-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-300">XP Progress</span>
                            <span class="text-white">50/100</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full" style="width: 50%"></div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-500/20 backdrop-blur-sm rounded-lg p-3 flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <span class="text-black font-bold">⭐</span>
                        </div>
                        <div>
                            <div class="text-white font-semibold">1250</div>
                            <div class="text-gray-300 text-sm">Total Points</div>
                        </div>
                    </div>
                    
                    <div class="bg-green-500/20 backdrop-blur-sm rounded-lg p-3 flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">📚</span>
                        </div>
                        <div>
                            <div class="text-white font-semibold">2</div>
                            <div class="text-gray-300 text-sm">Lessons Done</div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-500/20 backdrop-blur-sm rounded-lg p-3 flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">🏆</span>
                        </div>
                        <div>
                            <div class="text-white font-semibold">15</div>
                            <div class="text-gray-300 text-sm">Achievements</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Add any JavaScript functionality here
        document.addEventListener('DOMContentLoaded', function() {
            // Example: Add click animations to cards
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>