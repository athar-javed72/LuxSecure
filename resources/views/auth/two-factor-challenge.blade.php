@extends('include.master')

@section('title', 'Two-factor authentication')

@section('content')
<x-container class="py-16 max-w-md">
    <x-page-header
        title="Two-factor authentication"
        subtitle="Enter the code from your authenticator app."
    />

    <x-panel>
        <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
            @csrf
            <x-input label="Authentication code" name="code" inputmode="numeric" autocomplete="one-time-code" required />
            <x-button type="submit" variant="primary" class="w-full">Verify</x-button>
        </form>
    </x-panel>
</x-container>
@endsection
