<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapRobotsTest extends TestCase
{
    use RefreshDatabase;
    public function test_robots_txt_returns_plain_text()
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('Sitemap:');
        $response->assertSee('User-agent: *');
    }

    public function test_robots_txt_disallows_in_non_production()
    {
        $response = $this->get('/robots.txt');

        $response->assertSee('Disallow: /');
    }

    public function test_sitemap_xml_returns_xml()
    {
        $service = Service::factory()->create(['is_active' => true]);
        $project = Project::factory()->create(['is_active' => true]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', false);
        $response->assertSee(route('home'), false);
        $response->assertSee(route('public.services.index'), false);
        $response->assertSee(route('public.projects.index'), false);
        $response->assertSee(route('public.about'), false);
        $response->assertSee(route('public.contact'), false);
        $response->assertSee(route('public.services.show', $service), false);
        $response->assertSee(route('public.projects.show', $project), false);
    }

    public function test_sitemap_excludes_inactive_services()
    {
        $service = Service::factory()->create(['is_active' => false]);

        $response = $this->get('/sitemap.xml');

        $response->assertDontSee(route('public.services.show', $service));
    }

    public function test_sitemap_excludes_inactive_projects()
    {
        $project = Project::factory()->create(['is_active' => false]);

        $response = $this->get('/sitemap.xml');

        $response->assertDontSee(route('public.projects.show', $project));
    }

    public function test_sitemap_includes_lastmod_for_home()
    {
        $response = $this->get('/sitemap.xml');

        $response->assertSee('<lastmod>', false);
    }
}
