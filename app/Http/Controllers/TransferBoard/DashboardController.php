<?php

namespace App\Http\Controllers\TransferBoard;

use App\Http\Controllers\Controller;
use App\Models\TransferAppeal;
use App\Models\TransferApplication;
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
            $user->hasAnyRole([
                'Transfer Board Member',
                'Super Admin',
            ]),
            403
        );

        abort_unless(
            $user->can(
                'view transfer board dashboard'
            ),
            403
        );

        $statuses =
            $this->statusConstants();

        $applicationQuery =
            TransferApplication::query();

        $appealQuery =
            TransferAppeal::query();

        $documentQuery =
            TransferDocument::query();

        $summary = [
            'total_applications' =>
                (clone $applicationQuery)
                    ->count(),

            'awaiting_board_review' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_PROVINCIAL_APPROVED',
                        'STATUS_BOARD_REVIEW',
                        'STATUS_TRANSFER_BOARD_REVIEW',
                        'STATUS_UNDER_BOARD_REVIEW',
                    ],
                    [
                        'provincial_approved',
                        'board_review',
                        'transfer_board_review',
                        'under_board_review',
                    ]
                ),

            'final_approved' =>
                $this->countByStatusCandidates(
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
                $this->countByStatusCandidates(
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

            'waitlisted' =>
                $this->countByStatusCandidates(
                    clone $applicationQuery,
                    $statuses,
                    [
                        'STATUS_WAITLISTED',
                        'STATUS_WAITLIST',
                    ],
                    [
                        'waitlisted',
                        'waitlist',
                    ]
                ),

            'appeals' =>
                (clone $appealQuery)
                    ->count(),

            'pending_appeals' =>
                $this->pendingAppealCount(
                    clone $appealQuery
                ),

            'published_documents' =>
                $this->publishedDocumentCount(
                    clone $documentQuery
                ),

            'unpublished_documents' =>
                $this->unpublishedDocumentCount(
                    clone $documentQuery
                ),
        ];

        return Inertia::render(
            'TransferBoard/Dashboard/Index',
            [
                'summary' =>
                    $summary,

                'statusSummary' =>
                    $this->statusSummary(
                        clone $applicationQuery
                    ),

                'boardReviewQueue' =>
                    $this->boardReviewQueue(
                        clone $applicationQuery,
                        $statuses
                    ),

                'waitlistedApplications' =>
                    $this->waitlistedApplications(
                        clone $applicationQuery,
                        $statuses
                    ),

                'recentDecisions' =>
                    $this->recentDecisions(
                        clone $applicationQuery,
                        $statuses
                    ),

                'appealSummary' =>
                    $this->appealSummary(
                        clone $appealQuery
                    ),

                'appealQueue' =>
                    $this->appealQueue(
                        clone $appealQuery
                    ),

                'documentSummary' =>
                    $this->documentSummary(
                        clone $documentQuery
                    ),

                'recentDocuments' =>
                    $this->recentDocuments(
                        clone $documentQuery
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
                            'view board transfer applications'
                        ),

                    'reviewApplications' =>
                        $user->can(
                            'review board transfer applications'
                        ),

                    'recordDecisions' =>
                        $user->can(
                            'record transfer board decisions'
                        ),

                    'viewAppeals' =>
                        $user->can(
                            'view transfer appeals'
                        ),

                    'reviewAppeals' =>
                        $user->can(
                            'review transfer appeals'
                        ),

                    'viewDocuments' =>
                        $user->can(
                            'view transfer documents'
                        ),

                    'publishDocuments' =>
                        $user->can(
                            'publish transfer results'
                        ),

                    'viewReports' =>
                        $user->can(
                            'view transfer board reports'
                        )
                        || $user->can(
                            'view management reports'
                        ),
                ],
            ]
        );
    }

    private function boardReviewQueue(
        Builder $query,
        array $statuses
    ): array {
        $reviewStatuses =
            $this->resolveStatuses(
                $statuses,
                [
                    'STATUS_PROVINCIAL_APPROVED',
                    'STATUS_BOARD_REVIEW',
                    'STATUS_TRANSFER_BOARD_REVIEW',
                    'STATUS_UNDER_BOARD_REVIEW',
                ],
                [
                    'provincial_approved',
                    'board_review',
                    'transfer_board_review',
                    'under_board_review',
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
                        $this->principalName(
                            $application
                        ),

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
                            'transfer-board.transfer-applications.show',
                            $application
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function waitlistedApplications(
        Builder $query,
        array $statuses
    ): array {
        $waitlistStatuses =
            $this->resolveStatuses(
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

        if ($waitlistStatuses === []) {
            return [];
        }

        return $query
            ->whereIn(
                'status',
                $waitlistStatuses
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
                        $this->principalName(
                            $application
                        ),

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

                    'updated_at' =>
                        $this->formatDateTime(
                            $application->updated_at
                        ),

                    'show_url' =>
                        route(
                            'transfer-board.transfer-applications.show',
                            $application
                        ),
                ]
            )
            ->values()
            ->all();
    }

    private function recentDecisions(
        Builder $query,
        array $statuses
    ): array {
        $decisionStatuses =
            $this->resolveStatuses(
                $statuses,
                [
                    'STATUS_FINAL_APPROVED',
                    'STATUS_BOARD_APPROVED',
                    'STATUS_TRANSFER_APPROVED',
                    'STATUS_FINAL_REJECTED',
                    'STATUS_BOARD_REJECTED',
                    'STATUS_TRANSFER_REJECTED',
                    'STATUS_WAITLISTED',
                    'STATUS_WAITLIST',
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
                    'waitlisted',
                    'waitlist',
                ]
            );

        if ($decisionStatuses === []) {
            return [];
        }

        return $query
            ->whereIn(
                'status',
                $decisionStatuses
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
                        $this->principalName(
                            $application
                        ),

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
                            'transfer-board.transfer-applications.show',
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

        $application =
            $query
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
                $this->principalName(
                    $application
                ),

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
                    'transfer-board.transfer-applications.show',
                    $application
                ),
        ];
    }

    private function appealQueue(
        Builder $query
    ): array {
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
            ->with([
                'transferApplication.principalProfile.user',
            ])
            ->orderByRaw(
                'COALESCE(submitted_at, created_at) asc'
            )
            ->limit(6)
            ->get()
            ->map(
                fn (
                    TransferAppeal $appeal
                ): array => [
                    'id' =>
                        $appeal->id,

                    'appeal_number' =>
                        $appeal->appeal_number
                        ?? $appeal->reference_number
                        ?? 'Appeal #'
                            . $appeal->id,

                    'application_number' =>
                        $appeal
                            ->transferApplication
                            ?->application_number
                        ?? 'Application not available',

                    'principal_name' =>
                        $appeal
                            ->transferApplication
                            ?->principal_name
                        ?? $appeal
                            ->transferApplication
                            ?->principalProfile
                            ?->full_name
                        ?? $appeal
                            ->transferApplication
                            ?->principalProfile
                            ?->user
                            ?->name
                        ?? 'Unknown Principal',

                    'status_label' =>
                        $this->statusLabel(
                            (string) $appeal->status
                        ),

                    'status_tone' =>
                        $this->statusTone(
                            (string) $appeal->status
                        ),

                    'submitted_at' =>
                        $this->formatDateTime(
                            $appeal->submitted_at
                            ?? $appeal->created_at
                        ),

                    'pending_days' =>
                        $this->pendingDays(
                            $appeal->submitted_at
                            ?? $appeal->created_at
                        ),

                    'show_url' =>
                        route(
                            'transfer-board.transfer-appeals.show',
                            $appeal
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

    private function documentSummary(
        Builder $query
    ): array {
        $total =
            (clone $query)
                ->count();

        $published =
            $this->publishedDocumentCount(
                clone $query
            );

        return [
            [
                'key' =>
                    'published',

                'label' =>
                    'Published',

                'value' =>
                    $published,

                'tone' =>
                    'emerald',
            ],
            [
                'key' =>
                    'unpublished',

                'label' =>
                    'Awaiting Publication',

                'value' =>
                    max(
                        0,
                        $total - $published
                    ),

                'tone' =>
                    'amber',
            ],
        ];
    }

    private function recentDocuments(
        Builder $query
    ): array {
        return $query
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(
                fn (
                    TransferDocument $document
                ): array => [
                    'id' =>
                        $document->id,

                    'title' =>
                        $document->title
                        ?? $document->document_name
                        ?? $document->document_type
                        ?? 'Transfer Document',

                    'document_number' =>
                        $document->document_number
                        ?? $document->reference_number
                        ?? 'Document #'
                            . $document->id,

                    'is_published' =>
                        $this->documentIsPublished(
                            $document
                        ),

                    'created_at' =>
                        $this->formatDateTime(
                            $document->created_at
                        ),

                    'show_url' =>
                        route(
                            'admin.transfer-documents.show',
                            $document
                        ),
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

    private function unpublishedDocumentCount(
        Builder $query
    ): int {
        return max(
            0,
            $query->count()
            - $this->publishedDocumentCount(
                clone $query
            )
        );
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
                        'WAITLIST'
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
                    && $value !== ''
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

    private function principalName(
        TransferApplication $application
    ): string {
        return $application->principal_name
            ?? $application
                ->principalProfile
                ?->full_name
            ?? $application
                ->principalProfile
                ?->user
                ?->name
            ?? 'Unknown Principal';
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
                'publish'
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
