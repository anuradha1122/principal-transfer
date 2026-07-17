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
    return value ? value.substring(0, 10) : '';
}

export default function Edit({
    profile,
    genders,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        name_with_initials:
            profile.name_with_initials ?? '',
        gender: profile.gender ?? '',
        date_of_birth: dateValue(
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
        city: profile.city ?? '',
        postal_code:
            profile.postal_code ?? '',
        qualifications_summary:
            profile.qualifications_summary ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        put(route('principal.profile.update'));
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
                            Update your personal and contact
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
                            Official service information is read-only
                        </p>

                        <p className="mt-1 text-sm leading-6 text-blue-700">
                            NIC, employee number, service grade,
                            employment status and appointment details
                            can be changed only by an authorized
                            officer.
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
                        Official Information
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        These fields cannot be changed from your
                        account.
                    </p>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <InputLabel
                                value="Full Name"
                            />

                            <TextInput
                                value={
                                    profile.full_name ?? ''
                                }
                                disabled
                                className="mt-1 block w-full bg-slate-100"
                            />
                        </div>

                        <div>
                            <InputLabel
                                value="NIC Number"
                            />

                            <TextInput
                                value={profile.nic ?? ''}
                                disabled
                                className="mt-1 block w-full bg-slate-100"
                            />
                        </div>

                        <div>
                            <InputLabel
                                value="Employee Number"
                            />

                            <TextInput
                                value={
                                    profile.employee_number ??
                                    ''
                                }
                                disabled
                                className="mt-1 block w-full bg-slate-100"
                            />
                        </div>

                        <div>
                            <InputLabel
                                value="Service Grade"
                            />

                            <TextInput
                                value={
                                    profile.service_grade ??
                                    ''
                                }
                                disabled
                                className="mt-1 block w-full bg-slate-100"
                            />
                        </div>
                    </div>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Personal Information
                    </h2>

                    <div className="mt-6 grid gap-6 md:grid-cols-2">
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

                                {genders.map((gender) => (
                                    <option
                                        key={gender}
                                        value={gender}
                                    >
                                        {gender}
                                    </option>
                                ))}
                            </select>

                            <InputError
                                message={errors.gender}
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
