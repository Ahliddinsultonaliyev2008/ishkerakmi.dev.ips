<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => 'required|string|min:6|max:24',
            'admin' => 'required|string|max:25|min_digits:4'
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

