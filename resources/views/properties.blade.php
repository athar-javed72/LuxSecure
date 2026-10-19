@extends('include.master')

@section('header_mode', 'light')
@section('title', 'Properties')
@section('meta_description', 'Browse and filter '.($siteName ?? config('app.name')).' property listings by location, type, and budget.')

@section('content')
@php
    $total = $properties->total();
    $from = $properties->firstItem() ?? 0;
    $to = $properties->lastItem() ?? 0;
@endphp

<x-page-header
    eyebrow="Maskanoor Properties"
    title="Properties"
    lead="Homes, apartments, plots and stays, curated with care."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'Properties'],
    ]"
/>

<x-properties-filters
    :listing-types="$listingTypes"
    :cities="$cities"
    :total="$total"
/>

<section class="pb-[var(--section-y)]" aria-labelledby="results-heading">
    <x-container class="pt-6">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
            <div>
                <h2 id="results-heading" class="sr-only">Search results</h2>
                <p class="text-[14px] sm:text-[15px] text-[var(--color-muted)] fs-numeral" aria-live="polite">
                    @if($total === 0)
                        0 listings
                    @else
                        {{ number_format($total) }} {{ \Illuminate\Support\Str::plural('listing', $total) }}
                        @if($properties->hasPages())
                            <span class="text-[var(--color-muted)]"> · Showing {{ $from }}–{{ $to }} of {{ number_format($total) }}</span>
                        @endif
                    @endif
                </p>
            </div>
        </div>

        @if($properties->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-reveal data-reveal-stagger>
                @foreach($properties as $property)
                    <x-property-card :property="$property" />
                @endforeach
            </div>

            @if($properties->hasPages())
                <div class="mt-10 fs-pagination flex flex-col items-center gap-3">
                    <p class="text-sm text-[var(--color-muted)] fs-numeral">Showing {{ $from }}–{{ $to }} of {{ number_format($total) }}</p>
                    {{ $properties->onEachSide(1)->links() }}
                </div>
            @endif
        @else
            <div class="relative rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white px-6 py-16 text-center" data-reveal>
                <img
                    src="{{ \App\Support\BrandAssets::logo('monogram-nostar', 'light') }}"
                    alt=""
                    aria-hidden="true"
                    class="mx-auto w-24 h-24 object-contain opacity-10 mb-6"
                >
                <h2 class="text-[clamp(24px,3vw,32px)] font-[family-name:var(--font-display)] font-semibold text-[var(--color-ink)]">No listings match these filters</h2>
                <p class="mt-3 text-[15px] text-[var(--color-muted)] max-w-[42ch] mx-auto" style="text-wrap: pretty;">
                    Try widening your budget or removing a filter.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <x-button variant="primary" href="{{ route('properties') }}" surface="light">Clear all filters</x-button>
                    <x-button variant="secondary" href="{{ route('contact', ['type' => 'property']) }}" surface="light">Tell us what you are looking for</x-button>
                </div>
            </div>
        @endif
    </x-container>
</section>

<x-cta-band
    title="Can't find the right fit?"
    lead="Tell us what you are looking for and our team will help."
    primary-label="Start a Conversation"
    :primary-href="route('contact', ['type' => 'property'])"
    secondary-label="Browse hotels"
    :secondary-href="route('divisions.hospitality')"
    class="pb-24"
/>
@endsection
