@extends('layouts.user_layout')

@section('content')
    @php
        use App\Http\Controllers\RankController;

        // Initialize the RankController
        $rankController = new RankController();

        // Get user's current XP (replace with your actual user XP logic)
        $userXP = auth()->guard('student')->user()->xp ?? 460; // Example: 460 XP
        $progressInfo = $rankController->getProgressInfo($userXP);
    @endphp
    <div class="min-h-screen bg-[#C2DAFF] py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-left mb-8">
                <h1 class="text-3xl font-bold font-poppins text-gray-800 mb-2">Student Profile</h1>
                <p class="text-gray-600">Track your daily learning progress</p>
            </div>

            @if(session('success'))
                <div class="max-w-4xl mx-auto mb-4">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-green-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Dynamic Level Card -->
            <div class="mb-5 sm:mb-6">
                <div class="text-white rounded-xl p-4 sm:p-5 shadow-inner"
                    style="background: linear-gradient(to right, #101093, #931093); box-shadow: inset 0 -4px 4px #42045C, inset 0 2px 2px #CC39F6; box-shadow: 0 6px 0 #0A0A62;">
                    <div class="flex items-center gap-4">
                        <!-- Dynamic Rank image -->
                        <div class="flex-shrink-0">
                            <img src="{{ asset('images/rank_insignia/' . $progressInfo['rank_info']['image']) }}"
                                alt="{{ $progressInfo['rank_info']['title'] }}"
                                class="w-32 h-32 sm:w-18 sm:h-18 rounded-xl object-contain">
                        </div>

                        <!-- Content area -->
                        <div class="flex-1 min-w-0 mr-2">
                            <!-- Group 1: Title and Level -->
                            <div class="mb-2">
                                <h3 class="text-xl sm:text-xl font-bold">
                                    {{ $progressInfo['rank_info']['title'] }}
                                </h3>
                                <p class="text-sm text-blue-200">Level
                                    {{ $progressInfo['current_level'] }}
                                </p>
                            </div>

                            <!-- Group 2: XP Text (standalone) -->
                            <div class="mb-2 mr-4 text-right">
                                @if($progressInfo['is_max_level'])
                                    <p class="text-xs sm:text-sm text-yellow-300 font-bold">MAX LEVEL
                                        ACHIEVED!
                                    </p>
                                @else
                                    <p class="text-xs sm:text-sm text-blue-200">
                                        {{ number_format($progressInfo['current_xp']) }} XP /
                                        {{ number_format($progressInfo['rank_info']['xp_required']) }}
                                        XP
                                    </p>
                                @endif
                            </div>

                            <!-- Group 3: Custom Progress bar and XP remaining -->
                            <div class="mr-4">
                                <div class="mb-1">
                                    {{-- Custom Level Progress Bar with Handle --}}
                                    <div class="relative">
                                        <div class="level-progress-track rounded-full h-3 sm:h-4 relative overflow-visible">
                                            <div class="level-progress-fill h-3 sm:h-4 rounded-full transition-all duration-500 ease-out relative overflow-visible"
                                                style="width: {{ $progressInfo['progress_percentage'] }}%">
                                                {{-- Progress Handle/Thumb for Level --}}
                                                <div class="level-progress-handle"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($progressInfo['is_max_level'])
                                    <p class="text-xs sm:text-sm text-yellow-300">🏆 Grandmaster Status
                                    </p>
                                @else
                                    <p class="text-xs sm:text-sm text-blue-200">
                                        {{ number_format($progressInfo['xp_remaining']) }} XP remaining
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid with Live Data -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-12">
                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-green drop-shadow-stats-green">

                    <!-- Books Pattern (scattered icons) -->
                    <div class="absolute top-0 left-0 w-full h-24 opacity-80 blur-[1px]">

                    </div>

                    <!-- Icon -->
                    <div class="flex items-center justify-center mb-2 relative z-10">
                        <img src="{{ asset('images/dashboard/book.png') }}" alt="Completed"
                            class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
                    </div>

                    <!-- Number -->
                    <div class="text-xl sm:text-2xl font-bold relative z-10">2</div>

                    <!-- Label -->
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Completed</div>
                </div>



                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-yellow drop-shadow-stats-yellow">
                    <div class="flex items-center justify-center mb-2">
                        <img src="{{ asset('images/dashboard/points.png') }}" alt="Points"
                            class="w-10 h-10 sm:w-12 sm:h-12 object-contain relative z-10">
                    </div>
                    <div class="text-xl sm:text-2xl font-bold relative z-10" id="dashboardPoints">
                        {{ $profile?->total_points ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Points</div>
                </div>


                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-red drop-shadow-stats-red">
                    <div class="flex items-center justify-center mb-2">
                        <img src="{{ asset('images/dashboard/streak.png') }}" alt="Streak"
                            class="w-9 h-9 sm:w-11 sm:h-11 object-contain relative z-10">
                    </div>
                    <div class="text-xl sm:text-2xl font-bold relative z-10" id="dashboardStreak">
                        {{ $profile?->current_streak ?? 0 }}
                    </div>
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Streak</div>
                </div>


                <div
                    class="relative overflow-hidden rounded-2xl p-3 sm:p-4 text-white text-center bg-stats-blue drop-shadow-stats-blue">
                    <div class="flex items-center justify-center mb-2">
                        <img src="{{ asset('images/dashboard/star.png') }}" alt="Level"
                            class="w-12 h-8 sm:w-14 sm:h-10 object-contain relative z-10">
                    </div>
                    <div class="text-xl sm:text-2xl font-bold relative z-10">3</div>
                    <div class="text-xs sm:text-sm opacity-90 relative z-10">Level</div>
                </div>
            </div>

            <!-- Account Settings Section -->
            <div class="bg-white rounded-3xl shadow-xl p-6 mb-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                            clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-2xl font-bold font-poppins text-gray-800">Account Settings</h2>
                </div>

                <div class="space-y-3">
                    <!-- Edit Avatar Button -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 hover:from-purple-100 hover:to-pink-100 transition-all transform hover:scale-105">
                        <div class="flex items-center">
                            <div class="bg-purple-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">Edit Avatar</h3>
                                <p class="text-sm text-gray-600">Customize your profile picture</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <!-- Change Password Button (Opens Modal) -->
                    <button type="button" onclick="openPasswordModal()"
                        class="w-full flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-4 hover:from-blue-100 hover:to-cyan-100 transition-all transform hover:scale-105">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 class="font-bold text-gray-800">Change Password</h3>
                                <p class="text-sm text-gray-600">Update your account password</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-between bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-4 hover:from-red-100 hover:to-orange-100 transition-all transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="bg-red-500 rounded-full p-3 mr-4">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 class="font-bold text-gray-800">Logout</h3>
                                    <p class="text-sm text-gray-600">Sign out of your account</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Stats Section -->
            <div class="bg-white rounded-3xl shadow-xl p-6 mb-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <h2 class="text-2xl font-bold font-poppins text-gray-800">Player Statistics</h2>
                </div>

                <!-- Competency Level -->
                <div class="rounded-2xl p-5 mb-4">
                    <!-- Highlighted Stats: Difficulty Level and Accuracy Rate -->
                    <div class="grid grid-cols-2 gap-2 sm:gap-3 mb-3 sm:mb-4">
                        <!-- Difficulty Level -->
                        <div class="rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center shadow-md min-h-[100px] flex items-center justify-center"
                            style="background: linear-gradient(to bottom, #064E3B 0%, #059669 100%); box-shadow: 0 4px 0 0 #045C41;">
                            <div class="text-[#F8FAFC]">
                                <div class="text-xl sm:text-2xl font-bold text-[#22C55E]">Beginner</div>
                                <div class="text-xs sm:text-sm font-semibold mb-1 opacity-90">Difficulty Level</div>
                            </div>
                        </div>

                        <!-- Accuracy Rate -->
                        <div class="rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center shadow-md min-h-[100px] flex items-center justify-center"
                            style="background: linear-gradient(to bottom, #1E3A8A 0%, #2563EB 100%); box-shadow: 0 4px 0 0 #091F5E;">
                            <div class="text-[#F8FAFC]">
                                <div class="text-xl sm:text-2xl font-bold text-[#73A8FF]">98%</div>
                                <div class="text-xs sm:text-sm font-semibold mb-1 opacity-90">Accuracy Rate</div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Stats -->
                    <div class="space-y-2 sm:space-y-3">
                        <!-- Login Streak -->
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between text-sm sm:text-base">
                                <span class="text-gray-700 font-medium">📅 Login Streak</span>
                                <span class="text-orange-700 font-bold">10 days</span>
                            </div>
                        </div>

                        <!-- Highest Correct Streak -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between text-sm sm:text-base">
                                <span class="text-gray-700 font-medium">⭐ Highest Correct Streak</span>
                                <span class="text-green-700 font-bold">7 streak</span>
                            </div>
                        </div>

                        <!-- Highest Points Earned -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-3 sm:p-4">
                            <div class="flex items-center justify-between text-sm sm:text-base">
                                <span class="text-gray-700 font-medium">💎 Highest Points Earned</span>
                                <span class="text-purple-700 font-bold">7 streak</span>
                            </div>
                        </div>

                        <!-- Average Score -->
                        <div class="bg-indigo-50 rounded-2xl p-5 mb-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="font-semibold text-gray-700">Average Score</span>
                                </div>
                                <span class="text-2xl font-bold text-indigo-600">93%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="bg-white rounded-3xl shadow-xl p-6">
                <h2 class="text-2xl font-bold font-poppins text-gray-800 mb-4">Recent Achievements</h2>
                <div class="space-y-3">
                    <div
                        class="flex items-center bg-yellow-50 rounded-xl p-4 transform hover:scale-105 transition-transform">
                        <div class="bg-yellow-400 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">Perfect Week</h3>
                            <p class="text-sm text-gray-600">7 days streak achieved!</p>
                        </div>
                        <span class="text-xs text-gray-500">2 days ago</span>
                    </div>

                    <div
                        class="flex items-center bg-green-50 rounded-xl p-4 transform hover:scale-105 transition-transform">
                        <div class="bg-green-400 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">Level Up!</h3>
                            <p class="text-sm text-gray-600">Reached Level 13</p>
                        </div>
                        <span class="text-xs text-gray-500">5 days ago</span>
                    </div>

                    <div class="flex items-center bg-blue-50 rounded-xl p-4 transform hover:scale-105 transition-transform">
                        <div class="bg-blue-400 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800">Assessment Master</h3>
                            <p class="text-sm text-gray-600">Scored 100% on Math Quiz</p>
                        </div>
                        <span class="text-xs text-gray-500">1 week ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-change-password-modal />
    <!-- JavaScript for Modal -->
    <script>
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
            document.getElementById('passwordModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('passwordModal').classList.remove('flex');
            document.body.style.overflow = 'auto';

            // Clear form inputs
            document.getElementById('old_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('passwordModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closePasswordModal();
            }
        });

        // Auto-open modal if there are validation errors
        @if ($errors->any())
            openPasswordModal();
        @endif
    </script>
    @include('components.change-password-modal')
@endsection