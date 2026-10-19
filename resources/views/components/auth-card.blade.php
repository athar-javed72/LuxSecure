@props([
    'mode' => 'login', // login | register | forgot
])

@php
    $initialMode = in_array($mode, ['login', 'register', 'forgot'], true) ? $mode : 'login';
    $authCardConfig = [
        'mode' => $initialMode,
        'urls' => [
            'login' => route('login.form'),
            'register' => route('register.form'),
            'forgot' => route('password.request'),
        ],
    ];
    $panelImage = config('site-images.about.src', '/images/home/about.jpg');
    $panelAlt = config('site-images.about.alt', '');
    $brand = $siteName ?? config('app.name');
    $tagline = $siteTagline ?? \App\Support\BrandAssets::TAGLINE;
@endphp

<div
    class="fs-auth relative w-full overflow-hidden rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-ivory)] shadow-[var(--shadow-card)]"
    x-data="authCard"
    data-alpine-config="{{ json_encode($authCardConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
>
    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[min(720px,calc(100svh-var(--header-h)-6rem))]">
        {{-- Brand panel — atmosphere, not a floating card --}}
        <aside class="fs-auth-panel relative hidden lg:flex flex-col justify-between overflow-hidden bg-[var(--color-forest-shadow)] text-[var(--color-ivory)] p-10 xl:p-12">
            <img
                src="{{ $panelImage }}"
                alt="{{ $panelAlt }}"
                width="1200"
                height="1500"
                class="fs-auth-panel__media absolute inset-0 h-full w-full object-cover"
                decoding="async"
            >
            <div class="fs-auth-panel__veil absolute inset-0" aria-hidden="true"></div>

            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[var(--color-gold)] rounded-[var(--radius-button)]">
                    <x-brand-logo layout="stacked" surface="dark" class="[&_img]:h-[72px] [&_img]:min-w-[160px]" />
                </a>
                <p class="mt-6 max-w-[22ch] font-[family-name:var(--font-display)] text-[clamp(28px,3.2vw,40px)] font-medium leading-tight text-[var(--color-ivory)]" style="text-wrap: balance;">
                    {{ $tagline }}
                </p>
            </div>

            <div class="relative z-10 mt-16 min-h-[5.5rem]">
                <div x-show="isLogin" x-cloak x-transition.opacity.duration.200ms>
                    <x-eyebrow on-dark>Welcome back</x-eyebrow>
                    <p class="mt-3 text-[15px] text-[var(--color-sage)] max-w-[36ch]" style="text-wrap: pretty;">
                        Sign in to manage favourites, bookings, and conversations with {{ $brand }}.
                    </p>
                </div>
                <div x-show="isForgot" x-cloak x-transition.opacity.duration.200ms>
                    <x-eyebrow on-dark>Password help</x-eyebrow>
                    <p class="mt-3 text-[15px] text-[var(--color-sage)] max-w-[36ch]" style="text-wrap: pretty;">
                        Enter your email and we will send a secure reset link.
                    </p>
                </div>
                <div x-show="isRegister" x-cloak x-transition.opacity.duration.200ms>
                    <x-eyebrow on-dark>Join us</x-eyebrow>
                    <p class="mt-3 text-[15px] text-[var(--color-sage)] max-w-[36ch]" style="text-wrap: pretty;">
                        Create an account to save homes, enquire faster, and follow your stays.
                    </p>
                </div>
            </div>
        </aside>

        {{-- Form panel --}}
        <div class="relative flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-12 xl:px-14 bg-[var(--color-ivory-50)]">
            <div class="lg:hidden mb-8">
                <a href="{{ route('home') }}" class="inline-flex">
                    <x-brand-logo layout="compact" surface="light" />
                </a>
            </div>

            {{-- Mode switcher --}}
            <nav class="flex flex-wrap gap-1 p-1 rounded-[var(--radius-button)] bg-[var(--color-stone)]/50 border border-[var(--color-border)]" aria-label="Account modes">
                <button
                    type="button"
                    class="flex-1 min-h-11 px-3 rounded-[8px] text-sm font-semibold transition-colors duration-150"
                    :class="isLogin ? 'bg-[var(--color-forest)] text-[var(--color-ivory)]' : 'text-[var(--color-muted-strong)] hover:text-[var(--color-ink)]'"
                    @click="setMode('login')"
                    :aria-current="isLogin ? 'page' : null"
                >Login</button>
                <button
                    type="button"
                    class="flex-1 min-h-11 px-3 rounded-[8px] text-sm font-semibold transition-colors duration-150"
                    :class="isRegister ? 'bg-[var(--color-forest)] text-[var(--color-ivory)]' : 'text-[var(--color-muted-strong)] hover:text-[var(--color-ink)]'"
                    @click="setMode('register')"
                    :aria-current="isRegister ? 'page' : null"
                >Create account</button>
                <button
                    type="button"
                    class="flex-1 min-h-11 px-3 rounded-[8px] text-sm font-semibold transition-colors duration-150"
                    :class="isForgot ? 'bg-[var(--color-forest)] text-[var(--color-ivory)]' : 'text-[var(--color-muted-strong)] hover:text-[var(--color-ink)]'"
                    @click="setMode('forgot')"
                    :aria-current="isForgot ? 'page' : null"
                >Forgot</button>
            </nav>

            <div class="mt-8">
                @php
                    $deactivated = $errors->has('email') && str_contains(strtolower($errors->first('email') ?? ''), 'deactivated');
                @endphp

                @if(session('success') || session('status'))
                    <x-alert tone="success" class="mb-5">{{ session('success') ?? session('status') }}</x-alert>
                @endif

                @if($deactivated)
                    <x-empty-state
                        title="Account suspended"
                        description="This account has been deactivated. Contact support if you need access restored."
                        action-label="Back to home"
                        :action-href="route('home')"
                    />
                @elseif($errors->any())
                    <x-alert tone="error" class="mb-5">
                        <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </x-alert>
                @endif

                {{-- LOGIN --}}
                <div
                    x-show="isLogin"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                >
                    <h1 class="font-[family-name:var(--font-display)] text-[clamp(28px,4vw,36px)] font-semibold text-[var(--color-ink)] leading-tight">
                        Welcome back
                    </h1>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)] max-w-[42ch]" style="text-wrap: pretty;">
                        Sign in to continue with {{ $brand }}.
                    </p>

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-4">
                        @csrf
                        <x-input label="Email" name="email" type="email" :value="old('email')" placeholder="you@example.com" required autocomplete="username" />
                        <x-input label="Password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password" />
                        <x-button type="submit" variant="primary" class="w-full fs-btn-motion">Sign in</x-button>
                    </form>

                    <p class="mt-6 text-sm text-[var(--color-muted)]">
                        New here?
                        <button type="button" class="font-semibold text-[var(--color-gold-dark)] hover:underline" @click="setMode('register')">Create an account</button>
                    </p>
                </div>

                {{-- FORGOT --}}
                <div
                    x-show="isForgot"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                >
                    <h1 class="font-[family-name:var(--font-display)] text-[clamp(28px,4vw,36px)] font-semibold text-[var(--color-ink)] leading-tight">
                        Reset password
                    </h1>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)] max-w-[42ch]" style="text-wrap: pretty;">
                        We will email you a secure link to choose a new password.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-4">
                        @csrf
                        <x-input label="Email" name="email" id="forgot-email" type="email" :value="old('email')" required autocomplete="username" />
                        <x-button type="submit" variant="primary" class="w-full fs-btn-motion">Send reset link</x-button>
                    </form>

                    <p class="mt-6 text-sm text-[var(--color-muted)]">
                        Remembered it?
                        <button type="button" class="font-semibold text-[var(--color-gold-dark)] hover:underline" @click="setMode('login')">Back to login</button>
                    </p>
                </div>

                {{-- REGISTER --}}
                <div
                    x-show="isRegister"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                >
                    <h1 class="font-[family-name:var(--font-display)] text-[clamp(28px,4vw,36px)] font-semibold text-[var(--color-ink)] leading-tight">
                        Create your account
                    </h1>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)] max-w-[42ch]" style="text-wrap: pretty;">
                        A quieter way to explore spaces with {{ $brand }}.
                    </p>

                    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-4">
                        @csrf
                        <x-input label="Name" name="name" :value="old('name')" required autocomplete="name" />
                        <x-input label="Email" name="email" id="register-email" type="email" :value="old('email')" required autocomplete="email" />
                        <x-input label="Phone" name="phone" :value="old('phone')" placeholder="Optional" autocomplete="tel" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-input label="Password" name="password" id="register-password" type="password" required autocomplete="new-password" />
                            <x-input label="Confirm password" name="password_confirmation" type="password" required autocomplete="new-password" />
                        </div>
                        @php($legal = \App\Support\LegalAcceptanceService::currentPublished())
                        <x-legal-acceptances :terms="$legal['terms']" :privacy="$legal['privacy']" />
                        <x-button type="submit" variant="primary" class="w-full fs-btn-motion">Create account</x-button>
                    </form>

                    <p class="mt-6 text-sm text-[var(--color-muted)]">
                        Already have an account?
                        <button type="button" class="font-semibold text-[var(--color-gold-dark)] hover:underline" @click="setMode('login')">Sign in</button>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
