<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\PrincipalAppointment;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
use App\Models\TransferCycle;
use App\Models\TransferDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
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
            $user->hasRole('Principal')
                && $user->can('view principal dashboard'),
            403
        );

        $profile = $user
            ->principalProfile()
            ->with([
                'currentAppointment.school.division.zone',
            ])
            ->first();

        $applicationQuery = $profile
            ? TransferApplication::query()
                ->where(
                    'principal_profile_id',
                    $profile->id
                )
            : TransferApplication::query()
                ->whereRaw('1 = 0');

        $appealQuery = $profile
            ? TransferAppeal::query()
                ->whereHas(
                    'transferApplication',
                    fn (Builder $query) => $query->where(
                        'principal_profile_id',
                        $profile->id
                    )
                )
            : TransferAppeal::query()
                ->whereRaw('1 = 0');

        $documentQuery = $profile
            ? TransferDocument::query()
                ->whereHas(
                    'transferApplication',
                    fn (Builder $query) => $query->where(
                        'principal_profile_id',
                        $profile->id
                    )
                )
            : TransferDocument::query()
                ->whereRaw('1 = 0');

        $latestApplication = (clone $applicationQuery)
            ->with([
                'transferCycle',
                'currentSchool.division.zone',
                'originZone',
            ])
            ->latest('created_at')
            ->first();

        $latestAppeal = (clone $appealQuery)
            ->with('transferApplication')
            ->latest('created_at')
            ->first();

        $latestDocument = (clone $documentQuery)
            ->latest('created_at')
            ->first();

        $summary = [
            'applications' =>
                (clone $applicationQuery)->count(),

            'active_applications' =>
                $this->activeApplicationCount(
                    clone $applicationQuery
                ),

            'appeals' =>
                (clone $appealQuery)->count(),

            'published_documents' =>
                $this->publishedDocumentCount(
                    clone $documentQuery
                ),
        ];

        return Inertia::render(
            'Principal/Dashboard/Index',
            [
                'summary' =>
                    $summary,

                'profile' =>
                    $this->profileData(
                        $profile
                    ),

                'appointment' =>
                    $this->appointmentData(
                        $profile?->currentAppointment
                    ),

                'activeCycle' =>
                    $this->activeTransferCycle(),

                'latestApplication' =>
                    $this->applicationData(
                        $latestApplication
                    ),

                'workflowSteps' =>
                    $this->workflowSteps(
                        $latestApplication
                    ),

                'latestAppeal' =>
                    $this->appealData(
                        $latestAppeal
                    ),

                'latestDocument' =>
                    $this->documentData(
                        $latestDocument
                    ),

                'recentApplications' =>
                    $this->recentApplications(
                        clone $applicationQuery
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
                    'createApplication' =>
                        $user->can(
                            'create transfer applications'
                        ),

                    'viewApplications' =>
                        $user->can(
                            'view own transfer applications'
                        ),

                    'viewAppeals' =>
                        $user->can(
                            'view own transfer appeals'
                        ),

                    'viewDocuments' =>
                        $user->can(
                            'view own transfer documents'
                        ),
                ],
            ]
        );
    }

    private function profileData(
        mixed $profile
    ): ?array {
        if (! $profile) {
            return null;
        }

        return [
            'id' =>
                $profile->id,

            'full_name' =>
                $profile->full_name
                ?? $profile->user?->name
                ?? request()->user()?->name,

            'nic' =>
                $profile->nic,

            'employee_number' =>
                $profile->employee_number,

            'service_grade' =>
                $profile->service_grade,

            'designation' =>
                $profile->current_designation
                ?? $profile
                    ->currentAppointment
                    ?->designation
                ?? 'Principal',

            'profile_complete' =>
                $this->isProfileComplete(
                    $profile
                ),

            'show_url' =>
                route(
                    'principal.profile.show'
                ),

            'edit_url' =>
                route(
                    'principal.profile.edit'
                ),
        ];
    }

    private function appointmentData(
        ?PrincipalAppointment $appointment
    ): ?array {
        if (! $appointment) {
            return null;
        }

        return [
            'id' =>
                $appointment->id,

            'school_name' =>
                $appointment
                    ->school
                    ?->name
                ?? 'School not assigned',

            'school_code' =>
                $appointment
                    ->school
                    ?->code,

            'division_name' =>
                $appointment
                    ->school
                    ?->division
                    ?->name,

            'zone_name' =>
                $appointment
                    ->school
                    ?->division
                    ?->zone
                    ?->name,

            'designation' =>
                $appointment->designation
                ?? 'Principal',

            'service_grade' =>
                $appointment->service_grade,

            'start_date' =>
                $this->formatDate(
                    $appointment->start_date
                ),

            'end_date' =>
                $this->formatDate(
                    $appointment->end_date
                ),

            'service_years' =>
                $this->serviceYears(
                    $appointment->start_date
                ),

            'edit_url' =>
                route(
                    'principal.appointments.edit',
                    $appointment
                ),
        ];
    }

    private function activeTransferCycle(): ?array
    {
        $query = TransferCycle::query();

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

        $cycle = $query
            ->latest('id')
            ->first();

        if (! $cycle) {
            return null;
        }

        $startDate =
            $cycle->application_start_date
            ?? $cycle->start_date
            ?? null;

        $endDate =
            $cycle->application_end_date
            ?? $cycle->end_date
            ?? null;

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
                    $startDate
                ),

            'application_end_date' =>
                $this->formatDate(
                    $endDate
                ),

            'is_open' =>
                $this->cycleIsOpen(
                    $startDate,
                    $endDate
                ),
        ];
    }

    private function applicationData(
        ?TransferApplication $application
    ): ?array {
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

            'transfer_reason' =>
                $application->transfer_reason,

            'current_school_name' =>
                $application
                    ->currentSchool
                    ?->name,

            'zone_name' =>
                $application
                    ->originZone
                    ?->name
                ?? $application
                    ->currentSchool
                    ?->division
                    ?->zone
                    ?->name,

            'cycle_name' =>
                $application
                    ->transferCycle
                    ?->name
                ?? $application
                    ->transferCycle
                    ?->title,

            'submitted_at' =>
                $this->formatDateTime(
                    $application->submitted_at
                ),

            'created_at' =>
                $this->formatDateTime(
                    $application->created_at
                ),

            'updated_at' =>
                $this->formatDateTime(
                    $application->updated_at
                ),

            'show_url' =>
                route(
                    'principal.transfer-applications.show',
                    $application
                ),

            'edit_url' =>
                $this->canEditApplication(
                    $application
                )
                    ? route(
                        'principal.transfer-applications.edit',
                        $application
                    )
                    : null,
        ];
    }

    private function appealData(
        ?TransferAppeal $appeal
    ): ?array {
        if (! $appeal) {
            return null;
        }

        return [
            'id' =>
                $appeal->id,

            'reference_number' =>
                $appeal->appeal_number
                ?? $appeal->reference_number
                ?? 'Appeal #'
                    . $appeal->id,

            'status' =>
                $appeal->status,

            'status_label' =>
                $this->statusLabel(
                    (string) $appeal->status
                ),

            'submitted_at' =>
                $this->formatDateTime(
                    $appeal->submitted_at
                    ?? $appeal->created_at
                ),

            'application_number' =>
                $appeal
                    ->transferApplication
                    ?->application_number,

            'show_url' =>
                route(
                    'principal.transfer-appeals.show',
                    $appeal
                ),
        ];
    }

    private function documentData(
        ?TransferDocument $document
    ): ?array {
        if (! $document) {
            return null;
        }

        return [
            'id' =>
                $document->id,

            'title' =>
                $document->title
                ?? $document->document_name
                ?? $document->document_type
                ?? 'Transfer Document',

            'document_number' =>
                $document->document_number
                ?? $document->reference_number,

            'is_published' =>
                $this->documentIsPublished(
                    $document
                ),

            'published_at' =>
                $this->formatDateTime(
                    $document->published_at
                ),

            'show_url' =>
                route(
                    'principal.transfer-documents.show',
                    $document
                ),

            'download_url' =>
                $this->documentIsPublished(
                    $document
                )
                    ? route(
                        'principal.transfer-documents.download',
                        $document
                    )
                    : null,
        ];
    }

    private function recentApplications(
        Builder $query
    ): array {
        return $query
            ->latest('created_at')
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

                    'status_label' =>
                        $this->statusLabel(
                            (string) $application->status
                        ),

                    'status_tone' =>
                        $this->statusTone(
                            (string) $application->status
                        ),

                    'transfer_reason' =>
                        $application->transfer_reason
                        ?? 'Transfer application',

                    'date' =>
                        $this->formatDateTime(
                            $application->created_at
                        ),

                    'show_url' =>
                        route(
                            'principal.transfer-applications.show',
                            $application
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
                            ?? 'A new workflow notification is available.',

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

    private function workflowSteps(
        ?TransferApplication $application
    ): array {
        $steps = [
            [
                'key' =>
                    'draft',

                'label' =>
                    'Draft',

                'description' =>
                    'Application prepared',
            ],
            [
                'key' =>
                    'submitted',

                'label' =>
                    'Submitted',

                'description' =>
                    'Sent for review',
            ],
            [
                'key' =>
                    'zonal',

                'label' =>
                    'Zonal Review',

                'description' =>
                    'Zone decision',
            ],
            [
                'key' =>
                    'provincial',

                'label' =>
                    'Provincial Review',

                'description' =>
                    'Province decision',
            ],
            [
                'key' =>
                    'board',

                'label' =>
                    'Transfer Board',

                'description' =>
                    'Final evaluation',
            ],
            [
                'key' =>
                    'final',

                'label' =>
                    'Final Decision',

                'description' =>
                    'Outcome recorded',
            ],
        ];

        if (! $application) {
            return collect($steps)
                ->map(
                    fn (array $step): array => [
                        ...$step,
                        'state' =>
                            'pending',
                    ]
                )
                ->all();
        }

        $position =
            $this->workflowPosition(
                (string) $application->status
            );

        $isRejected =
            str_contains(
                strtolower(
                    (string) $application->status
                ),
                'reject'
            );

        return collect($steps)
            ->map(
                function (
                    array $step,
                    int $index
                ) use (
                    $position,
                    $isRejected
                ): array {
                    if (
                        $isRejected
                        && $index === $position
                    ) {
                        $state = 'rejected';
                    } elseif (
                        $index < $position
                    ) {
                        $state = 'completed';
                    } elseif (
                        $index === $position
                    ) {
                        $state = 'current';
                    } else {
                        $state = 'pending';
                    }

                    return [
                        ...$step,
                        'state' =>
                            $state,
                    ];
                }
            )
            ->all();
    }

    private function workflowPosition(
        string $status
    ): int {
        $status = strtolower($status);

        if (
            str_contains(
                $status,
                'final'
            )
            || str_contains(
                $status,
                'board_approved'
            )
            || str_contains(
                $status,
                'board_rejected'
            )
            || str_contains(
                $status,
                'waitlist'
            )
        ) {
            return 5;
        }

        if (
            str_contains(
                $status,
                'board'
            )
        ) {
            return 4;
        }

        if (
            str_contains(
                $status,
                'provincial'
            )
        ) {
            return 3;
        }

        if (
            str_contains(
                $status,
                'zonal'
            )
            || str_contains(
                $status,
                'zone'
            )
        ) {
            return 2;
        }

        if (
            str_contains(
                $status,
                'submitted'
            )
        ) {
            return 1;
        }

        return 0;
    }

    private function activeApplicationCount(
        Builder $query
    ): int {
        $terminalStatuses =
            $this->terminalStatuses();

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

    private function publishedDocumentCount(
        Builder $query
    ): int {
        if (
            Schema::hasColumn(
                'transfer_documents',
                'is_published'
            )
        ) {
            return $query
                ->where(
                    'is_published',
                    true
                )
                ->count();
        }

        if (
            Schema::hasColumn(
                'transfer_documents',
                'published_at'
            )
        ) {
            return $query
                ->whereNotNull(
                    'published_at'
                )
                ->count();
        }

        if (
            Schema::hasColumn(
                'transfer_documents',
                'status'
            )
        ) {
            return $query
                ->where(
                    'status',
                    'published'
                )
                ->count();
        }

        return 0;
    }

    private function documentIsPublished(
        TransferDocument $document
    ): bool {
        if (
            Schema::hasColumn(
                'transfer_documents',
                'is_published'
            )
        ) {
            return (bool) $document->is_published;
        }

        if (
            Schema::hasColumn(
                'transfer_documents',
                'published_at'
            )
        ) {
            return $document->published_at !== null;
        }

        return $document->status === 'published';
    }

    private function terminalStatuses(): array
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
                    && (
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
                    && is_string($value)
            )
            ->values()
            ->unique()
            ->all();
    }

    private function canEditApplication(
        TransferApplication $application
    ): bool {
        return in_array(
            strtolower(
                (string) $application->status
            ),
            [
                'draft',
                'returned',
                'returned_to_principal',
            ],
            true
        );
    }

    private function isProfileComplete(
        mixed $profile
    ): bool {
        return filled(
            $profile->nic
        )
            && filled(
                $profile->employee_number
            )
            && filled(
                $profile->service_grade
            )
            && $profile->currentAppointment !== null;
    }

    private function cycleIsOpen(
        mixed $startDate,
        mixed $endDate
    ): bool {
        try {
            $now = now();

            if (
                $startDate
                && $now->lt(
                    $now->copy()->parse(
                        $startDate
                    )->startOfDay()
                )
            ) {
                return false;
            }

            if (
                $endDate
                && $now->gt(
                    $now->copy()->parse(
                        $endDate
                    )->endOfDay()
                )
            ) {
                return false;
            }

            return true;
        } catch (\Throwable) {
            return true;
        }
    }

    private function serviceYears(
        mixed $startDate
    ): ?int {
        if (! $startDate) {
            return null;
        }

        try {
            return now()
                ->diffInYears(
                    now()->parse(
                        $startDate
                    )
                );
        } catch (\Throwable) {
            return null;
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
        $status = strtolower($status);

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
                'publish'
            )
        ) {
            return 'emerald';
        }

        if (
            str_contains(
                $status,
                'wait'
            )
            || str_contains(
                $status,
                'appeal'
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

    private function formatDateTime(
        mixed $date
    ): ?string {
        if (! $date) {
            return null;
        }

        try {
            return now()
                ->parse($date)
                ->format('Y-m-d H:i');
        } catch (\Throwable) {
            return (string) $date;
        }
    }
}
