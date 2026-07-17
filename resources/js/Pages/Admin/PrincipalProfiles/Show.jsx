import AdminLayout from '@/Layouts/AdminLayout';
import {
    Building2,
    CalendarDays,
    Mail,
    Pencil,
    Phone,
    Plus,
    Trash2,
    UserRound,
} from 'lucide-react';
import {
    Link,
    router,
} from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Not recorded';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    }).format(new Date(value));
}

export default function Show({ profile }) {
    const removeAppointment = (appointment) => {
        if (
            !window.confirm(
                'Delete this appointment record?',
            )
        ) {
            return;
        }

        router.delete(
            route(
                'admin.principal-appointments.destroy',
                appointment.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title={profile.full_name}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            {profile.full_name}
                        </h1>

                        <p className="mt-1 font-mono text-sm text-slate-500">
                            {profile.nic}
                        </p>
                    </div>

                    <div className="flex gap-3">
                        <Link
                            href={route(
                                'admin.principal-profiles.appointments.create',
                                profile.id,
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <Plus className="h-4 w-4" />
                            Add Appointment
                        </Link>

                        <Link
                            href={route(
                                'admin.principal-profiles.edit',
                                profile.id,
                            )}
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            <Pencil className="h-4 w-4" />
                            Edit Profile
                        </Link>
                    </div>
                </div>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <UserRound className="h-8 w-8 text-blue-600" />

                    <h2 className="mt-5 font-bold text-slate-900">
                        Personal Details
                    </h2>

                    <dl className="mt-5 space-y-4 text-sm">
                        <div>
                            <dt className="text-slate-500">
                                Employee number
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {profile.employee_number ||
                                    'Not recorded'}
                            </dd>
                        </div>

                        <div className="flex gap-2">
                            <Phone className="h-4 w-4 text-slate-400" />
                            <span>
                                {profile.mobile_number ||
                                    'Not recorded'}
                            </span>
                        </div>

                        <div className="flex gap-2">
                            <Mail className="h-4 w-4 text-slate-400" />
                            <span>
                                {profile.personal_email ||
                                    profile.user?.email}
                            </span>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                Date of birth
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {formatDate(
                                    profile.date_of_birth,
                                )}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <CalendarDays className="h-8 w-8 text-blue-600" />

                    <h2 className="mt-5 font-bold text-slate-900">
                        Service Details
                    </h2>

                    <dl className="mt-5 space-y-4 text-sm">
                        <div>
                            <dt className="text-slate-500">
                                Service category
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {profile.service_category}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                Service grade
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {profile.service_grade ||
                                    'Not recorded'}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                First appointment
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {formatDate(
                                    profile.first_appointment_date,
                                )}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                Employment status
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {profile.employment_status}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <Building2 className="h-8 w-8 text-blue-600" />

                    <h2 className="mt-5 font-bold text-slate-900">
                        Current Appointment
                    </h2>

                    {profile.appointments.find(
                        (appointment) =>
                            appointment.is_current,
                    ) ? (
                        (() => {
                            const current =
                                profile.appointments.find(
                                    (appointment) =>
                                        appointment.is_current,
                                );

                            return (
                                <div className="mt-5">
                                    <p className="font-bold text-slate-900">
                                        {
                                            current.designation
                                        }
                                    </p>

                                    <p className="mt-1 text-sm text-slate-700">
                                        {
                                            current.school
                                                ?.name
                                        }
                                    </p>

                                    <p className="mt-1 text-xs text-slate-500">
                                        {
                                            current.school
                                                ?.division
                                                ?.name
                                        }{' '}
                                        Division ·{' '}
                                        {
                                            current.school
                                                ?.division
                                                ?.zone
                                                ?.name
                                        }{' '}
                                        Zone
                                    </p>

                                    <p className="mt-4 text-xs text-slate-500">
                                        Since{' '}
                                        {formatDate(
                                            current.start_date,
                                        )}
                                    </p>
                                </div>
                            );
                        })()
                    ) : (
                        <p className="mt-5 text-sm text-slate-500">
                            No current appointment recorded.
                        </p>
                    )}
                </section>
            </div>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="border-b border-slate-200 px-6 py-5">
                    <h2 className="font-bold text-slate-900">
                        Appointment History
                    </h2>
                </div>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                {[
                                    'School',
                                    'Designation',
                                    'Type',
                                    'Start',
                                    'End',
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
                            {profile.appointments.map(
                                (appointment) => (
                                    <tr key={appointment.id}>
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-800">
                                                {
                                                    appointment
                                                        .school
                                                        ?.name
                                                }
                                            </p>

                                            <p className="text-xs text-slate-500">
                                                {
                                                    appointment
                                                        .school
                                                        ?.division
                                                        ?.zone
                                                        ?.name
                                                }{' '}
                                                Zone
                                            </p>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                appointment.designation
                                            }
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                appointment.appointment_type
                                            }
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {formatDate(
                                                appointment.start_date,
                                            )}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {formatDate(
                                                appointment.end_date,
                                            )}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    appointment.is_current
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-slate-100 text-slate-600',
                                                ].join(
                                                    ' ',
                                                )}
                                            >
                                                {appointment.is_current
                                                    ? 'Current'
                                                    : 'Previous'}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex gap-2">
                                                <Link
                                                    href={route(
                                                        'admin.principal-appointments.edit',
                                                        appointment.id,
                                                    )}
                                                    className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>

                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removeAppointment(
                                                            appointment,
                                                        )
                                                    }
                                                    className="rounded-lg p-2 text-red-600 hover:bg-red-50"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ),
                            )}
                        </tbody>
                    </table>
                </div>

                {profile.appointments.length === 0 && (
                    <div className="px-6 py-12 text-center text-sm text-slate-500">
                        No appointment records have been added.
                    </div>
                )}
            </section>
        </AdminLayout>
    );
}
