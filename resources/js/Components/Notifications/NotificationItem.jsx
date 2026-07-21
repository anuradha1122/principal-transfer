import { Link } from '@inertiajs/react';
import {
    AlertTriangle,
    Bell,
    CheckCircle2,
    FileText,
    Info,
    ShieldAlert,
} from 'lucide-react';

const severityStyles = {
    success: {
        icon: CheckCircle2,
        iconClass:
            'bg-emerald-50 text-emerald-700 ring-emerald-200',
    },

    warning: {
        icon: AlertTriangle,
        iconClass:
            'bg-amber-50 text-amber-700 ring-amber-200',
    },

    danger: {
        icon: ShieldAlert,
        iconClass:
            'bg-rose-50 text-rose-700 ring-rose-200',
    },

    error: {
        icon: ShieldAlert,
        iconClass:
            'bg-rose-50 text-rose-700 ring-rose-200',
    },

    info: {
        icon: Info,
        iconClass:
            'bg-blue-50 text-blue-700 ring-blue-200',
    },
};

function formatRelativeTime(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    const seconds = Math.floor(
        (Date.now() - date.getTime()) / 1000,
    );

    if (seconds < 60) {
        return 'Just now';
    }

    const minutes = Math.floor(seconds / 60);

    if (minutes < 60) {
        return `${minutes} min ago`;
    }

    const hours = Math.floor(minutes / 60);

    if (hours < 24) {
        return `${hours} hr ago`;
    }

    const days = Math.floor(hours / 24);

    if (days < 7) {
        return `${days} day${days === 1 ? '' : 's'} ago`;
    }

    return new Intl.DateTimeFormat('en-LK', {
        dateStyle: 'medium',
    }).format(date);
}

export default function NotificationItem({
    notification,
    compact = false,
    showActions = false,
    onMarkAsRead,
    onMarkAsUnread,
    onDelete,
}) {
    const style =
        severityStyles[notification.severity] ??
        severityStyles.info;

    const Icon = notification.category === 'document'
        ? FileText
        : style.icon ?? Bell;

    return (
        <div
            className={`relative flex gap-3 ${
                compact ? 'p-3' : 'p-4'
            } ${
                notification.is_read
                    ? 'bg-white'
                    : 'bg-blue-50/60'
            }`}
        >
            {!notification.is_read && (
                <span className="absolute left-0 top-0 h-full w-1 bg-blue-600" />
            )}

            <div
                className={`mt-0.5 shrink-0 rounded-xl p-2 ring-1 ${style.iconClass}`}
            >
                <Icon
                    className={
                        compact
                            ? 'h-4 w-4'
                            : 'h-5 w-5'
                    }
                />
            </div>

            <div className="min-w-0 flex-1">
                <div className="flex items-start justify-between gap-3">
                    <Link
                        href={route(
                            'notifications.show',
                            notification.id,
                        )}
                        className={`block text-sm text-slate-900 hover:text-blue-700 ${
                            notification.is_read
                                ? 'font-semibold'
                                : 'font-bold'
                        }`}
                    >
                        {notification.title}
                    </Link>

                    {!notification.is_read && (
                        <span className="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-600" />
                    )}
                </div>

                {notification.message && (
                    <p
                        className={`mt-1 text-slate-600 ${
                            compact
                                ? 'line-clamp-2 text-xs leading-5'
                                : 'text-sm leading-6'
                        }`}
                    >
                        {notification.message}
                    </p>
                )}

                <div className="mt-2 flex flex-wrap items-center gap-x-3 gap-y-2">
                    <span className="text-xs text-slate-400">
                        {formatRelativeTime(
                            notification.created_at,
                        )}
                    </span>

                    {notification.category && (
                        <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold capitalize text-slate-600">
                            {notification.category.replaceAll(
                                '_',
                                ' ',
                            )}
                        </span>
                    )}
                </div>

                {showActions && (
                    <div className="mt-3 flex flex-wrap gap-2">
                        {notification.is_read ? (
                            <button
                                type="button"
                                onClick={() =>
                                    onMarkAsUnread?.(
                                        notification,
                                    )
                                }
                                className="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Mark unread
                            </button>
                        ) : (
                            <button
                                type="button"
                                onClick={() =>
                                    onMarkAsRead?.(
                                        notification,
                                    )
                                }
                                className="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                            >
                                Mark read
                            </button>
                        )}

                        <button
                            type="button"
                            onClick={() =>
                                onDelete?.(notification)
                            }
                            className="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100"
                        >
                            Delete
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
}
