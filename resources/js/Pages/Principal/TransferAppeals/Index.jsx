import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    AlertCircle,
    CheckCircle2,
    Clock3,
    Eye,
    FileEdit,
    FilePlus2,
    RotateCcw,
} from 'lucide-react';
import { useState } from 'react';

const badgeClasses = {
    Draft: 'bg-slate-100 text-slate-700',
    Submitted: 'bg-blue-100 text-blue-700',
    'Under Review': 'bg-amber-100 text-amber-700',
    'Returned for Clarification': 'bg-orange-100 text-orange-700',
    Resubmitted: 'bg-violet-100 text-violet-700',
    Approved: 'bg-emerald-100 text-emerald-700',
    Rejected: 'bg-red-100 text-red-700',
    Withdrawn: 'bg-slate-200 text-slate-700',
};

function SummaryCard({ label, value, icon: Icon }) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm font-medium text-slate-500">
                        {label}
                    </p>
                    <p className="mt-2 text-3xl font-bold text-slate-900">
                        {value ?? 0}
                    </p>
                </div>

                <div className="rounded-xl bg-slate-100 p-3 text-slate-600">
                    <Icon className="h-6 w-6" />
                </div>
            </div>
        </div>
    );
}

export default function Index({
    appeals = {},
    filters = {},
    statuses = [],
    summary = {},
}) {
    const [form, setForm] = useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
    });

    const submitFilters = (event) => {
        event.preventDefault();

        router.get(
            route('principal.transfer-appeals.index'),
            form,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const clearFilters = () => {
        setForm({
            search: '',
            status: '',
        });

        router.get(
            route('principal.transfer-appeals.index'),
            {},
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return (
        <AdminLayout>
            <Head title="Transfer Appeals" />

            <div className="space-y-6">
                <header className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Transfer Appeals
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Create appeals and track their review progress.
                        </p>
                    </div>

                    <Link
                        href={route('principal.transfer-appeals.create')}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        <FilePlus2 className="h-4 w-4" />
                        Create Appeal
                    </Link>
                </header>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <SummaryCard
                        label="Draft Appeals"
                        value={summary.draft}
                        icon={FileEdit}
                    />
                    <SummaryCard
                        label="Submitted Appeals"
                        value={summary.submitted}
                        icon={Clock3}
                    />
                    <SummaryCard
                        label="Returned Appeals"
                        value={summary.returned}
                        icon={RotateCcw}
                    />
                    <SummaryCard
                        label="Completed Appeals"
                        value={summary.completed}
                        icon={CheckCircle2}
                    />
                </div>

                <form
                    onSubmit={submitFilters}
                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div className="grid gap-4 md:grid-cols-3">
                        <input
                            type="text"
                            value={form.search}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    search: event.target.value,
                                })
                            }
                            placeholder="Search appeal or application number"
                            className="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        />

                        <select
                            value={form.status}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    status: event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All statuses</option>

                            {(statuses ?? []).map((status) => (
                                <option key={status} value={status}>
                                    {status}
                                </option>
                            ))}
                        </select>

                        <div className="flex gap-3">
                            <button
                                type="submit"
                                className="inline-flex flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                Filter
                            </button>

                            <button
                                type="button"
                                onClick={clearFilters}
                                className="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </form>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-slate-50">
                                <tr>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Appeal
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Application
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Final Decision
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Status
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Submitted
                                    </th>
                                    <th className="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-slate-100 bg-white">
                                {(appeals.data ?? []).map((appeal) => (
                                    <tr key={appeal.id}>
                                        <td className="px-5 py-4 text-sm font-semibold text-slate-900">
                                            {appeal.appeal_number}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {appeal.transfer_application
                                                ?.application_number ?? '—'}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {appeal.transfer_application
                                                ?.status ?? '—'}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${
                                                    badgeClasses[
                                                        appeal.status
                                                    ] ??
                                                    'bg-slate-100 text-slate-700'
                                                }`}
                                            >
                                                {appeal.status}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {appeal.submitted_at
                                                ? new Date(
                                                      appeal.submitted_at,
                                                  ).toLocaleDateString()
                                                : 'Not submitted'}
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={route(
                                                        'principal.transfer-appeals.show',
                                                        appeal.id,
                                                    )}
                                                    className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                                    title="View appeal"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>

                                                {appeal.status === 'Draft' && (
                                                    <Link
                                                        href={route(
                                                            'principal.transfer-appeals.edit',
                                                            appeal.id,
                                                        )}
                                                        className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                                        title="Edit appeal"
                                                    >
                                                        <FileEdit className="h-4 w-4" />
                                                    </Link>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}

                                {(appeals.data ?? []).length === 0 && (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="px-5 py-16 text-center"
                                        >
                                            <AlertCircle className="mx-auto h-10 w-10 text-slate-300" />
                                            <p className="mt-3 font-semibold text-slate-700">
                                                No transfer appeals found
                                            </p>
                                            <p className="mt-1 text-sm text-slate-500">
                                                Create an appeal for an eligible
                                                published final decision.
                                            </p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {(appeals.links ?? []).length > 0 && (
                        <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                            {(appeals.links ?? []).map((link, index) =>
                                link.url ? (
                                    <Link
                                        key={`${link.label}-${index}`}
                                        href={link.url}
                                        preserveScroll
                                        className={`rounded-lg px-3 py-2 text-sm ${
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                                        }`}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ) : (
                                    <span
                                        key={`${link.label}-${index}`}
                                        className="rounded-lg border border-slate-100 px-3 py-2 text-sm text-slate-300"
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ),
                            )}
                        </div>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
