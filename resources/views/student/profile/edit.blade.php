@extends('layouts.user_layout')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') AralSipnayan</title>
    @vite('resources/css/app.css')
    <style>
        /* Custom glow animation */
        @keyframes glow-pulse {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 213, 136, 0.8),
                    0 0 30px rgba(255, 213, 136, 0.6),
                    0 0 40px rgba(255, 213, 136, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 213, 136, 1),
                    0 0 40px rgba(255, 213, 136, 0.8),
                    0 0 50px rgba(255, 213, 136, 0.6);
            }
        }

        .avatar-glow-1 {
            box-shadow: 0 0 20px rgba(255, 213, 136, 0.8), 0 0 30px rgba(255, 213, 136, 0.6), 0 0 40px rgba(255, 213, 136, 0.4);
            animation: glow-pulse-1 2s ease-in-out infinite;
        }

        @keyframes glow-pulse-1 {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 213, 136, 0.8), 0 0 30px rgba(255, 213, 136, 0.6), 0 0 40px rgba(255, 213, 136, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 213, 136, 1), 0 0 40px rgba(255, 213, 136, 0.8), 0 0 50px rgba(255, 213, 136, 0.6);
            }
        }

        .avatar-glow-2 {
            box-shadow: 0 0 20px rgba(255, 192, 203, 0.8), 0 0 30px rgba(255, 192, 203, 0.6), 0 0 40px rgba(255, 192, 203, 0.4);
            animation: glow-pulse-2 2s ease-in-out infinite;
        }

        @keyframes glow-pulse-2 {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 192, 203, 0.8), 0 0 30px rgba(255, 192, 203, 0.6), 0 0 40px rgba(255, 192, 203, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 192, 203, 1), 0 0 40px rgba(255, 192, 203, 0.8), 0 0 50px rgba(255, 192, 203, 0.6);
            }
        }

        .avatar-glow-3 {
            box-shadow: 0 0 20px rgba(255, 135, 128, 0.8), 0 0 30px rgba(255, 135, 128, 0.6), 0 0 40px rgba(255, 135, 128, 0.4);
            animation: glow-pulse-3 2s ease-in-out infinite;
        }

        @keyframes glow-pulse-3 {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 135, 128, 0.8), 0 0 30px rgba(255, 135, 128, 0.6), 0 0 40px rgba(255, 135, 128, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 135, 128, 1), 0 0 40px rgba(255, 135, 128, 0.8), 0 0 50px rgba(255, 135, 128, 0.6);
            }
        }

        .avatar-glow-4 {
            box-shadow: 0 0 20px rgba(232, 232, 232, 0.8), 0 0 30px rgba(232, 232, 232, 0.6), 0 0 40px rgba(232, 232, 232, 0.4);
            animation: glow-pulse-4 2s ease-in-out infinite;
        }

        @keyframes glow-pulse-4 {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(232, 232, 232, 0.8), 0 0 30px rgba(232, 232, 232, 0.6), 0 0 40px rgba(232, 232, 232, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(232, 232, 232, 1), 0 0 40px rgba(232, 232, 232, 0.8), 0 0 50px rgba(232, 232, 232, 0.6);
            }
        }

        .avatar-glow-5 {
            box-shadow: 0 0 20px rgba(67, 255, 250, 0.8), 0 0 30px rgba(67, 255, 250, 0.6), 0 0 40px rgba(67, 255, 250, 0.4);
            animation: glow-pulse-5 2s ease-in-out infinite;
        }

        @keyframes glow-pulse-5 {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(67, 255, 250, 0.8), 0 0 30px rgba(67, 255, 250, 0.6), 0 0 40px rgba(67, 255, 250, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(67, 255, 250, 1), 0 0 40px rgba(67, 255, 250, 0.8), 0 0 50px rgba(67, 255, 250, 0.6);
            }
        }

        .avatar-glow-6 {
            box-shadow: 0 0 20px rgba(255, 162, 115, 0.8), 0 0 30px rgba(255, 162, 115, 0.6), 0 0 40px rgba(255, 162, 115, 0.4);
            animation: glow-pulse-6 2s ease-in-out infinite;
        }

        @keyframes glow-pulse-6 {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 162, 115, 0.8), 0 0 30px rgba(255, 162, 115, 0.6), 0 0 40px rgba(255, 162, 115, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(255, 162, 115, 1), 0 0 40px rgba(255, 162, 115, 0.8), 0 0 50px rgba(255, 162, 115, 0.6);
            }
        }

        /* Smooth transition for glow effects */
        .avatar-container {
            transition: all 0.3s ease;
        }


        /* Custom responsive breakpoints for better avatar sizing */
        @media (min-width: 640px) {
            .avatar-grid {
                gap: 1.5rem;
                /* 24px gap for sm screens */
            }

            .mobile-scroll-content {
                padding-bottom: 8rem;
            }
        }

        @media (min-width: 768px) {
            .avatar-grid {
                gap: 2rem;
                /* 32px gap for md screens */
            }

            .mobile-scroll-content {
                padding-bottom: 4rem;
            }
        }

        @media (min-width: 1024px) {
            .avatar-grid {
                gap: 2.5rem;
                /* 40px gap for lg screens */
            }

            .mobile-scroll-content {
                padding-bottom: 4rem;
            }
        }

        @media (min-width: 1280px) {
            .avatar-grid {
                gap: 3rem;
                /* 48px gap for xl screens */
            }

            .mobile-scroll-content {
                padding-bottom: 4rem;
            }
        }

        /* Bokeh circles */
        .bokeh {
            position: absolute;
            border-radius: 50%;
            background: rgba(126, 136, 254, 0.25);
            filter: blur(5px);
            animation: float 12s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-20px) scale(1.1);
            }
        }
    </style>
</head>

<body class="bg-gray-100">

    @section('title', 'Choose Your Avatar')

    @section('content')
        <div class="min-h-screen">
            <div class="relative overflow-hidden bg-avatar-selection">
                <!-- Background Bokeh Circles -->
                <div class="absolute inset-0 overflow-hidden">
                    <!-- Bokeh 1 - Always visible, responsive sizing -->
                    <span
                        class="bokeh w-32 h-24 sm:w-40 sm:h-32 lg:w-56 lg:h-48 top-[5%] left-[5%] sm:top-[8%] sm:left-[8%] lg:top-[10%] lg:left-[10%]"></span>

                    <!-- Bokeh 2 - Always visible, responsive sizing -->
                    <span
                        class="bokeh w-24 h-24 sm:w-28 sm:h-28 lg:w-32 lg:h-32 top-[60%] right-[5%] sm:top-[35%] sm:left-[20%] lg:top-[75%] lg:left-[25%]"></span>

                    <!-- Bokeh 3 - Hidden on mobile, visible on tablet+ -->
                    <span
                        class="bokeh hidden sm:block w-36 h-28 lg:w-44 lg:h-36 bottom-[15%] right-[10%] lg:bottom-[20%] lg:right-[10%]"></span>

                    <!-- Bokeh 4 - Hidden on mobile and tablet, visible on desktop only -->
                    <span class="bokeh hidden lg:block w-40 h-40 bottom-[10%] left-[50%]"></span>

                    <!-- Bokeh 5 - Hidden on mobile and tablet, visible on desktop only -->
                    <span class="bokeh hidden lg:block w-28 h-28 top-[15%] right-[33%]"></span>
                </div>
                <!-- Avatar Header -->
                <div class="text-white px-6 pt-4">
                    <h2 class="pl-8 text-4xl font-baloo font-bold mb-2">Avatar</h2>
                </div>

                <!-- Main Avatar Display -->
                <div class="text-center mb-4">
                    <h1 class="text-white text-4xl font-baloo font-bold mb-4">Choose Your Avatar</h1>

                    <!-- Avatar Carousel Container -->
                    <div class="relative flex items-center justify-center mb-2">
                        <!-- Left Arrow -->
                        <div class="relative flex items-center justify-center">
                            <button id="prevBtn"
                                class="absolute left-4 md:left-4 text-white hover:text-gray-300 transition-colors z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </button>

                            <!-- Main Avatar Container with Background -->
                            <div class="relative w-[21rem] h-[21rem] flex items-center justify-center">
                                <!-- Radial Blur Background -->
                                <div class="absolute inset-0 bg-radial-blur-blue blur-[50px] opacity-70 rounded-full"></div>

                                <!-- Avatar Image -->
                                <div class="relative z-10">
                                    <img id="mainAvatar" src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}"
                                        alt="Selected Avatar" class="w-[21rem] h-[21rem] object-contain">
                                </div>
                            </div>

                            <button id="nextBtn"
                                class="absolute right-4 md:right-4 text-white hover:text-gray-300 transition-colors z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avatar Selection Grid -->
            <div class="relative p-5 sm:p-6 min-h-96 bg-white rounded-2xl -mt-4 z-10">
                <div class="mobile-scroll-content max-w-4xl mx-auto">
                    <div class="grid grid-cols-3 block-ce xl:gap-6 lg:gap-4 xs:gap-6 mb-8">
                        <!-- Avatar 1: BOY 1 AVATAR -->
                        <div class="avatar-option cursor-pointer transform hover:scale-105 transition-transform pt-8
                            @if(isset($student) && $student->gender === 'female') hidden @endif"
                            data-avatar="avatar1.png">
                            <div
                                class="avatar-container relative aspect-square xl:w-[12rem] xl:h-[15rem] lg:w-[10rem] lg:h-[12rem] xs:w-[6.9rem] xs:h-[8.5rem] bg-avatar-1 drop-shadow-avatar-1 rounded-2xl shadow-md flex items-end justify-center overflow-visible">
                                <img src="{{ asset('images/profile/avatar1.png') }}" alt="Avatar 1"
                                    class="absolute bottom-0 w-[100%] h-[115%]  -translate-y-1/6">
                            </div>
                        </div>

                        <!-- Avatar 2: GIRL 1 AVATAR -->
                        <div class="avatar-option cursor-pointer transform hover:scale-105 transition-transform pt-8
                            @if(isset($student) && $student->gender === 'male') hidden @endif"
                            data-avatar="avatar2.png">
                            <div
                                class="avatar-container relative aspect-square xl:w-[12rem] xl:h-[15rem] lg:w-[10rem] lg:h-[12rem] xs:w-[6.9rem] xs:h-[8.5rem] bg-avatar-2 drop-shadow-avatar-2 rounded-2xl shadow-md flex items-end justify-center overflow-visible">
                                <img src="{{ asset('images/profile/avatar2.png') }}" alt="Avatar 2"
                                    class="absolute bottom-0 w-[100%] h-[120%] object-contain -translate-y-1/6">
                            </div>
                        </div>

                        <!-- Avatar 3: GIRL 2 AVATAR -->
                        <div class="avatar-option cursor-pointer transform hover:scale-105 transition-transform pt-8
                            @if(isset($student) && $student->gender === 'male') hidden @endif"
                            data-avatar="avatar3.png">
                            <div
                                class="avatar-container relative aspect-square xl:w-[12rem] xl:h-[15rem] lg:w-[10rem] lg:h-[12rem] xs:w-[6.9rem] xs:h-[8.5rem] bg-avatar-3 drop-shadow-avatar-3 rounded-2xl shadow-md flex items-end justify-center overflow-visible">
                                <img src="{{ asset('images/profile/avatar3.png') }}" alt="Avatar 3"
                                    class="absolute bottom-0 w-[100%] h-[120%] object-contain -translate-y-1/6">
                            </div>
                        </div>

                        <!-- Avatar 4: BOY 2 AVATAR -->
                        <div class="avatar-option cursor-pointer transform hover:scale-105 transition-transform pt-8
                            @if(isset($student) && $student->gender === 'female') hidden @endif"
                            data-avatar="avatar4.png">
                            <div
                                class="avatar-container relative aspect-square  xl:w-[12rem] xl:h-[15rem] lg:w-[10rem] lg:h-[12rem] xs:w-[6.9rem] xs:h-[8.5rem] bg-avatar-4 drop-shadow-avatar-4 rounded-2xl shadow-md flex items-end justify-center overflow-visible">
                                <img src="{{ asset('images/profile/avatar4.png') }}" alt="Avatar 4"
                                    class="absolute bottom-0 w-[100%] h-[120%] object-contain -translate-y-1/6">
                            </div>
                        </div>

                        <!-- Avatar 5: GIRL 3 AVATAR -->
                        <div class="avatar-option cursor-pointer transform hover:scale-105 transition-transform pt-8
                            @if(isset($student) && $student->gender === 'male') hidden @endif"
                            data-avatar="avatar5.png">
                            <div
                                class="avatar-container relative aspect-square xl:w-[12rem] xl:h-[15rem] lg:w-[10rem] lg:h-[12rem] xs:w-[6.9rem] xs:h-[8.5rem] bg-avatar-5 drop-shadow-avatar-5 rounded-2xl shadow-md flex items-end justify-center overflow-visible">
                                <img src="{{ asset('images/profile/avatar5.png') }}" alt="Avatar 5"
                                    class="absolute bottom-0 w-[100%] h-[120%] object-contain -translate-y-1/6">
                            </div>
                        </div>

                        <!-- Avatar 6: BOY 3 AVATAR -->
                        <div class="avatar-option cursor-pointer transform hover:scale-105 transition-transform pt-8
                            @if(isset($student) && $student->gender === 'female') hidden @endif"
                            data-avatar="avatar6.png">
                            <div
                                class="avatar-container relative aspect-square xl:w-[12rem] xl:h-[15rem] lg:w-[10rem] lg:h-[12rem] xs:w-[6.9rem] xs:h-[8.5rem] bg-avatar-6 drop-shadow-avatar-6 rounded-2xl shadow-md flex items-end justify-center overflow-visible">
                                <img src="{{ asset('images/profile/avatar6.png') }}" alt="Avatar 6"
                                    class="absolute bottom-0 w-[100%] h-[120%] object-contain -translate-y-1/6">
                            </div>
                        </div>
                    </div>

                    <!-- Select Avatar Button -->
                    <div class="text-center pt-8 ">
                        <button id="selectAvatarBtn"
                            class="w-3/4 bg-select-avatar drop-shadow-select-avatar text-outline-custom-[#CE8E21]  hover:bg-yellow-500 text-white font-baloo font-bold py-4 px-12 rounded-full xl:text-2xl xs:text-xl shadow-lg transform hover:scale-105 transition-all">
                            <span>Select Your Avatar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Get all avatar options and filter only visible ones
                const allAvatarOptions = document.querySelectorAll('.avatar-option');
                const visibleAvatarOptions = Array.from(allAvatarOptions).filter(option => !option.classList.contains('hidden'));

                // Create arrays based on visible avatars only
                const avatars = visibleAvatarOptions.map(option => option.getAttribute('data-avatar'));

                // Avatar glow classes corresponding to each avatar
                const allAvatarGlowClasses = [
                    'avatar-glow-1', // Avatar 1 - Yellow/Gold glow
                    'avatar-glow-2', // Avatar 2 - Pink glow
                    'avatar-glow-3', // Avatar 3 - Red/Coral glow
                    'avatar-glow-4', // Avatar 4 - Gray/Silver glow
                    'avatar-glow-5', // Avatar 5 - Cyan/Teal glow
                    'avatar-glow-6', // Avatar 6 - Orange glow
                ];

                // Map visible avatars to their corresponding glow classes
                const avatarGlowClasses = avatars.map(avatar => {
                    const avatarIndex = parseInt(avatar.replace('avatar', '').replace('.png', '')) - 1;
                    return allAvatarGlowClasses[avatarIndex];
                });

                // Detect current avatar from the main avatar image src
                const mainAvatar = document.getElementById('mainAvatar');
                const currentAvatarSrc = mainAvatar.src;
                let currentAvatarIndex = 0; // Default to first visible avatar

                // Try to detect current avatar from src
                avatars.forEach((avatar, index) => {
                    if (currentAvatarSrc.includes(avatar)) {
                        currentAvatarIndex = index;
                    }
                });

                // If current avatar is not in the visible list, default to first visible avatar
                if (!avatars.some(avatar => currentAvatarSrc.includes(avatar))) {
                    currentAvatarIndex = 0;
                }

                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const avatarOptions = visibleAvatarOptions; // Use only visible options
                const selectAvatarBtn = document.getElementById('selectAvatarBtn');

                // Function to update main avatar display
                function updateMainAvatar(index) {
                    mainAvatar.src = `{{ asset('images/profile/') }}/${avatars[index]}`;
                    currentAvatarIndex = index;

                    // Remove all glow classes from all visible avatar containers
                    avatarOptions.forEach((option, i) => {
                        const container = option.querySelector('.avatar-container');
                        // Remove all possible glow classes
                        allAvatarGlowClasses.forEach(glowClass => {
                            container.classList.remove(glowClass);
                        });
                    });

                    // Add glow effect to selected avatar
                    if (avatarOptions[index]) {
                        const selectedContainer = avatarOptions[index].querySelector('.avatar-container');
                        if (avatarGlowClasses[index]) {
                            selectedContainer.classList.add(avatarGlowClasses[index]);
                        }
                    }
                }

                // Initialize with current avatar selected
                updateMainAvatar(currentAvatarIndex);

                // Previous button
                prevBtn.addEventListener('click', function () {
                    currentAvatarIndex = (currentAvatarIndex - 1 + avatars.length) % avatars.length;
                    updateMainAvatar(currentAvatarIndex);
                });

                // Next button
                nextBtn.addEventListener('click', function () {
                    currentAvatarIndex = (currentAvatarIndex + 1) % avatars.length;
                    updateMainAvatar(currentAvatarIndex);
                });

                // Avatar option clicks
                avatarOptions.forEach((option, index) => {
                    option.addEventListener('click', function () {
                        updateMainAvatar(index);
                    });
                });

                // Select avatar button
                selectAvatarBtn.addEventListener('click', function () {
                    const selectedAvatar = avatars[currentAvatarIndex];

                    // Show loading state
                    selectAvatarBtn.textContent = 'Saving...';
                    selectAvatarBtn.disabled = true;

                    // Send AJAX request to complete onboarding with selected avatar
                    fetch('{{ route("student.onboarding.complete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            avatar: selectedAvatar
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                alert(data.message);
                                // Redirect to dashboard
                                window.location.href = data.redirect_url;
                            } else {
                                alert('Error: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating avatar. Please try again.');
                        })
                        .finally(() => {
                            // Reset button state
                            selectAvatarBtn.textContent = 'Select Your Avatar';
                            selectAvatarBtn.disabled = false;
                        });
                });
            });
        </script>
    @endsection
</body>

</html>