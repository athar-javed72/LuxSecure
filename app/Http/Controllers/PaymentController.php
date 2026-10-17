<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentPayment;
use App\Services\Payments\PaymentGatewayManager;
use App\Support\StorageDisk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentGatewayManager $gateways) {}

    public function initiate(Request $request): RedirectResponse
    {
        $this->authorize('create', Payment::class);

        $data = $request->validate([
            'payable_type' => ['required', Rule::in(['booking', 'rent_payment'])],
            'payable_id' => ['required', 'integer'],
            'gateway' => ['required', Rule::in(Payment::GATEWAYS)],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'bank_reference' => ['nullable', 'string', 'max:120'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

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
            'currency' => 'PKR',
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
