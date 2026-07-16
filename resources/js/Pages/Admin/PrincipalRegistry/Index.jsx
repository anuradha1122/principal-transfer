import AdminLayout from '@/Layouts/AdminLayout';
import {
    Download,
    Eye,
    FileUp,
    Pencil,
    Plus,
    Search,
    Trash2,
    UserCheck,
    UserMinus,
    Users,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
} from '@inertiajs/react';

function StatisticCard({
    label,
    value,
    icon: Icon,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <Icon className="h-6 w-6 text-blue-600" />

            <p className="mt-4 text-3xl font-bold text-slate-900">
                {value}
            </p>

            <p className="mt-1 text-sm text-slate-500">
                {label}
            </p>
        </div>
    );
}

export default function Index({
    registries,
    filters,
    schools,
    designations,
    statistics,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        status: filters.status ?? '',
        designation: filters.designation ?? '',
        school_id: filters.school_id ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'admin.principal-registry.index',
            ),
            data,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const removeRecord = (record) => {
        if (
            !window.confirm(
                `Delete the registry record for ${record.nic}?`,
            )
        ) {
            return;
        }

        router.delete(
            route(
                'admin.principal-registry.destroy',
                record.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Principal Registry"
            header={
                <div className="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Principal Registry
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Manage NIC numbers eligible for
                            controlled self-registration.
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-3">
                        <a
                            href={route(
                                'admin.principal-registry.template',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <Download className="h-4 w-4" />
                            CSV Template
                        </a>

                        <Link
                            href={route(
                                'admin.principal-registry.import-page',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <FileUp className="h-4 w-4" />
                            Import CSV
                        </Link>

                        <Link
                            href={route(
                                'admin.principal-registry.create',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            <Plus className="h-4 w-4" />
                            Add NIC
                        </Link>
                    </div>
                </div>
            }
        >
            <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <StatisticCard
                    label="Total Registry Records"
                    value={statistics.total}
                    icon={Users}
                />
                <StatisticCard
                    label="Awaiting Registration"
                    value={statistics.unregistered}
                    icon={UserMinus}
                />
                <StatisticCard
                    label="Registered"
                    value={statistics.registered}
                    icon={UserCheck}
                />
                <StatisticCard
                    label="Disabled"
                    value={statistics.disabled}
                    icon={UserMinus}
                />
            </div>

            <div className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
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
                            placeholder="NIC, name or employee no."
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
                        <option value="unregistered">
                            Unregistered
                        </option>
                        <option value="registered">
                            Registered
                        </option>
                        <option value="disabled">
                            Disabled
                        </option>
                    </select>

                    <select
                        value={data.designation}
                        onChange={(event) =>
                            setData(
                                'designation',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All designations
                        </option>

                        {designations.map(
                            (designation) => (
                                <option
                                    key={designation}
                                    value={designation}
                                >
                                    {designation}
                                </option>
                            ),
                        )}
                    </select>

                    <select
                        value={data.school_id}
                        onChange={(event) =>
                            setData(
                                'school_id',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All schools
                        </option>

                        {schools.map((school) => (
                            <option
                                key={school.id}
                                value={school.id}
                            >
                                {school.name}
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
                                'admin.principal-registry.index',
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
                                    'Principal',
                                    'NIC',
                                    'Designation',
                                    'School',
                                    'Status',
                                    'Account',
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
                            {registries.data.map(
                                (record) => (
                                    <tr
                                        key={record.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-900">
                                                {record.full_name ||
                                                    record.name_with_initials ||
                                                    'Name not recorded'}
                                            </p>

                                            <p className="text-xs text-slate-500">
                                                {record.employee_number ||
                                                    'No employee number'}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 font-mono text-sm text-slate-700">
                                            {record.nic}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {record.designation ||
                                                'Not assigned'}
                                        </td>

                                        <td className="px-5 py-4">
                                            <p className="text-sm font-medium text-slate-700">
                                                {record.school
                                                    ?.name ||
                                                    'Not assigned'}
                                            </p>

                                            {record.school
                                                ?.division && (
                                                <p className="text-xs text-slate-500">
                                                    {
                                                        record
                                                            .school
                                                            .division
                                                            .zone
                                                            ?.name
                                                    }{' '}
                                                    Zone
                                                </p>
                                            )}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    record.registration_status ===
                                                    'registered'
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : record.registration_status ===
                                                            'disabled'
                                                          ? 'bg-red-50 text-red-700'
                                                          : 'bg-amber-50 text-amber-700',
                                                ].join(
                                                    ' ',
                                                )}
                                            >
                                                {
                                                    record.registration_status
                                                }
                                            </span>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {record.registered_user
                                                ?.email ||
                                                'No account'}
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex gap-2">
                                                <Link
                                                    href={route(
                                                        'admin.principal-registry.show',
                                                        record.id,
                                                    )}
                                                    className="rounded-lg p-2 text-slate-600 hover:bg-slate-100"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>

                                                <Link
                                                    href={route(
                                                        'admin.principal-registry.edit',
                                                        record.id,
                                                    )}
                                                    className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>

                                                {!record.registered_user_id && (
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            removeRecord(
                                                                record,
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

                {registries.data.length === 0 && (
                    <div className="px-6 py-14 text-center text-sm text-slate-500">
                        No principal registry records matched
                        the filters.
                    </div>
                )}

                {registries.links.length > 3 && (
                    <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                        {registries.links.map(
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
            </div>
        </AdminLayout>
    );
}
