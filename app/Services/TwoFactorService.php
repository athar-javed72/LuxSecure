<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    public function __construct(private readonly Google2FA $google2fa = new Google2FA) {}

    public function requiresTwoFactor(User $user): bool
    {
        if (! config('maskanoor.two_factor_required', true)) {
            return false;
        }

        return $user->can('panel.access') && filled($user->two_factor_confirmed_at);
    }

    public function requiresSetup(User $user): bool
    {
        if (! config('maskanoor.two_factor_required', true)) {
            return false;
        }

        return $user->can('panel.access') && blank($user->two_factor_confirmed_at);
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function qrUrl(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
    }

    public function confirm(User $user, string $secret, string $code): bool
    {
        if (! $this->verify($secret, $code)) {
            return false;
        }

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($this->generateRecoveryCodes()->all())),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return true;
    }

    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    public function verifyUser(User $user, string $code): bool
    {
        if (blank($user->two_factor_secret)) {
            return false;
        }

        $secret = decrypt($user->two_factor_secret);

        if ($this->verify($secret, $code)) {
            return true;
        }

        return $this->useRecoveryCode($user, $code);
    }

    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    /** @return Collection<int, string> */
    public function generateRecoveryCodes(): Collection
    {
        return collect(range(1, 8))->map(fn () => Str::lower(Str::random(10)));
    }

    public function issueChallengeToken(User $user): string
    {
        $token = Str::random(64);
        Cache::put($this->challengeKey($token), $user->id, now()->addMinutes(5));

        return $token;
    }

    public function userFromChallengeToken(string $token): ?User
    {
        $userId = Cache::pull($this->challengeKey($token));

        return $userId ? User::find($userId) : null;
    }

    private function challengeKey(string $token): string
    {
        return '2fa.challenge.'.$token;
    }

    private function useRecoveryCode(User $user, string $code): bool
    {
        if (blank($user->two_factor_recovery_codes)) {
            return false;
        }

        $codes = collect(json_decode(decrypt($user->two_factor_recovery_codes), true) ?: []);
        $normalized = Str::lower(trim($code));

        if (! $codes->contains($normalized)) {
            return false;
        }

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($codes->reject(fn ($c) => $c === $normalized)->values()->all())),
        ])->save();

        return true;
    }
}
