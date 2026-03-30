<?php

namespace App\Mail;

use App\Application\Shared\MailData\CouponCampaignMailData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LegacyCouponCampaignMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $theme = 'laravel-shop';

    public function __construct(
        public CouponCampaignMailData $mailData,
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
            markdown: 'mail.legacy.coupon-campaign',
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
