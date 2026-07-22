<?php

namespace App\Http\Controllers\Zonal;

use App\Http\Controllers\Controller;
use App\Models\TransferApplication;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
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
                'Zonal Director',
                'Super Admin',
            ]),
            403
        );

        abort_unless(
            $user->can(
                'view zonal dashboard'
            ),
            403
        );

        $zoneId = $this->resolveZoneId(
            $user
        );

        abort_unless(
            $zoneId !== null,
            403,
            'No Zone is assigned to this account.'
        );

        $zone = Zone::query()
            ->withCount([
                'divisions',
            ])
            ->findOrFail(
                $zoneId
            );

        $applicationQuery =
            TransferApplication::query()
                ->where(
                    'origin_zone_id',
                    $zoneId
                );

        $statuses =
            $this->statusConstants();

        $summary = [
            'total_applications' =>
                (clone $applicationQuery)
                    ->count(),

            'awaiting_zonal_review' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_SUBMITTED',
                        'STATUS_ZONAL_REVIEW',
                        'STATUS_UNDER_ZONAL_REVIEW',
                    ],
                    [
                        'submitted',
                        'zonal_review',
                        'under_zonal_review',
                    ]
                ),

            'zonal_approved' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_ZONAL_APPROVED',
                    ],
                    [
                        'zonal_approved',
                    ]
                ),

            'zonal_rejected' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_ZONAL_REJECTED',
                    ],
                    [
                        'zonal_rejected',
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
        ];

        return Inertia::render(
            'Zonal/Dashboard/Index',
            [
                'zone' => [
                    'id' =>
                        $zone->id,

                    'name' =>
                        $zone->name,

                    'code' =>
                        $zone->code,

                    'division_count' =>
                        $zone->divisions_count,

                    'school_count' =>
                        $this->schoolCount(
                            $zoneId
                        ),
                ],

                'summary' =>
                    $summary,

                'statusSummary' =>
                    $this->statusSummary(
                        clone $applicationQuery
                    ),

                'pendingApplications' =>
                    $this->pendingApplications(
                        clone $applicationQuery,
                        $statuses
                    ),

                'recentDecisions' =>
                    $this->recentDecisions(
                        clone $applicationQuery
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
                            'view zonal transfer applications'
                        ),

                    'reviewApplications' =>
                        $user->can(
                            'review zonal transfer applications'
                        ),

                    'approveApplications' =>
                        $user->can(
                            'approve zonal transfer applications'
                        ),

                    'rejectApplications' =>
                        $user->can(
                            'reject zonal transfer applications'
                        ),

                    'viewReports' =>
                        $user->can(
                            'view zonal reports'
                        )
                        || $user->can(
                            'view management reports'
                        ),
                ],
            ]
        );
    }

    private function resolveZoneId(
        User $user
    ): ?int {
        if (
            $user->hasRole(
                'Super Admin'
            )
        ) {
            $requestedZoneId =
                request()->integer(
                    'zone_id'
                );

            if ($requestedZoneId > 0) {
                return $requestedZoneId;
            }

            return Zone::query()
                ->orderBy('id')
                ->value('id');
        }

        return $user->assigned_zone_id
            ? (int) $user->assigned_zone_id
            : null;
    }

    private function schoolCount(
        int $zoneId
    ): int {
        return \App\Models\School::query()
            ->whereHas(
                'division',
                fn (
                    Builder $query
                ) => $query->where(
                    'zone_id',
                    $zoneId
                )
            )
            ->count();
    }

    private function pendingApplications(
        Builder $query,
        array $statuses
    ): array {
        $reviewStatuses =
            $this->resolveStatuses(
                $statuses,
                [
                    'STATUS_SUBMITTED',
                    'STATUS_ZONAL_REVIEW',
                    'STATUS_UNDER_ZONAL_REVIEW',
                    'STATUS_RETURNED_TO_ZONE',
                ],
                [
                    'submitted',
                    'zonal_review',
                    'under_zonal_review',
                    'returned_to_zone',
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
                            'zonal.transfer-applications.show',
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
                            '%zonal_approved%'
                        )
                        ->orWhere(
                            'status',
                            'like',
                            '%zonal_rejected%'
                        );
                }
            )
            ->with([
                'principalProfile.user',
                'currentSchool',
            ])
            ->latest('updated_at')
            ->limit(5)
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
                            'zonal.transfer-applications.show',
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
                    'zonal.transfer-applications.show',
                    $application
                ),
        ];
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
        ) {
            return 'red';
        }

        if (
            str_contains(
                $status,
                'approve'
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
