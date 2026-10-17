<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Services\Payments\Drivers\BankTransferGateway;
use App\Services\Payments\Drivers\EasypaisaGateway;
use App\Services\Payments\Drivers\JazzCashGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function driver(string $name): PaymentGateway
    {
        return match ($name) {
            'bank_transfer' => app(BankTransferGateway::class),
            'jazzcash' => app(JazzCashGateway::class),
            'easypaisa' => app(EasypaisaGateway::class),
            default => throw new InvalidArgumentException("Unknown payment gateway [{$name}]."),
        };
    }
}
