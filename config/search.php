<?php

/**
 * Hero / browse search UI config. Query param names match ListingController.
 */
return [
    'purposes' => [
        'sale' => ['label' => 'Buy', 'tab' => 'buy'],
        'rent' => ['label' => 'Rent', 'tab' => 'rent'],
        'short_stay' => ['label' => 'Stay', 'tab' => 'stay'],
    ],
    'budget_presets' => [
        ['label' => 'Any', 'min' => null, 'max' => null, 'price' => null],
        ['label' => 'Under 1 Cr', 'min' => null, 'max' => 10_000_000, 'price' => '0-10000000'],
        ['label' => '1–3 Cr', 'min' => 10_000_000, 'max' => 30_000_000, 'price' => '10000000-30000000'],
        ['label' => '3–5 Cr', 'min' => 30_000_000, 'max' => 50_000_000, 'price' => '30000000-50000000'],
        ['label' => '5 Cr+', 'min' => 50_000_000, 'max' => null, 'price' => '50000000-1000000000'],
    ],
    'type_icons' => [
        'any' => 'M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4',
        'apartment' => 'M4 21V3h16v18M8 7h2M8 11h2M8 15h2M14 7h2M14 11h2M14 15h2',
        'hotel' => 'M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6',
        'house' => 'M3 10l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z',
        'plot' => 'M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4',
    ],
];
