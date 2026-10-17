<?php

namespace App\Services;

use App\Contracts\MessageChannel;
use App\Models\PhoneOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PhoneOtpService
{
    public function __construct(private readonly MessageChannel $sms) {}

    public function send(User $user, string $phone): void
    {
        $code = (string) random_int(100000, 999999);

        PhoneOtp::query()
            ->where('user_id', $user->id)
            ->where('phone', $phone)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        PhoneOtp::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->sms->send($phone, "Your LuxSecure verification code is {$code}", [
            'user_id' => $user->id,
            'purpose' => 'phone_verification',
        ]);
    }

    public function verify(User $user, string $phone, string $code): void
    {
        $otp = PhoneOtp::query()
            ->where('user_id', $user->id)
            ->where('phone', $phone)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (! $otp || $otp->isExpired()) {
            throw ValidationException::withMessages(['code' => 'OTP expired or not found.']);
        }

        if ($otp->attempts >= 5) {
            throw ValidationException::withMessages(['code' => 'Too many attempts. Request a new code.']);
        }

        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code_hash)) {
            throw ValidationException::withMessages(['code' => 'Invalid verification code.']);
        }

        $otp->update(['consumed_at' => now()]);
        $user->update([
            'phone' => $phone,
            'phone_verified_at' => now(),
        ]);
    }
}
