<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | LuxSecure</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body
    class="min-h-screen bg-fs-surface text-fs-primary"
    x-data="{
        sidebarOpen: false,
        isDesktop: window.matchMedia('(min-width: 1024px)').matches,
        init() {
            const mq = window.matchMedia('(min-width: 1024px)');
            const sync = (e) => { this.isDesktop = e.matches; if (e.matches) this.sidebarOpen = false; };
            mq.addEventListener('change', sync);
        }
    }"
>
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-fs-primary text-white transform transition-transform duration-200 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        :inert="!isDesktop && !sidebarOpen"
        :aria-hidden="(!isDesktop && !sidebarOpen).toString()"
        aria-label="Admin sidebar"
    >
        <div class="h-[72px] px-4 flex items-center justify-between gap-2 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 min-w-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                <span class="inline-flex items-center justify-center w-10 h-10 shrink-0 rounded-sm bg-fs-brand text-white font-bold text-sm tracking-wide">LS</span>
                <span class="font-bold truncate">LuxSecure Admin</span>
            </a>
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center w-11 h-11 rounded-sm border border-white/20 shrink-0"
                @click="sidebarOpen = false"
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
                       @click="sidebarOpen = false">
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
                @click="sidebarOpen = true"
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
            x-show="sidebarOpen && !isDesktop"
            class="fixed inset-0 z-30 bg-fs-primary/40 lg:hidden"
            @click="sidebarOpen = false"
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
            <span>© {{ date('Y') }} LuxSecure Admin</span>
            @include('include.branding', ['brandingClass' => 'text-fs-muted', 'strongClass' => 'text-fs-brand font-medium'])
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
