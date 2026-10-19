<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Listing query cache
    |--------------------------------------------------------------------------
    |
    | Prefer CACHE_DRIVER=redis in production. database/file also work via
    | the versioned key strategy in ListingCacheService.
    |
    */
    'listing_cache_store' => env('LISTING_CACHE_STORE', env('CACHE_DRIVER', 'file')),
    'listing_cache_ttl' => (int) env('LISTING_CACHE_TTL', 60),

    'image' => [
        'max_width' => (int) env('IMAGE_MAX_WIDTH', 1920),
        'quality' => (int) env('IMAGE_QUALITY', 82),
    ],

    'backup' => [
        'disk' => env('BACKUP_DISK', 'local'),
        'path' => env('BACKUP_PATH', 'backups'),
        'keep' => (int) env('BACKUP_KEEP', 7),
    ],

    'uploads_disk' => env('UPLOADS_DISK', 'public'),
    'private_disk' => env('PRIVATE_DISK', 'local'),

    'booking_hold_minutes' => (int) env('BOOKING_HOLD_MINUTES', 30),

    'trusted_proxies' => env('TRUSTED_PROXIES', ''),

    'sanctum_2fa_setup_minutes' => (int) env('SANCTUM_2FA_SETUP_MINUTES', 15),

    /*
    | When false, panel users are not forced to enroll 2FA and login skips
    | the challenge even if a secret was previously confirmed. Re-enable for
    | production staff accounts when ready.
    */
    'two_factor_required' => (bool) env('TWO_FACTOR_REQUIRED', true),

    'messaging' => [
        'default' => env('MESSAGING_DRIVER', 'log'),
        'allow_log_in_production' => (bool) env('MESSAGING_ALLOW_LOG_IN_PRODUCTION', false),
        'http' => [
            'url' => env('MESSAGING_HTTP_URL', ''),
            'key' => env('MESSAGING_HTTP_KEY'),
            'sender' => env('MESSAGING_HTTP_SENDER'),
        ],
    ],
];
