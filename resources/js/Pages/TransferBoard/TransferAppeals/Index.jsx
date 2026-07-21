import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock3,
    Eye,
    RotateCcw,
    Search,
    XCircle,
} from 'lucide-react';
import { useState } from 'react';

const badgeClasses = {
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
    cycles = [],
    summary = {},
}) {
    const [form, setForm] = useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
        transfer_cycle_id: filters.transfer_cycle_id ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route('transfer-board.transfer-appeals.index'),
            form,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return (
        <AdminLayout>
            <Head title="Transfer Appeals Review" />

            <div className="space-y-6">
                <header>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Transfer Appeals
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Review submitted appeals and record final outcomes.
                    </p>
                </header>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <SummaryCard
                        label="Pending Review"
                        value={summary.pending}
                        icon={Clock3}
                    />
                    <SummaryCard
                        label="Under Review"
                        value={summary.under_review}
                        icon={Search}
                    />
                    <SummaryCard
                        label="Returned"
                        value={summary.returned}
                        icon={RotateCcw}
                    />
                    <SummaryCard
                        label="Approved"
                        value={summary.approved}
                        icon={CheckCircle2}
                    />
                    <SummaryCard
                        label="Rejected"
                        value={summary.rejected}
                        icon={XCircle}
                    />
                </div>

                <form
                    onSubmit={submit}
                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div className="grid gap-4 md:grid-cols-4">
                        <input
                            value={form.search}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    search: event.target.value,
                                })
                            }
                            placeholder="Search appeal, application, Principal"
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

                        <select
                            value={form.transfer_cycle_id}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    transfer_cycle_id:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">All transfer cycles</option>
                            {(cycles ?? []).map((cycle) => (
                                <option key={cycle.id} value={cycle.id}>
                                    {cycle.name}
                                </option>
                            ))}
                        </select>

                        <button
                            type="submit"
                            className="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                        >
                            Apply Filters
                        </button>
                    </div>
                </form>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-slate-50">
                                <tr>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                        Appeal
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                        Principal
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                        Application
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                        Submitted
                                    </th>
                                    <th className="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">
                                        Status
                                    </th>
                                    <th className="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-slate-100">
                                {(appeals.data ?? []).map((appeal) => (
                                    <tr key={appeal.id}>
                                        <td className="px-5 py-4 text-sm font-semibold text-slate-900">
                                            {appeal.appeal_number}
                                        </td>
                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {appeal.principal_profile
                                                ?.full_name ??
                                                appeal.principal_profile
                                                    ?.user?.name ??
                                                '—'}
                                        </td>
                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {appeal.transfer_application
                                                ?.application_number ?? '—'}
                                        </td>
                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {appeal.submitted_at
                                                ? new Date(
                                                      appeal.submitted_at,
                                                  ).toLocaleDateString()
                                                : '—'}
                                        </td>
                                        <td className="px-5 py-4">
                                            <span
                                                className={`rounded-full px-2.5 py-1 text-xs font-semibold ${
                                                    badgeClasses[
                                                        appeal.status
                                                    ] ??
                                                    'bg-slate-100 text-slate-700'
                                                }`}
                                            >
                                                {appeal.status}
                                            </span>
                                        </td>
                                        <td className="px-5 py-4 text-right">
                                            <Link
                                                href={route(
                                                    'transfer-board.transfer-appeals.show',
                                                    appeal.id,
                                                )}
                                                className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                            >
                                                <Eye className="h-4 w-4" />
                                            </Link>
                                        </td>
                                    </tr>
                                ))}

                                {(appeals.data ?? []).length === 0 && (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="px-5 py-16 text-center text-sm text-slate-500"
                                        >
                                            No transfer appeals found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {(appeals.links ?? []).length > 0 && (
                        <div className="flex flex-wrap gap-2 border-t border-slate-200 p-4">
                            {(appeals.links ?? []).map((link, index) =>
                                link.url ? (
                                    <Link
                                        key={index}
                                        href={link.url}
                                        preserveScroll
                                        className={`rounded-lg px-3 py-2 text-sm ${
                                            link.active
                                                ? 'bg-blue-600 text-white'
                                                : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
                                        }`}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                ) : (
                                    <span
                                        key={index}
                                        className="rounded-lg px-3 py-2 text-sm text-slate-300"
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
