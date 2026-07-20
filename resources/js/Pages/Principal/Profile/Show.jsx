import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    BadgeCheck,
    Building2,
    CalendarDays,
    Edit3,
    Mail,
    MapPin,
    Pencil,
    Phone,
    Plus,
    Trash2,
    UserRound,
} from 'lucide-react';

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

function DetailItem({ label, value }) {
    return (
        <div>
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </p>

            <p className="mt-1 text-sm font-semibold text-slate-800">
                {value || 'Not recorded'}
            </p>
        </div>
    );
}

export default function Show({ profile }) {
    const appointments =
        profile.appointments ?? [];

    const currentAppointment =
        appointments.find(
            (appointment) =>
                appointment.is_current,
        ) ?? null;

    const address = [
        profile.address_line_1,
        profile.address_line_2,
        profile.city,
        profile.postal_code,
    ]
        .filter(Boolean)
        .join(', ');

    const deleteAppointment = (
        appointment,
    ) => {
        if (appointment.is_current) {
            return;
        }

        const confirmed = window.confirm(
            'Are you sure you want to delete this appointment record?',
        );

        if (!confirmed) {
            return;
        }

        router.delete(
            route(
                'principal.appointments.destroy',
                appointment.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="My Profile"
            header={
                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            My Principal Profile
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            View and manage your personal,
                            service and appointment
                            information.
                        </p>
                    </div>

                    <div className="flex flex-col gap-3 sm:flex-row">
                        <Link
                            href={route(
                                'principal.appointments.create',
                            )}
                            className="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:border-blue-300 hover:bg-blue-100"
                        >
                            <Plus className="h-4 w-4" />
                            Add Appointment
                        </Link>

                        <Link
                            href={route(
                                'principal.profile.edit',
                            )}
                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            <Edit3 className="h-4 w-4" />
                            Edit My Profile
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title="My Principal Profile" />

            {!profile.profile_completed && (
                <div className="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <div className="flex items-start gap-3">
                        <BadgeCheck className="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />

                        <div>
                            <p className="font-semibold text-amber-900">
                                Your profile is incomplete
                            </p>

                            <p className="mt-1 text-sm leading-6 text-amber-700">
                                Complete your personal,
                                contact, service and
                                appointment information.
                                Your NIC number cannot be
                                changed.
                            </p>

                            <Link
                                href={route(
                                    'principal.profile.edit',
                                )}
                                className="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-amber-800 hover:text-amber-950"
                            >
                                <Edit3 className="h-4 w-4" />
                                Complete profile
                            </Link>
                        </div>
                    </div>
                </div>
            )}

            <div className="grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                    <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <UserRound className="h-6 w-6" />
                        </div>

                        <div>
                            <h2 className="text-lg font-bold text-slate-900">
                                Personal Information
                            </h2>

                            <p className="text-sm text-slate-500">
                                Your identity and contact
                                details
                            </p>
                        </div>
                    </div>

                    <div className="mt-6 grid gap-6 sm:grid-cols-2">
                        <DetailItem
                            label="Full Name"
                            value={profile.full_name}
                        />

                        <DetailItem
                            label="Name With Initials"
                            value={
                                profile.name_with_initials
                            }
                        />

                        <DetailItem
                            label="NIC Number"
                            value={profile.nic}
                        />

                        <DetailItem
                            label="Employee Number"
                            value={
                                profile.employee_number
                            }
                        />

                        <DetailItem
                            label="Gender"
                            value={profile.gender}
                        />

                        <DetailItem
                            label="Date of Birth"
                            value={formatDate(
                                profile.date_of_birth,
                            )}
                        />
                    </div>

                    <div className="mt-7 border-t border-slate-200 pt-6">
                        <h3 className="font-bold text-slate-900">
                            Contact Information
                        </h3>

                        <div className="mt-5 grid gap-5 sm:grid-cols-2">
                            <div className="flex items-start gap-3">
                                <Phone className="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />

                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Mobile Number
                                    </p>

                                    <p className="mt-1 text-sm font-semibold text-slate-800">
                                        {profile.mobile_number ||
                                            'Not recorded'}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <Phone className="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />

                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Alternate Number
                                    </p>

                                    <p className="mt-1 text-sm font-semibold text-slate-800">
                                        {profile.alternate_number ||
                                            'Not recorded'}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <Mail className="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />

                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Personal Email
                                    </p>

                                    <p className="mt-1 break-all text-sm font-semibold text-slate-800">
                                        {profile.personal_email ||
                                            'Not recorded'}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <MapPin className="mt-0.5 h-5 w-5 shrink-0 text-slate-400" />

                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Residential Address
                                    </p>

                                    <p className="mt-1 text-sm font-semibold leading-6 text-slate-800">
                                        {address ||
                                            'Not recorded'}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <CalendarDays className="h-6 w-6" />
                    </div>

                    <h2 className="mt-5 text-lg font-bold text-slate-900">
                        Service Information
                    </h2>

                    <div className="mt-6 space-y-5">
                        <DetailItem
                            label="Service Category"
                            value={
                                profile.service_category
                            }
                        />

                        <DetailItem
                            label="Service Grade"
                            value={profile.service_grade}
                        />

                        <DetailItem
                            label="First Appointment Date"
                            value={formatDate(
                                profile.first_appointment_date,
                            )}
                        />

                        <DetailItem
                            label="Principal Service Entry Date"
                            value={formatDate(
                                profile.principal_service_entry_date,
                            )}
                        />

                        <DetailItem
                            label="Retirement Date"
                            value={formatDate(
                                profile.retirement_date,
                            )}
                        />

                        <DetailItem
                            label="Employment Status"
                            value={
                                profile.employment_status
                            }
                        />
                    </div>
                </section>
            </div>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <Building2 className="h-6 w-6" />
                        </div>

                        <div>
                            <h2 className="text-lg font-bold text-slate-900">
                                Current Appointment
                            </h2>

                            <p className="text-sm text-slate-500">
                                Your current official school
                                appointment
                            </p>
                        </div>
                    </div>

                    {currentAppointment && (
                        <Link
                            href={route(
                                'principal.appointments.edit',
                                currentAppointment.id,
                            )}
                            className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                        >
                            <Pencil className="h-4 w-4" />
                            Edit Current Appointment
                        </Link>
                    )}
                </div>

                {currentAppointment ? (
                    <div className="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                        <DetailItem
                            label="Designation"
                            value={
                                currentAppointment.designation
                            }
                        />

                        <DetailItem
                            label="Appointment Type"
                            value={
                                currentAppointment.appointment_type
                            }
                        />

                        <DetailItem
                            label="School"
                            value={
                                currentAppointment.school?.name
                            }
                        />

                        <DetailItem
                            label="Start Date"
                            value={formatDate(
                                currentAppointment.start_date,
                            )}
                        />

                        <DetailItem
                            label="Division"
                            value={
                                currentAppointment.school
                                    ?.division?.name
                            }
                        />

                        <DetailItem
                            label="Zone"
                            value={
                                currentAppointment.school
                                    ?.division?.zone?.name
                            }
                        />

                        <DetailItem
                            label="Appointment Number"
                            value={
                                currentAppointment.appointment_number
                            }
                        />

                        <DetailItem
                            label="Appointment Date"
                            value={formatDate(
                                currentAppointment.appointment_date,
                            )}
                        />
                    </div>
                ) : (
                    <div className="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center">
                        <Building2 className="mx-auto h-10 w-10 text-slate-300" />

                        <p className="mt-3 text-sm font-semibold text-slate-700">
                            No current appointment has been
                            recorded.
                        </p>

                        <p className="mt-1 text-sm text-slate-500">
                            Add your current school
                            appointment before creating a
                            transfer application.
                        </p>

                        <Link
                            href={route(
                                'principal.appointments.create',
                            )}
                            className="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            <Plus className="h-4 w-4" />
                            Add Current Appointment
                        </Link>
                    </div>
                )}
            </section>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 className="text-lg font-bold text-slate-900">
                            Appointment History
                        </h2>

                        <p className="mt-1 text-sm text-slate-500">
                            Previous and current school
                            appointments
                        </p>
                    </div>

                    <Link
                        href={route(
                            'principal.appointments.create',
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        <Plus className="h-4 w-4" />
                        Add Appointment
                    </Link>
                </div>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                {[
                                    'School',
                                    'Designation',
                                    'Type',
                                    'Start Date',
                                    'End Date',
                                    'Status',
                                    'Actions',
                                ].map((heading) => (
                                    <th
                                        key={heading}
                                        className="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                    >
                                        {heading}
                                    </th>
                                ))}
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-slate-100">
                            {appointments.map(
                                (appointment) => (
                                    <tr
                                        key={appointment.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-800">
                                                {appointment
                                                    .school
                                                    ?.name ||
                                                    'School not available'}
                                            </p>

                                            <p className="mt-1 text-xs text-slate-500">
                                                {appointment
                                                    .school
                                                    ?.division
                                                    ?.name ||
                                                    'Division not recorded'}{' '}
                                                ·{' '}
                                                {appointment
                                                    .school
                                                    ?.division
                                                    ?.zone
                                                    ?.name ||
                                                    'Zone not recorded'}
                                            </p>
                                        </td>

                                        <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                            {appointment.designation ||
                                                'Not recorded'}
                                        </td>

                                        <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                            {appointment.appointment_type ||
                                                'Not recorded'}
                                        </td>

                                        <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                            {formatDate(
                                                appointment.start_date,
                                            )}
                                        </td>

                                        <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                            {appointment.end_date
                                                ? formatDate(
                                                      appointment.end_date,
                                                  )
                                                : 'Ongoing'}
                                        </td>

                                        <td className="whitespace-nowrap px-5 py-4">
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

                                        <td className="whitespace-nowrap px-5 py-4">
                                            <div className="flex items-center gap-2">
                                                <Link
                                                    href={route(
                                                        'principal.appointments.edit',
                                                        appointment.id,
                                                    )}
                                                    title="Edit appointment"
                                                    className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-blue-600 transition hover:border-blue-200 hover:bg-blue-50"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>

                                                {!appointment.is_current && (
                                                    <button
                                                        type="button"
                                                        title="Delete appointment"
                                                        onClick={() =>
                                                            deleteAppointment(
                                                                appointment,
                                                            )
                                                        }
                                                        className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-red-600 transition hover:border-red-200 hover:bg-red-50"
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

                {appointments.length === 0 && (
                    <div className="px-6 py-12 text-center">
                        <Building2 className="mx-auto h-10 w-10 text-slate-300" />

                        <p className="mt-3 text-sm font-semibold text-slate-700">
                            No appointment history has been
                            recorded.
                        </p>

                        <p className="mt-1 text-sm text-slate-500">
                            Add the current or previous
                            appointment records to complete
                            your service history.
                        </p>

                        <Link
                            href={route(
                                'principal.appointments.create',
                            )}
                            className="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                        >
                            <Plus className="h-4 w-4" />
                            Add First Appointment
                        </Link>
                    </div>
                )}
            </section>

            {profile.qualifications_summary && (
                <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Qualifications Summary
                    </h2>

                    <p className="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {
                            profile.qualifications_summary
                        }
                    </p>
                </section>
            )}

            {profile.notes && (
                <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Notes
                    </h2>

                    <p className="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {profile.notes}
                    </p>
                </section>
            )}
        </AdminLayout>
    );
}
