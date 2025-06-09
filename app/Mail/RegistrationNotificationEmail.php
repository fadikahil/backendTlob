<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private String $username;

    /**
     * Create a new message instance.
     */
    public function __construct(String $username)
    {
        $this->username = $username;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Notification Email',
        );
    }

    public function build(): RegistrationNotificationEmail {
        return $this->view('emails.notification-email')->with('message_content', 'A user "' . $this->username. '" registered on the system, kindly review their profile and assign them a package if necessary');
    }
}
