import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowLeft,
    Bell,
    CalendarClock,
    CheckCircle2,
    ExternalLink,
    Info,
    ShieldAlert,
    Trash2,
} from 'lucide-react';

const styles = {
    info: {
        icon: Info,
        background: 'bg-blue-50',
        iconClass:
            'bg-blue-100 text-blue-700 ring-blue-200',
        labelClass:
            'bg-blue-100 text-blue-700',
    },

    success: {
        icon: CheckCircle2,
        background: 'bg-emerald-50',
        iconClass:
            'bg-emerald-100 text-emerald-700 ring-emerald-200',
        labelClass:
            'bg-emerald-100 text-emerald-700',
    },

    warning: {
        icon: AlertTriangle,
        background: 'bg-amber-50',
        iconClass:
            'bg-amber-100 text-amber-700 ring-amber-200',
        labelClass:
            'bg-amber-100 text-amber-700',
    },

    danger: {
        icon: ShieldAlert,
        background: 'bg-rose-50',
        iconClass:
            'bg-rose-100 text-rose-700 ring-rose-200',
        labelClass:
            'bg-rose-100 text-rose-700',
    },

    error: {
        icon: ShieldAlert,
        background: 'bg-rose-50',
        iconClass:
            'bg-rose-100 text-rose-700 ring-rose-200',
        labelClass:
            'bg-rose-100 text-rose-700',
    },
};

function formatDateTime(value) {
    if (!value) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat('en-LK', {
        dateStyle: 'full',
        timeStyle: 'medium',
    }).format(new Date(value));
}

export default function Show({
    notification,
}) {
    const style =
        styles[notification.severity] ??
        styles.info;

    const Icon = style.icon ?? Bell;

    const markAsUnread = () => {
        router.post(
            route(
                'notifications.unread',
                notification.id,
            ),
        );
    };

    const remove = () => {
        if (
            !window.confirm(
                'Delete this notification?',
            )
        ) {
            return;
        }

        router.delete(
            route(
                'notifications.destroy',
                notification.id,
            ),
        );
    };

    return (
        <AdminLayout title="Notifications">
            <Head title={notification.title} />

            <div className="mx-auto max-w-4xl space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Link
                        href={route(
                            'notifications.index',
                        )}
                        className="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back to notifications
                    </Link>

                    <div className="flex flex-wrap gap-2">
                        <button
                            type="button"
                            onClick={markAsUnread}
                            className="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Mark unread
                        </button>

                        <button
                            type="button"
                            onClick={remove}
                            className="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100"
                        >
                            <Trash2 className="h-4 w-4" />
                            Delete
                        </button>
                    </div>
                </div>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div
                        className={`border-b border-slate-200 px-6 py-8 ${style.background}`}
                    >
                        <div className="flex items-start gap-4">
                            <div
                                className={`rounded-2xl p-3 ring-1 ${style.iconClass}`}
                            >
                                <Icon className="h-7 w-7" />
                            </div>

                            <div className="min-w-0">
                                <div className="flex flex-wrap items-center gap-2">
                                    <span
                                        className={`rounded-full px-3 py-1 text-xs font-bold capitalize ${style.labelClass}`}
                                    >
                                        {
                                            notification.severity
                                        }
                                    </span>

                                    <span className="rounded-full bg-white px-3 py-1 text-xs font-semibold capitalize text-slate-600 ring-1 ring-slate-200">
                                        {notification.category.replaceAll(
                                            '_',
                                            ' ',
                                        )}
                                    </span>
                                </div>

                                <h1 className="mt-4 text-2xl font-bold text-slate-900 sm:text-3xl">
                                    {notification.title}
                                </h1>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-6 p-6">
                        <p className="whitespace-pre-line text-base leading-8 text-slate-700">
                            {notification.message}
                        </p>

                        <div className="flex items-center gap-2 border-t border-slate-100 pt-5 text-sm text-slate-500">
                            <CalendarClock className="h-4 w-4" />
                            {formatDateTime(
                                notification.created_at,
                            )}
                        </div>

                        {notification.action_url && (
                            <a
                                href={
                                    notification.action_url
                                }
                                className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                            >
                                {notification.action_label ??
                                    'Open related record'}

                                <ExternalLink className="h-4 w-4" />
                            </a>
                        )}
                    </div>
                </div>

                {notification.metadata &&
                    Object.keys(
                        notification.metadata,
                    ).length > 0 && (
                        <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div className="border-b border-slate-200 bg-slate-50 px-5 py-4">
                                <h2 className="font-bold text-slate-800">
                                    Additional information
                                </h2>
                            </div>

                            <div className="p-5">
                                <pre className="max-h-96 overflow-auto rounded-xl bg-slate-950 p-4 text-xs leading-6 text-slate-100">
                                    {JSON.stringify(
                                        notification.metadata,
                                        null,
                                        2,
                                    )}
                                </pre>
                            </div>
                        </div>
                    )}
            </div>
        </AdminLayout>
    );
}
