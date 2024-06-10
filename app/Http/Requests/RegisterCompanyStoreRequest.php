<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
class RegisterCompanyStoreRequest extends FormRequest

{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30|min:2',
            'image' => 'required|string',
            'email' => 'required|string|max:50|min:11',
            'password' => 'required|string|confirmed|min:8|max:24',
            'profession' => 'required|string|max:30|min:2',
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

