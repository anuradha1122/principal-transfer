<?php

namespace App\Services;

use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DetailedTransferReportService
{
    public function applications(
        User $user,
        array $filters = [],
        bool $paginate = true
    ): LengthAwarePaginator|Collection {
        $query = $this->applicationQuery(
            $user,
            $filters
        )
            ->with([
                'transferCycle:id,name',
                'principalProfile:id,full_name',
                'currentSchool:id,name',
                'originZone:id,name',
            ])
            ->latest('submitted_at')
            ->latest('id');

        if ($paginate) {
            return $query
                ->paginate(25)
                ->withQueryString()
                ->through(
                    fn (
                        TransferApplication $application
                    ): array => $this
                        ->applicationRow(
                            $application
                        )
                );
        }

        return $query
            ->get()
            ->map(
                fn (
                    TransferApplication $application
                ): array => $this
                    ->applicationRow(
                        $application
                    )
            );
    }

    public function decisions(
        User $user,
        array $filters = [],
        bool $paginate = true
    ): LengthAwarePaginator|Collection {
        $query = $this->applicationQuery(
            $user,
            $filters
        )
            ->whereHas(
                'transferBoardDecision'
            )
            ->with([
                'transferCycle:id,name',
                'principalProfile:id,full_name',
                'currentSchool:id,name',
                'originZone:id,name',
                'transferBoardDecision',
                'transferBoardDecision.recommendedSchool:id,name',
            ])
            ->latest('updated_at')
            ->latest('id');

        if ($paginate) {
            return $query
                ->paginate(25)
                ->withQueryString()
                ->through(
                    fn (
                        TransferApplication $application
                    ): array => $this
                        ->decisionRow(
                            $application
                        )
                );
        }

        return $query
            ->get()
            ->map(
                fn (
                    TransferApplication $application
                ): array => $this
                    ->decisionRow(
                        $application
                    )
            );
    }

    public function appeals(
        User $user,
        array $filters = [],
        bool $paginate = true
    ): LengthAwarePaginator|Collection {
        $query = $this->appealQuery(
            $user,
            $filters
        )
            ->with([
                'transferApplication:id,transfer_cycle_id,principal_profile_id,application_number,origin_zone_id,status',
                'transferApplication.transferCycle:id,name',
                'transferApplication.principalProfile:id,full_name',
                'transferApplication.originZone:id,name',
            ])
            ->latest('created_at')
            ->latest('id');

        if ($paginate) {
            return $query
                ->paginate(25)
                ->withQueryString()
                ->through(
                    fn (
                        TransferAppeal $appeal
                    ): array => $this
                        ->appealRow(
                            $appeal
                        )
                );
        }

        return $query
            ->get()
            ->map(
                fn (
                    TransferAppeal $appeal
                ): array => $this
                    ->appealRow(
                        $appeal
                    )
            );
    }

    public function documents(
        User $user,
        array $filters = [],
        bool $paginate = true
    ): LengthAwarePaginator|Collection {
        $query = $this->documentQuery(
            $user,
            $filters
        )
            ->with([
                'transferApplication:id,transfer_cycle_id,principal_profile_id,application_number,origin_zone_id,status',
                'transferApplication.transferCycle:id,name',
                'transferApplication.principalProfile:id,full_name',
                'transferApplication.originZone:id,name',
                'issuer:id,name',
                'publisher:id,name',
            ])
            ->latest('issued_date')
            ->latest('id');

        if ($paginate) {
            return $query
                ->paginate(25)
                ->withQueryString()
                ->through(
                    fn (
                        TransferDocument $document
                    ): array => $this
                        ->documentRow(
                            $document
                        )
                );
        }

        return $query
            ->get()
            ->map(
                fn (
                    TransferDocument $document
                ): array => $this
                    ->documentRow(
                        $document
                    )
            );
    }

    private function applicationQuery(
        User $user,
        array $filters
    ): Builder {
        return TransferApplication::query()
            ->when(
                $filters['transfer_cycle_id']
                    ?? null,
                fn (
                    Builder $query,
                    string|int $cycleId
                ) => $query->where(
                    'transfer_cycle_id',
                    $cycleId
                )
            )
            ->when(
                $filters['zone_id'] ?? null,
                fn (
                    Builder $query,
                    string|int $zoneId
                ) => $query->where(
                    'origin_zone_id',
                    $zoneId
                )
            )
            ->when(
                $filters['status'] ?? null,
                fn (
                    Builder $query,
                    string $status
                ) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $filters['date_from'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'created_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $filters['date_to'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'created_at',
                    '<=',
                    $date
                )
            )
            ->when(
                $user->hasRole('Zonal Director'),
                fn (Builder $query) => $query->forZone(
                    $user->assigned_zone_id
                )
            );
    }

    private function appealQuery(
        User $user,
        array $filters
    ): Builder {
        return TransferAppeal::query()
            ->when(
                $filters['status'] ?? null,
                fn (
                    Builder $query,
                    string $status
                ) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $filters['date_from'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'created_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $filters['date_to'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'created_at',
                    '<=',
                    $date
                )
            )
            ->whereHas(
                'transferApplication',
                function (
                    Builder $query
                ) use (
                    $user,
                    $filters
                ): void {
                    if (
                        $filters[
                            'transfer_cycle_id'
                        ] ?? null
                    ) {
                        $query->where(
                            'transfer_cycle_id',
                            $filters[
                                'transfer_cycle_id'
                            ]
                        );
                    }

                    if (
                        $filters['zone_id']
                        ?? null
                    ) {
                        $query->where(
                            'origin_zone_id',
                            $filters['zone_id']
                        );
                    }

                    if (
                        $user->hasRole(
                            'Zonal Director'
                        )
                    ) {
                        $query->forZone(
                            $user->assigned_zone_id
                        );
                    }
                }
            );
    }

    private function documentQuery(
        User $user,
        array $filters
    ): Builder {
        return TransferDocument::query()
            ->when(
                $filters['document_type']
                    ?? null,
                fn (
                    Builder $query,
                    string $type
                ) => $query->where(
                    'document_type',
                    $type
                )
            )
            ->when(
                isset(
                    $filters['is_published']
                )
                && $filters['is_published']
                    !== '',
                fn (
                    Builder $query
                ) => $query->where(
                    'is_published',
                    filter_var(
                        $filters['is_published'],
                        FILTER_VALIDATE_BOOLEAN
                    )
                )
            )
            ->when(
                $filters['date_from'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'issued_date',
                    '>=',
                    $date
                )
            )
            ->when(
                $filters['date_to'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'issued_date',
                    '<=',
                    $date
                )
            )
            ->whereHas(
                'transferApplication',
                function (
                    Builder $query
                ) use (
                    $user,
                    $filters
                ): void {
                    if (
                        $filters[
                            'transfer_cycle_id'
                        ] ?? null
                    ) {
                        $query->where(
                            'transfer_cycle_id',
                            $filters[
                                'transfer_cycle_id'
                            ]
                        );
                    }

                    if (
                        $filters['zone_id']
                        ?? null
                    ) {
                        $query->where(
                            'origin_zone_id',
                            $filters['zone_id']
                        );
                    }

                    if (
                        $user->hasRole(
                            'Zonal Director'
                        )
                    ) {
                        $query->forZone(
                            $user->assigned_zone_id
                        );
                    }
                }
            );
    }

    private function applicationRow(
        TransferApplication $application
    ): array {
        return [
            'id' => $application->id,

            'application_number' => $application->application_number,

            'principal_name' => $application
                ->principalProfile
                ?->full_name
                ?? $application->principal_name,

            'nic' => $application->nic,

            'employee_number' => $application->employee_number,

            'cycle' => $application
                ->transferCycle
                ?->name,

            'zone' => $application
                ->originZone
                ?->name,

            'current_school' => $application
                ->currentSchool
                ?->name,

            'designation' => $application
                ->current_designation,

            'service_grade' => $application
                ->service_grade,

            'transfer_reason' => $application
                ->transfer_reason,

            'status' => $application->status,

            'submitted_at' => $application
                ->submitted_at
                ?->toIso8601String(),

            'created_at' => $application
                ->created_at
                ?->toIso8601String(),
        ];
    }

    private function decisionRow(
        TransferApplication $application
    ): array {
        $decision =
            $application->transferBoardDecision;

        return [
            'id' => $application->id,

            'application_number' => $application->application_number,

            'principal_name' => $application
                ->principalProfile
                ?->full_name
                ?? $application->principal_name,

            'cycle' => $application
                ->transferCycle
                ?->name,

            'zone' => $application
                ->originZone
                ?->name,

            'current_school' => $application
                ->currentSchool
                ?->name,

            'decision' => $application->status,

            'decision_reference' => data_get(
                $decision,
                'decision_reference'
            ),

            'recommended_school' => data_get(
                $decision,
                'recommendedSchool.name'
            ),

            'effective_date' => optional(
                data_get(
                    $decision,
                    'effective_date'
                )
            )->format('Y-m-d'),

            'remarks' => data_get(
                $decision,
                'remarks'
            )
                ?? data_get(
                    $decision,
                    'decision_remarks'
                ),

            'decided_at' => optional(
                data_get(
                    $decision,
                    'decided_at'
                )
            )->toIso8601String()
                ?? $application
                    ->updated_at
                    ?->toIso8601String(),
        ];
    }

    private function appealRow(
        TransferAppeal $appeal
    ): array {
        $application =
            $appeal->transferApplication;

        return [
            'id' => $appeal->id,

            'appeal_number' => data_get(
                $appeal,
                'appeal_number'
            )
                ?? 'APL-'.$appeal->id,

            'application_number' => $application
                ?->application_number,

            'principal_name' => $application
                ?->principalProfile
                ?->full_name,

            'cycle' => $application
                ?->transferCycle
                ?->name,

            'zone' => $application
                ?->originZone
                ?->name,

            'application_status' => $application?->status,

            'appeal_status' => $appeal->status,

            'grounds' => data_get(
                $appeal,
                'appeal_reason'
            )
                ?? data_get(
                    $appeal,
                    'grounds'
                )
                ?? data_get(
                    $appeal,
                    'reason'
                ),

            'submitted_at' => optional(
                data_get(
                    $appeal,
                    'submitted_at'
                )
            )->toIso8601String(),

            'decided_at' => optional(
                data_get(
                    $appeal,
                    'decided_at'
                )
            )->toIso8601String(),

            'created_at' => $appeal
                ->created_at
                ?->toIso8601String(),
        ];
    }

    private function documentRow(
        TransferDocument $document
    ): array {
        $application =
            $document->transferApplication;

        return [
            'id' => $document->id,

            'document_number' => $document->document_number,

            'document_type' => $document->document_type,

            'application_number' => $application
                ?->application_number,

            'principal_name' => $application
                ?->principalProfile
                ?->full_name,

            'cycle' => $application
                ?->transferCycle
                ?->name,

            'zone' => $application
                ?->originZone
                ?->name,

            'application_status' => $application?->status,

            'issued_date' => optional(
                $document->issued_date
            )->format('Y-m-d'),

            'effective_date' => optional(
                $document->effective_date
            )->format('Y-m-d'),

            'issuer' => $document->issuer?->name,

            'has_signed_copy' => filled(
                $document->signed_file_path
            ),

            'is_published' => (bool) $document->is_published,

            'published_by' => $document->publisher?->name,

            'published_at' => $document
                ->published_at
                ?->toIso8601String(),
        ];
    }
}
