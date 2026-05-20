<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiContentImportAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $role = Role::where('name', 'super_admin')->first();
        if ($role) {
            $this->admin->assignRole($role);
        }
    }

    public function test_admin_can_store_ai_settings_with_encrypted_api_key(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/ai/settings', [
                'provider' => 'openai',
                'api_key' => 'secret-key-1234',
                'model' => 'gpt-4.1-mini',
                'base_url' => 'https://api.openai.com/v1',
                'timeout' => 30,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.provider', 'openai')
            ->assertJsonPath('data.has_api_key', true);

        $stored = Setting::query()->where('key', 'ai_api_key')->firstOrFail();

        $this->assertNotSame('secret-key-1234', $stored->getRawOriginal('value'));
        $this->assertSame('secret-key-1234', Setting::get('ai_api_key'));
    }

    public function test_admin_can_preview_and_confirm_service_json_import(): void
    {
        $file = UploadedFile::fake()->createWithContent('services.json', json_encode([
            [
                'title' => [
                    'fr' => 'Service premium',
                    'en' => 'Premium service',
                ],
                'description' => [
                    'fr' => 'Description courte',
                    'en' => 'Short description',
                ],
                'icon' => 'briefcase',
                'is_active' => true,
            ],
        ], JSON_THROW_ON_ERROR));

        $preview = $this->actingAs($this->admin, 'sanctum')
            ->post('/api/ai/import-content/preview', [
                'module' => 'services',
                'file' => $file,
            ]);

        $preview
            ->assertOk()
            ->assertJsonPath('data.valid', true)
            ->assertJsonPath('data.item_count', 1);

        $token = $preview->json('data.preview_token');

        $confirm = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/ai/import-content/confirm', [
                'preview_token' => $token,
            ]);

        $confirm
            ->assertOk()
            ->assertJsonPath('data.created_count', 1);

        $this->assertDatabaseCount('services', 1);
        $service = Service::query()->firstOrFail();
        $this->assertSame('Service premium', $service->title['fr']);
    }

    public function test_import_preview_rejects_invalid_json(): void
    {
        $file = UploadedFile::fake()->createWithContent('invalid.json', '{not-valid-json');

        $response = $this->actingAs($this->admin, 'sanctum')
            ->post('/api/ai/import-content/preview', [
                'module' => 'services',
                'file' => $file,
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'The uploaded file does not contain valid JSON.');
    }

    public function test_forbids_import_without_permission(): void
    {
        $restrictedAdmin = User::factory()->create(['role' => 'user']);
        $restrictedAdmin->assignRole('user');

        $file = UploadedFile::fake()->createWithContent('services.json', json_encode([], JSON_THROW_ON_ERROR));

        $response = $this->actingAs($restrictedAdmin, 'sanctum')
            ->post('/api/ai/import-content/preview', [
                'module' => 'services',
                'file' => $file,
            ]);

        $response->assertForbidden();
    }
}
