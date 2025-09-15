<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loader</title>

    <style>
        /* Fullscreen overlay */
        #loader-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.75); /* Dark background w/ opacity */
            z-index: 9999;
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
            stroke-width: 20;
            stroke-linecap: round;
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
                stroke-dashoffset: -330;
            }

            12% {
                stroke-dasharray: 60 600;
                stroke-dashoffset: -335;
            }

            32% {
                stroke-dasharray: 60 600;
                stroke-dashoffset: -595;
            }

            40%,
            54% {
                stroke-dasharray: 0 660;
                stroke-dashoffset: -660;
            }

            62% {
                stroke-dasharray: 60 600;
                stroke-dashoffset: -665;
            }

            82% {
                stroke-dasharray: 60 600;
                stroke-dashoffset: -925;
            }

            90%,
            to {
                stroke-dasharray: 0 660;
                stroke-dashoffset: -990;
            }
        }

        @keyframes ringB {
            from,
            12% {
                stroke-dasharray: 0 220;
                stroke-dashoffset: -110;
            }

            20% {
                stroke-dasharray: 20 200;
                stroke-dashoffset: -115;
            }

            40% {
                stroke-dasharray: 20 200;
                stroke-dashoffset: -195;
            }

            48%,
            62% {
                stroke-dasharray: 0 220;
                stroke-dashoffset: -220;
            }

            70% {
                stroke-dasharray: 20 200;
                stroke-dashoffset: -225;
            }

            90% {
                stroke-dasharray: 20 200;
                stroke-dashoffset: -305;
            }

            98%,
            to {
                stroke-dasharray: 0 220;
                stroke-dashoffset: -330;
            }
        }

        @keyframes ringC {
            from {
                stroke-dasharray: 0 440;
                stroke-dashoffset: 0;
            }

            8% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -5;
            }

            28% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -175;
            }

            36%,
            58% {
                stroke-dasharray: 0 440;
                stroke-dashoffset: -220;
            }

            66% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -225;
            }

            86% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -395;
            }

            94%,
            to {
                stroke-dasharray: 0 440;
                stroke-dashoffset: -440;
            }
        }

        @keyframes ringD {
            from,
            8% {
                stroke-dasharray: 0 440;
                stroke-dashoffset: 0;
            }

            16% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -5;
            }

            36% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -175;
            }

            44%,
            50% {
                stroke-dasharray: 0 440;
                stroke-dashoffset: -220;
            }

            58% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -225;
            }

            78% {
                stroke-dasharray: 40 400;
                stroke-dashoffset: -395;
            }

            86%,
            to {
                stroke-dasharray: 0 440;
                stroke-dashoffset: -440;
            }
        }
    </style>
</head>

<body>
    <div id="loader-overlay">
        <!-- Spinner -->
        <svg class="pl" width="240" height="240" viewBox="0 0 240 240">
            <circle class="pl__ring pl__ring--a" cx="120" cy="120" r="105" fill="none"></circle>
            <circle class="pl__ring pl__ring--b" cx="120" cy="120" r="35" fill="none"></circle>
            <circle class="pl__ring pl__ring--c" cx="85" cy="120" r="70" fill="none"></circle>
            <circle class="pl__ring pl__ring--d" cx="155" cy="120" r="70" fill="none"></circle>
        </svg>
    </div>

    <script>
        // Example usage: hide after page load
        window.addEventListener("load", () => {
            setTimeout(() => {
                document.getElementById("loader-overlay").style.display = "none";
            }, 1500); // auto-hide after 1.5s
        });
    </script>
</body>

</html>
