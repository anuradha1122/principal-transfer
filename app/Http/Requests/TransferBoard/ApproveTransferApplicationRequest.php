<?php

namespace App\Http\Requests\TransferBoard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveTransferApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'record transfer board decisions'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [
            'recommended_school_id' => [
                'required',
                'integer',
                Rule::exists(
                    'schools',
                    'id'
                )->where(
                    'is_active',
                    true
                ),
            ],

            'effective_date' => [
                'required',
                'date',
            ],

            'appointment_type' => [
                'required',
                'string',
                Rule::in([
                    'Permanent',
                    'Acting',
                    'Temporary',
                    'Attached',
                ]),
            ],

            'decision_reference' => [
                'required',
                'string',
                'max:150',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recommended_school_id.required' => 'The approved transfer school is required.',

            'effective_date.required' => 'The transfer effective date is required.',

            'appointment_type.required' => 'The appointment type is required.',

            'decision_reference.required' => 'The official decision reference is required.',
        ];
    }
}
