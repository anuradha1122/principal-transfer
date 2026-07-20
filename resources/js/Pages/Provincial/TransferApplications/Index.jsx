import AdminLayout from '@/Layouts/AdminLayout';
import { Link, router } from '@inertiajs/react';
import {
    Eye,
    Filter,
    Search,
} from 'lucide-react';
import { useState } from 'react';

export default function Index({
    applications,
    filters = {},
    statuses = [],
    zones = [],
}) {
    const [form, setForm] = useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
        zone_id: filters.zone_id ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'provincial.transfer-applications.index'
            ),
            form,
            {
                preserveState: true,
                replace: true,
            }
        );
    };

    return (
        <AdminLayout
            title="Provincial Transfer Applications"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Provincial Transfer Applications
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Review applications approved by Zonal Directors.
                    </p>
                </div>
            }
        >
            <form
                onSubmit={submit}
                className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div className="grid gap-4 lg:grid-cols-4">
                    <div className="lg:col-span-2">
                        <label className="text-sm font-semibold text-slate-700">
                            Search
                        </label>

                        <div className="relative mt-1">
                            <Search className="absolute left-3 top-3 h-5 w-5 text-slate-400" />

                            <input
                                value={form.search}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        search:
                                            event.target.value,
                                    })
                                }
                                placeholder="Application number, principal, NIC or school"
                                className="block w-full rounded-xl border-slate-300 pl-10"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="text-sm font-semibold text-slate-700">
                            Status
                        </label>

                        <select
                            value={form.status}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    status:
                                        event.target.value,
                                })
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
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
                    </div>

                    <div>
                        <label className="text-sm font-semibold text-slate-700">
                            Zone
                        </label>

                        <select
                            value={form.zone_id}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    zone_id:
                                        event.target.value,
                                })
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        >
                            <option value="">
                                All Zones
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
                    </div>
                </div>

                <div className="mt-4 flex justify-end">
                    <button
                        type="submit"
                        className="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700"
                    >
                        <Filter className="h-4 w-4" />
                        Apply Filters
                    </button>
                </div>
            </form>

            <div className="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                {[
                                    'Application',
                                    'Principal',
                                    'Current School',
                                    'Zone',
                                    'Status',
                                    'Action',
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

                        <tbody className="divide-y divide-slate-100">
                            {applications.data.map(
                                (application) => (
                                    <tr
                                        key={application.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4 text-sm font-bold text-slate-900">
                                            {application.application_number}
                                        </td>

                                        <td className="px-5 py-4">
                                            <p className="text-sm font-semibold text-slate-900">
                                                {application.principal_name}
                                            </p>

                                            <p className="text-xs text-slate-500">
                                                {application.nic}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {application.current_school?.name}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {application.origin_zone?.name}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span className="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
                                                {application.status}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <Link
                                                href={route(
                                                    'provincial.transfer-applications.show',
                                                    application.id
                                                )}
                                                className="inline-flex items-center gap-2 text-sm font-semibold text-violet-700 hover:text-violet-900"
                                            >
                                                <Eye className="h-4 w-4" />
                                                Review
                                            </Link>
                                        </td>
                                    </tr>
                                )
                            )}

                            {applications.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan="6"
                                        className="px-5 py-12 text-center text-sm text-slate-500"
                                    >
                                        No Provincial transfer applications found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AdminLayout>
    );
}
