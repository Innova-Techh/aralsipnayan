<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@include('loaders.loader')

@php
    $captchaHtml = captcha_img('default');
@endphp

<script>
function refreshCaptcha() {
    const captchaImages = document.querySelectorAll('img[src*="captcha"]');
    captchaImages.forEach(img => {
        img.src = img.src.split('?')[0] + '?' + Date.now();
    });
}
</script>
<body class="min-h-screen bg-gray-100 font-sans">
    <!-- Mobile layout -->
    <div class="md:hidden h-screen flex flex-col justify-center items-center px-5 py-4 overflow-hidden">
        <!-- Logo and Brand -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-4 mb-2">
                <img src="{{ asset('images/Icons/Icon1.png') }}" alt="AralSipnayan Logo" class="w-16 h-16 rounded-xl">
                <h1 class="text-3xl font-bold">
                    <span class="text-primary-blue">Aral</span><span class="text-primary-red">Sipnayan</span>
                </h1>
            </div>
            <p class="text-gray-600 text-sm">Math learning made fun!</p>
        </div>

        <!-- Login Card -->
        <div class="bg-primary-blue rounded-2xl p-8 w-full max-w-sm shadow-lg">
            <h2 class="text-white text-xl font-semibold text-center mb-8">Student Login</h2>
            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf
                <div class="mb-5">
                    <label for="username" class="block text-white text-sm font-medium mb-2">Username</label>
                    <input type="text"
                        class="w-full px-4 py-3 rounded-xl border-0 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30"
                        name="username" id="username" placeholder="Enter your username" required>
                </div>
                <div class="mb-8">
                    <label for="password" class="block text-white text-sm font-medium mb-2">Password</label>
                    <input type="password"
                        class="w-full px-4 py-3 rounded-xl border-0 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30"
                        name="password" id="password" placeholder="••••••••" required>
                </div>
                <div class="mb-8">
                         <label for="captcha" class="block text-white text-base font-medium mb-2">Security Code</label>
                         <div class="flex flex-col gap-3">
                             <input type="text" 
                                    class="w-full px-4 py-3 rounded-xl border-0 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30" 
                                    name="captcha"  
                                    placeholder="Enter security code"
                                    >
                             <div class="border border-gray-300 rounded-xl p-3 bg-white w-full flex items-center justify-center">
                                 <div class="w-full max-w-[140px] min-h-[40px] flex items-center justify-center [&>img]:!w-full [&>img]:!h-auto [&>img]:!min-h-[40px] sm:max-w-[120px] sm:[&>img]:!min-h-[35px]">
                                     {!! $captchaHtml !!}
                                 </div>
                             </div>
                         </div>
                         <button type="button" onclick="refreshCaptcha()" class="text-white text-sm mt-2 underline hover:no-underline">
                             Refresh Code
                         </button>
                </div>
                <button type="submit" id="loginBtn"
                    class="w-full bg-primary-yellow text-primary-blue font-semibold py-3.5 rounded-xl transition-all duration-200 hover:bg-yellow-400 hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-yellow-300">
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Desktop layout -->
    <div class="hidden md:flex min-h-screen">
        <!-- Left Section -->
        <div class="flex-1 bg-gradient-to-br from-primary-blue to-blue-900 flex items-center justify-center">
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

        <!-- Right Section -->
        <div class="flex-1 bg-gray-100 flex items-center justify-center">
            <div class="bg-white rounded-2xl p-12 xl:p-16 w-full max-w-md xl:max-w-lg shadow-xl">
                <h2 class="text-gray-800 text-2xl xl:text-3xl font-semibold text-center mb-8">Student Login</h2>
                <form method="POST" action="{{ route('login.submit') }}" id="desktopLoginForm">
                    @csrf
                    <div class="mb-6">
                        <label for="desktop-username" class="block text-gray-700 text-base font-medium mb-2">Student
                            Number</label>
                        <input type="text"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue"
                            name="username" id="desktop-username" required>
                    </div>
                    <div class="mb-8">
                        <label for="desktop-password"
                            class="block text-gray-700 text-base font-medium mb-2">Password</label>
                        <input type="password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue"
                            name="password" id="desktop-password" required>
                    </div>
                    <div class="mb-8">
                         <label for="captcha" class="block text-gray-700 text-base font-medium mb-2">Security Code</label>
                         <div class="flex gap-3 items-center">
                             <input type="text" 
                                    class="flex-1 px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 focus:border-primary-blue" 
                                    name="captcha"  
                                    placeholder="Enter security code"
                                    >
                             <div class="border border-gray-300 rounded-xl p-2 bg-gray-50">
                                 {!! $captchaHtml !!}
                             </div>
                         </div>
                         <button type="button" onclick="refreshCaptcha()" class="text-primary-blue text-sm mt-2 underline hover:no-underline">
                             Refresh Code
                         </button>
                     </div>
                    <button type="submit" id="desktopLoginBtn"
                        class="w-full bg-primary-yellow text-primary-blue font-semibold py-3.5 rounded-xl transition-all duration-200 hover:bg-yellow-400 hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-yellow-300">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll("form");
            const loaderWrapper = document.querySelector("#loader-wrapper");

            forms.forEach(form => {
                form.addEventListener("submit", function (e) {
                    // Show loader immediately when form is submitted
                    if (loaderWrapper) {
                        loaderWrapper.style.display = "flex";
                    }

                    // Disable all submit buttons to prevent double submission
                    const submitBtns = document.querySelectorAll('button[type="submit"]');
                    submitBtns.forEach(btn => {
                        btn.disabled = true;
                        btn.innerHTML = 'Logging in...';
                    });

                    // Hide the forms to show only the loader
                    const mobileLayout = document.querySelector('.md\\:hidden');
                    const desktopLayout = document.querySelector('.hidden.md\\:flex');
                    if (mobileLayout) mobileLayout.style.display = 'none';
                    if (desktopLayout) desktopLayout.style.display = 'none';
                });
            });

            // Hide loader if there are validation errors (page reloads with errors)
            const errorModal = document.querySelector("#errorModal");

            if (errorModal && loaderWrapper) {
                loaderWrapper.style.display = "none";

                // Show the appropriate form based on screen size
                const mobileLayout = document.querySelector('.md\\:hidden');
                const desktopLayout = document.querySelector('.hidden.md\\:flex');

                // Re-enable submit buttons
                const submitBtns = document.querySelectorAll('button[type="submit"]');
                submitBtns.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = 'Login';
                });
            }
        });
    </script>
</body>

</html>

@if ($errors->any())
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-lg w-96 p-6 relative">
            <!-- Close button - moved to upper right -->
            <button onclick="document.getElementById('errorModal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl">&times;</button>

            <!-- Logo -->
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/Icons/Icon1.png') }}" alt="AralSipnayan Logo" class="w-12 h-12 rounded-xl mb-2">
                <h2 class="text-lg font-bold text-primary-blue mb-3">Login Failed</h2>

                <!-- Error Messages -->
                <ul class="text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif