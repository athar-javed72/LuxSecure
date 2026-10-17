<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class SanctumAbilityResolver
{
    public const ABILITY_2FA_SETUP = '2fa:setup';

    /**
     * @return list<string>
     */
    public function forUser(User $user): array
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return [
                'listings:*',
                'bookings:*',
                'favorites:*',
                'profile:*',
                'payments:*',
                'users:*',
                'kyc:*',
                'reviews:*',
                'reports:*',
                'panel:*',
            ];
        }

        if ($user->hasAnyRole(['manager', 'agent', 'support', 'accountant'])) {
            return [
                'listings:*',
                'bookings:*',
                'favorites:read',
                'profile:*',
                'payments:read',
                'kyc:read',
                'reviews:read',
                'panel:access',
            ];
        }

        return [
            'listings:read',
            'bookings:*',
            'favorites:*',
            'profile:*',
        ];
    }

    public function issueToken(User $user, string $name = 'api', ?TwoFactorService $twoFactor = null): NewAccessToken
    {
        $twoFactor ??= app(TwoFactorService::class);

        if ($twoFactor->requiresSetup($user)) {
            return $user->createToken(
                $name,
                [self::ABILITY_2FA_SETUP],
                now()->addMinutes((int) config('luxsecure.sanctum_2fa_setup_minutes', 15))
            );
        }

        return $user->createToken($name, $this->forUser($user));
    }

    public function tokenAllows(?object $token, string $ability): bool
    {
        if (! $token || ! method_exists($token, 'can')) {
            return false;
        }

        if ($token->can('*') || $token->can($ability)) {
            return true;
        }

        $abilities = $token->abilities ?? [];
        foreach ($abilities as $have) {
            if (! is_string($have)) {
                continue;
            }
            if ($have === '*') {
                return true;
            }
            if (str_ends_with($have, ':*')) {
                $prefix = substr($have, 0, -1);
                if (str_starts_with($ability, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
