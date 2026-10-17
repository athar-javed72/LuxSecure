<?php

namespace App\Mail;

use App\Models\RentPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentPaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public RentPayment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rent payment receipt '.$this->payment->receipt_no.' — LuxSecure',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rent-payment-receipt',
        );
    }
}
