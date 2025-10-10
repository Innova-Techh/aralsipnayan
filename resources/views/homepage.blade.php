<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AralSipnayan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .font-baloo {
            font-family: 'Baloo 2', cursive;
        }

        /* New gradient background */
        .full-screen-section {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #1E3A8A 0%, #0f1a3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        /* GSAP animated circles */
        .moving-circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
        }

        /* Animations */
        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 0.8;
            }

            50% {
                opacity: 0.4;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .hover-pop:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Swiper Styles */
        .swiper-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 0 20px;
        }

        .swiper {
            width: 100%;
            padding-top: 30px;
            padding-bottom: 120px;
            overflow: hidden;
            perspective: 1200px;
        }

        .swiper-wrapper {
            transform-style: preserve-3d;
            position: relative;
            width: 100%;
            margin: 0 auto;
        }

        .swiper-viewport {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .swiper-slide {
            width: 550px;
            height: 400px;
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
            transform: scale(0.6) translateX(0) translateZ(-400px);
            border-radius: 2rem;
            position: relative;
            background: linear-gradient(to bottom, var(--gradient-from), var(--gradient-to));
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            justify-content: flex-start;
            pointer-events: none;
            visibility: hidden;
            will-change: transform, opacity;
            transform-origin: center center;
        }

        .swiper-slide h1 {
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
        }

        .swiper-slide p {
            margin-top: auto;
            padding-bottom: 1rem;
        }

        .swiper-slide-active {
            opacity: 1;
            transform: scale(1) translateX(0) translateZ(0) rotateY(0);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            z-index: 3;
            pointer-events: auto;
            visibility: visible;
        }

        .swiper-slide-prev,
        .swiper-slide-next {
            opacity: 0.85;
            visibility: visible;
            pointer-events: none;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .swiper-slide-prev {
            transform: translateX(-75%) translateZ(-100px) rotateY(25deg);
            transform-origin: right center;
            z-index: 2;
            pointer-events: none;
        }

        .swiper-slide-next {
            transform: translateX(75%) translateZ(-100px) rotateY(-25deg);
            transform-origin: left center;
            z-index: 2;
            pointer-events: none;
        }

        .swiper-slide:not(.swiper-slide-active):not(.swiper-slide-prev):not(.swiper-slide-next) {
            opacity: 0.4;
            transform: translateZ(-200px);
            z-index: 1;
            pointer-events: none;
        }

        /* Ensure slides don't overflow the container */
        .swiper-slide {
            max-width: calc(100vw - 40px);
        }

        .swiper-slide img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            margin: 1.5rem 0;
            transition: transform 0.3s ease;
        }

        .swiper-slide-active img {
            transform: scale(1.05);
        }

        .swiper-slide p {
            color: white;
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 80%;
            margin: 0 auto;
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: white;
            background: rgba(79, 70, 229, 0.9);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            font-size: 0;
        }

        .swiper-button-next {
            right: 2%;
        }

        .swiper-button-prev {
            left: 2%;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: #4F46E5;
            transform: translateY(-50%) scale(1.1);
        }

        @media (max-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                top: auto;
                bottom: 0;
                transform: none;
                width: 40px;
                height: 40px;
            }

            .swiper-button-next:hover,
            .swiper-button-prev:hover {
                transform: scale(1.1);
            }

            .swiper-button-next {
                right: calc(50% - 60px);
            }

            .swiper-button-prev {
                left: calc(50% - 60px);
            }

            .swiper-button-next::after,
            .swiper-button-prev::after {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {

            .swiper-button-next,
            .swiper-button-prev {
                width: 36px;
                height: 36px;
            }

            .swiper-button-next {
                right: calc(50% - 50px);
            }

            .swiper-button-prev {
                left: calc(50% - 50px);
            }

            .swiper-button-next::after,
            .swiper-button-prev::after {
                font-size: 16px;
            }
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-family: swiper-icons;
            font-size: 20px;
            text-transform: none !important;
            letter-spacing: 0;
            font-variant: initial;
            line-height: 1;
        }

        .swiper-button-prev::after {
            content: 'prev';
        }

        .swiper-button-next::after {
            content: 'next';
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .swiper {
                padding-top: 20px;
            }

            .swiper-slide {
                width: 500px;
                height: 380px;
                padding: 2rem 1.5rem;
            }

            .swiper-slide h1 {
                font-size: 2rem;
                margin-top: -0.25rem;
                margin-bottom: 1.25rem;
            }

            .swiper-slide-prev {
                transform: scale(0.8) translateX(-80%) translateZ(-200px) rotateY(12deg);
            }

            .swiper-slide-next {
                transform: scale(0.8) translateX(80%) translateZ(-200px) rotateY(-12deg);
            }
        }

        @media (max-width: 768px) {
            .swiper {
                padding-top: 15px;
                padding-bottom: 100px;
            }

            .swiper-slide {
                width: 420px;
                height: 340px;
                padding: 1.75rem 1.5rem;
            }

            .swiper-slide h1 {
                font-size: 1.75rem;
                margin-top: 0;
                margin-bottom: 1rem;
            }

            .swiper-slide img {
                width: 160px;
                height: 160px;
                margin: 1rem 0;
            }

            .swiper-slide p {
                font-size: 1.1rem;
                margin-top: auto;
                padding-bottom: 0.5rem;
            }

            .swiper-slide-prev {
                transform: scale(0.75) translateX(-70%) translateZ(-150px) rotateY(10deg);
            }

            .swiper-slide-next {
                transform: scale(0.75) translateX(70%) translateZ(-150px) rotateY(-10deg);
            }
        }

        @media (max-width: 480px) {
            .swiper {
                padding-top: 10px;
                padding-bottom: 80px;
                perspective: 1000px;
            }

            .swiper-slide {
                width: 280px;
                height: 300px;
                padding: 1.5rem 1rem;
            }

            .swiper-slide h1 {
                font-size: 1.5rem;
                margin-top: 0;
                margin-bottom: 0.75rem;
            }

            .swiper-slide img {
                width: 140px;
                height: 140px;
                margin: 0.75rem 0;
            }

            .swiper-slide p {
                font-size: 1rem;
                margin-top: auto;
                padding-bottom: 0.25rem;
            }

            .swiper-slide-prev,
            .swiper-slide-next {
                opacity: 0.75;
                visibility: visible;
                width: 280px;
            }

            .swiper-slide-prev {
                transform: scale(0.85) translateX(-65%) translateZ(-50px) rotateY(25deg);
            }

            .swiper-slide-next {
                transform: scale(0.85) translateX(65%) translateZ(-50px) rotateY(-25deg);
            }

            .swiper-button-next,
            .swiper-button-prev {
                width: 40px;
                height: 40px;
                top: auto;
                bottom: 0;
            }

            .swiper-button-next {
                right: 30%;
            }

            .swiper-button-prev {
                left: 30%;
            }
        }

        @media (max-width: 360px) {
            .swiper-slide {
                width: 260px;
                height: 280px;
            }

            .swiper-slide img {
                width: 120px;
                height: 120px;
            }

            .swiper-button-next {
                right: 25%;
            }

            .swiper-button-prev {
                left: 25%;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-row justify-between items-center py-3 sm:py-4">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo"
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl">
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold">
                        <span class="text-blue-600">Aral</span><span class="text-red-600">Sipnayan</span>
                    </h1>
                </div>
                <a href="{{ route('login') }}"
                    class="bg-blue-900 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-white text-sm sm:text-base w-auto text-center">Login</a>
            </div>
        </nav>
    </header>

    <!-- Section 1 -->
    <section class="py-12 md:py-12 text-white full-screen-section">
        <!-- GSAP animated circles -->
        <div class="moving-circles">
            <div class="circle" id="circle1"></div>
            <div class="circle" id="circle2"></div>
            <div class="circle" id="circle3"></div>
            <div class="circle" id="circle4"></div>
            <div class="circle" id="circle5"></div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-32 left-8 w-2 h-2 bg-red-400 rounded-full opacity-80 animate-pulse-slow z-10"></div>
        <div class="absolute top-40 right-12 text-yellow-400 opacity-60 text-2xl animate-float z-10">✦</div>
        <div class="absolute top-64 left-16 w-1 h-1 bg-blue-300 rounded-full animate-float z-10"></div>
        <div class="absolute top-80 right-8 w-2 h-2 bg-pink-400 rounded-full opacity-70 animate-float z-10"></div>
        <div class="absolute bottom-80 left-12 w-2 h-2 bg-green-400 rounded-full opacity-60 animate-pulse-slow z-10">
        </div>
        <div class="absolute bottom-72 right-16 text-purple-300 opacity-70 text-xl animate-float z-10">✦</div>

        <!-- Mathematical Symbols -->
        <div class="absolute top-20 right-20 text-white opacity-20 text-4xl animate-float z-10">÷</div>
        <div class="absolute bottom-40 left-20 text-white opacity-20 text-4xl animate-float z-10">+</div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Master <span class="text-yellow-400">Advanced<br>Mathematics</span> with<br>
                <span class="text-white">Interactive Learning</span>
            </h1>
            <p class="text-lg md:text-xl mb-12 opacity-90 max-w-2xl mx-auto leading-relaxed text-center">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et
                dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris ut aliquip ex ea
                commodo consequat mauris ut diam vitae
            </p>
            <button
                class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 hover:scale-105 flex items-center mx-auto">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
                Start Your Journey
            </button>

            <!-- Quiz Section -->
            <div class="glass-card rounded-2xl p-6 mt-12 max-w-md mx-auto">
                <p class="text-white font-medium mb-4 text-lg">What is the quotient of 3/4 ÷ 1/2 = ?</p>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button
                        class="bg-green-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-green-600 transition-colors hover-pop">1.2</button>
                    <button
                        class="bg-blue-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-blue-600 transition-colors hover-pop">1.5</button>
                    <button
                        class="bg-red-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-red-600 transition-colors hover-pop">2.0</button>
                    <button
                        class="bg-purple-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-purple-600 transition-colors hover-pop">1.7</button>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-white text-opacity-70 text-sm">Lessons Completed</span>
                    <span class="text-white font-bold">75%</span>
                </div>
                <div class="w-full bg-white bg-opacity-20 rounded-full h-2">
                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2 -->
    <section class="py-12 md:py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-5xl font-bold text-center mb-8 md:mb-10 text-gray-900">
                How <span class="text-blue-600">AralSipnayan</span> <span class="text-blue-600">transforms
                    learning</span>
            </h2>
            <p class="text-center text-gray-600 mb-10 md:mb-12 max-w-2xl mx-auto text-base md:text-lg">
                Experience the future of mathematics education with our innovative platform designed specifically for
                Grade 6 students
            </p>

            <!-- Features Carousel -->
            <div class="relative mb-10 md:mb-12 lg:mb-16">
                <div class="swiper-viewport">
                    <div class="swiper-container">
                        <div class="swiper mySwiper">
                            <div class="swiper-wrapper">
                                <!-- Card 1 -->
                                <div class="swiper-slide" style="--gradient-from: #4F46E5; --gradient-to: #06B6D4;">
                                    <h1 class="font-baloo font-extrabold text-white text-4xl"
                                        style="text-shadow: 0 6px 0 #1c159c;">Adaptive Assessment</h1>
                                    <img src="{{ asset('images/carousel/caro1.png') }}" alt="Adaptive Assessment"
                                        style="margin-top: -4px">
                                    <p>Comprehensive Grade 6 assessments content</p>
                                </div>

                                <!-- Card 2 -->
                                <div class="swiper-slide" style="--gradient-from: #9333EA; --gradient-to: #EC4899;">
                                    <h1 class="font-baloo font-extrabold text-white text-4xl"
                                        style="text-shadow: 0 6px 0 #4a167a;">Interactive Learning</h1>
                                    <img src="{{ asset('images/carousel/caro2.png') }}" alt="Interactive Learning"
                                        style="margin-top: -16px">
                                    <p>Engaging quizzes and instant feedback</p>
                                </div>

                                <!-- Card 3 -->
                                <div class="swiper-slide" style="--gradient-from: #3B82F6; --gradient-to: #10B981;">
                                    <h1 class="font-baloo font-extrabold text-white text-4xl"
                                        style="text-shadow: 0 4px 0 #0f3779;">Cross Platform</h1>
                                    <img src="{{ asset('images/carousel/caro3-v5.png') }}" alt="Cross Platform">
                                    <p>Accessible from any device, everywhere</p>
                                </div>

                                <!-- Card 4 -->
                                <div class="swiper-slide" style="--gradient-from: #F97316; --gradient-to: #DC2626;">
                                    <h1 class="font-baloo font-extrabold text-white text-4xl"
                                        style="text-shadow: 0 4px 0 #aa4e0c; ">Progress Tracking</h1>
                                    <img src="{{ asset('images/carousel/caro4.png') }}" alt="Progress Tracking">
                                    <p>Monitor learning journey and achievements</p>
                                </div>

                                <!-- Card 5 -->
                                <div class="swiper-slide" style="--gradient-from: #1E3A8A; --gradient-to: #FACC15;">
                                    <h1 class="font-baloo font-extrabold text-white text-4xl"
                                        style="text-shadow: 0 4px 0 #10245c;">Gamified Experience</h1>
                                    <img src="{{ asset('images/carousel/caro4.png') }}" alt="Gamified Experience">
                                    <p>Points, badges, and leaderboards</p>
                                </div>

                            </div>

                            <!-- Navigation -->
                            <div class="swiper-button-next after:content-['']">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                            <div class="swiper-button-prev after:content-['']">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- Section 3 -->
    <section class="pb-6 md:pb-6 bg-white md:bg-blue-900 relative">
        <!-- Mobile design (only) -->
        <div class="block md:hidden relative z-10 py-4">
            <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-blue-900/90 border border-white/10 rounded-2xl p-5 shadow-2xl relative z-10">
                    <div class="text-center pb-4">
                        <h2 class="text-2xl font-extrabold text-yellow-300">AralSipnayan</h2>
                        <p class="text-white/80 text-sm">Features Overview</p>
                    </div>

                    <!-- Card: Adaptive Learning -->
                    <div
                        class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features1.png') }}" alt="Achievements"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold">Adaptive Learning</h3>
                                <p class="text-white/70 text-sm">Engaging quizzes & lessons</p>
                                <div class="bg-white/20 rounded-lg p-3 mt-3 text-center border border-white/10">
                                    <div class="text-white/70 text-xs">Current Lesson</div>
                                    <div class="text-yellow-300 font-semibold text-sm">Fractions</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Achievements -->
                    <div
                        class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features2.png') }}" alt="Achievements"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold">Achievements</h3>
                                <p class="text-white/70 text-sm">Badges and XP</p>
                                <div class="bg-white/20 rounded-lg p-3 mt-3 text-center border border-white/10">
                                    <div class="text-white/70 text-xs">Badges Earned</div>
                                    <div class="text-yellow-300 font-semibold">15/20</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Progress Tracking -->
                    <div
                        class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features3.png') }}" alt="Achievements"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold">Progress Tracking</h3>
                                <p class="text-white/70 text-sm">Real-time progress</p>
                                <div class="bg-white/20 rounded-lg p-3 mt-3 border border-white/10">
                                    <div class="text-white/70 text-xs mb-1">Progress</div>
                                    <div class="w-full h-2 bg-white/20 rounded-full mb-2">
                                        <div class="h-2 bg-yellow-300 rounded-full" style="width: 100%"></div>
                                    </div>
                                    <div class="text-center text-white/80 text-sm">Level 4</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Leaderboard -->
                    <div
                        class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features4.png') }}" alt="Achievements"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold">Leaderboard</h3>
                                <p class="text-white/70 text-sm">Your ranking</p>
                                <div class="bg-white/20 rounded-lg p-3 mt-3 border border-white/10">
                                    <div class="flex items-center justify-between text-sm mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-yellow-300">1</span>
                                            <span class="text-white/90">Mario S.</span>
                                        </div>
                                        <div class="text-yellow-300 font-semibold">1580</div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-yellow-300">2</span>
                                            <span class="text-white/90">You</span>
                                        </div>
                                        <div class="text-yellow-300 font-semibold">1250</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Desktop/Tablet design (md and up) -->
        <div class="hidden md:block relative z-10">
            <div class="max-w-7xl mx-auto px-8 lg:px-12">
                <div class="p-12">
                    <div class="text-center pb-6">
                        <h2 class="text-4xl font-extrabold text-yellow-300">AralSipnayan</h2>
                        <p class="text-white/80 text-lg">Features Overview</p>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <!-- Adaptive Learning -->
                        <div
                            class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features1.png') }}" alt="Achievements"
                                    class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Adaptive Learning</h3>
                                    <p class="text-white/70 text-sm">Engaging quizzes & lessons</p>
                                    <div class="bg-white/20 rounded-lg p-4 mt-4 text-center border border-white/10">
                                        <div class="text-white/70 text-xs">Current Lesson</div>
                                        <div class="text-yellow-300 font-semibold">Fractions</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Achievements -->
                        <div
                            class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features2.png') }}" alt="Achievements"
                                    class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Achievements</h3>
                                    <p class="text-white/70 text-sm">Badges and XP</p>
                                    <div class="bg-white/20 rounded-lg p-4 mt-4 text-center border border-white/10">
                                        <div class="text-white/70 text-xs">Badges Earned</div>
                                        <div class="text-yellow-300 font-semibold">15/20</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Tracking -->
                        <div
                            class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features3.png') }}" alt="Achievements"
                                    class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Progress Tracking</h3>
                                    <p class="text-white/70 text-sm">Real-time progress</p>
                                    <div class="bg-white/20 rounded-lg p-4 mt-4 border border-white/10">
                                        <div class="text-white/70 text-xs mb-2">Progress</div>
                                        <div class="w-full h-2 bg-white/20 rounded-full mb-2">
                                            <div class="h-2 bg-yellow-300 rounded-full" style="width: 100%"></div>
                                        </div>
                                        <div class="text-center text-white/80 text-sm">Level 4</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Leaderboard -->
                        <div
                            class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features4.png') }}" alt="Achievements"
                                    class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-white font-semibold">Leaderboard</h3>
                                    <p class="text-white/70 text-sm">Your ranking</p>
                                    <div class="bg-white/20 rounded-lg p-4 mt-4 border border-white/10">
                                        <div class="flex items-center justify-between text-sm mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-yellow-300">1</span>
                                                <span class="text-white/90">Mario S.</span>
                                            </div>
                                            <div class="text-yellow-300 font-semibold">1580</div>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-yellow-300">2</span>
                                                <span class="text-white/90">You</span>
                                            </div>
                                            <div class="text-yellow-300 font-semibold">1250</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bottom decorative waves (stick to section bottom, mobile only) -->
        <div class="absolute bottom-0 left-0 w-full md:hidden pointer-events-none select-none z-0 mt-6">
            <svg viewBox="0 0 375 120" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                <path d="M0 40 C80 80 160 0 240 30 C300 52 340 60 375 40 L375 120 L0 120 Z" fill="#FACC15" />
                <path d="M0 70 C90 100 180 40 260 70 C310 90 350 95 375 80 L375 120 L0 120 Z" fill="#3B82F6" />
            </svg>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initSwiper() {
                const screenWidth = window.innerWidth;
                let slideWidth = screenWidth < 768 ? 280 : 550;
                let viewportWidth = document.querySelector('.swiper-viewport').offsetWidth;
                let edgeOffset = (viewportWidth - slideWidth) / 2;

                const swiper = new Swiper(".mySwiper", {
                    effect: "coverflow",
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: "auto",
                    initialSlide: 2,
                    loop: true,
                    speed: 800,
                    watchSlidesProgress: true,
                    slideToClickedSlide: true,
                    coverflowEffect: {
                        rotate: 0,
                        stretch: 0,
                        depth: 200,
                        modifier: 1,
                        slideShadows: false,
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        320: {
                            spaceBetween: -20,
                            coverflowEffect: {
                                stretch: 20,
                                depth: 100,
                                rotate: 0,
                                modifier: 1,
                            }
                        },
                        480: {
                            spaceBetween: -30,
                            coverflowEffect: {
                                stretch: 30,
                                depth: 150,
                                rotate: 0,
                                modifier: 1,
                            }
                        },
                        768: {
                            spaceBetween: -40,
                            coverflowEffect: {
                                stretch: 40,
                                depth: 150,
                                rotate: 0,
                                modifier: 1,
                            }
                        },
                        1024: {
                            spaceBetween: -50,
                            coverflowEffect: {
                                stretch: 50,
                                depth: 200,
                                rotate: 0,
                                modifier: 1,
                            }
                        }
                    },
                    on: {
                        beforeInit: function () {
                            // Adjust container width to prevent overflow
                            let container = this.el.closest('.swiper-container');
                            if (container) {
                                container.style.overflow = 'hidden';
                                container.style.width = viewportWidth + 'px';
                                container.style.margin = '0 auto';
                            }
                        },
                        slideChange: function () {
                            // Ensure proper z-index for active and adjacent slides
                            const slides = this.slides;
                            slides.forEach((slide, index) => {
                                if (index === this.activeIndex) {
                                    slide.style.zIndex = '3';
                                } else if (
                                    index === this.activeIndex - 1 ||
                                    index === this.activeIndex + 1
                                ) {
                                    slide.style.zIndex = '2';
                                } else {
                                    slide.style.zIndex = '1';
                                }
                            });
                        }
                    }
                });

                // Update on window resize
                window.addEventListener('resize', function () {
                    viewportWidth = document.querySelector('.swiper-viewport').offsetWidth;
                    edgeOffset = (viewportWidth - slideWidth) / 2;
                    let container = document.querySelector('.swiper-container');
                    if (container) {
                        container.style.width = viewportWidth + 'px';
                    }
                    swiper.update();
                });
            }

            // Initialize the swiper
            initSwiper();

            // GSAP Animations for moving circles
            // Set initial positions and sizes for circles
            gsap.set('#circle1', {
                width: '120px',
                height: '120px',
                top: '10%',
                left: '10%'
            });

            gsap.set('#circle2', {
                width: '80px',
                height: '80px',
                top: '70%',
                left: '80%'
            });

            gsap.set('#circle3', {
                width: '150px',
                height: '150px',
                top: '40%',
                left: '70%'
            });

            gsap.set('#circle4', {
                width: '100px',
                height: '100px',
                top: '80%',
                left: '20%'
            });

            gsap.set('#circle5', {
                width: '60px',
                height: '60px',
                top: '20%',
                left: '85%'
            });

            // Create GSAP animations for each circle
            gsap.to('#circle1', {
                x: 'random(-100, 100)',
                y: 'random(-50, 50)',
                rotation: 'random(-180, 180)',
                duration: 'random(15, 25)',
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });

            gsap.to('#circle2', {
                x: 'random(-80, 80)',
                y: 'random(-40, 40)',
                rotation: 'random(-180, 180)',
                duration: 'random(12, 18)',
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });

            gsap.to('#circle3', {
                x: 'random(-120, 120)',
                y: 'random(-60, 60)',
                rotation: 'random(-180, 180)',
                duration: 'random(18, 28)',
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });

            gsap.to('#circle4', {
                x: 'random(-90, 90)',
                y: 'random(-45, 45)',
                rotation: 'random(-180, 180)',
                duration: 'random(14, 20)',
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });

            gsap.to('#circle5', {
                x: 'random(-70, 70)',
                y: 'random(-35, 35)',
                rotation: 'random(-180, 180)',
                duration: 'random(10, 16)',
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });
        });
    </script>
</body>

</html>