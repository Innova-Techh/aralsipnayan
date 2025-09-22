<!-- dashboard.blade.php -->
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
    </style>


    <!-- Welcome Header (Hero) - Fixed margins and width -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
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
                                            <p class="text-sm text-blue-200">Level {{ $progressInfo['current_level'] }}</p>
                                        </div>

                                        <!-- Group 2: XP Text (standalone) -->
                                        <div class="mb-2 mr-4 text-right">
                                            @if($progressInfo['is_max_level'])
                                                <p class="text-xs sm:text-sm text-yellow-300 font-bold">MAX LEVEL ACHIEVED!</p>
                                            @else
                                                <p class="text-xs sm:text-sm text-blue-200">
                                                    {{ number_format($progressInfo['current_xp']) }} XP /
                                                    {{ number_format($progressInfo['rank_info']['xp_required']) }} XP
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Group 3: Progress bar and XP remaining -->
                                        <div class="mr-4">
                                            <div class="mb-1">
                                                <div class="relative h-2 sm:h-2.5 bg-white/20 rounded-full overflow-hidden">
                                                    <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-yellow-400 to-orange-500 transition-all duration-300 rounded-full"
                                                        style="width: {{ $progressInfo['progress_percentage'] }}%"></div>
                                                </div>
                                            </div>
                                            @if($progressInfo['is_max_level'])
                                                <p class="text-xs sm:text-sm text-yellow-300">🏆 Grandmaster Status</p>
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
                                    {{-- <div
                                        class="absolute top-2 left-4 w-8 h-8 bg-book-icon bg-contain bg-no-repeat rotate-[-15deg]">
                                    </div>
                                    <div
                                        class="absolute top-6 left-20 w-6 h-6 bg-book-icon bg-contain bg-no-repeat rotate-[25deg]">
                                    </div>
                                    <div
                                        class="absolute top-12 left-12 w-4 h-4 bg-book-icon bg-contain bg-no-repeat rotate-[10deg]">
                                    </div>
                                    <div
                                        class="absolute top-12 right-12 w-4 h-4 bg-book-icon bg-contain bg-no-repeat rotate-[45deg]">
                                    </div>
                                    <div
                                        class="absolute top-5 right-16 w-7 h-7 bg-book-icon bg-contain bg-no-repeat rotate-[35deg]">
                                    </div>
                                    <div
                                        class="absolute top-1 right-2 w-10 h-10 bg-book-icon bg-contain bg-no-repeat rotate-[-20deg]">
                                    </div>
                                    <div
                                        class="absolute top-2 left-14 w-6 h-6 bg-book-icon bg-contain bg-no-repeat rotate-[-145deg]">
                                    </div>
                                    <div
                                        class="absolute top-1 right-24 w-8 h-8 bg-book-icon bg-contain bg-no-repeat rotate-[-140deg]">
                                    </div>
                                    <div
                                        class="absolute top-1 left-24 w-6 h-6 bg-book-icon bg-contain bg-no-repeat rotate-[24deg]">
                                    </div>
                                    <div
                                        class="absolute top-1 left-24 w-6 h-6 bg-book-icon bg-contain bg-no-repeat rotate-[24deg]">
                                    </div>
                                    <div
                                        class="absolute top-1 left-24 w-6 h-6 bg-book-icon bg-contain bg-no-repeat rotate-[24deg]">
                                    </div>
                                    <div
                                        class="absolute top-1 left-24 w-6 h-6 bg-book-icon bg-contain bg-no-repeat rotate-[24deg]">
                                    </div> --}}

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
                                <!-- Assessment Item 1 -->
                                <div
                                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between rounded-lg p-4 shadow relative">
                                    <div class="flex-1">
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
                                    <h2 class="text-lg sm:text-xl lg:text-2xl font-poppins font-bold text-white truncate">
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
                                                    alt="{{ $row['name'] ?? 'Student' }}" class="w-full h-full object-cover">
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