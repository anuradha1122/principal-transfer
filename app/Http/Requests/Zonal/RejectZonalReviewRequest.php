<?php

namespace App\Http\Requests\Zonal;

use App\Models\TransferApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RejectZonalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transferApplication = $this->route('transferApplication');

        return $transferApplication instanceof TransferApplication
            && $this->user()?->can(
                'rejectZonalReview',
                $transferApplication
            );
    }

    public function rules(): array
    {
        return [
            'recommendation' => [
                'nullable',
                'string',
                Rule::in([
                    'Strongly Recommended',
                    'Recommended',
                    'Recommended with Conditions',
                    'Not Recommended',
                ]),
            ],
            'remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'rejection_reason' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required' =>
                'A rejection reason is required.',
            'rejection_reason.min' =>
                'The rejection reason must contain at least 10 characters.',
        ];
    }
}
