<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(private readonly PaymentGatewayManager $gateways) {}

    public function jazzcash(Request $request): JsonResponse
    {
        return $this->handle('jazzcash', $request);
    }

    public function easypaisa(Request $request): JsonResponse
    {
        return $this->handle('easypaisa', $request);
    }

    private function handle(string $gateway, Request $request): JsonResponse
    {
        $driver = $this->gateways->driver($gateway);
        $payload = $request->all();
        $signature = $request->header('X-Signature') ?? $request->input('signature') ?? $request->input('pp_SecureHash');

        if (! $driver->verifyWebhook($payload, is_string($signature) ? $signature : null)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature.'], 401);
        }

        $payment = $driver->handleWebhook($payload);

        return response()->json([
            'success' => true,
            'payment_uuid' => $payment->uuid,
            'status' => $payment->status,
        ]);
    }
}
