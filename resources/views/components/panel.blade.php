@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-md bg-white border border-black/5 shadow-sm'.($padding ? ' p-4 sm:p-6' : '')]) }}>
    {{ $slot }}
</div>
