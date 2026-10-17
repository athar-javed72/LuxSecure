<?php

namespace App\Http\Middleware;

use App\Services\UserAccessRevoker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function __construct(private readonly UserAccessRevoker $revoker) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $inactive = ! $user->is_active;
        $revoked = $this->revoker->isSessionRevoked($user, $request);

        if (! $inactive && ! $revoked) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            $user->tokens()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Account deactivated.',
                'code' => 'account_inactive',
            ], 403);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login.form')
            ->withErrors(['email' => 'This account has been deactivated.']);
    }
}
