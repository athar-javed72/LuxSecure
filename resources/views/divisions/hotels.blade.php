@extends('layouts.landing')

@section('title', 'Maskanoor Hotels')
@section('meta_description', 'Boutique hotels and short-stay residences, presented with care.')

@php
    $hotels = config('hotels_page');
    $hotelsSearch = $hotelsSearch ?? [
        'active' => false,
        'stays' => collect(),
        'count' => 0,
        'summary' => null,
        'error' => null,
    ];
@endphp

@push('styles')
    <link rel="preload" as="image" href="{{ $hotels['hero']['background'] }}" fetchpriority="high">
    <link rel="canonical" href="{{ url('/hospitality') }}">
@endpush

@section('content')
    <x-landing.hotels.hero :data="$hotels" />
    <x-landing.hotels.booking-bar />
    <x-landing.hotels.results :search="$hotelsSearch" />
    <x-landing.hotels.trust-pills :pills="$hotels['trust_pills']" />
    <x-landing.hotels.destinations :destinations="$hotels['destinations']" />
    <x-landing.hotels.stays-grid :stays="$hotels['stays']" />
    <x-landing.hotels.why :why="$hotels['why']" />
    <x-landing.hotels.gallery :items="$hotels['gallery']" />
    <x-landing.hotels.how-it-works :steps="$hotels['steps']" />
    <x-landing.hotels.faq :faqs="$hotels['faqs']" />
    <x-landing.hotels.cta-band />
@endsection
