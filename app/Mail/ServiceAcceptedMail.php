<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;

    public function __construct($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function build()
    {
        return $this->subject('Your Service Request has been Accepted')
                    ->view('emails.service_accepted')
                    ->with([
                        'providerName' => $this->serviceRequest->provider->first_name . ' ' . $this->serviceRequest->provider->last_name,
                    ]);
    }
}
