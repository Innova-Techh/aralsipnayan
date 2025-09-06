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
    <span class="inline-flex items-center px-4 py-1 rounded-full text-xs font-medium 
               bg-white/20 text-white border border-white/30">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Sample assessment items -->
            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
                <h3 class="text-lg font-semibold mb-2">Sample Assessment 1</h3>
                <p class="text-gray-600 text-sm mb-4">Description of the assessment</p>
                <button class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    Start Assessment
                </button>
            </div>
            <!-- Repeat for more assessments -->
        </div>
    </div>
</div>

@endsection