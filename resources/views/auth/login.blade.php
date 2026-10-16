@extends('include.master')

@section('title', 'Login')
@section('meta_description', 'Sign in to your LuxSecure account.')

@section('content')
<x-container class="py-12">
    <x-auth-card mode="login" />
</x-container>
@endsection

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
@endpush
