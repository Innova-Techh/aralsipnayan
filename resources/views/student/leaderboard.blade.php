@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')

    <style>
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

        .skeleton-circle {
            border-radius: 50%;
        }

        .content-loaded {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 lg:pb-8">
        <!-- Loading Skeleton (Initially visible) -->
        <div id="leaderboardSkeleton">
            <div class="min-h-screen bg-gray-100">
                <!-- Top Section Skeleton -->
                <div
                    class="relative -mx-4 sm:-mx-6 lg:-mx-8 pt-2 sm:pt-2 overflow-hidden bg-gradient-to-b bg-center bg-cover from-blue-600 to-blue-700 text-white">
                    <!-- Toggle Switch Skeleton -->
                    <div class="flex justify-center pt-6 pb-4">
                        <div class="bg-white rounded-full p-1 flex">
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                            <div class="skeleton w-20 h-8 rounded-full ml-1"></div>
                        </div>
                    </div>

                    <!-- Podium Section Skeleton -->
                    <div class="px-6">
                        <div class="flex flex-col items-center">
                            <!-- Top Three Skeleton -->
                            <div class="flex items-end justify-center space-x-6 mb-4">
                                <!-- Second Place Skeleton -->
                                <div class="flex flex-col items-center transform translate-y-4">
                                    <div class="w-16 h-16 skeleton-dark rounded-full mb-2"></div>
                                    <div class="skeleton-dark skeleton-text w-24 mb-1"></div>
                                    <div class="skeleton-dark skeleton-text w-16"></div>
                                </div>

                                <!-- First Place Skeleton -->
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 skeleton-dark rounded-full mb-2"></div>
                                    <div class="skeleton-dark skeleton-text w-28 mb-1"></div>
                                    <div class="skeleton-dark skeleton-text w-20"></div>
                                </div>

                                <!-- Third Place Skeleton -->
                                <div class="flex flex-col items-center transform translate-y-4">
                                    <div class="w-16 h-16 skeleton-dark rounded-full mb-2"></div>
                                    <div class="skeleton-dark skeleton-text w-24 mb-1"></div>
                                    <div class="skeleton-dark skeleton-text w-16"></div>
                                </div>
                            </div>

                            <!-- Podium Image Placeholder -->
                            <div class="skeleton-dark w-full h-40 rounded-lg"></div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section Skeleton -->
                <div class="relative -mx-4 sm:-mx-6 lg:-mx-8 p-5 sm:p-6 bg-white rounded-2xl -mt-4 sm:-mt-4 lg:-mt-4 z-10">
                    <div class="bg-white px-6 lg:px-24 py-6 space-y-5">
                        <!-- Ranked List Items Skeleton -->
                        <div class="bg-gray-200 rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 skeleton-circle skeleton mr-4"></div>
                            <div class="flex-1">
                                <div class="skeleton skeleton-text w-32 mb-2"></div>
                                <div class="skeleton skeleton-text w-24"></div>
                            </div>
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                        </div>

                        <div class="bg-gray-200 rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 skeleton-circle skeleton mr-4"></div>
                            <div class="flex-1">
                                <div class="skeleton skeleton-text w-32 mb-2"></div>
                                <div class="skeleton skeleton-text w-24"></div>
                            </div>
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                        </div>

                        <div class="bg-gray-200 rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 skeleton-circle skeleton mr-4"></div>
                            <div class="flex-1">
                                <div class="skeleton skeleton-text w-32 mb-2"></div>
                                <div class="skeleton skeleton-text w-24"></div>
                            </div>
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                        </div>

                        <div class="bg-gray-200 rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 skeleton-circle skeleton mr-4"></div>
                            <div class="flex-1">
                                <div class="skeleton skeleton-text w-32 mb-2"></div>
                                <div class="skeleton skeleton-text w-24"></div>
                            </div>
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                        </div>

                        <div class="bg-gray-200 rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 skeleton-circle skeleton mr-4"></div>
                            <div class="flex-1">
                                <div class="skeleton skeleton-text w-32 mb-2"></div>
                                <div class="skeleton skeleton-text w-24"></div>
                            </div>
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                        </div>

                        <div class="bg-gray-200 rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 skeleton-circle skeleton mr-4"></div>
                            <div class="flex-1">
                                <div class="skeleton skeleton-text w-32 mb-2"></div>
                                <div class="skeleton skeleton-text w-24"></div>
                            </div>
                            <div class="skeleton w-20 h-8 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actual Leaderboard Content (Initially hidden) -->
        <div id="leaderboardContent" class="hidden">
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
                                <img src="{{ asset('images/leaderboards/Group 25.png') }}" alt="2nd place bg"
                                    class="absolute"
                                    style="left: 60px; top: 81%; width: max; height: max; z-index: 10; transform: translate(-50%, -80%);">
                                <!-- 3rd place background -->
                                <img src="{{ asset('images/leaderboards/Group 24.png') }}" alt="3rd place bg"
                                    class="absolute"
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
                <div class="relative -mx-4 sm:-mx-6 lg:-mx-8 p-5 sm:p-6 bg-white rounded-2xl -mt-4 sm:-mt-4 lg:-mt-4 z-10">
                    <div class="bg-white px-6 lg:px-24 py-6 space-y-5">
                        <!-- Ranked List Items -->
                        <div
                            class="bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <span class="text-gray-600 font-bold">A</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Anderson Silva</div>
                                <div class="text-blue-100 text-sm">VI - Sampaguita</div>
                            </div>
                            <div class="bg-leaderboard-points drop-shadow-leaderboard-points text-white px-3 py-1 rounded-full text-base font-medium font-baloo"
                                style="text-shadow: 
                                    -1px -1px 0 #AE6816, 
                                    1px -1px 0 #AE6816, 
                                    -1px 1px 0 #AE6816,
                                    1px  1px 0 #AE6816,
                                    -1px  2px 0 #AE6816, 
                                    1px 2px 0 #AE6816, 
                                    0 2px 0 #AE6816;">
                                698 pts
                            </div>
                        </div>

                        <div
                            class="bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <span class="text-gray-600 font-bold">K</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Kate Villamor</div>
                                <div class="text-blue-100 text-sm">VI - Orchid</div>
                            </div>
                            <div class="bg-leaderboard-points drop-shadow-leaderboard-points text-white px-3 py-1 rounded-full text-base font-medium font-baloo"
                                style="text-shadow: 
                                    -1px -1px 0 #AE6816, 
                                    1px -1px 0 #AE6816, 
                                    -1px 1px 0 #AE6816,
                                    1px  1px 0 #AE6816,
                                    -1px  2px 0 #AE6816, 
                                    1px 2px 0 #AE6816, 
                                    0 2px 0 #AE6816;">
                                698 pts
                            </div>
                        </div>

                        <div
                            class="bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <span class="text-gray-600 font-bold">A</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Angel Lopez</div>
                                <div class="text-blue-100 text-sm">VI - Yellow Bell</div>
                            </div>
                            <div class="bg-leaderboard-points drop-shadow-leaderboard-points text-white px-3 py-1 rounded-full text-base font-medium font-baloo"
                                style="text-shadow: 
                                    -1px -1px 0 #AE6816, 
                                    1px -1px 0 #AE6816, 
                                    -1px 1px 0 #AE6816,
                                    1px  1px 0 #AE6816,
                                    -1px  2px 0 #AE6816, 
                                    1px 2px 0 #AE6816, 
                                    0 2px 0 #AE6816;">
                                698 pts
                            </div>
                        </div>

                        <div
                            class="bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <span class="text-gray-600 font-bold">J</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Johnson Spear</div>
                                <div class="text-blue-100 text-sm">VI - Jasmin</div>
                            </div>
                            <div class="bg-leaderboard-points drop-shadow-leaderboard-points text-white px-3 py-1 rounded-full text-base font-medium font-baloo"
                                style="text-shadow: 
                                    -1px -1px 0 #AE6816, 
                                    1px -1px 0 #AE6816, 
                                    -1px 1px 0 #AE6816,
                                    1px  1px 0 #AE6816,
                                    -1px  2px 0 #AE6816, 
                                    1px 2px 0 #AE6816, 
                                    0 2px 0 #AE6816;">
                                698 pts
                            </div>
                        </div>

                        <!-- Continue with more entries as needed -->
                        <div
                            class="bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <span class="text-gray-600 font-bold">S</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Sarah Johnson</div>
                                <div class="text-blue-100 text-sm">VI - Rose</div>
                            </div>
                            <div class="bg-leaderboard-points drop-shadow-leaderboard-points text-white px-3 py-1 rounded-full text-base font-medium font-baloo"
                                style="text-shadow: 
                                    -1px -1px 0 #AE6816, 
                                    1px -1px 0 #AE6816, 
                                    -1px 1px 0 #AE6816,
                                    1px  1px 0 #AE6816,
                                    -1px  2px 0 #AE6816, 
                                    1px 2px 0 #AE6816, 
                                    0 2px 0 #AE6816;">
                                698 pts
                            </div>
                        </div>

                        <div
                            class="bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <span class="text-gray-600 font-bold">M</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Michael Chen</div>
                                <div class="text-blue-100 text-sm">VI - Lily</div>
                            </div>
                            <div class="bg-leaderboard-points drop-shadow-leaderboard-points text-white px-3 py-1 rounded-full text-base font-medium font-baloo"
                                style="text-shadow: 
                                    -1px -1px 0 #AE6816, 
                                    1px -1px 0 #AE6816, 
                                    -1px 1px 0 #AE6816,
                                    1px  1px 0 #AE6816,
                                    -1px  2px 0 #AE6816, 
                                    1px 2px 0 #AE6816, 
                                    0 2px 0 #AE6816;">
                                698 pts
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Leaderboard Loading Logic
        document.addEventListener('DOMContentLoaded', function () {
            // Show skeleton initially, hide actual content
            const skeleton = document.getElementById('leaderboardSkeleton');
            const content = document.getElementById('leaderboardContent');

            // Simulate data loading
            setTimeout(() => {
                loadLeaderboardData();
            }, 1500);
        });

        async function loadLeaderboardData() {
            try {
                // Simulate API calls
                const promises = [
                    loadTopThree(),
                    loadRankedList(),
                    loadUserRank()
                ];

                await Promise.all(promises);

                // Hide skeleton and show content
                const skeleton = document.getElementById('leaderboardSkeleton');
                const content = document.getElementById('leaderboardContent');

                skeleton.style.opacity = '0';
                setTimeout(() => {
                    skeleton.classList.add('hidden');
                    content.classList.remove('hidden');
                    content.classList.add('content-loaded');
                }, 300);

            } catch (error) {
                console.error('Error loading leaderboard:', error);
                showLeaderboardContent();
            }
        }

        function showLeaderboardContent() {
            const skeleton = document.getElementById('leaderboardSkeleton');
            const content = document.getElementById('leaderboardContent');

            skeleton.classList.add('hidden');
            content.classList.remove('hidden');
            content.classList.add('content-loaded');
        }

        // Simulate API calls (replace with actual endpoints)
        async function loadTopThree() {
            // Replace with: return fetch('/api/leaderboard/top-three').then(r => r.json());
            return new Promise(resolve => setTimeout(resolve, 400));
        }

        async function loadRankedList() {
            // Replace with: return fetch('/api/leaderboard/ranked-list').then(r => r.json());
            return new Promise(resolve => setTimeout(resolve, 500));
        }

        async function loadUserRank() {
            // Replace with: return fetch('/api/leaderboard/user-rank').then(r => r.json());
            return new Promise(resolve => setTimeout(resolve, 300));
        }
    </script>

@endsection