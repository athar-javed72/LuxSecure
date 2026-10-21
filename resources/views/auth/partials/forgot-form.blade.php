@php
    $emailError = $errors->first('email');
@endphp

<h1 class="mg-auth-card__title">Reset password</h1>
<p class="mg-auth-card__lead">We will email you a secure link to choose a new password.</p>

<form
    method="POST"
    action="{{ route('password.email') }}"
    @submit="loading = true"
>
    @csrf

    <div class="mg-auth-field">
        <label for="forgot-email">Email</label>
        <input
            id="forgot-email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            placeholder="you@example.com"
            required
            autocomplete="username"
            @if($emailError) aria-invalid="true" aria-describedby="forgot-email-error" @endif
        >
        @if($emailError)
            <p id="forgot-email-error" class="mg-auth-field__error">{{ $emailError }}</p>
        @endif
    </div>

    <button type="submit" class="mg-auth-submit" :disabled="loading">
        <span x-show="!loading">Send reset link</span>
        <span x-cloak x-show="loading">Please wait…</span>
    </button>
</form>

<p class="mg-auth-foot">
    Remembered it?
    <a href="{{ route('login.form') }}" class="mg-auth-link">Back to login</a>
</p>
