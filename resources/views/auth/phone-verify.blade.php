@extends('include.master')

@section('title', 'Verify phone')

@section('content')
<x-container class="py-10 max-w-md">
    <h1 class="fs-display text-3xl text-fs-primary">Phone verification</h1>
    <p class="mt-2 text-sm text-fs-muted">We send a one-time code (hashed at rest, expires in 10 minutes).</p>

    @if(session('success'))
        <div class="mt-4 p-3 rounded-sm bg-fs-brand-light text-sm">{{ session('success') }}</div>
    @endif

    @if($user->isPhoneVerified())
        <p class="mt-6 text-sm">Verified: <strong>{{ $user->phone }}</strong></p>
    @endif

    <form method="POST" action="{{ route('phone.verify.send') }}" class="mt-6 space-y-3 rounded-md bg-white border border-black/5 p-6">
        @csrf
        <x-input label="Phone" name="phone" :value="old('phone', $user->phone)" required />
        <x-button type="submit" variant="ghost">Send OTP</x-button>
    </form>

    <form method="POST" action="{{ route('phone.verify.confirm') }}" class="mt-4 space-y-3 rounded-md bg-white border border-black/5 p-6">
        @csrf
        <x-input label="Phone" name="phone" :value="old('phone', $user->phone)" required />
        <x-input label="Code" name="code" required maxlength="6" />
        <x-button type="submit" variant="primary">Verify</x-button>
    </form>
</x-container>
@endsection
