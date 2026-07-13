<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create zones') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:zones,name',
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                'alpha_dash',
                'unique:zones,code',
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
