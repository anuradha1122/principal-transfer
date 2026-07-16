<?php

namespace App\Http\Requests\Admin;

use App\Models\PrincipalRegistry;
use App\Services\NicService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePrincipalRegistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            ?->can('edit principal registry') ?? false;
    }

    public function rules(): array
    {
        return [
            'nic' => [
                'required',
                'string',
                'max:20',
            ],
            'full_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'name_with_initials' => [
                'nullable',
                'string',
                'max:255',
            ],
            'school_id' => [
                'nullable',
                Rule::exists('schools', 'id'),
            ],
            'designation' => [
                'nullable',
                Rule::in([
                    'Principal',
                    'Deputy Principal',
                    'Assistant Principal',
                ]),
            ],
            'employee_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'notes' => [
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
                $registry = $this->route(
                    'principal_registry'
                );

                if (! $registry instanceof PrincipalRegistry) {
                    return;
                }

                $nicService = app(NicService::class);
                $normalized = $nicService->normalize(
                    $this->input('nic')
                );

                if (! $nicService->isValidFormat($normalized)) {
                    $validator->errors()->add(
                        'nic',
                        'Enter a valid 9-digit NIC ending in V or X, or a 12-digit NIC.'
                    );

                    return;
                }

                $exists = PrincipalRegistry::query()
                    ->where(
                        'normalized_nic',
                        $normalized
                    )
                    ->whereKeyNot($registry->id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'nic',
                        'This NIC already exists in the principal registry.'
                    );
                }

                if (
                    $registry->registered_user_id &&
                    $normalized !==
                        $registry->normalized_nic
                ) {
                    $validator->errors()->add(
                        'nic',
                        'The NIC cannot be changed after an account has been registered.'
                    );
                }
            },
        ];
    }
}
