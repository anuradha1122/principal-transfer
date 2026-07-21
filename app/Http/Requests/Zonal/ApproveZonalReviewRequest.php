<?php

namespace App\Http\Requests\Zonal;

use App\Models\TransferApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveZonalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transferApplication = $this->route('transferApplication');

        return $transferApplication instanceof TransferApplication
            && $this->user()?->can(
                'approveZonalReview',
                $transferApplication
            );
    }

    public function rules(): array
    {
        return [
            'recommendation' => [
                'required',
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
        ];
    }

    public function messages(): array
    {
        return [
            'recommendation.required' => 'Please select a Zonal recommendation.',
        ];
    }
}
