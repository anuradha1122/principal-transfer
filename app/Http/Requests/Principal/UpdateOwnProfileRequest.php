<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOwnProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(
            'Principal'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'name_with_initials' => [
                'nullable',
                'string',
                'max:255',
            ],
            'gender' => [
                'nullable',
                Rule::in([
                    'Male',
                    'Female',
                    'Other',
                ]),
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],
            'mobile_number' => [
                'nullable',
                'string',
                'max:30',
            ],
            'alternate_number' => [
                'nullable',
                'string',
                'max:30',
            ],
            'personal_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'qualifications_summary' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
