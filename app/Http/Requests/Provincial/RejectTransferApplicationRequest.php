<?php

namespace App\Http\Requests\Provincial;

use Illuminate\Foundation\Http\FormRequest;

class RejectTransferApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'reject provincial transfer applications'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [
            'recommendation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'rejection_reason' => [
                'required',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'A rejection reason is required.',
        ];
    }
}
