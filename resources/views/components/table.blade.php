@props([
    'caption' => null,
])

<div {{ $attributes->merge(['class' => 'fs-table-wrap rounded-md bg-white border border-black/5 shadow-sm']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            @if($caption)
                <caption class="sr-only">{{ $caption }}</caption>
            @endif
            {{ $slot }}
        </table>
    </div>
</div>
