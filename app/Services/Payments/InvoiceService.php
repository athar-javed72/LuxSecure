<?php

namespace App\Services\Payments;

use App\Models\Country;
use App\Models\CountrySetting;
use App\Models\Currency;
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
            ? Country::query()->find($payment->country_id)
            : null;

        $taxRate = (float) ($country?->tax_percent
            ?? ($country ? CountrySetting::getValue($country->id, 'commerce.tax_rate') : null)
            ?? Setting::getValue('commerce.tax_rate', 0));
        $taxName = $country?->tax_name;
        $subtotal = (float) $payment->amount;

        $currency = Currency::query()->where('code', strtoupper((string) $payment->currency))->first();
        $decimals = (int) ($currency?->decimals ?? 2);
        $tax = round($subtotal * ($taxRate / 100), $decimals);

        $payable = $payment->payable;
        $purpose = null;
        if ($payable) {
            if (method_exists($payable, 'listing')) {
                $purpose = $payable->listing?->purpose;
            }
            $purpose ??= $payable->purpose ?? null;
        }

        $commissionKey = match ($purpose) {
            'rent' => 'commerce.commission_rent',
            'sale' => 'commerce.commission_sale',
            'short_stay' => 'commerce.commission_hotel',
            default => 'commerce.commission_rate',
        };
        $commissionRate = (float) (
            ($country ? CountrySetting::getValue($country->id, $commissionKey) : null)
            ?? Setting::getValue($commissionKey, Setting::getValue('commerce.commission_rate', 0))
        );

        // Invoice total mirrors charged payment amount (tax informational when amount is the charged total).
        $total = $subtotal;
        $number = $country
            ? $country->nextInvoiceNumber()
            : 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

        $siteName = ($country ? CountrySetting::getValue($country->id, 'site.name') : null)
            ?? Setting::getValue('site.name', config('app.name'));

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
                'description' => class_basename((string) $payment->payable_type).' #'.$payment->payable_id
                    .($commissionRate > 0 ? ' (commission '.$commissionRate.'%)' : ''),
                'amount' => $subtotal,
                'commission_percent' => $commissionRate,
            ]],
        ]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load('payment.user'),
            'siteName' => $siteName,
        ]);

        $path = 'invoices/'.$invoice->number.'.pdf';
        StorageDisk::privateDisk()->put($path, $pdf->output());
        $invoice->update(['pdf_path' => $path]);

        return $invoice->fresh();
    }
}
