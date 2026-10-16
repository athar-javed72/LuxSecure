<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'LuxSecure — Find verified homes, villas, and apartments across Pakistan.')">
    <title>@yield('title', 'Home') | LuxSecure</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-white text-fs-primary">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-3 focus:bg-fs-brand focus:text-white focus:rounded-sm">
        Skip to main content
    </a>

    @include('include.navbar')

    <main id="main-content" class="flex-1 pt-[72px]">
        @yield('home_content')
        @yield('content')
    </main>

    @include('include.footer')

    @stack('scripts')
</body>
</html>
