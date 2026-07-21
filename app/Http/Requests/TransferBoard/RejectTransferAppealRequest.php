<?php

namespace App\Http\Requests\TransferBoard;

use Illuminate\Foundation\Http\FormRequest;

class RejectTransferAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reject transfer appeals') ?? false;
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
            'decision_remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
