<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'question' => [$required, 'array'],
            'question.fr' => [$required, 'string', 'max:255'],
            'question.*' => ['nullable', 'string', 'max:255'],
            'answer' => [$required, 'array'],
            'answer.fr' => [$required, 'string'],
            'answer.*' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'array'],
            'seo_title.*' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'array'],
            'seo_description.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowedLocales = config('localization.supported_codes', ['fr']);

            foreach (['question', 'answer', 'seo_title', 'seo_description'] as $field) {
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
