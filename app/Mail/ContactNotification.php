<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Internal notification sent to the company (CONTACT_EMAIL) with the full
 * submitted customer details. Queued.
 */
class ContactNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactRequest $contact,
        public string $lang,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->lang === 'ar'
                ? "طلب عرض توضيحي جديد من {$this->contact->name}"
                : "New demo request from {$this->contact->name}",
            replyTo: [$this->contact->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.notification',
            with: [
                'contact' => $this->contact,
                'locale'  => $this->lang,
            ],
        );
    }
}
