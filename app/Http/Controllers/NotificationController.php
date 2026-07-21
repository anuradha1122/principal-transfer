<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $filter = $request->input(
            'filter',
            'all'
        );

        $search = trim(
            (string) $request->input(
                'search'
            )
        );

        $query = $request
            ->user()
            ->notifications()
            ->when(
                $filter === 'unread',
                fn ($query) => $query->whereNull(
                    'read_at'
                )
            )
            ->when(
                $filter === 'read',
                fn ($query) => $query->whereNotNull(
                    'read_at'
                )
            )
            ->when(
                $search,
                function (
                    $query,
                    string $search
                ): void {
                    $query->where(
                        function ($innerQuery) use (
                            $search
                        ): void {
                            $innerQuery
                                ->where(
                                    'data->title',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'data->message',
                                    'like',
                                    '%'.$search.'%'
                                )
                                ->orWhere(
                                    'data->category',
                                    'like',
                                    '%'.$search.'%'
                                );
                        }
                    );
                }
            )
            ->latest();

        $notifications = $query
            ->paginate(20)
            ->withQueryString()
            ->through(
                fn (
                    DatabaseNotification $notification
                ): array => $this
                    ->notificationData(
                        $notification
                    )
            );

        return Inertia::render(
            'Notifications/Index',
            [
                'notifications' => $notifications,

                'filters' => [
                    'filter' => $filter,

                    'search' => $search,
                ],

                'counts' => [
                    'all' => $request
                        ->user()
                        ->notifications()
                        ->count(),

                    'unread' => $request
                        ->user()
                        ->unreadNotifications()
                        ->count(),

                    'read' => $request
                        ->user()
                        ->readNotifications()
                        ->count(),
                ],
            ]
        );
    }

    public function show(
        Request $request,
        string $notification
    ): Response {
        $notificationRecord =
            $request
                ->user()
                ->notifications()
                ->whereKey(
                    $notification
                )
                ->firstOrFail();

        if (
            $notificationRecord
                ->read_at === null
        ) {
            $notificationRecord
                ->markAsRead();
        }

        return Inertia::render(
            'Notifications/Show',
            [
                'notification' => $this->notificationData(
                    $notificationRecord
                        ->fresh()
                ),
            ]
        );
    }

    public function markAsRead(
        Request $request,
        string $notification
    ): RedirectResponse {
        $notificationRecord =
            $request
                ->user()
                ->notifications()
                ->whereKey(
                    $notification
                )
                ->firstOrFail();

        $notificationRecord
            ->markAsRead();

        return back()->with(
            'success',
            'Notification marked as read.'
        );
    }

    public function markAsUnread(
        Request $request,
        string $notification
    ): RedirectResponse {
        $notificationRecord =
            $request
                ->user()
                ->notifications()
                ->whereKey(
                    $notification
                )
                ->firstOrFail();

        $notificationRecord->update([
            'read_at' => null,
        ]);

        return back()->with(
            'success',
            'Notification marked as unread.'
        );
    }

    public function markAllAsRead(
        Request $request
    ): RedirectResponse {
        $request
            ->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return back()->with(
            'success',
            'All notifications marked as read.'
        );
    }

    public function destroy(
        Request $request,
        string $notification
    ): RedirectResponse {
        $notificationRecord =
            $request
                ->user()
                ->notifications()
                ->whereKey(
                    $notification
                )
                ->firstOrFail();

        $notificationRecord->delete();

        return redirect()
            ->route(
                'notifications.index'
            )
            ->with(
                'success',
                'Notification deleted.'
            );
    }

    public function clearRead(
        Request $request
    ): RedirectResponse {
        $request
            ->user()
            ->readNotifications()
            ->delete();

        return back()->with(
            'success',
            'Read notifications cleared.'
        );
    }

    private function notificationData(
        DatabaseNotification $notification
    ): array {
        return [
            'id' => $notification->id,

            'type' => class_basename(
                $notification->type
            ),

            'title' => data_get(
                $notification->data,
                'title',
                'Notification'
            ),

            'message' => data_get(
                $notification->data,
                'message'
            ),

            'category' => data_get(
                $notification->data,
                'category',
                'system'
            ),

            'severity' => data_get(
                $notification->data,
                'severity',
                'info'
            ),

            'action_url' => data_get(
                $notification->data,
                'action_url'
            ),

            'action_label' => data_get(
                $notification->data,
                'action_label'
            ),

            'metadata' => data_get(
                $notification->data,
                'metadata',
                []
            ),

            'is_read' => $notification->read_at
                    !== null,

            'read_at' => $notification
                ->read_at
                ?->toIso8601String(),

            'created_at' => $notification
                ->created_at
                ?->toIso8601String(),
        ];
    }
}
