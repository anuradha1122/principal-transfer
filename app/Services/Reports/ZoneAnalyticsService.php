<?php

namespace App\Services\Reports;

use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class ZoneAnalyticsService
{
    public function __construct(
        private readonly ReportScopeService $scopeService
    ) {
    }

    /**
     * Build all Zone performance analytics.
     */
    public function build(
        User $user,
        array $filters = []
    ): array {
        $applicationQuery =
            $this->scopeService
                ->scopedAndFilteredApplications(
                    user: $user,
                    filters: $filters
                );

        $zoneRows =
            $this->zonePerformance(
                clone $applicationQuery
            );

        return [
            'summary' =>
                $this->summary(
                    $zoneRows
                ),

            'zones' =>
                $zoneRows,

            'chart' =>
                $this->chartData(
                    $zoneRows
                ),

            'status_legend' =>
                $this->statusLegend(),

            'filter_options' => [
                'zones' =>
                    $this->zoneOptions(
                        $user
                    ),

                'cycles' =>
                    $this->cycleOptions(
                        $user
                    ),
            ],
        ];
    }

    /**
     * Build Zone performance rows.
     */
    public function zonePerformance(
        Builder $query
    ): array {
        $statuses =
            $this->statusValues();

        $submittedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_SUBMITTED',
                ],
                [
                    'submitted',
                ]
            );

        $zonalReviewStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_ZONAL_REVIEW',
                ],
                [
                    'zonal_review',
                    'under_zonal_review',
                ]
            );

        $zonalApprovedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_ZONAL_APPROVED',
                ],
                [
                    'zonal_approved',
                ]
            );

        $zonalRejectedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_ZONAL_REJECTED',
                ],
                [
                    'zonal_rejected',
                ]
            );

        $provincialReviewStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_PROVINCIAL_REVIEW',
                ],
                [
                    'provincial_review',
                    'under_provincial_review',
                ]
            );

        $provincialApprovedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_PROVINCIAL_APPROVED',
                ],
                [
                    'provincial_approved',
                ]
            );

        $provincialRejectedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_PROVINCIAL_REJECTED',
                ],
                [
                    'provincial_rejected',
                ]
            );

        $returnedToZoneStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_RETURNED_TO_ZONE',
                ],
                [
                    'returned_to_zone',
                    'returned',
                ]
            );

        $boardReviewStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_BOARD_REVIEW',
                    'STATUS_TRANSFER_BOARD_REVIEW',
                ],
                [
                    'board_review',
                    'transfer_board_review',
                    'under_board_review',
                ]
            );

        $finalApprovedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_FINAL_APPROVED',
                    'STATUS_BOARD_APPROVED',
                    'STATUS_TRANSFER_APPROVED',
                ],
                [
                    'final_approved',
                    'board_approved',
                    'approved',
                    'transfer_approved',
                ]
            );

        $finalRejectedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_FINAL_REJECTED',
                    'STATUS_BOARD_REJECTED',
                    'STATUS_TRANSFER_REJECTED',
                ],
                [
                    'final_rejected',
                    'board_rejected',
                    'rejected',
                    'transfer_rejected',
                ]
            );

        $waitlistedStatus =
            $this->resolveStatus(
                $statuses,
                [
                    'STATUS_WAITLISTED',
                    'STATUS_WAITLIST',
                ],
                [
                    'waitlisted',
                    'waitlist',
                ]
            );

        $rows =
            (clone $query)
                ->leftJoin(
                    'zones',
                    'zones.id',
                    '=',
                    'transfer_applications.origin_zone_id'
                )
                ->select([
                    'zones.id as zone_id',

                    DB::raw(
                        "COALESCE(zones.name, 'Unassigned') as zone_name"
                    ),

                    DB::raw(
                        'COUNT(transfer_applications.id) as total_applications'
                    ),

                    $this->statusCountExpression(
                        $submittedStatus,
                        'submitted'
                    ),

                    $this->statusCountExpression(
                        $zonalReviewStatus,
                        'zonal_review'
                    ),

                    $this->statusCountExpression(
                        $zonalApprovedStatus,
                        'zonal_approved'
                    ),

                    $this->statusCountExpression(
                        $zonalRejectedStatus,
                        'zonal_rejected'
                    ),

                    $this->statusCountExpression(
                        $provincialReviewStatus,
                        'provincial_review'
                    ),

                    $this->statusCountExpression(
                        $provincialApprovedStatus,
                        'provincial_approved'
                    ),

                    $this->statusCountExpression(
                        $provincialRejectedStatus,
                        'provincial_rejected'
                    ),

                    $this->statusCountExpression(
                        $returnedToZoneStatus,
                        'returned_to_zone'
                    ),

                    $this->statusCountExpression(
                        $boardReviewStatus,
                        'board_review'
                    ),

                    $this->statusCountExpression(
                        $finalApprovedStatus,
                        'final_approved'
                    ),

                    $this->statusCountExpression(
                        $finalRejectedStatus,
                        'final_rejected'
                    ),

                    $this->statusCountExpression(
                        $waitlistedStatus,
                        'waitlisted'
                    ),
                ])
                ->groupBy(
                    'zones.id',
                    'zones.name'
                )
                ->orderBy('zone_name')
                ->get();

        $appealCounts =
            $this->appealCountsByZone(
                clone $query
            );

        return $rows
            ->map(
                function (
                    object $row
                ) use (
                    $appealCounts
                ): array {
                    $finalApproved =
                        (int) $row->final_approved;

                    $finalRejected =
                        (int) $row->final_rejected;

                    $decided =
                        $finalApproved
                        + $finalRejected;

                    $zoneKey =
                        $row->zone_id !== null
                            ? (string) $row->zone_id
                            : 'unassigned';

                    return [
                        'zone_id' =>
                            $row->zone_id !== null
                                ? (int) $row->zone_id
                                : null,

                        'zone_name' =>
                            (string) $row->zone_name,

                        'total_applications' =>
                            (int) $row
                                ->total_applications,

                        'submitted' =>
                            (int) $row->submitted,

                        'zonal_review' =>
                            (int) $row->zonal_review,

                        'zonal_approved' =>
                            (int) $row->zonal_approved,

                        'zonal_rejected' =>
                            (int) $row->zonal_rejected,

                        'provincial_review' =>
                            (int) $row
                                ->provincial_review,

                        'provincial_approved' =>
                            (int) $row
                                ->provincial_approved,

                        'provincial_rejected' =>
                            (int) $row
                                ->provincial_rejected,

                        'returned_to_zone' =>
                            (int) $row
                                ->returned_to_zone,

                        'board_review' =>
                            (int) $row->board_review,

                        'final_approved' =>
                            $finalApproved,

                        'final_rejected' =>
                            $finalRejected,

                        'waitlisted' =>
                            (int) $row->waitlisted,

                        'appeals' =>
                            (int) (
                                $appealCounts[
                                    $zoneKey
                                ] ?? 0
                            ),

                        'decided_applications' =>
                            $decided,

                        'approval_rate' =>
                            $this->percentage(
                                $finalApproved,
                                $decided
                            ),

                        'rejection_rate' =>
                            $this->percentage(
                                $finalRejected,
                                $decided
                            ),

                        'completion_rate' =>
                            $this->percentage(
                                $decided,
                                (int) $row
                                    ->total_applications
                            ),
                    ];
                }
            )
            ->values()
            ->all();
    }

    /**
     * Build overall summary from Zone rows.
     */
    private function summary(
        array $zoneRows
    ): array {
        $rows = collect(
            $zoneRows
        );

        $totalApplications =
            (int) $rows->sum(
                'total_applications'
            );

        $finalApproved =
            (int) $rows->sum(
                'final_approved'
            );

        $finalRejected =
            (int) $rows->sum(
                'final_rejected'
            );

        $waitlisted =
            (int) $rows->sum(
                'waitlisted'
            );

        $appeals =
            (int) $rows->sum(
                'appeals'
            );

        $decided =
            $finalApproved
            + $finalRejected;

        $bestZone =
            $rows
                ->filter(
                    fn (
                        array $row
                    ): bool =>
                        $row[
                            'decided_applications'
                        ] > 0
                )
                ->sortByDesc(
                    'approval_rate'
                )
                ->first();

        $highestVolumeZone =
            $rows
                ->sortByDesc(
                    'total_applications'
                )
                ->first();

        return [
            'zone_count' =>
                $rows->count(),

            'total_applications' =>
                $totalApplications,

            'final_approved' =>
                $finalApproved,

            'final_rejected' =>
                $finalRejected,

            'waitlisted' =>
                $waitlisted,

            'appeals' =>
                $appeals,

            'decided_applications' =>
                $decided,

            'overall_approval_rate' =>
                $this->percentage(
                    $finalApproved,
                    $decided
                ),

            'overall_rejection_rate' =>
                $this->percentage(
                    $finalRejected,
                    $decided
                ),

            'overall_completion_rate' =>
                $this->percentage(
                    $decided,
                    $totalApplications
                ),

            'best_performing_zone' =>
                $bestZone
                    ? [
                        'zone_id' =>
                            $bestZone['zone_id'],

                        'zone_name' =>
                            $bestZone['zone_name'],

                        'approval_rate' =>
                            $bestZone[
                                'approval_rate'
                            ],
                    ]
                    : null,

            'highest_volume_zone' =>
                $highestVolumeZone
                    ? [
                        'zone_id' =>
                            $highestVolumeZone[
                                'zone_id'
                            ],

                        'zone_name' =>
                            $highestVolumeZone[
                                'zone_name'
                            ],

                        'total_applications' =>
                            $highestVolumeZone[
                                'total_applications'
                            ],
                    ]
                    : null,
        ];
    }

    /**
     * Format chart-ready Zone data.
     */
    private function chartData(
        array $zoneRows
    ): array {
        return collect(
            $zoneRows
        )
            ->map(
                fn (
                    array $row
                ): array => [
                    'zone_id' =>
                        $row['zone_id'],

                    'zone_name' =>
                        $row['zone_name'],

                    'total' =>
                        $row[
                            'total_applications'
                        ],

                    'approved' =>
                        $row[
                            'final_approved'
                        ],

                    'rejected' =>
                        $row[
                            'final_rejected'
                        ],

                    'waitlisted' =>
                        $row['waitlisted'],

                    'appeals' =>
                        $row['appeals'],

                    'approval_rate' =>
                        $row[
                            'approval_rate'
                        ],
                ]
            )
            ->values()
            ->all();
    }

    /**
     * Count appeals grouped by application origin Zone.
     */
    private function appealCountsByZone(
        Builder $applicationQuery
    ): array {
        $applicationIds =
            (clone $applicationQuery)
                ->select(
                    'transfer_applications.id'
                );

        return TransferAppeal::query()
            ->join(
                'transfer_applications',
                'transfer_applications.id',
                '=',
                'transfer_appeals.transfer_application_id'
            )
            ->whereIn(
                'transfer_appeals.transfer_application_id',
                $applicationIds
            )
            ->select([
                'transfer_applications.origin_zone_id',

                DB::raw(
                    'COUNT(transfer_appeals.id) as total'
                ),
            ])
            ->groupBy(
                'transfer_applications.origin_zone_id'
            )
            ->get()
            ->mapWithKeys(
                function (
                    object $row
                ): array {
                    $key =
                        $row->origin_zone_id !== null
                            ? (string) $row
                                ->origin_zone_id
                            : 'unassigned';

                    return [
                        $key =>
                            (int) $row->total,
                    ];
                }
            )
            ->all();
    }

    /**
     * Return Zones available within the user's report scope.
     */
    private function zoneOptions(
        User $user
    ): array {
        $zoneIds =
            $this->scopeService
                ->transferApplications(
                    $user
                )
                ->whereNotNull(
                    'origin_zone_id'
                )
                ->distinct()
                ->pluck(
                    'origin_zone_id'
                );

        return DB::table('zones')
            ->whereIn(
                'id',
                $zoneIds
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
            ])
            ->map(
                fn (
                    object $zone
                ): array => [
                    'id' =>
                        (int) $zone->id,

                    'name' =>
                        (string) $zone->name,

                    'code' =>
                        $zone->code
                        ?? null,
                ]
            )
            ->values()
            ->all();
    }

    /**
     * Return transfer cycles available within the user's scope.
     */
    private function cycleOptions(
        User $user
    ): array {
        $cycleIds =
            $this->scopeService
                ->transferApplications(
                    $user
                )
                ->whereNotNull(
                    'transfer_cycle_id'
                )
                ->distinct()
                ->pluck(
                    'transfer_cycle_id'
                );

        return DB::table(
            'transfer_cycles'
        )
            ->whereIn(
                'id',
                $cycleIds
            )
            ->orderByDesc('id')
            ->get()
            ->map(
                fn (
                    object $cycle
                ): array => [
                    'id' =>
                        (int) $cycle->id,

                    'name' =>
                        $cycle->name
                        ?? $cycle->title
                        ?? $cycle->code
                        ?? 'Cycle #'
                            . $cycle->id,
                ]
            )
            ->values()
            ->all();
    }

    /**
     * Build SQL COUNT expression for a status.
     */
    private function statusCountExpression(
        ?string $status,
        string $alias
    ): mixed {
        if ($status === null) {
            return DB::raw(
                "0 as {$alias}"
            );
        }

        $quotedStatus =
            DB::connection()
                ->getPdo()
                ->quote($status);

        return DB::raw(
            "SUM(
                CASE
                    WHEN transfer_applications.status = {$quotedStatus}
                    THEN 1
                    ELSE 0
                END
            ) as {$alias}"
        );
    }

    /**
     * Read all STATUS_* constants from TransferApplication.
     */
    private function statusValues(): array
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
                ): bool =>
                    str_starts_with(
                        $name,
                        'STATUS_'
                    )
                    && is_string($value)
                    && $value !== ''
            )
            ->mapWithKeys(
                fn (
                    string $value,
                    string $name
                ): array => [
                    $name => $value,
                ]
            )
            ->all();
    }

    /**
     * Resolve a status using model constants first,
     * then known database values.
     */
    private function resolveStatus(
        array $statuses,
        array $constantNames,
        array $fallbackValues
    ): ?string {
        foreach (
            $constantNames as $constantName
        ) {
            if (
                isset(
                    $statuses[
                        $constantName
                    ]
                )
            ) {
                return $statuses[
                    $constantName
                ];
            }
        }

        $existingValues =
            array_values(
                $statuses
            );

        foreach (
            $fallbackValues as $fallbackValue
        ) {
            if (
                in_array(
                    $fallbackValue,
                    $existingValues,
                    true
                )
            ) {
                return $fallbackValue;
            }
        }

        return null;
    }

    /**
     * Status information for the frontend and exports.
     */
    private function statusLegend(): array
    {
        return [
            [
                'key' => 'submitted',
                'label' => 'Submitted',
            ],
            [
                'key' => 'zonal_review',
                'label' => 'Zonal Review',
            ],
            [
                'key' => 'zonal_approved',
                'label' => 'Zonal Approved',
            ],
            [
                'key' => 'zonal_rejected',
                'label' => 'Zonal Rejected',
            ],
            [
                'key' => 'provincial_review',
                'label' => 'Provincial Review',
            ],
            [
                'key' => 'provincial_approved',
                'label' => 'Provincial Approved',
            ],
            [
                'key' => 'provincial_rejected',
                'label' => 'Provincial Rejected',
            ],
            [
                'key' => 'returned_to_zone',
                'label' => 'Returned to Zone',
            ],
            [
                'key' => 'board_review',
                'label' => 'Board Review',
            ],
            [
                'key' => 'final_approved',
                'label' => 'Final Approved',
            ],
            [
                'key' => 'final_rejected',
                'label' => 'Final Rejected',
            ],
            [
                'key' => 'waitlisted',
                'label' => 'Waitlisted',
            ],
        ];
    }

    /**
     * Calculate a safe percentage.
     */
    private function percentage(
        int $value,
        int $total
    ): float {
        if ($total <= 0) {
            return 0.0;
        }

        return round(
            ($value / $total) * 100,
            2
        );
    }
}
