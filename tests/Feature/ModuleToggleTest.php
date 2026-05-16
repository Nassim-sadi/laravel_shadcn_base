<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Service;
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

    public function test_disabled_public_module_routes_are_not_registered(): void
    {
        $this->get('/services')->assertNotFound();
        $this->get('/contact')->assertNotFound();

        $this->get('/projects')->assertOk();
    }

    public function test_disabled_api_module_routes_are_not_registered(): void
    {
        $this->getJson('/api/services')->assertNotFound();
        $this->postJson('/api/contact-messages', [])->assertNotFound();
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

    public function test_admin_app_exposes_only_enabled_modules(): void
    {
        $response = $this->get('/admin');

        $response->assertOk();
        $response->assertSee('"projects"', false);
        $response->assertDontSee('"services"', false);
        $response->assertDontSee('"contact"', false);
    }

    private function setModuleEnv(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
