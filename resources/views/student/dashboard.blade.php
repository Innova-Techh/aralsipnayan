@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
    <style>
        .welcome-header {
            background: linear-gradient(135deg, #4338CA, #1E40AF, #3B82F6);
            background-size: 200% 200%;
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 150%, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 50%);
        }

        .welcome-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 80% -50%, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 50%);
        }

        .floating-circles div {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
        }

        .floating-circles div:nth-child(1) {
            width: 200px;
            height: 200px;
            left: -100px;
            top: -100px;
            animation-delay: -3s;
        }

        .floating-circles div:nth-child(2) {
            width: 180px;
            height: 180px;
            right: -90px;
            bottom: -90px;
            animation-delay: -5s;
        }

        .floating-circles div:nth-child(3) {
            width: 150px;
            height: 150px;
            left: 40%;
            bottom: -75px;
            animation-delay: -7s;
        }

        @media (min-width: 640px) {
            .floating-circles div:nth-child(1) {
                width: 250px;
                height: 250px;
                left: -125px;
                top: -125px;
            }

            .floating-circles div:nth-child(2) {
                width: 220px;
                height: 220px;
                right: -110px;
                bottom: -110px;
            }

            .floating-circles div:nth-child(3) {
                width: 180px;
                height: 180px;
                left: 40%;
                bottom: -90px;
            }
        }

        @media (min-width: 1024px) {
            .floating-circles div:nth-child(1) {
                width: 300px;
                height: 300px;
                left: -150px;
                top: -150px;
            }

            .floating-circles div:nth-child(2) {
                width: 250px;
                height: 250px;
                right: -125px;
                bottom: -125px;
            }

            .floating-circles div:nth-child(3) {
                width: 200px;
                height: 200px;
                left: 40%;
                bottom: -100px;
            }
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            25% {
                transform: translate(10px, -10px);
            }

            50% {
                transform: translate(-5px, 5px);
            }

            75% {
                transform: translate(-10px, 10px);
            }
        }

        /* Login Streak Modal Styles */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @keyframes flame {

            0%,
            100% {
                transform: scale(1) rotate(-1deg);
            }

            25% {
                transform: scale(1.05) rotate(1deg);
            }

            50% {
                transform: scale(1.02) rotate(-0.5deg);
            }

            75% {
                transform: scale(1.03) rotate(0.5deg);
            }
        }

        .modal-overlay {
            animation: fadeIn 0.3s ease-out;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            animation: slideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .flame-icon {
            animation: flame 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(255, 107, 53, 0.3));
        }

        /* Custom button hover effect */
        #continueButton:hover {
            box-shadow: 0 8px 25px rgba(251, 146, 60, 0.4);
        }

        /* Completed Card - 12 Book icons scattered */
        .stats-green::before {
            content: '📖 📚 📄 📝 📗 📘 📙 📕 📋 📜 📰 📑';
            position: absolute;
            top: -10px;
            left: -5px;
            right: -5px;
            bottom: -10px;
            font-size: 8px;
            opacity: 0.1;
            z-index: 1;
            word-spacing: 15px;
            line-height: 20px;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
        }

        .stats-green .bg-pattern::before {
            content: '📖';
            position: absolute;
            top: 5px;
            left: 8px;
            font-size: 9px;
            opacity: 0.12;
            z-index: 1;
            animation: float 6s ease-in-out infinite 1s;
        }

        .stats-green .bg-pattern::after {
            content: '📚';
            position: absolute;
            top: 20px;
            right: 12px;
            font-size: 7px;
            opacity: 0.08;
            z-index: 1;
            animation: float 6s ease-in-out infinite 3s;
        }

        /* Points Card - 12 Trophy and achievement icons */
        .bg-stats-yellow::before {
            content: '🏆 🥇 🏅 ⭐ 🌟 ✨ 🎖️ 🏵️ 👑 💎 🔥 💫';
            position: absolute;
            top: -10px;
            left: -5px;
            right: -5px;
            bottom: -10px;
            font-size: 8px;
            opacity: 0.1;
            z-index: 1;
            word-spacing: 12px;
            line-height: 18px;
            animation: float 9s ease-in-out infinite;
            pointer-events: none;
        }

        .bg-stats-yellow .bg-pattern::before {
            content: '🏆';
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 10px;
            opacity: 0.15;
            z-index: 1;
            animation: float 7s ease-in-out infinite 2s;
        }

        .bg-stats-yellow .bg-pattern::after {
            content: '🥇';
            position: absolute;
            bottom: 12px;
            left: 10px;
            font-size: 8px;
            opacity: 0.12;
            z-index: 1;
            animation: float 7s ease-in-out infinite 4s;
        }

        /* Streak Card - 12 Fire and energy icons */
        .bg-stats-red::before {
            content: '🔥 💥 ⚡ 💢 💨 🌟 ✨ 💫 ⭐ 🎯 🚀 💪';
            position: absolute;
            top: -10px;
            left: -5px;
            right: -5px;
            bottom: -10px;
            font-size: 8px;
            opacity: 0.12;
            z-index: 1;
            word-spacing: 10px;
            line-height: 16px;
            animation: float 6s ease-in-out infinite;
            pointer-events: none;
        }

        .bg-stats-red .bg-pattern::before {
            content: '🔥';
            position: absolute;
            top: 6px;
            left: 6px;
            font-size: 11px;
            opacity: 0.18;
            z-index: 1;
            animation: float 5s ease-in-out infinite 1.5s;
        }

        .bg-stats-red .bg-pattern::after {
            content: '⚡';
            position: absolute;
            top: 25px;
            right: 10px;
            font-size: 9px;
            opacity: 0.14;
            z-index: 1;
            animation: float 5s ease-in-out infinite 3.5s;
        }

        /* Level Card - 12 Star icons scattered */
        .bg-stats-blue::before {
            content: '⭐ ✨ 🌟 💫 ⚡ 🎆 🎇 ✴️ 💥 🔆 ⭐ 🌠';
            position: absolute;
            top: -10px;
            left: -5px;
            right: -5px;
            bottom: -10px;
            font-size: 8px;
            opacity: 0.1;
            z-index: 1;
            word-spacing: 13px;
            line-height: 19px;
            animation: float 10s ease-in-out infinite;
            pointer-events: none;
        }

        .bg-stats-blue .bg-pattern::before {
            content: '⭐';
            position: absolute;
            top: 4px;
            right: 6px;
            font-size: 12px;
            opacity: 0.16;
            z-index: 1;
            animation: float 8s ease-in-out infinite 2.5s;
        }

        .bg-stats-blue .bg-pattern::after {
            content: '✨';
            position: absolute;
            bottom: 8px;
            left: 8px;
            font-size: 10px;
            opacity: 0.13;
            z-index: 1;
            animation: float 8s ease-in-out infinite 5s;
        }

        /* Enhanced floating animation for more dynamic movement */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) translateX(0px) rotate(0deg);
                opacity: 0.1;
            }

            16% {
                transform: translateY(-2px) translateX(1px) rotate(2deg);
                opacity: 0.15;
            }

            33% {
                transform: translateY(-4px) translateX(-1px) rotate(-1deg);
                opacity: 0.08;
            }

            50% {
                transform: translateY(-6px) translateX(2px) rotate(3deg);
                opacity: 0.12;
            }

            66% {
                transform: translateY(-4px) translateX(-2px) rotate(-2deg);
                opacity: 0.18;
            }

            83% {
                transform: translateY(-2px) translateX(1px) rotate(1deg);
                opacity: 0.06;
            }
        }

        /* Content should be above background */
        .stats-content {
            position: relative;
            z-index: 10;
        }
    </style>

    <!-- Welcome Header (Hero) - Fixed margins and width -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
        <div
            class="welcome-header relative -mx-4 sm:-mx-6 lg:-mx-8  text-white overflow-hidden min-h-[160px] sm:min-h-[180px] lg:min-h-[180px]">
            <div class="relative z-10 px-4 sm:px-6 lg:px-8 py-4 sm:py-6 max-w-8xl mx-auto">
                <h1 class="text-lg xs:text-lg sm:text-xl md:text-3xl lg:text-4xl xl:text-5xl font-baloo font-extrabold leading-tight tracking-tight"
                    style="text-shadow: -1px -1px 0 #18337e, 1px -1px 0 #18337e, -1px 1px 0 #18337e, 1px 1px 0 #18337e, 0 4px 0 #18337e;">
                    Welcome back, {{ Auth::guard('student')->user()?->studentProfile?->fullname }}! 👋
                </h1>
                <p class="text-sm sm:text-base md:text-lg lg:text-xl text-blue-100 mt-2 sm:mt-3 lg:mt-4">Ready to
                    continue your math journey?</p>
            </div>
            <div class="floating-circles absolute inset-0">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

        <!-- Main content with proper top margin -->
        <div class="mt-6 sm:mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-12 xl:gap-24">
                <!-- Left Column - Progress and Continue Learning -->
                <div class="lg:col-span-2 space-y-8 space-x-8">
                    <!-- Learning Progress -->
                    <div class="rounded-xl p-4 mx-6 sm:p-6">
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <div class="flex items-center">
                                <div
                                    class="w-7 h-7 sm:w-8 sm:h-8  rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="lg:text-3xl sm:text-2xl font-bold text-gray-900">Your Learning Progress</h2>
                                    <h4 class="text-xs sm:text-sm text-gray-900 mt-1">You're growing into a math master
                                        every day!</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Level Card -->
                        <div class="mb-5 sm:mb-6">
                            <div class=" text-white rounded-xl p-4 sm:p-5 shadow-inner"
                                style="background: linear-gradient(to right, #101093, #931093); box-shadow: inset 0 -4px 4px #42045C, inset 0 2px 2px #CC39F6; box-shadow: 0 6px 0 #0A0A62;">
                                <div class="flex items-center gap-4 -mx-4">
                                    <!-- Rank image - fixed size for consistency -->
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('images/dashboard/rank.png') }}" alt="rank"
                                            class="w-32 h-32 sm:w-18 sm:h-18 rounded-xl object-contain">
                                    </div>

                                    <!-- Content area -->
                                    <div class="flex-1 min-w-0 -mx-4 mr-2">
                                        <!-- Group 1: Title and Level -->
                                        <div class="mb-2">
                                            <h3 class="text-xl sm:text-xl font-bold">Problem Solver</h3>
                                            <p class="text-sm text-blue-200">Level 3</p>
                                        </div>

                                        <!-- Group 2: XP Text (standalone) -->
                                        <div class="mb-2  mr-4  text-right">
                                            <p class="text-xs sm:text-sm text-blue-200">460 XP / 1000 XP</p>
                                        </div>

                                        <!-- Group 3: Progress bar and XP remaining -->
                                        <div class="mr-4">
                                            <div class="mb-1">
                                                <div class="relative h-2 sm:h-2.5 bg-white/20 rounded-full overflow-hidden">
                                                    <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-yellow-400 to-orange-500 transition-all duration-300 rounded-full"
                                                        style="width: 46%"></div>
                                                </div>
                                            </div>
                                            <p class="text-xs sm:text-sm text-blue-200">540 XP remaining</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Grid with Live Data -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                            <div
                                class="stats-green relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-green drop-shadow-stats-green">
                                <div class="flex items-center justify-center mb-2">
                                    <img src="{{ asset('images/dashboard/book.png') }}" alt="Completed"
                                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                                </div>
                                <div class="text-xl sm:text-2xl font-bold relative z-10">2</div>
                                <div class="text-xs sm:text-sm opacity-90 relative z-10">Completed</div>
                            </div>

                            <div
                                class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-yellow drop-shadow-stats-yellow">
                                <div class="flex items-center justify-center mb-2">
                                    <img src="{{ asset('images/dashboard/points.png') }}" alt="Points"
                                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                                </div>
                                <div class="text-xl sm:text-2xl font-bold relative z-10" id="dashboardPoints">
                                    {{ $profile?->total_points ?? 0 }}
                                </div>
                                <div class="text-xs sm:text-sm opacity-90 relative z-10">Points</div>
                            </div>

                            <div
                                class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-red drop-shadow-stats-red">
                                <div class="flex items-center justify-center mb-2">
                                    <img src="{{ asset('images/dashboard/streak.png') }}" alt="Streak"
                                        class="w-9 h-9 sm:w-11 sm:h-11 object-contain relative z-10">
                                </div>
                                <div class="text-xl sm:text-2xl font-bold relative z-10" id="dashboardStreak">
                                    {{ $profile?->current_streak ?? 0 }}
                                </div>
                                <div class="text-xs sm:text-sm opacity-90 relative z-10">Streak</div>
                            </div>

                            <div
                                class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-blue drop-shadow-stats-blue">
                                <div class="flex items-center justify-center mb-2">
                                    <img src="{{ asset('images/dashboard/star.png') }}" alt="Level"
                                        class="w-12 h-8 sm:w-14 sm:h-10 object-contain relative z-10">
                                </div>
                                <div class="text-xl sm:text-2xl font-bold relative z-10">3</div>
                                <div class="text-xs sm:text-sm opacity-90 relative z-10">Level</div>
                            </div>
                        </div>

                        <!-- Assigned Assessments -->
                        <div class="rounded-xl overflow-hidden mt-8">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 py-4  text-white"
                                style="background-color: #B91E2A;">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-red-600 text-xl">🎯</span>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-baloo font-bold">Assigned Assessments</h2>
                                        <p class="text-sm opacity-90">Complete your assigned tasks</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl text-center font-extrabold">2</div>
                                    <div class="text-sm opacity-90">Pending</div>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="p-6 space-y-4" style="background-color: #FFEAEA;">
                                <!-- Assessment Item -->
                                <div
                                    class="flex flex-col sm:flex-row sm:items-start sm:justify-between rounded-lg p-4 shadow">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-semibold text-gray-900 text-base">Evaluate Exponents</h3>
                                            <span
                                                class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">Individual</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">
                                            Learn how to calculate and evaluate expressions with exponents
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-xs px-2 py-1 rounded-full bg-yellow-400 text-white font-medium">
                                                120 points
                                            </span>
                                            <span
                                                class="text-xs px-2 py-1 text-pink-700 font-medium flex items-center gap-1">
                                                <span class="text-pink-500 font-bold">ⓘ</span> Hard
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        class="mt-4 sm:mt-0 sm:ml-4 px-6 py-2 rounded-xl text-white font-semibold shadow transition-all hover:shadow-md"
                                        style="background: linear-gradient(180deg, #4338CA 0%, #9333EA 100%);  box-shadow: 0 6px 0 #0F172A;">
                                        Start Assessment
                                    </button>
                                </div>

                                <!-- Assessment Item 2 -->
                                <div
                                    class="flex flex-col sm:flex-row sm:items-start sm:justify-between rounded-lg p-4 shadow">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-semibold text-gray-900 text-base">Evaluate Exponents</h3>
                                            <span
                                                class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">Class</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">
                                            Learn how to calculate and evaluate expressions with exponents
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-xs px-2 py-1 rounded-full bg-yellow-400 text-white font-medium">
                                                120 points
                                            </span>
                                            <span
                                                class="text-xs px-2 py-1 text-green-700 font-medium flex items-center gap-1">
                                                <span class="text-green-500 font-bold">ⓘ</span> Easy
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        class="mt-4 sm:mt-0 sm:ml-4 px-6 py-2 rounded-xl text-white font-semibold shadow transition-all hover:shadow-md"
                                        style="background: linear-gradient(180deg, #4338CA 0%, #9333EA 100%); box-shadow: 0 6px 0 #0F172A; ">
                                        Start Assessment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8 space-x-8 lg:space-y-6 lg:pt-[6.5rem]">
                    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Achievements Section -->
                        <div class="rounded-xl overflow-hidden shadow-md w-full">
                            <!-- Header -->
                            <div class="px-6 py-4"
                                style="background: linear-gradient(to right, #3B82F6, #2563EB, #1D4ED8);">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-4xl sm:text-xl font-poppins font-bold text-white">Recent
                                            Achievements
                                        </h2>
                                        <p class="text-sm text-blue-100 opacity-90">Recent acquired achievements</p>
                                    </div>
                                    <a href="{{ route('achievements.index') }}"
                                        class="px-4 py-2 transition-colors duration-200 rounded-xl text-white text-sm font-medium"
                                        style="background: linear-gradient(180deg, #F6510C 0%, #F5D70B 100%);  box-shadow: 0 4px 0 #7A4305;  text-shadow:
                                                -1px -1px 0 #7A4305,
                                                1px -1px 0 #7A4305,
                                                -1px 1px 0 #7A4305,
                                                1px 1px 0 #7A4305,
                                                0 0 1px #7A4305;">
                                        View All
                                    </a>
                                </div>
                            </div>

                            <!-- Achievements Body -->
                            <div class="p-6"
                                style="background: linear-gradient(135deg, #312E81 0%, #701FB7 50%, #1E1B4B 100%);">
                                <div class="grid grid-cols-3 gap-6">
                                    @foreach($recentAchievements as $achievement)
                                        <div
                                            class="text-center p-2 group cursor-pointer transition-transform duration-300 hover:-translate-y-2">
                                            <!-- Container for overlapping circles -->
                                            <div class="relative w-16 h-16 sm:w-18 sm:h-18 mx-auto mb-3">
                                                <!-- Dark background circle (larger, positioned behind) -->
                                                <div class="absolute inset-1 w-16 h-16 sm:w-18 sm:h-18 rounded-full shadow-lg"
                                                    style="background-color: {{ $achievement['background_dark'] }};"></div>
                                                <!-- Light foreground circle (smaller, positioned in front) -->
                                                <div class="absolute inset-0 w-16 h-16 sm:w-18 sm:h-18 rounded-full flex items-center justify-center shadow-md"
                                                    style="background-color: {{ $achievement['background_light'] }};">
                                                    <img src="{{ asset('images/achievements/' . $achievement['front_image']) }}"
                                                        alt="{{ $achievement['title'] }}"
                                                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain drop-shadow-sm">
                                                </div>
                                            </div>
                                            <div
                                                class="font-semibold text-white text-xs sm:text-sm md:text-base drop-shadow-sm leading-tight">
                                                {{ $achievement['title'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Leaderboard Section -->
                        <div class="mt-8 px-4">
                            <div class="max-w-3xl mx-auto rounded-xl overflow-hidden card-shadow">
                                <!-- Header -->
                                <div
                                    class="bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center px-6 py-4">
                                    <h1
                                        class="text-white text-2xl sm:text-3xl md:text-4xl font-poppins font-bold tracking-wide flex items-center">
                                        Leaderboards
                                        <svg class="w-6 h-6 text-yellow-300 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                        </svg>
                                    </h1>
                                </div>

                                <!-- Body -->
                                <div class="bg-gradient-to-b from-yellow-100 to-orange-50 p-6">
                                    <div class="space-y-3">
                                        @php
                                            $rankBadges = [
                                                1 => 'bg-gradient-to-b from-yellow-400 to-yellow-600 text-white',
                                                2 => 'bg-gradient-to-b from-gray-300 to-gray-500 text-white',
                                                3 => 'bg-gradient-to-b from-orange-500 to-orange-700 text-white',
                                                4 => 'bg-gradient-to-b from-blue-400 to-blue-600 text-white',
                                                5 => 'bg-gradient-to-b from-teal-400 to-teal-600 text-white',
                                            ];
                                            $rankLabels = [1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th', 5 => '5th'];
                                        @endphp

                                        @foreach(($leaderboardTop5 ?? []) as $index => $row)
                                            @php $rank = $index + 1; @endphp
                                            <div class="flex items-center justify-between p-3  rounded-lg shadow-sm">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center mr-3 border-2 {{ $rank === 1 ? 'bg-yellow-500 border-yellow-600' : ($rank === 2 ? 'bg-gray-400 border-gray-500' : ($rank === 3 ? 'bg-orange-600 border-orange-700' : ($rank === 4 ? 'bg-blue-400 border-blue-500' : 'bg-teal-400 border-teal-500'))) }}">
                                                        <img src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}"
                                                            alt="{{ $row['name'] ?? 'Student' }}"
                                                            class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <div
                                                            class="font-baloo font-bold text-gray-900 text-base sm:text-lg md:text-xl">
                                                            {{ $row['name'] ?? 'Student' }}
                                                        </div>
                                                        <div class="text-sm sm:text-base text-gray-600">
                                                            {{ $row['points'] ?? 0 }} pts
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="px-3 py-1 rounded-full text-sm font-bold shadow-sm {{ $rankBadges[$rank] ?? 'bg-gray-200 text-gray-800' }}">
                                                    {{ $rankLabels[$rank] ?? $rank . 'th' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Login Streak Modal -->
    <x-login-streak-modal />

    <!-- Test Button (Remove when done) -->
    <button onclick="showStreakModal({current_streak: 3, points_earned: 25, message: 'Keep it up!'})"
        class="fixed bottom-4 right-4 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-full shadow-lg z-50 font-medium">
        Test Modal
    </button>

    <script>
        // Modal functions
        function showStreakModal(data = null) {
            const modal = document.getElementById('loginStreakModal');

            if (data) {
                document.getElementById('streakDay').textContent = `Day ${data.current_streak}`;
                document.getElementById('pointsEarned').textContent = `${data.points_earned} points`;
                document.getElementById('streakMessage').textContent = data.message || "You're doing great! Keep the streak up!";
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeStreakModal() {
            const modal = document.getElementById('loginStreakModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Update dashboard stats after closing modal
            updateDashboardStats();
        }

        // Close modal when clicking outside
        document.getElementById('loginStreakModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeStreakModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeStreakModal();
            }
        });

        // Auto-check for login streak on page load
        document.addEventListener('DOMContentLoaded', function () {
            checkLoginStreak();
            updateDashboardStats();
        });

        function checkLoginStreak() {
            fetch('{{ route("login-streak.check") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.show_modal) {
                        showStreakModal(data.data);
                    }
                })
                .catch(error => {
                    console.error('Error checking login streak:', error);
                });
        }

        function updateDashboardStats() {
            fetch('{{ route("login-streak.data") }}')
                .then(response => response.json())
                .then(data => {
                    // Update the stats cards with live data
                    document.getElementById('dashboardStreak').textContent = data.current_streak;
                    document.getElementById('dashboardPoints').textContent = data.total_points;
                })
                .catch(error => {
                    console.error('Error updating dashboard stats:', error);
                });
        }
    </script>
@endsection