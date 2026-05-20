<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->isAdmin() ?? false)
            && ($this->user()?->hasPermissionTo('settings.edit') ?? false);
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:100'],
            'api_key' => ['nullable', 'string', 'max:5000'],
            'model' => ['required', 'string', 'max:255'],
            'base_url' => ['required', 'url', 'max:255'],
            'timeout' => ['required', 'integer', 'min:5', 'max:300'],
        ];
    }
}
