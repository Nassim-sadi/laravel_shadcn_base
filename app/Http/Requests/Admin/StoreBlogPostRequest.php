<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Replace with actual authorization logic (Policy/Middleware)
        return true; 
    }

    /**
     * Get the validation rules that apply to the given request.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Assume user_id and category_id are handled by Auth::id() or other means
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:blog_posts,slug', 'alpha_dash'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'is_published' => ['sometimes', 'boolean'],
            'featured' => ['sometimes', 'boolean'],
            // The body content is passed separately or handled by a nested request if we were fully optimizing, 
            // but for simplicity in the controller structure, we assume it's validated on post-save/update.
        ];
    }
}