@extends('include.master')

@section('title', 'Forgot Password')
@section('meta_description', 'Reset your '.($siteName ?? config('app.name')).' account password.')

@section('content')
<x-container class="py-12">
    <x-auth-card mode="forgot" />
</x-container>
@endsection

@push('scripts')
<script @nonce src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
@endpush
