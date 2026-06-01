<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'array'],
            'name.fr' => ['sometimes', 'string', 'max:255'],
            'name.en' => ['sometimes', 'string', 'max:255'],
            'name.ar' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'duration_minutes' => ['sometimes', 'integer', 'min:15'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'order' => ['integer'],
            'availability_rules' => ['nullable', 'array'],
            'availability_rules.*.day_of_week' => ['required_with:availability_rules', 'integer', 'between:0,6'],
            'availability_rules.*.start_time' => ['required_with:availability_rules', 'date_format:H:i'],
            'availability_rules.*.end_time' => ['required_with:availability_rules', 'date_format:H:i', 'after:availability_rules.*.start_time'],
        ];
    }
}
