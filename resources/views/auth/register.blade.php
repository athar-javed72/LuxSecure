@extends('include.master')

@section('title', 'Register')
@section('meta_description', 'Create your LuxSecure account.')

@section('content')
<x-container class="py-12">
    <x-auth-card mode="register" />
</x-container>
@endsection

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
@endpush
