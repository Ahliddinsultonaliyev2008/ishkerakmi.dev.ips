<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'resume_id' => ['nullable', Rule::exists('resumes', 'id')->where('user_id', $this->user()->id)],
            'cover_letter' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
