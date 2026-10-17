<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Two-factor authentication
 *
 * @authenticated
 *
 * Admin-level roles (admin, super_admin) can enable TOTP 2FA.
 */
class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Begin 2FA setup (admin roles only).
     */
    public function setup(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Two-factor setup is limited to admin-level roles.');

        $secret = $this->twoFactor->generateSecret();
        cache()->put('2fa.setup.'.$user->id, $secret, now()->addMinutes(15));

        return ApiResponse::success([
            'secret' => $secret,
            'otpauth_url' => $this->twoFactor->qrUrl($user, $secret),
        ], 'Scan the otpauth URL with an authenticator app, then confirm.');
    }

    /**
     * Confirm 2FA with a code from the authenticator.
     */
    public function confirm(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403);

        $data = $request->validate(['code' => ['required', 'string']]);
        $secret = cache()->pull('2fa.setup.'.$user->id);

        if (! $secret || ! $this->twoFactor->confirm($user, $secret, $data['code'])) {
            return ApiResponse::error('Unable to confirm two-factor authentication.', 422, null, 'invalid_2fa');
        }

        return ApiResponse::success([
            'two_factor_enabled' => true,
        ], 'Two-factor authentication enabled.');
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403);

        $data = $request->validate(['code' => ['required', 'string']]);
        if (! $this->twoFactor->verifyUser($user, $data['code'])) {
            return ApiResponse::error('Invalid two-factor code.', 422, null, 'invalid_2fa');
        }

        $this->twoFactor->disable($user);

        return ApiResponse::success(['two_factor_enabled' => false], 'Two-factor authentication disabled.');
    }
}
