<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $this->admin->assignRole($superAdminRole);
        }
    }

    // ─── Blog Categories ─────────────────────────────────────

    public function test_admin_can_create_blog_category()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-categories', [
                'name' => ['fr' => 'Actualités', 'en' => 'News'],
                'slug' => 'news',
                'description' => ['fr' => 'Articles actualités', 'en' => 'News articles'],
                'is_published' => true,
            ])->assertCreated()
            ->assertJsonPath('data.slug', 'news');
    }

    public function test_blog_category_requires_name_and_slug()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-categories', [])
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function test_blog_category_slug_must_be_unique()
    {
        BlogCategory::factory()->create(['slug' => 'news']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-categories', [
                'name' => ['fr' => 'Autre'],
                'slug' => 'news',
            ])->assertJsonValidationErrors(['slug']);
    }

    public function test_admin_can_list_blog_categories()
    {
        BlogCategory::factory()->create(['slug' => 'cat-1']);
        BlogCategory::factory()->create(['slug' => 'cat-2']);
        BlogCategory::factory()->create(['slug' => 'cat-3']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/blog-categories')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_update_blog_category()
    {
        $category = BlogCategory::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/blog-categories/{$category->id}", [
                'name' => ['fr' => 'Mis à jour'],
            ])->assertOk()
            ->assertJsonPath('data.id', $category->id);
    }

    public function test_admin_can_delete_blog_category()
    {
        $category = BlogCategory::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/blog-categories/{$category->id}")
            ->assertOk();
    }

    // ─── Blog Tags ───────────────────────────────────────────

    public function test_admin_can_create_blog_tag()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-tags', [
                'name' => 'Laravel',
                'slug' => 'laravel',
            ])->assertCreated()
            ->assertJsonPath('data.name', 'Laravel');
    }

    public function test_blog_tag_requires_name_and_slug()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-tags', [])
            ->assertJsonValidationErrors(['name', 'slug']);
    }

    public function test_blog_tag_slug_must_be_unique()
    {
        BlogTag::factory()->create(['slug' => 'laravel']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-tags', [
                'name' => 'Laravel Framework',
                'slug' => 'laravel',
            ])->assertJsonValidationErrors(['slug']);
    }

    public function test_admin_can_list_blog_tags()
    {
        BlogTag::factory()->create(['slug' => 'tag-one']);
        BlogTag::factory()->create(['slug' => 'tag-two']);
        BlogTag::factory()->create(['slug' => 'tag-three']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/blog-tags')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_delete_blog_tag()
    {
        $tag = BlogTag::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/blog-tags/{$tag->id}")
            ->assertOk();
    }

    // ─── Blog Posts ──────────────────────────────────────────

    public function test_admin_can_create_blog_post()
    {
        $category = BlogCategory::factory()->create();
        $tags = BlogTag::factory()->count(2)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-posts', [
                'title' => ['fr' => 'Mon article', 'en' => 'My post'],
                'slug' => 'my-post',
                'excerpt' => ['fr' => 'Résumé', 'en' => 'Summary'],
                'body' => ['fr' => '<p>Contenu</p>', 'en' => '<p>Content</p>'],
                'category_id' => $category->id,
                'tag_ids' => $tags->pluck('id')->toArray(),
                'is_published' => true,
                'featured' => false,
            ])->assertCreated()
            ->assertJsonPath('data.slug', 'my-post');
    }

    public function test_blog_post_requires_title_and_slug()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-posts', [])
            ->assertJsonValidationErrors(['title', 'slug']);
    }

    public function test_blog_post_slug_must_be_unique()
    {
        BlogPost::factory()->create(['slug' => 'existing-post']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-posts', [
                'title' => ['fr' => 'Nouveau'],
                'slug' => 'existing-post',
            ])->assertJsonValidationErrors(['slug']);
    }

    public function test_admin_can_list_blog_posts()
    {
        BlogPost::factory()->create(['title' => ['fr' => 'Post 1'], 'slug' => 'post-1']);
        BlogPost::factory()->create(['title' => ['fr' => 'Post 2'], 'slug' => 'post-2']);
        BlogPost::factory()->create(['title' => ['fr' => 'Post 3'], 'slug' => 'post-3']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/blog-posts')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_show_blog_post()
    {
        $post = BlogPost::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/blog-posts/{$post->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_admin_can_update_blog_post()
    {
        $post = BlogPost::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/blog-posts/{$post->id}", [
                'title' => ['fr' => 'Mis à jour'],
            ])->assertOk()
            ->assertJsonPath('data.id', $post->id);
    }

    public function test_admin_can_delete_blog_post()
    {
        $post = BlogPost::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/blog-posts/{$post->id}")
            ->assertOk();
    }

    public function test_unauthenticated_cannot_access_blog_endpoints()
    {
        $this->getJson('/api/blog-posts')->assertUnauthorized();
        $this->getJson('/api/blog-categories')->assertUnauthorized();
        $this->getJson('/api/blog-tags')->assertUnauthorized();
    }

    public function test_blog_post_can_have_tags_and_category()
    {
        $category = BlogCategory::factory()->create();
        $tags = BlogTag::factory()->count(2)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-posts', [
                'title' => ['fr' => 'Article complet'],
                'slug' => 'full-post',
                'body' => ['fr' => '<p>Contenu</p>'],
                'category_id' => $category->id,
                'tag_ids' => $tags->pluck('id')->toArray(),
                'is_published' => true,
            ]);

        $response->assertCreated();
        $postId = $response->json('data.id');

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/blog-posts/{$postId}")
            ->assertOk()
            ->assertJsonPath('data.category_id', $category->id);
    }

    public function test_blog_post_requires_alpha_dash_slug()
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/blog-posts', [
                'title' => ['fr' => 'Test'],
                'slug' => 'invalid slug with spaces',
            ])->assertJsonValidationErrors(['slug']);
    }
}
