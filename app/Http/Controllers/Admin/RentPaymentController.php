<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarkRentPaidRequest;
use App\Mail\RentPaymentReceiptMail;
use App\Models\RentPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class RentPaymentController extends Controller
{
    public function markPaid(MarkRentPaidRequest $request, RentPayment $rent_payment): RedirectResponse
    {
        $rent_payment->load(['tenancy.tenant', 'tenancy.listing']);
        $rent_payment->markPaid($request->validated('method'));

        Mail::to($rent_payment->tenancy->tenant->email)
            ->queue(new RentPaymentReceiptMail($rent_payment));

        return back()->with('success', 'Payment marked as paid. Receipt '.$rent_payment->receipt_no);
    }
}
