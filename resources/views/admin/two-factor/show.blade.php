@extends('admin.layout')

@section('title', 'Two-factor authentication')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Two-factor authentication'],
]" />

<x-page-header
    title="Two-factor authentication"
    subtitle="Required for admin-level accounts when enabled. Protects web and API logins."
/>

@if ($enabled)
    <x-panel class="max-w-md">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <x-status-badge status="verified">2FA enabled</x-status-badge>
        </div>
        <form method="POST" action="{{ route('admin.two-factor.disable') }}" class="space-y-3">
            @csrf
            <x-input label="Authenticator code to disable" name="code" inputmode="numeric" autocomplete="one-time-code" required />
            <x-button type="submit" variant="ghost">Disable 2FA</x-button>
        </form>
    </x-panel>
@else
    <x-panel class="max-w-md">
        <p class="text-sm text-fs-secondary mb-4">Add an extra layer of security before accessing the admin panel.</p>
        <x-button variant="primary" href="{{ route('admin.two-factor.enable') }}">Enable 2FA</x-button>
    </x-panel>
@endif
@endsection
