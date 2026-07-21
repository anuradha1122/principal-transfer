<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('create users')
            ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'assigned_zone_id' => $this->filled('assigned_zone_id')
                    ? (int) $this->input(
                        'assigned_zone_id'
                    )
                    : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],

            'role' => [
                'required',
                'string',
                Rule::exists(
                    'roles',
                    'name'
                )->where(
                    'guard_name',
                    'web'
                ),
            ],

            'assigned_zone_id' => [
                Rule::requiredIf(
                    fn (): bool => $this->input('role')
                        === 'Zonal Director'
                ),
                'nullable',
                'integer',
                Rule::exists(
                    'zones',
                    'id'
                )->where(
                    'is_active',
                    true
                ),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'email_verified' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_zone_id.required' => 'An assigned Zone is required for a Zonal Director.',
            'assigned_zone_id.exists' => 'The selected Zone is invalid or inactive.',
        ];
    }
}
