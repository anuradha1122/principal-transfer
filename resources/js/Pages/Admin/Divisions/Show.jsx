import AdminLayout from '@/Layouts/AdminLayout';
import {
    Pencil,
    Plus,
    School,
} from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function Show({ division }) {
    return (
        <AdminLayout
            title={division.name}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p className="text-sm font-semibold text-blue-600">
                            {division.zone.name} Education Zone
                        </p>
                        <h1 className="mt-1 text-2xl font-bold text-slate-900">
                            {division.name} Education Division
                        </h1>
                    </div>

                    <Link
                        href={route(
                            'admin.divisions.edit',
                            division.id,
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Pencil className="h-4 w-4" />
                        Edit Division
                    </Link>
                </div>
            }
        >
            <div className="grid gap-6 md:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p className="text-sm text-slate-500">
                        Division code
                    </p>
                    <p className="mt-2 text-xl font-bold text-slate-900">
                        {division.code}
                    </p>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p className="text-sm text-slate-500">
                        District
                    </p>
                    <p className="mt-2 text-xl font-bold text-slate-900">
                        {division.zone.district}
                    </p>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <School className="h-6 w-6 text-blue-600" />
                    <p className="mt-3 text-3xl font-bold text-slate-900">
                        {division.schools_count}
                    </p>
                    <p className="text-sm text-slate-500">
                        Schools
                    </p>
                </section>
            </div>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <h2 className="font-bold text-slate-900">
                        Schools
                    </h2>

                    <Link
                        href={`${route(
                            'admin.schools.create',
                        )}?division_id=${division.id}`}
                        className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700"
                    >
                        <Plus className="h-4 w-4" />
                        Add School
                    </Link>
                </div>

                <div className="divide-y divide-slate-100">
                    {division.schools.map((school) => (
                        <Link
                            key={school.id}
                            href={route(
                                'admin.schools.show',
                                school.id,
                            )}
                            className="flex items-center justify-between px-6 py-4 hover:bg-slate-50"
                        >
                            <div>
                                <p className="font-semibold text-slate-800">
                                    {school.name}
                                </p>
                                <p className="text-xs text-slate-500">
                                    Census No: {school.census_number}
                                </p>
                            </div>

                            <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {school.school_type || 'Unclassified'}
                            </span>
                        </Link>
                    ))}

                    {division.schools.length === 0 && (
                        <div className="px-6 py-12 text-center text-sm text-slate-500">
                            No schools have been added.
                        </div>
                    )}
                </div>
            </section>
        </AdminLayout>
    );
}
