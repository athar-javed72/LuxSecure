<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\Drivers\BankTransferGateway;
use App\Support\CsvExporter;
use App\Support\StorageDisk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::query()->with(['user', 'invoice', 'payable'])->latest();

        if (! $request->user()->can('payments.view.any')) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('status') && in_array($request->status, Payment::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway') && in_array($request->gateway, Payment::GATEWAYS, true)) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('uuid', 'like', "%{$search}%")
                    ->orWhere('external_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }

        return view('admin.payments.index', [
            'payments' => $query->paginate(20)->withQueryString(),
            'statuses' => Payment::STATUSES,
            'gateways' => Payment::GATEWAYS,
        ]);
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);
        $payment->load(['user', 'invoice', 'payable', 'approver']);

        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Payment $payment, BankTransferGateway $bank): RedirectResponse
    {
        $this->authorize('approve', $payment);

        abort_unless($payment->gateway === 'bank_transfer' && $payment->status === 'awaiting_approval', 422);

        $bank->approve($payment, (int) request()->user()->id);

        return back()->with('success', 'Payment approved and invoice generated.');
    }

    public function downloadInvoice(Payment $payment): StreamedResponse
    {
        $this->authorize('view', $payment);
        abort_unless($payment->invoice?->pdf_path, 404);

        return StorageDisk::privateDisk()->download(
            $payment->invoice->pdf_path,
            $payment->invoice->number.'.pdf'
        );
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Payment::class);
        abort_unless(
            $request->user()->can('reports.export') || $request->user()->can('payments.view.any'),
            403
        );

        $query = Payment::query()->with('user');
        if (! $request->user()->can('payments.view.any')) {
            $query->where('user_id', $request->user()->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $rows = $query->latest()->get()->map(fn (Payment $p) => [
            'uuid' => $p->uuid,
            'user' => $p->user?->email,
            'amount' => $p->amount,
            'currency' => $p->currency,
            'gateway' => $p->gateway,
            'status' => $p->status,
            'external_id' => $p->external_id,
            'paid_at' => optional($p->paid_at)->toDateTimeString(),
        ]);

        return CsvExporter::download(
            'payments.csv',
            ['uuid', 'user', 'amount', 'currency', 'gateway', 'status', 'external_id', 'paid_at'],
            $rows
        );
    }
}
