import AdminLayout from '@/Layouts/AdminLayout';
import {
    CalendarRange,
    Eye,
    Pencil,
    Plus,
    Search,
    Trash2,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
} from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Not set';
    }

    return new Intl.DateTimeFormat(
        'en-LK',
        {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
        },
    ).format(new Date(value));
}

export default function Index({
    cycles,
    filters,
    statuses,
    years,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        status: filters.status ?? '',
        year: filters.year ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'admin.transfer-cycles.index',
            ),
            data,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const remove = (cycle) => {
        if (
            !window.confirm(
                `Delete transfer cycle "${cycle.name}"?`,
            )
        ) {
            return;
        }

        router.delete(
            route(
                'admin.transfer-cycles.destroy',
                cycle.id,
            ),
        );
    };

    return (
        <AdminLayout
            title="Transfer Cycles"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Transfer Cycles
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Manage transfer application periods and rules.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.transfer-cycles.create',
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Create Cycle
                    </Link>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <form
                    onSubmit={submit}
                    className="grid gap-4 border-b border-slate-200 p-5 md:grid-cols-4"
                >
                    <div className="relative">
                        <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />

                        <input
                            value={data.search}
                            onChange={(event) =>
                                setData(
                                    'search',
                                    event.target.value,
                                )
                            }
                            placeholder="Name or cycle code"
                            className="w-full rounded-xl border-slate-300 pl-10 text-sm"
                        />
                    </div>

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
                        value={data.year}
                        onChange={(event) =>
                            setData(
                                'year',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All years
                        </option>

                        {years.map((year) => (
                            <option
                                key={year}
                                value={year}
                            >
                                {year}
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
                                'admin.transfer-cycles.index',
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
                                    'Cycle',
                                    'Type',
                                    'Application Period',
                                    'Status',
                                    'Applications',
                                    'Actions',
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
                            {cycles.data.map(
                                (cycle) => (
                                    <tr
                                        key={cycle.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-900">
                                                {cycle.name}
                                            </p>

                                            <p className="text-xs text-slate-500">
                                                {cycle.code} ·{' '}
                                                {cycle.transfer_year}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                cycle.transfer_type
                                            }
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            <p>
                                                {formatDate(
                                                    cycle.application_open_date,
                                                )}
                                            </p>
                                            <p className="text-xs text-slate-400">
                                                to{' '}
                                                {formatDate(
                                                    cycle.application_close_date,
                                                )}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    cycle.status ===
                                                    'Published'
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : cycle.status ===
                                                            'Draft'
                                                          ? 'bg-amber-50 text-amber-700'
                                                          : 'bg-slate-100 text-slate-700',
                                                ].join(
                                                    ' ',
                                                )}
                                            >
                                                {cycle.status}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4 text-sm font-semibold text-slate-700">
                                            {
                                                cycle.applications_count
                                            }
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex gap-2">
                                                <Link
                                                    href={route(
                                                        'admin.transfer-cycles.show',
                                                        cycle.id,
                                                    )}
                                                    className="rounded-lg p-2 text-slate-600 hover:bg-slate-100"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>

                                                <Link
                                                    href={route(
                                                        'admin.transfer-cycles.edit',
                                                        cycle.id,
                                                    )}
                                                    className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>

                                                {cycle.applications_count ===
                                                    0 && (
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            remove(
                                                                cycle,
                                                            )
                                                        }
                                                        className="rounded-lg p-2 text-red-600 hover:bg-red-50"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ),
                            )}
                        </tbody>
                    </table>
                </div>

                {cycles.data.length === 0 && (
                    <div className="flex flex-col items-center px-6 py-14 text-center">
                        <CalendarRange className="h-10 w-10 text-slate-300" />
                        <p className="mt-3 text-sm text-slate-500">
                            No transfer cycles found.
                        </p>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
