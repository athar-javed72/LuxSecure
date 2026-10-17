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

    'messaging' => [
        'default' => env('MESSAGING_DRIVER', 'log'),
    ],
];
