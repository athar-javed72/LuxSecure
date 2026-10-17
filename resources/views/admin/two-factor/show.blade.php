@extends('admin.layout')

@section('title', 'Two-factor authentication')

@section('content')
<h1 class="text-xl font-bold mb-4">Two-factor authentication</h1>
<p class="text-sm text-fs-secondary mb-6">Required for admin-level accounts when enabled. Protects web and API logins.</p>

@if ($enabled)
    <p class="mb-4 text-sm text-fs-brand font-medium">2FA is currently enabled on your account.</p>
    <form method="POST" action="{{ route('admin.two-factor.disable') }}" class="max-w-md space-y-3 rounded-md bg-white border border-black/5 p-4">
        @csrf
        <x-input label="Authenticator code to disable" name="code" required />
        <x-button type="submit" variant="ghost">Disable 2FA</x-button>
    </form>
@else
    <x-button variant="primary" href="{{ route('admin.two-factor.enable') }}">Enable 2FA</x-button>
@endif
@endsection
