<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogPost = $this->route('blogPost');

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($blogPost), 'alpha_dash'],
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
