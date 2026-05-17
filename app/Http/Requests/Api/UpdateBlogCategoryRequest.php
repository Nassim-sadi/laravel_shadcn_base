<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('blogCategory');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('blog_categories', 'slug')->ignore($category), 'alpha_dash'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
