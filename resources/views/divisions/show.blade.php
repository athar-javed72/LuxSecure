@extends('include.master')

@section('header_mode', 'forest')
@section('title', $divisionName)
@section('meta_description', ($heroLead ?: $introBody) ?: (($siteName ?? config('app.name')).' — '.$divisionName))

@push('styles')
    @php $first = $heroSlides->first(); @endphp
    @if(!empty($first['src']))
        <link rel="preload" as="image" href="{{ $first['src'] }}" fetchpriority="high">
    @endif
    <link rel="canonical" href="{{ url($division['url_path']) }}">
    <meta property="og:image" content="{{ $ogImage }}">
@endpush

@section('content')
@php
    $sections = $division['sections'] ?? [];
    $isDemoEnv = ! app()->isProduction();
@endphp

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $divisionName, 'item' => url($division['url_path'])],
    ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>

<x-division-hero
    :slides="$heroSlides->all()"
    :eyebrow="$division['eyebrow']"
    :title="$heroTitle"
    :lead="$heroLead"
    :primary-href="$browseHref"
    :primary-label="$division['browse_label']"
    :secondary-href="route('contact', ['type' => $division['cta_type']])"
    secondary-label="Start a Conversation"
    :breadcrumbs="[
        ['label' => 'Home', 'href' => route('home')],
        ['label' => $divisionName],
    ]"
/>

{{-- Search bridges hero → ivory; stats sit directly under with a short, even gap (no cream void) --}}
@if(!empty($sections['search']) || !empty($sections['stats']))
<section class="relative z-0 bg-[var(--color-ivory)]" aria-label="{{ $divisionName }} search">
    @if(!empty($sections['search']))
        <div class="relative z-30 w-full -mt-[72px]" data-hero-search>
            <x-container>
                <x-property-search
                    :suggestions="$locationSuggestions"
                    :types="$searchTypes"
                    :currency="$currency"
                    :purpose="$division['scope']['purpose'] ?? null"
                />
            </x-container>
        </div>
    @endif

    @if(!empty($sections['stats']))
        <x-stat-strip
            :stats="$stats"
            :flush-top="!empty($sections['search'])"
            class="relative z-0 {{ !empty($sections['search']) ? 'mt-6 md:mt-8' : '' }}"
        />
    @endif
</section>
@endif

@if(!empty($sections['intro']) && ($introTitle || $introBody))
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="division-intro-heading">
    <x-container>
        <x-split-section>
            <x-slot:media>
                <div class="aspect-[4/5] w-full rounded-[var(--radius-card)] overflow-hidden" data-reveal>
                    <x-media :media="$introMedia" class="w-full h-full" sizes="(min-width:1024px) 40vw, 100vw" />
                </div>
            </x-slot:media>
            <div data-reveal>
                <x-brand-logo layout="stacked" surface="light" :division="$divisionKey" class="[&_img]:h-[140px] [&_img]:min-w-[140px] mb-6" />
                <x-eyebrow>{{ $divisionName }}</x-eyebrow>
                <h2 id="division-intro-heading" class="mt-4">{{ $introTitle }}</h2>
                @if($introBody)
                    <p class="mt-4 fs-lead max-w-[48ch]">{{ $introBody }}</p>
                @endif
                <a href="{{ $browseHref }}" class="mt-6 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">
                    {{ $division['browse_label'] }} →
                </a>
            </div>
        </x-split-section>
    </x-container>
</section>
@endif

@if(!empty($sections['featured']) && $featured->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="division-featured-heading" @if($featured->count() > 3) data-carousel @endif>
    <x-container>
        <x-section-heading
            eyebrow="Featured"
            :title="$divisionKey === 'hotels' ? 'Featured stays' : 'Featured rentals'"
            lead="A selection from this division."
            heading-id="division-featured-heading"
            :action-href="$browseHref"
            action-label="View all"
        >
            @if($featured->count() > 3)
                <x-slot:controls>
                    <div class="flex items-center gap-2" data-carousel-controls>
                        <button type="button" class="fs-carousel__btn" data-carousel-prev aria-label="Previous" disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" class="fs-carousel__btn" data-carousel-next aria-label="Next">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </x-slot:controls>
            @endif
        </x-section-heading>
        @if($featured->count() > 3)
            <div class="mt-10 fs-carousel" data-reveal>
                <div class="fs-carousel__track" data-carousel-track tabindex="0">
                    @foreach($featured as $listing)
                        <div class="fs-carousel__slide" data-carousel-slide>
                            <x-property-card :property="$listing" :is-sample="$isDemoEnv" />
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 justify-center" data-reveal>
                @foreach($featured as $listing)
                    <x-property-card :property="$listing" :is-sample="$isDemoEnv" />
                @endforeach
            </div>
        @endif
    </x-container>
</section>
@endif

@if(!empty($sections['destinations']) && $destinations->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="division-destinations-heading">
    <x-container>
        <x-section-heading
            eyebrow="Destinations"
            title="Where to look first"
            lead="Cities with published listings in this division."
            heading-id="division-destinations-heading"
        />
        <div class="mt-10">
            <x-destination-grid :destinations="$destinations" />
        </div>
    </x-container>
</section>
@endif

@if(!empty($sections['amenities']) && $amenities->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="division-amenities-heading">
    <x-container>
        <x-section-heading
            eyebrow="Details that matter"
            title="Often found in these listings"
            heading-id="division-amenities-heading"
        />
        <div class="mt-10">
            <x-amenity-highlights :amenities="$amenities" />
        </div>
    </x-container>
</section>
@endif

@if(!empty($sections['video']) && $video)
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="division-video-heading">
    <x-container>
        <x-section-heading eyebrow="Experience" title="A closer look" heading-id="division-video-heading" />
        <div class="mt-10">
            <x-video-modal :media="$video" />
        </div>
    </x-container>
</section>
@endif

@if(!empty($sections['gallery']))
    @php
        $gallerySlot = ($division['media_prefix']).'.gallery';
        $galleryItems = app(\App\Services\MediaResolver::class)->collection($gallerySlot);
        $hasGallery = $galleryItems->isNotEmpty() || $featured->isNotEmpty();
    @endphp
    @if($hasGallery)
    <section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="division-gallery-heading">
        <x-container>
            <x-section-heading eyebrow="Gallery" title="Spaces in view" heading-id="division-gallery-heading" />
            <div class="mt-10">
                <x-media-collection :slot="$gallerySlot" :fallback-listings="$featured" />
            </div>
        </x-container>
    </section>
    @endif
@endif

@if(!empty($sections['steps']) && count($steps))
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="division-steps-heading">
    <x-container>
        <x-section-heading eyebrow="How it works" title="A clear path forward" heading-id="division-steps-heading" />
        <div class="mt-10">
            <x-steps :steps="$steps" />
        </div>

        @if($audience)
            <div class="mt-14 grid md:grid-cols-2 gap-6">
                <div class="p-6 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white" data-reveal>
                    <h3 class="text-lg font-semibold">{{ $audience['tenants']['title'] }}</h3>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)]">{{ $audience['tenants']['body'] }}</p>
                    <a href="{{ $browseHref }}" class="mt-4 inline-flex min-h-11 items-center text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">Browse rentals →</a>
                </div>
                <div class="p-6 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-white" data-reveal>
                    <h3 class="text-lg font-semibold">{{ $audience['landlords']['title'] }}</h3>
                    <p class="mt-2 text-sm text-[var(--color-muted-strong)]">{{ $audience['landlords']['body'] }}</p>
                    <a href="{{ route('contact', ['type' => 'property']) }}" class="mt-4 inline-flex min-h-11 items-center text-sm font-semibold text-[var(--color-gold-dark)] hover:underline">List your property →</a>
                </div>
            </div>
        @endif
    </x-container>
</section>
@endif

@if(!empty($sections['reviews']) && $reviews->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory-50" aria-labelledby="division-reviews-heading">
    <x-container>
        <x-section-heading eyebrow="Reviews" title="From people who stayed or lived here" heading-id="division-reviews-heading" />
        <div class="mt-10 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reviews as $review)
                <blockquote class="p-5 rounded-[var(--radius-card)] bg-white border border-[var(--color-border)]" data-reveal>
                    <p class="text-sm text-[var(--color-ink)]">“{{ $review->body }}”</p>
                    <footer class="mt-3 text-xs text-[var(--color-muted)] fs-numeral">{{ $review->rating }}/5 · {{ $review->user?->name ?? 'Guest' }}</footer>
                </blockquote>
            @endforeach
        </div>
    </x-container>
</section>
@endif

@if(!empty($sections['faq']) && $faqs->isNotEmpty())
<section class="relative z-0 fs-section fs-section-ivory" aria-labelledby="division-faq-heading">
    <x-container>
        <x-section-heading eyebrow="FAQ" title="Questions we are often asked" heading-id="division-faq-heading" />
        <div class="mt-10">
            <x-faq-section :faqs="$faqs" />
        </div>
    </x-container>
</section>
@endif

@if(!empty($sections['cta']))
    <x-cta-band
        :title="'Talk with us about '.$divisionName"
        lead="Share what you are looking for — we will follow up with care."
        :primary-href="route('contact', ['type' => $division['cta_type']])"
        primary-label="Start a Conversation"
        :secondary-href="$cross ? route($cross['route']) : null"
        :secondary-label="$cross ? ('Also explore '.$cross['nav_label']) : null"
    />
@endif
@endsection
