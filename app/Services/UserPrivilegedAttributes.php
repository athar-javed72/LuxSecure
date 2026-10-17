<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Sole write path for privileged User columns removed from $fillable.
 */
class UserPrivilegedAttributes
{
    public function setActive(User $user, bool $active): void
    {
        $user->forceFill(['is_active' => $active])->save();
    }

    public function setEmailVerifiedAt(User $user, ?CarbonInterface $at): void
    {
        $user->forceFill(['email_verified_at' => $at])->save();
    }

    public function setPhoneVerified(User $user, string $phone, ?CarbonInterface $at = null): void
    {
        $user->forceFill([
            'phone' => $phone,
            'phone_verified_at' => $at ?? now(),
        ])->save();
    }

    public function setKycVerifiedAt(User $user, ?CarbonInterface $at): void
    {
        $user->forceFill(['kyc_verified_at' => $at])->save();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createActive(array $attributes, bool $active = true): User
    {
        $user = new User;
        $user->fill(collect($attributes)->only([
            'name', 'email', 'phone', 'password', 'country_id', 'organization_id',
            'preferred_locale', 'preferred_currency',
        ])->all());
        $user->forceFill([
            'is_active' => $active,
            'email_verified_at' => $attributes['email_verified_at'] ?? null,
            'country_id' => $attributes['country_id'] ?? null,
            'organization_id' => $attributes['organization_id'] ?? null,
            'preferred_locale' => $attributes['preferred_locale'] ?? null,
            'preferred_currency' => $attributes['preferred_currency'] ?? null,
        ])->save();

        return $user;
    }
}
