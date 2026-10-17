@extends('include.master')

@section('title', $organization->name)

@section('content')
<x-container class="py-8">
    <nav class="mb-6 text-sm text-fs-secondary" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-fs-brand transition-colors duration-150">Home</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-fs-primary font-medium" aria-current="page">{{ $organization->name }}</li>
        </ol>
    </nav>

    <div class="mb-8">
        <h1 class="fs-display text-3xl md:text-4xl font-semibold text-fs-primary">{{ $organization->name }}</h1>
        @if($organization->country)
            <p class="mt-2 text-sm text-fs-secondary">{{ $organization->country->name }}</p>
        @endif
        @if($organization->description)
            <p class="mt-4 max-w-2xl text-fs-secondary">{{ $organization->description }}</p>
        @endif
    </div>

    <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
        <x-select
            label="City"
            name="city_id"
            :options="$cities->all()"
            :selected="request('city_id')"
            placeholder="All cities"
        />
        @if(request()->filled('country_id'))
            <input type="hidden" name="country_id" value="{{ request('country_id') }}">
        @endif
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($branches as $branch)
            <x-property-card :property="$branch" />
        @empty
            <p class="text-fs-muted col-span-full">No branches found for this organization.</p>
        @endforelse
    </div>

    @if($branches->hasPages())
        <div class="mt-8">{{ $branches->links() }}</div>
    @endif
</x-container>
@endsection
