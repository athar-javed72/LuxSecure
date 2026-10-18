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

        if ($request->routeIs(
            'legal.accept*',
            'logout',
            'pages.show',
            'login*',
            'register*',
            'password.*',
            'verification.*',
            'two-factor.*',
            'api.v1.auth.*',
            'webhooks.*'
        ) || $request->is('webhooks/*')) {
            return $next($request);
        }

        if (! LegalAcceptanceService::requiresAcceptance($user)) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Legal acceptance required.',
                'code' => 'legal_acceptance_required',
            ], 403);
        }

        return redirect()->guest(route('legal.accept'));
    }
}
