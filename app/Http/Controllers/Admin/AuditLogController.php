<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('view audit logs'),
            403
        );

        $filters = [
            'search' => trim(
                (string) $request->input('search')
            ),
            'category' => $request->input('category'),
            'event' => $request->input('event'),
            'user_id' => $request->input('user_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $auditLogs = AuditLog::query()
            ->with([
                'actor:id,name,email',
            ])
            ->when(
                $filters['search'],
                function (
                    Builder $query,
                    string $search
                ): void {
                    $query->where(
                        function (
                            Builder $innerQuery
                        ) use ($search): void {
                            $innerQuery
                                ->where(
                                    'event',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'description',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'actor_name',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'actor_email',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'request_id',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'route_name',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'auditable_type',
                                    'like',
                                    '%'.$search.'%'
                                );
                        }
                    );
                }
            )
            ->when(
                $filters['category'],
                fn (
                    Builder $query,
                    string $category
                ) => $query->where(
                    'category',
                    $category
                )
            )
            ->when(
                $filters['event'],
                fn (
                    Builder $query,
                    string $event
                ) => $query->where(
                    'event',
                    $event
                )
            )
            ->when(
                $filters['user_id'],
                fn (
                    Builder $query,
                    string|int $userId
                ) => $query->where(
                    'user_id',
                    $userId
                )
            )
            ->when(
                $filters['date_from'],
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'occurred_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $filters['date_to'],
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'occurred_at',
                    '<=',
                    $date
                )
            )
            ->latest('occurred_at')
            ->latest('id')
            ->paginate(25)
            ->withQueryString()
            ->through(
                fn (AuditLog $auditLog): array => [
                    'id' => $auditLog->id,
                    'request_id' => $auditLog->request_id,
                    'category' => $auditLog->category,
                    'event' => $auditLog->event,
                    'description' => $auditLog->description,
                    'actor_name' => $auditLog->actor_name
                        ?? $auditLog->actor?->name
                        ?? 'System',
                    'actor_email' => $auditLog->actor_email
                        ?? $auditLog->actor?->email,
                    'actor_roles' => $auditLog->actor_roles ?? [],
                    'old_status' => $auditLog->old_status,
                    'new_status' => $auditLog->new_status,
                    'auditable_type' => $auditLog->auditable_type
                            ? class_basename(
                                $auditLog->auditable_type
                            )
                            : null,
                    'auditable_id' => $auditLog->auditable_id,
                    'route_name' => $auditLog->route_name,
                    'ip_address' => $auditLog->ip_address,
                    'occurred_at' => $auditLog->occurred_at
                        ?->toIso8601String(),
                ]
            );

        return Inertia::render(
            'Admin/AuditLogs/Index',
            [
                'auditLogs' => $auditLogs,
                'filters' => $filters,

                'categories' => AuditLog::query()
                    ->select('category')
                    ->whereNotNull('category')
                    ->distinct()
                    ->orderBy('category')
                    ->pluck('category')
                    ->values(),

                'events' => AuditLog::query()
                    ->select('event')
                    ->whereNotNull('event')
                    ->distinct()
                    ->orderBy('event')
                    ->limit(500)
                    ->pluck('event')
                    ->values(),

                'users' => User::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                        'email',
                    ]),
            ]
        );
    }

    public function show(
        Request $request,
        AuditLog $auditLog
    ): Response {
        abort_unless(
            $request->user()->can('view audit logs'),
            403
        );

        $auditLog->load([
            'actor:id,name,email',
        ]);

        return Inertia::render(
            'Admin/AuditLogs/Show',
            [
                'auditLog' => [
                    'id' => $auditLog->id,
                    'request_id' => $auditLog->request_id,
                    'category' => $auditLog->category,
                    'event' => $auditLog->event,
                    'description' => $auditLog->description,

                    'auditable_type' => $auditLog->auditable_type,
                    'auditable_name' => $auditLog->auditable_type
                            ? class_basename(
                                $auditLog->auditable_type
                            )
                            : null,
                    'auditable_id' => $auditLog->auditable_id,

                    'parent_type' => $auditLog->parent_type,
                    'parent_name' => $auditLog->parent_type
                            ? class_basename(
                                $auditLog->parent_type
                            )
                            : null,
                    'parent_id' => $auditLog->parent_id,

                    'actor' => [
                        'id' => $auditLog->user_id,
                        'name' => $auditLog->actor_name
                            ?? $auditLog->actor?->name
                            ?? 'System',
                        'email' => $auditLog->actor_email
                            ?? $auditLog->actor?->email,
                        'roles' => $auditLog->actor_roles ?? [],
                    ],

                    'old_status' => $auditLog->old_status,
                    'new_status' => $auditLog->new_status,

                    'old_values' => $auditLog->old_values ?? [],
                    'new_values' => $auditLog->new_values ?? [],
                    'metadata' => $auditLog->metadata ?? [],

                    'route_name' => $auditLog->route_name,
                    'http_method' => $auditLog->http_method,
                    'url' => $auditLog->url,
                    'ip_address' => $auditLog->ip_address,
                    'user_agent' => $auditLog->user_agent,

                    'occurred_at' => $auditLog->occurred_at
                        ?->toIso8601String(),
                ],
            ]
        );
    }
}
