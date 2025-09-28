@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')

    <style>
        /* Grid Lines */
        .grid-lines {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>

    <div class="space-y-8 max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
        <!-- My Assessments Header -->

        <div
            class="relative overflow-hidden -mx-4 sm:-mx-6 lg:-mx-8 text-white bg-gradient-purple shadow-inner-violet drop-shadow-custom-purple bg-center bg-no-repeat min-h-[160px] sm:min-h-[200px] lg:min-h-[220px] flex items-center">

            <!-- Grid Background -->
            <div class="absolute inset-0 opacity-30"
                style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <!-- Content -->
            <div class="relative z-10 w-full px-4 sm:px-8 lg:px-8 max-w-8xl mx-auto">
                <div class="flex flex-col justify-center h-full">
                    <h1
                        class="text-2xl sm:text-4xl md:text-5xl lg:text-5xl font-baloo font-extrabold leading-tight tracking-tight drop-shadow-header">
                        My Assessments
                    </h1>
                    <p
                        class="text-base sm:text-base md:text-xl lg:text-xl text-blue-100 mt-3 sm:mt-4 lg:mt-5 drop-shadow-description">
                        Test your mathematical knowledge across different competencies and track your learning progress with
                        adaptive assessments
                    </p>
                </div>
            </div>
        </div>

        <!-- Feature Cards Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8 px-8 pt-8">
            <!-- Ready for a Challenge Card -->
            <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#2c014b] shadow-lg"
                style="background-image: url('{{ asset('images/assessments/card1.png') }}'); background-size: cover; background-position: center;">
                <div class="relative z-10 w-[65%] sm:w-[70%]">
                    <div class="flex items-center mb-2 md:mb-4">
                        <h3 class="text-base md:text-xl font-bold">🎯 Ready for a Challenge?</h3>
                    </div>
                    <p class="text-white mb-2 md:mb-4 text-sm md:text-base">
                        Test your math skills with fun assessments! Choose from geometry, numbers, or fractions and
                        start
                        your learning adventure!
                    </p>
                </div>
            </div>


            <!-- Level Up Your Skills Card -->
            <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#922f26] shadow-lg"
                style="background-image: url('{{ asset('images/assessments/card2.png') }}'); background-size: cover; background-position: center;">
                <div class="relative z-10 w-[65%] sm:w-[70%]">
                    <div class="flex items-center mb-2 md:mb-4">
                        <h3 class="text-base md:text-xl font-bold">🌟 Level Up Your Skills!</h3>
                    </div>
                    <p class="text-white mb-2 md:mb-4 text-sm md:text-base">Adaptive learning just for you. Our smart
                        system
                        adjusts questions to match your learning pace perfectly!</p>
                </div>
            </div>

            <!-- Learning is Fun Card -->
            <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#396697] shadow-lg"
                style="background-image: url('{{ asset('images/assessments/card3.png') }}'); background-size: cover; background-position: center;">
                <div class="relative z-10 w-[65%] sm:w-[70%]">
                    <div class="flex items-center mb-2 md:mb-4">
                        <h3 class="text-base md:text-xl font-bold">🎮 Learning is Fun!</h3>
                    </div>
                    <p class="text-white mb-2 md:mb-4 text-sm md:text-base">Gamified math assessments. Earn points,
                        unlock
                        achievements, and compete with friends while learning!</p>
                </div>
            </div>
        </div>

        <!-- Assessment Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-8">
            <!-- Number and Algebra Card -->
            <div class="rounded-xl shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
                style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

                <!-- Background Vector (kept on top for visual enhancement) -->
                <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                    <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                    <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
                </div>

                <div class="flex justify-between items-start mb-3 sm:mb-4 relative z-10">
                    <div class="relative w-[100%]">
                        <h3 class="text-lg font-bold text-white mb-1 leading-tight">Number and Algebra</h3>
                        <p class="text-sm text-gray-100">Test your knowledge of numbers, operations, and algebraic
                            concepts
                        </p>
                    </div>
                    @php
                        $naMastery = $masteryData['Number_Algebra'] ?? null;
                        $naLevel = $naMastery ? $naMastery->current_difficulty_level : 'Beginner';
                        $naProgress = $naMastery ? round($naMastery->mastery_probability * 100) : 30;
                        $naHasDiagnostic = $naMastery ? $naMastery->has_taken_diagnostic : false;
                        $naIncompleteSession = $incompleteSessionData['Number_Algebra'] ?? null;
                    @endphp
                    <span
                        class="bg-{{ $naLevel === 'Beginner' ? 'green' : ($naLevel === 'Intermediate' ? 'yellow' : 'red') }}-100 
                                                                                                                                                                    text-{{ $naLevel === 'Beginner' ? 'green' : ($naLevel === 'Intermediate' ? 'yellow' : 'red') }}-700 
                                                                                                                                                                    text-xs font-medium px-2 py-1 rounded-full whitespace-nowrap ml-2">
                        {{ $naLevel }}
                    </span>

                </div>

                <!-- Progress Bar -->
                <div class="mb-4 relative z-10 flex flex-col gap-2">
                    <!-- Label and percentage -->
                    <div class="flex justify-between w-full text-sm text-gray-100">
                        <span>Mastery Level</span>
                        <span id="progress-text-na">{{ $naProgress }}%</span>
                    </div>

                    <!-- Progress bar container -->
                    <div class="relative w-full h-8 bg-gray-800 rounded-full overflow-hidden shadow-inner">
                        <!-- Gradient progress bar -->
                        <div id="progress-bar-na"
                            class="absolute top-0 left-0 h-full rounded-full transition-all duration-1000 overflow-hidden"
                            style="width: 0%;
                                                                                                                                                                                                    background: linear-gradient(90deg, #DE4A0F, #F9C74F);">
                            <!-- Static shimmer lines -->
                            <div class="absolute inset-0 flex items-center justify-between px-2">
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex justify-between mb-4 relative z-10">
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">
                                {{ $naMastery ? $naMastery->correct_answers : 0 }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-100">Correct</span>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">
                                {{ $naMastery ? $naMastery->total_questions_answered : 0 }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-100">Total</span>
                    </div>
                </div>

                <!-- Button -->
                @if(!$naHasDiagnostic)
                    @if($naIncompleteSession)
                        <button onclick="checkDiagnosticBeforeStart('Number_Algebra')"
                            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-center border-b-[6px] border-[#cc4713] shadow-lg">
                            Resume Diagnostic Test
                        </button>
                    @else
                        <button onclick="checkDiagnosticBeforeStart('Number_Algebra')"
                            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                            Take Diagnostic Test
                        </button>
                    @endif
                @else
                    <a href="{{ route('student.assessments.category', 'Number_Algebra') }}"
                        class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)] text-center border-b-[6px] border-[#264566] shadow-lg"
                        style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                        View Assessments
                    </a>
                @endif
            </div>

            <!-- Measurement and Geometry Card -->
            <div class="rounded-xl shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
                style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

                <!-- Background Vector (kept on top for visual enhancement) -->
                <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                    <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                    <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <div class="flex justify-between items-start mb-3 sm:mb-4 relative z-20">
                    <div class=" relative w-[100%]">
                        <h3 class="text-lg font-bold text-white mb-1 leading-tight">Measurement and Geometry</h3>
                        <p class="text-sm text-gray-100 leading-relaxed">Test your knowledge of shapes, angles, and spatial
                            relationships
                        </p>
                    </div>
                    @php
                        $mgMastery = $masteryData['Measurement_Geometry'] ?? null;
                        $mgLevel = $mgMastery ? $mgMastery->current_difficulty_level : 'Beginner';
                        $mgProgress = $mgMastery ? round($mgMastery->mastery_probability * 100) : 30;
                        $mgHasDiagnostic = $mgMastery ? $mgMastery->has_taken_diagnostic : false;
                        $mgIncompleteSession = $incompleteSessionData['Measurement_Geometry'] ?? null;
                    @endphp
                    <span
                        class="bg-{{ $mgLevel === 'Beginner' ? 'green' : ($mgLevel === 'Intermediate' ? 'yellow' : 'red') }}-100 
                                                                                                                                                  text-{{ $mgLevel === 'Beginner' ? 'green' : ($mgLevel === 'Intermediate' ? 'yellow' : 'red') }}-700 
                                                                                                                                                  text-xs font-medium px-2 py-1 rounded-full relative z-20">
                        {{ $mgLevel }}
                    </span>

                </div>

                <!-- Progress Bar -->
                <div class="mb-4 relative z-10 flex flex-col gap-2">
                    <!-- Label and percentage -->
                    <div class="flex justify-between w-full text-sm text-gray-100">
                        <span>Mastery Level</span>
                        <span id="progress-text-mg">{{ $mgProgress }}%</span>
                    </div>

                    <!-- Progress bar container -->
                    <div class="relative w-full h-8 bg-gray-800 rounded-full overflow-hidden shadow-inner">
                        <!-- Gradient progress bar -->
                        <div id="progress-bar-mg"
                            class="absolute top-0 left-0 h-full rounded-full transition-all duration-1000 overflow-hidden"
                            style="width: 0%;
                                                                                                                                                                                                background: linear-gradient(90deg, #DE4A0F, #F9C74F);">
                            <!-- Static shimmer lines -->
                            <div class="absolute inset-0 flex items-center justify-between px-2">
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex justify-between mb-4 relative z-10">
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">
                                {{ $mgMastery ? $mgMastery->correct_answers : 0 }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-100">Correct</span>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">
                                {{ $mgMastery ? $mgMastery->total_questions_answered : 0 }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-100">Total</span>
                    </div>
                </div>

                <!-- Button -->
                @if(!$mgHasDiagnostic)
                    @if($mgIncompleteSession)
                        <button onclick="checkDiagnosticBeforeStart('Measurement_Geometry')"
                            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-center border-b-[6px] border-[#cc4713] shadow-lg">
                            Resume Diagnostic Test
                        </button>
                    @else
                        <button onclick="checkDiagnosticBeforeStart('Measurement_Geometry')"
                            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                            Take Diagnostic Test
                        </button>
                    @endif
                @else
                    <a href="{{ route('student.assessments.category', 'Measurement_Geometry') }}"
                        class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)] text-center border-b-[6px] border-[#264566] shadow-lg"
                        style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                        View Assessments
                    </a>
                @endif
            </div>

            <!-- Data and Probability Card -->
            <div class="rounded-xl shadow-sm p-4 sm:p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
                style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

                <!-- Background Vector (kept on top for visual enhancement) -->
                <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                    <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                    <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
                </div>

                <div class="flex justify-between items-start mb-3 sm:mb-4 relative z-10">
                    <div class=" relative w-[100%]">
                        <h3 class="text-lg font-bold text-white mb-1 leading-tight">Data and Probability</h3>
                        <p class="text-sm text-gray-100 leading-relaxed">Explore data tables, bar graphs, line plots, mean,
                            and chance
                            events</p>
                    </div>
                    @php
                        $dpMastery = $masteryData['Data_Probability'] ?? null;
                        $dpLevel = $dpMastery ? $dpMastery->current_difficulty_level : 'Beginner';
                        $dpProgress = $dpMastery ? round($dpMastery->mastery_probability * 100) : 30;
                        $dpHasDiagnostic = $dpMastery ? $dpMastery->has_taken_diagnostic : false;
                        $dpIncompleteSession = $incompleteSessionData['Data_Probability'] ?? null;
                    @endphp
                    <span
                        class="bg-{{ $dpLevel === 'Beginner' ? 'green' : ($dpLevel === 'Intermediate' ? 'yellow' : 'red') }}-100 
                                                                                                                                                                text-{{ $dpLevel === 'Beginner' ? 'green' : ($dpLevel === 'Intermediate' ? 'yellow' : 'red') }}-700 
                                                                                                                                                                text-xs font-medium px-2 py-1 rounded-full">
                        {{ $dpLevel }}
                    </span>

                </div>

                <!-- Progress Bar -->
                <div class="mb-4 relative z-10 flex flex-col gap-2">
                    <!-- Label and percentage -->
                    <div class="flex justify-between w-full text-sm text-gray-100">
                        <span>Mastery Level</span>
                        <span id="progress-text-dp">{{ $dpProgress }}%</span>
                    </div>

                    <!-- Progress bar container -->
                    <div class="relative w-full h-8 bg-gray-800 rounded-full overflow-hidden shadow-inner">
                        <!-- Gradient progress bar -->
                        <div id="progress-bar-dp"
                            class="absolute top-0 left-0 h-full rounded-full transition-all duration-1000 overflow-hidden"
                            style="width: 0%;
                                                                                                                                                                                                background: linear-gradient(90deg, #DE4A0F, #F9C74F);">
                            <!-- Static shimmer lines -->
                            <div class="absolute inset-0 flex items-center justify-between px-2">
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                                <div class="w-2 h-10 bg-gradient-to-t from-white/30 to-white/0 rotate-45"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex justify-between mb-4 relative z-10">
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">
                                {{ $dpMastery ? $dpMastery->correct_answers : 0 }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-100">Correct</span>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-1">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-white">
                                {{ $dpMastery ? $dpMastery->total_questions_answered : 0 }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-100">Total</span>
                    </div>
                </div>

                <!-- Button -->
                @if(!$dpHasDiagnostic)
                    @if($dpIncompleteSession)
                        <button onclick="checkDiagnosticBeforeStart('Data_Probability')"
                            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-center border-b-[6px] border-[#cc4713] shadow-lg">
                            Resume Diagnostic Test
                        </button>
                    @else
                        <button onclick="checkDiagnosticBeforeStart('Data_Probability')"
                            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                            Take Diagnostic Test
                        </button>
                    @endif
                @else
                    <a href="{{ route('student.assessments.category', 'Data_Probability') }}"
                        class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)] text-center border-b-[6px] border-[#264566] shadow-lg"
                        style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                        View Assessments
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Diagnostic Modal -->
    <div id="activeDiagnosticModal"
        class="fixed inset-0 bg-black bg-opacity-60 justify-center items-center z-[60] hidden px-4 sm:px-0">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-[0_20px_40px_rgba(0,0,0,0.4)] overflow-hidden transform scale-95 transition-all duration-300"
            id="activeDiagnosticModalContent">
            <!-- Header with 3D effects -->
            <div
                class="bg-gradient-to-r from-[#FF6B6B] to-[#FF8E8E] p-6 relative shadow-[0_8px_16px_rgba(0,0,0,0.3)] border-b-4 border-[#e74c3c]">
                <div class="absolute inset-0 bg-gradient-to-b from-white/30 to-transparent pointer-events-none"></div>
                <div class="flex items-center justify-center gap-3 relative z-10">
                    <div
                        class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shadow-[0_4px_8px_rgba(0,0,0,0.2)]">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white drop-shadow-lg">Diagnostic Test In Progress</h2>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 text-center">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Cannot Start New Diagnostic</h3>
                    <p class="text-gray-600 leading-relaxed">
                        You already have an active diagnostic test in progress. Please complete your current diagnostic test
                        before starting a new one.
                    </p>
                </div>

                <!-- Active Diagnostic Info -->
                <div
                    class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-6 border-l-4 border-blue-400 shadow-inner">
                    <div class="text-sm text-gray-700">
                        <div class="font-semibold text-blue-800 mb-2">Current Diagnostic:</div>
                        <div id="activeDiagnosticInfo" class="space-y-1">Loading...</div>
                    </div>
                </div>

                <!-- Action Button with 3D effects -->
                <div class="flex justify-center">
                    <!-- OK Button -->
                    <button onclick="closeActiveDiagnosticModal()"
                        class="w-full bg-gradient-to-b from-[#6C757D] to-[#5a6268] text-white font-semibold py-3 px-4 rounded-xl border-b-4 border-[#4e555b] shadow-[0_6px_12px_rgba(0,0,0,0.2)] hover:scale-[1.02] hover:shadow-[0_8px_16px_rgba(0,0,0,0.3)] active:scale-[0.98] active:shadow-[0_4px_8px_rgba(0,0,0,0.2)] transition-all duration-200 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none">
                        </div>
                        <span class="relative z-10">OK</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        #activeDiagnosticModal {
            display: none;
        }

        #activeDiagnosticModal.show {
            display: flex !important;
        }

        #activeDiagnosticModal.show #activeDiagnosticModalContent {
            transform: scale(1);
        }
    </style>


    <script>
        let activeDiagnostics = []; // Initialize as empty array

        document.addEventListener('DOMContentLoaded', function () {
            // Animate Number & Algebra progress bar
            const progressBarNA = document.getElementById('progress-bar-na');
            const progressNA = {{ $naProgress }};
            setTimeout(() => {
                progressBarNA.style.width = progressNA + '%';
            }, 100);

            // Animate Measurement & Geometry progress bar
            const progressBarMG = document.getElementById('progress-bar-mg');
            const progressMG = {{ $mgProgress }};
            setTimeout(() => {
                progressBarMG.style.width = progressMG + '%';
            }, 200);

            // Animate Data & Probability progress bar
            const progressBarDP = document.getElementById('progress-bar-dp');
            const progressDP = {{ $dpProgress }};
            setTimeout(() => {
                progressBarDP.style.width = progressDP + '%';
            }, 300);
        });

        // Check for active diagnostics before starting new diagnostic
        function checkDiagnosticBeforeStart(category) {
            console.log('Checking for active diagnostics for category:', category);

            // Show loading state (optional)
            const button = event.target;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = 'Checking...';

            // Check if this is a resume button (if text contains "Resume")
            const isResumeButton = originalText.includes('Resume');

            if (isResumeButton) {
                // If it's a resume button, proceed directly to the diagnostic
                console.log('Resume button clicked, proceeding to diagnostic');
                window.location.href = `/student/quiz/diagnostic/${category}`;
                return;
            }

            // For "Take Diagnostic" buttons, check for active diagnostics in ANY competency
            fetch('{{ route("student.quiz.check-active-diagnostics") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    category: category
                })
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Check diagnostics response:', data);

                    if (data.success) {
                        if (data.has_active_diagnostics && data.active_diagnostics.length > 0) {
                            console.log('Active diagnostics found:', data.active_diagnostics);
                            // Show active diagnostic modal to prevent starting new diagnostic
                            activeDiagnostics = data.active_diagnostics;
                            showActiveDiagnosticModal();
                        } else {
                            console.log('No active diagnostics, proceeding to new diagnostic');
                            // No active diagnostics, proceed to start new diagnostic
                            window.location.href = `/student/quiz/diagnostic/${category}`;
                        }
                    } else {
                        console.error('Error checking diagnostics:', data.message);
                        // Error checking diagnostics, proceed anyway
                        window.location.href = `/student/quiz/diagnostic/${category}`;
                    }
                })
                .catch(error => {
                    console.error('Error checking diagnostics:', error);
                    // Error in request, proceed anyway
                    window.location.href = `/student/quiz/diagnostic/${category}`;
                })
                .finally(() => {
                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = originalText;
                });
        }

        // Show active diagnostic notification modal
        function showActiveDiagnosticModal() {
            const modal = document.getElementById('activeDiagnosticModal');
            const activeDiagnostic = activeDiagnostics[0]; // Get the first (should be only) active diagnostic

            // Update the diagnostic info display
            const diagnosticInfo = document.getElementById('activeDiagnosticInfo');
            if (activeDiagnostic) {
                diagnosticInfo.innerHTML = `
                                                                                                                                                        <div><strong>Test:</strong> ${activeDiagnostic.title}</div>
                                                                                                                                                        <div><strong>Phase:</strong> ${activeDiagnostic.phase_name} (Phase ${activeDiagnostic.current_phase})</div>
                                                                                                                                                        <div><strong>Progress:</strong> ${activeDiagnostic.progress}/${activeDiagnostic.total_questions} questions</div>
                                                                                                                                                        <div><strong>Started:</strong> ${new Date(activeDiagnostic.started_at).toLocaleString()}</div>
                                                                                                                                                    `;
            }

            // Show modal with animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        // Close active diagnostic modal
        function closeActiveDiagnosticModal() {
            const modal = document.getElementById('activeDiagnosticModal');
            modal.classList.remove('show');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('activeDiagnosticModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeActiveDiagnosticModal();
            }
        });
    </script>

@endsection