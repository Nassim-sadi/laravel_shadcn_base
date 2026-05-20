<?php

namespace App\Http\Requests\Api;

use App\Services\AiModuleRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiContentImportPreviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->isAdmin() ?? false)
            && ($this->user()?->hasPermissionTo('ai.import') ?? false);
    }

    public function rules(): array
    {
        return [
            'module' => ['required', 'string', Rule::in(app(AiModuleRegistry::class)->supportedModules())],
            'file' => ['required', 'file', 'max:2048', 'mimes:json,txt'],
        ];
    }
}
