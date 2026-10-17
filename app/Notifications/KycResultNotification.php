<?php

namespace App\Notifications;

use App\Models\KycDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public KycDocument $document) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('KYC '.$this->document->status)
            ->line('Your '.$this->document->type.' document was '.$this->document->status.'.');

        if ($this->document->reason) {
            $mail->line('Reason: '.$this->document->reason);
        }

        return $mail->action('View KYC', url('/kyc'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'kyc_result',
            'kyc_document_id' => $this->document->id,
            'status' => $this->document->status,
            'message' => 'KYC '.$this->document->type.' '.$this->document->status.'.',
        ];
    }
}
