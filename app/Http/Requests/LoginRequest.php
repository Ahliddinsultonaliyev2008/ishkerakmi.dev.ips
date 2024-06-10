<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|numeric|max_digits:9|min_digits:9',
            'password' => 'required|string|min:8|max:24'
        ];
    }


    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
       throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => $validator ->errors()
       ],422));
    }
}

