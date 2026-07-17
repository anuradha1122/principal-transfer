import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function ProfileForm({
    data,
    setData,
    errors,
    processing,
    options,
    availableAccounts = [],
    registries = [],
    editing = false,
    onSubmit,
}) {
    return (
        <form
            onSubmit={onSubmit}
            className="space-y-8"
        >
            {!editing && (
                <section>
                    <h2 className="text-lg font-bold text-slate-900">
                        Account Link
                    </h2>

                    <div className="mt-5 grid gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel
                                htmlFor="user_id"
                                value="Principal Account"
                            />

                            <select
                                id="user_id"
                                value={data.user_id}
                                onChange={(event) =>
                                    setData(
                                        'user_id',
                                        event.target.value,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300"
                            >
                                <option value="">
                                    Select account
                                </option>

                                {availableAccounts.map(
                                    (account) => (
                                        <option
                                            key={account.id}
                                            value={account.id}
                                        >
                                            {account.name} -{' '}
                                            {account.email}
                                        </option>
                                    ),
                                )}
                            </select>

                            <InputError
                                message={errors.user_id}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="principal_registry_id"
                                value="Registry Record"
                            />

                            <select
                                id="principal_registry_id"
                                value={
                                    data.principal_registry_id
                                }
                                onChange={(event) =>
                                    setData(
                                        'principal_registry_id',
                                        event.target.value,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300"
                            >
                                <option value="">
                                    Select registry record
                                </option>

                                {registries.map(
                                    (registry) => (
                                        <option
                                            key={registry.id}
                                            value={registry.id}
                                        >
                                            {registry.nic} -{' '}
                                            {registry.full_name ||
                                                registry
                                                    .registered_user
                                                    ?.name}
                                        </option>
                                    ),
                                )}
                            </select>
                        </div>
                    </div>
                </section>
            )}

            <section className={editing ? '' : 'border-t border-slate-200 pt-8'}>
                <h2 className="text-lg font-bold text-slate-900">
                    Personal Information
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    {[
                        ['nic', 'NIC Number'],
                        [
                            'employee_number',
                            'Employee Number',
                        ],
                        ['full_name', 'Full Name'],
                        [
                            'name_with_initials',
                            'Name With Initials',
                        ],
                        [
                            'mobile_number',
                            'Mobile Number',
                        ],
                        [
                            'alternate_number',
                            'Alternate Number',
                        ],
                        [
                            'personal_email',
                            'Personal Email',
                        ],
                        ['city', 'City'],
                        ['postal_code', 'Postal Code'],
                    ].map(([field, label]) => (
                        <div key={field}>
                            <InputLabel
                                htmlFor={field}
                                value={label}
                            />

                            <TextInput
                                id={field}
                                type={
                                    field ===
                                    'personal_email'
                                        ? 'email'
                                        : 'text'
                                }
                                value={data[field]}
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        field,
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={errors[field]}
                                className="mt-2"
                            />
                        </div>
                    ))}

                    <div>
                        <InputLabel
                            htmlFor="gender"
                            value="Gender"
                        />

                        <select
                            id="gender"
                            value={data.gender}
                            onChange={(event) =>
                                setData(
                                    'gender',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            <option value="">
                                Select gender
                            </option>

                            {options.genders.map(
                                (gender) => (
                                    <option
                                        key={gender}
                                        value={gender}
                                    >
                                        {gender}
                                    </option>
                                ),
                            )}
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="date_of_birth"
                            value="Date of Birth"
                        />

                        <TextInput
                            id="date_of_birth"
                            type="date"
                            value={data.date_of_birth}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'date_of_birth',
                                    event.target.value,
                                )
                            }
                        />
                    </div>

                    <div className="md:col-span-2">
                        <InputLabel
                            htmlFor="address_line_1"
                            value="Address Line 1"
                        />

                        <TextInput
                            id="address_line_1"
                            value={data.address_line_1}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'address_line_1',
                                    event.target.value,
                                )
                            }
                        />
                    </div>

                    <div className="md:col-span-2">
                        <InputLabel
                            htmlFor="address_line_2"
                            value="Address Line 2"
                        />

                        <TextInput
                            id="address_line_2"
                            value={data.address_line_2}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'address_line_2',
                                    event.target.value,
                                )
                            }
                        />
                    </div>
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <h2 className="text-lg font-bold text-slate-900">
                    Service Information
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="service_category"
                            value="Service Category"
                        />

                        <select
                            id="service_category"
                            value={data.service_category}
                            onChange={(event) =>
                                setData(
                                    'service_category',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            {options.serviceCategories.map(
                                (category) => (
                                    <option
                                        key={category}
                                        value={category}
                                    >
                                        {category}
                                    </option>
                                ),
                            )}
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="service_grade"
                            value="Service Grade"
                        />

                        <TextInput
                            id="service_grade"
                            value={data.service_grade}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'service_grade',
                                    event.target.value,
                                )
                            }
                        />
                    </div>

                    {[
                        [
                            'first_appointment_date',
                            'First Appointment Date',
                        ],
                        [
                            'principal_service_entry_date',
                            'Principal Service Entry Date',
                        ],
                        [
                            'retirement_date',
                            'Retirement Date',
                        ],
                    ].map(([field, label]) => (
                        <div key={field}>
                            <InputLabel
                                htmlFor={field}
                                value={label}
                            />

                            <TextInput
                                id={field}
                                type="date"
                                value={data[field]}
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        field,
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={errors[field]}
                                className="mt-2"
                            />
                        </div>
                    ))}

                    <div>
                        <InputLabel
                            htmlFor="employment_status"
                            value="Employment Status"
                        />

                        <select
                            id="employment_status"
                            value={data.employment_status}
                            onChange={(event) =>
                                setData(
                                    'employment_status',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            {options.statuses.map(
                                (status) => (
                                    <option
                                        key={status}
                                        value={status}
                                    >
                                        {status}
                                    </option>
                                ),
                            )}
                        </select>
                    </div>
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <div>
                    <InputLabel
                        htmlFor="qualifications_summary"
                        value="Qualifications Summary"
                    />

                    <textarea
                        id="qualifications_summary"
                        rows="4"
                        value={data.qualifications_summary}
                        onChange={(event) =>
                            setData(
                                'qualifications_summary',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300"
                    />
                </div>

                <div className="mt-5">
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
                        className="mt-1 block w-full rounded-md border-gray-300"
                    />
                </div>

                <label className="mt-5 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <input
                        type="checkbox"
                        checked={data.profile_completed}
                        onChange={(event) =>
                            setData(
                                'profile_completed',
                                event.target.checked,
                            )
                        }
                        className="rounded border-gray-300 text-blue-600"
                    />

                    <span className="text-sm font-semibold text-slate-800">
                        Profile information has been verified and completed
                    </span>
                </label>
            </section>

            <div className="flex justify-end gap-3">
                <Link
                    href={route(
                        'admin.principal-profiles.index',
                    )}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Profile'
                          : 'Create Profile'}
                </PrimaryButton>
            </div>
        </form>
    );
}
