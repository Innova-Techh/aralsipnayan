<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AralSipnayan</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-poppins">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-row justify-between items-center py-3 sm:py-4">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10  rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl">
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold">
                        <span class="text-primary-blue">Aral</span><span class="text-primary-red">Sipnayan</span>
                    </h1>
                </div>
                <a href="{{ route('login') }}" class="btn bg-primary-blue btn-lg px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-white text-sm sm:text-base w-auto text-center">Login</a>
            </div>
        </nav>
    </header>

    <!-- Section 1 -->
     <section class="py-12 md:py-12 bg-gradient-math text-white full-screen-section relative overflow-hidden">
         <!-- Decorative Elements -->
         <div class="absolute top-32 left-8 w-2 h-2 bg-red-400 rounded-full opacity-80 animate-pulse-slow"></div>
         <div class="absolute top-40 right-12 text-yellow-400 opacity-60 text-2xl animate-float">✦</div>
         <div class="absolute top-64 left-16 w-1 h-1 bg-blue-300 rounded-full animate-float"></div>
         <div class="absolute top-80 right-8 w-2 h-2 bg-pink-400 rounded-full opacity-70 animate-float"></div>
         <div class="absolute bottom-80 left-12 w-2 h-2 bg-green-400 rounded-full opacity-60 animate-pulse-slow"></div>
         <div class="absolute bottom-72 right-16 text-purple-300 opacity-70 text-xl animate-float">✦</div>
         
         <!-- Mathematical Symbols -->
         <div class="absolute top-20 right-20 text-white opacity-20 text-4xl animate-float">÷</div>
         <div class="absolute bottom-40 left-20 text-white opacity-20 text-4xl animate-float">+</div>
         
         <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
             <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                 Master <span class="text-yellow-400">Advanced<br>Mathematics</span> with<br>
                 <span class="text-white">Interactive Learning</span>
             </h1>
             <p class="text-lg md:text-xl mb-12 opacity-90 max-w-2xl mx-auto leading-relaxed text-center">
                 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris ut aliquip ex ea commodo consequat mauris ut diam vitae
             </p>
             <button class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 hover:scale-105 flex items-center mx-auto">
                 <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                     <path d="M8 5v14l11-7z"/>
                 </svg>
                 Start Your Journey
             </button>
             
             <!-- Quiz Section -->
             <div class="glass-card rounded-2xl p-6 mt-12 max-w-md mx-auto">
                 <p class="text-white font-medium mb-4 text-lg">What is the quotient of 3/4 ÷ 1/2 = ?</p>
                 <div class="grid grid-cols-2 gap-3 mb-4">
                     <button class="bg-green-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-green-600 transition-colors hover-pop">1.2</button>
                     <button class="bg-blue-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-blue-600 transition-colors hover-pop">1.5</button>
                     <button class="bg-red-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-red-600 transition-colors hover-pop">2.0</button>
                     <button class="bg-purple-500 text-white px-4 py-3 rounded-full text-sm font-medium hover:bg-purple-600 transition-colors hover-pop">1.7</button>
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
         <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
             <h2 class="text-3xl md:text-5xl font-bold text-center mb-8 md:mb-10 text-gray-900">
                 How <span class="text-blue-600">AralSipnayan</span> <span class="text-blue-600">transforms learning</span>
             </h2>
             <p class="text-center text-gray-600 mb-10 md:mb-12 max-w-2xl mx-auto text-base md:text-lg">
                 Experience the future of mathematics education with our innovative platform designed specifically for Grade 6 students
             </p>
             
             <!-- Features Carousel -->
             <div class="relative mb-10 md:mb-12">
                 <div id="features-carousel" class="carousel">
                     <!-- Placeholder Card 1 -->
                     <div class="carousel-card">
                         <img src="{{ asset('images/carousel/Frame 368.png') }}" alt="Frame 368" class="carousel-image">
                     </div>

                     <!-- Placeholder Card 2 -->
                     <div class="carousel-card">
                         <img src="{{ asset('images/carousel/Frame 369.png') }}" alt="Frame 369" class="carousel-image">
                     </div>

                     <!-- Placeholder Card 3 -->
                     <div class="carousel-card">
                         <img src="{{ asset('images/carousel/Frame 370.png') }}" alt="Frame 370" class="carousel-image">
                     </div>

                     <!-- Placeholder Card 4 -->
                     <div class="carousel-card">
                         <img src="{{ asset('images/carousel/Frame 371.png') }}" alt="Frame 371" class="carousel-image">
                     </div>

                     <!-- Placeholder Card 5 -->
                     <div class="carousel-card">
                         <img src="{{ asset('images/carousel/Frame 372.png') }}" alt="Frame 372" class="carousel-image">
                     </div>
                 </div>
             </div>

             <!-- Bottom Section -->
             <div class="text-center">
                 <h3 class="text-2xl md:text-4xl font-bold text-gray-900 mb-3 md:mb-4">
                     Transform your <span class="text-blue-600">mathematical journey</span> today
                 </h3>
                 <p class="text-gray-600 text-center max-w-2xl mx-auto leading-relaxed text-base md:text-lg">
                     Join fellow Grade 6 students who are wanting to improved their mathematics skills through our innovative platform. Experience personalized learning that adapts to your pace and style
                 </p>
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
                    <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                        <img src="{{ asset('images/features/features1.png') }}" alt="Achievements" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
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
                    <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features2.png') }}" alt="Achievements" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
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
                    <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features3.png') }}" alt="Achievements" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
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
                    <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-4 transition-all duration-300 hover:bg-white/20 hover:-translate-y-0.5 hover:shadow-xl">
                        <div class="flex items-start gap-3">
                            <img src="{{ asset('images/features/features4.png') }}" alt="Achievements" class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg">
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
                        <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features1.png') }}" alt="Achievements" class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
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
                        <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features2.png') }}" alt="Achievements" class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
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
                        <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features3.png') }}" alt="Achievements" class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
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
                        <div class="bg-white/10 backdrop-blur-lg border border-white/10 rounded-xl p-6 transition-all duration-300 hover:bg-white/20 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="flex items-start gap-4">
                                <img src="{{ asset('images/features/features4.png') }}" alt="Achievements" class="w-10 h-10 lg:w-12 lg:h-12 rounded-lg">
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
                <path d="M0 40 C80 80 160 0 240 30 C300 52 340 60 375 40 L375 120 L0 120 Z" fill="#FACC15"/>
                <path d="M0 70 C90 100 180 40 260 70 C310 90 350 95 375 80 L375 120 L0 120 Z" fill="#3B82F6"/>
            </svg>
        </div>
    </section>
</body>
</html>