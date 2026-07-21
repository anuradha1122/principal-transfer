import NotificationItem from '@/Components/Notifications/NotificationItem';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    Bell,
    CheckCheck,
    ChevronLeft,
    ChevronRight,
    Inbox,
    Search,
    Trash2,
} from 'lucide-react';
import { useState } from 'react';

function Pagination({ links }) {
    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <div className="flex flex-wrap justify-center gap-2">
            {links.map((link, index) => {
                const previous = index === 0;
                const next =
                    index === links.length - 1;

                if (!link.url) {
                    return (
                        <span
                            key={`${link.label}-${index}`}
                            className="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 text-sm text-slate-400"
                        >
                            {previous ? (
                                <ChevronLeft className="h-4 w-4" />
                            ) : next ? (
                                <ChevronRight className="h-4 w-4" />
                            ) : (
                                <span
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            )}
                        </span>
                    );
                }

                return (
                    <Link
                        key={`${link.label}-${index}`}
                        href={link.url}
                        preserveScroll
                        className={`inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-semibold ${
                            link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:text-blue-700'
                        }`}
                    >
                        {previous ? (
                            <ChevronLeft className="h-4 w-4" />
                        ) : next ? (
                            <ChevronRight className="h-4 w-4" />
                        ) : (
                            <span
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        )}
                    </Link>
                );
            })}
        </div>
    );
}

export default function Index({
    notifications,
    filters,
    counts,
}) {
    const [search, setSearch] = useState(
        filters.search ?? '',
    );

    const applyFilter = (filter) => {
        router.get(
            route('notifications.index'),
            {
                filter,
                search,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const submitSearch = (event) => {
        event.preventDefault();

        applyFilter(filters.filter ?? 'all');
    };

    const markAsRead = (notification) => {
        router.post(
            route(
                'notifications.read',
                notification.id,
            ),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const markAsUnread = (notification) => {
        router.post(
            route(
                'notifications.unread',
                notification.id,
            ),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const remove = (notification) => {
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
            {
                preserveScroll: true,
            },
        );
    };

    const markAllAsRead = () => {
        router.post(
            route(
                'notifications.mark-all-as-read',
            ),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const clearRead = () => {
        if (
            !window.confirm(
                'Delete all read notifications?',
            )
        ) {
            return;
        }

        router.delete(
            route('notifications.clear-read'),
            {
                preserveScroll: true,
            },
        );
    };

    const tabs = [
        {
            key: 'all',
            label: 'All',
            count: counts.all,
        },
        {
            key: 'unread',
            label: 'Unread',
            count: counts.unread,
        },
        {
            key: 'read',
            label: 'Read',
            count: counts.read,
        },
    ];

    return (
        <AdminLayout title="Notifications">
            <Head title="Notifications" />

            <div className="space-y-6">
                <div className="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-blue-950 to-indigo-950 text-white shadow-sm">
                    <div className="flex flex-col gap-5 px-6 py-8 lg:flex-row lg:items-center lg:justify-between">
                        <div className="flex items-start gap-4">
                            <div className="rounded-2xl bg-white/10 p-3 ring-1 ring-white/20">
                                <Bell className="h-8 w-8" />
                            </div>

                            <div>
                                <p className="text-sm font-semibold uppercase tracking-[0.18em] text-blue-200">
                                    Updates and alerts
                                </p>

                                <h1 className="mt-1 text-3xl font-bold">
                                    Notifications Centre
                                </h1>

                                <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                                    Review transfer workflow
                                    updates, decisions,
                                    documents, appeals, and
                                    administrative alerts.
                                </p>
                            </div>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            {counts.unread > 0 && (
                                <button
                                    type="button"
                                    onClick={
                                        markAllAsRead
                                    }
                                    className="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold ring-1 ring-white/20 hover:bg-white/20"
                                >
                                    <CheckCheck className="h-4 w-4" />
                                    Mark all read
                                </button>
                            )}

                            {counts.read > 0 && (
                                <button
                                    type="button"
                                    onClick={clearRead}
                                    className="inline-flex items-center gap-2 rounded-xl bg-rose-500/20 px-4 py-2.5 text-sm font-semibold text-rose-100 ring-1 ring-rose-300/30 hover:bg-rose-500/30"
                                >
                                    <Trash2 className="h-4 w-4" />
                                    Clear read
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div className="flex flex-wrap gap-2">
                            {tabs.map((tab) => (
                                <button
                                    key={tab.key}
                                    type="button"
                                    onClick={() =>
                                        applyFilter(
                                            tab.key,
                                        )
                                    }
                                    className={`inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition ${
                                        filters.filter ===
                                        tab.key
                                            ? 'bg-blue-600 text-white shadow-sm'
                                            : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                    }`}
                                >
                                    {tab.label}

                                    <span
                                        className={`rounded-full px-2 py-0.5 text-xs ${
                                            filters.filter ===
                                            tab.key
                                                ? 'bg-white/20 text-white'
                                                : 'bg-white text-slate-600'
                                        }`}
                                    >
                                        {tab.count}
                                    </span>
                                </button>
                            ))}
                        </div>

                        <form
                            onSubmit={submitSearch}
                            className="flex w-full max-w-md gap-2"
                        >
                            <div className="relative flex-1">
                                <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

                                <input
                                    type="search"
                                    value={search}
                                    onChange={(event) =>
                                        setSearch(
                                            event.target
                                                .value,
                                        )
                                    }
                                    placeholder="Search notifications..."
                                    className="w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <button
                                type="submit"
                                className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
                            >
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="divide-y divide-slate-100">
                        {notifications.data.length ===
                        0 ? (
                            <div className="px-6 py-16 text-center">
                                <Inbox className="mx-auto h-11 w-11 text-slate-300" />

                                <p className="mt-4 font-bold text-slate-800">
                                    No notifications found
                                </p>

                                <p className="mt-1 text-sm text-slate-500">
                                    There are no notifications
                                    matching the selected
                                    filter.
                                </p>
                            </div>
                        ) : (
                            notifications.data.map(
                                (notification) => (
                                    <NotificationItem
                                        key={
                                            notification.id
                                        }
                                        notification={
                                            notification
                                        }
                                        showActions
                                        onMarkAsRead={
                                            markAsRead
                                        }
                                        onMarkAsUnread={
                                            markAsUnread
                                        }
                                        onDelete={remove}
                                    />
                                ),
                            )
                        )}
                    </div>

                    <div className="border-t border-slate-200 bg-slate-50 px-5 py-4">
                        <Pagination
                            links={
                                notifications.links
                            }
                        />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
