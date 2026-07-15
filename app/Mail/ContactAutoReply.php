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
 * Auto-reply sent to the customer confirming their demo request was received.
 * Queued so the HTTP request never blocks on SMTP.
 */
class ContactAutoReply extends Mailable implements ShouldQueue
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
                ? 'تم استلام طلبك — NAS LMS'
                : 'We received your request — NAS LMS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.auto-reply',
            with: [
                'contact' => $this->contact,
                'locale'  => $this->lang,
            ],
        );
    }
}
