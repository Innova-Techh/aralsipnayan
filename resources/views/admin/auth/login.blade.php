<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-primary-blue to-blue-900 flex items-center justify-center p-4">
<script>
function refreshCaptcha() {
    const captchaImages = document.querySelectorAll('img[src*="captcha"]');
    captchaImages.forEach(img => {
        img.src = img.src.split('?')[0] + '?' + Date.now();
    });
}
</script>
    <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-center gap-3 mb-6">
            <img src="{{ asset('images/Icons/Icon4.png') }}" alt="Logo" class="w-12 h-12 rounded-xl">
            <h1 class="text-2xl font-bold text-gray-800">AralSipnayan Admin Portal</h1>
        </div>

        <p class="text-gray-500 text-center mb-6">Sign in to access the admin dashboard</p>


        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf
            <input type="hidden" id="role" name="role" value="Admin">

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input id="username" name="username" type="text" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20" placeholder="Enter your username" value="{{ old('username') }}">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20" placeholder="Enter your password">
                </div>
            </div>

            <div>
                <label for="captcha" class="block text-sm font-medium text-gray-700 mb-1">Security Code</label>
                <div class="flex gap-3 items-center">
                    <input id="captcha" name="captcha" type="text" class="flex-1 px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-primary-blue focus:ring-opacity-20" placeholder="Enter security code">
                    <div class="border border-gray-300 rounded-xl p-2 bg-gray-50">
                        {!! captcha_img('default') !!}
                    </div>
                </div>
                <button type="button" onclick="refreshCaptcha()" class="text-primary-blue text-sm mt-1 underline hover:no-underline">
                    Refresh Code
                </button>
            </div>

            <button type="submit" class="w-full bg-primary-blue text-white font-semibold py-3.5 rounded-xl hover:bg-blue-800 transition">Sign In</button>
        </form>

        <div class="mt-6 border-t pt-4 text-sm text-gray-600">
            <p class="font-medium mb-1">Demo Accounts:</p>
            <p>Super Admin: <span class="font-mono">admin1 </span> / <span class="font-mono"> 123</span></p>
            <p>Teacher: <span class="font-mono">teacher1 </span> / <span class="font-mono"> 123</span></p>
        </div>  
    </div>

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


