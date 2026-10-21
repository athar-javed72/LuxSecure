@props([
    'mode' => 'login', // login | register | forgot
])

@php
    $mode = in_array($mode, ['login', 'register', 'forgot'], true) ? $mode : 'login';
    $brand = $siteName ?? config('app.name');
    $panelMedia = app(\App\Services\MediaResolver::class)->resolve('home.about');
    $panelImage = $panelMedia['src'] ?? '/images/home/about.jpg';
    $panelAlt = $panelMedia['alt'] ?? 'Maskanoor building exterior';

    $panels = [
        'login' => [
            'label' => 'Welcome back',
            'items' => [
                'Manage favourites and bookings',
                'Track your tenancy and rent schedule',
                'Pay securely and keep every receipt',
            ],
        ],
        'register' => [
            'label' => 'Join us',
            'items' => [
                'Save homes and stays you like',
                'Enquire faster with one profile',
                'Follow bookings and rent in one place',
            ],
        ],
        'forgot' => [
            'label' => 'Password help',
            'items' => [
                'Reset links expire in 30 minutes',
                'We never email your password',
            ],
        ],
    ];
    $panel = $panels[$mode];

    $legal = \App\Support\LegalAcceptanceService::currentPublished();
    $termsHref = $legal['terms'] ? route('pages.show', $legal['terms']->slug) : url('/pages/terms-of-service');
    $privacyHref = $legal['privacy'] ? route('pages.show', $legal['privacy']->slug) : url('/pages/privacy-policy');

    $emailError = $errors->first('email');
    $deactivated = $emailError && str_contains(strtolower($emailError), 'deactivated');
    $loginFailed = $mode === 'login'
        && $emailError
        && ! $deactivated
        && (str_contains(strtolower($emailError), 'invalid') || str_contains(strtolower($emailError), 'incorrect') || $emailError === 'Invalid credentials');
    $forgotSuccess = $mode === 'forgot' && session('success');
    $registerSuccess = $mode === 'login' && session('success') && (
        str_contains(strtolower((string) session('success')), 'registration')
        || str_contains(strtolower((string) session('success')), 'verify')
        || str_contains(strtolower((string) session('success')), 'created')
    );
@endphp

<div
    class="mg-auth-card"
    x-data="authCard"
    data-alpine-config="{{ json_encode([
        'mode' => $mode,
        'registerSuccess' => (bool) $registerSuccess,
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
>
    <aside class="mg-auth-card__panel" aria-hidden="false">
        <img
            src="{{ $panelImage }}"
            alt="{{ $panelAlt }}"
            width="1200"
            height="1500"
            class="mg-auth-card__panel-media"
            decoding="async"
        >
        <div class="mg-auth-card__panel-veil" aria-hidden="true"></div>
        <div class="mg-auth-card__panel-inner">
            <a href="{{ route('home') }}" class="inline-flex" aria-label="Maskanoor home">
                <x-brand-logo layout="stacked" surface="dark" class="[&_img]:h-[64px] [&_img]:min-w-[140px]" />
            </a>
            <div class="mg-auth-card__panel-copy">
                <p class="mg-auth-card__panel-label">{{ $panel['label'] }}</p>
                <ul class="mg-auth-card__checks">
                    @foreach($panel['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </aside>

    <div class="mg-auth-card__form">
        <div class="mg-auth-mobile-logo">
            <a href="{{ route('home') }}" aria-label="Maskanoor home">
                <x-brand-logo layout="compact" surface="light" />
            </a>
        </div>

        <nav
            class="mg-auth-card__tabs {{ $mode === 'forgot' ? 'is-reserved' : '' }}"
            aria-label="Account modes"
            @if($mode === 'forgot') aria-hidden="true" @endif
        >
            <a
                href="{{ route('login.form') }}"
                @class(['is-active' => $mode === 'login'])
                @if($mode === 'login') aria-current="page" @endif
            >Login</a>
            <a
                href="{{ route('register.form') }}"
                @class(['is-active' => $mode === 'register'])
                @if($mode === 'register') aria-current="page" @endif
            >Create account</a>
        </nav>

        <div class="mg-auth-card__body">
            @if($deactivated)
                <x-empty-state
                    title="Account suspended"
                    description="This account has been deactivated. Contact support if you need access restored."
                    action-label="Back to home"
                    :action-href="route('home')"
                />
            @elseif($forgotSuccess)
                <div class="mg-auth-success" role="status">
                    <div class="mg-auth-success__tick" aria-hidden="true">✓</div>
                    <h1 class="mg-auth-card__title" style="margin-top:18px">Check your inbox</h1>
                    <p class="mg-auth-card__lead" style="margin-inline:auto">
                        If an account exists for that email, a reset link is on its way. It expires in 30 minutes.
                    </p>
                    <a href="{{ route('login.form') }}" class="mg-auth-submit" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;margin-top:24px">
                        Back to login
                    </a>
                    <p class="mg-auth-secure" style="margin-top:16px">
                        Nothing arrived? Check spam or
                        <a href="{{ route('password.request') }}" class="mg-auth-link">try again</a>.
                    </p>
                </div>
            @elseif($mode === 'login')
                <template x-if="registerSuccess">
                    <div class="mg-auth-success" role="status">
                        <div class="mg-auth-success__tick" aria-hidden="true">✓</div>
                        <h1 class="mg-auth-card__title" style="margin-top:18px">Account created</h1>
                        <p class="mg-auth-card__lead" style="margin-inline:auto">
                            Welcome to Maskanoor Group. You can now save homes, enquire faster and follow your stays.
                        </p>
                        <button type="button" class="mg-auth-submit" style="margin-top:24px" @click="registerSuccess = false">
                            Continue to sign in
                        </button>
                    </div>
                </template>
                <div x-show="!registerSuccess">
                    @include('auth.partials.login-form', ['brand' => $brand, 'loginFailed' => $loginFailed])
                </div>
            @elseif($mode === 'register')
                @include('auth.partials.register-form', [
                    'brand' => $brand,
                    'termsHref' => $termsHref,
                    'privacyHref' => $privacyHref,
                    'legal' => $legal,
                ])
            @else
                @include('auth.partials.forgot-form')
            @endif
        </div>
    </div>
</div>
