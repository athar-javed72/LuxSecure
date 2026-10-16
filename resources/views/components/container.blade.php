@props([
    'as' => 'div',
])

<{{ $as }} {{ $attributes->merge(['class' => 'w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 2xl:px-12']) }}>
    {{ $slot }}
</{{ $as }}>
