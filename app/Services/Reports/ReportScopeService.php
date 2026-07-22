<?php

namespace App\Services\Reports;

use App\Models\TransferApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ReportScopeService
{
    public function transferApplications(
        User $user
    ): Builder {
        $query = TransferApplication::query();

        if (
            $user->hasRole('Super Admin')
            || $user->hasRole(
                'Provincial Director'
            )
            || $user->hasRole(
                'Transfer Board Member'
            )
        ) {
            return $query;
        }

        if (
            $user->hasRole(
                'Zonal Director'
            )
        ) {
            return $query->where(
                'origin_zone_id',
                $user->assigned_zone_id
            );
        }

        if (
            $user->hasRole(
                'Principal'
            )
        ) {
            return $query->whereHas(
                'principalProfile',
                function (
                    Builder $profileQuery
                ) use (
                    $user
                ): void {
                    $profileQuery->where(
                        'user_id',
                        $user->id
                    );
                }
            );
        }

        return $query->whereRaw(
            '1 = 0'
        );
    }

    public function applyApplicationFilters(
        Builder $query,
        array $filters,
        User $user
    ): Builder {
        $query
            ->when(
                data_get(
                    $filters,
                    'transfer_cycle_id'
                ),
                function (
                    Builder $builder,
                    int $transferCycleId
                ): void {
                    $builder->where(
                        'transfer_cycle_id',
                        $transferCycleId
                    );
                }
            )
            ->when(
                data_get(
                    $filters,
                    'status'
                ),
                function (
                    Builder $builder,
                    string $status
                ): void {
                    $builder->where(
                        'status',
                        $status
                    );
                }
            )
            ->when(
                data_get(
                    $filters,
                    'transfer_reason'
                ),
                function (
                    Builder $builder,
                    string $reason
                ): void {
                    $builder->where(
                        'transfer_reason',
                        $reason
                    );
                }
            )
            ->when(
                data_get(
                    $filters,
                    'service_grade'
                ),
                function (
                    Builder $builder,
                    string $grade
                ): void {
                    $builder->where(
                        'service_grade',
                        $grade
                    );
                }
            )
            ->when(
                data_get(
                    $filters,
                    'current_designation'
                ),
                function (
                    Builder $builder,
                    string $designation
                ): void {
                    $builder->where(
                        'current_designation',
                        $designation
                    );
                }
            )
            ->when(
                data_get(
                    $filters,
                    'date_from'
                ),
                function (
                    Builder $builder,
                    string $dateFrom
                ): void {
                    $builder->whereDate(
                        'submitted_at',
                        '>=',
                        $dateFrom
                    );
                }
            )
            ->when(
                data_get(
                    $filters,
                    'date_to'
                ),
                function (
                    Builder $builder,
                    string $dateTo
                ): void {
                    $builder->whereDate(
                        'submitted_at',
                        '<=',
                        $dateTo
                    );
                }
            );

        $requestedZoneId = data_get(
            $filters,
            'zone_id'
        );

        if (
            $user->hasRole(
                'Zonal Director'
            )
        ) {
            return $query->where(
                'origin_zone_id',
                $user->assigned_zone_id
            );
        }

        if ($requestedZoneId) {
            $query->where(
                'origin_zone_id',
                $requestedZoneId
            );
        }

        return $query;
    }

    public function scopedAndFilteredApplications(
        User $user,
        array $filters
    ): Builder {
        return $this->applyApplicationFilters(
            query: $this->transferApplications(
                $user
            ),
            filters: $filters,
            user: $user
        );
    }
}
