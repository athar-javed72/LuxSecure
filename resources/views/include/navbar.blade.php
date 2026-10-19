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
    class="fixed top-0 left-0 w-full z-50 h-[var(--header-h)] md:h-[72px] transition-[background-color,border-color] duration-300"
    style="--header-fg: var(--color-ivory); --header-bg: transparent;"
    :style="scrolled
        ? '--header-fg: var(--color-forest); --header-bg: color-mix(in srgb, var(--color-ivory-50) 98%, transparent);'
        : '--header-fg: var(--color-ivory); --header-bg: rgba(13,29,25,.25);'"
    :class="scrolled
        ? 'border-b border-[var(--color-border)] bg-[color-mix(in_srgb,var(--color-ivory-50)_98%,transparent)] shadow-none'
        : 'border-b border-transparent backdrop-blur-[6px]'"
    x-data="navBar"
    @keydown.escape.window="closeOverlays()"
>
    {{-- Over-hero readability veil (A4); removed when scrolled --}}
    <div
        class="pointer-events-none absolute inset-x-0 top-0 h-[160px] -z-10"
        x-show="!scrolled"
        x-cloak
        style="background: linear-gradient(to bottom, rgba(13,29,25,.85) 0%, rgba(13,29,25,.55) 60%, transparent 100%);"
        aria-hidden="true"
    ></div>
    <x-container class="h-full flex items-center justify-between gap-4">
        <a href="{{ $variant === 'admin' ? route('admin.dashboard') : route('home') }}"
           class="flex items-center gap-3 shrink-0 rounded-[var(--radius-button)] focus-visible:outline-2 focus-visible:outline-offset-2"
           :class="scrolled ? 'focus-visible:outline-[var(--color-forest)]' : 'focus-visible:outline-[var(--color-ivory)]'">
            <x-brand-logo layout="compact" surface="dual" class="shrink-0" />
            @if($variant === 'admin')
                <span class="hidden sm:inline text-[10px] tracking-[0.18em] uppercase" style="color: color-mix(in srgb, var(--header-fg) 70%, transparent)">Admin</span>
            @endif
        </a>

        <nav class="hidden lg:flex items-center gap-1 absolute left-1/2 -translate-x-1/2" aria-label="Main navigation">
            @foreach($menuConfig as $item)
                @php $active = $isActive($item['active'] ?? []); @endphp
                <a href="{{ $resolveHref($item) }}"
                   class="relative inline-flex items-center h-11 px-3 text-sm font-medium transition-colors duration-150"
                   style="color: var(--header-fg)"
                   @if($active) aria-current="page" @endif>
                    <span class="relative after:absolute after:left-0 after:right-0 after:-bottom-0.5 after:h-px after:bg-[var(--color-gold)] after:transition-opacity after:duration-150
                        {{ $active ? 'after:opacity-100' : 'after:opacity-0 hover:after:opacity-60' }}">
                        {{ $item['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="hidden lg:flex items-center gap-2">
            @if(($countries ?? collect())->isNotEmpty() && $variant !== 'admin')
                <div class="relative" @click.outside="closeLocale()">
                    <button type="button"
                            class="inline-flex items-center gap-1.5 h-11 px-3 rounded-[var(--radius-button)] text-xs font-medium border border-[color-mix(in_srgb,var(--header-fg)_25%,transparent)]"
                            style="color: var(--header-fg)"
                            @click="toggleLocale()"
                            :aria-expanded="localeExpanded()"
                            aria-label="Country and currency">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21a9 9 0 100-18 9 9 0 000 18zM2.5 12h19M12 2.5c2.5 2.8 3.8 5.8 3.8 9.5S14.5 18.7 12 21.5C9.5 18.7 8.2 15.7 8.2 12S9.5 5.3 12 2.5z"/></svg>
                        {{ session('currency', optional($activeCountry)->currency_code ?? 'PKR') }}
                    </button>
                    <div x-cloak x-show="localeOpen" class="absolute right-0 top-full mt-2 w-64 rounded-[var(--radius-card)] bg-white shadow-[var(--shadow-card)] border border-[var(--color-border)] p-3 z-50 text-[var(--color-ink)]">
                        <form method="POST" action="{{ route('locale.update') }}" x-ref="localeForm" class="space-y-2">
                            @csrf
                            <label class="block text-xs font-semibold text-[var(--color-muted)]">Country
                                <select name="country_id" class="mt-1 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] px-2 py-2 text-sm" @change="submitLocaleForm()">
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" @selected((int) session('country_id', optional($activeCountry)->id) === (int) $country->id)>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block text-xs font-semibold text-[var(--color-muted)]">Currency
                                <select name="currency" class="mt-1 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] px-2 py-2 text-sm" @change="submitLocaleForm()">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->code }}" @selected(session('currency', optional($activeCountry)->currency_code) === $currency->code)>{{ $currency->code }} ({{ $currency->symbol }})</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block text-xs font-semibold text-[var(--color-muted)]">Language
                                <select name="locale" class="mt-1 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] px-2 py-2 text-sm" @change="submitLocaleForm()">
                                    @foreach($languages as $language)
                                        <option value="{{ $language->code }}" @selected(app()->getLocale() === $language->code)>{{ $language->native_name ?: $language->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </form>
                    </div>
                </div>
            @endif
            @if($variant === 'admin')
                <a href="{{ route('home') }}" class="inline-flex items-center h-11 px-3 text-sm font-semibold" style="color: var(--header-fg)">View site</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button variant="secondary" type="submit" surface="dark">Logout</x-button>
                </form>
            @elseif(!$isAuthenticated)
                <a href="{{ route('login.form') }}"
                   class="inline-flex items-center h-11 px-3 text-sm font-semibold underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2"
                   style="color: var(--header-fg)"
                   :class="scrolled ? 'focus-visible:outline-[var(--color-forest)]' : 'focus-visible:outline-[var(--color-ivory)]'">
                    Login
                </a>
                <span x-show="!scrolled" x-cloak>
                    <x-button variant="primary" href="{{ route('contact') }}" surface="dark">Enquire</x-button>
                </span>
                <span x-show="scrolled" x-cloak>
                    <x-button variant="primary" href="{{ route('contact') }}" surface="light">Enquire</x-button>
                </span>
            @else
                @if($isAdmin)
                    <span x-show="!scrolled" x-cloak>
                        <x-button variant="secondary" href="{{ route('admin.dashboard') }}" surface="dark">Admin</x-button>
                    </span>
                    <span x-show="scrolled" x-cloak>
                        <x-button variant="secondary" href="{{ route('admin.dashboard') }}" surface="light">Admin</x-button>
                    </span>
                @endif
                @php
                    $notifConfig = [
                        'count' => Auth::user()->unreadNotifications()->count(),
                        'url' => route('notifications.unread-count'),
                    ];
                @endphp
                <a href="{{ route('notifications.index') }}"
                   class="relative inline-flex items-center justify-center w-11 h-11 rounded-[var(--radius-button)] border border-[color-mix(in_srgb,var(--header-fg)_25%,transparent)]"
                   style="color: var(--header-fg)"
                   aria-label="Notifications"
                   x-data="notifCount"
                   data-alpine-config="{{ json_encode($notifConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                    <span x-show="hasCount" x-text="count" class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-[var(--color-gold-dark)] text-[10px] font-bold text-white flex items-center justify-center"></span>
                </a>
                <div class="relative" @click.outside="closeProfile()">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 h-11 px-3 rounded-[var(--radius-button)] text-sm font-medium border border-[color-mix(in_srgb,var(--header-fg)_25%,transparent)]"
                        style="color: var(--header-fg)"
                        @click="toggleProfile()"
                        :aria-expanded="profileExpanded()"
                    >
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[var(--color-gold)] text-[var(--color-forest)] text-xs font-bold uppercase">
                            {{ mb_substr(Auth::user()->name, 0, 1, 'UTF-8') }}
                        </span>
                        <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                    </button>
                    <div x-cloak x-show="profileOpen" class="absolute right-0 top-full mt-2 w-56 rounded-[var(--radius-card)] bg-white shadow-[var(--shadow-card)] border border-[var(--color-border)] py-2 z-50">
                        <a href="{{ route('profile') }}" class="block px-4 py-2.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-ivory-50)]">My Profile</a>
                        <a href="{{ route('notifications.index') }}" class="block px-4 py-2.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-ivory-50)]">Notifications</a>
                        <a href="{{ route('phone.verify') }}" class="block px-4 py-2.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-ivory-50)]">Verify phone</a>
                        <a href="{{ route('kyc.index') }}" class="block px-4 py-2.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-ivory-50)]">KYC</a>
                        <a href="{{ route('profile') }}#favorites" class="block px-4 py-2.5 text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-ivory-50)]">Saved Properties</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-[var(--color-border)] mt-1 pt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-fs-danger hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2 lg:hidden">
            @guest
                <a href="{{ route('login.form') }}"
                   class="inline-flex items-center h-11 px-3 text-xs font-semibold"
                   style="color: var(--header-fg)">Login</a>
            @endguest
            <button
                type="button"
                class="inline-flex items-center justify-center w-11 h-11 rounded-[var(--radius-button)] border border-[color-mix(in_srgb,var(--header-fg)_25%,transparent)]"
                style="color: var(--header-fg)"
                @click="openNav()"
                :aria-expanded="navOpen ? 'true' : 'false'"
                aria-controls="mobile-nav-panel"
                aria-label="Open menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </x-container>

    <div x-cloak x-show="navOpen" class="lg:hidden fixed inset-0 z-[60]" role="dialog" aria-modal="true" aria-label="Mobile navigation">
        <div class="absolute inset-0 bg-[var(--color-forest-shadow)]/50" @click="closeNav()"></div>
        <div
            id="mobile-nav-panel"
            class="absolute inset-y-0 right-0 w-full max-w-sm bg-[var(--color-forest)] text-[var(--color-ivory)] shadow-lg flex flex-col"
            x-show="navOpen"
            x-transition:enter="transition transform duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @keydown.tab="trapFocus($event)"
        >
            <div class="h-[var(--header-h)] px-4 flex items-center justify-between border-b border-white/10">
                <x-brand-logo layout="monogram-nostar" surface="dark" />
                <button type="button" class="inline-flex items-center justify-center w-11 h-11 rounded-[var(--radius-button)] border border-white/20" @click="closeNav()" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                @foreach($menuConfig as $item)
                    <a href="{{ $resolveHref($item) }}" class="flex items-center min-h-11 px-4 rounded-[var(--radius-button)] text-lg font-medium text-[var(--color-ivory)] hover:bg-white/10" @click="closeNav()">{{ $item['label'] }}</a>
                @endforeach
            </nav>
            <div class="p-4 border-t border-white/10 space-y-2">
                @guest
                    <x-button variant="secondary" href="{{ route('login.form') }}" surface="dark" class="w-full">Login</x-button>
                    <x-button variant="primary" href="{{ route('contact') }}" surface="dark" class="w-full">Enquire</x-button>
                @else
                    <x-button variant="secondary" href="{{ route('profile') }}" surface="dark" class="w-full">Account</x-button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button variant="primary" type="submit" surface="dark" class="w-full">Logout</x-button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</header>
