<?php

namespace App\Http\Requests\Provincial;

use Illuminate\Foundation\Http\FormRequest;

class ReturnTransferApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can(
                'return provincial transfer applications'
            )
            ?? false;
    }

    public function rules(): array
    {
        return [
            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'return_reason' => [
                'required',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'return_reason.required' =>
                'A return reason is required.',
        ];
    }
}
