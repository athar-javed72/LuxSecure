<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;

/**
 * App CSRF / request-forgery middleware (Laravel 13 PreventRequestForgery).
 * Class name kept for Sanctum config compatibility.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF / forgery verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhooks/payments/*',
    ];
}
