import AdminLayout from '@/Layouts/AdminLayout';
import {
    KeyRound,
    Pencil,
    Plus,
    Trash2,
    Users,
} from 'lucide-react';
import {
    Link,
    router,
} from '@inertiajs/react';

export default function Index({ roles }) {
    const removeRole = (role) => {
        if (
            !window.confirm(
                `Delete the role "${role.name}"?`,
            )
        ) {
            return;
        }

        router.delete(
            route('admin.roles.destroy', role.id),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Roles"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Roles and Permissions
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Control access through role-based
                            permissions.
                        </p>
                    </div>

                    <div className="flex gap-3">
                        <Link
                            href={route(
                                'admin.permissions.index',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <KeyRound className="h-4 w-4" />
                            Permissions
                        </Link>

                        <Link
                            href={route(
                                'admin.roles.create',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            <Plus className="h-4 w-4" />
                            Create Role
                        </Link>
                    </div>
                </div>
            }
        >
            <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                {roles.map((role) => (
                    <div
                        key={role.id}
                        className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <div className="flex items-start justify-between gap-4">
                            <div>
                                <h2 className="text-lg font-bold text-slate-900">
                                    {role.name}
                                </h2>

                                {role.is_system && (
                                    <span className="mt-2 inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        System role
                                    </span>
                                )}
                            </div>

                            <div className="flex gap-2">
                                <Link
                                    href={route(
                                        'admin.roles.edit',
                                        role.id,
                                    )}
                                    className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                >
                                    <Pencil className="h-4 w-4" />
                                </Link>

                                {!role.is_system && (
                                    <button
                                        type="button"
                                        onClick={() =>
                                            removeRole(role)
                                        }
                                        className="rounded-lg p-2 text-red-600 hover:bg-red-50"
                                    >
                                        <Trash2 className="h-4 w-4" />
                                    </button>
                                )}
                            </div>
                        </div>

                        <div className="mt-6 grid grid-cols-2 gap-4">
                            <div className="rounded-xl bg-slate-50 p-4">
                                <Users className="h-5 w-5 text-slate-500" />

                                <p className="mt-3 text-2xl font-bold text-slate-900">
                                    {role.users_count}
                                </p>

                                <p className="text-xs text-slate-500">
                                    Assigned users
                                </p>
                            </div>

                            <div className="rounded-xl bg-slate-50 p-4">
                                <KeyRound className="h-5 w-5 text-slate-500" />

                                <p className="mt-3 text-2xl font-bold text-slate-900">
                                    {
                                        role.permissions_count
                                    }
                                </p>

                                <p className="text-xs text-slate-500">
                                    Permissions
                                </p>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </AdminLayout>
    );
}
