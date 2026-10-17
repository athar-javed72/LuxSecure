@extends('include.master')

@section('title', 'Maintenance')

@section('content')
<x-container class="py-16 text-center">
    <h1 class="text-xl md:text-2xl font-bold text-fs-primary">We’ll be right back</h1>
    <p class="mt-2 text-sm text-fs-secondary">{{ $siteName ?? config('app.name') }} is undergoing scheduled maintenance.</p>
</x-container>
@endsection
