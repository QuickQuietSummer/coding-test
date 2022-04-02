<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestContact extends Mailable
{
    use Queueable, SerializesModels;

    public string $fromEmail;
    public string $fromName;
    public int $requestId;
    public string $employeeMessage;


    /**
     * Create a new message instance.
     *
     * @param string $fromEmail
     * @param string $fromName
     * @param int $requestId
     * @param string $employeeMessage
     */
    public function __construct(string $fromEmail, string $fromName, int $requestId, string $employeeMessage)
    {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->requestId = $requestId;
        $this->employeeMessage = $employeeMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->fromEmail, $this->fromName)->view('emails.request.contact');
    }
}
