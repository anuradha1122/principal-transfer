<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
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

        $filters = $request->only([
            'search',
            'category',
            'event',
            'user_id',
            'date_from',
            'date_to',
        ]);

        $auditLogs = AuditLog::query()
            ->with([
                'actor:id,name,email',
            ])
            ->when(
                $filters['search'] ?? null,
                function ($query, string $search): void {
                    $query->where(
                        function ($innerQuery) use ($search): void {
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
                                    'route_name',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'request_id',
                                    'like',
                                    '%'.$search.'%'
                                );
                        }
                    );
                }
            )
            ->category($filters['category'] ?? null)
            ->event($filters['event'] ?? null)
            ->forUser(
                isset($filters['user_id'])
                    ? (int) $filters['user_id']
                    : null
            )
            ->betweenDates(
                $filters['date_from'] ?? null,
                $filters['date_to'] ?? null
            )
            ->latest('occurred_at')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render(
            'Admin/AuditLogs/Index',
            [
                'auditLogs' => $auditLogs,
                'filters' => $filters,

                'categories' => AuditLog::query()
                    ->select('category')
                    ->distinct()
                    ->orderBy('category')
                    ->pluck('category')
                    ->values(),

                'events' => AuditLog::query()
                    ->select('event')
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
                'auditLog' => $auditLog,
            ]
        );
    }
}
