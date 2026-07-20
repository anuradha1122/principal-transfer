import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import {
    AlertCircle,
    ArrowLeft,
    Save,
} from 'lucide-react';

function dateValue(value) {
    return value
        ? String(value).substring(0, 10)
        : '';
}

export default function Edit({
    profile,
    options = {},
    genders: legacyGenders = [],
}) {
    const genders =
        options.genders ??
        legacyGenders ??
        [
            'Male',
            'Female',
            'Other',
        ];

    const serviceGrades =
        options.serviceGrades ?? [
            'SLPS I',
            'SLPS II',
            'SLPS III',
            'Other',
        ];

    const employmentStatuses =
        options.employmentStatuses ?? [
            'Active',
            'Retired',
            'Resigned',
            'Deceased',
            'Suspended',
            'Other',
        ];

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
                profile.date_of_birth,
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
                profile.first_appointment_date,
            ),

        principal_service_entry_date:
            dateValue(
                profile.principal_service_entry_date,
            ),

        retirement_date:
            dateValue(
                profile.retirement_date,
            ),

        employment_status:
            profile.employment_status ??
            'Active',

        qualifications_summary:
            profile.qualifications_summary ??
            '',

        notes:
            profile.notes ?? '',

        profile_completed:
            Boolean(
                profile.profile_completed,
            ),
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'principal.profile.update',
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Edit My Profile"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Edit My Profile
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Update your personal,
                            contact and service
                            information.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'principal.profile.show',
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back to Profile
                    </Link>
                </div>
            }
        >
            <Head title="Edit My Profile" />

            <div className="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <div className="flex items-start gap-3">
                    <AlertCircle className="mt-0.5 h-5 w-5 shrink-0 text-blue-600" />

                    <div>
                        <p className="font-semibold text-blue-900">
                            NIC number is locked
                        </p>

                        <p className="mt-1 text-sm leading-6 text-blue-700">
                            You may update personal,
                            contact and service
                            information. Your NIC number
                            cannot be changed because it is
                            linked to the principal
                            registry.
                        </p>
                    </div>
                </div>
            </div>

            <form
                onSubmit={submit}
                className="space-y-6"
            >
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Identity Information
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        Only the NIC field is read-only.
                    </p>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel
                                htmlFor="full_name"
                                value="Full Name"
                            />

                            <TextInput
                                id="full_name"
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
                                value="NIC Number"
                            />

                            <TextInput
                                value={
                                    profile.nic ?? ''
                                }
                                disabled
                                className="mt-1 block w-full bg-slate-100 text-slate-500"
                            />

                            <p className="mt-1 text-xs text-slate-500">
                                NIC cannot be changed.
                            </p>
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="employee_number"
                                value="Employee Number"
                            />

                            <TextInput
                                id="employee_number"
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
                                htmlFor="name_with_initials"
                                value="Name With Initials"
                            />

                            <TextInput
                                id="name_with_initials"
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

                            <InputError
                                message={
                                    errors.name_with_initials
                                }
                                className="mt-2"
                            />
                        </div>

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
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    Select gender
                                </option>

                                {genders.map(
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

                            <InputError
                                message={
                                    errors.gender
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="date_of_birth"
                                value="Date of Birth"
                            />

                            <TextInput
                                id="date_of_birth"
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

                            <InputError
                                message={
                                    errors.date_of_birth
                                }
                                className="mt-2"
                            />
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Contact Information
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel
                                htmlFor="mobile_number"
                                value="Mobile Number"
                            />

                            <TextInput
                                id="mobile_number"
                                value={
                                    data.mobile_number
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'mobile_number',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.mobile_number
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="alternate_number"
                                value="Alternate Number"
                            />

                            <TextInput
                                id="alternate_number"
                                value={
                                    data.alternate_number
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'alternate_number',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.alternate_number
                                }
                                className="mt-2"
                            />
                        </div>

                        <div className="md:col-span-2">
                            <InputLabel
                                htmlFor="personal_email"
                                value="Personal Email Address"
                            />

                            <TextInput
                                id="personal_email"
                                type="email"
                                value={
                                    data.personal_email
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'personal_email',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.personal_email
                                }
                                className="mt-2"
                            />
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Residential Address
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        <div className="md:col-span-2">
                            <InputLabel
                                htmlFor="address_line_1"
                                value="Address Line 1"
                            />

                            <TextInput
                                id="address_line_1"
                                value={
                                    data.address_line_1
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'address_line_1',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.address_line_1
                                }
                                className="mt-2"
                            />
                        </div>

                        <div className="md:col-span-2">
                            <InputLabel
                                htmlFor="address_line_2"
                                value="Address Line 2"
                            />

                            <TextInput
                                id="address_line_2"
                                value={
                                    data.address_line_2
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'address_line_2',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.address_line_2
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="city"
                                value="City"
                            />

                            <TextInput
                                id="city"
                                value={data.city}
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'city',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={errors.city}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="postal_code"
                                value="Postal Code"
                            />

                            <TextInput
                                id="postal_code"
                                value={data.postal_code}
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'postal_code',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.postal_code
                                }
                                className="mt-2"
                            />
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Service Information
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
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
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    Select service category
                                </option>

                                <option value="Sri Lanka Principals Service">
                                    Sri Lanka Principals Service
                                </option>

                                <option value="Sri Lanka Education Administrative Service">
                                    Sri Lanka Education Administrative Service
                                </option>

                                <option value="Other">
                                    Other
                                </option>
                            </select>

                            <InputError
                                message={errors.service_category}
                                className="mt-2"
                            />

                            <InputError
                                message={
                                    errors.service_category
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="service_grade"
                                value="Service Grade"
                            />

                            <select
                                id="service_grade"
                                value={
                                    data.service_grade
                                }
                                onChange={(event) =>
                                    setData(
                                        'service_grade',
                                        event.target.value,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    Select service grade
                                </option>

                                {serviceGrades.map(
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

                            <InputError
                                message={
                                    errors.service_grade
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="first_appointment_date"
                                value="First Appointment Date"
                            />

                            <TextInput
                                id="first_appointment_date"
                                type="date"
                                value={
                                    data.first_appointment_date
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'first_appointment_date',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.first_appointment_date
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="principal_service_entry_date"
                                value="Principal Service Entry Date"
                            />

                            <TextInput
                                id="principal_service_entry_date"
                                type="date"
                                value={
                                    data.principal_service_entry_date
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'principal_service_entry_date',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.principal_service_entry_date
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="retirement_date"
                                value="Retirement Date"
                            />

                            <TextInput
                                id="retirement_date"
                                type="date"
                                value={
                                    data.retirement_date
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    setData(
                                        'retirement_date',
                                        event.target.value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.retirement_date
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="employment_status"
                                value="Employment Status"
                            />

                            <select
                                id="employment_status"
                                value={
                                    data.employment_status
                                }
                                onChange={(event) =>
                                    setData(
                                        'employment_status',
                                        event.target.value,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                {employmentStatuses.map(
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

                            <InputError
                                message={
                                    errors.employment_status
                                }
                                className="mt-2"
                            />
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <InputLabel
                        htmlFor="qualifications_summary"
                        value="Qualifications Summary"
                    />

                    <textarea
                        id="qualifications_summary"
                        rows="6"
                        value={
                            data.qualifications_summary
                        }
                        onChange={(event) =>
                            setData(
                                'qualifications_summary',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Enter academic and professional qualifications"
                    />

                    <InputError
                        message={
                            errors.qualifications_summary
                        }
                        className="mt-2"
                    />

                    <div className="mt-6">
                        <InputLabel
                            htmlFor="notes"
                            value="Notes"
                        />

                        <textarea
                            id="notes"
                            rows="5"
                            value={data.notes}
                            onChange={(event) =>
                                setData(
                                    'notes',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Add any relevant notes"
                        />

                        <InputError
                            message={errors.notes}
                            className="mt-2"
                        />
                    </div>

                    <label className="mt-6 flex items-center gap-3 rounded-xl border border-slate-200 p-4">
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
                            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />

                        <div>
                            <p className="text-sm font-semibold text-slate-800">
                                Profile information is complete
                            </p>

                            <p className="mt-1 text-xs text-slate-500">
                                Confirm that the information
                                entered above is complete.
                            </p>
                        </div>
                    </label>
                </section>

                <div className="flex justify-end gap-3">
                    <Link
                        href={route(
                            'principal.profile.show',
                        )}
                        className="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <PrimaryButton
                        disabled={processing}
                        className="inline-flex items-center gap-2"
                    >
                        <Save className="h-4 w-4" />

                        {processing
                            ? 'Saving...'
                            : 'Save Changes'}
                    </PrimaryButton>
                </div>
            </form>
        </AdminLayout>
    );
}
