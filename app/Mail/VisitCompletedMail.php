<?php

namespace App\Mail;

use App\Models\Visit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Visit $visit
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Visit Completed - Haland PetCare',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visit-completed',
            with: [
                'visit' => $this->visit->load(['customer', 'pet', 'creator', 'invoice']),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
