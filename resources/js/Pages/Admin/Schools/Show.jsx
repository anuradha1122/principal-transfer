import AdminLayout from '@/Layouts/AdminLayout';
import {
    Building2,
    Mail,
    MapPin,
    Pencil,
    Phone,
    School as SchoolIcon,
    Users,
} from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function Show({ school }) {
    const address = [
        school.address_line_1,
        school.address_line_2,
        school.city,
        school.postal_code,
    ]
        .filter(Boolean)
        .join(', ');

    return (
        <AdminLayout
            title={school.name}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p className="text-sm font-semibold text-blue-600">
                            {school.division.zone.name} Zone ·{' '}
                            {school.division.name} Division
                        </p>
                        <h1 className="mt-1 text-2xl font-bold text-slate-900">
                            {school.name}
                        </h1>
                        <p className="mt-1 text-sm text-slate-500">
                            Census No: {school.census_number}
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.schools.edit',
                            school.id,
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Pencil className="h-4 w-4" />
                        Edit School
                    </Link>
                </div>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 className="text-lg font-bold text-slate-900">
                        School Information
                    </h2>

                    <div className="mt-6 grid gap-6 sm:grid-cols-2">
                        <div>
                            <p className="text-xs uppercase tracking-wide text-slate-500">
                                School type
                            </p>
                            <p className="mt-1 font-semibold text-slate-800">
                                {school.school_type ||
                                    'Not recorded'}
                            </p>
                        </div>

                        <div>
                            <p className="text-xs uppercase tracking-wide text-slate-500">
                                Gender type
                            </p>
                            <p className="mt-1 font-semibold text-slate-800">
                                {school.gender_type}
                            </p>
                        </div>

                        <div>
                            <p className="text-xs uppercase tracking-wide text-slate-500">
                                School level
                            </p>
                            <p className="mt-1 font-semibold text-slate-800">
                                {school.school_level ||
                                    'Not recorded'}
                            </p>
                        </div>

                        <div>
                            <p className="text-xs uppercase tracking-wide text-slate-500">
                                Administration
                            </p>
                            <p className="mt-1 font-semibold text-slate-800">
                                {school.is_national_school
                                    ? 'National School'
                                    : 'Provincial School'}
                            </p>
                        </div>
                    </div>

                    <div className="mt-7 border-t border-slate-200 pt-6">
                        <h3 className="font-semibold text-slate-800">
                            Teaching Mediums
                        </h3>

                        <div className="mt-3 flex flex-wrap gap-2">
                            {(school.mediums ?? []).map(
                                (medium) => (
                                    <span
                                        key={medium}
                                        className="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700"
                                    >
                                        {medium}
                                    </span>
                                ),
                            )}

                            {(school.mediums ?? []).length ===
                                0 && (
                                <span className="text-sm text-slate-500">
                                    No mediums recorded
                                </span>
                            )}
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <SchoolIcon className="h-7 w-7 text-blue-600" />

                    <h2 className="mt-4 font-bold text-slate-900">
                        Status
                    </h2>

                    <span
                        className={[
                            'mt-3 inline-block rounded-full px-3 py-1 text-xs font-semibold',
                            school.is_active
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-red-50 text-red-700',
                        ].join(' ')}
                    >
                        {school.is_active
                            ? 'Active School'
                            : 'Inactive School'}
                    </span>
                </section>
            </div>

            <div className="mt-6 grid gap-6 md:grid-cols-2">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        Contact Details
                    </h2>

                    <div className="mt-5 space-y-4 text-sm">
                        <div className="flex gap-3">
                            <MapPin className="mt-0.5 h-4 w-4 text-slate-400" />
                            <span className="text-slate-700">
                                {address ||
                                    'Address not recorded'}
                            </span>
                        </div>

                        <div className="flex gap-3">
                            <Phone className="h-4 w-4 text-slate-400" />
                            <span className="text-slate-700">
                                {school.telephone ||
                                    'Telephone not recorded'}
                            </span>
                        </div>

                        <div className="flex gap-3">
                            <Mail className="h-4 w-4 text-slate-400" />
                            <span className="break-all text-slate-700">
                                {school.email ||
                                    'Email not recorded'}
                            </span>
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        School Population
                    </h2>

                    <div className="mt-5 grid grid-cols-2 gap-4">
                        <div className="rounded-xl bg-slate-50 p-4">
                            <Users className="h-5 w-5 text-blue-600" />
                            <p className="mt-3 text-2xl font-bold text-slate-900">
                                {school.student_count ??
                                    '—'}
                            </p>
                            <p className="text-xs text-slate-500">
                                Students
                            </p>
                        </div>

                        <div className="rounded-xl bg-slate-50 p-4">
                            <Building2 className="h-5 w-5 text-blue-600" />
                            <p className="mt-3 text-2xl font-bold text-slate-900">
                                {school.teacher_count ??
                                    '—'}
                            </p>
                            <p className="text-xs text-slate-500">
                                Teachers
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </AdminLayout>
    );
}
