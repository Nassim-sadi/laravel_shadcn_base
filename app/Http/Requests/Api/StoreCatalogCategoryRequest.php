<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'string|max:1000',
            'image_id' => 'nullable|integer|exists:media,id',
            'parent_id' => 'nullable|integer|exists:catalog_categories,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
