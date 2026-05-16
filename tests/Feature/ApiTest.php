<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    // ─── Auth ───────────────────────────────────────────────────

    public function test_auth_register()
    {
        $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'new@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertCreated();
    }

    public function test_auth_login()
    {
        $this->postJson('/api/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_auth_login_invalid_returns_422()
    {
        $this->postJson('/api/login', [
            'email' => $this->admin->email,
            'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_auth_user()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', $this->admin->email);
    }

    public function test_auth_logout()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/logout')
            ->assertOk();
    }

    public function test_auth_requires_authentication()
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }

    // ─── Users ───────────────────────────────────────────────────

    public function test_authenticated_user_can_list_users()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/users')
            ->assertOk();
    }

    public function test_unauthenticated_cannot_list_users()
    {
        $this->getJson('/api/users')->assertUnauthorized();
    }

    public function test_admin_can_create_user()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Created User',
                'email' => 'created@test.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])->assertCreated();
    }

    public function test_admin_can_update_user()
    {
        $target = User::factory()->create();
        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/users/{$target->id}", ['name' => 'Updated'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated');
    }

    public function test_admin_can_delete_user()
    {
        $target = User::factory()->create();
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/users/{$target->id}")
            ->assertOk();
    }

    // ─── Roles & Permissions ────────────────────────────────────

    public function test_admin_can_list_roles()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/roles')
            ->assertOk();
    }

    public function test_admin_can_create_role()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/roles', ['name' => 'editor', 'guard_name' => 'web'])
            ->assertCreated();
    }

    public function test_admin_can_list_permissions()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/permissions')
            ->assertOk();
    }

    // ─── Services ────────────────────────────────────────────────

    public function test_admin_can_create_service()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/services', [
                'title' => ['fr' => 'Service FR', 'en' => 'Service EN'],
                'description' => ['fr' => 'Description FR'],
                'icon' => 'code',
                'order' => 1,
                'is_active' => true,
            ])->assertCreated();
    }

    public function test_service_requires_translated_title()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/services', ['icon' => 'code'])
            ->assertJsonValidationErrors(['title.fr']);
    }

    public function test_admin_can_update_service()
    {
        $service = Service::create([
            'title' => ['fr' => 'Original'],
            'description' => ['fr' => 'Original desc'],
            'order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/services/{$service->id}", [
                'title' => ['fr' => 'Mis à jour'],
            ])->assertOk();
    }

    public function test_admin_can_delete_service()
    {
        $service = Service::create([
            'title' => ['fr' => 'À supprimer'],
            'description' => ['fr' => 'Desc'],
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/services/{$service->id}")
            ->assertOk();
    }

    public function test_can_list_services()
    {
        Service::create(['title' => ['fr' => 'S1'], 'description' => ['fr' => 'D1'], 'is_active' => true]);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/services')
            ->assertOk();
    }

    // ─── Projects ───────────────────────────────────────────────

    public function test_admin_can_create_project()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/projects', [
                'title' => ['fr' => 'Projet Test'],
                'description' => ['fr' => 'Description'],
                'client' => ['fr' => 'Client'],
                'technologies' => ['Laravel', 'Vue'],
                'is_active' => true,
            ])->assertCreated();
    }

    public function test_admin_can_update_project()
    {
        $project = Project::create([
            'title' => ['fr' => 'Original'],
            'description' => ['fr' => 'Desc'],
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/projects/{$project->id}", [
                'title' => ['fr' => 'Mis à jour'],
            ])->assertOk();
    }

    public function test_admin_can_delete_project()
    {
        $project = Project::create(['title' => ['fr' => 'P'], 'description' => ['fr' => 'D'], 'is_active' => true]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}")
            ->assertOk();
    }

    // ─── Testimonials ───────────────────────────────────────────

    public function test_admin_can_create_testimonial()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/testimonials', [
                'name' => ['fr' => 'Jean Dupont'],
                'content' => ['fr' => 'Excellent service'],
                'rating' => 5,
                'is_active' => true,
            ])->assertCreated();
    }

    public function test_admin_can_delete_testimonial()
    {
        $t = Testimonial::create(['name' => ['fr' => 'N'], 'content' => ['fr' => 'C'], 'is_active' => true]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/testimonials/{$t->id}")
            ->assertOk();
    }

    // ─── FAQs ───────────────────────────────────────────────────

    public function test_admin_can_create_faq()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/faqs', [
                'question' => ['fr' => 'Question ?'],
                'answer' => ['fr' => 'Réponse.'],
                'is_active' => true,
            ])->assertCreated();
    }

    public function test_admin_can_delete_faq()
    {
        $faq = Faq::create(['question' => ['fr' => 'Q'], 'answer' => ['fr' => 'A'], 'is_active' => true]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/faqs/{$faq->id}")
            ->assertOk();
    }

    // ─── Settings ───────────────────────────────────────────────

    public function test_admin_can_list_settings()
    {
        Setting::create(['key' => 'test_key', 'group' => 'general', 'value' => 'test', 'type' => 'string', 'name' => 'Test']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/settings')
            ->assertOk();
    }

    public function test_admin_can_update_setting()
    {
        $setting = Setting::create(['key' => 'update_key', 'group' => 'general', 'value' => 'old', 'type' => 'string', 'name' => 'Update test']);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/settings/{$setting->id}", ['value' => 'new'])
            ->assertOk();
    }

    // ─── Email Templates ────────────────────────────────────────

    public function test_admin_can_create_email_template()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/email-templates', [
                'key' => 'test_template',
                'name' => ['fr' => 'Template test'],
                'subject' => ['fr' => 'Sujet {name}'],
                'body' => ['fr' => '<p>Bonjour {name}</p>'],
                'variables' => ['name'],
                'is_active' => true,
            ])->assertCreated();
    }

    public function test_email_template_render()
    {
        $template = EmailTemplate::create([
            'key' => 'welcome',
            'name' => ['fr' => 'Bienvenue'],
            'subject' => ['fr' => 'Bienvenue {name}'],
            'body' => ['fr' => '<p>Bonjour {name}</p>'],
            'variables' => ['name'],
            'is_active' => true,
        ]);

        $rendered = $template->render(['name' => 'Jean']);
        $this->assertStringContainsString('Bienvenue Jean', $rendered['subject']);
        $this->assertStringContainsString('Bonjour Jean', $rendered['body']);
    }

    public function test_admin_can_delete_email_template()
    {
        $t = EmailTemplate::create(['key' => 'del', 'name' => ['fr' => 'D'], 'subject' => ['fr' => 'S'], 'body' => ['fr' => 'B'], 'is_active' => true]);

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/email-templates/{$t->id}")
            ->assertOk();
    }

    // ─── Contact Messages ───────────────────────────────────────

    public function test_authenticated_user_can_create_contact_message()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/contact-messages', [
                'name' => 'John',
                'email' => 'john@test.com',
                'subject' => 'Question',
                'message' => 'Hello',
            ])->assertCreated();
    }

    public function test_contact_message_requires_valid_email()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/contact-messages', [
                'name' => 'John',
                'email' => 'not-an-email',
                'subject' => 'Subject',
                'message' => 'Body',
            ])->assertJsonValidationErrors(['email']);
    }

    public function test_unauthenticated_cannot_create_contact_message()
    {
        $this->postJson('/api/contact-messages', [
            'name' => 'John',
            'email' => 'john@test.com',
            'subject' => 'Subject',
            'message' => 'Body',
        ])->assertUnauthorized();
    }

    public function test_admin_can_list_contact_messages()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/contact-messages')
            ->assertOk();
    }

    // ─── Activity Logs ──────────────────────────────────────────

    public function test_authenticated_user_can_list_activity_logs()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/activity-logs')
            ->assertOk();
    }

    public function test_unauthenticated_cannot_list_activity_logs()
    {
        $this->getJson('/api/activity-logs')->assertUnauthorized();
    }

    // ─── Media ──────────────────────────────────────────────────

    public function test_admin_can_upload_media()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/media', ['file' => $file, 'name' => 'Test Image'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Test Image');
    }

    public function test_admin_can_list_media()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/media')
            ->assertOk();
    }

    public function test_admin_can_update_media_metadata()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('update.jpg');
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/media', ['file' => $file, 'name' => 'Original']);
        $mediaId = $response->json('data.id');

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/media/{$mediaId}", ['alt_text' => 'New alt'])
            ->assertOk()
            ->assertJsonPath('data.alt_text', 'New alt');
    }

    public function test_admin_can_delete_media()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('delete.jpg');
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/media', ['file' => $file]);
        $mediaId = $response->json('data.id');

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/media/{$mediaId}")
            ->assertOk();
    }

    public function test_media_requires_file()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/media', [])
            ->assertJsonValidationErrors(['file']);
    }

    // ─── Localization ───────────────────────────────────────────

    public function test_localization_metadata()
    {
        $this->getJson('/api/localization')
            ->assertOk()
            ->assertJsonStructure(['default_locale', 'fallback_locale', 'languages']);
    }

    public function test_translations_for_valid_locale()
    {
        $this->getJson('/api/translations/fr')
            ->assertOk()
            ->assertJsonFragment(['language' => 'Français']);
    }

    public function test_translations_for_invalid_locale()
    {
        $this->getJson('/api/translations/xx')
            ->assertNotFound();
    }

    public function test_admin_can_read_admin_translations()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/translations/fr')
            ->assertOk();
    }

    public function test_non_admin_cannot_read_admin_translations()
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/admin/translations/fr')
            ->assertForbidden();
    }

    // ─── Validation ─────────────────────────────────────────────

    public function test_service_rejects_empty_title()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/services', ['title' => []])
            ->assertJsonValidationErrors(['title.fr']);
    }

    public function test_faq_rejects_empty_question()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/faqs', ['question' => []])
            ->assertJsonValidationErrors(['question.fr']);
    }
}
