<?php

namespace App\Http\Requests\Api;

use App\Services\AiModuleRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiContentGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->isAdmin() ?? false)
            && ($this->user()?->hasPermissionTo('ai.generate') ?? false);
    }

    public function rules(): array
    {
        $registry = app(AiModuleRegistry::class);
        $module = (string) $this->input('module');
        $allowedFields = $registry->fieldsFor($module);

        return [
            'module' => ['required', 'string', Rule::in($registry->supportedModules())],
            'mode' => ['required', 'string', Rule::in(['draft', 'improve'])],
            'locale' => ['required', 'string', Rule::in(config('localization.supported_codes', ['fr']))],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['required', 'string', Rule::in($allowedFields)],
            'tone' => ['nullable', 'string', 'max:100'],
            'context' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'array'],
            'source.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $registry = app(AiModuleRegistry::class);
            $module = (string) $this->input('module');
            $allowedFields = $registry->fieldsFor($module);
            $fields = $this->input('fields', []);
            $source = $this->input('source', []);

            if (count($fields) !== count(array_unique($fields))) {
                $validator->errors()->add('fields', 'Duplicate fields are not allowed.');
            }

            if (! is_array($source)) {
                return;
            }

            foreach (array_keys($source) as $field) {
                if (! in_array($field, $allowedFields, true)) {
                    $validator->errors()->add('source', "Unsupported source field [{$field}].");
                }
            }
        });
    }
}
