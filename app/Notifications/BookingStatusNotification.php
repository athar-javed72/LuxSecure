<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking '.$this->booking->status)
            ->line('Your booking #'.$this->booking->id.' is now '.$this->booking->status.'.')
            ->line('Listing: '.($this->booking->listing?->title ?? 'N/A'))
            ->action('View booking', url('/bookings/'.$this->booking->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking',
            'booking_id' => $this->booking->id,
            'status' => $this->booking->status,
            'message' => 'Booking #'.$this->booking->id.' is '.$this->booking->status.'.',
        ];
    }
}
