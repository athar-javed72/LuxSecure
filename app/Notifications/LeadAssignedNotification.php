<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New lead assigned')
            ->line('Lead "'.$this->lead->name.'" was assigned to you.')
            ->action('Open lead', url('/admin/leads/'.$this->lead->id.'/edit'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'lead_assigned',
            'lead_id' => $this->lead->id,
            'message' => 'Lead '.$this->lead->name.' assigned to you.',
        ];
    }
}
