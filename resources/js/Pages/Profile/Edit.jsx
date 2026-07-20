import AdminLayout from '@/Layouts/AdminLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link, useForm } from '@inertiajs/react';

function dateValue(value) {
    return value
        ? String(value).substring(0, 10)
        : '';
}

export default function Edit({
    profile,
    options,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        employee_number:
            profile.employee_number ?? '',

        full_name:
            profile.full_name ?? '',

        name_with_initials:
            profile.name_with_initials ?? '',

        gender:
            profile.gender ?? '',

        date_of_birth:
            dateValue(
                profile.date_of_birth
            ),

        mobile_number:
            profile.mobile_number ?? '',

        alternate_number:
            profile.alternate_number ?? '',

        personal_email:
            profile.personal_email ?? '',

        address_line_1:
            profile.address_line_1 ?? '',

        address_line_2:
            profile.address_line_2 ?? '',

        city:
            profile.city ?? '',

        postal_code:
            profile.postal_code ?? '',

        service_category:
            profile.service_category ?? '',

        service_grade:
            profile.service_grade ?? '',

        first_appointment_date:
            dateValue(
                profile.first_appointment_date
            ),

        principal_service_entry_date:
            dateValue(
                profile.principal_service_entry_date
            ),

        retirement_date:
            dateValue(
                profile.retirement_date
            ),

        employment_status:
            profile.employment_status
            ?? 'Active',

        qualifications_summary:
            profile.qualifications_summary
            ?? '',

        notes:
            profile.notes ?? '',

        profile_completed:
            Boolean(
                profile.profile_completed
            ),
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'principal.profile.update'
            )
        );
    };

    return (
        <AdminLayout
            title="Edit My Profile"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit My Profile
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update your personal and service information.
                    </p>
                </div>
            }
        >
            <form
                onSubmit={submit}
                className="space-y-6"
            >
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Identity Information
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel
                                value="NIC Number"
                            />

                            <input
                                type="text"
                                value={
                                    profile.nic ?? ''
                                }
                                disabled
                                className="mt-1 block w-full rounded-md border-slate-200 bg-slate-100 text-slate-500"
                            />

                            <p className="mt-1 text-xs text-slate-500">
                                NIC cannot be changed.
                            </p>
                        </div>

                        <div>
                            <InputLabel
                                value="Employee Number"
                            />

                            <TextInput
                                value={
                                    data.employee_number
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'employee_number',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.employee_number
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                value="Full Name"
                            />

                            <TextInput
                                value={
                                    data.full_name
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'full_name',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.full_name
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                value="Name with Initials"
                            />

                            <TextInput
                                value={
                                    data.name_with_initials
                                }
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
                                value="Gender"
                            />

                            <select
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
                                value="Date of Birth"
                            />

                            <TextInput
                                type="date"
                                value={
                                    data.date_of_birth
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'date_of_birth',
                                        event.target.value,
                                    )
                                }
                            />
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Contact Information
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        {[
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
                            [
                                'address_line_1',
                                'Address Line 1',
                            ],
                            [
                                'address_line_2',
                                'Address Line 2',
                            ],
                            [
                                'city',
                                'City',
                            ],
                            [
                                'postal_code',
                                'Postal Code',
                            ],
                        ].map(
                            ([field, label]) => (
                                <div key={field}>
                                    <InputLabel
                                        value={label}
                                    />

                                    <TextInput
                                        type={
                                            field
                                            === 'personal_email'
                                                ? 'email'
                                                : 'text'
                                        }
                                        value={
                                            data[field]
                                        }
                                        className="mt-1 block w-full"
                                        onChange={(event) =>
                                            setData(
                                                field,
                                                event
                                                    .target
                                                    .value,
                                            )
                                        }
                                    />

                                    <InputError
                                        message={
                                            errors[field]
                                        }
                                        className="mt-2"
                                    />
                                </div>
                            ),
                        )}
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Service Information
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel
                                value="Service Category"
                            />

                            <TextInput
                                value={
                                    data.service_category
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'service_category',
                                        event.target.value,
                                    )
                                }
                            />
                        </div>

                        <div>
                            <InputLabel
                                value="Service Grade"
                            />

                            <select
                                value={
                                    data.service_grade
                                }
                                onChange={(event) =>
                                    setData(
                                        'service_grade',
                                        event.target.value,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300"
                            >
                                <option value="">
                                    Select grade
                                </option>

                                {options.serviceGrades.map(
                                    (grade) => (
                                        <option
                                            key={grade}
                                            value={grade}
                                        >
                                            {grade}
                                        </option>
                                    ),
                                )}
                            </select>
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
                        ].map(
                            ([field, label]) => (
                                <div key={field}>
                                    <InputLabel
                                        value={label}
                                    />

                                    <TextInput
                                        type="date"
                                        value={
                                            data[field]
                                        }
                                        className="mt-1 block w-full"
                                        onChange={(event) =>
                                            setData(
                                                field,
                                                event
                                                    .target
                                                    .value,
                                            )
                                        }
                                    />
                                </div>
                            ),
                        )}

                        <div>
                            <InputLabel
                                value="Employment Status"
                            />

                            <select
                                value={
                                    data.employment_status
                                }
                                onChange={(event) =>
                                    setData(
                                        'employment_status',
                                        event.target.value,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300"
                            >
                                {options
                                    .employmentStatuses
                                    .map(
                                        (status) => (
                                            <option
                                                key={status}
                                                value={
                                                    status
                                                }
                                            >
                                                {
                                                    status
                                                }
                                            </option>
                                        ),
                                    )}
                            </select>
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <InputLabel
                            value="Qualifications Summary"
                        />

                        <textarea
                            rows="5"
                            value={
                                data.qualifications_summary
                            }
                            onChange={(event) =>
                                setData(
                                    'qualifications_summary',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        />
                    </div>

                    <div className="mt-6">
                        <InputLabel
                            value="Notes"
                        />

                        <textarea
                            rows="5"
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

                    <label className="mt-6 flex items-center gap-3">
                        <input
                            type="checkbox"
                            checked={
                                data.profile_completed
                            }
                            onChange={(event) =>
                                setData(
                                    'profile_completed',
                                    event.target.checked,
                                )
                            }
                            className="rounded border-gray-300 text-blue-600"
                        />

                        <span className="text-sm font-semibold text-slate-700">
                            Profile information is complete
                        </span>
                    </label>
                </section>

                <div className="flex justify-end gap-3">
                    <Link
                        href={route(
                            'principal.profile.show'
                        )}
                        className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        Cancel
                    </Link>

                    <PrimaryButton
                        disabled={processing}
                    >
                        {processing
                            ? 'Saving...'
                            : 'Update Profile'}
                    </PrimaryButton>
                </div>
            </form>
        </AdminLayout>
    );
}
