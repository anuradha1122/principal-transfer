<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit zones') ?? false;
    }

    public function rules(): array
    {
        $zoneId = $this->route('zone')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('zones', 'name')
                    ->ignore($zoneId),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                'alpha_dash',
                Rule::unique('zones', 'code')
                    ->ignore($zoneId),
            ],
            'district' => [
                'required',
                Rule::in([
                    'Ratnapura',
                    'Kegalle',
                ]),
            ],
            'office_address' => [
                'nullable',
                'string',
                'max:255',
            ],
            'telephone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:999',
            ],
        ];
    }
}
