<?php

/**
 * Placeholder content for the Maskanoor Living landing page.
 * Swap images / prices / names here — components read from this file only.
 */
return [
    'hero' => [
        'background' => '/images/listings/house-1.jpg',
        'background_alt' => 'Calm residential exterior with warm interior light at dusk',
        'featured' => [
            'image' => '/images/listings/apartment-2.jpg',
            'image_alt' => 'Bright apartment living room with natural light',
            'tag' => 'For rent',
            'price' => 'PKR 180,000',
            'price_suffix' => '/month',
            'title' => 'DHA Apartment',
            'meta' => 'DHA Phase 5, Karachi · 3 beds · 2 baths',
            'search' => 'DHA',
        ],
    ],

    'trust_pills' => [
        'Monthly rent schedule',
        'Pay rent online',
        'Receipts and invoices',
        'KYC-verified landlords',
        'Security deposit tracked',
    ],

    'city_chips' => [
        ['label' => 'Karachi · 1 rental', 'search' => 'Karachi', 'active' => true],
        ['label' => 'More cities soon', 'search' => null, 'active' => false],
    ],

    'rentals' => [
        [
            'title' => 'DHA Apartment',
            'location' => 'DHA Phase 5, Karachi',
            'price' => 'PKR 180,000',
            'price_suffix' => '/month',
            'image' => '/images/listings/apartment-2.jpg',
            'image_alt' => 'Bright apartment living room with plants and natural light',
            'beds' => '3 beds',
            'baths' => '2 baths',
            'area' => '1,400 sq ft',
            'search' => 'DHA',
        ],
    ],

    'request_card' => [
        'title' => 'Tell us what you need',
        'body' => 'Share area, budget and move-in date, and we will match you.',
        'cta' => 'Request a rental',
    ],

    'clarity' => [
        'title' => 'Living with clarity',
        'lead' => 'Find a home that fits your rhythm, with transparent details and a thoughtful path from first look to move-in.',
        'features' => [
            ['title' => 'Clear pricing', 'body' => 'Rent and deposit shown up front.'],
            ['title' => 'Verified landlords', 'body' => 'KYC reviewed before listing.'],
            ['title' => 'Rent schedule', 'body' => 'Every due date in one place.'],
            ['title' => 'Online receipts', 'body' => 'Invoices for each payment.'],
        ],
        'cta' => 'See how it works',
        'cta_href' => '#path',
    ],

    'tenancy' => [
        'heading' => 'My tenancy',
        'property' => 'DHA Apartment',
        'next_label' => 'Next rent due',
        'next_amount' => 'PKR 180,000',
        'next_date' => '1 November 2026',
        'rows' => [
            ['label' => 'October rent', 'value' => 'Paid', 'tone' => 'ok'],
            ['label' => 'September rent', 'value' => 'Paid', 'tone' => 'ok'],
            ['label' => 'Security deposit', 'value' => 'Held', 'tone' => 'ok'],
            ['label' => 'Receipts', 'value' => 'Download', 'tone' => 'link', 'href' => '#'],
        ],
    ],

    'steps' => [
        [
            'title' => 'Find a home',
            'body' => 'Filter by city, type and monthly budget.',
        ],
        [
            'title' => 'Arrange a viewing',
            'body' => 'Send an enquiry and our team arranges the visit.',
        ],
        [
            'title' => 'Move in with support',
            'body' => 'Sign with clear terms and track rent from day one.',
        ],
    ],

    'audiences' => [
        [
            'title' => 'For tenants',
            'bullets' => [
                'Browse homes with rent and deposit shown clearly.',
                'Arrange a viewing through our team.',
                'Pay rent online and keep every receipt.',
            ],
            'cta' => 'Request a rental',
            'cta_type' => 'living',
        ],
        [
            'title' => 'For landlords',
            'bullets' => [
                'Present your property with care.',
                'Connect with KYC-verified tenants.',
                'Track deposits and rent in one place.',
            ],
            'cta' => 'List your property',
            'cta_type' => 'property',
        ],
    ],

    'faqs' => [
        [
            'q' => 'How do I arrange a viewing?',
            'a' => 'Open a listing or use Request a rental, and our team will arrange a visit at a time that works for you.',
        ],
        [
            'q' => 'How is the security deposit handled?',
            'a' => 'The deposit is collected and held with a clear status in My tenancy, so both sides can see it at any time.',
        ],
        [
            'q' => 'How do I pay rent?',
            'a' => 'Pay rent online against the schedule. Each payment creates a receipt and invoice you can download.',
        ],
        [
            'q' => 'What if I can\'t find a home I like?',
            'a' => 'Tell us your area, budget and move-in date with Request a rental, and we will match you to options.',
        ],
    ],
];
