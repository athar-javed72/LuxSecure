@php
    use Illuminate\Support\Facades\Auth;

    $variant = $variant ?? (request()->routeIs('admin.*') && Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin() ? 'admin' : 'public');
    $isAuthenticated = Auth::check();
    $isAdmin = $isAuthenticated && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin();

    if ($variant === 'admin') {
        $menuConfig = config('navigation.admin', []);
    } elseif ($isAuthenticated && !$isAdmin) {
        $menuConfig = config('navigation.user', []);
    } else {
        $menuConfig = config('navigation.public', []);
    }

    $resolveHref = function (array $item) {
        if (!empty($item['href'])) {
            return $item['href'];
        }
        try {
            $url = !empty($item['route']) ? route($item['route']) : url('/');
        } catch (\Throwable $e) {
            $url = url('/');
        }
        if (!empty($item['anchor'])) {
            $url .= $item['anchor'];
        }
        return $url;
    };

    $isActive = function (array $patterns) {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }
        return false;
    };
@endphp

<header
    class="fixed top-0 left-0 w-full z-50 h-[72px] border-b border-white/10 shadow-lg backdrop-blur-xl bg-fs-primary/97"
    x-data="navBar"
    @keydown.escape.window="closeOverlays()"
>
    <x-container class="h-full flex items-center justify-between gap-4">
        <a href="{{ $variant === 'admin' ? route('admin.dashboard') : route('home') }}"
           class="flex items-center gap-3 shrink-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-sm bg-fs-brand text-white font-bold text-sm tracking-wide">LS</span>
            <span class="flex flex-col leading-tight">
                <span class="fs-display text-xl font-semibold tracking-wide text-white">
                    {{ $siteName ?? 'LuxSecure' }}
                </span>
                <span class="hidden sm:inline text-[10px] tracking-[0.18em] uppercase text-slate-300/80">
                    {{ $variant === 'admin' ? 'Admin Control' : 'Trusted Property Platform' }}
                </span>
            </span>
        </a>

        <nav class="hidden lg:flex items-center gap-1" aria-label="Main navigation">
            @foreach($menuConfig as $item)
                @php $active = $isActive($item['active'] ?? []); @endphp
                <a href="{{ $resolveHref($item) }}"
                   class="inline-flex items-center h-11 px-4 rounded-full text-sm font-medium transition-colors duration-150 {{ $active ? 'bg-white/15 text-white' : 'text-slate-200/90 hover:text-white hover:bg-white/10' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden lg:flex items-center gap-2">
            @if($variant === 'admin')
                <x-button variant="ghost" href="{{ route('home') }}" class="!border-white/20 !text-white hover:!bg-white/10">View site</x-button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="secondary" type="submit">Logout</x-button>
                </form>
            @elseif(!$isAuthenticated)
                <x-button variant="ghost" href="{{ route('login.form') }}" class="!border-white/20 !text-white hover:!bg-white/10">Login</x-button>
                <x-button variant="secondary" href="{{ route('register.form') }}">Register</x-button>
                <x-button variant="primary" href="{{ route('properties') }}">Explore</x-button>
            @else
                @if($isAdmin)
                    <x-button variant="secondary" href="{{ route('admin.dashboard') }}">Admin</x-button>
                @endif
                @php
                    $notifConfig = [
                        'count' => Auth::user()->unreadNotifications()->count(),
                        'url' => route('notifications.unread-count'),
                    ];
                @endphp
                <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center justify-center w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/15 transition-colors duration-150 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-fs-brand" aria-label="Notifications"
                   x-data="notifCount"
                   data-alpine-config="{{ json_encode($notifConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                    <span x-show="hasCount" x-text="count" class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-fs-brand text-[10px] font-bold flex items-center justify-center"></span>
                </a>
                <div class="relative" @click.outside="closeProfile()">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 h-11 px-3 rounded-full bg-white/10 text-white text-sm font-medium hover:bg-white/15 transition-colors duration-150"
                        @click="toggleProfile()"
                        :aria-expanded="profileExpanded()"
                    >
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-fs-brand-light text-fs-brand text-xs font-bold uppercase">
                            {{ mb_substr(Auth::user()->name, 0, 1, 'UTF-8') }}
                        </span>
                        <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                    </button>
                    <div x-cloak x-show="profileOpen" class="absolute right-0 top-full mt-2 w-56 rounded-md bg-white shadow-lg border border-slate-100 py-2 z-50">
                        <a href="{{ route('profile') }}" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">My Profile</a>
                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">Notifications</a>
                        <a href="{{ route('phone.verify') }}" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">Verify phone</a>
                        <a href="{{ route('kyc.index') }}" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">KYC</a>
                        <a href="{{ route('profile') }}#favorites" class="block px-4 py-2.5 text-sm text-fs-secondary hover:bg-slate-50 hover:text-fs-primary">Saved Properties</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1 pt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-fs-danger hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 lg:hidden">
            @guest
                <a href="{{ route('login.form') }}" class="inline-flex items-center h-11 px-3 rounded-full text-xs font-semibold text-white border border-white/20">Login</a>
            @endguest
            <button
                type="button"
                class="inline-flex items-center justify-center w-11 h-11 rounded-full border border-white/20 text-white hover:bg-white/10 transition-colors duration-150"
                @click="openNav()"
                aria-label="Open menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </x-container>

    <div x-cloak x-show="navOpen" class="lg:hidden fixed inset-0 z-[60]" role="dialog" aria-modal="true" aria-label="Mobile navigation">
        <div class="absolute inset-0 bg-fs-primary/50" @click="closeNav()"></div>
        <div
            class="absolute inset-y-0 right-0 w-full max-w-sm bg-fs-primary text-white shadow-lg flex flex-col"
            x-show="navOpen"
            x-transition:enter="transition transform duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="h-[72px] px-4 flex items-center justify-between border-b border-white/10">
                <span class="text-base font-bold">LuxSecure</span>
                <button type="button" class="inline-flex items-center justify-center w-11 h-11 rounded-sm border border-white/20" @click="closeNav()" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                @foreach($menuConfig as $item)
                    <a href="{{ $resolveHref($item) }}" class="flex items-center min-h-11 px-4 rounded-sm text-sm font-medium text-slate-100 hover:bg-white/10" @click="closeNav()">{{ $item['label'] }}</a>
                @endforeach
            </nav>
            <div class="p-4 border-t border-white/10 space-y-2">
                @guest
                    <x-button variant="ghost" href="{{ route('login.form') }}" class="w-full !border-white/20 !text-white hover:!bg-white/10">Login</x-button>
                    <x-button variant="secondary" href="{{ route('register.form') }}" class="w-full">Register</x-button>
                    <x-button variant="primary" href="{{ route('properties') }}" class="w-full">Explore Properties</x-button>
                @else
                    <x-button variant="ghost" href="{{ route('profile') }}" class="w-full !border-white/20 !text-white hover:!bg-white/10">Account</x-button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button variant="secondary" type="submit" class="w-full">Logout</x-button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</header>
