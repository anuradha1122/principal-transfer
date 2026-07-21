import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    Activity,
    CalendarDays,
    ChevronLeft,
    ChevronRight,
    Eye,
    Filter,
    RotateCcw,
    Search,
    ShieldCheck,
    UserRound,
} from 'lucide-react';
import { useState } from 'react';

const categoryClasses = {
    workflow:
        'bg-blue-50 text-blue-700 ring-blue-600/20',
    model:
        'bg-slate-100 text-slate-700 ring-slate-600/20',
    document:
        'bg-violet-50 text-violet-700 ring-violet-600/20',
    authentication:
        'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    security:
        'bg-rose-50 text-rose-700 ring-rose-600/20',
    system:
        'bg-amber-50 text-amber-700 ring-amber-600/20',
};

function formatDateTime(value) {
    if (!value) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat('en-LK', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

function humanize(value) {
    if (!value) {
        return 'Unknown';
    }

    return value
        .replaceAll('.', ' ')
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (character) =>
            character.toUpperCase(),
        );
}

function Pagination({ links }) {
    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <div className="flex flex-wrap items-center justify-center gap-2">
            {links.map((link, index) => {
                const isPrevious = index === 0;
                const isNext =
                    index === links.length - 1;

                return link.url ? (
                    <Link
                        key={`${link.label}-${index}`}
                        href={link.url}
                        preserveScroll
                        className={`inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-medium transition ${
                            link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:text-blue-700'
                        }`}
                    >
                        {isPrevious ? (
                            <ChevronLeft className="h-4 w-4" />
                        ) : isNext ? (
                            <ChevronRight className="h-4 w-4" />
                        ) : (
                            <span
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        )}
                    </Link>
                ) : (
                    <span
                        key={`${link.label}-${index}`}
                        className="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 text-sm text-slate-400"
                    >
                        {isPrevious ? (
                            <ChevronLeft className="h-4 w-4" />
                        ) : isNext ? (
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
            })}
        </div>
    );
}

export default function Index({
    auditLogs,
    filters,
    categories,
    events,
    users,
}) {
    const [form, setForm] = useState({
        search: filters.search ?? '',
        category: filters.category ?? '',
        event: filters.event ?? '',
        user_id: filters.user_id ?? '',
        date_from: filters.date_from ?? '',
        date_to: filters.date_to ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route('admin.audit-logs.index'),
            Object.fromEntries(
                Object.entries(form).filter(
                    ([, value]) =>
                        value !== '' &&
                        value !== null,
                ),
            ),
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const reset = () => {
        setForm({
            search: '',
            category: '',
            event: '',
            user_id: '',
            date_from: '',
            date_to: '',
        });

        router.get(
            route('admin.audit-logs.index'),
            {},
            {
                preserveState: false,
                replace: true,
            },
        );
    };

    return (
        <AdminLayout>
            <Head title="Audit Logs" />

            <div className="space-y-6">
                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-200 bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 px-6 py-7 text-white">
                        <div className="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div className="flex items-start gap-4">
                                <div className="rounded-2xl bg-white/10 p-3 ring-1 ring-white/20">
                                    <ShieldCheck className="h-7 w-7" />
                                </div>

                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-blue-200">
                                        Security and accountability
                                    </p>

                                    <h1 className="mt-1 text-2xl font-bold sm:text-3xl">
                                        Audit Logs
                                    </h1>

                                    <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                                        Review workflow actions,
                                        authentication activity,
                                        model changes, document events,
                                        and request-level details.
                                    </p>
                                </div>
                            </div>

                            <div className="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-white/20">
                                <p className="text-xs uppercase tracking-wide text-slate-300">
                                    Matching records
                                </p>
                                <p className="mt-1 text-2xl font-bold">
                                    {auditLogs.total ?? 0}
                                </p>
                            </div>
                        </div>
                    </div>

                    <form
                        onSubmit={submit}
                        className="space-y-4 p-5 sm:p-6"
                    >
                        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <label className="block">
                                <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Search
                                </span>

                                <div className="relative">
                                    <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

                                    <input
                                        type="search"
                                        value={form.search}
                                        onChange={(event) =>
                                            setForm({
                                                ...form,
                                                search:
                                                    event.target
                                                        .value,
                                            })
                                        }
                                        placeholder="Event, actor, request ID..."
                                        className="w-full rounded-xl border-slate-300 pl-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                </div>
                            </label>

                            <label className="block">
                                <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Category
                                </span>

                                <select
                                    value={form.category}
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            category:
                                                event.target.value,
                                        })
                                    }
                                    className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        All categories
                                    </option>

                                    {categories.map(
                                        (category) => (
                                            <option
                                                key={category}
                                                value={category}
                                            >
                                                {humanize(
                                                    category,
                                                )}
                                            </option>
                                        ),
                                    )}
                                </select>
                            </label>

                            <label className="block">
                                <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Event
                                </span>

                                <select
                                    value={form.event}
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            event:
                                                event.target.value,
                                        })
                                    }
                                    className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        All events
                                    </option>

                                    {events.map((eventName) => (
                                        <option
                                            key={eventName}
                                            value={eventName}
                                        >
                                            {humanize(eventName)}
                                        </option>
                                    ))}
                                </select>
                            </label>

                            <label className="block">
                                <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Actor
                                </span>

                                <select
                                    value={form.user_id}
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            user_id:
                                                event.target.value,
                                        })
                                    }
                                    className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        All actors
                                    </option>

                                    {users.map((user) => (
                                        <option
                                            key={user.id}
                                            value={user.id}
                                        >
                                            {user.name} (
                                            {user.email})
                                        </option>
                                    ))}
                                </select>
                            </label>

                            <label className="block">
                                <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                    From date
                                </span>

                                <input
                                    type="date"
                                    value={form.date_from}
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            date_from:
                                                event.target.value,
                                        })
                                    }
                                    className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </label>

                            <label className="block">
                                <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                    To date
                                </span>

                                <input
                                    type="date"
                                    value={form.date_to}
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            date_to:
                                                event.target.value,
                                        })
                                    }
                                    className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </label>
                        </div>

                        <div className="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                            <button
                                type="button"
                                onClick={reset}
                                className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                <RotateCcw className="h-4 w-4" />
                                Reset
                            </button>

                            <button
                                type="submit"
                                className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                <Filter className="h-4 w-4" />
                                Apply filters
                            </button>
                        </div>
                    </form>
                </div>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-slate-50">
                                <tr>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Event
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Actor
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Status change
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Subject
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Date
                                    </th>
                                    <th className="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-slate-100 bg-white">
                                {auditLogs.data.length ===
                                0 ? (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="px-6 py-16 text-center"
                                        >
                                            <Activity className="mx-auto h-10 w-10 text-slate-300" />
                                            <p className="mt-3 font-semibold text-slate-700">
                                                No audit records
                                                found
                                            </p>
                                            <p className="mt-1 text-sm text-slate-500">
                                                Adjust the filters and
                                                try again.
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    auditLogs.data.map(
                                        (auditLog) => (
                                            <tr
                                                key={auditLog.id}
                                                className="align-top hover:bg-slate-50/70"
                                            >
                                                <td className="px-5 py-4">
                                                    <div className="flex flex-col items-start gap-2">
                                                        <span
                                                            className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize ring-1 ring-inset ${
                                                                categoryClasses[
                                                                    auditLog
                                                                        .category
                                                                ] ??
                                                                categoryClasses.model
                                                            }`}
                                                        >
                                                            {
                                                                auditLog.category
                                                            }
                                                        </span>

                                                        <div>
                                                            <p className="text-sm font-semibold text-slate-900">
                                                                {humanize(
                                                                    auditLog.event,
                                                                )}
                                                            </p>

                                                            {auditLog.description && (
                                                                <p className="mt-1 max-w-md text-xs leading-5 text-slate-500">
                                                                    {
                                                                        auditLog.description
                                                                    }
                                                                </p>
                                                            )}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <div className="flex items-start gap-2">
                                                        <UserRound className="mt-0.5 h-4 w-4 text-slate-400" />

                                                        <div>
                                                            <p className="text-sm font-medium text-slate-800">
                                                                {
                                                                    auditLog.actor_name
                                                                }
                                                            </p>
                                                            <p className="text-xs text-slate-500">
                                                                {auditLog.actor_email ??
                                                                    'No email'}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td className="px-5 py-4">
                                                    {auditLog.old_status ||
                                                    auditLog.new_status ? (
                                                        <div className="flex flex-wrap items-center gap-2 text-xs">
                                                            <span className="rounded-lg bg-slate-100 px-2 py-1 font-medium text-slate-600">
                                                                {auditLog.old_status ??
                                                                    'None'}
                                                            </span>
                                                            <span className="text-slate-400">
                                                                →
                                                            </span>
                                                            <span className="rounded-lg bg-blue-50 px-2 py-1 font-semibold text-blue-700">
                                                                {auditLog.new_status ??
                                                                    'None'}
                                                            </span>
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-slate-400">
                                                            No status
                                                            change
                                                        </span>
                                                    )}
                                                </td>

                                                <td className="px-5 py-4">
                                                    {auditLog.auditable_type ? (
                                                        <div>
                                                            <p className="text-sm font-medium text-slate-800">
                                                                {
                                                                    auditLog.auditable_type
                                                                }
                                                            </p>
                                                            <p className="text-xs text-slate-500">
                                                                ID #
                                                                {
                                                                    auditLog.auditable_id
                                                                }
                                                            </p>
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-slate-400">
                                                            System
                                                        </span>
                                                    )}
                                                </td>

                                                <td className="px-5 py-4">
                                                    <div className="flex items-start gap-2">
                                                        <CalendarDays className="mt-0.5 h-4 w-4 text-slate-400" />
                                                        <span className="whitespace-nowrap text-sm text-slate-600">
                                                            {formatDateTime(
                                                                auditLog.occurred_at,
                                                            )}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td className="px-5 py-4 text-right">
                                                    <Link
                                                        href={route(
                                                            'admin.audit-logs.show',
                                                            auditLog.id,
                                                        )}
                                                        className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700"
                                                    >
                                                        <Eye className="h-4 w-4" />
                                                        View
                                                    </Link>
                                                </td>
                                            </tr>
                                        ),
                                    )
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="border-t border-slate-200 bg-slate-50 px-5 py-4">
                        <Pagination
                            links={auditLogs.links}
                        />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
