@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')

<div class="space-y-8">
    <!-- My Assessments Header -->
    
<div class="relative overflow-hidden mt-4 lg:mt-8">
    <div class="mx-auto max-w-10xl text-white bg-center bg-no-repeat rounded-2xl flex items-center" 
         style="background-image: url('{{ asset('images/assessments/bg.png') }}'); 
                background-size: 95% clamp(120px, 10vw + 60px, 200px);
                min-height: clamp(120px, 10vw + 60px, 200px);
                padding-left: clamp(2rem, 8vw, 18rem);">
        <div class="relative z-10 pr-6">
            <h1 class="text-3xl sm:text-3xl md:text-5xl lg:text-5xl leading-tight font-baloo font-extrabold">
                My Assessments
            </h1>
            <p class="text-[10px] sm:text-sm md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">
                Test your mathematical knowledge across different competencies and track your learning progress with adaptive assessments
            </p>
        </div>
    </div>
</div>

<!-- Feature Cards Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <!-- Ready for a Challenge Card -->
        <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#2c014b] shadow-lg"
            style="background-image: url('{{ asset('images/assessments/card1.png') }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 w-[70%] md:w-[60%]">
                <div class="flex items-center mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold">🎯 Ready for a Challenge?</h3>
                </div>
                <p class="text-white mb-2 md:mb-4 text-sm md:text-base">
                    Test your math skills with fun assessments! Choose from geometry, numbers, or fractions and start your learning adventure!
                </p>
            </div>
        </div>


        <!-- Level Up Your Skills Card -->
        <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#922f26] shadow-lg" 
            style="background-image: url('{{ asset('images/assessments/card2.png') }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 w-[70%] md:w-[60%]">
                <div class="flex items-center mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold">🌟 Level Up Your Skills!</h3>
                </div>
                <p class="text-white mb-2 md:mb-4 text-sm md:text-base">Adaptive learning just for you. Our smart system adjusts questions to match your learning pace perfectly!</p>
            </div>
        </div>

        <!-- Learning is Fun Card -->
        <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#396697] shadow-lg" 
            style="background-image: url('{{ asset('images/assessments/card3.png') }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 w-[70%] md:w-[60%]">
                <div class="flex items-center mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold">🎮 Learning is Fun!</h3>
                </div>
                <p class="text-white mb-2 md:mb-4 text-sm md:text-base">Gamified math assessments. Earn points, unlock achievements, and compete with friends while learning!</p>
            </div>
        </div>
</div>

    <!-- Assessment Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Number and Algebra Card -->
        <div class="rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
        style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            <!-- Background Vector (kept on top for visual enhancement) -->
            <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
            </div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="relative w-[100%]">
                    <h3 class="text-lg font-bold text-white mb-1">Number and Algebra</h3>
                    <p class="text-sm text-gray-100">Test your knowledge of numbers, operations, and algebraic concepts</p>
                </div>
                @php
                    $naMastery = $masteryData['Number_Algebra'] ?? null;
                    $naLevel = $naMastery ? $naMastery->current_difficulty_level : 'Beginner';
                    $naProgress = $naMastery ? round($naMastery->mastery_probability * 100) : 30;
                    $naHasDiagnostic = $naMastery ? $naMastery->has_taken_diagnostic : false;
                @endphp
                <span class="bg-{{ $naLevel === 'Beginner' ? 'green' : ($naLevel === 'Intermediate' ? 'orange' : 'red') }}-100 
                            text-{{ $naLevel === 'Beginner' ? 'green' : ($naLevel === 'Intermediate' ? 'orange' : 'red') }}-700 
                            text-xs font-medium px-2 py-1 rounded-full">
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
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
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
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
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
                <a href="{{ route('student.quiz.diagnostic', 'Number_Algebra') }}" 
            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                    Take Diagnostic Test
                </a>
            @else
                <a href="{{ route('student.assessments.category', 'Number_Algebra') }}" 
            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                    View Assessments
                </a>
            @endif
        </div>

        <!-- Measurement and Geometry Card -->
        <div class="rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
        style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            <!-- Background Vector (kept on top for visual enhancement) -->
            <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="flex justify-between items-start mb-4 relative z-20">
                <div class=" relative w-[100%]">
                    <h3 class="text-lg font-bold text-white mb-1">Measurement and Geometry</h3>
                    <p class="text-sm text-gray-100">Test your knowledge of shapes, angles, and spatial relationships</p>
                </div>
                @php
                    $mgMastery = $masteryData['Measurement_Geometry'] ?? null;
                    $mgLevel = $mgMastery ? $mgMastery->current_difficulty_level : 'Beginner';
                    $mgProgress = $mgMastery ? round($mgMastery->mastery_probability * 100) : 30;
                    $mgHasDiagnostic = $mgMastery ? $mgMastery->has_taken_diagnostic : false;
                @endphp
                <span class="bg-{{ $mgLevel === 'Beginner' ? 'green' : ($mgLevel === 'Intermediate' ? 'orange' : 'red') }}-100 text-{{ $mgLevel === 'Beginner' ? 'green' : ($mgLevel === 'Intermediate' ? 'orange' : 'red') }}-700 text-xs font-medium px-2 py-1 rounded-full relative z-20">{{ $mgLevel }}</span>
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
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
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
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
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
            <a href="{{ route('student.quiz.diagnostic', 'Measurement_Geometry') }}" 
            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                Take Diagnostic Test
            </a>
        @else
            <a href="{{ route('student.assessments.category', 'Measurement_Geometry') }}" 
            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)] text-center border-b-[6px] border-[#264566] shadow-lg"
            style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                View Assessments
            </a>
        @endif
    </div>

        <!-- Data and Probability Card -->
           <div class="rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
        style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            <!-- Background Vector (kept on top for visual enhancement) -->
            <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
            </div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class=" relative w-[100%]">
                    <h3 class="text-lg font-bold text-white mb-1">Data and Probability</h3>
                    <p class="text-sm text-gray-100">Explore data tables, bar graphs, line plots, mean, and chance events</p>
                </div>
                @php
                    $dpMastery = $masteryData['Data_Probability'] ?? null;
                    $dpLevel = $dpMastery ? $dpMastery->current_difficulty_level : 'Beginner';
                    $dpProgress = $dpMastery ? round($dpMastery->mastery_probability * 100) : 30;
                    $dpHasDiagnostic = $dpMastery ? $dpMastery->has_taken_diagnostic : false;
                @endphp
                <span class="bg-{{ $dpLevel === 'Beginner' ? 'green' : ($dpLevel === 'Intermediate' ? 'orange' : 'red') }}-100 text-{{ $dpLevel === 'Beginner' ? 'green' : ($dpLevel === 'Intermediate' ? 'orange' : 'red') }}-700 text-xs font-medium px-2 py-1 rounded-full">{{ $dpLevel }}</span>
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
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
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
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
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
            <a href="{{ route('student.quiz.diagnostic', 'Data_Probability') }}" 
            class="block w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-center border-b-[6px] border-[#922f26] shadow-lg">
                Take Diagnostic Test
            </a>
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

<script>
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
</script>

@endsection