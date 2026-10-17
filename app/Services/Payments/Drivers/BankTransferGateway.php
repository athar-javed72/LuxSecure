<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Services\Payments\InvoiceService;
use Illuminate\Validation\ValidationException;

class BankTransferGateway implements PaymentGateway
{
    public function __construct(private readonly InvoiceService $invoices) {}

    public function name(): string
    {
        return 'bank_transfer';
    }

    public function initiate(Payment $payment, array $payload = []): array
    {
        $payment->update([
            'status' => 'awaiting_approval',
            'proof_path' => $payload['proof_path'] ?? $payment->proof_path,
            'meta' => array_merge($payment->meta ?? [], [
                'bank_reference' => $payload['bank_reference'] ?? null,
                'instructions' => config('payments.bank_transfer.instructions'),
            ]),
        ]);

        return [
            'payment_uuid' => $payment->uuid,
            'status' => $payment->status,
            'message' => 'Upload proof received. Awaiting admin approval.',
        ];
    }

    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        return false;
    }

    public function handleWebhook(array $payload): Payment
    {
        throw ValidationException::withMessages([
            'gateway' => 'Bank transfer does not use webhooks.',
        ]);
    }

    public function approve(Payment $payment, int $approverId): Payment
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'approved_by' => $approverId,
        ]);

        $this->invoices->createFor($payment);

        return $payment->fresh('invoice');
    }
}
