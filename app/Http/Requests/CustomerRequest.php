<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'is_active' => ['boolean'],
        ];

        if ($this->isMethod('POST')) {
            $rules['email'][] = Rule::unique('customers');
        } else {
            $rules['email'][] = Rule::unique('customers')->ignore($this->route('id'));
        }

        return $rules;
    }
} 