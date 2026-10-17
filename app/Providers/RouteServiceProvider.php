<?php

namespace App\Providers;

/**
 * Kept for HOME constant used by auth redirects.
 * Routing and rate limiters live in bootstrap/app.php and AppServiceProvider.
 */
class RouteServiceProvider
{
    public const HOME = '/';
}
