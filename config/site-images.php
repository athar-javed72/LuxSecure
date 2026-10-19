<?php

/**
 * Central image paths for marketing surfaces.
 * Listing card photos come from each listing — never reuse mood shots as listing photos.
 * No image path may repeat across hero / divisions / about / break slots.
 */
return [
    'hero_slides' => [
        [
            'desktop' => '/images/home/hero-1.jpg',
            'mobile' => '/images/home/hero-1-mobile.jpg',
            'alt' => 'Warm hotel suite with linen bedding and soft evening light',
            'object_position' => '68% center',
            'origin' => '70% 40%',
        ],
        [
            'desktop' => '/images/home/hero-2.jpg',
            'mobile' => '/images/home/hero-2-mobile.jpg',
            'alt' => 'Contemporary residence exterior at dusk with calm reflecting water',
            'object_position' => '70% center',
            'origin' => '65% 50%',
        ],
        [
            'desktop' => '/images/home/hero-3.jpg',
            'mobile' => '/images/home/hero-3-mobile.jpg',
            'alt' => 'Refined living room with window light and quiet furnishings',
            'object_position' => '72% center',
            'origin' => '75% 45%',
        ],
    ],
    // Legacy single-hero keys (first slide) for preload / OG fallbacks
    'hero' => [
        'desktop' => '/images/home/hero-1.jpg',
        'mobile' => '/images/home/hero-1-mobile.jpg',
        'alt' => 'Warm hotel suite with linen bedding and soft evening light',
        'object_position' => '68% center',
    ],
    'about' => [
        'src' => '/images/home/about.jpg',
        'alt' => 'Stone archway with soft raking light and quiet shadow',
    ],
    'break' => [
        'desktop' => '/images/home/break.jpg',
        'mobile' => '/images/home/break-mobile.jpg',
        'alt' => 'Warm stone colonnade with raking light',
    ],
    'divisions' => [
        'hotels' => [
            'src' => '/images/divisions/hotels.jpg',
            'alt' => 'Boutique hotel lobby detail with warm timber and soft light',
        ],
        'properties' => [
            'src' => '/images/divisions/properties.jpg',
            'alt' => 'Residential courtyard facade in soft daylight',
        ],
        'living' => [
            'src' => '/images/divisions/living.jpg',
            'alt' => 'Serviced apartment living area with window light',
        ],
    ],
    'listing_placeholders' => [
        'apartment' => '/images/listings/apartment-1.jpg',
        'house' => '/images/listings/house-1.jpg',
        'plot' => '/images/listings/plot-1.jpg',
        'hotel' => '/images/listings/hotel-1.jpg',
    ],
];
