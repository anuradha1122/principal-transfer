<?php

namespace App\Http\Requests\TransferBoard;

use Illuminate\Foundation\Http\FormRequest;

class WaitlistTransferApplicationRequest extends FormRequest
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

            'waitlist_reason' => [
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

            'waitlist_reason.required' =>
                'A waitlist reason is required.',
        ];
    }
}
