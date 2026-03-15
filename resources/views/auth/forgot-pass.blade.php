<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password | AralSipnayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-baloo bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center px-5 py-10">
    <div class="w-full max-w-md">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-2xl p-8">
            <div class="flex items-center justify-center gap-3 mb-6">
                <img src="{{ asset('images/Icons/Icon4.png') }}" alt="AralSipnayan Logo" class="w-12 h-12 rounded-xl">
                <h1 class="text-2xl font-bold">
                    <span class="text-white">Aral</span><span class="text-primary-yellow">Sipnayan</span>
                </h1>
            </div>

            <h2 class="text-white text-3xl font-bold text-center mb-2">Forgot Password</h2>
            <p class="text-white/80 text-sm text-center mb-6">
                Enter your email and we'll send you a password reset link.
            </p>

            @if (session('status'))
                <div class="mb-4 rounded-xl bg-emerald-500/15 border border-emerald-400/30 text-emerald-100 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-500/15 border border-red-400/30 text-red-100 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-white text-sm font-medium mb-2">Email</label>
                    <input id="email" name="email" type="email" required autocomplete="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-xl border-0 bg-white/90 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white/30"
                        placeholder="you@example.com">
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-primary-yellow text-black font-semibold hover:brightness-110 transition">
                    Send Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-white/90 hover:text-white underline text-sm">Back to login</a>
            </div>
        </div>
    </div>
</body>

</html>
