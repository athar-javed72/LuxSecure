<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorForPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->can('panel.access')) {
            return $next($request);
        }

        if ($user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        if ($request->routeIs('admin.two-factor.*', 'api.v1.two-factor.*', 'logout')) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication setup is required.',
                'code' => 'two_factor_setup_required',
            ], 403);
        }

        return redirect()
            ->route('admin.two-factor.show')
            ->with('error', 'Please set up two-factor authentication to continue.');
    }
}
