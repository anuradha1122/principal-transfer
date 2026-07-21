<?php

namespace App\Http\Requests\Provincial;

use Illuminate\Foundation\Http\FormRequest;

class ApproveTransferApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'approve provincial transfer applications'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [
            'recommendation' => [
                'required',
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

    public function messages(): array
    {
        return [
            'recommendation.required' => 'A Provincial recommendation is required.',
        ];
    }
}
