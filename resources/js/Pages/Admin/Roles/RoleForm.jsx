import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function RoleForm({
    data,
    setData,
    errors,
    processing,
    permissionGroups,
    editing = false,
    isSystem = false,
    onSubmit,
}) {
    const togglePermission = (permission) => {
        const permissions = data.permissions.includes(
            permission,
        )
            ? data.permissions.filter(
                  (item) => item !== permission,
              )
            : [...data.permissions, permission];

        setData('permissions', permissions);
    };

    const toggleGroup = (permissions) => {
        const allSelected = permissions.every(
            (permission) =>
                data.permissions.includes(permission),
        );

        if (allSelected) {
            setData(
                'permissions',
                data.permissions.filter(
                    (permission) =>
                        !permissions.includes(permission),
                ),
            );

            return;
        }

        setData(
            'permissions',
            Array.from(
                new Set([
                    ...data.permissions,
                    ...permissions,
                ]),
            ),
        );
    };

    return (
        <form
            onSubmit={onSubmit}
            className="space-y-6"
        >
            <div>
                <InputLabel
                    htmlFor="name"
                    value="Role Name"
                />

                <TextInput
                    id="name"
                    value={data.name}
                    disabled={
                        editing &&
                        isSystem &&
                        data.name === 'Super Admin'
                    }
                    className="mt-1 block w-full"
                    onChange={(event) =>
                        setData(
                            'name',
                            event.target.value,
                        )
                    }
                />

                <InputError
                    message={errors.name}
                    className="mt-2"
                />
            </div>

            <div>
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="font-bold text-slate-900">
                            Permissions
                        </h2>

                        <p className="text-sm text-slate-500">
                            Select the actions available to this
                            role.
                        </p>
                    </div>

                    <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {data.permissions.length} selected
                    </span>
                </div>

                <div className="mt-5 space-y-5">
                    {Object.entries(permissionGroups).map(
                        ([group, permissions]) => (
                            <section
                                key={group}
                                className="rounded-xl border border-slate-200"
                            >
                                <div className="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                                    <h3 className="font-semibold text-slate-800">
                                        {group}
                                    </h3>

                                    <button
                                        type="button"
                                        onClick={() =>
                                            toggleGroup(
                                                permissions,
                                            )
                                        }
                                        className="text-xs font-semibold text-blue-600"
                                    >
                                        Toggle group
                                    </button>
                                </div>

                                <div className="grid gap-3 p-4 md:grid-cols-2 xl:grid-cols-3">
                                    {permissions.map(
                                        (permission) => (
                                            <label
                                                key={
                                                    permission
                                                }
                                                className="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    checked={data.permissions.includes(
                                                        permission,
                                                    )}
                                                    disabled={
                                                        data.name ===
                                                        'Super Admin'
                                                    }
                                                    onChange={() =>
                                                        togglePermission(
                                                            permission,
                                                        )
                                                    }
                                                    className="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                />

                                                <span className="text-sm text-slate-700">
                                                    {
                                                        permission
                                                    }
                                                </span>
                                            </label>
                                        ),
                                    )}
                                </div>
                            </section>
                        ),
                    )}
                </div>

                <InputError
                    message={errors.permissions}
                    className="mt-2"
                />
            </div>

            <div className="flex justify-end gap-3">
                <Link
                    href={route('admin.roles.index')}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Role'
                          : 'Create Role'}
                </PrimaryButton>
            </div>
        </form>
    );
}
