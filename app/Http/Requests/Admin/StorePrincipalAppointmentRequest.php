<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrincipalAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('manage principal profiles') ?? false;
    }

    public function rules(): array
    {
        return [
            'school_id' => [
                'required',
                Rule::exists('schools', 'id'),
            ],
            'designation' => [
                'required',
                Rule::in([
                    'Principal',
                    'Deputy Principal',
                    'Assistant Principal',
                ]),
            ],
            'appointment_type' => [
                'required',
                Rule::in([
                    'Permanent',
                    'Acting',
                    'Temporary',
                    'Attached',
                ]),
            ],
            'appointment_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'appointment_date' => [
                'required',
                'date',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'is_current' => [
                'required',
                'boolean',
            ],
            'reason_for_end' => [
                'nullable',
                'string',
                'max:255',
            ],
            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
