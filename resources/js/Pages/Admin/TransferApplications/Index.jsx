import AdminLayout from '@/Layouts/AdminLayout';
import {
    Eye,
    FileSearch,
    Search,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
} from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Not submitted';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(new Date(value));
}

function statusClass(status) {
    const classes = {
        Draft: 'bg-slate-100 text-slate-700',
        Submitted: 'bg-blue-50 text-blue-700',
        'Zonal Review':
            'bg-amber-50 text-amber-700',
        'Zonal Approved':
            'bg-emerald-50 text-emerald-700',
        'Zonal Rejected':
            'bg-red-50 text-red-700',
        'Provincial Review':
            'bg-amber-50 text-amber-700',
        'Provincial Approved':
            'bg-emerald-50 text-emerald-700',
        'Provincial Rejected':
            'bg-red-50 text-red-700',
        'Board Review':
            'bg-violet-50 text-violet-700',
        Approved:
            'bg-emerald-50 text-emerald-700',
        Rejected: 'bg-red-50 text-red-700',
        Waitlisted:
            'bg-orange-50 text-orange-700',
        Withdrawn:
            'bg-slate-100 text-slate-600',
        Cancelled: 'bg-red-50 text-red-700',
    };

    return (
        classes[status] ??
        'bg-slate-100 text-slate-700'
    );
}

export default function Index({
    applications,
    filters,
    cycles,
    zones,
    statuses,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        transfer_cycle_id:
            filters.transfer_cycle_id ?? '',
        status: filters.status ?? '',
        zone_id: filters.zone_id ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'admin.transfer-applications.index',
            ),
            data,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Transfer Applications"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Transfer Applications
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        View submitted principal transfer
                        applications and their current workflow
                        status.
                    </p>
                </div>
            }
        >
            <section className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <form
                    onSubmit={submit}
                    className="grid gap-4 border-b border-slate-200 p-5 md:grid-cols-2 xl:grid-cols-5"
                >
                    <div className="relative">
                        <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />

                        <input
                            type="text"
                            value={data.search}
                            onChange={(event) =>
                                setData(
                                    'search',
                                    event.target.value,
                                )
                            }
                            placeholder="Application no., name, NIC"
                            className="w-full rounded-xl border-slate-300 pl-10 text-sm"
                        />
                    </div>

                    <select
                        value={data.transfer_cycle_id}
                        onChange={(event) =>
                            setData(
                                'transfer_cycle_id',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All transfer cycles
                        </option>

                        {cycles.map((cycle) => (
                            <option
                                key={cycle.id}
                                value={cycle.id}
                            >
                                {cycle.name} ({cycle.code})
                            </option>
                        ))}
                    </select>

                    <select
                        value={data.status}
                        onChange={(event) =>
                            setData(
                                'status',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
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

                    <select
                        value={data.zone_id}
                        onChange={(event) =>
                            setData(
                                'zone_id',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
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

                    <div className="flex gap-2">
                        <button
                            type="submit"
                            className="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Filter
                        </button>

                        <Link
                            href={route(
                                'admin.transfer-applications.index',
                            )}
                            className="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700"
                        >
                            Clear
                        </Link>
                    </div>
                </form>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                {[
                                    'Application',
                                    'Principal',
                                    'Current School',
                                    'Zone',
                                    'Transfer Cycle',
                                    'Reason',
                                    'Status',
                                    'Submitted',
                                    'Action',
                                ].map((heading) => (
                                    <th
                                        key={heading}
                                        className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
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
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-900">
                                                {application.application_number ||
                                                    `Draft #${application.id}`}
                                            </p>

                                            <p className="mt-1 text-xs text-slate-500">
                                                ID: {application.id}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-900">
                                                {
                                                    application.principal_name
                                                }
                                            </p>

                                            <p className="mt-1 font-mono text-xs text-slate-500">
                                                {application.nic}
                                            </p>

                                            <p className="text-xs text-slate-500">
                                                {application.employee_number ||
                                                    'No employee number'}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4">
                                            <p className="text-sm font-semibold text-slate-800">
                                                {application
                                                    .current_school
                                                    ?.name ||
                                                    'Not recorded'}
                                            </p>

                                            <p className="mt-1 text-xs text-slate-500">
                                                {
                                                    application.current_designation
                                                }
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {application
                                                .current_school
                                                ?.division?.zone
                                                ?.name ||
                                                'Not recorded'}
                                        </td>

                                        <td className="px-5 py-4">
                                            <p className="text-sm font-semibold text-slate-700">
                                                {
                                                    application
                                                        .transfer_cycle
                                                        ?.name
                                                }
                                            </p>

                                            <p className="mt-1 text-xs text-slate-500">
                                                {
                                                    application
                                                        .transfer_cycle
                                                        ?.code
                                                }
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                application.transfer_reason
                                            }
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    statusClass(
                                                        application.status,
                                                    ),
                                                ].join(' ')}
                                            >
                                                {application.status}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {formatDate(
                                                application.submitted_at,
                                            )}
                                        </td>

                                        <td className="px-5 py-4">
                                            <Link
                                                href={route(
                                                    'admin.transfer-applications.show',
                                                    application.id,
                                                )}
                                                className="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-blue-600 transition hover:bg-blue-50"
                                            >
                                                <Eye className="h-4 w-4" />
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                ),
                            )}
                        </tbody>
                    </table>
                </div>

                {applications.data.length === 0 && (
                    <div className="flex flex-col items-center px-6 py-14 text-center">
                        <FileSearch className="h-10 w-10 text-slate-300" />

                        <p className="mt-3 text-sm font-semibold text-slate-700">
                            No transfer applications found
                        </p>

                        <p className="mt-1 text-sm text-slate-500">
                            Adjust the filters or wait for principals
                            to submit applications.
                        </p>
                    </div>
                )}

                {applications.links.length > 3 && (
                    <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                        {applications.links.map(
                            (link) => (
                                <Link
                                    key={link.label}
                                    href={link.url ?? '#'}
                                    preserveScroll
                                    className={[
                                        'rounded-lg px-3 py-2 text-sm',
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'border border-slate-200 text-slate-600',
                                        !link.url
                                            ? 'pointer-events-none opacity-50'
                                            : '',
                                    ].join(' ')}
                                    dangerouslySetInnerHTML={{
                                        __html:
                                            link.label,
                                    }}
                                />
                            ),
                        )}
                    </div>
                )}
            </section>
        </AdminLayout>
    );
}
