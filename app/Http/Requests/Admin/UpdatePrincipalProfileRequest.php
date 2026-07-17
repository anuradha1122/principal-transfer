<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrincipalProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('manage principal profiles') ?? false;
    }

    public function rules(): array
    {
        $profile = $this->route(
            'principal_profile'
        );

        return [
            'nic' => [
                'required',
                'string',
                'max:20',
                Rule::unique(
                    'principal_profiles',
                    'nic'
                )->ignore($profile?->id),
            ],
            'employee_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(
                    'principal_profiles',
                    'employee_number'
                )->ignore($profile?->id),
            ],
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],
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
            'service_category' => [
                'required',
                Rule::in([
                    'Sri Lanka Principals Service',
                    'Sri Lanka Education Administrative Service',
                    'Other',
                ]),
            ],
            'service_grade' => [
                'nullable',
                'string',
                'max:100',
            ],
            'first_appointment_date' => [
                'nullable',
                'date',
            ],
            'principal_service_entry_date' => [
                'nullable',
                'date',
                'after_or_equal:first_appointment_date',
            ],
            'retirement_date' => [
                'nullable',
                'date',
                'after:date_of_birth',
            ],
            'employment_status' => [
                'required',
                Rule::in([
                    'Active',
                    'Retired',
                    'Transferred Out',
                    'Suspended',
                    'Deceased',
                    'Other',
                ]),
            ],
            'qualifications_summary' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'profile_completed' => [
                'required',
                'boolean',
            ],
        ];
    }
}
