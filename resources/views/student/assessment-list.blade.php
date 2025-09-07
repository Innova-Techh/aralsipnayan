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

    {{-- Return to Assessments --}}
    <div class="mb-1">
        <a href="{{ route('assessments.index') }}" 
        class="inline-flex items-center gap-2 p-3 rounded-full hover:scale-110 transition-transform duration-200">
            <!-- Arrow SVG -->
            <div class="w-6 h-6">
                <svg viewBox="0 0 24 24" class="w-full h-full">
                    <defs>
                        <linearGradient id="arrowGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#4338CA"/>
                            <stop offset="100%" stop-color="#9333EA"/>
                        </linearGradient>
                    </defs>
                    <path d="M15 18l-6-6 6-6" 
                        stroke="url(#arrowGradient)" 
                        stroke-width="3" 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        fill="none"/>
                </svg>
            </div>

            <!-- Text next to arrow -->
            <span class="text-indigo-500 font-baloo font-extrabold text-xl">Back to Assessments</span>
        </a>
    </div>


    <!-- Category Card -->
    <div class="relative overflow-hidden">
        <div class="mx-auto max-w-10xl rounded-2xl text-white transition-all duration-300 
                    border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
            style="
                background: linear-gradient(to bottom, #4338CA, #9333EA);
                min-height: clamp(70px, 5vw + 30px, 100px);     ">
            
            <!-- Mobile Layout: Vertical Stack -->
            <div class="lg:hidden relative z-10 h-full flex flex-col justify-center
                        pl-6 sm:pl-10 md:pl-16 lg:pl-24
                        pt-2 sm:pt-6 md:pt-8 lg:pt-6">
                
                <!-- Title Row -->
                <div class="flex items-center gap-3 sm:gap-4 mb-1">
                    <span class="text-xl sm:text-2xl md:text-3xl lg:text-4xl">{{ $data['icon'] }}</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl lg:text-4xl font-baloo font-extrabold leading-tight">
                        {{ $data['title'] }}
                    </h2>
                </div>
                
            <!-- Status Badge - Mobile -->
            <div class="ml-[2.5rem] mb-[1rem] sm:ml-[3rem] md:ml-[3.5rem] lg:ml-[4.5rem]">
            <span
                class="inline-flex items-center justify-center px-4 py-1 rounded-full text-xs font-medium
                    bg-white/20 text-white border border-white/30
                    lg:text-lg lg:px-6 lg:py-2 lg:min-w-[5.2rem] lg:h-10"
            >
                Intermediate
            </span>
            </div>

            </div>

            <!-- Desktop Layout: Horizontal -->
            <div class="hidden lg:flex relative z-10 h-full items-center justify-between
                        pl-6 sm:pl-10 md:pl-16 lg:pl-24 pr-6
                        pt-2 sm:pt-6 md:pt-8 lg:pt-6">
                
                <!-- Left: Title -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="text-xl sm:text-2xl md:text-3xl lg:text-4xl">{{ $data['icon'] }}</span>
                    <h2 class="text-lg sm:text-xl md:text-2xl lg:text-4xl font-baloo font-extrabold leading-tight">
                        {{ $data['title'] }}
                    </h2>
                </div>
                
                <!-- Right: Status Badge -->
                <div>
                    <span class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium 
                            bg-white/20 text-white border border-white/30">
                        Intermediate
                    </span>
                </div>
            </div>
        </div>
    </div>


    <!-- Assessment List Content -->
    <div class="mx-auto max-w-10xl">
        <!-- Add your assessment list content here -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 font-baloo ">
            <!-- Sample assessment items -->
            <div class="w-full max-w-sm bg-gradient-to-br from-[#2077AF] to-[#4720AF]
                        rounded-xl border-b-4 border-[#0b1d30] shadow-lg
                        transition-all p-4 flex flex-col justify-between h-auto">

                <!-- Title + Time Row -->
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-bold text-white">Sample Assessment 1</h3>
                    <div class="flex items-center gap-1 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#FF6B6B]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8a1 1 0 0 1 1 1v3.28l2.72 1.64a1 1 0 1 1-1.04 1.72l-3.2-1.92A1 1 0 0 1 11 13V9a1 1 0 0 1 1-1zm0-6a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/>
                        </svg>
                        <span class="text-sm font-semibold">30 mins</span>
                    </div>
                </div>

                <!-- Attributes Section -->
                <div class="space-y-3 mb-4">

                    <!-- Number of Questions -->
                    <div class="flex items-center gap-3">
                        <!-- Questions Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#4ADE80]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 5h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2zm0 6h18a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2z"/>
                        </svg>
                        <span class="text-lg text-white font-bold font-baloo">10 Questions</span>
                    </div>

                    <!-- Difficulty Tag -->
                    <div class="flex items-center gap-3">
                        <!-- Difficulty Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#FFD93D]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 .587l3.668 7.571 8.332 1.151-6.064 5.879 1.524 8.229L12 18.897l-7.46 4.52 1.524-8.229L0 9.309l8.332-1.151z"/>
                        </svg>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                                    bg-yellow-200 text-yellow-900 shadow-md">
                            Intermediate
                        </span>
                    </div>
                </div>

                <!-- Trigger Button -->
                <button onclick="openModal()" 
                    class="w-full bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white py-2 rounded-xl font-semibold 
                    shadow-[0_4px_0_#c03f00] hover:scale-[1.03] transition-all duration-200">
                    Start Assessment
                </button>
            </div>
   
        </div>
    </div>



</div>

<!-- Modal Background -->
<div id="assessmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden px-4 sm:px-0">
    <!-- Modal Container -->
    <div class="w-full max-w-lg sm:max-w-lg md:max-w-xl lg:max-w-md xl:max-w-md bg-[#FFF7E6] 
                rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden sm:mx-auto 
                max-h-[90vh] sm:max-h-none flex flex-col">

        <!-- Header -->
        <div class="bg-gradient-to-r from-[#2077AF] to-[#4720AF] p-3 sm:p-4 flex items-center gap-2">
            <!-- Icon for Title -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-6.5A9.985 9.985 0 0012 2a9.985 9.985 0 00-9 5.5M12 22a9.985 9.985 0 009-5.5" />
            </svg>
            <h2 class="text-lg sm:text-xl font-extrabold text-white">Geometry</h2>
        </div>

        <!-- Content -->
        <div class="p-4 sm:p-6 overflow-y-auto">
            
            <!-- Topic Overview -->
            <div class="mb-4 sm:mb-5">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Topic Overview</h3>
                <p class="text-gray-700 text-xs sm:text-sm leading-relaxed mt-1 sm:mt-2">
                    Explore the fascinating world of shapes, angles, lines, and spatial relationships.
                    This competency covers fundamental geometric concepts including area,
                    perimeter, volume, and coordinate geometry.
                </p>
            </div>

            <!-- Time & Questions Cards -->
            <div class="flex gap-3 mb-5">
                <!-- Time Limit Card -->
                <div class="flex-1 bg-gradient-to-b from-[#F5A623] to-[#F5D70B] rounded-xl shadow-[0_5px_0px_rgba(0,0,0,0.25)] p-3 text-white flex flex-col items-center">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs sm:text-sm font-medium">Time Limit</span>
                    </div>
                    <span class="text-sm sm:text-base font-bold mt-1">25 minutes</span>
                </div>

                <!-- Number of Questions Card -->
                <div class="flex-1 bg-gradient-to-b from-[#34D399] to-[#059669] rounded-xl shadow-[0_5px_0px_rgba(0,0,0,0.25)] p-3 text-white flex flex-col items-center">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-xs sm:text-sm font-medium">Questions</span>
                    </div>
                    <span class="text-sm sm:text-base font-bold mt-1">20 Questions</span>
                </div>
            </div>

            <!-- Instructions -->
            <div>
                <h3 class="flex items-center gap-2 text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">
                    <!-- Icon for Instructions -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-6 sm:h-6 text-[#2077AF]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 2V7a2 2 0 00-2-2h-3l-2-3H10L8 5H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2z" />
                    </svg>
                    Instructions
                </h3>
                <ul class="list-disc list-inside text-gray-700 space-y-1 sm:space-y-2 text-xs sm:text-sm">
                    <li>Read each question carefully before answering</li>
                    <li>You can use hints, but they will cost points</li>
                    <li>Submit your answer to see your score</li>
                    <li>You cannot go back to previous questions</li>
                    <li>Complete all questions within the time limit</li>
                </ul>
            </div>

            <!-- Highlighted Info -->
            <div class="bg-gradient-to-r from-[#3B82F6] to-[#06B6D4] text-white text-center text-xs sm:text-sm font-medium rounded-xl p-2 sm:p-3 mt-4 sm:mt-5 border-b-4 border-[#135177] shadow-lg">
                Every question is a chance to show what you know. You've got this! 💡
            </div>

            <!-- Start Button -->
            <a href="{{ route('quiz.start', $category) }}" 
            class="block w-full bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white text-sm sm:text-lg font-semibold py-3 mt-5 rounded-2xl border-b-4 border-[#922f26] shadow-lg hover:scale-[1.03] transition-all duration-300 text-center">
                Start Assessment
            </a>

            <!-- Close Button -->
            <button onclick="closeModal()" 
                class="w-full text-center text-xs sm:text-sm text-gray-600 mt-2 sm:mt-3 hover:underline">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- JavaScript for Modal -->
<script>
    function openModal() {
        document.getElementById("assessmentModal").classList.remove("hidden");
    }
    function closeModal() {
        document.getElementById("assessmentModal").classList.add("hidden");
    }
</script>


@endsection