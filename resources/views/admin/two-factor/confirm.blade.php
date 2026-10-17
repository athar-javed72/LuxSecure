@extends('admin.layout')

@section('title', 'Confirm 2FA')

@section('content')
<x-breadcrumbs :items="[
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Two-factor authentication', 'href' => route('admin.two-factor.show')],
    ['label' => 'Confirm'],
]" />

<x-page-header
    title="Confirm authenticator"
    subtitle="Add this secret to your authenticator app, then enter a code to finish setup."
/>

<x-panel class="max-w-md space-y-4">
    <div>
        <p class="text-xs font-semibold text-fs-primary mb-1">Secret key</p>
        <p class="font-mono text-sm bg-fs-surface p-3 rounded-sm break-all border border-black/5">{{ $secret }}</p>
    </div>
    <div>
        <p class="text-xs font-semibold text-fs-primary mb-1">Setup URL</p>
        <p class="text-xs text-fs-muted break-all">{{ $otpauthUrl }}</p>
    </div>
    <form method="POST" action="{{ route('admin.two-factor.confirm') }}" class="space-y-3 pt-2">
        @csrf
        <x-input label="Code" name="code" inputmode="numeric" autocomplete="one-time-code" required />
        <x-button type="submit" variant="primary">Confirm</x-button>
    </form>
</x-panel>
@endsection
