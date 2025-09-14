<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AralSipnayan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 font-sans">

    {{-- Loader --}}
    @include('loaders.loader')

    {{-- Main Page Content (hidden until loader finishes) --}}
    <div id="app-content" class="opacity-0 transition-opacity duration-700">
        @yield('content')
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const loaderWrapper = document.getElementById("loader-wrapper");
            const appContent = document.getElementById("app-content");

            // Keep loader for 2 seconds
            setTimeout(() => {
                loaderWrapper.style.opacity = "0";
                loaderWrapper.style.transition = "opacity 0.5s ease";

                // After fade out, hide completely and show content
                setTimeout(() => {
                    loaderWrapper.style.display = "none";
                    appContent.classList.remove("opacity-0");
                    appContent.classList.add("opacity-100");
                }, 500);
            }, 2000);
        });
    </script>

</body>

</html>