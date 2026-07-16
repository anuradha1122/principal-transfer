<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create divisions') ?? false;
    }

    public function rules(): array
    {
        return [
            'zone_id' => [
                'required',
                Rule::exists('zones', 'id'),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('divisions', 'name')
                    ->where(
                        fn ($query) => $query->where(
                            'zone_id',
                            $this->input('zone_id')
                        )
                    ),
            ],
            'code' => [
                'required',
                'string',
                'max:30',
                'alpha_dash',
                'unique:divisions,code',
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
