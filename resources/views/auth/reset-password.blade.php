@extends('include.master')

@section('title', 'Reset Password')

@section('content')
<x-container class="py-12">
    <div class="flex w-full max-w-4xl mx-auto rounded-md overflow-hidden shadow-lg border border-slate-100 bg-white">
        <div class="hidden md:flex w-1/2 flex-col items-center justify-center bg-gradient-to-br from-fs-primary via-[#152238] to-[#1e3a5f] p-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-md bg-white/15 text-white mb-6" aria-hidden="true">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-lg font-bold text-white">New password</h2>
            <p class="mt-2 text-sm text-slate-300 max-w-prose">Choose a strong password for your LuxSecure account.</p>
        </div>

        <div class="w-full md:w-1/2 p-6 sm:p-8 flex flex-col justify-center">
            <h1 class="text-xl font-bold text-fs-primary">Reset password</h1>

            @if($errors->any())
                <div class="mt-4 p-4 rounded-sm bg-red-50 text-fs-danger text-sm">
                    <ul class="space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-input label="Email" name="email" type="email" :value="old('email', request('email'))" required />
                <x-input label="Password" name="password" type="password" required />
                <x-input label="Confirm password" name="password_confirmation" type="password" required />
                <x-button type="submit" variant="primary" class="w-full">Update password</x-button>
            </form>
        </div>
    </div>
</x-container>
@endsection
