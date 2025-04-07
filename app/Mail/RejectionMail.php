<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reason;

    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Service Provider Application Rejected')
            ->view('emails.rejection')
            ->with(['reason' => $this->reason]);
    }
}
