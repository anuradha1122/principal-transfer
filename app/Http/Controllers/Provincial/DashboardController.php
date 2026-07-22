<?php

namespace App\Http\Controllers\Provincial;

use App\Http\Controllers\Controller;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use ReflectionClass;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        /** @var User|null $user */
        $user = request()->user();

        abort_unless(
            $user !== null,
            401
        );

        abort_unless(
            $user->hasAnyRole([
                'Provincial Director',
                'Super Admin',
            ]),
            403
        );

        abort_unless(
            $user->can(
                'view provincial dashboard'
            ),
            403
        );

        $statuses =
            $this->statusConstants();

        $applicationQuery =
            TransferApplication::query();

        $appealQuery =
            TransferAppeal::query();

        $summary = [
            'total_applications' =>
                (clone $applicationQuery)
                    ->count(),

            'awaiting_provincial_review' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_ZONAL_APPROVED',
                        'STATUS_PROVINCIAL_REVIEW',
                        'STATUS_UNDER_PROVINCIAL_REVIEW',
                    ],
                    [
                        'zonal_approved',
                        'provincial_review',
                        'under_provincial_review',
                    ]
                ),

            'provincial_approved' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_PROVINCIAL_APPROVED',
                    ],
                    [
                        'provincial_approved',
                    ]
                ),

            'provincial_rejected' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_PROVINCIAL_REJECTED',
                    ],
                    [
                        'provincial_rejected',
                    ]
                ),

            'returned_to_zone' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_RETURNED_TO_ZONE',
                    ],
                    [
                        'returned_to_zone',
                        'returned',
                    ]
                ),

            'pending_applications' =>
                $this->pendingCount(
                    clone $applicationQuery,
                    $statuses
                ),

            'appeals' =>
                (clone $appealQuery)
                    ->count(),

            'pending_appeals' =>
                $this->pendingAppealCount(
                    clone $appealQuery
                ),
        ];

        return Inertia::render(
            'Provincial/Dashboard/Index',
            [
                'summary' =>
                    $summary,

                'statusSummary' =>
                    $this->statusSummary(
                        clone $applicationQuery
                    ),

                'zoneSummary' =>
                    $this->zoneSummary(
                        clone $applicationQuery
                    ),

                'reviewQueue' =>
                    $this->reviewQueue(
                        clone $applicationQuery,
                        $statuses
                    ),

                'recentDecisions' =>
                    $this->recentDecisions(
                        clone $applicationQuery
                    ),

                'appealSummary' =>
                    $this->appealSummary(
                        clone $appealQuery
                    ),

                'oldestPending' =>
                    $this->oldestPending(
                        clone $applicationQuery,
                        $statuses
                    ),

                'recentNotifications' =>
                    $this->recentNotifications(
                        $user
                    ),

                'unreadNotificationCount' =>
                    $user
                        ->unreadNotifications()
                        ->count(),

                'permissions' => [
                    'viewApplications' =>
                        $user->can(
                            'view provincial transfer applications'
                        ),

                    'reviewApplications' =>
                        $user->can(
                            'review provincial transfer applications'
                        ),

                    'approveApplications' =>
                        $user->can(
                            'approve provincial transfer applications'
                        ),

                    'rejectApplications' =>
                        $user->can(
                            'reject provincial transfer applications'
                        ),

                    'returnApplications' =>
                        $user->can(
                            'return provincial transfer applications'
                        ),

                    'viewAppeals' =>
                        $user->can(
                            'view transfer appeals'
                        ),

                    'viewReports' =>
                        $user->can(
                            'view provincial reports'
                        )
                        || $user->can(
                            'view management reports'
                        ),

                    'viewAuditLogs' =>
                        $user->can(
                            'view audit logs'
                        ),
                ],
            ]
        );
    }

    private function reviewQueue(
        Builder $query,
        array $statuses
    ): array {
        $reviewStatuses =
            $this->resolveStatuses(
                $statuses,
                [
                    'STATUS_ZONAL_APPROVED',
                    'STATUS_PROVINCIAL_REVIEW',
                    'STATUS_UNDER_PROVINCIAL_REVIEW',
                ],
                [
                    'zonal_approved',
                    'provincial_review',
                    'under_provincial_review',
                ]
            );

        if ($reviewStatuses === []) {
            return [];
        }

        return $query
            ->whereIn(
                'status',
                $reviewStatuses
            )
            ->with([
                'principalProfile.user',
                'currentSchool',
                'originZone',
            ])
            ->orderByRaw(
                'COALESCE(submitted_at, created_at) asc'
            )
            ->limit(7)
            ->get()
            ->map(
                fn (
                    TransferApplication $application
                ): array => [
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

                    'status_label' =>
                        $this->statusLabel(
                            (string) $application->status
                        ),

                    'status_tone' =>
                        $this->statusTone(
                            (string) $application->status
                        ),

                    'submitted_at' =>
                        $this->formatDateTime(
                            $application->submitted_at
                            ?? $application->created_at
                        ),

                    'pending_days' =>
                        $this->pendingDays(
                            $application->submitted_at
                            ?? $application->created_at
                        ),

                    'show_url' =>
                        route(
                            'provincial.transfer-applications.show',
                            $application
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function recentDecisions(
        Builder $query
    ): array {
        return $query
            ->where(
                function (
                    Builder $builder
                ): void {
                    $builder
                        ->where(
                            'status',
                            'like',
                            '%provincial_approved%'
                        )
                        ->orWhere(
                            'status',
                            'like',
                            '%provincial_rejected%'
                        )
                        ->orWhere(
                            'status',
                            'like',
                            '%returned_to_zone%'
                        );
                }
            )
            ->with([
                'principalProfile.user',
                'currentSchool',
                'originZone',
            ])
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(
                fn (
                    TransferApplication $application
                ): array => [
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

                    'status_label' =>
                        $this->statusLabel(
                            (string) $application->status
                        ),

                    'status_tone' =>
                        $this->statusTone(
                            (string) $application->status
                        ),

                    'decided_at' =>
                        $this->formatDateTime(
                            $application->updated_at
                        ),

                    'show_url' =>
                        route(
                            'provincial.transfer-applications.show',
                            $application
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function oldestPending(
        Builder $query,
        array $statuses
    ): ?array {
        $terminalStatuses =
            $this->terminalStatuses(
                $statuses
            );

        if ($terminalStatuses !== []) {
            $query->whereNotIn(
                'status',
                $terminalStatuses
            );
        }

        $application = $query
            ->whereNotNull(
                'submitted_at'
            )
            ->with([
                'principalProfile.user',
                'currentSchool',
                'originZone',
            ])
            ->orderBy(
                'submitted_at'
            )
            ->first();

        if (! $application) {
            return null;
        }

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

            'status_label' =>
                $this->statusLabel(
                    (string) $application->status
                ),

            'pending_days' =>
                $this->pendingDays(
                    $application->submitted_at
                ),

            'submitted_at' =>
                $this->formatDateTime(
                    $application->submitted_at
                ),

            'show_url' =>
                route(
                    'provincial.transfer-applications.show',
                    $application
                ),
        ];
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
            ->select([
                'zones.id as zone_id',

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

    private function appealSummary(
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
                    TransferAppeal $appeal
                ): array => [
                    'key' =>
                        (string) $appeal->status,

                    'label' =>
                        $this->statusLabel(
                            (string) $appeal->status
                        ),

                    'value' =>
                        (int) $appeal->total,

                    'tone' =>
                        $this->statusTone(
                            (string) $appeal->status
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function pendingAppealCount(
        Builder $query
    ): int {
        return $query
            ->whereNotIn(
                'status',
                [
                    'approved',
                    'rejected',
                    'withdrawn',
                    'closed',
                ]
            )
            ->count();
    }

    private function recentNotifications(
        User $user
    ): array {
        return $user
            ->notifications()
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(
                function (
                    mixed $notification
                ): array {
                    $data =
                        $notification->data
                        ?? [];

                    return [
                        'id' =>
                            $notification->id,

                        'title' =>
                            $data['title']
                            ?? $data['subject']
                            ?? 'Notification',

                        'message' =>
                            $data['message']
                            ?? $data['body']
                            ?? 'A workflow notification is available.',

                        'is_read' =>
                            $notification->read_at
                            !== null,

                        'created_at' =>
                            $this->formatDateTime(
                                $notification->created_at
                            ),
                    ];
                }
            )
            ->values()
            ->all();
    }

    private function countByStatusCandidates(
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

    private function pendingCount(
        Builder $query,
        array $statuses
    ): int {
        $terminalStatuses =
            $this->terminalStatuses(
                $statuses
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

    private function terminalStatuses(
        array $statuses
    ): array {
        return collect(
            $statuses
        )
            ->filter(
                fn (
                    string $value,
                    string $name
                ): bool =>
                    str_contains(
                        $name,
                        'FINAL_APPROVED'
                    )
                    || str_contains(
                        $name,
                        'FINAL_REJECTED'
                    )
                    || str_contains(
                        $name,
                        'BOARD_APPROVED'
                    )
                    || str_contains(
                        $name,
                        'BOARD_REJECTED'
                    )
                    || str_contains(
                        $name,
                        'WITHDRAWN'
                    )
            )
            ->values()
            ->unique()
            ->all();
    }

    private function statusConstants(): array
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

    private function pendingDays(
        mixed $date
    ): int {
        if (! $date) {
            return 0;
        }

        try {
            return max(
                0,
                now()->diffInDays(
                    now()->parse(
                        $date
                    )
                )
            );
        } catch (\Throwable) {
            return 0;
        }
    }

    private function statusLabel(
        string $status
    ): string {
        return str($status)
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
        $status =
            strtolower($status);

        if (
            str_contains(
                $status,
                'reject'
            )
            || str_contains(
                $status,
                'withdraw'
            )
        ) {
            return 'red';
        }

        if (
            str_contains(
                $status,
                'approve'
            )
            || str_contains(
                $status,
                'complete'
            )
        ) {
            return 'emerald';
        }

        if (
            str_contains(
                $status,
                'return'
            )
            || str_contains(
                $status,
                'appeal'
            )
            || str_contains(
                $status,
                'wait'
            )
        ) {
            return 'violet';
        }

        if (
            str_contains(
                $status,
                'review'
            )
            || str_contains(
                $status,
                'submitted'
            )
            || str_contains(
                $status,
                'pending'
            )
        ) {
            return 'amber';
        }

        return 'blue';
    }

    private function formatDateTime(
        mixed $date
    ): ?string {
        if (! $date) {
            return null;
        }

        try {
            return now()
                ->parse($date)
                ->format(
                    'Y-m-d H:i'
                );
        } catch (\Throwable) {
            return (string) $date;
        }
    }
}
