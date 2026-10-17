<?php

namespace App\Notifications;

use App\Models\RentPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RentPayment $rentPayment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rent payment due')
            ->line('Rent of '.\App\Support\Money::format($this->rentPayment->amount, $this->rentPayment->tenancy?->listing?->currency).' is due on '.$this->rentPayment->due_date->toDateString().'.')
            ->action('View tenancy', url('/tenancies/'.$this->rentPayment->tenancy_id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rent_due',
            'rent_payment_id' => $this->rentPayment->id,
            'tenancy_id' => $this->rentPayment->tenancy_id,
            'due_date' => $this->rentPayment->due_date->toDateString(),
            'message' => 'Rent payment due '.$this->rentPayment->due_date->toDateString().'.',
        ];
    }
}
