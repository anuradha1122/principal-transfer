import AdminLayout from '@/Layouts/AdminLayout';
import {
    Building2,
    Eye,
    Pencil,
    Plus,
    Search,
    School,
    Trash2,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
} from '@inertiajs/react';

export default function Index({
    zones,
    filters,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        district: filters.district ?? '',
        status: filters.status ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route('admin.zones.index'),
            data,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const removeZone = (zone) => {
        if (
            !window.confirm(
                `Delete the ${zone.name} Education Zone?`,
            )
        ) {
            return;
        }

        router.delete(
            route('admin.zones.destroy', zone.id),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Education Zones"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Education Zones
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Manage the seven education zones in
                            Sabaragamuwa Province.
                        </p>
                    </div>

                    <Link
                        href={route('admin.zones.create')}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        <Plus className="h-4 w-4" />
                        Create Zone
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
                            type="text"
                            value={data.search}
                            onChange={(event) =>
                                setData(
                                    'search',
                                    event.target.value,
                                )
                            }
                            placeholder="Search zone"
                            className="w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <select
                        value={data.district}
                        onChange={(event) =>
                            setData(
                                'district',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All districts
                        </option>
                        <option value="Ratnapura">
                            Ratnapura
                        </option>
                        <option value="Kegalle">
                            Kegalle
                        </option>
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
                        <option value="active">
                            Active
                        </option>
                        <option value="inactive">
                            Inactive
                        </option>
                    </select>

                    <div className="flex gap-2">
                        <button
                            type="submit"
                            className="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Filter
                        </button>

                        <Link
                            href={route('admin.zones.index')}
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
                                    'Zone',
                                    'District',
                                    'Divisions',
                                    'Schools',
                                    'Status',
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
                            {zones.data.map((zone) => (
                                <tr
                                    key={zone.id}
                                    className="hover:bg-slate-50"
                                >
                                    <td className="px-5 py-4">
                                        <p className="font-semibold text-slate-900">
                                            {zone.name}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            {zone.code}
                                        </p>
                                    </td>

                                    <td className="px-5 py-4 text-sm text-slate-600">
                                        {zone.district}
                                    </td>

                                    <td className="px-5 py-4">
                                        <span className="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <Building2 className="h-4 w-4 text-slate-400" />
                                            {zone.divisions_count}
                                        </span>
                                    </td>

                                    <td className="px-5 py-4">
                                        <span className="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <School className="h-4 w-4 text-slate-400" />
                                            {zone.schools_count}
                                        </span>
                                    </td>

                                    <td className="px-5 py-4">
                                        <span
                                            className={[
                                                'rounded-full px-3 py-1 text-xs font-semibold',
                                                zone.is_active
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-red-50 text-red-700',
                                            ].join(' ')}
                                        >
                                            {zone.is_active
                                                ? 'Active'
                                                : 'Inactive'}
                                        </span>
                                    </td>

                                    <td className="px-5 py-4">
                                        <div className="flex gap-2">
                                            <Link
                                                href={route(
                                                    'admin.zones.show',
                                                    zone.id,
                                                )}
                                                className="rounded-lg p-2 text-slate-600 hover:bg-slate-100"
                                            >
                                                <Eye className="h-4 w-4" />
                                            </Link>

                                            <Link
                                                href={route(
                                                    'admin.zones.edit',
                                                    zone.id,
                                                )}
                                                className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                            >
                                                <Pencil className="h-4 w-4" />
                                            </Link>

                                            <button
                                                type="button"
                                                onClick={() =>
                                                    removeZone(zone)
                                                }
                                                className="rounded-lg p-2 text-red-600 hover:bg-red-50"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {zones.data.length === 0 && (
                    <div className="px-6 py-14 text-center text-sm text-slate-500">
                        No education zones matched the filters.
                    </div>
                )}

                {zones.links.length > 3 && (
                    <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                        {zones.links.map((link) => (
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
                                    __html: link.label,
                                }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
