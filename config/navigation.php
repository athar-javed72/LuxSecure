<?php

return [
    'public' => [
        [
            'label' => 'Home',
            'route' => 'home',
            'active' => ['home'],
        ],
        [
            'label' => 'Properties',
            'route' => 'properties',
            'active' => ['properties', 'properties.*'],
        ],
        [
            'label' => 'Why LuxSecure',
            'route' => 'home',
            'anchor' => '#about',
            'active' => [],
        ],
        [
            'label' => 'Contact',
            'route' => 'contact',
            'active' => ['contact'],
        ],
    ],

    'user' => [
        [
            'label' => 'Home',
            'route' => 'home',
            'active' => ['home'],
        ],
        [
            'label' => 'Properties',
            'route' => 'properties',
            'active' => ['properties', 'properties.*'],
        ],
        [
            'label' => 'Favorites',
            'route' => 'profile',
            'anchor' => '#favorites',
            'active' => ['profile'],
        ],
        [
            'label' => 'Contact',
            'route' => 'contact',
            'active' => ['contact'],
        ],
    ],

    'admin' => [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'active' => ['admin.dashboard'],
            'permission' => 'panel.access',
        ],
        [
            'label' => 'Listings',
            'route' => 'admin.listings.index',
            'active' => ['admin.listings.*'],
            'permission' => ['listings.view.any', 'listings.view.own'],
        ],
        [
            'label' => 'Listing Types',
            'route' => 'admin.listing-types.index',
            'active' => ['admin.listing-types.*'],
            'permission' => 'listing_types.view',
        ],
        [
            'label' => 'Inquiries',
            'route' => 'admin.inquiries.index',
            'active' => ['admin.inquiries.*'],
            'permission' => ['inquiries.view.any', 'inquiries.view.own'],
        ],
        [
            'label' => 'Users',
            'route' => 'admin.users.index',
            'active' => ['admin.users.*'],
            'permission' => 'users.view',
        ],
        [
            'label' => 'Roles',
            'route' => 'admin.roles.index',
            'active' => ['admin.roles.*'],
            'permission' => 'roles.view',
        ],
    ],
];
