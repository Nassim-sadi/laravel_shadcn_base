<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class ModuleToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->setModuleEnv('MODULE_SERVICES', 'false');
        $this->setModuleEnv('MODULE_CONTACT', 'false');
        $this->setModuleEnv('MODULE_PROJECTS', 'true');

        parent::setUp();
    }

    public function test_disabled_public_module_routes_return_404(): void
    {
        $this->get('/services')->assertNotFound();
        $this->get('/contact')->assertNotFound();

        $this->get('/projects')->assertOk();
    }

    public function test_disabled_api_module_routes_return_403(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $this->getJson('/api/services')->assertForbidden();
        $this->postJson('/api/contact-messages', [])->assertForbidden();
    }

    public function test_sitemap_excludes_disabled_module_urls(): void
    {
        Service::factory()->create(['is_active' => true]);
        $project = Project::factory()->create(['is_active' => true]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertDontSee('/services', false);
        $response->assertDontSee('/contact', false);
        $response->assertSee(route('public.projects.index'), false);
        $response->assertSee(route('public.projects.show', $project), false);
    }

    public function test_admin_app_exposes_module_snapshot_with_enabled_status(): void
    {
        $response = $this->get('/admin');

        $response->assertOk();
        $response->assertSee('"name":"projects"', false);
        $response->assertSee('"enabled":true', false);
        $response->assertSee('"name":"services"', false);
        $response->assertSee('"enabled":false', false);
        $response->assertSee('"name":"contact"', false);
        $response->assertSee('"enabled":false', false);
    }

    private function setModuleEnv(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
