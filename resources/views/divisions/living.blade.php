@extends('layouts.landing')

@section('title', 'Maskanoor Living')
@section('meta_description', 'Rentals and residences for everyday life: clear, calm and carefully presented.')

@php
    $living = config('living_page');
@endphp

@push('styles')
    <link rel="preload" as="image" href="{{ $living['hero']['background'] }}" fetchpriority="high">
    <link rel="canonical" href="{{ url('/living') }}">
@endpush

@section('content')
    <x-landing.living.hero :data="$living" />
    <x-landing.living.search-bar />
    <x-landing.living.trust-pills :pills="$living['trust_pills']" />
    <x-landing.living.rentals-grid
        :rentals="$living['rentals']"
        :chips="$living['city_chips']"
        :request="$living['request_card']"
    />
    <x-landing.living.clarity :clarity="$living['clarity']" :tenancy="$living['tenancy']" />
    <x-landing.living.path :steps="$living['steps']" :audiences="$living['audiences']" />
    <x-landing.living.faq :faqs="$living['faqs']" />
@endsection
