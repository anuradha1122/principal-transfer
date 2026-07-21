<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTransferAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('submit transfer appeals') ?? false;
    }

    public function rules(): array
    {
        return [
            'declaration' => [
                'required',
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'declaration.accepted' => 'You must accept the declaration before submitting the appeal.',
        ];
    }
}
