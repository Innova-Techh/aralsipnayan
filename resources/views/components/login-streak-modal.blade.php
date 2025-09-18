<!-- Login Streak Modal Component -->
<div id="loginStreakModal" class="fixed inset-0 bg-blue-200 bg-opacity-90 z-50 modal-overlay hidden">
    <div class="fixed left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 modal-content">
        <div class="bg-white rounded-3xl p-8 w-96 text-center shadow-xl">

            <!-- Header -->
            <h2 class="text-2xl font-bold text-orange-500 mb-8"
                style="text-shadow: 2px 2px 0px rgba(251, 146, 60, 0.2);">
                Daily Login Streak
            </h2>

            <!-- Flame Icon -->
            <div class="mb-6 flex justify-center">
                <div class="relative">
                    <div class="flame-icon">
                        <img src="{{ asset('images/login_streak/streak-icon.png') }}" alt="Streak Icon"
                            class="w-20 h-20 object-contain">
                    </div>
                </div>
            </div>

            <!-- Streak Day -->
            <div class="mb-6">
                <div class="text-4xl font-bold text-gray-600 mb-2" id="streakDay">
                    Day 1
                </div>
                <div class="text-lg text-gray-500" id="pointsEarned">
                    10 points
                </div>
            </div>

            <!-- Message -->
            <p class="text-gray-600 mb-8 text-lg" id="streakMessage">
                You're doing great! Keep the streak up
            </p>

            <!-- Continue Button -->
            <button id="continueButton"
                class="w-full bg-select-avatar text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg text-lg"
                onclick="closeStreakModal()">
                Continue
            </button>

        </div>
    </div>
</div>