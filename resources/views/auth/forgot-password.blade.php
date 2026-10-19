@extends('include.master')

@section('header_mode', 'light')
@section('title', 'Forgot password')
@section('meta_description', 'Reset your '.($siteName ?? config('app.name')).' password.')

@section('content')
<section class="relative overflow-hidden bg-[var(--color-ivory)] py-10 md:py-14" aria-label="Forgot password">
    <div class="pointer-events-none absolute inset-0 opacity-[0.35]" aria-hidden="true" style="background:
        radial-gradient(ellipse 60% 50% at 10% 0%, color-mix(in srgb, var(--color-gold) 18%, transparent), transparent 70%),
        radial-gradient(ellipse 50% 40% at 100% 100%, color-mix(in srgb, var(--color-forest) 10%, transparent), transparent 65%);"></div>
    <x-container class="relative">
        <x-auth-card mode="forgot" />
    </x-container>
</section>
@endsection
