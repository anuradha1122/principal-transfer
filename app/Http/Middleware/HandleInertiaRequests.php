<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            $user->loadMissing('assignedZone');
        }

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user
                    ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,

                        'assigned_zone_id' => $user->assigned_zone_id,

                        'assigned_zone' => $user->assignedZone
                                ? [
                                    'id' => $user->assignedZone->id,

                                    'name' => $user->assignedZone->name,

                                    'code' => $user->assignedZone->code,

                                    'district' => $user->assignedZone->district,
                                ]
                                : null,
                    ]
                    : null,

                'roles' => $user
                    ? $user
                        ->getRoleNames()
                        ->values()
                        ->all()
                    : [],

                'permissions' => $user
                    ? $user
                        ->getAllPermissions()
                        ->pluck('name')
                        ->values()
                        ->all()
                    : [],
            ],

            'flash' => [
                'success' => fn () => $request
                    ->session()
                    ->get('success'),

                'error' => fn () => $request
                    ->session()
                    ->get('error'),

                'warning' => fn () => $request
                    ->session()
                    ->get('warning'),

                'info' => fn () => $request
                    ->session()
                    ->get('info'),
            ],

            'notifications' => [
                'unread_count' => fn () => $request->user()
                        ? $request
                            ->user()
                            ->unreadNotifications()
                            ->count()
                        : 0,

                'recent' => fn () => $request->user()
                        ? $request
                            ->user()
                            ->notifications()
                            ->latest()
                            ->limit(5)
                            ->get()
                            ->map(
                                fn ($notification): array => [
                                    'id' => $notification->id,

                                    'title' => data_get(
                                        $notification->data,
                                        'title',
                                        'Notification'
                                    ),

                                    'message' => data_get(
                                        $notification->data,
                                        'message'
                                    ),

                                    'severity' => data_get(
                                        $notification->data,
                                        'severity',
                                        'info'
                                    ),

                                    'is_read' => $notification
                                        ->read_at !== null,

                                    'created_at' => $notification
                                        ->created_at
                                        ?->toIso8601String(),
                                ]
                            )
                            ->values()
                        : [],
            ],
        ];
    }
}
