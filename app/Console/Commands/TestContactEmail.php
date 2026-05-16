<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use App\Notifications\ContactMessageNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TestContactEmail extends Command
{
    protected $signature = 'email:test-contact';
    protected $description = 'Send a test contact email to verify notification + TemplateMailable work';

    public function handle(): int
    {
        $email = $this->ask('Recipient email');
        $name = $this->ask('Name', 'Test User');
        $subject = $this->ask('Subject', 'Test subject');
        $message = $this->ask('Message', 'This is a test message.');

        $data = compact('name', 'email', 'subject', 'message');

        $this->info('Sending contact confirmation...');
        Notification::route('mail', $email)
            ->notify(new ContactMessageNotification('contact_confirmation', $data));

        $this->info('Sending admin notification...');
        Notification::route('mail', $email)
            ->notify(new ContactMessageNotification('contact_notification_admin', $data));

        $this->info('Done. Check your mail — or run php artisan queue:work if QUEUE_CONNECTION=database.');

        return Command::SUCCESS;
    }
}
