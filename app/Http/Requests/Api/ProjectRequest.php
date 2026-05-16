<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'array'],
            'title.fr' => [$required, 'string', 'max:255'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'client' => ['nullable', 'array'],
            'client.*' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'image_id' => ['nullable', 'integer', 'exists:media,id'],
            'url' => ['nullable', 'url', 'max:255'],
            'technologies' => ['sometimes', 'array'],
            'technologies.*' => ['string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'array'],
            'seo_title.*' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'array'],
            'seo_description.*' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'array'],
            'seo_keywords.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allowedLocales = config('localization.supported_codes', ['fr']);

            foreach (['title', 'description', 'client', 'seo_title', 'seo_description', 'seo_keywords'] as $field) {
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
