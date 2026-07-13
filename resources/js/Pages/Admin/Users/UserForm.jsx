import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function UserForm({
    data,
    setData,
    errors,
    processing,
    roles,
    editing = false,
    onSubmit,
}) {
    return (
        <form
            onSubmit={onSubmit}
            className="space-y-6"
        >
            <div className="grid gap-6 md:grid-cols-2">
                <div>
                    <InputLabel
                        htmlFor="name"
                        value="Full Name"
                    />

                    <TextInput
                        id="name"
                        value={data.name}
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
                    <InputLabel
                        htmlFor="email"
                        value="Email Address"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'email',
                                event.target.value,
                            )
                        }
                    />

                    <InputError
                        message={errors.email}
                        className="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="role"
                        value="System Role"
                    />

                    <select
                        id="role"
                        value={data.role}
                        onChange={(event) =>
                            setData(
                                'role',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">
                            Select a role
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

                    <InputError
                        message={errors.role}
                        className="mt-2"
                    />
                </div>

                {!editing && (
                    <div>
                        <InputLabel
                            htmlFor="password"
                            value="Temporary Password"
                        />

                        <TextInput
                            id="password"
                            type="password"
                            value={data.password}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'password',
                                    event.target.value,
                                )
                            }
                        />

                        <InputError
                            message={errors.password}
                            className="mt-2"
                        />
                    </div>
                )}

                {!editing && (
                    <div>
                        <InputLabel
                            htmlFor="password_confirmation"
                            value="Confirm Password"
                        />

                        <TextInput
                            id="password_confirmation"
                            type="password"
                            value={data.password_confirmation}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'password_confirmation',
                                    event.target.value,
                                )
                            }
                        />
                    </div>
                )}
            </div>

            <div className="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2">
                <label className="flex items-center gap-3">
                    <input
                        type="checkbox"
                        checked={data.is_active}
                        onChange={(event) =>
                            setData(
                                'is_active',
                                event.target.checked,
                            )
                        }
                        className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />

                    <span>
                        <span className="block text-sm font-semibold text-slate-800">
                            Active account
                        </span>

                        <span className="block text-xs text-slate-500">
                            Inactive users cannot sign in.
                        </span>
                    </span>
                </label>

                <label className="flex items-center gap-3">
                    <input
                        type="checkbox"
                        checked={data.email_verified}
                        onChange={(event) =>
                            setData(
                                'email_verified',
                                event.target.checked,
                            )
                        }
                        className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />

                    <span>
                        <span className="block text-sm font-semibold text-slate-800">
                            Email verified
                        </span>

                        <span className="block text-xs text-slate-500">
                            Mark trusted staff accounts as verified.
                        </span>
                    </span>
                </label>
            </div>

            <div className="flex justify-end gap-3">
                <Link
                    href={route('admin.users.index')}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update User'
                          : 'Create User'}
                </PrimaryButton>
            </div>
        </form>
    );
}
