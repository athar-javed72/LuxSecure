@props([
    'as' => 'div',
])

<{{ $as }} {{ $attributes->merge(['class' => 'fs-container']) }}>
    {{ $slot }}
</{{ $as }}>
