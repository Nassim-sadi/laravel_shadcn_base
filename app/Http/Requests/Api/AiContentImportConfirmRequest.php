<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AiContentImportConfirmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->isAdmin() ?? false)
            && ($this->user()?->hasPermissionTo('ai.import') ?? false);
    }

    public function rules(): array
    {
        return [
            'preview_token' => ['required', 'uuid'],
        ];
    }
}
