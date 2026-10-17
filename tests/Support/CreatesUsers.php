<?php

namespace Tests\Support;

use App\Models\User;

trait CreatesUsers
{
    protected function makeUserWithRole(string $role, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'is_active' => true,
        ], $overrides));

        $user->syncRoles([$role]);

        if ($user->can('panel.access') && blank($user->two_factor_confirmed_at)) {
            $user->forceFill([
                'two_factor_secret' => encrypt('TEST2FASECRET233'),
                'two_factor_recovery_codes' => encrypt(json_encode(['recovercode1'])),
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        return $user->fresh();
    }

    protected function makeSuperAdmin(array $overrides = []): User
    {
        return $this->makeUserWithRole('super_admin', $overrides);
    }

    protected function makeAdmin(array $overrides = []): User
    {
        return $this->makeUserWithRole('admin', $overrides);
    }

    protected function makeAgent(array $overrides = []): User
    {
        return $this->makeUserWithRole('agent', $overrides);
    }

    protected function makeClient(array $overrides = []): User
    {
        return $this->makeUserWithRole('client', $overrides);
    }

    protected function makeLandlord(array $overrides = []): User
    {
        return $this->makeUserWithRole('landlord', $overrides);
    }

    protected function makeHotelManager(array $overrides = []): User
    {
        return $this->makeUserWithRole('hotel_manager', $overrides);
    }
}
