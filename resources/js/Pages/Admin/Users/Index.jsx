import AdminLayout from '@/Layouts/AdminLayout';
import {
    Eye,
    Pencil,
    Plus,
    Search,
    Trash2,
    Users,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
} from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Never';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
    }).format(new Date(value));
}

export default function Index({
    users,
    roles,
    filters,
}) {
    const { data, setData } = useForm({
        search: filters.search ?? '',
        role: filters.role ?? '',
        status: filters.status ?? '',
    });

    const submitFilters = (event) => {
        event.preventDefault();

        router.get(
            route('admin.users.index'),
            data,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const clearFilters = () => {
        router.get(route('admin.users.index'));
    };

    const removeUser = (user) => {
        if (
            !window.confirm(
                `Delete the account for ${user.name}?`,
            )
        ) {
            return;
        }

        router.delete(
            route('admin.users.destroy', user.id),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Users"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            User Management
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Manage system accounts, roles and
                            access status.
                        </p>
                    </div>

                    <Link
                        href={route('admin.users.create')}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        <Plus className="h-4 w-4" />
                        Create User
                    </Link>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <form
                    onSubmit={submitFilters}
                    className="grid gap-4 border-b border-slate-200 p-5 md:grid-cols-4"
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
                            placeholder="Search name or email"
                            className="w-full rounded-xl border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <select
                        value={data.role}
                        onChange={(event) =>
                            setData(
                                'role',
                                event.target.value,
                            )
                        }
                        className="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">
                            All roles
                        </option>

                        {roles.map((role) => (
                            <option
                                key={role}
                                value={role}
                            >
                                {role}
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
                        className="rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
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
                            className="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Filter
                        </button>

                        <button
                            type="button"
                            onClick={clearFilters}
                            className="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700"
                        >
                            Clear
                        </button>
                    </div>
                </form>

                {users.data.length === 0 ? (
                    <div className="flex flex-col items-center px-6 py-16 text-center">
                        <Users className="h-10 w-10 text-slate-300" />

                        <h2 className="mt-4 font-bold text-slate-800">
                            No users found
                        </h2>

                        <p className="mt-1 text-sm text-slate-500">
                            Adjust the filters or create a new
                            account.
                        </p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-slate-50">
                                <tr>
                                    {[
                                        'User',
                                        'Role',
                                        'Status',
                                        'Last Login',
                                        'Created',
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
                                {users.data.map((user) => (
                                    <tr
                                        key={user.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4">
                                            <p className="font-semibold text-slate-900">
                                                {user.name}
                                            </p>
                                            <p className="text-sm text-slate-500">
                                                {user.email}
                                            </p>
                                        </td>

                                        <td className="px-5 py-4">
                                            <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                {user.roles[0] ??
                                                    'No role'}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    user.is_active
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-red-50 text-red-700',
                                                ].join(' ')}
                                            >
                                                {user.is_active
                                                    ? 'Active'
                                                    : 'Inactive'}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {formatDate(
                                                user.last_login_at,
                                            )}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {formatDate(
                                                user.created_at,
                                            )}
                                        </td>

                                        <td className="px-5 py-4">
                                            <div className="flex items-center gap-2">
                                                <Link
                                                    href={route(
                                                        'admin.users.show',
                                                        user.id,
                                                    )}
                                                    className="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800"
                                                >
                                                    <Eye className="h-4 w-4" />
                                                </Link>

                                                <Link
                                                    href={route(
                                                        'admin.users.edit',
                                                        user.id,
                                                    )}
                                                    className="rounded-lg p-2 text-blue-600 hover:bg-blue-50"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Link>

                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removeUser(
                                                            user,
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
                )}

                {users.links.length > 3 && (
                    <div className="flex flex-wrap gap-2 border-t border-slate-200 px-5 py-4">
                        {users.links.map((link) => (
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
