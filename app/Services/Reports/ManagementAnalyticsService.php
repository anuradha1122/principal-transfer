<?php

namespace App\Services\Reports;

use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ManagementAnalyticsService
{
    public function __construct(
        private readonly ReportScopeService $scopeService
    ) {}

    public function build(
        User $user,
        array $filters
    ): array {
        $baseQuery =
            $this->scopeService
                ->scopedAndFilteredApplications(
                    user: $user,
                    filters: $filters
                );

        return [
            'summary' => $this->summary(
                clone $baseQuery
            ),

            'status_distribution' => $this->statusDistribution(
                clone $baseQuery
            ),

            'zone_distribution' => $this->zoneDistribution(
                clone $baseQuery
            ),

            'monthly_trend' => $this->monthlyTrend(
                clone $baseQuery
            ),

            'oldest_pending' => $this->oldestPending(
                clone $baseQuery
            ),

            'filter_options' => $this->filterOptions(
                $user
            ),
        ];
    }

    private function summary(
        Builder $query
    ): array {
        $total =
            (clone $query)->count();

        $draft =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_DRAFT',
                    'draft'
                )
            );

        $submitted =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_SUBMITTED',
                    'submitted'
                )
            );

        $zonalReview =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_ZONAL_REVIEW',
                    'zonal_review'
                )
            );

        $zonalApproved =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_ZONAL_APPROVED',
                    'zonal_approved'
                )
            );

        $zonalRejected =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_ZONAL_REJECTED',
                    'zonal_rejected'
                )
            );

        $provincialReview =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_PROVINCIAL_REVIEW',
                    'provincial_review'
                )
            );

        $provincialApproved =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_PROVINCIAL_APPROVED',
                    'provincial_approved'
                )
            );

        $provincialRejected =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_PROVINCIAL_REJECTED',
                    'provincial_rejected'
                )
            );

        $returnedToZone =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_RETURNED_TO_ZONE',
                    'returned_to_zone'
                )
            );

        $boardReview =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_BOARD_REVIEW',
                    'board_review'
                )
            );

        $finalApproved =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_BOARD_APPROVED',
                    'board_approved'
                )
            );

        $finalRejected =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_BOARD_REJECTED',
                    'board_rejected'
                )
            );

        $waitlisted =
            $this->countByStatus(
                $query,
                $this->statusConstant(
                    'STATUS_WAITLISTED',
                    'waitlisted'
                )
            );

        $pending =
            $submitted
            + $zonalReview
            + $zonalApproved
            + $provincialReview
            + $provincialApproved
            + $returnedToZone
            + $boardReview;

        $applicationIds =
            (clone $query)
                ->select(
                    'transfer_applications.id'
                );

        $appeals =
            TransferAppeal::query()
                ->whereIn(
                    'transfer_application_id',
                    $applicationIds
                )
                ->count();

        $publishedDocuments =
            TransferDocument::query()
                ->whereIn(
                    'transfer_application_id',
                    (clone $query)->select(
                        'transfer_applications.id'
                    )
                )
                ->where(
                    'is_published',
                    true
                )
                ->count();

        return [
            'total_applications' => $total,

            'draft' => $draft,

            'submitted' => $submitted,

            'zonal_review' => $zonalReview,

            'zonal_approved' => $zonalApproved,

            'zonal_rejected' => $zonalRejected,

            'provincial_review' => $provincialReview,

            'provincial_approved' => $provincialApproved,

            'provincial_rejected' => $provincialRejected,

            'returned_to_zone' => $returnedToZone,

            'board_review' => $boardReview,

            'final_approved' => $finalApproved,

            'final_rejected' => $finalRejected,

            'waitlisted' => $waitlisted,

            'pending_decisions' => $pending,

            'appeals' => $appeals,

            'published_documents' => $publishedDocuments,

            'approval_rate' => $this->percentage(
                $finalApproved,
                $finalApproved
                + $finalRejected
            ),
        ];
    }

    private function statusDistribution(
        Builder $query
    ): array {
        return (clone $query)
            ->select([
                'status',
                DB::raw(
                    'COUNT(*) as total'
                ),
            ])
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(
                fn ($item): array => [
                    'status' => $item->status,

                    'label' => $this->statusLabel(
                        $item->status
                    ),

                    'total' => (int) $item->total,
                ]
            )
            ->values()
            ->all();
    }

    private function zoneDistribution(
        Builder $query
    ): array {
        return (clone $query)
            ->leftJoin(
                'zones',
                'zones.id',
                '=',
                'transfer_applications.origin_zone_id'
            )
            ->select([
                'zones.id',
                DB::raw(
                    "COALESCE(zones.name, 'Unassigned') as zone_name"
                ),
                DB::raw(
                    'COUNT(transfer_applications.id) as total'
                ),
            ])
            ->groupBy(
                'zones.id',
                'zones.name'
            )
            ->orderByDesc('total')
            ->get()
            ->map(
                fn ($item): array => [
                    'zone_id' => $item->id,

                    'zone_name' => $item->zone_name,

                    'total' => (int) $item->total,
                ]
            )
            ->values()
            ->all();
    }

    private function monthlyTrend(
        Builder $query
    ): array {
        $driver =
            DB::connection()
                ->getDriverName();

        $monthExpression =
            match ($driver) {
                'pgsql' => "TO_CHAR(submitted_at, 'YYYY-MM')",

                'sqlite' => "strftime('%Y-%m', submitted_at)",

                default => "DATE_FORMAT(submitted_at, '%Y-%m')",
            };

        return (clone $query)
            ->whereNotNull(
                'submitted_at'
            )
            ->whereDate(
                'submitted_at',
                '>=',
                now()
                    ->subMonths(11)
                    ->startOfMonth()
                    ->toDateString()
            )
            ->selectRaw(
                "{$monthExpression} as month"
            )
            ->selectRaw(
                'COUNT(*) as total'
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(
                fn ($item): array => [
                    'month' => $item->month,

                    'label' => Carbon::createFromFormat(
                        'Y-m',
                        $item->month
                    )->format('M Y'),

                    'total' => (int) $item->total,
                ]
            )
            ->values()
            ->all();
    }

    private function oldestPending(
        Builder $query
    ): array {
        $finalStatuses = [
            $this->statusConstant(
                'STATUS_BOARD_APPROVED',
                'board_approved'
            ),

            $this->statusConstant(
                'STATUS_BOARD_REJECTED',
                'board_rejected'
            ),

            $this->statusConstant(
                'STATUS_WAITLISTED',
                'waitlisted'
            ),
        ];

        return (clone $query)
            ->with([
                'originZone:id,name',
                'currentSchool:id,name',
            ])
            ->whereNotIn(
                'status',
                $finalStatuses
            )
            ->whereNotNull(
                'submitted_at'
            )
            ->oldest('submitted_at')
            ->limit(10)
            ->get()
            ->map(
                fn (
                    TransferApplication $application
                ): array => [
                    'id' => $application->id,

                    'application_number' => $application
                        ->application_number
                        ?? 'Application #'
                            .$application->id,

                    'principal_name' => $application
                        ->principal_name
                        ?? 'Principal',

                    'zone_name' => $application
                        ->originZone
                        ?->name
                        ?? 'Unassigned',

                    'school_name' => $application
                        ->currentSchool
                        ?->name
                        ?? 'Unassigned',

                    'status' => $application->status,

                    'status_label' => $this->statusLabel(
                        $application->status
                    ),

                    'submitted_at' => $application
                        ->submitted_at
                        ?->toDateString(),

                    'pending_days' => $application
                        ->submitted_at
                        ?->diffInDays(
                            now()
                        )
                        ?? 0,
                ]
            )
            ->values()
            ->all();
    }

    private function filterOptions(
        User $user
    ): array {
        $applications =
            $this->scopeService
                ->transferApplications(
                    $user
                );

        $cycleIds =
            (clone $applications)
                ->whereNotNull(
                    'transfer_cycle_id'
                )
                ->distinct()
                ->pluck(
                    'transfer_cycle_id'
                );

        $zoneIds =
            (clone $applications)
                ->whereNotNull(
                    'origin_zone_id'
                )
                ->distinct()
                ->pluck(
                    'origin_zone_id'
                );

        $cycles =
            DB::table(
                'transfer_cycles'
            )
                ->whereIn(
                    'id',
                    $cycleIds
                )
                ->orderByDesc('id')
                ->get()
                ->map(
                    fn ($cycle): array => [
                        'id' => $cycle->id,

                        'name' => $cycle->name
                            ?? $cycle->title
                            ?? $cycle->code
                            ?? 'Cycle #'
                                .$cycle->id,
                    ]
                )
                ->values()
                ->all();

        $zones =
            DB::table('zones')
                ->whereIn(
                    'id',
                    $zoneIds
                )
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                ])
                ->map(
                    fn ($zone): array => [
                        'id' => $zone->id,

                        'name' => $zone->name,
                    ]
                )
                ->values()
                ->all();

        return [
            'cycles' => $cycles,

            'zones' => $zones,

            'statuses' => (clone $applications)
                ->whereNotNull('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status')
                ->map(
                    fn (
                        string $status
                    ): array => [
                        'value' => $status,

                        'label' => $this->statusLabel(
                            $status
                        ),
                    ]
                )
                ->values()
                ->all(),

            'transfer_reasons' => $this->distinctOptions(
                clone $applications,
                'transfer_reason'
            ),

            'service_grades' => $this->distinctOptions(
                clone $applications,
                'service_grade'
            ),

            'designations' => $this->distinctOptions(
                clone $applications,
                'current_designation'
            ),
        ];
    }

    private function distinctOptions(
        Builder $query,
        string $column
    ): array {
        return $query
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->values()
            ->all();
    }

    private function countByStatus(
        Builder $query,
        string $status
    ): int {
        return (clone $query)
            ->where(
                'status',
                $status
            )
            ->count();
    }

    private function statusConstant(
        string $constantName,
        string $fallback
    ): string {
        $qualifiedName =
            TransferApplication::class
            .'::'
            .$constantName;

        return defined(
            $qualifiedName
        )
            ? constant(
                $qualifiedName
            )
            : $fallback;
    }

    private function percentage(
        int $value,
        int $total
    ): float {
        if ($total === 0) {
            return 0;
        }

        return round(
            ($value / $total) * 100,
            2
        );
    }

    private function statusLabel(
        ?string $status
    ): string {
        if (! $status) {
            return 'Unknown';
        }

        return str($status)
            ->replace(['-', '_'], ' ')
            ->title()
            ->toString();
    }
}
