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
@endphp

<div
    class="flex w-full max-w-4xl mx-auto rounded-md overflow-hidden shadow-lg border border-slate-100 bg-white"
    x-data="authCard"
    data-alpine-config="{{ json_encode($authCardConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
>
    {{-- Left panel stays mounted; only copy/animation swap --}}
    <div class="hidden md:flex w-1/2 flex-col items-center justify-center bg-gradient-to-br from-fs-primary via-[#152238] to-[#1e3a5f] p-8 text-center">
        <div class="relative w-[260px] h-[260px]">
            <lottie-player
                x-show="isLogin"
                x-cloak
                src="https://assets2.lottiefiles.com/packages/lf20_ktwnwv5m.json"
                background="transparent"
                speed="1"
                class="fs-lottie"
                loop
                autoplay
                aria-hidden="true"
            ></lottie-player>
            <lottie-player
                x-show="isForgot"
                x-cloak
                src="https://assets2.lottiefiles.com/packages/lf20_4kx2q32n.json"
                background="transparent"
                speed="1"
                class="fs-lottie"
                loop
                autoplay
                aria-hidden="true"
            ></lottie-player>
            <lottie-player
                x-show="isRegister"
                x-cloak
                src="https://assets2.lottiefiles.com/packages/lf20_jcikwtux.json"
                background="transparent"
                speed="1"
                class="fs-lottie"
                loop
                autoplay
                aria-hidden="true"
            ></lottie-player>
        </div>

        <div class="mt-4 min-h-[72px]">
            <template x-if="isLogin">
                <div>
                    <h2 class="text-lg font-bold text-white">Welcome back</h2>
                    <p class="mt-2 text-sm text-slate-300 max-w-prose mx-auto">Sign in to your LuxSecure account.</p>
                </div>
            </template>
            <template x-if="isForgot">
                <div>
                    <h2 class="text-lg font-bold text-white">Reset password</h2>
                    <p class="mt-2 text-sm text-slate-300 max-w-prose mx-auto">We’ll email you a secure reset link.</p>
                </div>
            </template>
            <template x-if="isRegister">
                <div>
                    <h2 class="text-lg font-bold text-white">Join LuxSecure</h2>
                    <p class="mt-2 text-sm text-slate-300 max-w-prose mx-auto">Save favorites and manage inquiries.</p>
                </div>
            </template>
        </div>
    </div>

    {{-- Right panel: forms swap without leaving the page --}}
    <div class="w-full md:w-1/2 p-6 sm:p-8 flex flex-col justify-center relative min-h-[480px]">
        @php
            $deactivated = $errors->has('email') && str_contains(strtolower($errors->first('email') ?? ''), 'deactivated');
        @endphp
        @if($deactivated)
            <div class="mb-6">
                <x-empty-state
                    title="Account suspended"
                    description="This account has been deactivated. Contact support if you need access restored."
                    action-label="Back to home"
                    :action-href="route('home')"
                />
            </div>
        @elseif($errors->any())
            <x-alert tone="error" class="mb-4">
                <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </x-alert>
        @endif

        {{-- LOGIN --}}
        <div x-show="isLogin" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <h1 class="text-xl font-bold text-fs-primary text-center">Login</h1>
            <p class="mt-2 text-sm text-fs-secondary text-center md:hidden">Welcome back to LuxSecure</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Email" name="email" type="email" :value="old('email')" placeholder="you@example.com" required />
                <x-input label="Password" name="password" type="password" placeholder="••••••••" required />
                <x-button type="submit" variant="primary" class="w-full">Login</x-button>
            </form>

            <div class="mt-6 flex flex-col sm:flex-row sm:justify-between gap-2 text-sm">
                <button type="button" class="text-fs-brand font-medium text-left hover:underline" @click="setMode('forgot')">Forgot password?</button>
                <button type="button" class="text-fs-secondary hover:text-fs-primary text-left" @click="setMode('register')">Create account</button>
            </div>
        </div>

        {{-- FORGOT --}}
        <div x-show="isForgot" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <h1 class="text-xl font-bold text-fs-primary">Forgot password</h1>
            <p class="mt-2 text-sm text-fs-secondary max-w-prose">Enter your email and we’ll send a reset link.</p>

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Email" name="email" id="forgot-email" type="email" :value="old('email')" required />
                <x-button type="submit" variant="primary" class="w-full">Send reset link</x-button>
            </form>

            <div class="mt-6">
                <button type="button" class="w-full inline-flex items-center justify-center h-11 px-5 rounded-sm text-sm font-semibold border border-slate-200 text-fs-primary hover:bg-slate-100 transition-colors duration-150" @click="setMode('login')">
                    Back to login
                </button>
            </div>
        </div>

        {{-- REGISTER --}}
        <div x-show="isRegister" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
            <h1 class="text-xl font-bold text-fs-primary text-center">Create account</h1>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                @csrf
                <x-input label="Name" name="name" :value="old('name')" required />
                <x-input label="Email" name="email" id="register-email" type="email" :value="old('email')" required />
                <x-input label="Phone" name="phone" :value="old('phone')" placeholder="Optional" />
                <x-input label="Password" name="password" id="register-password" type="password" required />
                <x-input label="Confirm password" name="password_confirmation" type="password" required />
                @php($legal = \App\Support\LegalAcceptanceService::currentPublished())
                <x-legal-acceptances :terms="$legal['terms']" :privacy="$legal['privacy']" />
                <x-button type="submit" variant="primary" class="w-full">Register</x-button>
            </form>

            <p class="mt-6 text-sm text-center text-fs-secondary">
                Already have an account?
                <button type="button" class="text-fs-brand font-medium hover:underline" @click="setMode('login')">Login</button>
            </p>
        </div>
    </div>
</div>
