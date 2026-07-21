import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    Activity,
    BarChart3,
    CalendarRange,
    CheckCircle2,
    ClipboardList,
    FileCheck2,
    FileText,
    Filter,
    MapPinned,
    RotateCcw,
    Scale,
    TrendingUp,
} from 'lucide-react';
import { useState } from 'react';

function formatNumber(value) {
    return new Intl.NumberFormat('en-LK').format(
        value ?? 0,
    );
}

function formatDateTime(value) {
    if (!value) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat('en-LK', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

function SummaryCard({
    title,
    value,
    subtitle,
    icon: Icon,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-sm font-semibold text-slate-500">
                        {title}
                    </p>
                    <p className="mt-2 text-3xl font-bold text-slate-900">
                        {value}
                    </p>
                    <p className="mt-2 text-xs leading-5 text-slate-500">
                        {subtitle}
                    </p>
                </div>

                <div className="rounded-2xl bg-blue-50 p-3 text-blue-700">
                    <Icon className="h-6 w-6" />
                </div>
            </div>
        </div>
    );
}

function HorizontalBars({
    title,
    subtitle,
    items,
}) {
    const maximum = Math.max(
        ...items.map((item) => item.total),
        1,
    );

    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 className="font-bold text-slate-900">
                    {title}
                </h2>
                <p className="mt-1 text-sm text-slate-500">
                    {subtitle}
                </p>
            </div>

            <div className="mt-6 space-y-4">
                {items.length === 0 ? (
                    <p className="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        No information available.
                    </p>
                ) : (
                    items.map((item) => (
                        <div key={item.label}>
                            <div className="mb-1.5 flex items-center justify-between gap-4">
                                <span className="truncate text-sm font-medium text-slate-700">
                                    {item.label}
                                </span>
                                <span className="text-sm font-bold text-slate-900">
                                    {formatNumber(
                                        item.total,
                                    )}
                                </span>
                            </div>

                            <div className="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    className="h-full rounded-full bg-blue-600"
                                    style={{
                                        width: `${Math.max(
                                            (item.total /
                                                maximum) *
                                                100,
                                            2,
                                        )}%`,
                                    }}
                                />
                            </div>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
}

export default function Index({
    report,
    filters,
    transferCycles,
    zones,
    statuses,
    scope,
}) {
    const [form, setForm] = useState({
        transfer_cycle_id:
            filters.transfer_cycle_id ?? '',
        zone_id: filters.zone_id ?? '',
        status: filters.status ?? '',
        date_from: filters.date_from ?? '',
        date_to: filters.date_to ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route('admin.reports.index'),
            Object.fromEntries(
                Object.entries(form).filter(
                    ([, value]) => value !== '',
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
            transfer_cycle_id: '',
            zone_id: '',
            status: '',
            date_from: '',
            date_to: '',
        });

        router.get(
            route('admin.reports.index'),
            {},
            {
                preserveState: false,
                replace: true,
            },
        );
    };

    return (
        <AdminLayout>
            <Head title="Transfer Reports" />

            <div className="space-y-6">
                <div className="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-blue-950 to-indigo-950 text-white shadow-sm">
                    <div className="px-6 py-8">
                        <div className="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div className="flex items-start gap-4">
                                <div className="rounded-2xl bg-white/10 p-3 ring-1 ring-white/20">
                                    <BarChart3 className="h-8 w-8" />
                                </div>

                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-blue-200">
                                        Analytics and reporting
                                    </p>

                                    <h1 className="mt-1 text-3xl font-bold">
                                        Transfer Reports
                                    </h1>

                                    <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-300">
                                        Monitor application
                                        volumes, decisions,
                                        appeals, publication
                                        activity, and Zone-level
                                        performance.
                                    </p>
                                </div>
                            </div>

                            {scope.is_zonal && (
                                <div className="rounded-xl bg-amber-400/10 px-4 py-3 text-sm text-amber-100 ring-1 ring-amber-300/30">
                                    Results are restricted to
                                    your assigned Zone.
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <form
                    onSubmit={submit}
                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <label>
                            <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                Transfer cycle
                            </span>

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
                                className="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    All cycles
                                </option>

                                {transferCycles.map(
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
                        </label>

                        <label>
                            <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                Zone
                            </span>

                            <select
                                value={form.zone_id}
                                disabled={scope.is_zonal}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        zone_id:
                                            event.target.value,
                                    })
                                }
                                className="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-slate-100"
                            >
                                <option value="">
                                    All zones
                                </option>

                                {zones.map((zone) => (
                                    <option
                                        key={zone.id}
                                        value={zone.id}
                                    >
                                        {zone.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label>
                            <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                                Application status
                            </span>

                            <select
                                value={form.status}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        status:
                                            event.target.value,
                                    })
                                }
                                className="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    All statuses
                                </option>

                                {statuses.map((status) => (
                                    <option
                                        key={status}
                                        value={status}
                                    >
                                        {status}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label>
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
                                className="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </label>

                        <label>
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
                                className="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </label>
                    </div>

                    <div className="mt-5 flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            onClick={reset}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            <RotateCcw className="h-4 w-4" />
                            Reset
                        </button>

                        <button
                            type="submit"
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            <Filter className="h-4 w-4" />
                            Apply filters
                        </button>
                    </div>
                </form>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <SummaryCard
                        title="Applications"
                        value={formatNumber(
                            report.summary
                                .total_applications,
                        )}
                        subtitle="Applications matching the selected filters."
                        icon={ClipboardList}
                    />

                    <SummaryCard
                        title="Approval rate"
                        value={`${report.summary.approval_rate}%`}
                        subtitle={`${formatNumber(
                            report.summary
                                .finalized_applications,
                        )} finalized applications.`}
                        icon={TrendingUp}
                    />

                    <SummaryCard
                        title="Pending appeals"
                        value={formatNumber(
                            report.summary
                                .pending_appeals,
                        )}
                        subtitle={`${formatNumber(
                            report.summary
                                .total_appeals,
                        )} total appeals recorded.`}
                        icon={Scale}
                    />

                    <SummaryCard
                        title="Published documents"
                        value={formatNumber(
                            report.summary
                                .published_documents,
                        )}
                        subtitle={`${formatNumber(
                            report.summary
                                .signed_documents,
                        )} signed documents uploaded.`}
                        icon={FileCheck2}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <HorizontalBars
                        title="Application status"
                        subtitle="Current application distribution by workflow status."
                        items={
                            report.status_breakdown
                        }
                    />

                    <HorizontalBars
                        title="Applications by Zone"
                        subtitle="Application volume grouped by origin Zone."
                        items={
                            report.zone_breakdown
                        }
                    />

                    <HorizontalBars
                        title="Monthly applications"
                        subtitle="Applications created across the available reporting period."
                        items={
                            report.monthly_applications
                        }
                    />

                    <HorizontalBars
                        title="Appeal status"
                        subtitle="Current appeal workload and final outcomes."
                        items={
                            report.appeal_status_breakdown
                        }
                    />

                    <HorizontalBars
                        title="Transfer documents"
                        subtitle="Generated document totals by document type."
                        items={
                            report.document_breakdown
                        }
                    />

                    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 className="font-bold text-slate-900">
                            Outcome snapshot
                        </h2>

                        <p className="mt-1 text-sm text-slate-500">
                            Appeal and document processing
                            summary.
                        </p>

                        <div className="mt-6 grid gap-4 sm:grid-cols-2">
                            <div className="rounded-xl bg-emerald-50 p-4">
                                <CheckCircle2 className="h-5 w-5 text-emerald-700" />
                                <p className="mt-3 text-2xl font-bold text-emerald-900">
                                    {formatNumber(
                                        report.summary
                                            .approved_appeals,
                                    )}
                                </p>
                                <p className="mt-1 text-sm text-emerald-700">
                                    Approved appeals
                                </p>
                            </div>

                            <div className="rounded-xl bg-blue-50 p-4">
                                <FileText className="h-5 w-5 text-blue-700" />
                                <p className="mt-3 text-2xl font-bold text-blue-900">
                                    {formatNumber(
                                        report.summary
                                            .signed_documents,
                                    )}
                                </p>
                                <p className="mt-1 text-sm text-blue-700">
                                    Signed documents
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <h2 className="font-bold text-slate-900">
                            Recently updated applications
                        </h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-white">
                                <tr>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                        Application
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                        Principal
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                        Cycle
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                        Zone
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                        Status
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                        Updated
                                    </th>
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-slate-100">
                                {report.recent_applications
                                    .length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="px-5 py-12 text-center text-sm text-slate-500"
                                        >
                                            No applications
                                            match the selected
                                            filters.
                                        </td>
                                    </tr>
                                ) : (
                                    report.recent_applications.map(
                                        (application) => (
                                            <tr
                                                key={
                                                    application.id
                                                }
                                                className="hover:bg-slate-50"
                                            >
                                                <td className="px-5 py-4 text-sm font-semibold text-slate-900">
                                                    {application.application_number ??
                                                        `#${application.id}`}
                                                </td>

                                                <td className="px-5 py-4 text-sm text-slate-700">
                                                    {
                                                        application.principal_name
                                                    }
                                                </td>

                                                <td className="px-5 py-4 text-sm text-slate-600">
                                                    {
                                                        application.cycle
                                                    }
                                                </td>

                                                <td className="px-5 py-4 text-sm text-slate-600">
                                                    {
                                                        application.zone
                                                    }
                                                </td>

                                                <td className="px-5 py-4">
                                                    <span className="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                        {
                                                            application.status
                                                        }
                                                    </span>
                                                </td>

                                                <td className="px-5 py-4 text-sm text-slate-500">
                                                    {formatDateTime(
                                                        application.updated_at,
                                                    )}
                                                </td>
                                            </tr>
                                        ),
                                    )
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
