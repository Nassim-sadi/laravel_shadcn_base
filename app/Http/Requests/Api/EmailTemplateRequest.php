<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        $templateId = $this->route('email_template');

        return [
            'key' => [$required, 'string', 'max:255', Rule::unique('email_templates', 'key')->ignore($templateId)],
            'name' => [$required, 'array'],
            'name.fr' => [$required, 'string', 'max:255'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'subject' => [$required, 'array'],
            'subject.fr' => [$required, 'string'],
            'subject.*' => ['nullable', 'string'],
            'body' => [$required, 'array'],
            'body.fr' => [$required, 'string'],
            'body.*' => ['nullable', 'string'],
            'variables' => ['sometimes', 'array'],
            'variables.*' => ['string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowedLocales = config('localization.supported_codes', ['fr']);

            foreach (['name', 'subject', 'body'] as $field) {
                $translations = $this->input($field);

                if (! is_array($translations)) {
                    continue;
                }

                foreach (array_keys($translations) as $locale) {
                    if (! in_array($locale, $allowedLocales, true)) {
                        $validator->errors()->add($field, "The {$field} field contains an unsupported locale.");
                    }
                }
            }
        });
    }
}
