<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(!empty($isRtl)) dir="rtl" @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'LuxSecure — Find verified homes, villas, and apartments across Pakistan.')">
    <title>@yield('title', 'Home') | {{ $siteName ?? 'LuxSecure' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-fs-surface text-fs-primary {{ !empty($isRtl) ? 'rtl' : '' }}">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-3 focus:bg-fs-brand focus:text-white focus:rounded-sm">
        Skip to main content
    </a>

    @include('include.navbar')

    <main id="main-content" class="flex-1 pt-[72px]">
        @if (session('success') || session('error') || session('status'))
            <x-container class="pt-4">
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
