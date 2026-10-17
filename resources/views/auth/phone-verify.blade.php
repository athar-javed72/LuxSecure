@extends('include.master')

@section('title', 'Verify phone')

@section('content')
<x-container class="py-10 max-w-md">
    <x-page-header
        title="Phone verification"
        subtitle="We send a one-time code (hashed at rest, expires in 10 minutes)."
    />

    @if($user->isPhoneVerified())
        <x-panel class="mt-6 text-sm">
            <p class="text-fs-secondary">Verified number</p>
            <p class="mt-1 font-semibold text-fs-primary">{{ $user->phone }}</p>
            <x-status-badge status="verified" class="mt-2">Verified</x-status-badge>
        </x-panel>
    @endif

    <x-panel class="mt-6">
        <h2 class="text-sm font-semibold text-fs-primary">Send code</h2>
        <form method="POST" action="{{ route('phone.verify.send') }}" class="mt-3 space-y-3">
            @csrf
            <x-input label="Phone" name="phone" type="tel" autocomplete="tel" :value="old('phone', $user->phone)" required />
            <x-button type="submit" variant="ghost">Send OTP</x-button>
        </form>
    </x-panel>

    <x-panel class="mt-4">
        <h2 class="text-sm font-semibold text-fs-primary">Confirm code</h2>
        <form method="POST" action="{{ route('phone.verify.confirm') }}" class="mt-3 space-y-3">
            @csrf
            <x-input label="Phone" name="phone" type="tel" autocomplete="tel" :value="old('phone', $user->phone)" required />
            <x-input label="Code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required />
            <x-button type="submit" variant="primary">Verify</x-button>
        </form>
    </x-panel>
</x-container>
@endsection
