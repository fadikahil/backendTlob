<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    private String $item_name;

    /**
     * Create a new message instance.
     */
    public function __construct(String $item_name)
    {
        $this->item_name = $item_name;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Report Submitted',
        );
    }

    public function build(): ReportNotificationEmail {
        return $this->view('emails.notification-email')->with('message_content', 'A report of item with name "'. $this->item_name. '" was submitted, login to check it out!');
    }
}
