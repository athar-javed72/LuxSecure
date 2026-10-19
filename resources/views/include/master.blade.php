<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(!empty($isRtl)) dir="rtl" @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', ($siteName ?? config('app.name')).' — '.\App\Support\BrandAssets::TAGLINE)">
    <title>@yield('title', 'Home') | {{ $siteName ?? config('app.name') }}</title>
    <link rel="icon" href="{{ $siteFavicon ?? \App\Support\BrandAssets::favicon() }}" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="/android-chrome-192x192.png" type="image/png" sizes="192x192">
    <link rel="icon" href="/android-chrome-512x512.png" type="image/png" sizes="512x512">
    <meta property="og:title" content="@yield('title', 'Home') | {{ $siteName ?? config('app.name') }}">
    <meta property="og:description" content="@yield('meta_description', \App\Support\BrandAssets::TAGLINE)">
    <meta property="og:image" content="{{ url(\App\Support\BrandAssets::ogImage()) }}">
    <meta name="twitter:card" content="summary_large_image">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
@php $isHome = request()->routeIs('home'); @endphp
<body class="min-h-screen flex flex-col bg-[var(--color-ivory)] text-[var(--color-ink)] {{ !empty($isRtl) ? 'rtl' : '' }}">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-3 focus:bg-[var(--color-forest)] focus:text-[var(--color-ivory)] focus:rounded-[var(--radius-button)]">
        Skip to main content
    </a>

    @include('include.navbar')

    <main id="main-content" class="flex-1 {{ $isHome ? 'pt-0' : 'pt-[var(--header-h)]' }}">
        @if (session('success') || session('error') || session('status'))
            <x-container class="pt-4 {{ $isHome ? 'pt-[calc(var(--header-h)+1rem)]' : '' }}">
                @if (session('success'))
                    <x-alert tone="success" class="mb-2" dismissible>{{ session('success') }}</x-alert>
                @endif
                @if (session('error'))
                    <x-alert tone="error" class="mb-2" dismissible>{{ session('error') }}</x-alert>
                @endif
                @if (session('status'))
                    <x-alert tone="info" class="mb-2" dismissible>{{ session('status') }}</x-alert>
                @endif
            </x-container>
        @endif
        @yield('home_content')
        @yield('content')
    </main>

    @include('include.footer')

    @stack('scripts')
</body>
</html>
