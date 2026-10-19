@props([
    'onDark' => false,
])

<p {{ $attributes->class([$onDark ? 'fs-eyebrow-on-dark' : 'fs-eyebrow']) }}>
    {{ $slot }}
</p>
