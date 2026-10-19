@extends('include.master')

@section('title', $title)
@section('meta_description', $title.' — '.\App\Support\BrandAssets::TAGLINE)

@section('content')
<section class="relative overflow-hidden bg-[#142A25] text-white">
    <x-container class="py-16 md:py-24">
        <div class="flex flex-col items-start gap-6 max-w-xl">
            <x-brand-logo layout="stacked" surface="dark" :division="$division" class="[&_img]:h-20 [&_img]:min-w-[140px]" />
            <p class="fs-display text-xl md:text-2xl italic text-[#D6B779]">
                {{ \App\Support\BrandAssets::TAGLINE }}
            </p>
            <p class="text-sm md:text-base text-slate-300 leading-relaxed">
                {{ $intro }}
            </p>
            @if(!empty($ctaHref))
                <x-button variant="secondary" :href="$ctaHref">{{ $ctaLabel }}</x-button>
            @endif
        </div>
    </x-container>
</section>

<section class="bg-[#F5F0E6]">
    <x-container class="py-12 md:py-16">
        <x-section-heading :title="$sectionTitle" :subtitle="$sectionSubtitle" />
        <div class="mt-8 prose prose-neutral max-w-prose">
            <p class="text-fs-secondary text-sm leading-relaxed">{{ $body }}</p>
        </div>
    </x-container>
</section>
@endsection
