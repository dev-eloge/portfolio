<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Notification envoyée au propriétaire du portfolio (toujours en français). */
class ContactMessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
        $this->locale('fr');
    }

    public function envelope(): Envelope
    {
        $topic = $this->contactMessage->subject ?: $this->contactMessage->name;

        return new Envelope(
            // « Répondre » écrit directement à la personne qui a envoyé le message
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            subject: "Nouveau message du portfolio : {$topic}",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.contact-message-received');
    }
}
