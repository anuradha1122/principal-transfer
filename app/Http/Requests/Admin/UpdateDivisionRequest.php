<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit divisions') ?? false;
    }

    public function rules(): array
    {
        $divisionId = $this->route('division')?->id;

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
                    )
                    ->ignore($divisionId),
            ],
            'code' => [
                'required',
                'string',
                'max:30',
                'alpha_dash',
                Rule::unique('divisions', 'code')
                    ->ignore($divisionId),
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
