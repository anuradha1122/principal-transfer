<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PrincipalProfile;
use App\Models\School;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use ReflectionClass;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();

        abort_unless(
            $user !== null,
            401
        );

        abort_unless(
            $user->can(
                'view admin dashboard'
            ),
            403
        );

        $statuses =
            $this->transferApplicationStatuses();

        $applicationQuery =
            TransferApplication::query();

        $summary = [
            'total_users' =>
                User::query()->count(),

            'active_users' =>
                $this->activeUserCount(),

            'principal_profiles' =>
                PrincipalProfile::query()
                    ->count(),

            'zones' =>
                Zone::query()->count(),

            'schools' =>
                School::query()->count(),

            'active_cycles' =>
                $this->activeTransferCycleCount(),

            'total_applications' =>
                (clone $applicationQuery)
                    ->count(),

            'pending_applications' =>
                $this->pendingApplicationCount(
                    clone $applicationQuery,
                    $statuses
                ),

            'final_approved' =>
                $this->applicationCountByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_FINAL_APPROVED',
                        'STATUS_BOARD_APPROVED',
                        'STATUS_TRANSFER_APPROVED',
                    ],
                    [
                        'final_approved',
                        'board_approved',
                        'transfer_approved',
                        'approved',
                    ]
                ),

            'final_rejected' =>
                $this->applicationCountByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_FINAL_REJECTED',
                        'STATUS_BOARD_REJECTED',
                        'STATUS_TRANSFER_REJECTED',
                    ],
                    [
                        'final_rejected',
                        'board_rejected',
                        'transfer_rejected',
                        'rejected',
                    ]
                ),

            'appeals' =>
                TransferAppeal::query()
                    ->count(),
        ];

        return Inertia::render(
            'Admin/Dashboard/Index',
            [
                'summary' =>
                    $summary,

                'activeCycle' =>
                    $this->activeTransferCycle(),

                'statusSummary' =>
                    $this->statusSummary(
                        clone $applicationQuery
                    ),

                'zoneSummary' =>
                    $this->zoneSummary(
                        clone $applicationQuery
                    ),

                'recentApplications' =>
                    $this->recentApplications(),

                'recentUsers' =>
                    $this->recentUsers(),

                'recentAuditLogs' =>
                    $this->recentAuditLogs(),

                'systemAlerts' =>
                    $this->systemAlerts(
                        $summary,
                        $statuses
                    ),

                'quickActionPermissions' => [
                    'manageUsers' =>
                        $user->can(
                            'view users'
                        )
                        || $user->can(
                            'manage users'
                        ),

                    'viewRegistry' =>
                        $user->can(
                            'view principal registry'
                        )
                        || $user->hasRole(
                            'Super Admin'
                        ),

                    'manageCycles' =>
                        $user->can(
                            'view transfer cycles'
                        )
                        || $user->can(
                            'manage transfer cycles'
                        )
                        || $user->hasRole(
                            'Super Admin'
                        ),

                    'viewApplications' =>
                        $user->can(
                            'view transfer applications'
                        ),

                    'viewReports' =>
                        $user->can(
                            'view management reports'
                        )
                        || $user->can(
                            'view reports'
                        ),

                    'viewAuditLogs' =>
                        $user->can(
                            'view audit logs'
                        ),
                ],
            ]
        );
    }

    private function activeUserCount(): int
    {
        if (
            Schema::hasColumn(
                'users',
                'is_active'
            )
        ) {
            return User::query()
                ->where(
                    'is_active',
                    true
                )
                ->count();
        }

        return User::query()->count();
    }

    private function activeTransferCycleCount(): int
    {
        $query =
            TransferCycle::query();

        if (
            Schema::hasColumn(
                'transfer_cycles',
                'status'
            )
        ) {
            return $query
                ->where(
                    'status',
                    'active'
                )
                ->count();
        }

        if (
            Schema::hasColumn(
                'transfer_cycles',
                'is_active'
            )
        ) {
            return $query
                ->where(
                    'is_active',
                    true
                )
                ->count();
        }

        return $query->count();
    }

    private function activeTransferCycle(): ?array
    {
        $query =
            TransferCycle::query();

        if (
            Schema::hasColumn(
                'transfer_cycles',
                'status'
            )
        ) {
            $query->where(
                'status',
                'active'
            );
        } elseif (
            Schema::hasColumn(
                'transfer_cycles',
                'is_active'
            )
        ) {
            $query->where(
                'is_active',
                true
            );
        }

        $cycle =
            $query
                ->latest('id')
                ->first();

        if (! $cycle) {
            return null;
        }

        return [
            'id' =>
                $cycle->id,

            'name' =>
                $cycle->name
                ?? $cycle->title
                ?? $cycle->code
                ?? 'Transfer Cycle #'
                    . $cycle->id,

            'status' =>
                $cycle->status
                ?? (
                    $cycle->is_active
                        ? 'active'
                        : 'inactive'
                ),

            'application_start_date' =>
                $this->formatDate(
                    $cycle->application_start_date
                    ?? $cycle->start_date
                    ?? null
                ),

            'application_end_date' =>
                $this->formatDate(
                    $cycle->application_end_date
                    ?? $cycle->end_date
                    ?? null
                ),
        ];
    }

    private function pendingApplicationCount(
        Builder $query,
        array $statuses
    ): int {
        $terminalStatuses =
            $this->resolveStatuses(
                $statuses,
                [
                    'STATUS_FINAL_APPROVED',
                    'STATUS_BOARD_APPROVED',
                    'STATUS_TRANSFER_APPROVED',
                    'STATUS_FINAL_REJECTED',
                    'STATUS_BOARD_REJECTED',
                    'STATUS_TRANSFER_REJECTED',
                    'STATUS_WITHDRAWN',
                ],
                [
                    'final_approved',
                    'board_approved',
                    'transfer_approved',
                    'approved',
                    'final_rejected',
                    'board_rejected',
                    'transfer_rejected',
                    'rejected',
                    'withdrawn',
                ]
            );

        if ($terminalStatuses === []) {
            return $query->count();
        }

        return $query
            ->whereNotIn(
                'status',
                $terminalStatuses
            )
            ->count();
    }

    private function statusSummary(
        Builder $query
    ): array {
        return $query
            ->selectRaw(
                'status, COUNT(*) as total'
            )
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(
                fn (
                    TransferApplication $application
                ): array => [
                    'key' =>
                        (string) $application->status,

                    'label' =>
                        $this->statusLabel(
                            (string) $application->status
                        ),

                    'value' =>
                        (int) $application->total,

                    'tone' =>
                        $this->statusTone(
                            (string) $application->status
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function zoneSummary(
        Builder $query
    ): array {
        return $query
            ->leftJoin(
                'zones',
                'zones.id',
                '=',
                'transfer_applications.origin_zone_id'
            )
            ->selectRaw(
                "
                zones.id as zone_id,
                COALESCE(zones.name, 'Unassigned') as zone_name,
                COUNT(transfer_applications.id) as total
                "
            )
            ->groupBy(
                'zones.id',
                'zones.name'
            )
            ->orderByDesc('total')
            ->limit(7)
            ->get()
            ->map(
                fn (
                    object $row
                ): array => [
                    'zone_id' =>
                        $row->zone_id
                            ? (int) $row->zone_id
                            : null,

                    'zone_name' =>
                        (string) $row->zone_name,

                    'total' =>
                        (int) $row->total,
                ]
            )
            ->values()
            ->all();
    }

    private function recentApplications(): array
    {
        return TransferApplication::query()
            ->with([
                'principalProfile.user',
                'currentSchool',
                'originZone',
            ])
            ->latest(
                'created_at'
            )
            ->limit(6)
            ->get()
            ->map(
                function (
                    TransferApplication $application
                ): array {
                    return [
                        'id' =>
                            $application->id,

                        'application_number' =>
                            $application->application_number
                            ?? 'Application #'
                                . $application->id,

                        'principal_name' =>
                            $application->principal_name
                            ?? $application
                                ->principalProfile
                                ?->full_name
                            ?? $application
                                ->principalProfile
                                ?->user
                                ?->name
                            ?? 'Unknown Principal',

                        'school_name' =>
                            $application
                                ->currentSchool
                                ?->name
                            ?? 'School not assigned',

                        'zone_name' =>
                            $application
                                ->originZone
                                ?->name
                            ?? 'Zone not assigned',

                        'status' =>
                            $application->status,

                        'status_label' =>
                            $this->statusLabel(
                                (string) $application->status
                            ),

                        'status_tone' =>
                            $this->statusTone(
                                (string) $application->status
                            ),

                        'created_at' =>
                            $application
                                ->created_at
                                ?->format(
                                    'Y-m-d H:i'
                                ),

                        'show_url' =>
                            route(
                                'admin.transfer-applications.show',
                                $application
                            ),
                    ];
                }
            )
            ->values()
            ->all();
    }

    private function recentUsers(): array
    {
        return User::query()
            ->with('roles')
            ->latest(
                'created_at'
            )
            ->limit(5)
            ->get()
            ->map(
                fn (
                    User $user
                ): array => [
                    'id' =>
                        $user->id,

                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,

                    'role' =>
                        $user
                            ->roles
                            ->pluck('name')
                            ->implode(', ')
                        ?: 'No role assigned',

                    'is_active' =>
                        Schema::hasColumn(
                            'users',
                            'is_active'
                        )
                            ? (bool) $user->is_active
                            : true,

                    'created_at' =>
                        $user
                            ->created_at
                            ?->format(
                                'Y-m-d H:i'
                            ),

                    'show_url' =>
                        route(
                            'admin.users.show',
                            $user
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function recentAuditLogs(): array
    {
        if (
            ! class_exists(
                AuditLog::class
            )
            || ! Schema::hasTable(
                'audit_logs'
            )
        ) {
            return [];
        }

        return AuditLog::query()
            ->latest(
                'created_at'
            )
            ->limit(5)
            ->get()
            ->map(
                fn (
                    AuditLog $log
                ): array => [
                    'id' =>
                        $log->id,

                    'action' =>
                        $log->action
                        ?? $log->event
                        ?? 'System activity',

                    'description' =>
                        $log->description
                        ?? $log->message
                        ?? $log->auditable_type
                        ?? 'Audit activity recorded.',

                    'user_name' =>
                        $log->user_name
                        ?? $log->actor_name
                        ?? 'System',

                    'created_at' =>
                        $log
                            ->created_at
                            ?->format(
                                'Y-m-d H:i'
                            ),

                    'show_url' =>
                        route(
                            'admin.audit-logs.show',
                            $log
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function systemAlerts(
        array $summary,
        array $statuses
    ): array {
        $alerts = [];

        if (
            ($summary['active_cycles'] ?? 0) === 0
        ) {
            $alerts[] = [
                'id' =>
                    'no-active-cycle',

                'title' =>
                    'No active transfer cycle',

                'description' =>
                    'Create or activate a transfer cycle before applications are expected.',

                'tone' =>
                    'red',

                'href' =>
                    route(
                        'admin.transfer-cycles.index'
                    ),
            ];
        }

        if (
            ($summary['pending_applications'] ?? 0) > 0
        ) {
            $alerts[] = [
                'id' =>
                    'pending-applications',

                'title' =>
                    number_format(
                        $summary['pending_applications']
                    )
                    . ' applications are still pending',

                'description' =>
                    'Applications remain within the approval workflow and may require attention.',

                'tone' =>
                    'amber',

                'href' =>
                    route(
                        'admin.transfer-applications.index'
                    ),
            ];
        }

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

        if ($submittedStatus) {
            $submittedCount =
                TransferApplication::query()
                    ->where(
                        'status',
                        $submittedStatus
                    )
                    ->count();

            if ($submittedCount > 0) {
                $alerts[] = [
                    'id' =>
                        'submitted-applications',

                    'title' =>
                        number_format(
                            $submittedCount
                        )
                        . ' newly submitted applications',

                    'description' =>
                        'These applications have entered the formal review workflow.',

                    'tone' =>
                        'blue',

                    'href' =>
                        route(
                            'admin.transfer-applications.index',
                            [
                                'status' =>
                                    $submittedStatus,
                            ]
                        ),
                ];
            }
        }

        if (
            ($summary['appeals'] ?? 0) > 0
        ) {
            $alerts[] = [
                'id' =>
                    'appeals',

                'title' =>
                    number_format(
                        $summary['appeals']
                    )
                    . ' transfer appeals recorded',

                'description' =>
                    'Appeal workload should be monitored by authorized Provincial and Board users.',

                'tone' =>
                    'violet',

                'href' =>
                    route(
                        'transfer-board.transfer-appeals.index'
                    ),
            ];
        }

        return array_slice(
            $alerts,
            0,
            4
        );
    }

    private function applicationCountByStatusCandidates(
        Builder $query,
        array $statuses,
        array $constantNames,
        array $fallbackValues
    ): int {
        $resolved =
            $this->resolveStatuses(
                $statuses,
                $constantNames,
                $fallbackValues
            );

        if ($resolved === []) {
            return 0;
        }

        return $query
            ->whereIn(
                'status',
                $resolved
            )
            ->count();
    }

    private function transferApplicationStatuses(): array
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

    private function resolveStatuses(
        array $statuses,
        array $constantNames,
        array $fallbackValues
    ): array {
        $resolved = [];

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
                $resolved[] =
                    $statuses[
                        $constantName
                    ];
            }
        }

        $knownValues =
            array_values(
                $statuses
            );

        foreach (
            $fallbackValues as $fallbackValue
        ) {
            if (
                in_array(
                    $fallbackValue,
                    $knownValues,
                    true
                )
            ) {
                $resolved[] =
                    $fallbackValue;
            }
        }

        return array_values(
            array_unique(
                $resolved
            )
        );
    }

    private function resolveStatus(
        array $statuses,
        array $constantNames,
        array $fallbackValues
    ): ?string {
        return $this->resolveStatuses(
            $statuses,
            $constantNames,
            $fallbackValues
        )[0] ?? null;
    }

    private function statusLabel(
        string $status
    ): string {
        return str(
            $status
        )
            ->replace(
                [
                    '_',
                    '-',
                ],
                ' '
            )
            ->title()
            ->toString();
    }

    private function statusTone(
        string $status
    ): string {
        $normalized =
            strtolower($status);

        if (
            str_contains(
                $normalized,
                'reject'
            )
            || str_contains(
                $normalized,
                'withdraw'
            )
        ) {
            return 'red';
        }

        if (
            str_contains(
                $normalized,
                'approve'
            )
            || str_contains(
                $normalized,
                'publish'
            )
            || str_contains(
                $normalized,
                'complete'
            )
        ) {
            return 'emerald';
        }

        if (
            str_contains(
                $normalized,
                'wait'
            )
            || str_contains(
                $normalized,
                'appeal'
            )
        ) {
            return 'violet';
        }

        if (
            str_contains(
                $normalized,
                'review'
            )
            || str_contains(
                $normalized,
                'pending'
            )
            || str_contains(
                $normalized,
                'submitted'
            )
        ) {
            return 'amber';
        }

        return 'blue';
    }

    private function formatDate(
        mixed $date
    ): ?string {
        if (! $date) {
            return null;
        }

        try {
            return now()
                ->parse($date)
                ->format('Y-m-d');
        } catch (\Throwable) {
            return (string) $date;
        }
    }
}
