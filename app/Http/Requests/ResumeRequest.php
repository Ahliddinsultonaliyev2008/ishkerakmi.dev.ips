<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Http\Requests\Concerns\NormalizesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResumeRequest extends FormRequest
{
    use NormalizesPhone;

    protected function prepareForValidation(): void
    {
        $this->merge(['phone' => $this->normalizePhone($this->input('phone'))]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^\+998\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:-14 years'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'experience' => ['required', Rule::enum(Experience::class)],
            'salary_expected' => ['nullable', 'integer', 'min:0'],
            'currency' => ['sometimes', Rule::enum(Currency::class)],
            'skills' => ['nullable', 'array', 'max:30'],
            'skills.*' => ['string', 'max:50'],
            'about' => ['nullable', 'string', 'max:5000'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }
}
