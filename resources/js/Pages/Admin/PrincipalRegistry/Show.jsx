import AdminLayout from '@/Layouts/AdminLayout';
import {
    Building2,
    Mail,
    Pencil,
    ShieldCheck,
    UserRound,
} from 'lucide-react';
import { Link } from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

export default function Show({ registry }) {
    return (
        <AdminLayout
            title={registry.full_name || registry.nic}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            {registry.full_name ||
                                registry.name_with_initials ||
                                'Principal Registry Record'}
                        </h1>

                        <p className="mt-1 font-mono text-sm text-slate-500">
                            {registry.nic}
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.principal-registry.edit',
                            registry.id,
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Pencil className="h-4 w-4" />
                        Edit Record
                    </Link>
                </div>
            }
        >
            <div className="grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <UserRound className="h-8 w-8 text-blue-600" />

                    <h2 className="mt-5 font-bold text-slate-900">
                        Registry Information
                    </h2>

                    <dl className="mt-5 space-y-4 text-sm">
                        <div>
                            <dt className="text-slate-500">
                                NIC
                            </dt>
                            <dd className="font-mono font-semibold text-slate-800">
                                {registry.nic}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                Designation
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {registry.designation ||
                                    'Not assigned'}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                Employee number
                            </dt>
                            <dd className="font-semibold text-slate-800">
                                {registry.employee_number ||
                                    'Not recorded'}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-slate-500">
                                Registration status
                            </dt>
                            <dd className="font-semibold capitalize text-slate-800">
                                {
                                    registry.registration_status
                                }
                            </dd>
                        </div>
                    </dl>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <Building2 className="h-8 w-8 text-blue-600" />

                    <h2 className="mt-5 font-bold text-slate-900">
                        Current School
                    </h2>

                    {registry.school ? (
                        <div className="mt-5">
                            <p className="font-semibold text-slate-800">
                                {registry.school.name}
                            </p>
                            <p className="mt-1 text-sm text-slate-500">
                                {
                                    registry.school
                                        .division?.name
                                }{' '}
                                Division
                            </p>
                            <p className="text-sm text-slate-500">
                                {
                                    registry.school
                                        .division?.zone
                                        ?.name
                                }{' '}
                                Zone
                            </p>
                        </div>
                    ) : (
                        <p className="mt-5 text-sm text-slate-500">
                            No school has been assigned.
                        </p>
                    )}
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <ShieldCheck className="h-8 w-8 text-blue-600" />

                    <h2 className="mt-5 font-bold text-slate-900">
                        Registered Account
                    </h2>

                    {registry.registered_user ? (
                        <div className="mt-5 space-y-3">
                            <p className="font-semibold text-slate-800">
                                {
                                    registry.registered_user
                                        .name
                                }
                            </p>

                            <div className="flex items-center gap-2 text-sm text-slate-600">
                                <Mail className="h-4 w-4" />
                                {
                                    registry.registered_user
                                        .email
                                }
                            </div>

                            <p className="text-sm text-slate-500">
                                Registered:{' '}
                                {formatDate(
                                    registry.registered_at,
                                )}
                            </p>
                        </div>
                    ) : (
                        <p className="mt-5 text-sm text-slate-500">
                            No account has been registered.
                        </p>
                    )}
                </section>
            </div>

            {registry.notes && (
                <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        Administrative Notes
                    </h2>

                    <p className="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">
                        {registry.notes}
                    </p>
                </section>
            )}
        </AdminLayout>
    );
}
