<?php

namespace App\Http\Middleware;

use App\Services\SanctumAbilityResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSanctumAbility
{
    public function __construct(private readonly SanctumAbilityResolver $abilities) {}

    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        $needed = [];
        foreach ($abilities as $ability) {
            foreach (explode(',', $ability) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $needed[] = $part;
                }
            }
        }

        $token = $user->currentAccessToken();

        foreach ($needed as $ability) {
            if ($this->abilities->tokenAllows($token, $ability)) {
                return $next($request);
            }
            if (method_exists($user, 'tokenCan') && $user->tokenCan($ability)) {
                return $next($request);
            }
        }

        abort(403, 'Missing required token ability.');
    }
}
