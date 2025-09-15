@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')

    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
        <div class="min-h-screen bg-gray-100">
            <!-- Top Section with Blue Background -->
            <div
                class="relative -mx-4 sm:-mx-6 lg:-mx-8 pt-2 sm:pt-2 overflow-hidden bg-gradient-to-b bg-center bg-cover from-blue-600 to-blue-700 text-white">
                <!-- Toggle Switch -->
                <div class="flex justify-center pt-6 pb-4">
                    <div class="bg-white rounded-full p-1 flex">
                        <button
                            class="px-6 py-2 rounded-full bg-primary-blue text-white font-medium text-sm transition-all">
                            Section
                        </button>
                        <button class="px-6 py-2 rounded-full text-primary-blue font-medium text-sm transition-all">
                            School
                        </button>
                    </div>
                </div>

                <!-- Podium Section -->
                <div class="px-6">
                    <div class="flex flex-col items-center">
                        <!-- Top Three -->
                        <div class="flex items-end justify-center space-x-6">
                            <!-- Second Place -->
                            <div class="flex flex-col items-center transform translate-y-4 ">
                                <div
                                    class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mb-2 border-4 border-white shadow-lg">
                                    <span class="text-gray-600 font-bold text-lg">M</span>
                                </div>
                                <div class="text-center mb-2">
                                    <div class="font-semibold text-sm text-white">Mary Soberano</div>
                                    <div class="text-yellow-300 text-xs">1,000 pts</div>
                                </div>
                            </div>

                            <!-- First Place -->
                            <div class="flex flex-col items-center ">
                                <div
                                    class="w-20 h-20 bg-red-400 rounded-full flex items-center justify-center mb-2 border-4 border-white shadow-lg">
                                    <span class="text-white font-bold text-xl">J</span>
                                </div>
                                <div class="text-center mb-2">
                                    <div class="font-semibold text-white">John Llyod</div>
                                    <div class="text-yellow-300 text-sm">1,250 pts</div>
                                </div>
                            </div>

                            <!-- Third Place -->
                            <div class="flex flex-col items-center transform translate-y-4">
                                <div
                                    class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mb-2 border-4 border-white shadow-lg">
                                    <span class="text-gray-600 font-bold text-lg">C</span>
                                </div>
                                <div class="text-center mb-2">
                                    <div class="font-semibold text-sm text-white">Christian Alexis</div>
                                    <div class="text-yellow-300 text-xs">700 pts</div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Podium -->
                        <div class="relative">
                            <img src="{{ asset('images/leaderboards/Group 26.png') }}" alt="podium"
                                class="w-max h-max object-cover">
                            <!-- 2nd place background -->
                            <img src="{{ asset('images/leaderboards/Group 25.png') }}" alt="2nd place bg" class="absolute"
                                style="left: 60px; top: 81%; width: max; height: max; z-index: 10; transform: translate(-50%, -80%);">
                            <!-- 3rd place background -->
                            <img src="{{ asset('images/leaderboards/Group 24.png') }}" alt="3rd place bg" class="absolute"
                                style="right: 60px; top: 81%; width: max; height: max; z-index: 10; transform: translate(50%, -80%);">
                            <!-- Position numbers with proper centering -->
                            <span
                                class="absolute inset-0 flex items-center justify-center text-white font-bold text-4xl drop-shadow-lg transform translate-y-[-10px]">1</span>
                            <!-- 2nd place number - centered on left podium -->
                            <span class="absolute text-white font-bold text-2xl drop-shadow-lg"
                                style="left: 55px; top: 60%; transform: translate(-50%, -50%); z-index: 20;">2</span>
                            <!-- 3rd place number - centered on right podium -->
                            <span class="absolute text-white font-bold text-2xl drop-shadow-lg"
                                style="right: 55px; top: 60%; transform: translate(50%, -50%); z-index: 20;">3</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section with Ranked List -->
            <div class="relative -mx-4 sm:-mx-6 lg:-mx-8  p-5 sm:p-6 bg-white rounded-2xl -mt-4 sm:-mt-4 lg:-mt-4 z-10">
                <div class="bg-white px-6 py-6 space-y-3">
                    <!-- Ranked List Items -->
                    <div class="bg-blue-500 rounded-xl p-4 shadow-md flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-600 font-bold">A</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Anderson Silva</div>
                            <div class="text-blue-100 text-sm">VI - Sampaguita</div>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            698 pts
                        </div>
                    </div>

                    <div class="bg-blue-500 rounded-xl p-4 shadow-md flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-600 font-bold">K</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Kate Villamor</div>
                            <div class="text-blue-100 text-sm">VI - Orchid</div>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            698 pts
                        </div>
                    </div>

                    <div class="bg-blue-500 rounded-xl p-4 shadow-md flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-600 font-bold">A</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Angel Lopez</div>
                            <div class="text-blue-100 text-sm">VI - Yellow Bell</div>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            698 pts
                        </div>
                    </div>

                    <div class="bg-blue-500 rounded-xl p-4 shadow-md flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-600 font-bold">J</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Johnson Spear</div>
                            <div class="text-blue-100 text-sm">VI - Jasmin</div>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            698 pts
                        </div>
                    </div>

                    <!-- Continue with more entries as needed -->
                    <div class="bg-blue-500 rounded-xl p-4 shadow-md flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-600 font-bold">S</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Sarah Johnson</div>
                            <div class="text-blue-100 text-sm">VI - Rose</div>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            650 pts
                        </div>
                    </div>

                    <div class="bg-blue-500 rounded-xl p-4 shadow-md flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-600 font-bold">M</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-semibold">Michael Chen</div>
                            <div class="text-blue-100 text-sm">VI - Lily</div>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            620 pts
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection