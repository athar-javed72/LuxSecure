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

        $country = $payment->country_id
            ? \App\Models\Country::query()->find($payment->country_id)
            : \App\Support\CountryContext::current();
        $taxRate = (float) ($country?->tax_percent
            ?? \App\Support\CountryContext::setting('commerce.tax_rate', Setting::getValue('commerce.tax_rate', 0)));
        $taxName = $country?->tax_name;
        $subtotal = (float) $payment->amount;
        $tax = round($subtotal * ($taxRate / 100), 2);
        $total = $subtotal + $tax;

        $number = $country
            ? $country->nextInvoiceNumber()
            : 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

        $invoice = Invoice::create([
            'payment_id' => $payment->id,
            'country_id' => $country?->id,
            'number' => $number,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_name' => $taxName,
            'total' => $total,
            'currency' => $payment->currency,
            'line_items' => [[
                'description' => class_basename($payment->payable_type).' #'.$payment->payable_id,
                'amount' => $subtotal,
            ]],
        ]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load('payment.user'),
            'siteName' => \App\Support\CountryContext::setting('site.name', Setting::getValue('site.name', config('app.name'))),
        ]);

        $path = 'invoices/'.$invoice->number.'.pdf';
        StorageDisk::privateDisk()->put($path, $pdf->output());
        $invoice->update(['pdf_path' => $path]);

        return $invoice->fresh();
    }
}
