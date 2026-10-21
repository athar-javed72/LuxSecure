@extends('layouts.landing')

@section('title', 'Elevating Spaces. Enriching Stays.')
@section('meta_description', 'Hotels, residences and carefully selected properties across six countries, presented with care.')

@push('styles')
    <link rel="preload" as="image" href="/images/home/hero-2.jpg" fetchpriority="high">
@endpush

@section('content')
    <x-landing.hero :featured="$houses->firstWhere('purpose', 'short_stay') ?? $houses->first()" />
    <x-landing.search-bar />
    <x-landing.country-strip />
    <x-landing.divisions />
    <x-landing.featured-listings :listings="$houses" />
    <x-landing.destinations :destinations="$destinations" />
    <x-landing.how-it-works />
    <x-landing.cta-band />
@endsection
