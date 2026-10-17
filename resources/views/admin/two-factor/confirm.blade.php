@extends('admin.layout')

@section('title', 'Confirm 2FA')

@section('content')
<h1 class="text-xl font-bold mb-4">Confirm authenticator</h1>
<p class="text-sm text-fs-secondary mb-2">Add this secret to your authenticator app, then enter a code.</p>
<p class="font-mono text-sm bg-fs-surface p-3 rounded-sm mb-4 break-all">{{ $secret }}</p>
<p class="text-xs text-fs-muted mb-4 break-all">{{ $otpauthUrl }}</p>
<form method="POST" action="{{ route('admin.two-factor.confirm') }}" class="max-w-md space-y-3">
    @csrf
    <x-input label="Code" name="code" required />
    <x-button type="submit" variant="primary">Confirm</x-button>
</form>
@endsection
