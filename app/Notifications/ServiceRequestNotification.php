<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ServiceRequestNotification extends Notification
{
    use Queueable;

    protected $userName;
    protected $serviceName;
    protected $subServiceName;
    protected $submissionTime;

    public function __construct($userName, $serviceName, $subServiceName, $submissionTime)
    {
        $this->userName = $userName;
        $this->serviceName = $serviceName;
        $this->subServiceName = $subServiceName;
        $this->submissionTime = $submissionTime;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_name' => $this->userName ?? 'Unknown User',
            'service_name' => $this->serviceName ?? 'Unknown Service',
            'sub_service_name' => $this->subServiceName ?? 'N/A',
            'provider_id' => $notifiable->id,
            'message' => sprintf(
                "%s has requested %s - %s",
                $this->userName ?? 'Unknown User',
                $this->serviceName ?? 'Unknown Service',
                $this->subServiceName ?? 'N/A'
            ),
            'time'=> $this->submissionTime,
        ];
    }
}
