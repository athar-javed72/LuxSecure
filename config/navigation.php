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
        ],
        [
            'label' => 'Manage Properties',
            'route' => 'admin.properties.index',
            'active' => ['admin.properties.*'],
        ],
        [
            'label' => 'Inquiries',
            'route' => 'admin.inquiries.index',
            'active' => ['admin.inquiries.*'],
        ],
        [
            'label' => 'Users',
            'route' => 'admin.users.index',
            'active' => ['admin.users.*'],
        ],
    ],
];
