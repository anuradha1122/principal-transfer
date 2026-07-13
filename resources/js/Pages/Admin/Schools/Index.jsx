import AdminLayout from '@/Layouts/AdminLayout';
import {
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
import { useMemo } from 'react';

export default function Index({
    schools,
    zones,
    divisions,
    filters,
    schoolTypes,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        zone_id: filters.zone_id ?? '',
        division_id: filters.division_id ?? '',
        school_type: filters.school_type ?? '',
        status: filters.status ?? '',
    });

    const filteredDivisions = useMemo(() => {
        if (!data.zone_id) {
            return divisions;
        }

        return divisions.filter(
            (division) =>
                String(division.zone_id) ===
                String(data.zone_id),
        );
    }, [data.zone_id, divisions]);

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route('admin.schools.index'),
            data,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const removeSchool = (school) => {
        if (
            !window.confirm(
                `Delete ${school.name}?`,
            )
        ) {
            return;
        }

        router.delete(
            route(
                'admin.schools.destroy',
                school.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Schools"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Schools
                        </h1>
                        <p className="mt-1 text-sm text-slate-500">
                            Maintain schools within zones and
                            divisions.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.schools.create',
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Create School
                    </Link>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <form
                    onSubmit={submit}
                    className="grid gap-4 border-b border-slate-200 p-5 md:grid-cols-3 xl:grid-cols-6"
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
                            placeholder="Name or census no."
                            className="w-full rounded-xl border-slate-300 pl-10 text-sm"
                        />
                    </div>

                    <select
                        value={data.zone_id}
                        onChange={(event) => {
                            setData((current) => ({
                                ...current,
                                zone_id:
                                    event.target.value,
                                division_id: '',
                            }));
                        }}
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

                    <select
                        value={data.division_id}
                        onChange={(event) =>
                            setData(
                                'division_id',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All divisions
                        </option>
                        {filteredDivisions.map(
                            (division) => (
                                <option
                                    key={division.id}
                                    value={division.id}
                                >
                                    {division.name}
                                </option>
                            ),
                        )}
                    </select>

                    <select
                        value={data.school_type}
                        onChange={(event) =>
                            setData(
                                'school_type',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All school types
                        </option>
                        {schoolTypes.map((type) => (
                            <option
                                key={type}
                                value={type}
                            >
                                {type}
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
                            className="flex-1 rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white"
                        >
                            Filter
                        </button>
                        <Link
                            href={route(
                                'admin.schools.index',
                            )}
                            className="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700"
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
                                    'School',
                                    'Zone',
                                    'Division',
                                    'Type',
                                    'Medium',
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
                            {schools.data.map((school) => (
                                <tr
                                    key={school.id}
                                    className="hover:bg-slate-50"
                                >
                                    <td className="px-5 py-4">
                                        <p className="font-semibold text-slate-900">
                                            {school.name}
                                        </p>
                                        <p className="text-xs text-slate-500">
                                            Census:{' '}
                                            {
                                                school.census_number
                                            }
                                        </p>
                                    </td>

                                    <td className="px-5 py-4 text-sm text-slate-600">
                                        {
                                            school.division.zone
                                                .name
                                        }
                                    </td>

                                    <td className="px-5 py-4 text-sm text-slate-600">
                                        {
                                            school.division
                                                .name
                                        }
                                    </td>

                                    <td className="px-5 py-4 text-sm text-slate-600">
                                        {school.school_type ||
                                            'Not set'}
                                    </td>

                                    <td className="px-5 py-4">
                                        <div className="flex flex-wrap gap-1">
                                            {(school.mediums ?? [])
                                                .map(
                                                    (
                                                        medium,
                                                    ) => (
                                                        <span
                                                            key={
                                                                medium
                                                            }
                                                            className="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600"
                                                        >
                                                            {
                                                                medium
                                                            }
                                                        </span>
                                                    ),
                                                )}
                                        </div>
                                    </td>

                                    <td className="px-5 py-4">
                                        <span
                                            className={[
                                                'rounded-full px-3 py-1 text-xs font-semibold',
                                                school.is_active
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-red-50 text-red-700',
                                            ].join(' ')}
                                        >
                                            {school.is_active
                                                ? 'Active'
                                                : 'Inactive'}
                                        </span>
                                    </td>

                                    <td className="px-5 py-4">
                                        <div className="flex gap-2">
                                            <Link
                                                href={route(
                                                    'admin.schools.show',
                                                    school.id,
                                                )}
                                                className="rounded-lg p-2 text-slate-600 hover:bg-slate-100"
                                            >
                                                <Eye className="h-4 w-4" />
                                            </Link>

                                            <Link
                                                href={route(
                                                    'admin.schools.edit',
                                                    school.id,
                                                )}
                                                className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                            >
                                                <Pencil className="h-4 w-4" />
                                            </Link>

                                            <button
                                                type="button"
                                                onClick={() =>
                                                    removeSchool(
                                                        school,
                                                    )
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

                {schools.data.length === 0 && (
                    <div className="px-6 py-14 text-center text-sm text-slate-500">
                        No schools matched the selected filters.
                    </div>
                )}

                {schools.links.length > 3 && (
                    <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                        {schools.links.map((link) => (
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
