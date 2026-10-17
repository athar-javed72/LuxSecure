@extends('include.master')

@section('title', 'Two-factor authentication')

@section('content')
<x-container class="py-16 max-w-md">
    <h1 class="text-2xl font-bold text-fs-primary">Two-factor authentication</h1>
    <p class="mt-2 text-sm text-fs-secondary">Enter the code from your authenticator app.</p>
    <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-4 rounded-md bg-white border border-black/5 p-6">
        @csrf
        <x-input label="Authentication code" name="code" autocomplete="one-time-code" required />
        <x-button type="submit" variant="primary" class="w-full">Verify</x-button>
    </form>
</x-container>
@endsection
