import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function RegistryForm({
    data,
    setData,
    errors,
    processing,
    schools,
    designations,
    editing = false,
    registered = false,
    onSubmit,
}) {
    return (
        <form
            onSubmit={onSubmit}
            className="space-y-7"
        >
            <div className="grid gap-6 md:grid-cols-2">
                <div>
                    <InputLabel
                        htmlFor="nic"
                        value="NIC Number"
                    />

                    <TextInput
                        id="nic"
                        value={data.nic}
                        disabled={editing && registered}
                        className="mt-1 block w-full uppercase"
                        onChange={(event) =>
                            setData(
                                'nic',
                                event.target.value
                                    .toUpperCase(),
                            )
                        }
                    />

                    <InputError
                        message={errors.nic}
                        className="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="employee_number"
                        value="Employee Number"
                    />

                    <TextInput
                        id="employee_number"
                        value={data.employee_number}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'employee_number',
                                event.target.value,
                            )
                        }
                    />

                    <InputError
                        message={errors.employee_number}
                        className="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="full_name"
                        value="Full Name"
                    />

                    <TextInput
                        id="full_name"
                        value={data.full_name}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'full_name',
                                event.target.value,
                            )
                        }
                    />

                    <InputError
                        message={errors.full_name}
                        className="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="name_with_initials"
                        value="Name With Initials"
                    />

                    <TextInput
                        id="name_with_initials"
                        value={data.name_with_initials}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'name_with_initials',
                                event.target.value,
                            )
                        }
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="designation"
                        value="Designation"
                    />

                    <select
                        id="designation"
                        value={data.designation}
                        onChange={(event) =>
                            setData(
                                'designation',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">
                            Select designation
                        </option>

                        {designations.map(
                            (designation) => (
                                <option
                                    key={designation}
                                    value={designation}
                                >
                                    {designation}
                                </option>
                            ),
                        )}
                    </select>

                    <InputError
                        message={errors.designation}
                        className="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="school_id"
                        value="Current School"
                    />

                    <select
                        id="school_id"
                        value={data.school_id}
                        onChange={(event) =>
                            setData(
                                'school_id',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">
                            School not assigned
                        </option>

                        {schools.map((school) => (
                            <option
                                key={school.id}
                                value={school.id}
                            >
                                {school.name} (
                                {school.census_number}) -{' '}
                                {
                                    school.division?.zone
                                        ?.name
                                }
                            </option>
                        ))}
                    </select>

                    <InputError
                        message={errors.school_id}
                        className="mt-2"
                    />
                </div>
            </div>

            <div>
                <InputLabel
                    htmlFor="notes"
                    value="Administrative Notes"
                />

                <textarea
                    id="notes"
                    rows="4"
                    value={data.notes}
                    onChange={(event) =>
                        setData(
                            'notes',
                            event.target.value,
                        )
                    }
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />

                <InputError
                    message={errors.notes}
                    className="mt-2"
                />
            </div>

            <label className="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
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
                        Eligible for system access
                    </span>

                    <span className="block text-xs text-slate-500">
                        Inactive registry records cannot be used
                        for registration.
                    </span>
                </span>
            </label>

            <div className="flex justify-end gap-3">
                <Link
                    href={route(
                        'admin.principal-registry.index',
                    )}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Record'
                          : 'Create Record'}
                </PrimaryButton>
            </div>
        </form>
    );
}
