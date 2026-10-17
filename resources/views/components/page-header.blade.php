@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        <h1 class="fs-display text-2xl md:text-3xl text-fs-primary">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-2 text-sm text-fs-secondary max-w-prose">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-2 shrink-0">{{ $actions }}</div>
    @endisset
</div>
