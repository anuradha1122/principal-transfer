import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock3,
    Download,
    Eye,
    FileSearch,
    Filter,
    Search,
    X,
    XCircle,
} from 'lucide-react';
import { useEffect, useState } from 'react';

const statusClasses = {
    Submitted:
        'bg-blue-100 text-blue-700 ring-blue-200',
    'Zonal Review':
        'bg-amber-100 text-amber-700 ring-amber-200',
    'Zonal Approved':
        'bg-emerald-100 text-emerald-700 ring-emerald-200',
    'Zonal Rejected':
        'bg-red-100 text-red-700 ring-red-200',
};

const summaryCards = [
    {
        key: 'submitted',
        label: 'Submitted',
        icon: FileSearch,
        classes:
            'border-blue-200 bg-blue-50 text-blue-700',
    },
    {
        key: 'under_review',
        label: 'Under Review',
        icon: Clock3,
        classes:
            'border-amber-200 bg-amber-50 text-amber-700',
    },
    {
        key: 'approved',
        label: 'Approved',
        icon: CheckCircle2,
        classes:
            'border-emerald-200 bg-emerald-50 text-emerald-700',
    },
    {
        key: 'rejected',
        label: 'Rejected',
        icon: XCircle,
        classes:
            'border-red-200 bg-red-50 text-red-700',
    },
];

const formatDate = (value) => {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(new Date(value));
};

export default function Index({
    applications = {},
    filters = {},
    cycles = [],
    schools = [],
    statuses = [],
    summary = {},
    zone = null,
}) {
    const [form, setForm] = useState({
        search: filters?.search ?? '',
        transfer_cycle_id:
            filters?.transfer_cycle_id ?? '',
        status: filters?.status ?? '',
        school_id: filters?.school_id ?? '',
        submitted_from:
            filters?.submitted_from ?? '',
        submitted_to: filters?.submitted_to ?? '',
    });

    useEffect(() => {
        const timeout = window.setTimeout(() => {
            if (
                form.search !==
                (filters?.search ?? '')
            ) {
                applyFilters();
            }
        }, 500);

        return () => window.clearTimeout(timeout);
    }, [form.search]);

    const applyFilters = () => {
        const query = Object.fromEntries(
            Object.entries(form).filter(
                ([, value]) =>
                    value !== '' &&
                    value !== null &&
                    value !== undefined,
            ),
        );

        router.get(
            route(
                'zonal.transfer-applications.index',
            ),
            query,
            {
                preserveState: true,
                replace: true,
                preserveScroll: true,
            },
        );
    };

    const clearFilters = () => {
        setForm({
            search: '',
            transfer_cycle_id: '',
            status: '',
            school_id: '',
            submitted_from: '',
            submitted_to: '',
        });

        router.get(
            route(
                'zonal.transfer-applications.index',
            ),
            {},
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const records = applications?.data ?? [];
    const links = applications?.links ?? [];

    return (
        <AdminLayout>
            <Head title="Zonal Transfer Applications" />

            <div className="space-y-6">
                <header className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Transfer Applications
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            {zone?.name
                                ? `${zone.name} Zone applications only.`
                                : 'Applications across all Zones.'}
                        </p>
                    </div>
                </header>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {summaryCards.map((card) => {
                        const Icon = card.icon;

                        return (
                            <div
                                key={card.key}
                                className={`rounded-2xl border p-5 shadow-sm ${card.classes}`}
                            >
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-semibold">
                                            {card.label}
                                        </p>

                                        <p className="mt-2 text-3xl font-bold">
                                            {summary?.[
                                                card.key
                                            ] ?? 0}
                                        </p>
                                    </div>

                                    <Icon className="h-7 w-7" />
                                </div>
                            </div>
                        );
                    })}
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div className="relative">
                            <Search className="pointer-events-none absolute left-3 top-3 h-4 w-4 text-slate-400" />

                            <input
                                type="text"
                                value={form.search}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        search: event.target.value,
                                    })
                                }
                                placeholder="Search application, Principal or NIC"
                                className="w-full rounded-xl border-slate-300 pl-10 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <select
                            value={
                                form.transfer_cycle_id
                            }
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    transfer_cycle_id:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                All Transfer Cycles
                            </option>

                            {(cycles ?? []).map(
                                (cycle) => (
                                    <option
                                        key={cycle.id}
                                        value={cycle.id}
                                    >
                                        {cycle.name}
                                    </option>
                                ),
                            )}
                        </select>

                        <select
                            value={form.status}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    status: event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                All Statuses
                            </option>

                            {(statuses ?? []).map(
                                (status) => (
                                    <option
                                        key={status}
                                        value={status}
                                    >
                                        {status}
                                    </option>
                                ),
                            )}
                        </select>

                        <select
                            value={form.school_id}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    school_id:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                All Schools
                            </option>

                            {(schools ?? []).map(
                                (school) => (
                                    <option
                                        key={school.id}
                                        value={school.id}
                                    >
                                        {school.name}
                                        {school.census_number
                                            ? ` (${school.census_number})`
                                            : ''}
                                    </option>
                                ),
                            )}
                        </select>

                        <input
                            type="date"
                            value={form.submitted_from}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    submitted_from:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />

                        <input
                            type="date"
                            value={form.submitted_to}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    submitted_to:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div className="mt-4 flex flex-wrap gap-3">
                        <button
                            type="button"
                            onClick={applyFilters}
                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            <Filter className="h-4 w-4" />
                            Apply Filters
                        </button>

                        <button
                            type="button"
                            onClick={clearFilters}
                            className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            <X className="h-4 w-4" />
                            Clear
                        </button>
                    </div>
                </section>

                <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-slate-50">
                                <tr>
                                    {[
                                        'Application',
                                        'Principal',
                                        'Current School',
                                        'Submitted',
                                        'Status',
                                        'Actions',
                                    ].map((heading) => (
                                        <th
                                            key={heading}
                                            className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                        >
                                            {heading}
                                        </th>
                                    ))}
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-slate-100 bg-white">
                                {records.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="px-5 py-12 text-center"
                                        >
                                            <FileSearch className="mx-auto h-10 w-10 text-slate-300" />

                                            <p className="mt-3 text-sm font-semibold text-slate-700">
                                                No applications
                                                found
                                            </p>

                                            <p className="mt-1 text-sm text-slate-500">
                                                Try changing the
                                                selected filters.
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    records.map(
                                        (application) => (
                                            <tr
                                                key={
                                                    application.id
                                                }
                                                className="hover:bg-slate-50"
                                            >
                                                <td className="whitespace-nowrap px-5 py-4">
                                                    <p className="text-sm font-bold text-slate-900">
                                                        {application.application_number ??
                                                            `#${application.id}`}
                                                    </p>

                                                    <p className="mt-1 text-xs text-slate-500">
                                                        {application
                                                            ?.transfer_cycle
                                                            ?.name ??
                                                            'No cycle'}
                                                    </p>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <p className="text-sm font-semibold text-slate-900">
                                                        {application.principal_name_snapshot ??
                                                            application
                                                                ?.principal_profile
                                                                ?.full_name ??
                                                            application
                                                                ?.principal_profile
                                                                ?.user
                                                                ?.name ??
                                                            '—'}
                                                    </p>

                                                    <p className="mt-1 text-xs text-slate-500">
                                                        {application.nic_snapshot ??
                                                            application
                                                                ?.principal_profile
                                                                ?.nic ??
                                                            '—'}
                                                    </p>
                                                </td>

                                                <td className="px-5 py-4">
                                                    <p className="text-sm text-slate-700">
                                                        {application.current_school_name_snapshot ??
                                                            application
                                                                ?.current_school
                                                                ?.name ??
                                                            '—'}
                                                    </p>
                                                </td>

                                                <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                    {formatDate(
                                                        application.submitted_at,
                                                    )}
                                                </td>

                                                <td className="whitespace-nowrap px-5 py-4">
                                                    <span
                                                        className={`inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset ${
                                                            statusClasses[
                                                                application
                                                                    .status
                                                            ] ??
                                                            'bg-slate-100 text-slate-700 ring-slate-200'
                                                        }`}
                                                    >
                                                        {
                                                            application.status
                                                        }
                                                    </span>
                                                </td>

                                                <td className="whitespace-nowrap px-5 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <Link
                                                            href={route(
                                                                'zonal.transfer-applications.show',
                                                                application.id,
                                                            )}
                                                            title="View application"
                                                            className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                                        >
                                                            <Eye className="h-4 w-4" />
                                                        </Link>

                                                        {application.pdf_path ? (
                                                            <a
                                                                href={route(
                                                                    'zonal.transfer-applications.pdf',
                                                                    application.id,
                                                                )}
                                                                title="Download PDF"
                                                                className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700"
                                                            >
                                                                <Download className="h-4 w-4" />
                                                            </a>
                                                        ) : null}
                                                    </div>
                                                </td>
                                            </tr>
                                        ),
                                    )
                                )}
                            </tbody>
                        </table>
                    </div>

                    {links.length > 3 ? (
                        <div className="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4">
                            <p className="text-sm text-slate-500">
                                Showing{' '}
                                {applications?.from ?? 0} to{' '}
                                {applications?.to ?? 0} of{' '}
                                {applications?.total ?? 0}
                            </p>

                            <div className="flex flex-wrap gap-1">
                                {links.map(
                                    (link, index) => (
                                        <Link
                                            key={`${link.label}-${index}`}
                                            href={
                                                link.url ?? '#'
                                            }
                                            preserveScroll
                                            className={[
                                                'rounded-lg border px-3 py-2 text-sm font-semibold',
                                                link.active
                                                    ? 'border-blue-600 bg-blue-600 text-white'
                                                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
                                                !link.url
                                                    ? 'pointer-events-none opacity-50'
                                                    : '',
                                            ].join(
                                                ' ',
                                            )}
                                            dangerouslySetInnerHTML={{
                                                __html: link.label,
                                            }}
                                        />
                                    ),
                                )}
                            </div>
                        </div>
                    ) : null}
                </section>
            </div>
        </AdminLayout>
    );
}
