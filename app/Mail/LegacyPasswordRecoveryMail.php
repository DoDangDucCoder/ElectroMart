<?php

namespace App\Mail;

use App\Application\Shared\MailData\PasswordRecoveryMailData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LegacyPasswordRecoveryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $theme = 'laravel-shop';

    public function __construct(
        public PasswordRecoveryMailData $mailData,
        public string $subjectLine
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.legacy.password-recovery',
            with: [
                'mailData' => $this->mailData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
