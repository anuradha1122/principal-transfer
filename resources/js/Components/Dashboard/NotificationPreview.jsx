import { Link } from '@inertiajs/react';
import {
    Bell,
    ChevronRight,
} from 'lucide-react';

export default function NotificationPreview({
    notifications = [],
    unreadCount = 0,
}) {
    return (
        <div>
            <div className="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p className="text-sm font-bold text-slate-900">
                        Recent Notifications
                    </p>

                    <p className="mt-1 text-xs text-slate-500">
                        {Number(
                            unreadCount ?? 0,
                        ).toLocaleString()}{' '}
                        unread notifications
                    </p>
                </div>

                <Link
                    href={route(
                        'notifications.index',
                    )}
                    className="text-xs font-bold text-blue-700 hover:text-blue-900"
                >
                    View all
                </Link>
            </div>

            {notifications.length === 0 ? (
                <div className="rounded-xl bg-slate-50 px-4 py-8 text-center">
                    <Bell className="mx-auto h-6 w-6 text-slate-400" />

                    <p className="mt-3 text-sm font-semibold text-slate-700">
                        No notifications yet
                    </p>

                    <p className="mt-1 text-xs text-slate-500">
                        Workflow updates and decisions will appear here.
                    </p>
                </div>
            ) : (
                <div className="divide-y divide-slate-100">
                    {notifications.map(
                        (notification) => (
                            <Link
                                key={
                                    notification.id
                                }
                                href={route(
                                    'notifications.show',
                                    notification.id,
                                )}
                                className="flex items-start gap-3 py-3 transition hover:bg-slate-50"
                            >
                                <div
                                    className={[
                                        'mt-1 h-2.5 w-2.5 shrink-0 rounded-full',
                                        notification.is_read
                                            ? 'bg-slate-300'
                                            : 'bg-blue-600',
                                    ].join(' ')}
                                />

                                <div className="min-w-0 flex-1">
                                    <p className="truncate text-sm font-semibold text-slate-900">
                                        {
                                            notification.title
                                        }
                                    </p>

                                    <p className="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                        {
                                            notification.message
                                        }
                                    </p>
                                </div>

                                <ChevronRight className="mt-2 h-4 w-4 shrink-0 text-slate-300" />
                            </Link>
                        ),
                    )}
                </div>
            )}
        </div>
    );
}
