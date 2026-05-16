<?php

namespace App\Notifications;

use App\Mail\TemplateMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $templateKey,
        protected array $data = [],
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): TemplateMailable
    {
        return new TemplateMailable($this->templateKey, $this->data);
    }
}
