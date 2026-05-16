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

    protected array $rendered;

    public function __construct(
        protected string $templateKey,
        protected array $data = [],
        protected ?string $locale = null,
    ) {
        $template = EmailTemplate::where('key', $this->templateKey)->first();
        $this->rendered = $template?->render($this->data, $this->locale)
            ?? ['subject' => 'Notification', 'body' => ''];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->rendered['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->rendered['body'],
        );
    }
}
