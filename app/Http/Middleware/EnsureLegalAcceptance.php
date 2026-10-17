<?php

namespace App\Http\Middleware;

use App\Support\LegalAcceptanceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegalAcceptance
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('legal.accept*', 'logout', 'pages.show', 'login*', 'register*', 'password.*', 'verification.*', 'two-factor.*')) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return $next($request);
        }

        if (LegalAcceptanceService::requiresAcceptance($user)) {
            return redirect()->guest(route('legal.accept'));
        }

        return $next($request);
    }
}
