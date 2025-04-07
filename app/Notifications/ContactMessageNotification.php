<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage; // Optional
use Illuminate\Notifications\Messages\MailMessage; // If email needed

class ContactMessageNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'contact_message' => $this->message->message,
            'name'    => $this->message->name,
            'email'   => $this->message->email,
            'time'    => now()->format('Y-m-d H:i:s'),
        ];
    }
}
