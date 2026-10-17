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
            'label' => 'My bookings',
            'route' => 'bookings.mine',
            'active' => ['bookings.*'],
        ],
        [
            'label' => 'My tenancy',
            'route' => 'tenancies.mine',
            'active' => ['tenancies.*'],
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
            'label' => 'Tenancies',
            'route' => 'admin.tenancies.index',
            'active' => ['admin.tenancies.*'],
            'permission' => ['tenancies.view.any', 'tenancies.view.own'],
        ],
        [
            'label' => 'Room types',
            'route' => 'admin.room-types.index',
            'active' => ['admin.room-types.*'],
            'permission' => ['listings.view.any', 'listings.view.own'],
            'roles' => ['super_admin', 'admin', 'hotel_manager'],
        ],
        [
            'label' => 'Bookings',
            'route' => 'admin.bookings.index',
            'active' => ['admin.bookings.*'],
            'permission' => ['bookings.view.any', 'bookings.view.own'],
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
