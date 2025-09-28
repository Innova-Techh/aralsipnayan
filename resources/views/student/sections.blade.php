@extends('layouts.user_layout')

@section('title', 'My Section - AralSipnayan')

@section('content')
    <style>
        /* .hero-bg {
            background-image: url('{{ asset('images/section/bg2.png') }}');
        }

        @media (min-width: 1024px) {
            .hero-bg {
                background-image: url('{{ asset('images/section/bg.png') }}');
            }
        } */
    </style>

    <!-- Main Container with Responsive Padding -->
    <div class="max-w-8xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 pb-12 sm:pb-16 lg:pb-20">
        <div class="space-y-4 sm:space-y-6 lg:space-y-8">

            <!-- Welcome Header (Hero) - Responsive -->
            <div
                class="welcome-header relative -mx-4 sm:-mx-6 lg:-mx-8 text-white bg-gradient-purple shadow-inner-violet drop-shadow-custom-purple overflow-hidden min-h-[160px] sm:min-h-[200px] lg:min-h-[220px] flex items-center bg-center bg-cover hero-bg">

                <!-- Grid Background -->
                <div class="absolute inset-0 opacity-30"
                    style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 40px 40px;">
                </div>
                <!-- Content -->

                <div class="relative z-10 w-full px-4 sm:px-8 lg:px-8 max-w-8xl mx-auto">
                    <div class="flex flex-col justify-center h-full">
                        <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-5xl font-baloo font-extrabold leading-tight tracking-tight"
                            style="text-shadow: -1px -1px 0 #18337e,
                                                       1px -1px 0 #18337e,
                                                       -1px 1px 0 #18337e,
                                                       1px 1px 0 #18337e,
                                                       0 4px 0 #18337e;">
                            My Section 🏆
                        </h1>
                        <p class="text-base sm:text-lg md:text-xl lg:text-xl text-blue-100 mt-3 sm:mt-4 lg:mt-5">
                            Your skills update and assessment quest in one place!
                        </p>
                    </div>
                </div>


            </div>

            <!-- Main Content Grid - Responsive Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">

                <!-- Left Column - Quest Board -->
                <div class="lg:col-span-2 lg:order-1 space-y-4 sm:space-y-6 lg:space-y-8">
                    <div class="rounded-xl px-3 sm:px-6 lg:px-12">
                        <div class="space-y-3 sm:space-y-4">

                            <!-- Quest Board Header - Responsive -->
                            <div
                                class=" bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-xl lg:rounded-t-2xl p-4 sm:p-5 lg:p-6 text-white">
                                <div class="flex items-center">
                                    <h2 class="text-xl xs:text-3xl sm:text-2xl lg:text-3xl font-baloo font-bold mr-3">Quest
                                        Board 🎮
                                    </h2>
                                </div>
                                <p class="text-blue-100 text-xs sm:text-sm mt-1">Complete your epic math adventures</p>
                            </div>

                            <!-- Quest Cards Container - Responsive -->
                            <div>

                                <div class="shadow-lg px-2 xs:px-4 sm:px-3 lg:px-12 pb-2 rounded-xl space-y-3 sm:space-y-4">
                                    @forelse($sections as $section)
                                        <div class="bg-gradient-to-r {{ $section['color'] }} rounded-xl lg:rounded-2xl p-4 sm:p-5 lg:px-8 lg:p-6 text-white shadow-xl hover:shadow-2xl transition-shadow">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-baloo font-bold mb-2">{{ $section['title'] }}</h3>
                                            <p class="text-white/90 text-xs sm:text-sm mb-4 sm:mb-6">{{ $section['description'] }}</p>

                                            <!-- Stats Row - Mobile Responsive -->
                                            <div class="grid grid-cols-3 gap-2 sm:gap-4 rounded-xl py-3 sm:py-4 bg-gray-100/50 mb-4 sm:mb-6">
                                                <div class="text-center">
                                                    <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">{{ $section['xp_reward'] }}</div>
                                                    <div class="text-xs lg:text-sm font-medium opacity-90">XP Reward</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">{{ $section['time_limit'] }}</div>
                                                    <div class="text-xs lg:text-sm font-medium opacity-90">Time Limit</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-lg sm:text-lg lg:text-xl xl:text-2xl font-bold mb-1">{{ $section['difficulty'] }}</div>
                                                    <div class="text-xs lg:text-lg font-medium opacity-90">Difficulty</div>
                                                </div>
                                            </div>

<<<<<<< Updated upstream
                                            <!-- Live Quiz Indicator -->
                                            @if(isset($section['is_live_quiz']) && $section['is_live_quiz'])
                                                <div class="mb-4 p-2 bg-red-500/20 border border-red-300/30 rounded-lg">
                                                    <div class="flex items-center justify-center">
                                                        <span class="text-red-200 text-sm font-medium">🔴 Live Quiz Mode</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Start Assessment Button - Responsive -->
                                            @if(isset($section['assignment_id']))
                                                <a href="{{ route('teacher-assessments.show', $section['id']) }}"
                                                class="w-full bg-gradient-secondary drop-shadow-gradient-secondary text-white font-baloo font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl shadow-lg text-sm sm:text-base lg:text-xl transition-colors block text-center"
                                                style="text-shadow: -1px -1px 0 #7A4305, 1px -1px 0 #7A4305, -1px 1px 0 #7A4305, 1px 1px 0 #7A4305, 0 0 1px #7A4305;">
                                                    @if(isset($section['is_live_quiz']) && $section['is_live_quiz'])
                                                        🔴 Live Quiz
                                                    @else
                                                        Start Assessment
                                                    @endif
                                                </a>
                                            @else
                                                <button class="w-full bg-gradient-secondary drop-shadow-gradient-secondary text-white font-baloo font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl shadow-lg text-sm sm:text-base lg:text-xl transition-colors"
                                                        style="text-shadow: -1px -1px 0 #7A4305, 1px -1px 0 #7A4305, -1px 1px 0 #7A4305, 1px 1px 0 #7A4305, 0 0 1px #7A4305;">
                                                    Start Assessment
                                                </button>
                                            @endif
=======
                                    <!-- Start Assessment Button - Responsive -->
                                    <button
                                        class="w-full bg-gradient-secondary drop-shadow-gradient-secondary  text-white font-baloo font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl shadow-lg text-sm sm:text-base lg:text-xl transition-colors
                                                                                                                                                        "
                                        style="text-shadow:
                                                                                                                                                                -1px -1px 0 #7A4305,
                                                                                                                                                                1px -1px 0 #7A4305,
                                                                                                                                                                -1px 1px 0 #7A4305,
                                                                                                                                                                1px 1px 0 #7A4305,
                                                                                                                                                                0 0 1px #7A4305;">
                                        Start Assessment
                                    </button>
                                </div>

                                <!-- Fractions and Decimals Card -->
                                <div
                                    class=" bg-gradient-to-r from-green-500 to-teal-500 rounded-xl lg:rounded-2xl p-4 lg:px-8 
                                                                                                                                                    sm:p-5 lg:p-6 text-white shadow-xl hover:shadow-2xl transition-shadow">
                                    <h3 class="text-lg sm:text-xl lg:text-2xl font-baloo font-bold mb-2">Fractions and
                                        Decimals</h3>
                                    <p class="text-white/90 text-xs sm:text-sm mb-4 sm:mb-6">Practice assessment on
                                        converting fractions to decimals</p>

                                    <!-- Stats Row - Mobile Responsive -->
                                    <div
                                        class="grid grid-cols-3 gap-2 sm:gap-4 rounded-xl py-3 sm:py-4 bg-gray-100/50 mb-4 sm:mb-6">
                                        <div class="text-center">
                                            <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">150
                                            </div>
                                            <div class="text-xs lg:text-sm font-medium opacity-90">XP Reward</div>
>>>>>>> Stashed changes
                                        </div>
                                    @empty
                                        <div class="text-center py-12">
                                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">quiz</span>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No assessments assigned</h3>
                                            <p class="text-gray-500">Your teacher hasn't assigned any assessments yet</p>
                                        </div>
<<<<<<< Updated upstream
                                    @endforelse
=======
                                        <div class="text-center">
                                            <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">Easy
                                            </div>
                                            <div class="text-xs lg:text-sm font-medium opacity-90">Difficulty</div>
                                        </div>
                                    </div>

                                    <!-- Start Assessment Button - Responsive -->
                                    <button
                                        class="w-full bg-gradient-secondary drop-shadow-gradient-secondary  text-white font-baloo font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl shadow-lg text-sm sm:text-base lg:text-xl transition-colors
                                                                                                                                                        "
                                        style="text-shadow:
                                                                                                                                                                -1px -1px 0 #7A4305,
                                                                                                                                                                1px -1px 0 #7A4305,
                                                                                                                                                                -1px 1px 0 #7A4305,
                                                                                                                                                                1px 1px 0 #7A4305,
                                                                                                                                                                0 0 1px #7A4305;">
                                        Start Assessment
                                    </button>
                                </div>

                                <!-- Number Operations Quiz Card -->
                                <div
                                    class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl lg:rounded-2xl p-4 sm:p-5 lg:px-8  lg:p-6 text-white shadow-xl hover:shadow-2xl transition-shadow">
                                    <h3 class="text-lg sm:text-xl lg:text-2xl font-baloo font-bold mb-2">Number Operations
                                        Quiz</h3>
                                    <p class="text-white/90 text-xs sm:text-sm mb-4 sm:mb-6">Quick assessment on basic
                                        arithmetic operations</p>

                                    <!-- Stats Row - Mobile Responsive -->
                                    <div
                                        class="grid grid-cols-3 gap-2 sm:gap-4 rounded-xl py-3 sm:py-4 bg-gray-100/50 mb-4 sm:mb-6">
                                        <div class="text-center">
                                            <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">150</div>
                                            <div class="text-xs lg:text-sm font-medium opacity-90">XP Reward</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">25 mins
                                            </div>
                                            <div class="text-xs lg:text-sm font-medium opacity-90">Time Limit</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg sm:text-xl lg:text-xl xl:text-2xl font-bold mb-1">Hard
                                            </div>
                                            <div class="text-xs lg:text-sm font-medium opacity-90">Difficulty</div>
                                        </div>
                                    </div>

                                    <!-- Start Assessment Button - Responsive -->
                                    <button
                                        class="w-full bg-gradient-secondary drop-shadow-gradient-secondary  text-white font-baloo font-bold py-3 sm:py-4 px-4 sm:px-6 rounded-xl shadow-lg text-sm sm:text-base lg:text-xl transition-colors
                                                                                                                                                        "
                                        style="text-shadow:
                                                                                                                                                                -1px -1px 0 #7A4305,
                                                                                                                                                                1px -1px 0 #7A4305,
                                                                                                                                                                -1px 1px 0 #7A4305,
                                                                                                                                                                1px 1px 0 #7A4305,
                                                                                                                                                                0 0 1px #7A4305;">
                                        Start Assessment
                                    </button>
>>>>>>> Stashed changes
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Teacher's Board -->
                <div x-data="{ activeIndex: 0, total: 3 }" class="lg:col-span-1 lg:order-2 space-y-4 px-8">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

                        <!-- Teacher's Board Header - Responsive -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 sm:p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="bg-white/20 p-1.5 sm:p-2 rounded-lg mr-2 sm:mr-3">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg sm:text-xl font-baloo font-bold">Teacher's Board</h2>
                                        <p class="text-blue-100 text-xs sm:text-sm">Latest announcements</p>
                                    </div>
                                </div>

                                <!-- Mobile/Tablet Carousel Controls -->
                                <div class="flex items-center space-x-1 sm:space-x-2 lg:hidden">
                                    <button @click="activeIndex = (activeIndex === 0 ? total - 1 : activeIndex - 1)"
                                        class="text-white/70 hover:text-white p-1">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <span class="text-white/90 text-xs sm:text-sm"
                                        x-text="(activeIndex+1) + '/' + total"></span>
                                    <button @click="activeIndex = (activeIndex + 1) % total"
                                        class="text-white/70 hover:text-white p-1">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher's Messages - Responsive Content -->
                        <div class="p-3 sm:p-4">

                            <!-- Desktop (all stacked) -->
                            <div class="hidden lg:block space-y-4">
                                <!-- Announcement 1 -->
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-400">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-semibold text-gray-800 text-sm sm:text-base">Upcoming Math
                                            Competition</h4>
                                        <span
                                            class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full font-medium">high</span>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Students who are interested in joining the inter-school math competition should
                                        submit
                                        their names by Friday. This is a great opportunity to showcase your mathematical
                                        skills!
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="font-medium">Ms. Rodriguez</span>
                                        <span>18/01/2024</span>
                                    </div>
                                </div>

                                <!-- Announcement 2 -->
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-yellow-400">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-semibold text-gray-800 text-sm sm:text-base">Study Group Session
                                        </h4>
                                        <span
                                            class="bg-yellow-100 text-yellow-600 text-xs px-2 py-1 rounded-full font-medium">medium</span>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Extra study session for Geometry will be held this Saturday at 2:00 PM in Room 205.
                                        Bring your notebooks and calculators.
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="font-medium">Ms. Rodriguez</span>
                                        <span>17/01/2024</span>
                                    </div>
                                </div>

                                <!-- Announcement 3 -->
                                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-400">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="font-semibold text-gray-800 text-sm sm:text-base">Assignment Reminder
                                        </h4>
                                        <span
                                            class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full font-medium">low</span>
                                    </div>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">
                                        Don't forget to complete your fraction worksheets. They are due next Monday.
                                        If you need help, please don't hesitate to ask during office hours.
                                    </p>
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span class="font-medium">Ms. Rodriguez</span>
                                        <span>16/01/2024</span>
                                    </div>
                                </div>
                            </div>


                            <!-- Mobile/Tablet Version (carousel) -->
                            <div class="lg:hidden">
                                <!-- Announcement 1 -->
                                <template x-if="activeIndex === 0">
                                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border-l-4 border-red-400">
                                        <div class="flex items-start justify-between mb-2 sm:mb-3">
                                            <h4 class="font-semibold text-gray-800 text-sm sm:text-base leading-tight">
                                                Upcoming Math Competition</h4>
                                            <span
                                                class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full font-medium ml-2 whitespace-nowrap">high</span>
                                        </div>
                                        <p class="text-gray-600 text-xs sm:text-sm leading-relaxed mb-2 sm:mb-3">
                                            Students who are interested in joining the inter-school math competition should
                                            submit their names by Friday. This is a great opportunity to showcase your
                                            mathematical skills!
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span class="font-medium">Ms. Rodriguez</span>
                                            <span>18/01/2024</span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Announcement 2 -->
                                <template x-if="activeIndex === 1">
                                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border-l-4 border-yellow-400">
                                        <div class="flex items-start justify-between mb-2 sm:mb-3">
                                            <h4 class="font-semibold text-gray-800 text-sm sm:text-base leading-tight">Study
                                                Group Session</h4>
                                            <span
                                                class="bg-yellow-100 text-yellow-600 text-xs px-2 py-1 rounded-full font-medium ml-2 whitespace-nowrap">medium</span>
                                        </div>
                                        <p class="text-gray-600 text-xs sm:text-sm leading-relaxed mb-2 sm:mb-3">
                                            Extra study session for Geometry will be held this Saturday at 2:00 PM in Room
                                            205. Bring your notebooks and calculators.
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span class="font-medium">Ms. Rodriguez</span>
                                            <span>17/01/2024</span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Announcement 3 -->
                                <template x-if="activeIndex === 2">
                                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border-l-4 border-blue-400">
                                        <div class="flex items-start justify-between mb-2 sm:mb-3">
                                            <h4 class="font-semibold text-gray-800 text-sm sm:text-base leading-tight">
                                                Assignment Reminder</h4>
                                            <span
                                                class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full font-medium ml-2 whitespace-nowrap">low</span>
                                        </div>
                                        <p class="text-gray-600 text-xs sm:text-sm leading-relaxed mb-2 sm:mb-3">
                                            Don't forget to complete your fraction worksheets. They are due next Monday. If
                                            you need help, please don't hesitate to ask during office hours.
                                        </p>
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span class="font-medium">Ms. Rodriguez</span>
                                            <span>16/01/2024</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
@endsection