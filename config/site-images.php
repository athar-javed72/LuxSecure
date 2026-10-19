<?php

/**
 * Central image paths for marketing surfaces (home, divisions, mood shots).
 * Owner can swap files under public/images/ without editing Blade templates.
 * Listing card photos come from each listing record — never reuse mood shots as listing photos.
 */
return [
    'hero' => [
        'desktop' => '/images/home/hero.jpg',
        'mobile' => '/images/home/hero-mobile.jpg',
        'alt' => 'Warm hotel suite with linen bedding and soft evening light',
        'object_position' => 'center',
    ],
    'about' => [
        'src' => '/images/home/about.jpg',
        'alt' => 'Soft daylight across timber and plaster interior architecture',
    ],
    'break' => [
        'desktop' => '/images/home/break.jpg',
        'mobile' => '/images/home/break-mobile.jpg',
        'alt' => 'Stone colonnade with raking light and quiet shadows',
    ],
    'divisions' => [
        'hotels' => [
            'src' => '/images/divisions/hotels.jpg',
            'alt' => 'Boutique hotel suite with warm linen and stone textures',
        ],
        'properties' => [
            'src' => '/images/divisions/properties.jpg',
            'alt' => 'Contemporary residential facade in soft daylight',
        ],
        'living' => [
            'src' => '/images/divisions/living.jpg',
            'alt' => 'Calm living area with window light and tidy furnishings',
        ],
    ],
    'listing_placeholders' => [
        'apartment' => '/images/listings/apartment-1.jpg',
        'house' => '/images/listings/house-1.jpg',
        'plot' => '/images/listings/plot-1.jpg',
        'hotel' => '/images/listings/hotel-1.jpg',
    ],
];
