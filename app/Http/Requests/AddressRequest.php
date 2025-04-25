<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:billing,shipping'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:10'],
            'postal_code' => ['required', 'string', 'max:10'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'is_default' => ['boolean'],
            'additional_info' => ['nullable', 'string'],
        ];
    }
} 