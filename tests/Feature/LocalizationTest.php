<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'localization.default_locale' => 'fr',
            'localization.fallback_locale' => 'fr',
        ]);
    }

    public function test_localization_metadata_returns_configured_languages(): void
    {
        $this->getJson('/api/localization')
            ->assertOk()
            ->assertJsonPath('default_locale', 'fr')
            ->assertJsonPath('fallback_locale', 'fr')
            ->assertJsonPath('languages.0.code', 'fr')
            ->assertJsonPath('languages.1.code', 'en')
            ->assertJsonPath('languages.2.code', 'ar');
    }

    public function test_public_translation_endpoint_returns_locale_json(): void
    {
        $this->getJson('/api/translations/fr')
            ->assertOk()
            ->assertJsonFragment([
                'admin.dashboard' => 'Tableau de bord',
            ]);
    }

    public function test_unknown_translation_locale_is_rejected(): void
    {
        $this->getJson('/api/translations/es')
            ->assertNotFound();
    }

    public function test_non_admin_cannot_read_admin_translation_editor_data(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/translations/fr')
            ->assertForbidden();
    }

    public function test_admin_can_read_admin_translation_editor_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/translations/fr')
            ->assertOk()
            ->assertJsonFragment([
                'admin.dashboard' => 'Tableau de bord',
            ]);
    }
}
