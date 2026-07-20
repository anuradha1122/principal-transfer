<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOwnAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->hasRole('Principal')
            ?? false;
    }

    public function rules(): array
    {
        return [
            'school_id' => [
                'required',
                Rule::exists(
                    'schools',
                    'id'
                )->where(
                    'is_active',
                    true
                ),
            ],

            'designation' => [
                'required',
                'string',
                'max:150',
            ],

            'appointment_type' => [
                'required',
                Rule::in([
                    'Permanent',
                    'Acting',
                    'Covering',
                    'Temporary',
                    'Other',
                ]),
            ],

            'appointment_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'appointment_date' => [
                'nullable',
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

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
