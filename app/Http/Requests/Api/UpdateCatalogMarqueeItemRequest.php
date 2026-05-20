<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogMarqueeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image_id' => 'nullable|integer|exists:media,id',
            'text' => 'nullable|array',
            'text.*' => 'string|max:255',
            'position' => 'sometimes|integer|min:1|max:5',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
