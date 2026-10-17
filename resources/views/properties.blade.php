@extends('include.master')

@section('title', 'Properties')
@section('meta_description', 'Browse and filter '.($siteName ?? config('app.name')).' property listings by location, type, and budget.')

@section('content')
<x-container class="py-8" x-data="filtersPanel">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-fs-primary">Properties</h1>
            <p class="mt-2 text-sm text-fs-secondary">{{ $properties->total() }} listings found</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center h-11 px-5 rounded-sm border border-slate-200 text-sm font-semibold text-fs-primary"
                @click="openFilters()"
            >
                Filters
            </button>
            <form method="GET" class="flex items-center gap-2">
                @foreach(request()->except('sort', 'page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label for="sort" class="sr-only">Sort</label>
                <select id="sort" name="sort" @change="submitSort($el)" class="h-11 px-4 rounded-sm border border-slate-200 text-sm text-fs-primary bg-white">
                    <option value="">Newest</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Desktop sidebar filters --}}
        <aside class="hidden lg:block lg:col-span-3">
            <div class="rounded-md bg-white shadow-sm border border-slate-100 p-5 sticky top-24">
                <h2 class="text-base font-semibold text-fs-primary mb-4">Filters</h2>
                @include('include.property-filters', ['idPrefix' => 'desktop'])
            </div>
        </aside>

        <div class="lg:col-span-9">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse($properties as $property)
                    <x-property-card :property="$property" />
                @empty
                    <div class="col-span-full rounded-md border border-slate-200 bg-slate-50 p-8 text-center">
                        <h2 class="text-base font-semibold text-fs-primary">No properties found</h2>
                        <p class="mt-2 text-sm text-fs-secondary">Try adjusting your filters.</p>
                        <div class="mt-6">
                            <x-button variant="primary" href="{{ route('properties') }}">Clear filters</x-button>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($properties->hasPages())
                <div class="mt-8">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Mobile filter drawer --}}
    <div x-cloak x-show="filtersOpen" class="lg:hidden fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Filters">
        <div class="absolute inset-0 bg-fs-primary/40" @click="closeFilters()"></div>
        <div
            class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-md bg-white shadow-lg p-5"
            x-show="filtersOpen"
            x-transition:enter="transition transform duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition transform duration-150"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
        >
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-fs-primary">Filters</h2>
                <button type="button" class="w-11 h-11 rounded-sm border border-slate-200" @click="closeFilters()" aria-label="Close filters">✕</button>
            </div>
            @include('include.property-filters', ['idPrefix' => 'mobile'])
        </div>
    </div>
</x-container>
@endsection
