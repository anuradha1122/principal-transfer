import AdminLayout from '@/Layouts/AdminLayout';
import {
    ArrowLeft,
    CalendarDays,
    CheckCircle2,
    Clock3,
    FileText,
    Pencil,
    Settings2,
    Users,
    XCircle,
} from 'lucide-react';
import { Link } from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Not set';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    }).format(new Date(value));
}

function formatDateTime(value) {
    if (!value) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

function StatisticCard({
    label,
    value,
    icon: Icon,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <Icon className="h-6 w-6" />
            </div>

            <p className="mt-4 text-3xl font-bold text-slate-900">
                {value ?? 0}
            </p>

            <p className="mt-1 text-sm text-slate-500">
                {label}
            </p>
        </div>
    );
}

function DetailItem({
    label,
    value,
}) {
    return (
        <div>
            <dt className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </dt>

            <dd className="mt-1 text-sm font-semibold text-slate-800">
                {value ?? 'Not set'}
            </dd>
        </div>
    );
}

export default function Show({ cycle }) {
    const statusClasses = {
        Draft: 'bg-amber-50 text-amber-700',
        Published: 'bg-emerald-50 text-emerald-700',
        Closed: 'bg-slate-100 text-slate-700',
        Completed: 'bg-blue-50 text-blue-700',
        Cancelled: 'bg-red-50 text-red-700',
    };

    return (
        <AdminLayout
            title={cycle.name}
            header={
                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div className="flex items-center gap-3">
                            <Link
                                href={route(
                                    'admin.transfer-cycles.index',
                                )}
                                className="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                            >
                                <ArrowLeft className="h-5 w-5" />
                            </Link>

                            <div>
                                <h1 className="text-2xl font-bold text-slate-900">
                                    {cycle.name}
                                </h1>

                                <p className="mt-1 text-sm text-slate-500">
                                    {cycle.code} ·{' '}
                                    {cycle.transfer_year}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-wrap gap-3">
                        <Link
                            href={route(
                                'admin.transfer-applications.index',
                                {
                                    transfer_cycle_id:
                                        cycle.id,
                                },
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            <FileText className="h-4 w-4" />
                            View Applications
                        </Link>

                        <Link
                            href={route(
                                'admin.transfer-cycles.edit',
                                cycle.id,
                            )}
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            <Pencil className="h-4 w-4" />
                            Edit Cycle
                        </Link>
                    </div>
                </div>
            }
        >
            <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <StatisticCard
                    label="Total Applications"
                    value={cycle.applications_count}
                    icon={Users}
                />

                <StatisticCard
                    label="Draft Applications"
                    value={cycle.draft_applications_count}
                    icon={Clock3}
                />

                <StatisticCard
                    label="Submitted Applications"
                    value={
                        cycle.submitted_applications_count
                    }
                    icon={CheckCircle2}
                />

                <StatisticCard
                    label="Withdrawn Applications"
                    value={
                        cycle.withdrawn_applications_count
                    }
                    icon={XCircle}
                />
            </div>

            <div className="mt-6 grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                    <div className="flex items-center justify-between gap-4">
                        <div>
                            <h2 className="text-lg font-bold text-slate-900">
                                Cycle Information
                            </h2>

                            <p className="mt-1 text-sm text-slate-500">
                                Application period and transfer
                                configuration
                            </p>
                        </div>

                        <span
                            className={[
                                'rounded-full px-3 py-1.5 text-xs font-semibold',
                                statusClasses[cycle.status] ??
                                    'bg-slate-100 text-slate-700',
                            ].join(' ')}
                        >
                            {cycle.status}
                        </span>
                    </div>

                    <dl className="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <DetailItem
                            label="Transfer Type"
                            value={cycle.transfer_type}
                        />

                        <DetailItem
                            label="Transfer Year"
                            value={cycle.transfer_year}
                        />

                        <DetailItem
                            label="Cycle Code"
                            value={cycle.code}
                        />

                        <DetailItem
                            label="Application Opens"
                            value={formatDate(
                                cycle.application_open_date,
                            )}
                        />

                        <DetailItem
                            label="Application Closes"
                            value={formatDate(
                                cycle.application_close_date,
                            )}
                        />

                        <DetailItem
                            label="Effective From"
                            value={formatDate(
                                cycle.effective_from_date,
                            )}
                        />

                        <DetailItem
                            label="Minimum Service"
                            value={`${cycle.minimum_service_years} year(s)`}
                        />

                        <DetailItem
                            label="Maximum Preferences"
                            value={cycle.maximum_preferences}
                        />

                        <DetailItem
                            label="Published At"
                            value={formatDateTime(
                                cycle.published_at,
                            )}
                        />

                        <DetailItem
                            label="Closed At"
                            value={formatDateTime(
                                cycle.closed_at,
                            )}
                        />
                    </dl>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <Settings2 className="h-6 w-6" />
                    </div>

                    <h2 className="mt-5 text-lg font-bold text-slate-900">
                        Cycle Rules
                    </h2>

                    <div className="mt-5 space-y-4">
                        <div className="flex items-start justify-between gap-4 rounded-xl bg-slate-50 p-4">
                            <span className="text-sm text-slate-600">
                                Same-zone preferences
                            </span>

                            <span
                                className={[
                                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                                    cycle.allow_same_zone_preferences
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-red-50 text-red-700',
                                ].join(' ')}
                            >
                                {cycle.allow_same_zone_preferences
                                    ? 'Allowed'
                                    : 'Not allowed'}
                            </span>
                        </div>

                        <div className="flex items-start justify-between gap-4 rounded-xl bg-slate-50 p-4">
                            <span className="text-sm text-slate-600">
                                Other-zone preferences
                            </span>

                            <span
                                className={[
                                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                                    cycle.allow_other_zone_preferences
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-red-50 text-red-700',
                                ].join(' ')}
                            >
                                {cycle.allow_other_zone_preferences
                                    ? 'Allowed'
                                    : 'Not allowed'}
                            </span>
                        </div>

                        <div className="flex items-start justify-between gap-4 rounded-xl bg-slate-50 p-4">
                            <span className="text-sm text-slate-600">
                                Application withdrawal
                            </span>

                            <span
                                className={[
                                    'rounded-full px-2.5 py-1 text-xs font-semibold',
                                    cycle.allow_withdrawal
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-red-50 text-red-700',
                                ].join(' ')}
                            >
                                {cycle.allow_withdrawal
                                    ? 'Allowed'
                                    : 'Not allowed'}
                            </span>
                        </div>
                    </div>
                </section>
            </div>

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <CalendarDays className="h-6 w-6 text-blue-600" />

                        <h2 className="text-lg font-bold text-slate-900">
                            Application Instructions
                        </h2>
                    </div>

                    <p className="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {cycle.instructions ||
                            'No application instructions have been added.'}
                    </p>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <CheckCircle2 className="h-6 w-6 text-blue-600" />

                        <h2 className="text-lg font-bold text-slate-900">
                            Eligibility Notes
                        </h2>
                    </div>

                    <p className="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {cycle.eligibility_notes ||
                            'No additional eligibility notes have been added.'}
                    </p>
                </section>
            </div>
        </AdminLayout>
    );
}
