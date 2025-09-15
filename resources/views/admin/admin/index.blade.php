<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-6">
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Placeholder view. You will be redirected here after login.</p>
        <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="inline-block mt-6 px-4 py-2 bg-primary-blue text-white rounded">Logout</a>
        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="hidden">@csrf</form>
    </div>
</body>
</html>


