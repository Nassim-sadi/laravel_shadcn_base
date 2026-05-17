<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $this->isMethod('post')
                ? ['required', 'string', 'min:8']
                : ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', 'string', Rule::in(['super_admin', 'admin', 'user', 'guest'])],
            'locale' => ['sometimes', 'string', Rule::in(config('localization.supported_codes', ['fr']))],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
