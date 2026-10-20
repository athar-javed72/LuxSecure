@props([
    'slot' => '',
    'fallbackListings' => null,
])

@php
    $items = app(\App\Services\MediaResolver::class)->collection($slot);
    if ($items->isEmpty() && $fallbackListings) {
        $seen = [];
        foreach ($fallbackListings as $listing) {
            $src = $listing->featured_image
                ? \App\Support\StorageDisk::url($listing->featured_image)
                : ($listing->images->first() ? \App\Support\StorageDisk::url($listing->images->first()->path) : null);
            if (! $src || isset($seen[$src])) {
                continue;
            }
            $seen[$src] = true;
            $items->push([
                'kind' => 'derived',
                'type' => 'image',
                'src' => $src,
                'alt' => $listing->title,
                'decorative' => false,
                'object_position' => '50% 50%',
                'variants' => [],
                'width' => 1600,
                'height' => 1200,
            ]);
            if ($items->count() >= 9) {
                break;
            }
        }
    }
@endphp

@if($items->isNotEmpty())
    <x-gallery-mosaic :items="$items->all()" {{ $attributes }} />
@endif
