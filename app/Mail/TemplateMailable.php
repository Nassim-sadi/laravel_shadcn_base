<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected string $templateKey,
        protected array $data = [],
        protected ?string $locale = null,
    ) {}

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', $this->templateKey)->first();
        $rendered = $template?->render($this->data, $this->locale);

        return new Envelope(
            subject: $rendered['subject'] ?? 'Notification',
        );
    }

    public function content(): Content
    {
        $template = EmailTemplate::where('key', $this->templateKey)->first();
        $rendered = $template?->render($this->data, $this->locale);

        return new Content(
            htmlString: $rendered['body'] ?? '',
        );
    }
}
