@props([
    'reverse' => false,
])

<div {{ $attributes->class([
    'grid lg:grid-cols-2 gap-10 lg:gap-16 items-start',
]) }}>
    <div class="{{ $reverse ? 'order-2 lg:order-2' : 'order-2 lg:order-1' }}">
        {{ $media ?? '' }}
    </div>
    <div class="{{ $reverse ? 'order-1 lg:order-1' : 'order-1 lg:order-2' }}">
        {{ $slot }}
    </div>
</div>
