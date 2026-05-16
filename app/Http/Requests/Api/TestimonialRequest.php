<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'array'],
            'name.fr' => [$required, 'string', 'max:255'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'array'],
            'position.*' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'array'],
            'company.*' => ['nullable', 'string', 'max:255'],
            'content' => [$required, 'array'],
            'content.fr' => [$required, 'string'],
            'content.*' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'image_id' => ['nullable', 'integer', 'exists:media,id'],
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer', 'min:0'],
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

            foreach (['name', 'position', 'company', 'content', 'seo_title', 'seo_description'] as $field) {
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
