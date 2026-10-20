@extends('include.master')

@section('title', 'Verify Email')

@section('content')
<x-container class="py-12">
    <div class="max-w-md mx-auto rounded-md bg-white shadow-sm border border-slate-100 p-6 sm:p-8 text-center">
        <h1 class="text-xl font-bold text-fs-primary">Verify your email</h1>
        <p class="mt-2 text-sm text-fs-secondary">Please check your inbox for a verification link.</p>

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <x-button type="submit" variant="primary" class="w-full">Resend verification email</x-button>
        </form>
    </div>
</x-container>
@endsection