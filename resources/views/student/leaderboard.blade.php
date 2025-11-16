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

        /* Sticky Footer */
        .sticky-footer {
            position: fixed;
            left: 0;
            right: 0;
            z-index: 35;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1), 0 -2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Position above bottom nav on mobile/tablet, at bottom on desktop */
        @media (max-width: 1279px) {
            .sticky-footer {
                bottom: 60px;
                /* Above the bottom navigation bar */
            }
        }

        @media (min-width: 1280px) {
            .sticky-footer {
                bottom: 0;
                /* At the very bottom on desktop */
            }
        }

        /* Add padding to bottom of content to prevent overlap with sticky footer and nav */
        .content-with-footer {
            padding-bottom: 160px;
            /* Account for both sticky footer and bottom nav on mobile */
        }

        @media (min-width: 1280px) {
            .content-with-footer {
                padding-bottom: 100px;
                /* Less padding on desktop (no bottom nav) */
            }
        }

        /* Initial animation states - CRITICAL FOR ANIMATIONS */
        .podium-element {
            opacity: 0;
            transform: translateY(50px) scale(0.8);
        }

        .rank-element {
            opacity: 0;
            transform: translateY(30px);
        }

        .sticky-footer-element {
            opacity: 0;
            transform: translateY(50px);
        }

        /* Ensure transitions work smoothly */
        .podium-element,
        .rank-element,
        .sticky-footer-element {
            will-change: transform, opacity;
        }
    </style>

    <!-- Include GSAP Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="{{ asset('js/leaderboard-animations.js') }}"></script>

    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 content-with-footer">
        <!-- Loading Skeleton (Initially visible) -->
        <div id="leaderboardSkeleton">
            <div class="min-h-screen">
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
            <div class="min-h-screen">
                <!-- Top Section with Blue Background -->
                <div
                    class="relative -mx-4 sm:-mx-6 lg:-mx-8 pt-2 sm:pt-2 overflow-hidden bg-gradient-to-b bg-center bg-cover from-blue-600 to-blue-700 text-white">
                    <!-- Toggle Switch -->
                    <div class="flex justify-center pt-6 pb-4">
                        <div class="bg-white rounded-full p-1 flex">
                            <button id="sectionBtn"
                                class="px-6 py-2 rounded-full bg-primary-blue text-white font-medium text-sm transition-all">
                                Section
                            </button>
                            <button id="schoolBtn"
                                class="px-6 py-2 rounded-full text-primary-blue font-medium text-sm transition-all">
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
                                <div id="secondPlace"
                                    class="flex flex-col items-center transform translate-y-4 podium-element">
                                    <!-- Will be populated by JS -->
                                </div>

                                <!-- First Place -->
                                <div id="firstPlace" class="flex flex-col items-center podium-element">
                                    <!-- Will be populated by JS -->
                                </div>

                                <!-- Third Place -->
                                <div id="thirdPlace"
                                    class="flex flex-col items-center transform translate-y-4 podium-element">
                                    <!-- Will be populated by JS -->
                                </div>
                            </div>

                            <!-- Image Podium -->
                            <div class="relative podium-element" id="podiumImage">
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
                    <div class="bg-white px-6 lg:px-24 py-6">
                        <!-- Ranked List Items (4-10) -->
                        <div id="rankedList" class="space-y-5">
                            <!-- Will be populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Footer for Current User Rank -->
    <div id="stickyFooter"
        class="sticky-footer bg-gradient-to-r from-blue-600 to-blue-700 text-white hidden sticky-footer-element">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div id="footerAvatar"
                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center border-2 border-yellow-300">
                        <!-- Avatar will be inserted here -->
                    </div>
                    <div>
                        <div class="text-sm font-semibold">Your Rank</div>
                        <div id="footerRank" class="text-lg font-bold">Loading...</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold">Your Points</div>
                    <div id="footerPoints" class="text-lg font-bold text-yellow-300">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentView = 'section'; // 'section' or 'school'
        let leaderboardData = null;

        // Leaderboard Loading Logic
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM Content Loaded - Initializing leaderboard');

            // Show skeleton initially, hide actual content
            const skeleton = document.getElementById('leaderboardSkeleton');
            const content = document.getElementById('leaderboardContent');

            // Load section leaderboard by default
            setTimeout(() => {
                loadLeaderboardData('section');
            }, 500);

            // Toggle button event listeners
            document.getElementById('sectionBtn').addEventListener('click', function () {
                if (currentView !== 'section') {
                    currentView = 'section';
                    updateToggleButtons();
                    loadLeaderboardData('section');
                }
            });

            document.getElementById('schoolBtn').addEventListener('click', function () {
                if (currentView !== 'school') {
                    currentView = 'school';
                    updateToggleButtons();
                    loadLeaderboardData('school');
                }
            });
        });

        function updateToggleButtons() {
            const sectionBtn = document.getElementById('sectionBtn');
            const schoolBtn = document.getElementById('schoolBtn');

            if (currentView === 'section') {
                sectionBtn.classList.add('bg-primary-blue', 'text-white');
                sectionBtn.classList.remove('text-primary-blue');
                schoolBtn.classList.remove('bg-primary-blue', 'text-white');
                schoolBtn.classList.add('text-primary-blue');
            } else {
                schoolBtn.classList.add('bg-primary-blue', 'text-white');
                schoolBtn.classList.remove('text-primary-blue');
                sectionBtn.classList.remove('bg-primary-blue', 'text-white');
                sectionBtn.classList.add('text-primary-blue');
            }
        }

        async function loadLeaderboardData(type) {
            try {
                console.log('Loading leaderboard data for:', type);

                const url = type === 'section'
                    ? '{{ route("leaderboard.section") }}'
                    : '{{ route("leaderboard.school") }}';

                const response = await fetch(url);
                const data = await response.json();

                leaderboardData = data;

                // Populate the UI
                populateTopThree(data.top_three);
                populateRankedList(data.ranked_list);
                updateStickyFooter(data.current_user);

                // Hide skeleton and show content
                const skeleton = document.getElementById('leaderboardSkeleton');
                const content = document.getElementById('leaderboardContent');

                skeleton.style.opacity = '0';
                setTimeout(() => {
                    skeleton.classList.add('hidden');
                    content.classList.remove('hidden');
                    content.classList.add('content-loaded');

                    // DEBUG: Check if elements exist and have correct classes
                    console.log('Podium elements found:', document.querySelectorAll('.podium-element').length);
                    console.log('Rank elements found:', document.querySelectorAll('.rank-element').length);
                    console.log('Sticky footer found:', document.querySelector('.sticky-footer-element') ? 'Yes' : 'No');

                    // Check initial states
                    const podiumEls = document.querySelectorAll('.podium-element');
                    podiumEls.forEach((el, index) => {
                        console.log(`Podium element ${index} opacity:`, window.getComputedStyle(el).opacity);
                        console.log(`Podium element ${index} transform:`, window.getComputedStyle(el).transform);
                    });

                    // Trigger animations after a short delay to ensure DOM is ready
                    setTimeout(() => {
                        triggerLeaderboardAnimations();
                    }, 100);
                }, 300);

            } catch (error) {
                console.error('Error loading leaderboard:', error);
                showLeaderboardContent();
            }
        }

        function getInitials(name) {
            if (!name) return '?';
            const parts = name.split(' ');
            if (parts.length >= 2) {
                return parts[0].charAt(0).toUpperCase() + parts[1].charAt(0).toUpperCase();
            }
            return name.charAt(0).toUpperCase();
        }

        function populateTopThree(topThree) {
            const firstPlace = document.getElementById('firstPlace');
            const secondPlace = document.getElementById('secondPlace');
            const thirdPlace = document.getElementById('thirdPlace');

            // Clear previous content
            firstPlace.innerHTML = '';
            secondPlace.innerHTML = '';
            thirdPlace.innerHTML = '';

            // Ensure animation classes are set (important!)
            firstPlace.classList.add('podium-element');
            secondPlace.classList.add('podium-element');
            thirdPlace.classList.add('podium-element');

            // Reset GSAP properties if available
            if (typeof gsap !== 'undefined') {
                gsap.set([firstPlace, secondPlace, thirdPlace], {
                    opacity: 0,
                    y: 50,
                    scale: 0.8
                });
            }

            // First Place (index 0)
            if (topThree && topThree[0]) {
                const student = topThree[0];
                firstPlace.innerHTML = `
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-2 border-4 border-yellow-300 shadow-lg overflow-hidden">
                                ${student.avatar_url
                        ? `<img src="${student.avatar_url}" alt="${student.name}" class="w-full h-full object-cover">`
                        : `<span class="text-gray-600 font-bold text-xl">${getInitials(student.name)}</span>`
                    }
                            </div>
                            <div class="text-center mb-2">
                                <div class="font-semibold text-white">${student.name || 'Unknown'}</div>
                                <div class="text-yellow-300 text-sm">${(student.points || 0).toLocaleString()} pts</div>
                            </div>
                        `;
            } else {
                firstPlace.innerHTML = `
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-2 border-4 border-yellow-300 shadow-lg">
                                <span class="text-gray-400 font-bold text-xl">?</span>
                            </div>
                            <div class="text-center mb-2">
                                <div class="font-semibold text-white">No Data</div>
                                <div class="text-yellow-300 text-sm">0 pts</div>
                            </div>
                        `;
            }

            // Second Place (index 1)
            if (topThree && topThree[1]) {
                const student = topThree[1];
                secondPlace.innerHTML = `
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 border-4 border-gray-300 shadow-lg overflow-hidden">
                                ${student.avatar_url
                        ? `<img src="${student.avatar_url}" alt="${student.name}" class="w-full h-full object-cover">`
                        : `<span class="text-gray-600 font-bold text-lg">${getInitials(student.name)}</span>`
                    }
                            </div>
                            <div class="text-center mb-2">
                                <div class="font-semibold text-sm text-white">${student.name || 'Unknown'}</div>
                                <div class="text-yellow-300 text-xs">${(student.points || 0).toLocaleString()} pts</div>
                            </div>
                        `;
            } else {
                secondPlace.innerHTML = `
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 border-4 border-gray-300 shadow-lg">
                                <span class="text-gray-400 font-bold text-lg">?</span>
                            </div>
                            <div class="text-center mb-2">
                                <div class="font-semibold text-sm text-white">No Data</div>
                                <div class="text-yellow-300 text-xs">0 pts</div>
                            </div>
                        `;
            }

            // Third Place (index 2)
            if (topThree && topThree[2]) {
                const student = topThree[2];
                thirdPlace.innerHTML = `
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 border-4 border-orange-300 shadow-lg overflow-hidden">
                                ${student.avatar_url
                        ? `<img src="${student.avatar_url}" alt="${student.name}" class="w-full h-full object-cover">`
                        : `<span class="text-gray-600 font-bold text-lg">${getInitials(student.name)}</span>`
                    }
                            </div>
                            <div class="text-center mb-2">
                                <div class="font-semibold text-sm text-white">${student.name || 'Unknown'}</div>
                                <div class="text-yellow-300 text-xs">${(student.points || 0).toLocaleString()} pts</div>
                            </div>
                        `;
            } else {
                thirdPlace.innerHTML = `
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2 border-4 border-orange-300 shadow-lg">
                                <span class="text-gray-400 font-bold text-lg">?</span>
                            </div>
                            <div class="text-center mb-2">
                                <div class="font-semibold text-sm text-white">No Data</div>
                                <div class="text-yellow-300 text-xs">0 pts</div>
                            </div>
                        `;
            }
        }

        function populateRankedList(rankedList) {
            const container = document.getElementById('rankedList');
            container.innerHTML = '';

            if (!rankedList || rankedList.length === 0) {
                container.innerHTML = `
                            <div class="text-center py-8 text-gray-500">
                                <div class="text-lg font-semibold">No rankings available</div>
                                <div class="text-sm">There are no students to display in this leaderboard yet.</div>
                            </div>
                        `;
                return;
            }

            rankedList.forEach((student, index) => {
                const item = document.createElement('div');
                item.className = 'bg-[#3B82F6] drop-shadow-leaderboard-container rounded-xl p-4 shadow-md flex items-center rank-element';

                // Set initial state for animation
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';

                item.innerHTML = `
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4 overflow-hidden">
                                ${student.avatar_url
                        ? `<img src="${student.avatar_url}" alt="${student.name}" class="w-full h-full object-cover">`
                        : `<span class="text-gray-600 font-bold">${getInitials(student.name)}</span>`
                    }
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold">Rank ${student.rank || 'N/A'} - ${student.name || 'Unknown'}</div>
                                <div class="text-blue-100 text-sm">${student.grade_level ? 'Grade ' + student.grade_level : ''} ${student.section ? '- ' + student.section : ''}</div>
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
                                ${(student.points || 0).toLocaleString()} pts
                            </div>
                        `;

                container.appendChild(item);
            });
        }

        function updateStickyFooter(currentUser) {
            const footer = document.getElementById('stickyFooter');

            if (!currentUser) {
                footer.classList.add('hidden');
                return;
            }

            const footerAvatar = document.getElementById('footerAvatar');
            const footerRank = document.getElementById('footerRank');
            const footerPoints = document.getElementById('footerPoints');

            // Update avatar
            footerAvatar.innerHTML = '';
            if (currentUser.avatar_url) {
                footerAvatar.innerHTML = `<img src="${currentUser.avatar_url}" alt="Your avatar" class="w-full h-full object-cover rounded-full">`;
            } else {
                footerAvatar.innerHTML = `<span class="text-gray-600 font-bold text-lg">${getInitials(currentUser.name)}</span>`;
            }

            // Update rank and points
            footerRank.textContent = `Rank ${currentUser.rank || 'N/A'}`;
            footerPoints.textContent = `${(currentUser.points || 0).toLocaleString()} pts`;

            // Show the sticky footer and ensure animation class
            footer.classList.remove('hidden');
            footer.classList.add('sticky-footer-element');

            // Set initial state
            footer.style.opacity = '0';
            footer.style.transform = 'translateY(50px)';
        }

        function showLeaderboardContent() {
            const skeleton = document.getElementById('leaderboardSkeleton');
            const content = document.getElementById('leaderboardContent');

            skeleton.classList.add('hidden');
            content.classList.remove('hidden');
            content.classList.add('content-loaded');
        }

        function triggerLeaderboardAnimations() {
            console.log('=== TRIGGERING LEADERBOARD ANIMATIONS ===');

            // Use the external animation library
            if (typeof window.LeaderboardAnimations !== 'undefined') {
                console.log('Using external animation library');
                window.LeaderboardAnimations.triggerAllAnimations();
            } else {
                console.log('External library not found, using fallback animations');
                animateLeaderboardFallback();
            }
        }

        function animateLeaderboardFallback() {
            console.log('Running fallback CSS animations');

            // Simple fallback animations using CSS transitions
            const podiumElements = document.querySelectorAll('.podium-element');
            const rankElements = document.querySelectorAll('.rank-element');
            const footerElement = document.querySelector('.sticky-footer-element');

            // Animate podium with stagger
            podiumElements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0) scale(1)';
                }, index * 200);
            });

            // Animate rank list with stagger
            rankElements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease-out';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100 + 600); // Start after podium
            });

            // Animate sticky footer
            if (footerElement) {
                setTimeout(() => {
                    footerElement.style.transition = 'all 0.7s ease-out';
                    footerElement.style.opacity = '1';
                    footerElement.style.transform = 'translateY(0)';
                }, 1000);
            }
        }

        // Handle page visibility changes (in case animations get stuck)
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                // Page became visible again, check if animations need restarting
                const animatedElements = document.querySelectorAll('.podium-element, .rank-element, .sticky-footer-element');
                let allVisible = true;

                animatedElements.forEach(el => {
                    if (window.getComputedStyle(el).opacity === '0') {
                        allVisible = false;
                    }
                });

                if (!allVisible) {
                    console.log('Some elements not visible, re-triggering animations');
                    triggerLeaderboardAnimations();
                }
            }
        });
    </script>

@endsection