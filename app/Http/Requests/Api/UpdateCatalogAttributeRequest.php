<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|array',
            'name.*' => 'string|max:255',
            'type' => 'sometimes|string|in:select,text,boolean',
            'values' => 'nullable|array',
            'values.*' => 'required|array',
            'values.*.*' => 'string|max:255',
        ];
    }
}
