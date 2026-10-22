<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="@yield('meta_description', ($siteName ?? config('app.name')).' — '.\App\Support\BrandAssets::TAGLINE)">
    <title>@yield('title', 'Account') | {{ $siteName ?? config('app.name') }}</title>
    <link rel="icon" href="{{ $siteFavicon ?? \App\Support\BrandAssets::favicon() }}" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <meta property="og:title" content="@yield('title', 'Account') | {{ $siteName ?? config('app.name') }}">
    <meta property="og:description" content="@yield('meta_description', \App\Support\BrandAssets::TAGLINE)">
    <meta property="og:image" content="{{ url(\App\Support\BrandAssets::ogImage()) }}">
    @vite(['resources/css/app.css', 'resources/css/motion.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="mg-auth">
    <a href="#main-content" class="sr-only focus:not-sr-only">Skip to main content</a>

    <header class="mg-auth__top">
        <a href="{{ route('home') }}" class="mg-auth__back">← Back to Maskanoor</a>
        @php
            $localeMeta = strtoupper(substr((string) app()->getLocale(), 0, 2))
                .' · '
                .session('currency', 'PKR');
        @endphp
        <span class="mg-auth__meta" aria-label="Language and currency">{{ $localeMeta }}</span>
    </header>

    <main id="main-content" class="mg-auth__main">
        @yield('content')
    </main>

    <footer class="mg-auth__bottom">
        <p>© {{ date('Y') }} Maskanoor Group · Developed by Nexora Labs</p>
        <p class="mg-auth__bottom-links">
            <a href="{{ url('/pages/privacy-policy') }}">Privacy</a>
            <span aria-hidden="true"> · </span>
            <a href="{{ url('/pages/terms-of-service') }}">Terms</a>
            <span aria-hidden="true"> · </span>
            <a href="{{ route('contact') }}">Support</a>
        </p>
    </footer>

    <x-toast-host />
    @stack('scripts')
</body>
</html>
