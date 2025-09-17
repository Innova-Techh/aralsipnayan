<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-blue to-blue-900 flex items-center justify-center px-4 py-6 sm:p-4">
<script>
function refreshCaptcha() {
    const captchaImages = document.querySelectorAll('img[src*="captcha"]');
    captchaImages.forEach(img => {
        img.src = img.src.split('?')[0] + '?' + Date.now();
    });
}
</script>
    <div class="bg-white rounded-2xl p-6 sm:p-8 w-full max-w-lg shadow-2xl">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-6">
            <img src="{{ asset('images/Icons/Icon4.png') }}" alt="Logo" class="w-12 h-12 rounded-xl">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 text-center sm:text-left">AralSipnayan Admin Portal</h1>
        </div>

        <p class="text-gray-500 text-center mb-6 text-sm sm:text-base">Sign in to access the admin dashboard</p>


        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4 sm:space-y-5">
            @csrf
            <input type="hidden" id="role" name="role" value="Admin">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input id="username" name="username" type="text" required class="w-full px-4 py-3.5 sm:py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 text-base" placeholder="Enter your username" value="{{ old('username') }}">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required class="w-full px-4 py-3.5 sm:py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 text-base" placeholder="Enter your password">
                </div>
            </div>

            <div>
                <label for="captcha" class="block text-sm font-medium text-gray-700 mb-2">Security Code</label>
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                    <input id="captcha" name="captcha" type="text" class="flex-1 px-4 py-3.5 sm:py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20 text-base" placeholder="Enter security code">
                    <div class="border border-gray-300 rounded-xl p-3 sm:p-2 bg-gray-50 flex justify-center">
                        {!! captcha_img('default') !!}
                    </div>
                </div>
                <button type="button" onclick="refreshCaptcha()" class="text-primary-blue text-sm mt-2 underline hover:no-underline">
                    Refresh Code
                </button>
            </div>

            <button type="submit" class="w-full bg-primary-blue text-white font-semibold py-4 sm:py-3.5 rounded-xl hover:bg-blue-800 transition text-base">Sign In</button>
        </form>

        <div class="mt-6 border-t pt-4 text-sm text-gray-600">
            <p class="font-medium mb-2">Demo Accounts:</p>
            <div class="space-y-1">
                <p class="break-words">Super Admin: <span class="font-mono text-xs sm:text-sm">admin1</span> / <span class="font-mono text-xs sm:text-sm">123</span></p>
                <p class="break-words">Teacher: <span class="font-mono text-xs sm:text-sm">teacher1</span> / <span class="font-mono text-xs sm:text-sm">123</span></p>
            </div>
        </div>  
    </div>

</body>
</html>

@if ($errors->any())
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-sm sm:max-w-md p-6 relative mx-4">
            <!-- Close button - moved to upper right -->
            <button onclick="document.getElementById('errorModal').classList.add('hidden')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-2xl touch-manipulation">&times;</button>

            <!-- Logo -->
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/Icons/Icon1.png') }}" alt="AralSipnayan Logo" class="w-12 h-12 rounded-xl mb-2">
                <h2 class="text-lg font-bold text-primary-blue mb-3 text-center">Login Failed</h2>
                
                <!-- Error Messages -->
                <ul class="text-red-600 text-sm space-y-1 text-center">
                    @foreach ($errors->all() as $error)
                        <li class="break-words">{{ $error }}</li>
                    @endforeach
                </ul>
            
            </div>
        </div>
</div>
@endif


