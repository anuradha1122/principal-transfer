import AdminLayout from '@/Layouts/AdminLayout';
import {
    Building2,
    Mail,
    MapPin,
    Pencil,
    Phone,
    Plus,
    School,
} from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function Show({ zone }) {
    return (
        <AdminLayout
            title={zone.name}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p className="text-sm font-semibold text-blue-600">
                            {zone.district} District
                        </p>

                        <h1 className="mt-1 text-2xl font-bold text-slate-900">
                            {zone.name} Education Zone
                        </h1>
                    </div>

                    <Link
                        href={route(
                            'admin.zones.edit',
                            zone.id,
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Pencil className="h-4 w-4" />
                        Edit Zone
                    </Link>
                </div>
            }
        >
            <div className="grid gap-6 lg:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        Zone Information
                    </h2>

                    <div className="mt-5 space-y-4 text-sm">
                        <div>
                            <span className="text-slate-500">
                                Code
                            </span>
                            <p className="font-semibold text-slate-800">
                                {zone.code}
                            </p>
                        </div>

                        <div className="flex gap-3">
                            <MapPin className="mt-0.5 h-4 w-4 text-slate-400" />
                            <span className="text-slate-700">
                                {zone.office_address ||
                                    'Address not recorded'}
                            </span>
                        </div>

                        <div className="flex gap-3">
                            <Phone className="h-4 w-4 text-slate-400" />
                            <span className="text-slate-700">
                                {zone.telephone ||
                                    'Telephone not recorded'}
                            </span>
                        </div>

                        <div className="flex gap-3">
                            <Mail className="h-4 w-4 text-slate-400" />
                            <span className="break-all text-slate-700">
                                {zone.email ||
                                    'Email not recorded'}
                            </span>
                        </div>
                    </div>
                </section>

                <section className="grid gap-5 sm:grid-cols-2 lg:col-span-2">
                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <Building2 className="h-7 w-7 text-blue-600" />
                        <p className="mt-5 text-3xl font-bold text-slate-900">
                            {zone.divisions_count}
                        </p>
                        <p className="text-sm text-slate-500">
                            Education divisions
                        </p>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <School className="h-7 w-7 text-blue-600" />
                        <p className="mt-5 text-3xl font-bold text-slate-900">
                            {zone.schools_count}
                        </p>
                        <p className="text-sm text-slate-500">
                            Schools
                        </p>
                    </div>
                </section>
            </div>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h2 className="font-bold text-slate-900">
                            Education Divisions
                        </h2>
                        <p className="text-sm text-slate-500">
                            Divisions assigned to this zone
                        </p>
                    </div>

                    <Link
                        href={`${route(
                            'admin.divisions.create',
                        )}?zone_id=${zone.id}`}
                        className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700"
                    >
                        <Plus className="h-4 w-4" />
                        Add Division
                    </Link>
                </div>

                <div className="divide-y divide-slate-100">
                    {zone.divisions.map((division) => (
                        <Link
                            key={division.id}
                            href={route(
                                'admin.divisions.show',
                                division.id,
                            )}
                            className="flex items-center justify-between px-6 py-4 hover:bg-slate-50"
                        >
                            <div>
                                <p className="font-semibold text-slate-800">
                                    {division.name}
                                </p>
                                <p className="text-xs text-slate-500">
                                    {division.code}
                                </p>
                            </div>

                            <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {division.schools_count} schools
                            </span>
                        </Link>
                    ))}

                    {zone.divisions.length === 0 && (
                        <div className="px-6 py-12 text-center text-sm text-slate-500">
                            No divisions have been added.
                        </div>
                    )}
                </div>
            </section>
        </AdminLayout>
    );
}
