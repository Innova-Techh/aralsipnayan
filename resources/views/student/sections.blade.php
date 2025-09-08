@extends('layouts.user_layout')

@section('title', 'My Section - AralSipnayan')

@section('content')
<style>
.hero-bg {
    background-image: url('{{ asset('images/section/bg2.png') }}');
}

@media (min-width: 1024px) {
    .hero-bg {
        background-image: url('{{ asset('images/section/bg.png') }}');
    }
}
</style>
<div class="space-y-6 sm:space-y-8">
    <!-- Welcome Header (Hero) -->
    <div class="relative -mx-6 sm:-mx-8 lg:-mx-12 p-5 sm:p-6 text-white overflow-hidden bg-center bg-cover hero-bg">
        <div class="relative z-10 px-6 sm:px-8 lg:px-12">
            <h1 class="text-[22px] sm:text-lg md:text-2xl lg:text-4xl xl:text-5xl font-baloo font-extrabold leading-tight">
                My Section 🏆
            </h1>
            <p class="text-[10px] sm:text-sm md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">Your skills update and assessment quest in one place!</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Right Column - Teacher's Board (appears first on mobile) -->
        <div class="space-y-4 lg:order-2">
        <div class="rounded-xl px-12">

                <!-- Teacher's Board Header -->
                <div class="pr-24">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-2xl p-4 text-white shadow-lg">
                        <h2 class="text-xl font-baloo font-bold">Teacher's Board! 📌</h2>
                        <p class="text-blue-100 text-sm">Latest updates from your teacher</p>
                    </div>
                </div>

                <!-- Teacher's Messages -->
            
                <div class="space-y-4">
                    <!-- Upcoming Math Competition -->
                     <div class="rounded-tr-2xl rounded-bl-2xl rounded-br-2xl p-5 shadow-lg border border-black" style="background-color: #D1DDFF;">
                         <div class="flex items-center justify-between mb-3">
                             <h4 class="font-bold text-gray-800 text-sm sm:text-base lg:text-lg">Upcoming Math Competition</h4>
                             <span class="bg-blue-500 text-white text-xs sm:text-sm px-3 py-1 rounded-full font-medium">10 DAYS</span>
                         </div>
                         <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed mb-4">
                             Students who are interested in joining the inter-school 
                             math competition should submit their names by Friday. 
                             This is a great opportunity to showcase your 
                             mathematical skills!
                         </p>
                         <p class="text-gray-500 text-xs sm:text-sm font-medium">Ms. Rodriguez</p>
                     </div>

                    <!-- Assignment Reminder -->
                    <div class="rounded-tr-2xl rounded-bl-2xl rounded-br-2xl p-5 shadow-lg border border-black" style="background-color: #D1DDFF;">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-800 text-sm sm:text-base lg:text-lg">Assignment Reminder</h4>
                            <span class="bg-blue-500 text-white text-xs sm:text-sm px-3 py-1 rounded-full font-medium">3 DAYS</span>
                        </div>
                        <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed mb-4">
                            Don't forget to complete your function worksheets. They 
                            are due next Monday. If you need help, please don't 
                            hesitate to ask during office hours.
                        </p>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Ms. Rodriguez</p>
                    </div>

                    <!-- Study Group Session -->
                    <div class="rounded-tr-2xl rounded-bl-2xl rounded-br-2xl p-5 shadow-lg border border-black" style="background-color: #D1DDFF;">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-800 text-sm sm:text-base lg:text-lg">Study Group Session</h4>
                            <span class="bg-blue-500 text-white text-xs sm:text-sm px-3 py-1 rounded-full font-medium">5 DAYS</span>
                        </div>
                        <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed mb-4">
                            Extra study session for Geometry will be held this Saturday 
                            at 2:00 PM in room 205. Bring your notebooks and 
                            calculators.
                        </p>
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Ms. Rodriguez</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column - Quest Board (appears second on mobile) -->
        <div class="lg:col-span-2 space-y-8 lg:order-1">
            <!-- Quest Board -->
            <div class="rounded-xl px-12">
                <div class="space-y-4">
                    <!-- Quest Board Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center">
                            <h2 class="text-2xl font-baloo font-bold mr-3">Quest Board 🎮</h2>
                        </div>
                        <p class="text-blue-100 text-sm mt-1">Complete your epic math adventures</p>
                    </div>

                    <!-- Quest Cards -->
                    <div class="shadow-lg px-4 pb-2 rounded-xl space-y-4">
                        <!-- Geometry Fundamentals -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-xl ">
                            <h3 class="text-2xl font-baloo font-bold mb-2">Geometry Fundamentals</h3>
                            <p class="text-white/90 text-sm mb-6">Assessment covering basic geometric shapes and properties</p>
                            
                            <!-- Stats Row -->
                            <div class="flex items-center justify-center rounded-xl py-4 bg-gray-100/50 space-x-4 sm:space-x-8 mb-6">
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">150</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">XP REWARD</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">25 mins</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">TIME LIMIT</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">Medium</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">DIFFICULTY</div>
                                </div>
                            </div>
                            
                            <!-- Start Assessment Button -->
                            <button class="w-full bg-gradient-to-r from-orange-400 to-red-500 hover:from-orange-500 hover:to-red-600 text-white font-baloo font-bold py-4 px-6 rounded-xl shadow-lg text-lg">
                                Start Assessment
                            </button>
                        </div>

                        <!-- Fractions and Decimals -->
                        <div class="bg-gradient-to-r from-green-500 to-teal-500 rounded-2xl p-6 text-white shadow-xl hover:shadow-xl">
                            <h3 class="text-2xl font-baloo font-bold mb-2">Fractions and Decimals</h3>
                            <p class="text-white/90 text-sm mb-6">Practice assessment on converting fractions to decimals</p>
                            
                            <!-- Stats Row -->
                            <div class="flex items-center justify-center rounded-xl py-4 bg-gray-100/50 space-x-4 sm:space-x-8 mb-6">
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">150</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">XP REWARD</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">25 mins</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">TIME LIMIT</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">Easy</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">DIFFICULTY</div>
                                </div>
                            </div>
                            
                            <!-- Start Assessment Button -->
                            <button class="w-full bg-gradient-to-r from-orange-400 to-red-500 hover:from-orange-500 hover:to-red-600 text-white font-baloo font-bold py-4 px-6 rounded-xl shadow-lg text-lg">
                                Start Assessment
                            </button>
                        </div>

                        <!-- Number Operations Quiz -->
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-xl">
                            <h3 class="text-2xl font-baloo font-bold mb-2">Number Operations Quiz</h3>
                            <p class="text-white/90 text-sm mb-6">Quick assessment on basic arithmetic operations</p>
                            
                            <!-- Stats Row -->
                            <div class="flex items-center justify-center rounded-xl py-4 bg-gray-100/50 space-x-4 sm:space-x-8 mb-6">
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">150</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">XP REWARD</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">25 mins</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">TIME LIMIT</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl sm:text-2xl lg:text-3xl font-bold mb-1">Hard</div>
                                    <div class="text-xs sm:text-xs lg:text-sm font-medium opacity-90">DIFFICULTY</div>
                                </div>
                            </div>
                            
                            <!-- Start Assessment Button -->
                            <button class="w-full bg-gradient-to-r from-orange-400 to-red-500 hover:from-orange-500 hover:to-red-600 text-white font-baloo font-bold py-4 px-6 rounded-xl shadow-lg text-lg">
                                Start Assessment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection