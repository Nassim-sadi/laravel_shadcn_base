<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_categories,slug', 'alpha_dash'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
