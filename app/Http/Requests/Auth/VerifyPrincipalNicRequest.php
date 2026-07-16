<?php

namespace App\Http\Requests\Auth;

use App\Services\NicService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VerifyPrincipalNicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    public function rules(): array
    {
        return [
            'nic' => [
                'required',
                'string',
                'max:20',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (
                    ! app(NicService::class)
                        ->isValidFormat($this->input('nic'))
                ) {
                    $validator->errors()->add(
                        'nic',
                        'Enter a valid Sri Lankan NIC number.'
                    );
                }
            },
        ];
    }
}
