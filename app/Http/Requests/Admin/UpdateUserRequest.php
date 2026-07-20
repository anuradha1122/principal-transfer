<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('edit users')
            ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'assigned_zone_id' =>
                $this->filled('assigned_zone_id')
                    ? (int) $this->input(
                        'assigned_zone_id'
                    )
                    : null,
        ]);
    }

    public function rules(): array
    {
        $user = $this->route('user');

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
                Rule::unique(
                    'users',
                    'email'
                )->ignore($user?->id),
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
                    fn (): bool =>
                        $this->input('role')
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
            'assigned_zone_id.required' =>
                'An assigned Zone is required for a Zonal Director.',
            'assigned_zone_id.exists' =>
                'The selected Zone is invalid or inactive.',
        ];
    }
}
