<!-- dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <href rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    @extends('layouts.user_layout')
    @section('title', 'AralSipnayan')


    @section('content')

        @php
            use App\Http\Controllers\RankController;

            // Initialize the RankController
            $rankController = new RankController();

            // Get user's current XP (replace with your actual user XP logic)
            $userXP = auth()->guard('student')->user()->xp ?? 460; // Example: 460 XP
            $progressInfo = $rankController->getProgressInfo($userXP);
        @endphp
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

            /* Skeleton Loading Animations */
            @keyframes shimmer {
                0% {
                    background-position: -200px 0;
                }

                100% {
                    background-position: calc(200px + 100%) 0;
                }
            }

            .skeleton {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200px 100%;
                animation: shimmer 1.5s infinite;
            }

            .skeleton-dark {
                background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 25%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.1) 75%);
                background-size: 200px 100%;
                animation: shimmer 1.5s infinite;
            }

            .skeleton-text {
                height: 1rem;
                border-radius: 0.25rem;
                margin-bottom: 0.5rem;
            }

            .skeleton-title {
                height: 1.5rem;
                border-radius: 0.25rem;
                margin-bottom: 0.75rem;
            }

            .skeleton-circle {
                border-radius: 50%;
            }

            .content-loaded {
                animation: fadeIn 0.5s ease-in-out;
            }
        </style>
        <href rel="stylesheet" href="css/dashboard.css">

            <!-- Welcome Header (Hero) - Fixed margins and width -->
            <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
                <!-- Loading Skeleton (Initially visible) -->
                <div id="dashboardSkeleton">
                    <!-- Welcome Header Skeleton -->
                    <div
                        class="welcome-header relative -mx-4 sm:-mx-6 lg:-mx-8 text-white overflow-hidden min-h-[160px] sm:min-h-[200px] lg:min-h-[220px] flex items-center">
                        <div class="relative z-10 w-full px-4 sm:px-8 lg:px-8 max-w-8xl mx-auto">
                            <div class="flex flex-col justify-center h-full">
                                <div class="skeleton-dark skeleton-title w-3/4 mb-4"></div>
                                <div class="skeleton-dark skeleton-text w-1/2"></div>
                            </div>
                        </div>
                        <div class="floating-circles absolute inset-0">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>

                    <!-- Main content skeleton -->
                    <div class="mt-6 sm:mt-8">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-12 xl:gap-24">
                            <!-- Left Column Skeleton -->
                            <div class="lg:col-span-2 space-y-8 space-x-8">
                                <div class="rounded-xl p-4 lg:mx-6 sm:p-6">
                                    <!-- Progress section skeleton -->
                                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 skeleton rounded-lg mr-2 sm:mr-3"></div>
                                            <div>
                                                <div class="skeleton skeleton-title w-48 mb-2"></div>
                                                <div class="skeleton skeleton-text w-32"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Level card skeleton -->
                                    <div class="mb-5 sm:mb-6">
                                        <div class="rounded-xl p-4 sm:p-5"
                                            style="background: linear-gradient(to right, #101093, #931093);">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-32 h-32 sm:w-18 sm:h-18 skeleton-dark rounded-xl"></div>
                                                </div>
                                                <div class="flex-1 min-w-0 mr-2">
                                                    <div class="mb-2">
                                                        <div class="skeleton-dark skeleton-title w-40 mb-2"></div>
                                                        <div class="skeleton-dark skeleton-text w-20"></div>
                                                    </div>
                                                    <div class="mb-2 mr-4">
                                                        <div class="skeleton-dark skeleton-text w-24"></div>
                                                    </div>
                                                    <div class="mr-4">
                                                        <div class="skeleton-dark h-3 sm:h-4 rounded-full mb-2"></div>
                                                        <div class="skeleton-dark skeleton-text w-32"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stats grid skeleton -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
                                        <div class="rounded-2xl p-3 sm:p-4 text-center bg-gray-200">
                                            <div class="flex items-center justify-center mb-2">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 skeleton-circle skeleton"></div>
                                            </div>
                                            <div class="skeleton skeleton-title w-8 mx-auto mb-2"></div>
                                            <div class="skeleton skeleton-text w-16 mx-auto"></div>
                                        </div>
                                        <!-- Repeat for other 3 stats cards -->
                                        <div class="rounded-2xl p-3 sm:p-4 text-center bg-gray-200">
                                            <div class="flex items-center justify-center mb-2">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 skeleton-circle skeleton"></div>
                                            </div>
                                            <div class="skeleton skeleton-title w-8 mx-auto mb-2"></div>
                                            <div class="skeleton skeleton-text w-16 mx-auto"></div>
                                        </div>
                                        <div class="rounded-2xl p-3 sm:p-4 text-center bg-gray-200">
                                            <div class="flex items-center justify-center mb-2">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 skeleton-circle skeleton"></div>
                                            </div>
                                            <div class="skeleton skeleton-title w-8 mx-auto mb-2"></div>
                                            <div class="skeleton skeleton-text w-16 mx-auto"></div>
                                        </div>
                                        <div class="rounded-2xl p-3 sm:p-4 text-center bg-gray-200">
                                            <div class="flex items-center justify-center mb-2">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 skeleton-circle skeleton"></div>
                                            </div>
                                            <div class="skeleton skeleton-title w-8 mx-auto mb-2"></div>
                                            <div class="skeleton skeleton-text w-16 mx-auto"></div>
                                        </div>
                                    </div>

                                    <!-- Assignments skeleton -->
                                    <div class="rounded-xl overflow-hidden">
                                        <div class="flex items-center justify-between px-6 py-4 bg-gray-300">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 skeleton-circle skeleton"></div>
                                                <div>
                                                    <div class="skeleton skeleton-title w-32 mb-2"></div>
                                                    <div class="skeleton skeleton-text w-24"></div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="skeleton skeleton-title w-8 mb-1"></div>
                                                <div class="skeleton skeleton-text w-12"></div>
                                            </div>
                                        </div>
                                        <div class="p-6 space-y-4 bg-gray-100">
                                            <!-- Assignment items skeleton -->
                                            <div class="rounded-lg p-4 bg-white">
                                                <div class="skeleton skeleton-title w-48 mb-2"></div>
                                                <div class="skeleton skeleton-text w-full mb-3"></div>
                                                <div class="flex items-center gap-2 justify-between">
                                                    <div class="flex gap-2">
                                                        <div class="skeleton skeleton-text w-16"></div>
                                                        <div class="skeleton skeleton-text w-12"></div>
                                                    </div>
                                                    <div class="skeleton h-10 w-32 rounded-xl"></div>
                                                </div>
                                            </div>
                                            <!-- Repeat for 2 more assessment items -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column Skeleton -->
                            <div class="space-y-6 lg:space-y-8 lg:pt-[6.5rem] mx-4">
                                <!-- Achievements skeleton -->
                                <div class="rounded-xl overflow-hidden shadow-md">
                                    <div class="px-4 sm:px-6 py-3 sm:py-4 bg-blue-600">
                                        <div class="flex items-center justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="skeleton-dark skeleton-title w-48 mb-2"></div>
                                                <div class="skeleton-dark skeleton-text w-32"></div>
                                            </div>
                                            <div class="skeleton-dark h-8 w-20 rounded-xl"></div>
                                        </div>
                                    </div>
                                    <div class="p-4 sm:p-6 bg-purple-900">
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                                            <div class="text-center p-2 sm:p-3 lg:p-4">
                                                <div
                                                    class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 mx-auto mb-2 sm:mb-3 skeleton-circle skeleton-dark">
                                                </div>
                                                <div class="skeleton-dark skeleton-text w-20 mx-auto"></div>
                                            </div>
                                            <!-- Repeat for 2 more achievement items -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Leaderboard skeleton -->
                                <div class="rounded-xl overflow-hidden shadow-md">
                                    <div class="bg-orange-600 px-4 sm:px-6 py-3 sm:py-4">
                                        <div class="skeleton-dark skeleton-title w-32 mb-2"></div>
                                        <div class="skeleton-dark skeleton-text w-24"></div>
                                    </div>
                                    <div class="bg-yellow-100 p-4 sm:p-6">
                                        <div class="space-y-2 sm:space-y-3">
                                            <!-- Repeat this 5 times for leaderboard items -->
                                            <div class="flex items-center justify-between p-2 sm:p-3 rounded-lg bg-white">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 sm:w-10 sm:h-10 skeleton-circle skeleton mr-2 sm:mr-3">
                                                    </div>
                                                    <div>
                                                        <div class="skeleton skeleton-text w-24 mb-1"></div>
                                                        <div class="skeleton skeleton-text w-16"></div>
                                                    </div>
                                                </div>
                                                <div class="skeleton skeleton-text w-12"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="dashboardContent" class="hidden">
                    <div
                        class="welcome-header relative -mx-4 sm:-mx-6 lg:-mx-8 text-white overflow-hidden min-h-[160px] sm:min-h-[200px] lg:min-h-[220px] flex items-center">
                        <div class="relative z-10 w-full px-4 sm:px-8 lg:px-8 max-w-8xl mx-auto">
                            <div class="flex flex-col justify-center h-full">
                                <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-5xl font-baloo font-extrabold leading-tight tracking-tight"
                                    style="text-shadow: -1px -1px 0 #18337e,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               1px -1px 0 #18337e,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               -1px 1px 0 #18337e,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               1px 1px 0 #18337e,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               0 4px 0 #18337e;">
                                    Welcome back, {{ Auth::guard('student')->user()?->studentProfile?->fullname }}! 👋
                                </h1>
                                <p class="text-base sm:text-lg md:text-xl lg:text-xl text-blue-100 mt-3 sm:mt-4 lg:mt-5">
                                    Ready to continue your math journey?
                                </p>
                            </div>
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
                                <div class="rounded-xl p-4 lg:mx-6 sm:p-6">
                                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                                        <div class="flex items-center">
                                            <div
                                                class="w-7 h-7 sm:w-8 sm:h-8  rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="lg:text-3xl sm:text-2xl font-bold text-gray-900">Your Learning
                                                    Progress
                                                </h2>
                                                <h4 class="text-xs sm:text-sm text-gray-900 mt-1">You're growing into a math
                                                    master
                                                    every day!</h3>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Dynamic Level Card -->
                                    <div class="mb-5 sm:mb-6">
                                        <div class="text-white rounded-xl p-4 sm:p-5 shadow-inner"
                                            style="background: linear-gradient(to right, #101093, #931093); box-shadow: inset 0 -4px 4px #42045C, inset 0 2px 2px #CC39F6; box-shadow: 0 6px 0 #0A0A62;">
                                            <div class="flex items-center gap-4">
                                                <!-- Dynamic Rank image -->
                                                <div class="flex-shrink-0">
                                                    <img src="{{ asset('images/rank_insignia/' . $progressInfo['rank_info']['image']) }}"
                                                        alt="{{ $progressInfo['rank_info']['title'] }}"
                                                        class="w-32 h-32 sm:w-18 sm:h-18 rounded-xl object-contain">
                                                </div>

                                                <!-- Content area -->
                                                <div class="flex-1 min-w-0 mr-2">
                                                    <!-- Group 1: Title and Level -->
                                                    <div class="mb-2">
                                                        <h3 class="text-xl sm:text-xl font-bold">
                                                            {{ $progressInfo['rank_info']['title'] }}
                                                        </h3>
                                                        <p class="text-sm text-blue-200">Level
                                                            {{ $progressInfo['current_level'] }}
                                                        </p>
                                                    </div>

                                                    <!-- Group 2: XP Text (standalone) -->
                                                    <div class="mb-2 mr-4 text-right">
                                                        @if($progressInfo['is_max_level'])
                                                            <p class="text-xs sm:text-sm text-yellow-300 font-bold">MAX LEVEL
                                                                ACHIEVED!
                                                            </p>
                                                        @else
                                                            <p class="text-xs sm:text-sm text-blue-200">
                                                                {{ number_format($progressInfo['current_xp']) }} XP /
                                                                {{ number_format($progressInfo['rank_info']['xp_required']) }}
                                                                XP
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <!-- Group 3: Custom Progress bar and XP remaining -->
                                                    <div class="mr-4">
                                                        <div class="mb-1">
                                                            {{-- Custom Level Progress Bar with Handle --}}
                                                            <div class="relative">
                                                                <div
                                                                    class="level-progress-track rounded-full h-3 sm:h-4 relative overflow-visible">
                                                                    <div class="level-progress-fill h-3 sm:h-4 rounded-full transition-all duration-500 ease-out relative overflow-visible"
                                                                        style="width: {{ $progressInfo['progress_percentage'] }}%">
                                                                        {{-- Progress Handle/Thumb for Level --}}
                                                                        <div class="level-progress-handle"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($progressInfo['is_max_level'])
                                                            <p class="text-xs sm:text-sm text-yellow-300">🏆 Grandmaster Status
                                                            </p>
                                                        @else
                                                            <p class="text-xs sm:text-sm text-blue-200">
                                                                {{ number_format($progressInfo['xp_remaining']) }} XP remaining
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Single Test Button that opens a panel -->
                                    <div class="fixed bottom-14 right-4 z-50">
                                        <!-- Main Test Button -->
                                        {{-- <button onclick="toggleTestPanel()" id="testPanelBtn"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-full shadow-lg font-medium transition-all duration-200">
                                            🧪 Level Tests
                                        </button> --}}

                                        <!-- Test Panel (Hidden by default) -->
                                        <div id="testPanel"
                                            class="hidden absolute bottom-16 right-0 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 min-w-[250px] transform transition-all duration-300">

                                            <!-- Panel Header -->
                                            <div
                                                class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                                                <h3 class="font-semibold text-gray-800 text-sm">Level-Up Modal Tests</h3>
                                                <button onclick="toggleTestPanel()"
                                                    class="text-gray-400 hover:text-gray-600 text-lg">×</button>
                                            </div>

                                            <!-- Tier Test Buttons -->
                                            <div class="space-y-2">
                                                <!-- Bronze Tier -->
                                                <button onclick="testTierModal('bronze')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-orange-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-bronze-badge border-2 border-level-bronze-stroke flex-shrink-0 drop-shadow-level-bronze-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Bronze Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 1-10</div>
                                                    </div>
                                                </button>

                                                <!-- Silver Tier -->
                                                <button onclick="testTierModal('silver')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-silver-badge border-2 border-level-silver-stroke flex-shrink-0 drop-shadow-level-silver-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Silver Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 11-20</div>
                                                    </div>
                                                </button>

                                                <!-- Gold Tier -->
                                                <button onclick="testTierModal('gold')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-yellow-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-gold-badge border-2 border-level-gold-stroke flex-shrink-0 drop-shadow-level-gold-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Gold Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 21-30</div>
                                                    </div>
                                                </button>

                                                <!-- Topaz Tier -->
                                                <button onclick="testTierModal('topaz')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-orange-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-topaz-badge border-2 border-level-topaz-badge-stroke flex-shrink-0 drop-shadow-level-topaz-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Topaz Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 31-40</div>
                                                    </div>
                                                </button>

                                                <!-- Emerald Tier -->
                                                <button onclick="testTierModal('emerald')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-emerald-badge border-2 border-level-emerald-stroke flex-shrink-0 drop-shadow-level-emerald-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Emerald Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 41-50</div>
                                                    </div>
                                                </button>

                                                <!-- Ruby Tier -->
                                                <button onclick="testTierModal('ruby')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-ruby-badge border-2 border-level-ruby-stroke flex-shrink-0 drop-shadow-level-ruby-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Ruby Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 51-60</div>
                                                    </div>
                                                </button>

                                                <!-- Amethyst Tier -->
                                                <button onclick="testTierModal('amethyst')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-purple-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-amethyst-badge border-2 border-level-amethyst-stroke flex-shrink-0 drop-shadow-level-amethyst-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Amethyst Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 61-70</div>
                                                    </div>
                                                </button>

                                                <!-- Tanzite Tier -->
                                                <button onclick="testTierModal('tanzite')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-sapphire-badge border-2 border-level-sapphire-stroke flex-shrink-0 drop-shadow-level-sapphire-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Tanzite Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 71-80</div>
                                                    </div>
                                                </button>

                                                <!-- Sapphire Tier -->
                                                <button onclick="testTierModal('sapphire')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-blue-sapphire-badge border-2 border-level-blue-sapphire-stroke flex-shrink-0 drop-shadow-level-blue-sapphire-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Sapphire Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 81-90</div>
                                                    </div>
                                                </button>

                                                <!-- Prismatic Tier -->
                                                <button onclick="testTierModal('prismatic')"
                                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-purple-50 transition-colors group">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-level-prismatic-badge border-2 border-level-prismatic-stroke flex-shrink-0 drop-shadow-level-prismatic-badge">
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-medium text-gray-800 text-sm">Prismatic Tier</div>
                                                        <div class="text-xs text-gray-500">Levels 91-100</div>
                                                    </div>
                                                </button>
                                            </div>

                                            <!-- Separator -->
                                            <hr class="my-3 border-gray-100">

                                            <!-- Dashboard Test -->
                                            <button onclick="updateDashboardLevel()"
                                                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-50 transition-colors">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-gradient-to-r from-green-400 to-green-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                    📊
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-medium text-gray-800 text-sm">Update Level Card</div>
                                                    <div class="text-xs text-gray-500">Test content changes</div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <script>
                                        let testPanelOpen = false;

                                        function toggleTestPanel() {
                                            const panel = document.getElementById('testPanel');
                                            const btn = document.getElementById('testPanelBtn');

                                            if (testPanelOpen) {
                                                panel.classList.add('hidden');
                                                panel.style.transform = 'scale(0.95) translateY(10px)';
                                                panel.style.opacity = '0';
                                                btn.style.transform = 'rotate(0deg)';
                                                testPanelOpen = false;
                                            } else {
                                                panel.classList.remove('hidden');
                                                setTimeout(() => {
                                                    panel.style.transform = 'scale(1) translateY(0)';
                                                    panel.style.opacity = '1';
                                                }, 10);
                                                btn.style.transform = 'rotate(180deg)';
                                                testPanelOpen = true;
                                            }
                                        }

                                        function testTierModal(tierName) {
                                            const tierData = {
                                                bronze: {
                                                    tier: 'bronze',
                                                    new_level: 10,
                                                    xp_gained: 150,
                                                    points_gained: 200,
                                                    rank_image: 'rank-1.png',
                                                    rank_title: 'Math Explorer',
                                                    message: 'A great journey begins with small steps!'
                                                },
                                                silver: {
                                                    tier: 'silver',
                                                    new_level: 20,
                                                    xp_gained: 300,
                                                    points_gained: 400,
                                                    rank_image: 'rank-2.png',
                                                    rank_title: 'Math Adventurer',
                                                    message: 'Your skills are developing nicely!'
                                                },
                                                gold: {
                                                    tier: 'gold',
                                                    new_level: 30,
                                                    xp_gained: 500,
                                                    points_gained: 600,
                                                    rank_image: 'rank-3.png',
                                                    rank_title: 'Math Seeker',
                                                    message: 'You are showing great potential!'
                                                },
                                                topaz: {
                                                    tier: 'topaz',
                                                    new_level: 40,
                                                    xp_gained: 750,
                                                    points_gained: 800,
                                                    rank_image: 'rank-4.png',
                                                    rank_title: 'Math Strategist',
                                                    message: 'You are mastering advanced concepts!'
                                                },
                                                emerald: {
                                                    tier: 'emerald',
                                                    new_level: 50,
                                                    xp_gained: 1000,
                                                    points_gained: 1200,
                                                    rank_image: 'rank-5.png',
                                                    rank_title: 'Math Innovator',
                                                    message: 'You are reaching new mathematical heights!'
                                                },
                                                ruby: {
                                                    tier: 'ruby',
                                                    new_level: 60,
                                                    xp_gained: 1250,
                                                    points_gained: 1500,
                                                    rank_image: 'rank-6.png',
                                                    rank_title: 'Math Prodigy',
                                                    message: 'Your mathematical prowess is extraordinary!'
                                                },
                                                amethyst: {
                                                    tier: 'amethyst',
                                                    new_level: 70,
                                                    xp_gained: 1500,
                                                    points_gained: 1800,
                                                    rank_image: 'rank-7.png',
                                                    rank_title: 'Math Virtuoso',
                                                    message: 'You have achieved mathematical excellence!'
                                                },
                                                tanzite: {
                                                    tier: 'tanzite',
                                                    new_level: 80,
                                                    xp_gained: 1750,
                                                    points_gained: 2000,
                                                    rank_image: 'rank-8.png',
                                                    rank_title: 'Math Sage',
                                                    message: 'You are becoming a mathematical virtuoso!'
                                                },
                                                sapphire: {
                                                    tier: 'sapphire',
                                                    new_level: 90,
                                                    xp_gained: 2000,
                                                    points_gained: 2200,
                                                    rank_image: 'rank-9.png',
                                                    rank_title: 'Math Champion',
                                                    message: 'You have reached the highest levels of mastery!'
                                                },
                                                prismatic: {
                                                    tier: 'prismatic',
                                                    new_level: 100,
                                                    xp_gained: 2500,
                                                    points_gained: 3000,
                                                    rank_image: 'rank-10.png',
                                                    rank_title: 'Math Grandmaster',
                                                    message: 'You have achieved the ultimate mathematical pinnacle!'
                                                }
                                            };

                                            // Close the panel
                                            toggleTestPanel();

                                            // Show the modal after a short delay
                                            setTimeout(() => {
                                                showLevelUpModal(tierData[tierName]);
                                            }, 200);
                                        }
                                        // Close panel when clicking outside
                                        document.addEventListener('click', function (event) {
                                            const panel = document.getElementById('testPanel');
                                            const btn = document.getElementById('testPanelBtn');

                                            if (testPanelOpen && !panel.contains(event.target) && !btn.contains(event.target)) {
                                                toggleTestPanel();
                                            }
                                        });

                                        // Close panel with Escape key
                                        document.addEventListener('keydown', function (event) {
                                            if (event.key === 'Escape' && testPanelOpen) {
                                                toggleTestPanel();
                                            }
                                        });
                                    </script>


                                    {{-- Optional: Next rank preview --}}
                                    @if(!$progressInfo['is_max_level'])
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 text-center">
                                                <span class="font-medium">Next Rank:</span>
                                                @php
                                                    $nextRankInfo = $rankController->getRankInfo($progressInfo['current_level'] + 1);
                                                @endphp
                                                {{ $nextRankInfo['title'] }} - {{ $nextRankInfo['description'] }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Stats Grid with Live Data -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                                        <div
                                            class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-green drop-shadow-stats-green">

                                            <!-- Books Pattern (scattered icons) -->
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-80 blur-[1px]">

                                            </div>

                                            <!-- Icon -->
                                            <div class="flex items-center justify-center mb-2 relative z-10">
                                                <img src="{{ asset('images/dashboard/book.png') }}" alt="Completed"
                                                    class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
                                            </div>

                                            <!-- Number -->
                                            <div class="text-xl sm:text-2xl font-bold relative z-10">2</div>

                                            <!-- Label -->
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
                                                <div
                                                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
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
                                            <!-- Assessment Item 1 -->
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between rounded-lg p-4 shadow relative">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h3 class="font-semibold text-gray-900 text-base">Evaluate Exponents
                                                        </h3>
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

                                                <!-- Button container with responsive positioning -->
                                                <div class="mt-4 lg:mt-0 lg:ml-6 lg:flex-shrink-0">
                                                    <button
                                                        class="w-full lg:w-auto px-8 lg:px-10 py-3 lg:py-3.5 rounded-xl text-sm text-white font-semibold bg-gradient-primary drop-shadow-gradient-primary shadow-inner-y-4-[#AF68FF] transition-all hover:shadow-md">
                                                        Start Assessment
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Assessment Item 2 -->
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between rounded-lg p-4 shadow relative">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h3 class="font-semibold text-gray-900 text-base">Evaluate Exponents
                                                        </h3>
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

                                                <!-- Button container with responsive positioning -->
                                                <div class="mt-4 lg:mt-0 lg:ml-6 lg:flex-shrink-0">
                                                    <button
                                                        class="w-full lg:w-auto px-8 lg:px-10 py-3 lg:py-3.5 rounded-xl text-sm text-white font-semibold bg-gradient-primary drop-shadow-gradient-primary shadow-inner-y-4-[#AF68FF] transition-all hover:shadow-md">
                                                        Start Assessment
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Assessment Item 3 -->
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between rounded-lg p-4 shadow relative">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <h3 class="font-semibold text-gray-900 text-base">Evaluate Exponents
                                                        </h3>
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

                                                <!-- Button container with responsive positioning -->
                                                <div class="mt-4 lg:mt-0 lg:ml-6 lg:flex-shrink-0">
                                                    <button
                                                        class="w-full lg:w-auto px-8 lg:px-10 py-3 lg:py-3.5 rounded-xl text-sm text-white font-semibold bg-gradient-primary drop-shadow-gradient-primary shadow-inner-y-4-[#AF68FF] transition-all hover:shadow-md">
                                                        Start Assessment
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Right Column -->
                            <div class="space-y-6 lg:space-y-8 lg:pt-[6.5rem] mx-4">
                                <!-- Achievements Section -->
                                <div class="rounded-xl overflow-hidden shadow-md ">
                                    <!-- Header -->
                                    <div class="px-4 sm:px-6 py-3 sm:py-4"
                                        style="background: linear-gradient(to right, #3B82F6, #2563EB, #1D4ED8);">
                                        <div class="flex items-center justify-between">
                                            <div class="min-w-0 flex-1">
                                                <h2
                                                    class="text-lg sm:text-xl lg:text-3xl font-baloo font-bold text-white truncate">
                                                    Recent Achievements
                                                </h2>
                                                <p class="text-xs sm:text-sm text-blue-100 opacity-90 mt-1">
                                                    Recent acquired achievements
                                                </p>
                                            </div>
                                            <a href="{{ route('achievements.index') }}"
                                                class="ml-3 px-3 py-1.5 sm:px-4 sm:py-2 transition-colors duration-200 rounded-xl text-white text-xs sm:text-sm font-medium flex-shrink-0"
                                                style="background: linear-gradient(180deg, #F6510C 0%, #F5D70B 100%); 
                                                                                                                                                                                        box-shadow: 0 4px 0 #7A4305; text-shadow: -1px -1px 0 #7A4305, 1px -1px 0 #7A4305, -1px 1px 0 #7A4305, 1px 1px 0 #7A4305, 0 0 1px #7A4305;">
                                                View All
                                            </a>
                                        </div>
                                    </div>


                                    <!-- Achievements Section in Dashboard -->
                                    <div class="p-4 sm:p-6 "
                                        style="background: linear-gradient(135deg, #312E81 0%, #701FB7 50%, #1E1B4B 100%);">
                                        <!-- Replace the achievements grid section with this updated version -->
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                                            @foreach($recentAchievements as $achievement)
                                                @php
                                                    // Get the drop shadow class based on rarity
                                                    $dropShadowClass = match (strtolower($achievement['rarity'])) {
                                                        'common' => 'drop-shadow-achievement-common',
                                                        'uncommon' => 'drop-shadow-achievement-uncommon',
                                                        'rare' => 'drop-shadow-achievement-rare',
                                                        'epic' => 'drop-shadow-achievement-epic',
                                                        'legendary' => 'drop-shadow-achievement-legendary',
                                                        default => 'drop-shadow-achievement-blue'
                                                    };

                                                    // Get the inner shadow class based on rarity (now includes both shadows)
                                                    $innerShadowClass = match (strtolower($achievement['rarity'])) {
                                                        'common' => 'shadow-inner-achievement-common',
                                                        'uncommon' => 'shadow-inner-achievement-uncommon',
                                                        'rare' => 'shadow-inner-achievement-rare',
                                                        'epic' => 'shadow-inner-achievement-epic',
                                                        'legendary' => 'shadow-inner-achievement-legendary',
                                                        default => 'shadow-inner-achievement-blue'
                                                    };
                                                @endphp
                                                <div
                                                    class="text-center p-2 sm:p-3 lg:p-4 group cursor-pointer transition-transform duration-300 hover:-translate-y-3">
                                                    <!-- Container for achievement circle -->
                                                    <div
                                                        class="relative w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-24 lg:h-24 mx-auto mb-2 sm:mb-3">
                                                        <!-- Achievement circle with custom drop shadow and inner shadow -->
                                                        <div class="absolute inset-0 w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-full flex items-center justify-center {{ $dropShadowClass }} {{ $innerShadowClass }}"
                                                            style="background-color: {{ $achievement['background_light'] }};">
                                                            <img src="{{ asset('images/achievements/' . $achievement['front_image']) }}"
                                                                alt="{{ $achievement['title'] }}"
                                                                class="w-8 h-8 sm:w-12 sm:h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 xl:w-18 xl:h-18 object-contain drop-shadow-sm">
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="font-semibold text-white text-sm sm:text-base md:text-lg lg:text-xl drop-shadow-sm px-1">
                                                        {{ $achievement['title'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>


                                <!-- Leaderboard Section -->
                                <div class="rounded-xl overflow-hidden shadow-md">
                                    <!-- Header -->
                                    <div
                                        class="bg-gradient-to-r from-orange-500 to-orange-700 flex flex-col items-start px-4 sm:px-6 py-3 sm:py-4">

                                        <h1
                                            class="text-white text-xl sm:text-2xl lg:text-3xl font-poppins font-bold tracking-wide flex items-center">
                                            Leaderboards
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-300 ml-2" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                            </svg>
                                        </h1>

                                        <p class="text-xs sm:text-sm text-yellow-100 opacity-90 mt-1">
                                            See the top performing students.
                                        </p>
                                    </div>



                                    <!-- Body -->
                                    <div class="bg-gradient-to-b from-yellow-100 to-orange-50 p-4 sm:p-6">
                                        <div class="space-y-2 sm:space-y-3">
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
                                                <div class="flex items-center justify-between p-2 sm:p-3 rounded-lg shadow-sm">
                                                    <div class="flex items-center min-w-0 flex-1">
                                                        <div
                                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full overflow-hidden flex items-center justify-center mr-2 sm:mr-3 border-2 flex-shrink-0 {{ $rank === 1 ? 'bg-yellow-500 border-yellow-600' : ($rank === 2 ? 'bg-gray-400 border-gray-500' : ($rank === 3 ? 'bg-orange-600 border-orange-700' : ($rank === 4 ? 'bg-blue-400 border-blue-500' : 'bg-teal-400 border-teal-500'))) }}">
                                                            <img src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}"
                                                                alt="{{ $row['name'] ?? 'Student' }}"
                                                                class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <div
                                                                class="font-baloo font-bold text-gray-900 text-sm sm:text-base lg:text-lg truncate">
                                                                {{ $row['name'] ?? 'Student' }}
                                                            </div>
                                                            <div class="text-xs sm:text-sm text-gray-600">
                                                                {{ $row['points'] ?? 0 }} pts
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm font-bold shadow-sm flex-shrink-0 ml-2 {{ $rankBadges[$rank] ?? 'bg-gray-200 text-gray-800' }}">
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


            <!-- Include Login Streak Modal -->
            <x-login-streak-modal />


            <!-- Test Button (Remove when done) -->
            <button onclick="showStreakModal({current_streak: 3, points_earned: 25, message: 'Keep it up!'})"
                class="fixed bottom-4 right-4 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-full shadow-lg z-50 font-medium">
                Test Modal
            </button>
            <script>
                // Dashboard Loading Logic
                document.addEventListener('DOMContentLoaded', function () {
                    // Show skeleton initially, hide actual content
                    const skeleton = document.getElementById('dashboardSkeleton');
                    const content = document.getElementById('dashboardContent');

                    // Simulate data loading (replace with actual API calls)
                    setTimeout(() => {
                        loadDashboardData();
                    }, 1500); // Adjust timing as needed

                    // Auto-check for login streak on page load
                    checkLoginStreak();
                    updateDashboardStats();
                });

                async function loadDashboardData() {
                    try {
                        // Simulate API calls for different data sections
                        const promises = [
                            loadUserProgress(),
                            loadAchievements(),
                            loadLeaderboard(),
                            loadAssignments(),
                            updateDashboardStats()
                        ];

                        // Wait for all data to load
                        await Promise.all(promises);

                        // Hide skeleton and show actual content with animation
                        const skeleton = document.getElementById('dashboardSkeleton');
                        const content = document.getElementById('dashboardContent');

                        skeleton.style.opacity = '0';
                        setTimeout(() => {
                            skeleton.classList.add('hidden');
                            content.classList.remove('hidden');
                            content.classList.add('content-loaded');
                        }, 300);

                    } catch (error) {
                        console.error('Error loading dashboard data:', error);
                        // Show content anyway to prevent infinite loading
                        showDashboardContent();
                    }
                }

                function showDashboardContent() {
                    const skeleton = document.getElementById('dashboardSkeleton');
                    const content = document.getElementById('dashboardContent');

                    skeleton.classList.add('hidden');
                    content.classList.remove('hidden');
                    content.classList.add('content-loaded');
                }

                // Simulate API calls (replace with actual endpoints)
                async function loadUserProgress() {
                    // Replace with: return fetch('/api/user-progress').then(r => r.json());
                    return new Promise(resolve => setTimeout(resolve, 300));
                }

                async function loadAchievements() {
                    // Replace with: return fetch('/api/achievements').then(r => r.json());
                    return new Promise(resolve => setTimeout(resolve, 400));
                }

                async function loadLeaderboard() {
                    // Replace with: return fetch('/api/leaderboard').then(r => r.json());
                    return new Promise(resolve => setTimeout(resolve, 500));
                }

                async function loadAssignments() {
                    // Replace with: return fetch('/api/assignments').then(r => r.json());
                    return new Promise(resolve => setTimeout(resolve, 200));
                }

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

                // Check for new badges on page load
                function checkNewBadges() {
                    fetch('{{ route("student.gamification.check-new-badges") }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.has_new_badges) {
                            console.log('New badges found:', data.new_badges);

                            // Show badge modal
                            if (typeof showBadgeModal === 'function') {
                                showBadgeModal(data.new_badges);

                                // Mark badges as viewed after a delay
                                setTimeout(() => {
                                    markBadgesAsViewed(data.new_badges);
                                }, 5000);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error checking new badges:', error);
                    });
                }

                // Mark badges as viewed
                function markBadgesAsViewed(badges) {
                    const badgeIds = badges.map(badge => badge.id);

                    fetch('{{ route("student.gamification.mark-badges-viewed") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            badge_ids: badgeIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Badges marked as viewed:', data);
                    })
                    .catch(error => {
                        console.error('Error marking badges as viewed:', error);
                    });
                }

                // Check for new badges when page loads
                document.addEventListener('DOMContentLoaded', function() {
                    // Small delay to ensure everything is loaded
                    setTimeout(checkNewBadges, 1000);
                });

                // Test function to show badge modal with sample data
                function testBadgeModal() {
                    const sampleBadges = [
                        {
                            id: 1,
                            badge_key: 'first_steps',
                            badge_name: 'First Steps',
                            badge_description: 'Earn 50 total points',
                            badge_icon: '🎯',
                            points_required: 50
                        },
                        {
                            id: 2,
                            badge_key: 'quick_learner',
                            badge_name: 'Quick Learner',
                            badge_description: 'Earn 500 total points',
                            badge_icon: '⚡',
                            points_required: 500
                        }
                    ];

                    showBadgeModal(sampleBadges);
                }
            </script>

    <!-- Floating Test Button for Badge Modal -->
    {{-- <button onclick="testBadgeModal()"
            class="fixed bottom-4 right-4 z-50 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 px-6 rounded-full shadow-2xl hover:scale-110 transition-all duration-200 flex items-center gap-2 group">
        <span class="text-2xl">🏆</span>
        <span class="hidden group-hover:inline-block">Test Badge Modal</span>
    </button> --}}

    @include('components.level-up-modal')
    @include('components.badge-unlock-modal')
@endsection
</body>

</html>