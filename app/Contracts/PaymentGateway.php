<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentGateway
{
    public function name(): string;

    /** @param  array<string, mixed>  $payload */
    public function initiate(Payment $payment, array $payload = []): array;

    /** @param  array<string, mixed>  $payload */
    public function verifyWebhook(array $payload, ?string $signature = null): bool;

    /** @param  array<string, mixed>  $payload */
    public function handleWebhook(array $payload): Payment;
}
