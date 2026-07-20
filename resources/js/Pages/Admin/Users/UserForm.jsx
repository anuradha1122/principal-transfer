import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';
import {
    Building2,
    MapPinned,
} from 'lucide-react';

export default function UserForm({
    data,
    setData,
    errors,
    processing,
    roles = [],
    zones = [],
    editing = false,
    onSubmit,
}) {
    const isZonalDirector =
        data.role === 'Zonal Director';

    const isProvincialDirector =
        data.role === 'Provincial Director';

    const isTransferBoardMember =
        data.role === 'Transfer Board Member';

    const handleRoleChange = (
        event
    ) => {
        const selectedRole =
            event.target.value;

        setData((currentData) => ({
            ...currentData,
            role: selectedRole,

            /*
             * Remove stale Zone access whenever the user
             * is changed to a non-Zonal role.
             */
            assigned_zone_id:
                selectedRole ===
                'Zonal Director'
                    ? currentData
                        .assigned_zone_id
                    : '',
        }));
    };

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
                                event.target.value
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
                                event.target.value
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
                        onChange={
                            handleRoleChange
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
                                    event.target.value
                                )
                            }
                        />

                        <InputError
                            message={
                                errors.password
                            }
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
                            value={
                                data.password_confirmation
                            }
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'password_confirmation',
                                    event.target.value
                                )
                            }
                        />

                        <InputError
                            message={
                                errors
                                    .password_confirmation
                            }
                            className="mt-2"
                        />
                    </div>
                )}
            </div>

            {isZonalDirector && (
                <section className="rounded-2xl border border-blue-200 bg-blue-50/60 p-5">
                    <div className="flex items-start gap-3">
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm">
                            <MapPinned className="h-5 w-5" />
                        </div>

                        <div>
                            <h2 className="font-bold text-slate-900">
                                Zonal Office Assignment
                            </h2>

                            <p className="mt-1 text-sm leading-6 text-slate-600">
                                Select the Zone whose transfer
                                applications this officer may
                                review.
                            </p>
                        </div>
                    </div>

                    <div className="mt-5">
                        <InputLabel
                            htmlFor="assigned_zone_id"
                            value="Assigned Zone *"
                        />

                        <select
                            id="assigned_zone_id"
                            value={
                                data.assigned_zone_id
                                ?? ''
                            }
                            onChange={(event) =>
                                setData(
                                    'assigned_zone_id',
                                    event.target.value
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select a Zone
                            </option>

                            {zones.map((zone) => (
                                <option
                                    key={zone.id}
                                    value={zone.id}
                                >
                                    {zone.name}
                                    {zone.code
                                        ? ` (${zone.code})`
                                        : ''}
                                    {zone.district
                                        ? ` - ${zone.district}`
                                        : ''}
                                </option>
                            ))}
                        </select>

                        <InputError
                            message={
                                errors
                                    .assigned_zone_id
                            }
                            className="mt-2"
                        />

                        <div className="mt-3 flex items-center gap-2 text-xs text-slate-500">
                            <Building2 className="h-4 w-4" />

                            This officer will only access
                            applications from the selected
                            Zone.
                        </div>
                    </div>
                </section>
            )}

            {isProvincialDirector && (
                <section className="rounded-2xl border border-violet-200 bg-violet-50 p-5">
                    <h2 className="font-bold text-slate-900">
                        Provincial Office Access
                    </h2>

                    <p className="mt-2 text-sm leading-6 text-slate-600">
                        This officer will have Province-wide access to transfer applications from all Zones.
                    </p>
                </section>
            )}

            {isTransferBoardMember && (
                <section className="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
                    <h2 className="font-bold text-slate-900">
                        Transfer Board Access
                    </h2>

                    <p className="mt-2 text-sm leading-6 text-slate-600">
                        This officer will have Province-wide access to Provincially approved transfer applications and may record final Board decisions.
                    </p>
                </section>
            )}

            <div className="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2">
                <label className="flex items-center gap-3">
                    <input
                        type="checkbox"
                        checked={
                            Boolean(
                                data.is_active
                            )
                        }
                        onChange={(event) =>
                            setData(
                                'is_active',
                                event.target.checked
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
                        checked={
                            Boolean(
                                data.email_verified
                            )
                        }
                        onChange={(event) =>
                            setData(
                                'email_verified',
                                event.target.checked
                            )
                        }
                        className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />

                    <span>
                        <span className="block text-sm font-semibold text-slate-800">
                            Email verified
                        </span>

                        <span className="block text-xs text-slate-500">
                            Mark trusted staff accounts as
                            verified.
                        </span>
                    </span>
                </label>
            </div>

            <div className="flex justify-end gap-3">
                <Link
                    href={route(
                        'admin.users.index'
                    )}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Cancel
                </Link>

                <PrimaryButton
                    disabled={processing}
                >
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
