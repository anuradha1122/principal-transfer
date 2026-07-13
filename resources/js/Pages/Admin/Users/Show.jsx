import AdminLayout from '@/Layouts/AdminLayout';
import { Link } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock3,
    Mail,
    Pencil,
    ShieldCheck,
    UserRound,
    XCircle,
} from 'lucide-react';

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

export default function Show({ account }) {
    return (
        <AdminLayout
            title={account.name}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            {account.name}
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            User account and effective permissions
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.users.edit',
                            account.id,
                        )}
                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        <Pencil className="h-4 w-4" />
                        Edit User
                    </Link>
                </div>
            }
        >
            <div className="grid gap-6 xl:grid-cols-3">
                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-1">
                    <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <UserRound className="h-8 w-8" />
                    </div>

                    <h2 className="mt-5 text-xl font-bold text-slate-900">
                        {account.name}
                    </h2>

                    <div className="mt-3 flex items-center gap-2 text-sm text-slate-600">
                        <Mail className="h-4 w-4" />
                        {account.email}
                    </div>

                    <div className="mt-5 space-y-3 border-t border-slate-200 pt-5">
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-slate-500">
                                Account
                            </span>

                            <span
                                className={[
                                    'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold',
                                    account.is_active
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-red-50 text-red-700',
                                ].join(' ')}
                            >
                                {account.is_active ? (
                                    <CheckCircle2 className="h-3.5 w-3.5" />
                                ) : (
                                    <XCircle className="h-3.5 w-3.5" />
                                )}

                                {account.is_active
                                    ? 'Active'
                                    : 'Inactive'}
                            </span>
                        </div>

                        <div className="flex items-center justify-between">
                            <span className="text-sm text-slate-500">
                                Email
                            </span>

                            <span className="text-sm font-medium text-slate-700">
                                {account.email_verified_at
                                    ? 'Verified'
                                    : 'Not verified'}
                            </span>
                        </div>

                        <div>
                            <span className="text-sm text-slate-500">
                                Last login
                            </span>

                            <p className="mt-1 text-sm font-medium text-slate-700">
                                {formatDate(
                                    account.last_login_at,
                                )}
                            </p>
                        </div>

                        <div>
                            <span className="text-sm text-slate-500">
                                Created
                            </span>

                            <p className="mt-1 text-sm font-medium text-slate-700">
                                {formatDate(
                                    account.created_at,
                                )}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="space-y-6 xl:col-span-2">
                    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-3">
                            <ShieldCheck className="h-5 w-5 text-blue-600" />

                            <h2 className="text-lg font-bold text-slate-900">
                                Assigned Role
                            </h2>
                        </div>

                        <div className="mt-4 flex flex-wrap gap-2">
                            {account.roles.map((role) => (
                                <span
                                    key={role}
                                    className="rounded-full bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700"
                                >
                                    {role}
                                </span>
                            ))}
                        </div>
                    </section>

                    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-3">
                            <Clock3 className="h-5 w-5 text-blue-600" />

                            <h2 className="text-lg font-bold text-slate-900">
                                Effective Permissions
                            </h2>
                        </div>

                        <div className="mt-5 grid gap-2 sm:grid-cols-2">
                            {account.permissions.map(
                                (permission) => (
                                    <div
                                        key={permission}
                                        className="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"
                                    >
                                        {permission}
                                    </div>
                                ),
                            )}
                        </div>
                    </section>
                </div>
            </div>
        </AdminLayout>
    );
}
