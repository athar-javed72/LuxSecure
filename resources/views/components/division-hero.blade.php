@props([
    'slides' => [],
    'eyebrow' => '',
    'title' => '',
    'lead' => '',
    'primaryHref' => '#',
    'primaryLabel' => 'Browse',
    'secondaryHref' => '#',
    'secondaryLabel' => 'Start a Conversation',
    'breadcrumbs' => [],
])

@php
    $slides = collect($slides)->values();
    if ($slides->isEmpty()) {
        $slides = collect([app(\App\Services\MediaResolver::class)->brandedFallback()]);
    }
@endphp

<section
    {{ $attributes->class(['relative overflow-hidden w-full min-h-[min(80svh,680px)] flex flex-col justify-end -mt-[var(--header-h)] pt-[var(--header-h)]']) }}
    @if($slides->count() > 1) data-hero-slideshow @endif
    aria-label="{{ $eyebrow }}"
>
    <div class="fs-hero-media absolute inset-0" aria-hidden="true">
        @foreach($slides as $i => $slide)
            <div class="fs-hero-slide {{ $i === 0 ? 'is-active' : '' }} absolute inset-0">
                <x-media :media="$slide" class="absolute inset-0 w-full h-full" :eager="$i === 0" sizes="100vw" />
            </div>
        @endforeach
        <div class="absolute inset-0 z-[2]" style="background: linear-gradient(90deg, rgba(13,29,25,.85), rgba(13,29,25,.5) 55%, rgba(13,29,25,.15));"></div>
    </div>

    <div class="relative z-10 w-full pb-28 md:pb-32">
        <x-container class="pt-10">
            @if(count($breadcrumbs))
                <nav aria-label="Breadcrumb" class="text-xs text-[var(--color-sage)] mb-4">
                    <ol class="flex flex-wrap gap-2">
                        @foreach($breadcrumbs as $crumb)
                            <li class="flex items-center gap-2">
                                @if(!empty($crumb['href']))
                                    <a href="{{ $crumb['href'] }}" class="hover:text-[var(--color-ivory)]">{{ $crumb['label'] }}</a>
                                @else
                                    <span class="text-[var(--color-ivory)]">{{ $crumb['label'] }}</span>
                                @endif
                                @if(! $loop->last)<span aria-hidden="true">/</span>@endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif
            <x-eyebrow on-dark>{{ $eyebrow }}</x-eyebrow>
            <h1 class="mt-4 text-[var(--color-ivory)] max-w-[18ch]">{{ $title }}</h1>
            @if($lead)
                <p class="mt-4 text-base md:text-[17px] text-[var(--color-sage)] max-w-[52ch]">{{ $lead }}</p>
            @endif
            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <x-button variant="primary" :href="$primaryHref" surface="dark" class="fs-btn-motion">{{ $primaryLabel }}</x-button>
                <x-button variant="secondary" :href="$secondaryHref" surface="dark" class="fs-btn-motion">{{ $secondaryLabel }}</x-button>
            </div>
        </x-container>
    </div>
</section>
