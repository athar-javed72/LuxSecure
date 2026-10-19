@extends('include.master')

@section('header_mode', 'forest')
@section('title', $title)
@section('meta_description', $title.' — '.\App\Support\BrandAssets::TAGLINE)

@section('content')
{{-- Negative margin pulls forest hero under the solid forest header (seamless, no grey band) --}}
<section class="relative overflow-hidden bg-[var(--color-forest)] text-[var(--color-ivory)] -mt-[var(--header-h)] pt-[var(--header-h)]">
    <x-container class="py-16 md:py-24">
        <div class="flex flex-col items-start gap-6 max-w-xl">
            <x-brand-logo layout="stacked" surface="dark" :division="$division" class="[&_img]:h-20 [&_img]:min-w-[140px]" />
            <p class="fs-display text-xl md:text-2xl italic font-normal text-[var(--color-gold)]">
                {{ \App\Support\BrandAssets::TAGLINE }}
            </p>
            <p class="text-sm md:text-base text-[var(--color-sage)] leading-relaxed">
                {{ $intro }}
            </p>
            @if(!empty($ctaHref))
                <x-button variant="secondary" :href="$ctaHref" surface="dark">{{ $ctaLabel }}</x-button>
            @endif
        </div>
    </x-container>
</section>

<section class="bg-[var(--color-ivory)]">
    <x-container class="py-12 md:py-16">
        <x-section-heading :title="$sectionTitle" :subtitle="$sectionSubtitle" />
        <div class="mt-8 prose prose-neutral max-w-prose">
            <p class="text-[var(--color-muted)] text-sm leading-relaxed">{{ $body }}</p>
        </div>
    </x-container>
</section>
@endsection
