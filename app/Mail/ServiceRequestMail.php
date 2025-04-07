<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ServiceRequest;

class ServiceRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function build()
    {
        return $this->subject('Service Request Received')
                    ->view('emails.service_request')
                    ->with([
                        'name' => $this->serviceRequest->name,
                        'service' => $this->serviceRequest->service->service_name,
                        'subService' => $this->serviceRequest->subService->sub_service_name ?? 'N/A',
                        'provider' => $this->serviceRequest->provider->first_name . ' ' . $this->serviceRequest->provider->last_name,
                    ]);
    }
}
