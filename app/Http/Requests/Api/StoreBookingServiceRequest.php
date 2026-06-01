<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.fr' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
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
