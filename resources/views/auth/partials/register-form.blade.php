@php
    $intent = old('account_intent', 'guest');
@endphp

<h1 class="mg-auth-card__title">Create your account</h1>
<p class="mg-auth-card__lead">A quieter way to explore spaces with {{ $brand }}.</p>

<div class="mg-auth-segment" role="radiogroup" aria-label="Account type">
    @foreach(['guest' => 'Guest', 'tenant' => 'Tenant', 'landlord' => 'Landlord'] as $value => $label)
        <label>
            <input
                type="radio"
                name="account_intent_ui"
                value="{{ $value }}"
                @checked($intent === $value || ($value === 'guest' && ! old('account_intent_ui')))
            >
            <span>{{ $label }}</span>
        </label>
    @endforeach
</div>
<p class="sr-only">Account type is for display only and is not submitted to the server.</p>

<form
    method="POST"
    action="{{ route('register') }}"
    @submit="loading = true"
>
    @csrf

    <div class="mg-auth-field">
        <label for="register-name">Full name</label>
        <input
            id="register-name"
            name="name"
            type="text"
            value="{{ old('name') }}"
            placeholder="Enter your full name"
            required
            autocomplete="name"
            @if($errors->has('name')) aria-invalid="true" aria-describedby="register-name-error" @endif
        >
        @error('name')
            <p id="register-name-error" class="mg-auth-field__error">{{ $message }}</p>
        @enderror
    </div>

    <div class="mg-auth-row mg-auth-row--2">
        <div class="mg-auth-field">
            <label for="register-email">Email</label>
            <input
                id="register-email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                required
                autocomplete="email"
                @if($errors->has('email')) aria-invalid="true" aria-describedby="register-email-error" @endif
            >
            @error('email')
                <p id="register-email-error" class="mg-auth-field__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="mg-auth-field">
            <label for="register-phone">Phone (optional)</label>
            <input
                id="register-phone"
                name="phone"
                type="tel"
                value="{{ old('phone') }}"
                placeholder="+92 300 1234567"
                autocomplete="tel"
                @if($errors->has('phone')) aria-invalid="true" aria-describedby="register-phone-error" @endif
            >
            @error('phone')
                <p id="register-phone-error" class="mg-auth-field__error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mg-auth-row mg-auth-row--2">
        <div class="mg-auth-field mg-auth-field--password">
            <label for="register-password">Password</label>
            <input
                id="register-password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="At least 8 characters"
                required
                autocomplete="new-password"
                @if($errors->has('password')) aria-invalid="true" aria-describedby="register-password-error register-password-hint" @else aria-describedby="register-password-hint" @endif
            >
            <button
                type="button"
                class="mg-auth-field__toggle"
                @click="showPassword = !showPassword"
                :aria-pressed="showPassword.toString()"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
                <span x-text="showPassword ? 'Hide' : 'Show'">Show</span>
            </button>
            <p id="register-password-hint" class="mg-auth-field__hint">At least 8 characters.</p>
            @error('password')
                <p id="register-password-error" class="mg-auth-field__error">{{ $message }}</p>
            @enderror
        </div>
        <div class="mg-auth-field mg-auth-field--password">
            <label for="register-password-confirmation">Confirm password</label>
            <input
                id="register-password-confirmation"
                name="password_confirmation"
                :type="showPasswordConfirm ? 'text' : 'password'"
                placeholder="Confirm password"
                required
                autocomplete="new-password"
                @if($errors->has('password_confirmation')) aria-invalid="true" aria-describedby="register-password-confirmation-error" @endif
            >
            <button
                type="button"
                class="mg-auth-field__toggle"
                @click="showPasswordConfirm = !showPasswordConfirm"
                :aria-pressed="showPasswordConfirm.toString()"
                :aria-label="showPasswordConfirm ? 'Hide confirm password' : 'Show confirm password'"
            >
                <span x-text="showPasswordConfirm ? 'Hide' : 'Show'">Show</span>
            </button>
            @error('password_confirmation')
                <p id="register-password-confirmation-error" class="mg-auth-field__error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Backend still requires these when published legal pages exist --}}
    @if($legal['terms'] ?? null)
        <input type="hidden" name="accept_terms" value="1">
    @endif
    @if($legal['privacy'] ?? null)
        <input type="hidden" name="accept_privacy" value="1">
    @endif

    <button type="submit" class="mg-auth-submit" :disabled="loading">
        <span x-show="!loading">Create account</span>
        <span x-cloak x-show="loading">Please wait…</span>
    </button>

    <p class="mg-auth-consent">
        By creating an account you agree to our
        <a href="{{ $termsHref }}" target="_blank" rel="noopener">Terms</a>
        and
        <a href="{{ $privacyHref }}" target="_blank" rel="noopener">Privacy policy</a>.
    </p>
</form>

<p class="mg-auth-foot">
    Already have an account?
    <a href="{{ route('login.form') }}" class="mg-auth-link">Sign in</a>
</p>
