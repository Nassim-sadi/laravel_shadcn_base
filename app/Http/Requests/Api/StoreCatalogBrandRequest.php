<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.fr' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'logo_id' => ['nullable', 'exists:media,id'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'is_active' => ['boolean'],
            'order' => ['integer'],
        ];
    }
}
