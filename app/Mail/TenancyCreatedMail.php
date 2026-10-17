<?php

namespace App\Mail;

use App\Models\Tenancy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenancyCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Tenancy $tenancy) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your tenancy has been created — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tenancy-created',
        );
    }
}
