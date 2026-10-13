<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($this->user())],
            'city' => ['nullable', 'string', 'max:100'],
            'current_password' => ['required_with:password', 'current_password:sanctum'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }
}
