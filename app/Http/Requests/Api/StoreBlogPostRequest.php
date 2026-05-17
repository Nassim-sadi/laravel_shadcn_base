<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_posts,slug', 'alpha_dash'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'featured' => ['sometimes', 'boolean'],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:blog_tags,id'],
            'image_id' => ['nullable', 'exists:media,id'],
        ];
    }
}
