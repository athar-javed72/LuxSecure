<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiatePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentPayment;
use App\Services\Payments\PaymentGatewayManager;
use App\Support\CountryContext;
use App\Support\StorageDisk;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentGatewayManager $gateways) {}

    public function initiate(InitiatePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        [$payable, $amount] = $this->resolvePayable($data['payable_type'], (int) $data['payable_id'], $request->user()->id);

        if (! empty($data['idempotency_key'])) {
            $existing = Payment::query()->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                return back()->with('success', 'Payment already initiated.');
            }
        }

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment-proofs', StorageDisk::private());
        }

        $payment = Payment::create([
            'payable_type' => $payable::class,
            'payable_id' => $payable->id,
            'user_id' => $request->user()->id,
            'amount' => $amount,
            'currency' => $data['currency']
                ?? ($payable->currency ?? null)
                ?? ($payable->listing?->currency ?? null)
                ?? CountryContext::current()?->currency_code
                ?? 'PKR',
            'country_id' => $payable->country_id
                ?? ($payable->listing?->country_id ?? null)
                ?? CountryContext::current()?->id,
            'gateway' => $data['gateway'],
            'status' => 'pending',
            'idempotency_key' => $data['idempotency_key'] ?? null,
            'proof_path' => $proofPath,
        ]);

        $result = $this->gateways->driver($data['gateway'])->initiate($payment, [
            'proof_path' => $proofPath,
            'bank_reference' => $data['bank_reference'] ?? null,
        ]);

        return back()->with('success', $result['message'] ?? 'Payment initiated.')->with('payment_checkout', $result);
    }

    /** @return array{0: Booking|RentPayment, 1: float} */
    private function resolvePayable(string $type, int $id, int $userId): array
    {
        if ($type === 'booking') {
            $booking = Booking::query()->where('id', $id)->where('user_id', $userId)->firstOrFail();

            return [$booking, (float) $booking->total];
        }

        $rent = RentPayment::query()
            ->where('id', $id)
            ->whereHas('tenancy', fn ($q) => $q->where('tenant_id', $userId))
            ->firstOrFail();

        return [$rent, (float) $rent->amount];
    }
}
