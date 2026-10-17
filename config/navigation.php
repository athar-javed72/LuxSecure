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
            'label' => 'Why us',
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
            'label' => 'Leads',
            'route' => 'admin.leads.index',
            'active' => ['admin.leads.*'],
            'permission' => ['leads.view.any', 'leads.view.own'],
        ],
        [
            'label' => 'Tickets',
            'route' => 'admin.tickets.index',
            'active' => ['admin.tickets.*'],
            'permission' => ['tickets.view.any', 'tickets.view.own'],
        ],
        [
            'label' => 'Agents',
            'route' => 'admin.agents.index',
            'active' => ['admin.agents.*'],
            'permission' => ['agents.view', 'agents.manage'],
        ],
        [
            'label' => 'CMS',
            'route' => 'admin.cms.index',
            'active' => ['admin.cms.*'],
            'permission' => 'cms.view',
        ],
        [
            'label' => 'Countries',
            'route' => 'admin.countries.index',
            'active' => ['admin.countries.*', 'admin.kyc-document-types.*', 'admin.country-payment-methods.*'],
            'permission' => ['countries.view', 'countries.update'],
        ],
        [
            'label' => 'Organizations',
            'route' => 'admin.organizations.index',
            'active' => ['admin.organizations.*'],
            'permission' => ['organizations.view', 'organizations.update'],
        ],
        [
            'label' => 'Cancellation policies',
            'route' => 'admin.cancellation-policies.index',
            'active' => ['admin.cancellation-policies.*'],
            'permission' => ['cancellation_policies.view', 'cancellation_policies.update'],
        ],
        [
            'label' => 'Settings',
            'route' => 'admin.settings.edit',
            'active' => ['admin.settings.*'],
            'permission' => 'settings.view',
        ],
        [
            'label' => 'Activity',
            'route' => 'admin.activity.index',
            'active' => ['admin.activity.*'],
            'permission' => 'activity_log.view',
        ],
        [
            'label' => '2FA',
            'route' => 'admin.two-factor.show',
            'active' => ['admin.two-factor.*'],
            'permission' => 'panel.access',
            'roles' => ['super_admin', 'admin'],
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
            'permission' => ['rooms.view.any', 'rooms.view.own'],
            'roles' => ['super_admin', 'admin', 'hotel_manager'],
        ],
        [
            'label' => 'Bookings',
            'route' => 'admin.bookings.index',
            'active' => ['admin.bookings.*'],
            'permission' => ['bookings.view.any', 'bookings.view.own'],
        ],
        [
            'label' => 'Payments',
            'route' => 'admin.payments.index',
            'active' => ['admin.payments.*'],
            'permission' => ['payments.view.any', 'payments.view.own'],
        ],
        [
            'label' => 'Reviews',
            'route' => 'admin.reviews.index',
            'active' => ['admin.reviews.*'],
            'permission' => ['reviews.view.any', 'reviews.moderate'],
        ],
        [
            'label' => 'KYC',
            'route' => 'admin.kyc.index',
            'active' => ['admin.kyc.*'],
            'permission' => ['kyc.view.any', 'kyc.review'],
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
