<?php

namespace App\Http\Requests\TransferBoard;

use Illuminate\Foundation\Http\FormRequest;

class ReturnTransferAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('return transfer appeals') ?? false;
    }

    public function rules(): array
    {
        return [
            'clarification_request' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
        ];
    }
}
