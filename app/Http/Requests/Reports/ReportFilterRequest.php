<?php

namespace App\Http\Requests\Reports;

use App\Models\TransferApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use ReflectionClass;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->can(
            'view management reports'
        )
            || $user->can(
                'view provincial reports'
            )
            || $user->can(
                'view zonal reports'
            )
            || $user->can(
                'view transfer board reports'
            )
            || $user->can(
                'view personal reports'
            );
    }

    public function rules(): array
    {
        return [
            'transfer_cycle_id' => [
                'nullable',
                'integer',
                'exists:transfer_cycles,id',
            ],

            'zone_id' => [
                'nullable',
                'integer',
                'exists:zones,id',
            ],

            'status' => [
                'nullable',
                'string',
                Rule::in(
                    $this->applicationStatuses()
                ),
            ],

            'transfer_reason' => [
                'nullable',
                'string',
                'max:255',
            ],

            'service_grade' => [
                'nullable',
                'string',
                'max:100',
            ],

            'current_designation' => [
                'nullable',
                'string',
                'max:100',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'transfer_cycle_id' => $this->filled(
                'transfer_cycle_id'
            )
                    ? (int) $this->input(
                        'transfer_cycle_id'
                    )
                    : null,

            'zone_id' => $this->filled(
                'zone_id'
            )
                    ? (int) $this->input(
                        'zone_id'
                    )
                    : null,

            'status' => $this->cleanString(
                'status'
            ),

            'transfer_reason' => $this->cleanString(
                'transfer_reason'
            ),

            'service_grade' => $this->cleanString(
                'service_grade'
            ),

            'current_designation' => $this->cleanString(
                'current_designation'
            ),

            'date_from' => $this->cleanString(
                'date_from'
            ),

            'date_to' => $this->cleanString(
                'date_to'
            ),
        ]);
    }

    private function applicationStatuses(): array
    {
        $reflection =
            new ReflectionClass(
                TransferApplication::class
            );

        return collect(
            $reflection->getConstants()
        )
            ->filter(
                fn (
                    mixed $value,
                    string $name
                ): bool => str_starts_with(
                    $name,
                    'STATUS_'
                )
                    && is_string($value)
                    && $value !== ''
            )
            ->values()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    private function cleanString(
        string $field
    ): ?string {
        if (! $this->filled($field)) {
            return null;
        }

        $value = trim(
            (string) $this->input(
                $field
            )
        );

        return $value !== ''
            ? $value
            : null;
    }
}
