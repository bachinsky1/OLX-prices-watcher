<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;

class ConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function build()
    {
        return $this->view('emails.confirm')
                    ->with([
                        'confirmationUrl' => url("/api/confirm-email/{$this->email->confirmation_token}")
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm Email',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
