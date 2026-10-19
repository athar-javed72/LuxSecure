@props([
    'title',
    'subtitle' => null,
    'lead' => null,
    'eyebrow' => null,
    'breadcrumbs' => [],
    'band' => false, // true = inner-page ivory band under the header
])

@php
    $leadText = $lead ?? $subtitle;
    $useBand = $band || $eyebrow || ! empty($breadcrumbs);
@endphp

@if($useBand)
    <header {{ $attributes->class(['w-full bg-[var(--color-ivory-50)] border-b border-[var(--color-border)]']) }}>
        <x-container class="py-[clamp(32px,5vw,56px)]">
            @if(! empty($breadcrumbs))
                <nav aria-label="Breadcrumb" class="mb-4">
                    <ol class="flex flex-wrap items-center gap-1.5 text-[13px] text-[var(--color-muted)]">
                        @foreach($breadcrumbs as $i => $crumb)
                            <li class="inline-flex items-center gap-1.5">
                                @if($i > 0)
                                    <span aria-hidden="true" class="text-[var(--color-border)]">/</span>
                                @endif
                                @if(! empty($crumb['href']) && ! $loop->last)
                                    <a href="{{ $crumb['href'] }}" class="hover:text-[var(--color-forest)] hover:underline underline-offset-2">{{ $crumb['label'] }}</a>
                                @else
                                    <span @if($loop->last) aria-current="page" @endif class="{{ $loop->last ? 'text-[var(--color-ink)] font-medium' : '' }}">{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div class="min-w-0 max-w-[56ch]">
                    @if($eyebrow)
                        <x-eyebrow>{{ $eyebrow }}</x-eyebrow>
                    @endif
                    <h1 class="mt-3 text-[clamp(36px,5vw,52px)] font-[family-name:var(--font-display)] font-semibold tracking-[-0.01em] text-[var(--color-ink)]" style="text-wrap: balance;">
                        {{ $title }}
                    </h1>
                    @if($leadText)
                        <p class="mt-3 text-[15px] leading-relaxed text-[var(--color-muted)] max-w-[56ch]" style="text-wrap: pretty;">{{ $leadText }}</p>
                    @endif
                </div>
                @isset($actions)
                    <div class="shrink-0 flex flex-wrap items-center gap-3 lg:justify-end">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </x-container>
    </header>
@else
    {{-- Legacy compact header used by admin / auth / existing pages --}}
    <div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
        <div class="min-w-0">
            <h1 class="fs-display text-2xl md:text-3xl text-fs-primary">{{ $title }}</h1>
            @if($leadText)
                <p class="mt-2 text-sm text-fs-secondary max-w-prose">{{ $leadText }}</p>
            @endif
        </div>
        @isset($actions)
            <div class="flex flex-wrap gap-2 shrink-0">{{ $actions }}</div>
        @endisset
    </div>
@endif
