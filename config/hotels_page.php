<?php

/**
 * Placeholder content for the Maskanoor Hotels landing page.
 * Swap images / prices / names here — components read from this file only.
 */
return [
    'hero' => [
        'background' => '/images/listings/hotel-1.jpg',
        'background_alt' => 'Resort pool deck at golden hour beside a timber lodge',
        'featured' => [
            'image' => '/images/home/hero-1.jpg',
            'image_alt' => 'Boutique hotel bedroom with soft lighting and brass accents',
            'tag' => 'Guest favourite',
            'price' => 'PKR 25,000',
            'price_suffix' => '/night',
            'title' => 'Blue Area Boutique Hotel',
            'meta' => 'Blue Area, Islamabad · 2 room types',
            'search' => 'Blue Area',
            'href' => null,
        ],
    ],

    'trust_pills' => [
        '4 stays · 3 cities · 3 countries',
        'KYC-verified hosts',
        'Pay by bank transfer',
        'Free cancellation up to 7 days',
        'Instant 30-minute hold',
    ],

    'destinations' => [
        [
            'name' => 'Islamabad',
            'line' => '2 stays · from PKR 15,000',
            'image' => '/images/home/about.jpg',
            'image_alt' => 'Glass towers at Constitution Square, Islamabad',
            'search' => 'Islamabad',
        ],
        [
            'name' => 'Dubai',
            'line' => '1 stay · from AED 15,000',
            'image' => '/images/home/break.jpg',
            'image_alt' => 'Contemporary interior corridor with warm evening light',
            'search' => 'Dubai',
        ],
        [
            'name' => 'Kuala Lumpur',
            'line' => '1 stay · from MYR 15,000',
            'image' => '/images/listings/house-1.jpg',
            'image_alt' => 'Contemporary residence facade with greenery at dusk',
            'search' => 'Kuala Lumpur',
        ],
        [
            'name' => 'More cities',
            'line' => 'Browse all destinations',
            'image' => '/images/home/hero-2.jpg',
            'image_alt' => 'Modern villa with pool under a clear sky',
            'search' => null,
            'browse_all' => true,
        ],
    ],

    'stays' => [
        [
            'title' => 'Blue Area Boutique Hotel',
            'location' => 'Blue Area, Islamabad',
            'price' => 'PKR 25,000',
            'price_suffix' => '/night',
            'image' => '/images/divisions/hotels.jpg',
            'image_alt' => 'Luxury suite seating area with velvet sofa',
            'amenities' => ['2 room types', 'Free cancellation'],
            'search' => 'Blue Area',
        ],
        [
            'title' => 'Maskanoor Stays Islamabad',
            'location' => 'Islamabad',
            'price' => 'PKR 15,000',
            'price_suffix' => '/night',
            'image' => '/images/home/hero-3.jpg',
            'image_alt' => 'Sunlit living room with city view',
            'amenities' => ['Free cancellation'],
            'search' => 'Islamabad',
        ],
        [
            'title' => 'Maskanoor Stays Dubai',
            'location' => 'Dubai',
            'price' => 'AED 15,000',
            'price_suffix' => '/night',
            'image' => '/images/listings/apartment-1.jpg',
            'image_alt' => 'Bright short-stay apartment with dining and lounge',
            'amenities' => ['Free cancellation'],
            'search' => 'Dubai',
        ],
        [
            'title' => 'Maskanoor Stays Kuala Lumpur',
            'location' => 'Kuala Lumpur',
            'price' => 'MYR 15,000',
            'price_suffix' => '/night',
            'image' => '/images/listings/apartment-2.jpg',
            'image_alt' => 'Airy apartment living room with plants and natural light',
            'amenities' => ['Free cancellation'],
            'search' => 'Kuala Lumpur',
        ],
    ],

    'why' => [
        'image' => '/images/divisions/living.jpg',
        'image_alt' => 'Calm living corner with mustard armchair and soft daylight',
        'features' => [
            ['title' => 'Verified hosts', 'body' => 'Every host passes KYC review.'],
            ['title' => 'Clear refunds', 'body' => 'Policy shown before you book.'],
            ['title' => 'Simple payment', 'body' => 'Bank transfer with proof upload.'],
            ['title' => 'Real support', 'body' => 'Reach us any hour.'],
        ],
        'refunds' => [
            ['title' => 'Full refund', 'body' => '7 or more days before'],
            ['title' => '50% refund', 'body' => '3 to 6 days before'],
            ['title' => 'No refund', 'body' => 'Under 3 days before'],
        ],
    ],

    'gallery' => [
        [
            'image' => '/images/home/break-mobile.jpg',
            'alt' => 'Lobby corridor with pendant lights',
            'large' => true,
        ],
        [
            'image' => '/images/divisions/hotels.jpg',
            'alt' => 'Suite seating area with velvet sofa',
        ],
        [
            'image' => '/images/listings/apartment-1.jpg',
            'alt' => 'Dining area with kitchenette in a short-stay residence',
        ],
        [
            'image' => '/images/listings/hotel-1.jpg',
            'alt' => 'Pool terrace with lounge chairs at dusk',
        ],
        [
            'image' => '/images/home/hero-1.jpg',
            'alt' => 'Boutique hotel bedroom ready for guests',
        ],
    ],

    'steps' => [
        [
            'title' => 'Choose your dates',
            'body' => 'See live availability and the full price before you commit.',
        ],
        [
            'title' => 'Reserve and pay',
            'body' => 'Your room is held for 30 minutes while you pay by bank transfer.',
        ],
        [
            'title' => 'Check in',
            'body' => 'Manage or cancel from My bookings, with the refund shown upfront.',
        ],
    ],

    'faqs' => [
        [
            'q' => 'How do I book a stay?',
            'a' => 'Pick dates, review the quote, reserve, then upload your bank transfer proof within the hold time.',
        ],
        [
            'q' => 'What is the cancellation policy?',
            'a' => 'Full refund 7 or more days before check-in, 50% from 3 to 6 days, none under 3 days. Each branch can set its own policy.',
        ],
        [
            'q' => 'How do I pay?',
            'a' => 'Bank transfer is available now. Other methods appear as they are enabled for your country.',
        ],
        [
            'q' => 'Can I enquire about a group or long stay?',
            'a' => 'Yes. Use Enquire for groups and our team will respond.',
        ],
    ],
];
