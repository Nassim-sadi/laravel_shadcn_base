<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        $settingId = $this->route('setting');

        return [
            'key' => [$required, 'string', 'max:255', Rule::unique('settings', 'key')->ignore($settingId)],
            'group' => [$required, 'string', 'max:50'],
            'name' => [$required, 'string', 'max:255'],
            'value' => ['nullable'],
            'default_value' => ['nullable'],
            'type' => [$required, 'string', 'in:string,integer,boolean,json,array'],
            'description' => ['nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');

            if ($type === 'json' || $type === 'array') {
                $value = $this->input('value');

                if ($value !== null && ! is_array($value)) {
                    $validator->errors()->add('value', 'The value must be an array for json/array type settings.');
                }
            }
        });
    }
}
