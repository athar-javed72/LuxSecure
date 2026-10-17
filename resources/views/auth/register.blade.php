@extends('include.master')

@section('title', 'Register')
@section('meta_description', 'Create your '.($siteName ?? config('app.name')).' account.')

@section('content')
<x-container class="py-12">
    <x-auth-card mode="register" />
</x-container>
@endsection

@push('scripts')
<script @nonce src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
@endpush
