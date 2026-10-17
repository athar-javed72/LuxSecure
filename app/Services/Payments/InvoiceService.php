<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Support\StorageDisk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class InvoiceService
{
    public function createFor(Payment $payment): Invoice
    {
        if ($payment->invoice) {
            return $payment->invoice;
        }

        $taxRate = (float) Setting::getValue('commerce.tax_rate', 0);
        $subtotal = (float) $payment->amount;
        $tax = round($subtotal * ($taxRate / 100), 2);
        $total = $subtotal + $tax;

        $invoice = Invoice::create([
            'payment_id' => $payment->id,
            'number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'line_items' => [[
                'description' => class_basename($payment->payable_type).' #'.$payment->payable_id,
                'amount' => $subtotal,
            ]],
        ]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load('payment.user'),
            'siteName' => Setting::getValue('site.name', config('app.name')),
        ]);

        $path = 'invoices/'.$invoice->number.'.pdf';
        StorageDisk::privateDisk()->put($path, $pdf->output());
        $invoice->update(['pdf_path' => $path]);

        return $invoice->fresh();
    }
}
