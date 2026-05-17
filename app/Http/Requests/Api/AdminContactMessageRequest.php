<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AdminContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'subject' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string'],
            'is_read' => ['sometimes', 'boolean'],
            'reply' => ['sometimes', 'string'],
            'replied_at' => ['sometimes', 'date'],
        ];
    }
}
