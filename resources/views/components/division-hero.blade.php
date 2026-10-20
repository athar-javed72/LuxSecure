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
    $slides = collect($slides)->values()->filter(fn ($s) => is_array($s))->values();
    if ($slides->isEmpty()) {
        $slides = collect([app(\App\Services\MediaResolver::class)->brandedFallback()]);
    }
@endphp

<section
    {{ $attributes->class([
        'relative overflow-hidden w-full flex flex-col justify-end',
        'bg-[var(--color-forest)]',
        'min-h-[min(48svh,440px)] md:min-h-[min(52svh,500px)]',
        '-mt-[var(--header-h)] pt-[var(--header-h)]',
    ]) }}
    @if($slides->count() > 1) data-hero-slideshow @endif
    aria-label="{{ $eyebrow }}"
>
    <div class="fs-hero-media absolute inset-0 z-0" aria-hidden="true">
        @foreach($slides as $i => $slide)
            <div class="fs-hero-slide {{ $i === 0 ? 'is-active' : '' }} absolute inset-0">
                <x-media :media="$slide" class="absolute inset-0 w-full h-full" :eager="$i === 0" sizes="100vw" />
            </div>
        @endforeach
        {{-- Left wash keeps ivory type readable; keep photo visible on the right --}}
        <div
            class="absolute inset-0 z-[2] pointer-events-none"
            style="background: linear-gradient(90deg, rgba(13,29,25,.88) 0%, rgba(13,29,25,.62) 38%, rgba(13,29,25,.28) 68%, rgba(13,29,25,.12) 100%);"
        ></div>
        <div
            class="absolute inset-x-0 bottom-0 z-[2] h-28 pointer-events-none"
            style="background: linear-gradient(to top, rgba(13,29,25,.5), transparent);"
        ></div>
    </div>

    <div class="relative z-10 w-full pb-20 md:pb-24 pt-6 md:pt-8">
        <x-container>
            @if(count($breadcrumbs))
                <nav aria-label="Breadcrumb" class="text-xs text-[var(--color-sage)] mb-4">
                    <ol class="flex flex-wrap gap-2">
                        @foreach($breadcrumbs as $crumb)
                            <li class="flex items-center gap-2">
                                @if(!empty($crumb['href']))
                                    <a href="{{ $crumb['href'] }}" class="hover:text-[var(--color-ivory)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-gold)] rounded-sm">{{ $crumb['label'] }}</a>
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

            @if(filled($title))
                <h1 class="mt-4 text-[var(--color-ivory)] max-w-[18ch] drop-shadow-[0_1px_12px_rgba(13,29,25,.45)]">{{ $title }}</h1>
            @endif

            @if(filled($lead))
                <p class="mt-4 text-base md:text-[17px] text-[var(--color-ivory)]/90 max-w-[52ch]" style="text-wrap: pretty;">{{ $lead }}</p>
            @endif

            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <x-button variant="primary" :href="$primaryHref" surface="dark" class="fs-btn-motion">{{ $primaryLabel }}</x-button>
                <x-button variant="secondary" :href="$secondaryHref" surface="dark" class="fs-btn-motion">{{ $secondaryLabel }}</x-button>
            </div>
        </x-container>
    </div>
</section>
