<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AiModuleRegistry
{
    public const MODULE_SERVICES = 'services';
    public const MODULE_PROJECTS = 'projects';
    public const MODULE_FAQS = 'faqs';
    public const MODULE_TESTIMONIALS = 'testimonials';
    public const MODULE_BLOG_POSTS = 'blog_posts';

    public function supportedModules(): array
    {
        return array_keys($this->definitions());
    }

    public function fieldsFor(string $module): array
    {
        return $this->definitions()[$module]['generator_fields'] ?? [];
    }

    public function translatedFieldsFor(string $module): array
    {
        return $this->definitions()[$module]['translated_fields'] ?? [];
    }

    public function importRulesFor(string $module): array
    {
        $supportedLocales = $this->supportedLocales();

        return match ($module) {
            self::MODULE_SERVICES => [
                'title' => ['required', 'array'],
                'title.fr' => ['required', 'string', 'max:255'],
                'title.*' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'array'],
                'description.*' => ['nullable', 'string'],
                'icon' => ['nullable', 'string', 'max:255'],
                'image_id' => ['nullable', 'integer', 'exists:media,id'],
                'url' => ['nullable', 'url', 'max:255'],
                'order' => ['sometimes', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
                'seo_title' => ['nullable', 'array'],
                'seo_title.*' => ['nullable', 'string', 'max:255'],
                'seo_description' => ['nullable', 'array'],
                'seo_description.*' => ['nullable', 'string'],
                'seo_keywords' => ['nullable', 'array'],
                'seo_keywords.*' => ['nullable', 'string'],
            ],
            self::MODULE_PROJECTS => [
                'title' => ['required', 'array'],
                'title.fr' => ['required', 'string', 'max:255'],
                'title.*' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'array'],
                'description.*' => ['nullable', 'string'],
                'client' => ['nullable', 'array'],
                'client.*' => ['nullable', 'string', 'max:255'],
                'image_id' => ['nullable', 'integer', 'exists:media,id'],
                'url' => ['nullable', 'url', 'max:255'],
                'technologies' => ['sometimes', 'array'],
                'technologies.*' => ['string'],
                'order' => ['sometimes', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
                'seo_title' => ['nullable', 'array'],
                'seo_title.*' => ['nullable', 'string', 'max:255'],
                'seo_description' => ['nullable', 'array'],
                'seo_description.*' => ['nullable', 'string'],
                'seo_keywords' => ['nullable', 'array'],
                'seo_keywords.*' => ['nullable', 'string'],
            ],
            self::MODULE_FAQS => [
                'question' => ['required', 'array'],
                'question.fr' => ['required', 'string', 'max:255'],
                'question.*' => ['nullable', 'string', 'max:255'],
                'answer' => ['required', 'array'],
                'answer.fr' => ['required', 'string'],
                'answer.*' => ['nullable', 'string'],
                'category' => ['nullable', 'string', 'max:255'],
                'order' => ['sometimes', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
                'seo_title' => ['nullable', 'array'],
                'seo_title.*' => ['nullable', 'string', 'max:255'],
                'seo_description' => ['nullable', 'array'],
                'seo_description.*' => ['nullable', 'string'],
            ],
            self::MODULE_TESTIMONIALS => [
                'name' => ['required', 'array'],
                'name.fr' => ['required', 'string', 'max:255'],
                'name.*' => ['nullable', 'string', 'max:255'],
                'position' => ['nullable', 'array'],
                'position.*' => ['nullable', 'string', 'max:255'],
                'company' => ['nullable', 'array'],
                'company.*' => ['nullable', 'string', 'max:255'],
                'content' => ['required', 'array'],
                'content.fr' => ['required', 'string'],
                'content.*' => ['nullable', 'string'],
                'image_id' => ['nullable', 'integer', 'exists:media,id'],
                'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
                'is_active' => ['sometimes', 'boolean'],
                'order' => ['sometimes', 'integer', 'min:0'],
                'seo_title' => ['nullable', 'array'],
                'seo_title.*' => ['nullable', 'string', 'max:255'],
                'seo_description' => ['nullable', 'array'],
                'seo_description.*' => ['nullable', 'string'],
            ],
            self::MODULE_BLOG_POSTS => [
                'title' => ['required', 'array'],
                'title.fr' => ['required', 'string', 'max:255'],
                'title.*' => ['nullable', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')],
                'excerpt' => ['nullable', 'array'],
                'excerpt.*' => ['nullable', 'string'],
                'body' => ['nullable', 'array'],
                'body.*' => ['nullable', 'string'],
                'is_published' => ['sometimes', 'boolean'],
                'featured' => ['sometimes', 'boolean'],
                'category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
                'tag_ids' => ['nullable', 'array'],
                'tag_ids.*' => ['integer', 'exists:blog_tags,id'],
                'image_id' => ['nullable', 'integer', 'exists:media,id'],
            ],
            default => [],
        };
    }

    public function validateImportItem(string $module, array $item): array
    {
        $validator = Validator::make($item, $this->importRulesFor($module));

        $validator->after(function ($validator) use ($module, $item) {
            $allowedLocales = $this->supportedLocales();

            foreach ($this->translatedFieldsFor($module) as $field) {
                $translations = $item[$field] ?? null;

                if ($translations === null || ! is_array($translations)) {
                    continue;
                }

                foreach (array_keys($translations) as $locale) {
                    if (! in_array($locale, $allowedLocales, true)) {
                        $validator->errors()->add($field, "The {$field} field contains an unsupported locale [{$locale}].");
                    }
                }
            }
        });

        if ($validator->fails()) {
            return [
                'valid' => false,
                'validated' => [],
                'errors' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valid' => true,
            'validated' => $validator->validated(),
            'errors' => [],
        ];
    }

    public function createRecord(string $module, array $payload, User $user): mixed
    {
        return DB::transaction(function () use ($module, $payload, $user) {
            return match ($module) {
                self::MODULE_SERVICES => Service::create($payload),
                self::MODULE_PROJECTS => Project::create($payload),
                self::MODULE_FAQS => Faq::create($payload),
                self::MODULE_TESTIMONIALS => Testimonial::create($payload),
                self::MODULE_BLOG_POSTS => $this->createBlogPost($payload, $user),
                default => null,
            };
        });
    }

    public function labelFor(string $module): string
    {
        return $this->definitions()[$module]['label'] ?? $module;
    }

    private function createBlogPost(array $payload, User $user): BlogPost
    {
        $postPayload = $payload;
        $body = $postPayload['body'] ?? null;
        $tagIds = $postPayload['tag_ids'] ?? [];

        unset($postPayload['body'], $postPayload['tag_ids']);

        $postPayload['user_id'] = $user->id;

        $post = BlogPost::create($postPayload);

        if (is_array($body)) {
            $post->body()->create(['body' => $body]);
        }

        if (is_array($tagIds) && $tagIds !== []) {
            $post->tags()->sync($tagIds);
        }

        return $post->load(['category', 'author', 'tags', 'body']);
    }

    private function supportedLocales(): array
    {
        return config('localization.supported_codes', ['fr']);
    }

    private function definitions(): array
    {
        return [
            self::MODULE_SERVICES => [
                'label' => 'Services',
                'generator_fields' => ['title', 'description', 'seo_title', 'seo_description', 'seo_keywords'],
                'translated_fields' => ['title', 'description', 'seo_title', 'seo_description', 'seo_keywords'],
            ],
            self::MODULE_PROJECTS => [
                'label' => 'Projects',
                'generator_fields' => ['title', 'description', 'client', 'seo_title', 'seo_description', 'seo_keywords'],
                'translated_fields' => ['title', 'description', 'client', 'seo_title', 'seo_description', 'seo_keywords'],
            ],
            self::MODULE_FAQS => [
                'label' => 'FAQs',
                'generator_fields' => ['question', 'answer', 'seo_title', 'seo_description'],
                'translated_fields' => ['question', 'answer', 'seo_title', 'seo_description'],
            ],
            self::MODULE_TESTIMONIALS => [
                'label' => 'Testimonials',
                'generator_fields' => ['name', 'position', 'company', 'content', 'seo_title', 'seo_description'],
                'translated_fields' => ['name', 'position', 'company', 'content', 'seo_title', 'seo_description'],
            ],
            self::MODULE_BLOG_POSTS => [
                'label' => 'Blog posts',
                'generator_fields' => ['title', 'excerpt', 'body'],
                'translated_fields' => ['title', 'excerpt', 'body'],
            ],
        ];
    }
}
