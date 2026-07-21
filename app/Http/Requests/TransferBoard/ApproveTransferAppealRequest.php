<?php

namespace App\Http\Requests\TransferBoard;

use Illuminate\Foundation\Http\FormRequest;

class ApproveTransferAppealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('approve transfer appeals') ?? false;
    }

    public function rules(): array
    {
        return [
            'decision_remarks' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
            'revised_school_id' => [
                'nullable',
                'integer',
                'exists:schools,id',
            ],
            'revised_effective_date' => [
                'nullable',
                'date',
            ],
            'revised_appointment_type' => [
                'nullable',
                'string',
                'max:100',
            ],
            'revised_decision_reference' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }
}
