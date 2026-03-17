<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password | AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Three.js (required for Vanta) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Vanta.js FOG Effect -->
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.fog.min.js"></script>

    <style>
        .math-symbol {
            position: absolute;
            color: rgba(255, 255, 255, 0.15);
            font-size: 2.5rem;
            font-weight: bold;
            pointer-events: none;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
            animation: float-symbol 20s infinite ease-in-out;
        }

        @keyframes float-symbol {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.15;
            }

            25% {
                transform: translateY(-30px) rotate(5deg);
                opacity: 0.25;
            }

            50% {
                transform: translateY(-15px) rotate(-5deg);
                opacity: 0.2;
            }

            75% {
                transform: translateY(-40px) rotate(3deg);
                opacity: 0.18;
            }
        }

        .math-symbol:nth-child(1) {
            top: 10%;
            left: 15%;
            animation-delay: 0s;
            font-size: 3rem;
        }

        .math-symbol:nth-child(2) {
            top: 25%;
            left: 75%;
            animation-delay: 2s;
            font-size: 2.5rem;
        }

        .math-symbol:nth-child(3) {
            top: 45%;
            left: 25%;
            animation-delay: 4s;
            font-size: 2.8rem;
        }

        .math-symbol:nth-child(4) {
            top: 65%;
            left: 70%;
            animation-delay: 6s;
            font-size: 2.3rem;
        }

        .math-symbol:nth-child(5) {
            top: 80%;
            left: 30%;
            animation-delay: 8s;
            font-size: 3.2rem;
        }

        .math-symbol:nth-child(6) {
            top: 35%;
            left: 85%;
            animation-delay: 10s;
            font-size: 2.6rem;
        }

        .math-symbol:nth-child(7) {
            top: 55%;
            left: 10%;
            animation-delay: 12s;
            font-size: 2.9rem;
        }

        .math-symbol:nth-child(8) {
            top: 15%;
            left: 50%;
            animation-delay: 14s;
            font-size: 2.4rem;
        }

        .math-symbol:nth-child(9) {
            top: 75%;
            left: 60%;
            animation-delay: 16s;
            font-size: 3.1rem;
        }

        .math-symbol:nth-child(10) {
            top: 90%;
            left: 80%;
            animation-delay: 18s;
            font-size: 2.7rem;
        }

        #vanta-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .content-overlay {
            position: relative;
            z-index: 1;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .mobile-vanta-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 0;
        }

        .mobile-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        @media (max-width: 1023px) {
            .mobile-vanta-container {
                position: fixed;
                width: 100vw;
                height: 100vh;
            }
        }
    </style>
</head>

@include('loaders.loader')

<body class="min-h-screen font-baloo overflow-x-hidden">
    <!-- Mobile/Tablet layout -->
    <div class="lg:hidden">
        <div class="mobile-vanta-container" id="mobile-vanta-bg">
            <div class="math-symbol">+</div>
            <div class="math-symbol">−</div>
            <div class="math-symbol">×</div>
            <div class="math-symbol">÷</div>
            <div class="math-symbol">=</div>
            <div class="math-symbol">π</div>
            <div class="math-symbol">√</div>
            <div class="math-symbol">∑</div>
            <div class="math-symbol">∞</div>
            <div class="math-symbol">∫</div>
        </div>

        <div class="mobile-content flex flex-col justify-center items-center px-5 py-8">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <img src="{{ asset('images/Icons/Icon4.png') }}" alt="AralSipnayan Logo"
                        class="w-14 h-14 rounded-xl">
                    <h1 class="text-3xl font-bold">
                        <span class="text-white">Aral</span><span class="text-primary-yellow">Sipnayan</span>
                    </h1>
                </div>
                <p class="text-white text-opacity-90 text-sm">Math learning made fun!</p>
            </div>

            <div class="glass-card rounded-2xl p-8 w-full max-w-sm shadow-2xl">
                <h2 class="text-white text-3xl font-bold text-center mb-6 font-baloo">Reset Password</h2>

                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-500/15 border border-red-400/30 text-red-100 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-5 font-baloo">
                        <label for="email" class="block text-white text-sm font-medium mb-2 font-baloo">Email</label>
                        <input type="email"
                            class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40 font-baloo"
                            name="email" id="email" value="{{ old('email', $email) }}" required>
                    </div>

                    <div class="mb-5 font-baloo">
                        <label for="password" class="block text-white text-sm font-medium mb-2 font-baloo">New Password</label>
                        <input type="password"
                            class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40 font-baloo"
                            name="password" id="password" placeholder="Enter new password" required>
                    </div>

                    <div class="mb-7 font-baloo">
                        <label for="password_confirmation"
                            class="block text-white text-sm font-medium mb-2 font-baloo">Confirm Password</label>
                        <input type="password"
                            class="glass-input w-full px-4 py-3 rounded-xl border-0 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-40 font-baloo"
                            name="password_confirmation" id="password_confirmation" placeholder="Confirm new password" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary-blue text-white font-semibold py-3.5 rounded-xl transition-all duration-150 shadow-[0_8px_0_0_rgba(29,78,216,1),0_12px_20px_rgba(0,0,0,0.25)] hover:-translate-y-1 hover:shadow-[0_10px_0_0_rgba(29,78,216,1),0_16px_25px_rgba(0,0,0,0.3)] active:translate-y-2 active:shadow-[0_2px_0_0_rgba(29,78,216,1)] focus:outline-none focus:ring-4 focus:ring-blue-300 font-baloo">
                        Reset Password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-white/90 hover:text-white underline text-sm font-baloo">
                        Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop layout -->
    <div class="hidden lg:flex min-h-screen">
        <div class="flex-1 relative overflow-hidden">
            <div id="vanta-bg"></div>
            <div class="math-symbol">+</div>
            <div class="math-symbol">−</div>
            <div class="math-symbol">×</div>
            <div class="math-symbol">÷</div>
            <div class="math-symbol">=</div>
            <div class="math-symbol">π</div>
            <div class="math-symbol">√</div>
            <div class="math-symbol">∑</div>
            <div class="math-symbol">∞</div>
            <div class="math-symbol">∫</div>

            <div class="content-overlay flex items-center justify-center w-full h-full">
                <div class="text-center text-white">
                    <div class="flex items-center justify-center gap-4 mb-2">
                        <img src="{{ asset('images/Icons/Icon4.png') }}" alt="AralSipnayan Logo"
                            class="w-20 h-20 rounded-xl">
                        <h1 class="text-4xl xl:text-5xl font-bold">
                            <span class="text-white">Aral</span><span class="text-primary-yellow">Sipnayan</span>
                        </h1>
                    </div>
                    <p class="text-white text-opacity-80 text-lg xl:text-xl">Math learning made fun!</p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center"
            style="background-image: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.2)), url('{{ asset('images/login/login-form-bg-latest.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: bottom top;">

            <div class="bg-white/60 rounded-2xl p-12 xl:p-16 w-full max-w-md xl:max-w-lg shadow-xl">
                <h2 class="text-gray-800 text-4xl xl:text-3xl font-bold text-center mb-8 font-baloo">Reset Password</h2>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-red-100 border border-red-200 text-red-800 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="font-baloo">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-6">
                        <label for="desktop-email" class="block text-gray-700 text-base font-medium mb-2 font-baloo">Email</label>
                        <input type="email"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/45 text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue font-baloo"
                            name="email" id="desktop-email" value="{{ old('email', $email) }}" required>
                    </div>

                    <div class="mb-6">
                        <label for="desktop-password"
                            class="block text-gray-700 text-base font-medium mb-2 font-baloo">New Password</label>
                        <input type="password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/45 text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue font-baloo"
                            name="password" id="desktop-password" required>
                    </div>

                    <div class="mb-8">
                        <label for="desktop-password-confirmation"
                            class="block text-gray-700 text-base font-medium mb-2 font-baloo">Confirm Password</label>
                        <input type="password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/45 text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue font-baloo"
                            name="password_confirmation" id="desktop-password-confirmation" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary-blue text-white font-semibold py-3.5 rounded-xl transition-all duration-150 shadow-[0_8px_0_0_rgba(29,78,216,1),0_12px_20px_rgba(0,0,0,0.25)] hover:-translate-y-1 hover:shadow-[0_10px_0_0_rgba(29,78,216,1),0_16px_25px_rgba(0,0,0,0.3)] active:translate-y-2 active:shadow-[0_2px_0_0_rgba(29,78,216,1)] focus:outline-none focus:ring-4 focus:ring-blue-300 font-baloo">
                        Reset Password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 underline text-sm font-baloo">
                        Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                VANTA.FOG({
                    el: "#mobile-vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    highlightColor: 0xfacc15,
                    midtoneColor: 0x1d4ed8,
                    lowlightColor: 0x0b1220,
                    baseColor: 0x0b1220,
                    blurFactor: 0.75,
                    speed: 1.2,
                    zoom: 0.9
                });
            } else {
                VANTA.FOG({
                    el: "#vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    highlightColor: 0xfacc15,
                    midtoneColor: 0x1d4ed8,
                    lowlightColor: 0x0b1220,
                    baseColor: 0x0b1220,
                    blurFactor: 0.75,
                    speed: 1.2,
                    zoom: 0.9
                });
            }
        });
    </script>
</body>

</html>
