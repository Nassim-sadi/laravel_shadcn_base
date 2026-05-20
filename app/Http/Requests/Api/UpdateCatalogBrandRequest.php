<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'array'],
            'name.fr' => ['sometimes', 'string', 'max:255'],
            'name.en' => ['sometimes', 'string', 'max:255'],
            'name.ar' => ['sometimes', 'string', 'max:255'],
            'logo_id' => ['nullable', 'exists:media,id'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'is_active' => ['boolean'],
            'order' => ['integer'],
        ];
    }
}
