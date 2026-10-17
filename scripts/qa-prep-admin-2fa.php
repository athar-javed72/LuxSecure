<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\TwoFactorService;
use PragmaRX\Google2FA\Google2FA;

$user = User::where('email', 'admin@luxsecure.com')->firstOrFail();
$service = app(TwoFactorService::class);
$google2fa = new Google2FA;
$secret = $google2fa->generateSecretKey();
$service->confirm($user->fresh(), $secret, $google2fa->getCurrentOtp($secret));

$user->refresh();
$code = $google2fa->getCurrentOtp(decrypt($user->two_factor_secret));

file_put_contents(__DIR__.'/../storage/app/qa-screenshots/admin-otp.txt', $code);
echo $code.PHP_EOL;
