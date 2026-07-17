import AdminLayout from '@/Layouts/AdminLayout';
import {
    Eye,
    Pencil,
    Plus,
    Search,
    UserRound,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
} from '@inertiajs/react';

export default function Index({
    profiles,
    filters,
    statuses,
    serviceGrades,
    zones,
    schools,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        status: filters.status ?? '',
        service_grade:
            filters.service_grade ?? '',
        zone_id: filters.zone_id ?? '',
        school_id: filters.school_id ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'admin.principal-profiles.index',
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
            title="Principal Profiles"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Principal Profiles
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Manage principal service records and current appointments.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.principal-profiles.create',
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Plus className="h-4 w-4" />
                        Create Profile
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
                            value={data.search}
                            onChange={(event) =>
                                setData(
                                    'search',
                                    event.target.value,
                                )
                            }
                            placeholder="Name, NIC or employee no."
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
                        value={data.service_grade}
                        onChange={(event) =>
                            setData(
                                'service_grade',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm"
                    >
                        <option value="">
                            All grades
                        </option>

                        {serviceGrades.map(
                            (grade) => (
                                <option
                                    key={grade}
                                    value={grade}
                                >
                                    {grade}
                                </option>
                            ),
                        )}
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
                            className="flex-1 rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white"
                        >
                            Filter
                        </button>

                        <Link
                            href={route(
                                'admin.principal-profiles.index',
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
                                    'Principal',
                                    'NIC',
                                    'Service Grade',
                                    'Current Appointment',
                                    'Status',
                                    'Profile',
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
                            {profiles.data.map(
                                (profile) => (
                                    <tr
                                        key={profile.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-900">
                                                {
                                                    profile.full_name
                                                }
                                            </p>

                                            <p className="text-xs text-slate-500">
                                                {profile.employee_number ||
                                                    profile.user
                                                        ?.email}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 font-mono text-sm text-slate-600">
                                            {profile.nic}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {profile.service_grade ||
                                                'Not set'}
                                        </td>

                                        <td className="px-5 py-4">
                                            {profile.current_appointment ? (
                                                <>
                                                    <p className="text-sm font-semibold text-slate-800">
                                                        {
                                                            profile
                                                                .current_appointment
                                                                .designation
                                                        }
                                                    </p>

                                                    <p className="text-xs text-slate-500">
                                                        {
                                                            profile
                                                                .current_appointment
                                                                .school
                                                                ?.name
                                                        }
                                                    </p>
                                                </>
                                            ) : (
                                                <span className="text-sm text-slate-400">
                                                    No current appointment
                                                </span>
                                            )}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                {
                                                    profile.employment_status
                                                }
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    profile.profile_completed
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-amber-50 text-amber-700',
                                                ].join(
                                                    ' ',
                                                )}
                                            >
                                                {profile.profile_completed
                                                    ? 'Complete'
                                                    : 'Incomplete'}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex gap-2">
                                                <Link
                                                    href={route(
                                                        'admin.principal-profiles.show',
                                                        profile.id,
                                                    )}
                                                    className="rounded-lg p-2 text-slate-600 hover:bg-slate-100"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>

                                                <Link
                                                    href={route(
                                                        'admin.principal-profiles.edit',
                                                        profile.id,
                                                    )}
                                                    className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ),
                            )}
                        </tbody>
                    </table>
                </div>

                {profiles.data.length === 0 && (
                    <div className="flex flex-col items-center px-6 py-14 text-center">
                        <UserRound className="h-10 w-10 text-slate-300" />

                        <p className="mt-3 text-sm text-slate-500">
                            No principal profiles matched the filters.
                        </p>
                    </div>
                )}

                {profiles.links.length > 3 && (
                    <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                        {profiles.links.map(
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
