@php
    $emailId = 'login-email';
    $passwordId = 'login-password';
    $emailError = $errors->first('email');
    $passwordError = $errors->first('password');
    $showFieldEmailError = $emailError && empty($loginFailed);
@endphp

<h1 class="mg-auth-card__title">Welcome back</h1>
<p class="mg-auth-card__lead">Sign in to continue with {{ $brand }}.</p>

@if(!empty($loginFailed))
    <div class="mg-auth-alert" role="alert">
        Email or password is incorrect. Try again or reset your password.
    </div>
@endif

<form
    method="POST"
    action="{{ route('login') }}"
    class="mt-2"
    @submit="loading = true"
>
    @csrf

    <div class="mg-auth-field">
        <label for="{{ $emailId }}">Email</label>
        <input
            id="{{ $emailId }}"
            name="email"
            type="email"
            value="{{ old('email') }}"
            placeholder="you@example.com"
            required
            autocomplete="username"
            @if($showFieldEmailError) aria-invalid="true" aria-describedby="login-email-error" @endif
        >
        @if($showFieldEmailError)
            <p id="login-email-error" class="mg-auth-field__error">{{ $emailError }}</p>
        @endif
    </div>

    <div class="mg-auth-field mg-auth-field--password">
        <label for="{{ $passwordId }}">Password</label>
        <input
            id="{{ $passwordId }}"
            name="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Your password"
            required
            autocomplete="current-password"
            @if($passwordError) aria-invalid="true" aria-describedby="login-password-error" @endif
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
        @if($passwordError)
            <p id="login-password-error" class="mg-auth-field__error">{{ $passwordError }}</p>
        @endif
    </div>

    <div class="mg-auth-meta-row">
        <label class="mg-auth-check">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            <span>Keep me signed in on this device</span>
        </label>
        <a href="{{ route('password.request') }}" class="mg-auth-link">Forgot password?</a>
    </div>

    <button type="submit" class="mg-auth-submit" :disabled="loading">
        <span x-show="!loading">Sign in</span>
        <span x-cloak x-show="loading">Please wait…</span>
    </button>
</form>

<p class="mg-auth-foot">
    New here?
    <a href="{{ route('register.form') }}" class="mg-auth-link">Create an account</a>
</p>
<p class="mg-auth-secure">Secure sign-in. Staff accounts use two-factor authentication.</p>
