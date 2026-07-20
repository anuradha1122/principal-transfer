<?php

namespace App\Http\Requests\TransferBoard;

use Illuminate\Foundation\Http\FormRequest;

class RejectTransferApplicationRequest extends FormRequest
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
            'decision_reference' => [
                'required',
                'string',
                'max:150',
            ],

            'rejection_reason' => [
                'required',
                'string',
                'max:5000',
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
            'decision_reference.required' =>
                'The official decision reference is required.',

            'rejection_reason.required' =>
                'A final rejection reason is required.',
        ];
    }
}
