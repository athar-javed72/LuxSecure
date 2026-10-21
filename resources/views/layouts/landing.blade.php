<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="@yield('meta_description', 'Hotels, residences and carefully selected properties across six countries, presented with care.')">
    <title>@yield('title', 'Elevating Spaces. Enriching Stays.') | {{ $siteName ?? config('app.name') }}</title>
    <link rel="icon" href="{{ $siteFavicon ?? \App\Support\BrandAssets::favicon() }}" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <meta property="og:title" content="@yield('title', 'Home') | {{ $siteName ?? config('app.name') }}">
    <meta property="og:description" content="@yield('meta_description', \App\Support\BrandAssets::TAGLINE)">
    <meta property="og:image" content="{{ url(\App\Support\BrandAssets::ogImage()) }}">
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
    @stack('styles')
</head>
<body class="mg-landing">
    <a href="#main-content" class="sr-only focus:not-sr-only">
        Skip to main content
    </a>

    <x-landing.navbar />

    <main id="main-content">
        @yield('content')
    </main>

    <x-landing.footer />
    <x-landing.floating-buttons />

    @stack('scripts')
</body>
</html>
