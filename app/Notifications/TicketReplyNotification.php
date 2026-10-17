<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket, public TicketReply $reply) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket reply: '.$this->ticket->subject)
            ->line('A new reply was added to your ticket.')
            ->line(str($this->reply->body)->limit(200))
            ->action('View ticket', url('/admin/tickets/'.$this->ticket->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_reply',
            'ticket_id' => $this->ticket->id,
            'reply_id' => $this->reply->id,
            'message' => 'New reply on ticket #'.$this->ticket->id.'.',
        ];
    }
}
