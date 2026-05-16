<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Notifications\ContactMessageNotification;
use App\Support\Localization\TranslationNamespace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PublicTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_namespace_json_translations(): void
    {
        app()->setLocale('en');

        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSee('Our Story');
        $response->assertDontSee('about.story_title');
    }

    public function test_contact_page_renders_namespace_json_translations(): void
    {
        app()->setLocale('en');

        $response = $this->get('/contact');

        $response->assertOk();
        $response->assertSee('Get In Touch');
        $response->assertDontSee('contact.title');
    }

    public function test_about_and_contact_pages_render_supported_locale_namespaces(): void
    {
        foreach (['en', 'fr', 'ar'] as $locale) {
            app()->setLocale($locale);

            $about = TranslationNamespace::get('about');
            $contact = TranslationNamespace::get('contact');

            $this->get('/about')
                ->assertOk()
                ->assertSee($about['about.story_title'])
                ->assertDontSee('about.story_title');

            $this->get('/contact')
                ->assertOk()
                ->assertSee($contact['contact.title'])
                ->assertDontSee('contact.title');
        }
    }

    public function test_namespace_loader_uses_fallback_locale_when_current_locale_file_is_missing(): void
    {
        config(['app.fallback_locale' => 'fr']);
        app()->setLocale('zz');

        $translations = TranslationNamespace::get('about');

        $this->assertSame('Notre histoire', $translations['about.story_title']);
    }

    public function test_namespace_value_returns_default_for_missing_key(): void
    {
        $value = TranslationNamespace::value('about.missing_key', 'Fallback text');

        $this->assertSame('Fallback text', $value);
    }

    public function test_contact_success_message_uses_namespace_json_translation(): void
    {
        app()->setLocale('en');

        Notification::fake();

        EmailTemplate::create([
            'key' => 'contact_confirmation',
            'name' => ['fr' => 'Confirmation contact'],
            'subject' => ['fr' => 'Merci {name}'],
            'body' => ['fr' => '<p>Merci {name}</p>'],
            'variables' => ['name'],
            'is_active' => true,
        ]);

        EmailTemplate::create([
            'key' => 'contact_notification_admin',
            'name' => ['fr' => 'Notification contact'],
            'subject' => ['fr' => 'Nouveau message'],
            'body' => ['fr' => '<p>{message}</p>'],
            'variables' => ['message'],
            'is_active' => true,
        ]);

        $response = $this->post('/contact', [
            'name' => 'Nassim',
            'email' => 'nassim@example.com',
            'subject' => 'Question',
            'message' => 'Bonjour',
        ]);

        $response->assertRedirect(route('public.contact'));
        $response->assertSessionHas('success', 'Thank you! Your message has been sent successfully.');
        Notification::assertSentOnDemand(ContactMessageNotification::class, 2);
    }
}
