@props([
    'title',
    'subtitle' => null,
    'align' => 'left',
    'headingId' => null,
])

@php
    $alignClass = $align === 'center' ? 'text-center mx-auto' : 'text-left';
@endphp

<div {{ $attributes->merge(['class' => 'max-w-prose ' . $alignClass]) }}>
    <h2 @if($headingId) id="{{ $headingId }}" @endif class="text-xl md:text-2xl font-bold text-fs-primary">
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="mt-2 text-sm text-fs-secondary max-w-prose">{{ $subtitle }}</p>
    @endif
</div>
