@extends('include.master')

@section('title', 'Page Not Found')

@section('content')
<x-container class="py-16 text-center">
    <p class="text-2xl font-bold text-fs-muted" aria-hidden="true">404</p>
    <h1 class="mt-2 text-xl md:text-2xl font-bold text-fs-primary">Page not found</h1>
    <p class="mt-2 text-sm text-fs-secondary">The page you’re looking for doesn’t exist or has been moved.</p>
    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
        <x-button variant="primary" href="{{ route('home') }}">Back to home</x-button>
        <x-button variant="ghost" href="{{ route('properties') }}">Browse properties</x-button>
    </div>
</x-container>
@endsection
