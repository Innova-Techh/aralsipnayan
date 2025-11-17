@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - AralSipnayan</title>
    <!-- Confetti CDN -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
</head>
<body>
    <audio id="welcomeAudio" autoplay>
        <source src="{{ asset('audio/welcome.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    
    <main class="min-h-screen flex items-center justify-center px-2 md:px-8 py-8 mb-10 lg:mb-0 xl:mb-0">
        <div class="w-full max-w-7xl rounded-3xl p-4 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-16">
                <!-- Left Column - Character Image -->
                <aside class="w-full lg:w-2/5 hidden lg:block">
                    <img src="{{ asset('images/onboarding/on-board-desktop.png') }}" 
                         alt="Student Characters" 
                         class="w-full h-auto">
                </aside>

                <!-- Right Column - Content -->
                <section class="w-full lg:w-3/5 flex flex-col gap-6">
                    <!-- Welcome Text -->
                    <header class="text-center lg:text-left">
                        <h1 class="text-4xl md:text-6xl lg:text-7xl xs:text-5xl font-baloo font-bold">
                            <span class="text-[#658DFF] drop-shadow-on-welcome">Welcome,</span>
                            <span class="text-[#658DFF] drop-shadow-on-welcome">{{ $student->firstname }}</span>
                        </h1>
                    </header>

                    <!-- Mobile Character Image -->
                    <div class="lg:hidden w-full max-w-sm mx-auto">
                        <img src="{{ asset('images/onboarding/on-board-desktop.png') }}" 
                             alt="Student Characters"
                             class="w-full h-auto">
                    </div>

                    <!-- Text Content -->
                    <article class="space-y-4 text-gray-600">
                        <p class="text-lg">{{ $welcomeMessage ?? "We're excited to have you here! Are you ready to earn points, collect badges, and level up your math skills? Dive into fun challenges, unlock achievements, and track your progress every step of the way. Whether you're here to learn, compete, or climb the leaderboards, AralSipnayan is your space to grow and shine. Let the learning adventure begin!" }}</p>
                        @if(!empty($additionalMessage))
                            <p class="text-lg">{{ $additionalMessage }}</p>
                        @endif
                    </article>

                    <!-- Action Cards -->
                    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Earn Points Card -->
                        <div
                          class="bg-on-board-earn drop-shadow-on-board-earn rounded-xl p-4 text-center text-white hover:scale-105 transition-transform duration-300">
                          <img src="{{ asset('images/onboarding/on-board-award.png') }}" alt="Trophy" class="w-24 h-24 mx-auto mb-4">
                          <p class="font-poppins font-bold xl:font-bold lg:text-base xs:font-normal">Earn your points and badges</p>
                        </div>
        
                        <!-- Track Progress Card -->
                        <div
                          class="bg-on-board-progress drop-shadow-on-board-progress rounded-xl p-4 text-center text-white hover:scale-105 transition-transform duration-300">
                          <img src="{{ asset('images/onboarding/on-board-progress.png') }}" alt="Progress"
                            class="w-24 h-24 mx-auto mb-6">
                          <p class="font-poppins font-bold xl:font-bold text-sm lg:text-base xs:font-normal">Track your progress</p>
                        </div>
        
                        <!-- Learn Challenges Card -->
                        <div
                          class="bg-on-board-learn drop-shadow-on-board-learn rounded-xl p-4 text-center text-white hover:scale-105 transition-transform duration-300">
                          <img src="{{ asset('images/onboarding/on-board-learn.png') }}" alt="Learn" class="w-24 h-24 mx-auto mb-4">
                          <p class="font-poppins font-bold xl:font-bold text-sm lg:text-base xs:font-normal ">Learn through fun challenges</p>
                        </div>
                      </section>

                    <!-- Select Avatar Button -->
                    <nav class="text-center">
                        <a href="{{ route('student.onboarding.avatar') }}"
                           class="w-full text-xl drop-shadow-select-avatar font-baloo font-bold inline-block bg-gradient-secondary hover:bg-hover-secondary text-white py-3 px-8 rounded-full hover:scale-95 transition-transform duration-300">
                            Select Avatar
                        </a>
                    </nav>
                </section>
            </div>
        </div>
    </main>

     <script>
        document.addEventListener("DOMContentLoaded", function () {
            const audio = document.getElementById("welcomeAudio");

            // Try to play audio
            audio.play().then(() => {
                triggerConfetti(); // fire confetti when audio plays
            }).catch(() => {
                console.log("Autoplay was blocked, waiting for user interaction...");
                document.body.addEventListener("click", () => {
                    audio.play();
                    triggerConfetti();
                }, { once: true });
            });

            audio.volume = 0.6; // Set volume to 60%

            // Confetti effect function
            function triggerConfetti() {
                // burst effect for trumpet sound
                var duration = 2 * 1000;
                var end = Date.now() + duration;

                (function frame() {
                    // random bursts
                    confetti({
                        particleCount: 7,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 }
                    });
                    confetti({
                        particleCount: 7,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1 }
                    });

                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                }());
            }
        });
    </script>
</body>
</html>
@endsection
