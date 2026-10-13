<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return ['status' => ['required', Rule::enum(ApplicationStatus::class)]];
    }
}
