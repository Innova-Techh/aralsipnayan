<!-- resources/views/loaders/loader.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loader</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap');


        .font-baloo {
            font-family: "Baloo 2", sans-serif;
            font-weight: 800;
            font-style: normal;
        }

        .pl {
            width: 6em;
            height: 6em;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .pl {
                width: 4em;
                height: 4em;
            }
        }

        @media (min-width: 1024px) {
            .pl {
                width: 7em;
                height: 7em;
            }
        }

        .pl__ring {
            animation: ringA 2s linear infinite;
        }

        .pl__ring--a {
            stroke: #f42f25;
        }

        .pl__ring--b {
            animation-name: ringB;
            stroke: #f49725;
        }

        .pl__ring--c {
            animation-name: ringC;
            stroke: #255ff4;
        }

        .pl__ring--d {
            animation-name: ringD;
            stroke: #f42582;
        }

        /* Spinner Animations */
        @keyframes ringA {

            from,
            4% {
                stroke-dasharray: 0 660;
                stroke-width: 20;
                stroke-dashoffset: -330;
            }

            12% {
                stroke-dasharray: 60 600;
                stroke-width: 30;
                stroke-dashoffset: -335;
            }

            32% {
                stroke-dasharray: 60 600;
                stroke-width: 30;
                stroke-dashoffset: -595;
            }

            40%,
            54% {
                stroke-dasharray: 0 660;
                stroke-width: 20;
                stroke-dashoffset: -660;
            }

            62% {
                stroke-dasharray: 60 600;
                stroke-width: 30;
                stroke-dashoffset: -665;
            }

            82% {
                stroke-dasharray: 60 600;
                stroke-width: 30;
                stroke-dashoffset: -925;
            }

            90%,
            to {
                stroke-dasharray: 0 660;
                stroke-width: 20;
                stroke-dashoffset: -990;
            }
        }

        @keyframes ringB {

            from,
            12% {
                stroke-dasharray: 0 220;
                stroke-width: 20;
                stroke-dashoffset: -110;
            }

            20% {
                stroke-dasharray: 20 200;
                stroke-width: 30;
                stroke-dashoffset: -115;
            }

            40% {
                stroke-dasharray: 20 200;
                stroke-width: 30;
                stroke-dashoffset: -195;
            }

            48%,
            62% {
                stroke-dasharray: 0 220;
                stroke-width: 20;
                stroke-dashoffset: -220;
            }

            70% {
                stroke-dasharray: 20 200;
                stroke-width: 30;
                stroke-dashoffset: -225;
            }

            90% {
                stroke-dasharray: 20 200;
                stroke-width: 30;
                stroke-dashoffset: -305;
            }

            98%,
            to {
                stroke-dasharray: 0 220;
                stroke-width: 20;
                stroke-dashoffset: -330;
            }
        }

        @keyframes ringC {
            from {
                stroke-dasharray: 0 440;
                stroke-width: 20;
                stroke-dashoffset: 0;
            }

            8% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -5;
            }

            28% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -175;
            }

            36%,
            58% {
                stroke-dasharray: 0 440;
                stroke-width: 20;
                stroke-dashoffset: -220;
            }

            66% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -225;
            }

            86% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -395;
            }

            94%,
            to {
                stroke-dasharray: 0 440;
                stroke-width: 20;
                stroke-dashoffset: -440;
            }
        }

        @keyframes ringD {

            from,
            8% {
                stroke-dasharray: 0 440;
                stroke-width: 20;
                stroke-dashoffset: 0;
            }

            16% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -5;
            }

            36% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -175;
            }

            44%,
            50% {
                stroke-dasharray: 0 440;
                stroke-width: 20;
                stroke-dashoffset: -220;
            }

            58% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -225;
            }

            78% {
                stroke-dasharray: 40 400;
                stroke-width: 30;
                stroke-dashoffset: -395;
            }

            86%,
            to {
                stroke-dasharray: 0 440;
                stroke-width: 20;
                stroke-dashoffset: -440;
            }
        }

        /* Clean Logo + Text Animations */
        @keyframes slideUp {
            from {
                transform: translateY(20px);
            }

            to {
                transform: translateY(0);
            }
        }

        .animate-logo {
            opacity: 1 !important;
            animation: slideUp 1s ease forwards;
        }

        .reveal-text {
            display: inline-block;
            transform: translateY(20px);
            opacity: 1 !important;
            /* no haze */
            animation: slideUp 1s ease forwards;
        }

        /* Responsive text sizes */
        @media (max-width: 480px) {
            .text-responsive {
                font-size: 2.5rem !important;
                word-spacing: -0.2rem !important;
                letter-spacing: -0.15rem !important;
            }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .text-responsive {
                font-size: 3rem !important;
            }
        }

        @media (min-width: 1024px) {
            .text-responsive {
                font-size: 4.5rem !important;
            }
        }
    </style>
</head>

<body>
    <div id="loader-wrapper" class="flex flex-col fixed inset-0 items-center justify-center z-[9999] px-4"
        style="display: none;">

        <!-- Logo + Text -->
        <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 mb-4">
            <img src="{{ asset('images/Icons/Icon2.png') }}" alt="AralSipnayan Logo"
                class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 animate-logo">

            <span class="font-baloo text-blue-700 text-responsive text-center sm:text-left reveal-text"
                style="font-size: 4rem; text-shadow: 0 4px 0px #081846; word-spacing: -0.3rem; letter-spacing: -0.2rem;">
                Aral
                <span class="text-red-600"
                    style="text-shadow: 0 4px 0px #71161F; letter-spacing: -0.2rem;">Sipnayan</span>
            </span>
        </div>

        <!-- Spinner -->
        <svg class="pl" width="240" height="240" viewBox="0 0 240 240">
            <circle class="pl__ring pl__ring--a" cx="120" cy="120" r="105" fill="none"></circle>
            <circle class="pl__ring pl__ring--b" cx="120" cy="120" r="35" fill="none"></circle>
            <circle class="pl__ring pl__ring--c" cx="85" cy="120" r="70" fill="none"></circle>
            <circle class="pl__ring pl__ring--d" cx="155" cy="120" r="70" fill="none"></circle>
        </svg>
    </div>
</body>

</html>