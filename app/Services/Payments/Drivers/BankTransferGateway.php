<?php

namespace App\Services\Payments\Drivers;

use App\Contracts\PaymentGateway;
use App\Models\Payment;
use App\Services\Payments\InvoiceService;
use App\Services\Payments\PaymentStatusMachine;
use Illuminate\Validation\ValidationException;

class BankTransferGateway implements PaymentGateway
{
    public function __construct(
        private readonly InvoiceService $invoices,
        private readonly PaymentStatusMachine $statuses,
    ) {}

    public function name(): string
    {
        return 'bank_transfer';
    }

    public function initiate(Payment $payment, array $payload = []): array
    {
        if (blank($payload['proof_path'] ?? null) && blank($payment->proof_path)) {
            throw ValidationException::withMessages([
                'proof' => 'Bank transfer requires a proof upload.',
            ]);
        }

        $payment->update([
            'proof_path' => $payload['proof_path'] ?? $payment->proof_path,
            'meta' => array_merge($payment->meta ?? [], [
                'bank_reference' => $payload['bank_reference'] ?? null,
                'instructions' => config('payments.bank_transfer.instructions'),
            ]),
        ]);

        $this->statuses->transition($payment->fresh(), 'awaiting_approval');

        return [
            'payment_uuid' => $payment->uuid,
            'status' => $payment->fresh()->status,
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
        if ($payment->status === 'paid' || $payment->status === 'refunded') {
            throw ValidationException::withMessages([
                'status' => 'Payment already finalized.',
            ]);
        }

        if ($payment->status !== 'awaiting_approval' || blank($payment->proof_path)) {
            throw ValidationException::withMessages([
                'proof' => 'Payment must be awaiting approval with proof uploaded.',
            ]);
        }

        $payment->forceFill(['approved_by' => $approverId])->save();
        $this->statuses->transition($payment->fresh(), 'paid');
        $this->invoices->createFor($payment->fresh());

        return $payment->fresh('invoice');
    }
}
