<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransferCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('manage transfer cycles') ?? false;
    }

    public function rules(): array
    {
        $cycle = $this->route('transfer_cycle');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique(
                    'transfer_cycles',
                    'code'
                )->ignore($cycle?->id),
            ],
            'transfer_type' => [
                'required',
                Rule::in([
                    'Annual',
                    'Special',
                    'Mutual',
                    'Administrative',
                ]),
            ],
            'transfer_year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
            ],
            'application_open_date' => [
                'required',
                'date',
            ],
            'application_close_date' => [
                'required',
                'date',
                'after_or_equal:application_open_date',
            ],
            'effective_from_date' => [
                'nullable',
                'date',
                'after_or_equal:application_close_date',
            ],
            'minimum_service_years' => [
                'required',
                'integer',
                'min:0',
                'max:50',
            ],
            'maximum_preferences' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'allow_same_zone_preferences' => [
                'required',
                'boolean',
            ],
            'allow_other_zone_preferences' => [
                'required',
                'boolean',
            ],
            'allow_withdrawal' => [
                'required',
                'boolean',
            ],
            'status' => [
                'required',
                Rule::in([
                    'Draft',
                    'Published',
                    'Closed',
                    'Completed',
                    'Cancelled',
                ]),
            ],
            'instructions' => [
                'nullable',
                'string',
                'max:10000',
            ],
            'eligibility_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
