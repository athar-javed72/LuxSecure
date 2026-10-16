@extends('include.master')

@section('title', 'Server Error')

@section('content')
<x-container class="py-16 text-center">
    <p class="text-2xl font-bold text-fs-muted" aria-hidden="true">500</p>
    <h1 class="mt-2 text-xl md:text-2xl font-bold text-fs-primary">Something went wrong</h1>
    <p class="mt-2 text-sm text-fs-secondary">Please try again in a moment.</p>
    <div class="mt-8">
        <x-button variant="primary" href="{{ route('home') }}">Back to home</x-button>
    </div>
</x-container>
@endsection
