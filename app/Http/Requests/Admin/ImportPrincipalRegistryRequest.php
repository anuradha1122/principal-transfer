<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportPrincipalRegistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('import principal registry') ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
            'update_existing' => [
                'required',
                'boolean',
            ],
        ];
    }
}
