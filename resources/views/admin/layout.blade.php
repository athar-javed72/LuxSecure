<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | {{ $siteName ?? config('app.name') }}</title>
    <link rel="icon" href="{{ $siteFavicon ?? \App\Support\BrandAssets::favicon() }}" type="image/svg+xml">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body
    class="min-h-screen bg-fs-surface text-fs-primary"
    x-data="adminShell"
>
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-[#142A25] text-white transform transition-transform duration-200 lg:translate-x-0"
        :class="sidebarClass()"
        :inert="sidebarHidden"
        :aria-hidden="ariaHidden()"
        aria-label="Admin sidebar"
    >
        <div class="h-[72px] px-4 flex items-center justify-between gap-2 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 min-w-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                <x-brand-logo layout="monogram-nostar" surface="dark" />
                <span class="font-bold truncate text-sm">Admin</span>
            </a>
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-sm border border-white/20 shrink-0"
                @click="closeSidebar()"
                aria-label="Close sidebar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-72px)]">
            @foreach(config('navigation.admin', []) as $item)
                @php
                    $perms = $item['permission'] ?? null;
                    $allowed = $perms === null;
                    if (is_string($perms)) {
                        $allowed = auth()->user()?->can($perms) ?? false;
                    } elseif (is_array($perms)) {
                        $allowed = collect($perms)->contains(fn ($p) => auth()->user()?->can($p));
                    }
                    $active = false;
                    foreach (($item['active'] ?? []) as $pattern) {
                        if (request()->routeIs($pattern)) { $active = true; break; }
                    }
                    try { $href = route($item['route']); } catch (\Throwable $e) { $href = '#'; }
                @endphp
                @if($allowed)
                    <a href="{{ $href }}"
                       class="flex items-center min-h-11 px-4 rounded-sm text-sm font-medium transition-colors duration-150 {{ $active ? 'bg-white/15 text-white' : 'text-fs-muted hover:bg-white/10 hover:text-white' }}"
                       @click="closeSidebar()">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
            <a href="{{ route('home') }}" class="flex items-center min-h-11 px-4 rounded-sm text-sm font-medium text-fs-muted hover:bg-white/10 hover:text-white transition-colors duration-150">View site</a>
        </nav>
    </aside>

    <div class="min-h-screen flex flex-col lg:pl-64">
        <header class="sticky top-0 z-30 h-[72px] bg-white border-b border-black/10 flex items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-sm border border-black/10 shrink-0"
                @click="openSidebar()"
                aria-label="Open sidebar"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-fs-primary truncate">@yield('title', 'Admin')</p>
                <p class="text-xs text-fs-muted truncate">Signed in as {{ auth()->user()->name }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <x-button type="submit" variant="ghost">Logout</x-button>
            </form>
        </header>

        <div
            x-cloak
            x-show="showOverlay"
            class="fixed inset-0 z-30 bg-fs-primary/40 lg:hidden"
            @click="closeSidebar()"
        ></div>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="w-full max-w-[1600px]">
                @if (session('success'))
                    <x-alert tone="success" class="mb-4" dismissible>{{ session('success') }}</x-alert>
                @endif
                @if (session('error'))
                    <x-alert tone="error" class="mb-4" dismissible>{{ session('error') }}</x-alert>
                @endif
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-black/10 bg-white px-4 sm:px-6 lg:px-8 py-4 text-xs text-fs-muted flex flex-col sm:flex-row sm:justify-between gap-2">
            <span>© {{ date('Y') }} {{ $siteName ?? config('app.name') }} Admin</span>
            @include('include.branding', ['brandingClass' => 'text-fs-muted', 'strongClass' => 'text-fs-brand font-medium'])
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
