<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTransferApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(
            'Principal'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'transfer_cycle_id' => [
                'required',
                Rule::exists('transfer_cycles', 'id'),
            ],
            'transfer_reason' => [
                'required',
                Rule::in([
                    'Long Service',
                    'Medical',
                    'Spouse Employment',
                    'Family Requirement',
                    'Travel Difficulty',
                    'Personal Request',
                    'Mutual Transfer',
                    'Administrative Reason',
                    'Other',
                ]),
            ],
            'reason_details' => [
                'required',
                'string',
                'min:20',
                'max:5000',
            ],
            'has_medical_reason' => [
                'required',
                'boolean',
            ],
            'has_spouse_employment_reason' => [
                'required',
                'boolean',
            ],
            'is_mutual_transfer' => [
                'required',
                'boolean',
            ],
            'mutual_principal_nic' => [
                'nullable',
                'string',
                'max:20',
                'required_if:is_mutual_transfer,true',
            ],
            'principal_remarks' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'preferences' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],
            'preferences.*.school_id' => [
                'required',
                'distinct',
                Rule::exists('schools', 'id')
                    ->where('is_active', true),
            ],
            'preferences.*.preference_reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $profile = $this
                    ->user()
                    ?->principalProfile;

                if (! $profile) {
                    $validator->errors()->add(
                        'transfer_cycle_id',
                        'Your principal profile has not been created.'
                    );

                    return;
                }

                $currentAppointment =
                    $profile->currentAppointment;

                if (! $currentAppointment) {
                    $validator->errors()->add(
                        'transfer_cycle_id',
                        'A current school appointment is required before applying.'
                    );
                }

                $currentSchoolId =
                    $currentAppointment?->school_id;

                foreach (
                    $this->input('preferences', []) as $index => $preference
                ) {
                    if (
                        (int) ($preference['school_id'] ?? 0)
                        === (int) $currentSchoolId
                    ) {
                        $validator->errors()->add(
                            "preferences.{$index}.school_id",
                            'Your current school cannot be selected as a transfer preference.'
                        );
                    }
                }
            },
        ];
    }
}
