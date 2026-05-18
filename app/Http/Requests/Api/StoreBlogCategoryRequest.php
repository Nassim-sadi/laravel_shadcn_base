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
            'name' => ['required'],
            'slug' => ['required', 'string', 'max:255', 'unique:blog_categories,slug', 'alpha_dash'],
            'description' => ['nullable'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
