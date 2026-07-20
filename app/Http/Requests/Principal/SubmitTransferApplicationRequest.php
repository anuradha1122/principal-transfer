<?php

namespace App\Http\Requests\Principal;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTransferApplicationRequest extends FormRequest
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
            'declaration_accepted' => [
                'accepted',
            ],
        ];
    }
}
