@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<style>
.hero-bg {
    background-image: url('{{ asset('images/dashboard/bg.png') }}');
}

@media (min-width: 1024px) {
    .hero-bg {
        background-image: url('{{ asset('images/dashboard/bg1.png') }}');
    }
}
</style>
<div class="space-y-6 sm:space-y-8">
    <!-- Welcome Header (Hero) -->
    <div class="relative -mx-6 sm:-mx-8 lg:-mx-12 p-5 sm:p-6 text-white overflow-hidden bg-center bg-cover hero-bg">
        <div class="relative z-10 px-6 sm:px-8 lg:px-12">
            <h1 class="text-[22px] sm:text-lg md:text-2xl lg:text-4xl xl:text-5xl font-baloo font-extrabold leading-tight">
                Welcome back, {{ Auth::user()->username }}! 👋
            </h1>
            <p class="text-[10px] sm:text-sm md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">Ready to continue your math journey?</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Progress and Continue Learning -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Learning Progress -->
            <div class="rounded-xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center">
                        <div class="w-7 h-7 sm:w-8 sm:h-8  rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Your Learning Progress</h2>
                            <h4 class="text-xs sm:text-sm text-gray-900 mt-1">You're growing into a math master every day!</h3>
                        </div>
                    </div>
                </div>
                
                <!-- Level Card -->
                <div class="mb-5 sm:mb-6">
                    <div class="bg-[#0F1A5B] text-white rounded-xl p-4 sm:p-5 shadow-inner">
                        <div class="flex items-start gap-3">
                            <!-- Placeholder for rocket/level image -->
                            <div class="flex items-center justify-center">
                                <img src="{{ asset('images/dashboard/rank.png') }}" alt="rank" class="w-full h-full sm:w-full sm:h-full rounded-xl">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm sm:text-base font-bold">Problem Solver</div>
                                        <div class="text-[11px] sm:text-xs text-blue-200">Level 3</div>
                                    </div>
                                    <div class="text-[11px] sm:text-xs text-blue-200">460 XP / 1000 XP</div>
                                </div>
                                <!-- Progress bar stylized -->
                                <div class="mt-2 sm:mt-3">
                                    <div class="relative h-2.5 bg-white/15 rounded-full overflow-hidden">
                                        <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-orange-400 to-orange-500" style="width: 46%"></div>
                                    </div>
                                    <div class="mt-2 text-[11px] sm:text-xs text-blue-200">540 XP remaining</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/bookcard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/book.png') }}" alt="Completed" class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">2</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Completed</div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/pointscard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/points.png') }}" alt="Points" class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">1250</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Points</div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/streakcard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/streak.png') }}" alt="Streak" class="w-9 h-9 sm:w-11 sm:h-11 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">8</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Streak</div>
                    </div>
                    
                    <div class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/starcard.png') }}');">
                        <div class="flex items-center justify-center mb-2">
                            <img src="{{ asset('images/dashboard/star.png') }}" alt="Level" class="w-12 h-8 sm:w-14 sm:h-10 object-contain relative z-10">
                        </div>
                        <div class="text-xl sm:text-2xl font-bold relative z-10">3</div>
                        <div class="text-xs sm:text-sm opacity-90 relative z-10">Level</div>
                    </div>
                </div>
            </div>

            <!-- Assigned Assessments -->
            <div class="rounded-xl p-4 sm:p-6">
                    <!-- header -->  
                    <div class="relative -mx-4 sm:-mx-6 -mt-2 px-4 sm:px-6 py-2 rounded-t-2xl overflow-hidden" style="background-image: url('{{ asset('images/dashboard/assessmentsbg.png') }}'); background-size: 100% auto; background-repeat: no-repeat; background-position: center;">                                       
                        <div class="flex items-center justify-between text-white min-h-[60px] px-3 sm:px-5" style="background-image: url('{{ asset('images/dashboard/assessmentsbg2.png') }}'); background-size: 100% auto; background-repeat: no-repeat; background-position: center;">                               
                            <div class="flex items-center">                                 
                                <div class="flex items-center justify-center mr-3 pl-4">                                     
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-white rounded-full flex items-center justify-center">                                         
                                        <span class="text-red-600 font-bold text-lg">🎯</span>                                     
                                    </div>                                 
                                </div>                                 
                                <div class="min-w-0">                                     
                                    <h2 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl font-baloo font-bold truncate drop-shadow">Assigned Assessments</h2>                                     
                                    <h3 class="text-[10px] sm:text-xs lg:text-sm mt-1 opacity-90">                                         
                                        Complete your assigned tasks to earn points and level up!                                     
                                    </h3>                                 
                                </div>                             
                            </div>                             
                            <div class="flex-shrink-0 text-right leading-tight pr-6">                                 
                                <div class="text-xl sm:text-2xl font-extrabold leading-none drop-shadow">2</div>                                 
                                <div class="text-[11px] sm:text-xs opacity-90">Pending</div>                             
                            </div>                         
                        </div>                        
                    </div>

                    <div class="lg:px-4">
                         <!-- Assessments -->   
                        <div class="-mt-4 p-6 rounded-xl space-y-3 bg-rose-50 border-4 sm:space-y-3" style="border-color: #B91E2A;">
                            <!-- Assessment Item 1 -->
                            <div class="rounded-lg p-4 shadow-sm sm:flex sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="font-semibold text-gray-900 text-sm">Evaluate Exponents</h3>
                                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">Individual</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">Learn how to calculate and evaluate expressions with exponents</p>
                                    <div class="flex items-center gap-2 mb-4 sm:mb-0">
                                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-400 text-white font-medium">120 points</span>
                                        <span class="text-xs px-2 py-1 text-pink-700 font-medium flex items-center gap-1">
                                            <span class=" text-pink-500 font-bold">ⓘ</span>
                                            Hard
                                        </span>
                                    </div>
                                </div>
                                <button class="w-full sm:w-auto sm:ml-4 text-white text-sm font-baloo font-semibold px-6 py-3 rounded-xl shadow-sm transition-all hover:shadow-md" style="background: linear-gradient(180deg, #4338CA 0%, #9333EA 100%);">
                                    Start Assessment
                                </button>
                            </div>

                            <!-- Assessment Item 2 -->
                            <div class="rounded-lg p-4 shadow-sm sm:flex sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="font-semibold text-gray-900 text-sm">Evaluate Exponents</h3>
                                        <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 font-medium">Class</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">Learn how to calculate and evaluate expressions with exponents</p>
                                    <div class="flex items-center gap-2 mb-4 sm:mb-0">
                                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-400 text-white font-medium">120 points</span>
                                        <span class="text-xs px-2 py-1 text-green-700 font-medium flex items-center gap-1">
                                            <span class="text-green-500 font-bold">ⓘ</span>
                                            Easy
                                        </span>
                                    </div>
                                </div>
                                <button class="w-full sm:w-auto sm:ml-4 text-white text-sm font-baloo font-semibold px-6 py-3 rounded-xl shadow-sm transition-all hover:shadow-md" style="background: linear-gradient(180deg, #4338CA 0%, #9333EA 100%);">
                                    Start Assessment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- Completed Assessments 
        <div class="rounded-xl p-4 sm:p-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center mr-2 sm:mr-3">
                        <img src="{{ asset('images/dashboard/check.png') }}" alt="Check" class="w-5 h-5 sm:w-12 sm:h-12 object-contain relative z-10">
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900">Completed Assessments</h2>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Mistakes help you learn—keep going!</p>
                    </div>
                </div>

                <div class="space-y-3">
                     Completed Assessment Item 1 
                    <div class="rounded-xl p-4 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/assessment1.png') }}');">
                        <div class="absolute top-0 right-0 bg-yellow-700/90 text-white text-xs font-bold px-3 py-1 rounded-bl-lg backdrop-blur-sm">17/30</div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white text-sm sm:text-base mb-1 drop-shadow-lg">Basic Arithmetic</h3>
                            <p class="text-yellow-100 text-xs sm:text-sm mb-3 drop-shadow-md">Lorem ipsum dolor sit amet</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/90 backdrop-blur-sm text-orange-600 text-xs px-2 py-1 rounded-full font-medium">Intermediate</span>
                                <div class="bg-gray-900/90 flex items-center gap-1 px-4 pr-8 rounded-full">
                                    <img src="{{ asset('images/dashboard/trophy.png') }}" alt="Trophy" class="w-5 h-5 sm:w-5 sm:h-5 object-contain relative z-10">
                                    <span class="text-white text-xs font-medium drop-shadow-md">+ 120 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>

                     Completed Assessment Item 2 
                    <div class="rounded-xl p-4 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/assessment2.png') }}');">
                        <div class="absolute top-0 right-0 bg-green-700/90 text-white text-xs font-bold px-3 py-1 rounded-bl-lg backdrop-blur-sm">25/30</div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white text-sm sm:text-base mb-1 drop-shadow-lg">Basic Arithmetic</h3>
                            <p class="text-green-100 text-xs sm:text-sm mb-3 drop-shadow-md">Lorem ipsum dolor sit amet</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/90 backdrop-blur-sm text-green-600 text-xs px-2 py-1 rounded-full font-medium">Beginner</span>
                                <div class="bg-gray-900/90 flex items-center gap-1 px-4 pr-8 rounded-full">
                                    <img src="{{ asset('images/dashboard/trophy.png') }}" alt="Trophy" class="w-5 h-5 sm:w-5 sm:h-5 object-contain relative z-10">
                                    <span class="text-white text-xs font-medium drop-shadow-md">+ 120 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>

                     Completed Assessment Item 3 
                    <div class="rounded-xl p-4 relative overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/dashboard/assessment3.png') }}');">
                        <div class="absolute top-0 right-0 bg-red-700/90 text-white text-xs font-bold px-3 py-1 rounded-bl-lg backdrop-blur-sm">9/30</div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-white text-sm sm:text-base mb-1 drop-shadow-lg">Basic Arithmetic</h3>
                            <p class="text-red-100 text-xs sm:text-sm mb-3 drop-shadow-md">Lorem ipsum dolor sit amet</p>
                            <div class="flex items-center gap-2">
                                <span class="bg-white/90 backdrop-blur-sm text-red-600 text-xs px-2 py-1 rounded-full font-medium">Advanced</span>
                                <div class="bg-gray-900/90 flex items-center gap-1 px-4 pr-8 rounded-full">
                                    <img src="{{ asset('images/dashboard/trophy.png') }}" alt="Trophy" class="w-5 h-5 sm:w-5 sm:h-5 object-contain relative z-10">
                                    <span class="text-white text-xs font-medium drop-shadow-md">+ 120 pts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
         -->

        <!-- Recent Achievements -->
        
       <!-- Right Column -->         
        <div class="rounded-xl p-4 sm:p-6">  
            <!--achievements-->           
            <div class="space-y-8 p-4">                 
                <div class="rounded-3xl overflow-hidden">                     
                 <!-- Header with background image -->
                    <div class="relative px-8 py-6 shadow-xl" style="background-image: url('{{ asset('images/dashboard/recentach.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center; width: calc(100% + 2rem);">
                        <div class="flex items-center justify-between relative z-10">
                            <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-baloo font-bold text-white drop-shadow-sm">Recent Achievements</h2>
                            <div class=" py-3 px-6" style="background-image: url('{{ asset('images/dashboard/viewall.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center; min-width: 120px; min-height: 40px;">
                                <a href="{{ route('achievements.index') }}" class="text-white text-base font-medium block text-center">
                                    View All
                                </a>
                            </div>
                        </div>
                    </div>         
                    
                    <!-- Achievements grid -->                    
                    <div class="px-2 pb-2 rounded-3xl" style="background-color: #312E81";>                         
                        <div class="-mt-6 p-6 rounded-2xl shadow-4xl" style="background-color: #2841D1;">                             
                            <div class="pt-6 grid grid-cols-3 gap-4 sm:gap-6 max-w-sm mx-auto">                                 
                                @foreach($recentAchievements as $achievement)                                 
                                <div class="text-center p-2 group cursor-pointer transition-transform duration-300 hover:-translate-y-2">
                                    <!-- Container for overlapping circles -->
                                    <div class="relative w-16 h-16 sm:w-18 sm:h-18 mx-auto mb-3">
                                        <!-- Dark background circle (larger, positioned behind) -->
                                        <div class="absolute inset-1 w-16 h-16 sm:w-18 sm:h-18 rounded-full shadow-lg" style="background-color: {{ $achievement['background_dark'] }};"></div>
                                        <!-- Light foreground circle (smaller, positioned in front) -->
                                        <div class="absolute inset-0 w-16 h-16 sm:w-18 sm:h-18 rounded-full flex items-center justify-center shadow-md" style="background-color: {{ $achievement['background_light'] }};">
                                            <img src="{{ asset('images/achievements/' . $achievement['front_image']) }}" alt="{{ $achievement['title'] }}" class="w-10 h-10 sm:w-12 sm:h-12 object-contain drop-shadow-sm">
                                        </div>
                                    </div>
                                    <div class="font-semibold text-white text-xs sm:text-sm md:text-base drop-shadow-sm leading-tight">{{ $achievement['title'] }}</div>
                                </div>                                   
                                @endforeach                             
                            </div>                         
                        </div>                     
                    </div>                
                </div>             
            </div>  
            
             <!-- Leaderboard-->
            <div class="px-4">     
                <div class="relative -mx-4 sm:-mx-6 px-4 sm:px-6 overflow-hidden min-h-[120px] max-h-[150px] flex items-center justify-center z-40" style="background-image: url('{{ asset('images/dashboard/leaderboard.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center top;">
                    <!-- Main header -->
                    <div class="gradient-bg rounded-t-xl px-6 -mt-4 card-shadow relative w-full flex items-center justify-center text-center">
                        <h1 class="text-white text-2xl sm:-mt-4 sm:text-3xl md:text-4xl lg:text-5xl font-baloo font-bold tracking-wide">Leaderboards</h1>
                        <svg class="w-6 h-6 text-yellow-300 ml-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Leaderboard content -->
                <!--Outer Div -->
                <div class="relative px-6 lg:px-6 md:px-4 sm:px-4 -mt-8 lg:-mt-8 md:-mt-12 -z-10">
                <div class="px-2 pb-4 rounded-xl" style="background-color: #9B2C14;">
                    <!--Inner Div -->     
                    <div class="bg-gradient-to-b from-yellow-100 to-orange-50 rounded-b-xl card-shadow p-6 relative">
                        
                        <div class="space-y-3 relative z-10">
                            @php
                                $rankBadges = [
                                    1 => 'bg-gradient-to-b from-yellow-400 to-yellow-600 text-white',
                                    2 => 'bg-gradient-to-b from-gray-300 to-gray-500 text-white',
                                    3 => 'bg-gradient-to-b from-orange-500 to-orange-700 text-white',
                                    4 => 'bg-gradient-to-b from-blue-400 to-blue-600 text-white',
                                    5 => 'bg-gradient-to-b from-teal-400 to-teal-600 text-white',
                                ];
                                $rankLabels = [1=>'1st',2=>'2nd',3=>'3rd',4=>'4th',5=>'5th'];
                            @endphp
                            @foreach(($leaderboardTop5 ?? []) as $index => $row)
                            @php $rank = $index + 1; @endphp
                            <div class="flex items-center justify-between p-3 rounded-lg shadow-sm ">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center mr-3 border-2 {{ $rank === 1 ? 'bg-yellow-500 border-yellow-600' : ($rank === 2 ? 'bg-gray-400 border-gray-500' : ($rank === 3 ? 'bg-orange-600 border-orange-700' : ($rank === 4 ? 'bg-blue-400 border-blue-500' : 'bg-teal-400 border-teal-500'))) }}">
                                        <img src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}" 
                                        alt="{{ Auth::user()->username ?? 'Student' }}" 
                                        class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-baloo font-bold text-gray-900 text-base sm:text-lg md:text-xl">{{ $row['name'] ?? 'Student' }}</div>
                                        <div class="text-sm sm:text-base text-gray-600">{{ $row['points'] ?? 0 }} pts</div>
                                    </div>
                                </div>
                                <div class="px-3 py-1 rounded-full text-sm font-bold shadow-sm {{ $rankBadges[$rank] ?? 'bg-gray-200 text-gray-800' }}">{{ $rankLabels[$rank] ?? $rank.'th' }}</div>
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
@endsection