<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiContentGenerationTest extends TestCase
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

    public function test_admin_can_generate_service_content(): void
    {
        config()->set('services.ai.provider', 'openai');
        config()->set('services.ai.api_key', 'test-key');
        config()->set('services.ai.base_url', 'https://api.openai.com/v1');
        config()->set('services.ai.model', 'gpt-4.1-mini');

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output' => [[
                    'content' => [[
                        'text' => json_encode([
                            'fields' => [
                                'title' => 'Service title',
                                'description' => 'Service description',
                            ],
                        ], JSON_THROW_ON_ERROR),
                    ]],
                ]],
                'usage' => [
                    'input_tokens' => 10,
                    'output_tokens' => 20,
                    'total_tokens' => 30,
                ],
            ]),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/ai/generate-content', [
                'module' => 'services',
                'mode' => 'draft',
                'locale' => 'fr',
                'fields' => ['title', 'description'],
                'context' => 'A premium consulting service for manufacturers.',
                'source' => [
                    'title' => 'Old title',
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.fields.title', 'Service title')
            ->assertJsonPath('data.fields.description', 'Service description')
            ->assertJsonPath('data.usage.total_tokens', 30);
    }

    public function test_validation_rejects_unsupported_field(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/ai/generate-content', [
                'module' => 'services',
                'mode' => 'draft',
                'locale' => 'fr',
                'fields' => ['body'],
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['fields.0']);
    }

    public function test_returns_safe_error_when_ai_is_not_configured(): void
    {
        config()->set('services.ai.provider', 'openai');
        config()->set('services.ai.api_key', '');

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/ai/generate-content', [
                'module' => 'services',
                'mode' => 'draft',
                'locale' => 'fr',
                'fields' => ['title'],
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'AI generation is not configured yet. Add an API key in AI settings or AI_API_KEY.');
    }

    public function test_forbids_admin_without_ai_permission(): void
    {
        $adminWithoutAiPermission = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($adminWithoutAiPermission, 'sanctum')
            ->postJson('/api/ai/generate-content', [
                'module' => 'services',
                'mode' => 'draft',
                'locale' => 'fr',
                'fields' => ['title'],
            ]);

        $response->assertForbidden();
    }
}
