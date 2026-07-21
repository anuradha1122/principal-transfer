<?php

namespace App\Services;

use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TransferReportService
{
    public function dashboard(
        User $user,
        array $filters = []
    ): array {
        $applicationQuery =
            $this->applicationQuery(
                $user,
                $filters
            );

        $appealQuery =
            $this->appealQuery(
                $user,
                $filters
            );

        $documentQuery =
            $this->documentQuery(
                $user,
                $filters
            );

        $totalApplications =
            (clone $applicationQuery)->count();

        $decisionStatuses = [
            TransferApplication::STATUS_APPROVED,
            TransferApplication::STATUS_REJECTED,
            TransferApplication::STATUS_WAITLISTED,
        ];

        $finalizedApplications =
            (clone $applicationQuery)
                ->whereIn(
                    'status',
                    $decisionStatuses
                )
                ->count();

        $statusBreakdown =
            (clone $applicationQuery)
                ->select(
                    'status',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('status')
                ->orderByDesc('total')
                ->get()
                ->map(
                    fn ($row): array => [
                        'label' => $row->status
                            ?? 'Unknown',
                        'total' => (int) $row->total,
                    ]
                )
                ->values();

        $zoneBreakdown =
            (clone $applicationQuery)
                ->leftJoin(
                    'zones',
                    'zones.id',
                    '=',
                    'transfer_applications.origin_zone_id'
                )
                ->select(
                    'zones.id',
                    DB::raw(
                        "COALESCE(zones.name, 'Unassigned') as label"
                    ),
                    DB::raw(
                        'COUNT(transfer_applications.id) as total'
                    )
                )
                ->groupBy(
                    'zones.id',
                    'zones.name'
                )
                ->orderByDesc('total')
                ->get()
                ->map(
                    fn ($row): array => [
                        'id' => $row->id,
                        'label' => $row->label,
                        'total' => (int) $row->total,
                    ]
                )
                ->values();

        $monthlyApplications =
            (clone $applicationQuery)
                ->whereNotNull('created_at')
                ->selectRaw(
                    "DATE_FORMAT(created_at, '%Y-%m') as month_key"
                )
                ->selectRaw(
                    "DATE_FORMAT(created_at, '%b %Y') as label"
                )
                ->selectRaw(
                    'COUNT(*) as total'
                )
                ->groupBy(
                    'month_key',
                    'label'
                )
                ->orderBy('month_key')
                ->limit(12)
                ->get()
                ->map(
                    fn ($row): array => [
                        'label' => $row->label,
                        'total' => (int) $row->total,
                    ]
                )
                ->values();

        $appealStatusBreakdown =
            (clone $appealQuery)
                ->select(
                    'status',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('status')
                ->orderByDesc('total')
                ->get()
                ->map(
                    fn ($row): array => [
                        'label' => $row->status
                            ?? 'Unknown',
                        'total' => (int) $row->total,
                    ]
                )
                ->values();

        $documentBreakdown =
            (clone $documentQuery)
                ->select(
                    'document_type',
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('document_type')
                ->orderByDesc('total')
                ->get()
                ->map(
                    fn ($row): array => [
                        'label' => $this->humanize(
                            $row->document_type
                            ?? 'Unknown'
                        ),
                        'total' => (int) $row->total,
                    ]
                )
                ->values();

        $publishedDocuments =
            (clone $documentQuery)
                ->where(
                    'is_published',
                    true
                )
                ->count();

        $signedDocuments =
            (clone $documentQuery)
                ->whereNotNull(
                    'signed_file_path'
                )
                ->count();

        $totalAppeals =
            (clone $appealQuery)->count();

        $approvedAppeals =
            (clone $appealQuery)
                ->where(
                    'status',
                    TransferAppeal::STATUS_APPROVED
                )
                ->count();

        $pendingAppeals =
            (clone $appealQuery)
                ->whereIn(
                    'status',
                    TransferAppeal::activeStatuses()
                )
                ->count();

        $recentApplications =
            (clone $applicationQuery)
                ->with([
                    'principalProfile:id,user_id,full_name',
                    'originZone:id,name',
                    'transferCycle:id,name',
                ])
                ->latest('updated_at')
                ->limit(8)
                ->get()
                ->map(
                    fn (
                        TransferApplication $application
                    ): array => [
                        'id' => $application->id,
                        'application_number' => $application
                            ->application_number,
                        'principal_name' => $application
                            ->principalProfile
                            ?->full_name
                            ?? 'Unknown Principal',
                        'zone' => $application
                            ->originZone
                            ?->name
                            ?? 'Unassigned',
                        'cycle' => $application
                            ->transferCycle
                            ?->name
                            ?? 'Unknown',
                        'status' => $application->status,
                        'updated_at' => $application
                            ->updated_at
                            ?->toIso8601String(),
                    ]
                );

        return [
            'summary' => [
                'total_applications' => $totalApplications,
                'finalized_applications' => $finalizedApplications,
                'approval_rate' => $finalizedApplications > 0
                        ? round(
                            (
                                (clone $applicationQuery)
                                    ->where(
                                        'status',
                                        TransferApplication::STATUS_APPROVED
                                    )
                                    ->count()
                                / $finalizedApplications
                            ) * 100,
                            1
                        )
                        : 0,
                'total_appeals' => $totalAppeals,
                'pending_appeals' => $pendingAppeals,
                'approved_appeals' => $approvedAppeals,
                'published_documents' => $publishedDocuments,
                'signed_documents' => $signedDocuments,
            ],

            'status_breakdown' => $statusBreakdown,
            'zone_breakdown' => $zoneBreakdown,
            'monthly_applications' => $monthlyApplications,
            'appeal_status_breakdown' => $appealStatusBreakdown,
            'document_breakdown' => $documentBreakdown,
            'recent_applications' => $recentApplications,
        ];
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
                fn (Builder $query) => $query->where(
                    'origin_zone_id',
                    $user->assigned_zone_id
                )
            );
    }

    private function appealQuery(
        User $user,
        array $filters
    ): Builder {
        return TransferAppeal::query()
            ->whereHas(
                'transferApplication',
                function (
                    Builder $query
                ) use (
                    $user,
                    $filters
                ): void {
                    if (
                        $filters['transfer_cycle_id']
                        ?? null
                    ) {
                        $query->where(
                            'transfer_cycle_id',
                            $filters[
                                'transfer_cycle_id'
                            ]
                        );
                    }

                    if ($filters['zone_id'] ?? null) {
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
                        $query->where(
                            'origin_zone_id',
                            $user->assigned_zone_id
                        );
                    }
                }
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
            );
    }

    private function documentQuery(
        User $user,
        array $filters
    ): Builder {
        return TransferDocument::query()
            ->whereHas(
                'transferApplication',
                function (
                    Builder $query
                ) use (
                    $user,
                    $filters
                ): void {
                    if (
                        $filters['transfer_cycle_id']
                                               ?? null
                    ) {
                        $query->where(
                            'transfer_cycle_id',
                            $filters[
                                'transfer_cycle_id'
                            ]
                        );
                    }

                    if ($filters['zone_id'] ?? null) {
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
                        $query->where(
                            'origin_zone_id',
                            $user->assigned_zone_id
                        );
                    }
                }
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
            );
    }

    private function humanize(
        string $value
    ): string {
        return str($value)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }
}
