import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';
import {
    ArrowDown,
    ArrowUp,
    Plus,
    Trash2,
} from 'lucide-react';

export default function ApplicationForm({
    data,
    setData,
    errors,
    processing,
    profile,
    cycle,
    schools = [],
    reasons = [],
    editing = false,
    onSubmit,
}) {
    const preferences =
        data.preferences ?? [];

    const updatePreference = (
        index,
        field,
        value,
    ) => {
        const updated = [...preferences];

        updated[index] = {
            ...updated[index],
            [field]: value,
        };

        setData(
            'preferences',
            updated,
        );
    };

    const addPreference = () => {
        const maximumPreferences =
            Number(
                cycle.maximum_preferences ??
                    10,
            );

        if (
            preferences.length >=
            maximumPreferences
        ) {
            return;
        }

        setData('preferences', [
            ...preferences,
            {
                school_id: '',
                preference_reason: '',
            },
        ]);
    };

    const removePreference = (index) => {
        if (preferences.length <= 1) {
            return;
        }

        setData(
            'preferences',
            preferences.filter(
                (_, itemIndex) =>
                    itemIndex !== index,
            ),
        );
    };

    const movePreference = (
        index,
        direction,
    ) => {
        const targetIndex =
            index + direction;

        if (
            targetIndex < 0 ||
            targetIndex >=
                preferences.length
        ) {
            return;
        }

        const updated = [
            ...preferences,
        ];

        [
            updated[index],
            updated[targetIndex],
        ] = [
            updated[targetIndex],
            updated[index],
        ];

        setData(
            'preferences',
            updated,
        );
    };

    const selectedSchoolIds =
        preferences
            .map((preference) =>
                String(
                    preference.school_id,
                ),
            )
            .filter(Boolean);

    const currentAppointment =
        profile.current_appointment;

    return (
        <form
            onSubmit={onSubmit}
            className="space-y-6"
        >
            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h2 className="text-lg font-bold text-slate-900">
                            Transfer Cycle
                        </h2>

                        <p className="mt-1 text-sm text-slate-500">
                            Review the selected
                            transfer cycle before
                            completing the
                            application.
                        </p>
                    </div>

                    <span className="inline-flex w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {cycle.code}
                    </span>
                </div>

                <div className="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Cycle Name
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {cycle.name}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Transfer Year
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {cycle.transfer_year}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Transfer Type
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {cycle.transfer_type}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Maximum Preferences
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {cycle.maximum_preferences ??
                                10}
                        </p>
                    </div>
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-bold text-slate-900">
                    Principal and Current Appointment
                </h2>

                <p className="mt-1 text-sm text-slate-500">
                    These details are copied
                    into the transfer
                    application.
                </p>

                <div className="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Full Name
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {profile.full_name}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            NIC
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {profile.nic}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Employee Number
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {profile.employee_number ||
                                'Not recorded'}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Service Grade
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {profile.service_grade ||
                                'Not recorded'}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Current School
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {currentAppointment
                                ?.school?.name ||
                                'Not recorded'}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Designation
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {currentAppointment
                                ?.designation ||
                                'Not recorded'}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Division
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {currentAppointment
                                ?.school?.division
                                ?.name ||
                                'Not recorded'}
                        </p>
                    </div>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Zone
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-800">
                            {currentAppointment
                                ?.school?.division
                                ?.zone?.name ||
                                'Not recorded'}
                        </p>
                    </div>
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-bold text-slate-900">
                    Transfer Reason
                </h2>

                <div className="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            value="Transfer Reason"
                        />

                        <select
                            value={
                                data.transfer_reason
                            }
                            onChange={(event) =>
                                setData(
                                    'transfer_reason',
                                    event.target
                                        .value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select transfer
                                reason
                            </option>

                            {reasons.map(
                                (reason) => (
                                    <option
                                        key={reason}
                                        value={reason}
                                    >
                                        {reason}
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={
                                errors.transfer_reason
                            }
                            className="mt-2"
                        />
                    </div>

                    <div className="md:col-span-2">
                        <InputLabel
                            value="Reason Details"
                        />

                        <textarea
                            rows="5"
                            value={
                                data.reason_details
                            }
                            onChange={(event) =>
                                setData(
                                    'reason_details',
                                    event.target
                                        .value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Explain the reason for requesting this transfer."
                        />

                        <InputError
                            message={
                                errors.reason_details
                            }
                            className="mt-2"
                        />
                    </div>
                </div>

                <div className="mt-6 grid gap-4 md:grid-cols-2">
                    <label className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                        <input
                            type="checkbox"
                            checked={Boolean(
                                data.has_medical_reason,
                            )}
                            onChange={(event) =>
                                setData(
                                    'has_medical_reason',
                                    event.target
                                        .checked,
                                )
                            }
                            className="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />

                        <div>
                            <p className="text-sm font-semibold text-slate-800">
                                Medical Reason
                            </p>

                            <p className="mt-1 text-xs text-slate-500">
                                This application
                                includes a medical
                                reason.
                            </p>
                        </div>
                    </label>

                    <label className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                        <input
                            type="checkbox"
                            checked={Boolean(
                                data.has_spouse_employment_reason,
                            )}
                            onChange={(event) =>
                                setData(
                                    'has_spouse_employment_reason',
                                    event.target
                                        .checked,
                                )
                            }
                            className="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />

                        <div>
                            <p className="text-sm font-semibold text-slate-800">
                                Spouse Employment
                                Reason
                            </p>

                            <p className="mt-1 text-xs text-slate-500">
                                This application
                                includes a spouse
                                employment reason.
                            </p>
                        </div>
                    </label>
                </div>

                <label className="mt-4 flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                    <input
                        type="checkbox"
                        checked={Boolean(
                            data.is_mutual_transfer,
                        )}
                        onChange={(event) =>
                            setData({
                                ...data,
                                is_mutual_transfer:
                                    event.target
                                        .checked,
                                mutual_principal_nic:
                                    event.target
                                        .checked
                                        ? data.mutual_principal_nic
                                        : '',
                            })
                        }
                        className="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />

                    <div>
                        <p className="text-sm font-semibold text-slate-800">
                            Mutual Transfer
                        </p>

                        <p className="mt-1 text-xs text-slate-500">
                            Select this only when
                            another principal has
                            agreed to a mutual
                            transfer.
                        </p>
                    </div>
                </label>

                {data.is_mutual_transfer && (
                    <div className="mt-5 max-w-xl">
                        <InputLabel
                            value="Mutual Principal NIC"
                        />

                        <TextInput
                            value={
                                data.mutual_principal_nic
                            }
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'mutual_principal_nic',
                                    event.target
                                        .value,
                                )
                            }
                        />

                        <InputError
                            message={
                                errors.mutual_principal_nic
                            }
                            className="mt-2"
                        />
                    </div>
                )}
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 className="text-lg font-bold text-slate-900">
                            School Preferences
                        </h2>

                        <p className="mt-1 text-sm text-slate-500">
                            Add schools in order
                            of preference.
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={addPreference}
                        disabled={
                            preferences.length >=
                            Number(
                                cycle.maximum_preferences ??
                                    10,
                            )
                        }
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Plus className="h-4 w-4" />
                        Add Preference
                    </button>
                </div>

                <div className="space-y-4 p-6">
                    {preferences.map(
                        (
                            preference,
                            index,
                        ) => (
                            <div
                                key={index}
                                className="rounded-xl border border-slate-200 p-5"
                            >
                                <div className="flex flex-col gap-4 lg:flex-row lg:items-start">
                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-700">
                                        {index + 1}
                                    </div>

                                    <div className="grid flex-1 gap-5 md:grid-cols-2">
                                        <div>
                                            <InputLabel
                                                value="Preferred School"
                                            />

                                            <select
                                                value={
                                                    preference.school_id
                                                }
                                                onChange={(
                                                    event,
                                                ) =>
                                                    updatePreference(
                                                        index,
                                                        'school_id',
                                                        event
                                                            .target
                                                            .value,
                                                    )
                                                }
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            >
                                                <option value="">
                                                    Select
                                                    school
                                                </option>

                                                {schools.map(
                                                    (
                                                        school,
                                                    ) => {
                                                        const schoolId =
                                                            String(
                                                                school.id,
                                                            );

                                                        const alreadySelected =
                                                            selectedSchoolIds.includes(
                                                                schoolId,
                                                            ) &&
                                                            String(
                                                                preference.school_id,
                                                            ) !==
                                                                schoolId;

                                                        return (
                                                            <option
                                                                key={
                                                                    school.id
                                                                }
                                                                value={
                                                                    school.id
                                                                }
                                                                disabled={
                                                                    alreadySelected
                                                                }
                                                            >
                                                                {
                                                                    school.name
                                                                }
                                                                {' - '}
                                                                {school
                                                                    .division
                                                                    ?.name ||
                                                                    'No division'}
                                                                {' / '}
                                                                {school
                                                                    .division
                                                                    ?.zone
                                                                    ?.name ||
                                                                    'No zone'}
                                                            </option>
                                                        );
                                                    },
                                                )}
                                            </select>

                                            <InputError
                                                message={
                                                    errors[
                                                        `preferences.${index}.school_id`
                                                    ]
                                                }
                                                className="mt-2"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                value="Preference Reason"
                                            />

                                            <TextInput
                                                value={
                                                    preference.preference_reason ??
                                                    ''
                                                }
                                                className="mt-1 block w-full"
                                                onChange={(
                                                    event,
                                                ) =>
                                                    updatePreference(
                                                        index,
                                                        'preference_reason',
                                                        event
                                                            .target
                                                            .value,
                                                    )
                                                }
                                                placeholder="Optional reason"
                                            />

                                            <InputError
                                                message={
                                                    errors[
                                                        `preferences.${index}.preference_reason`
                                                    ]
                                                }
                                                className="mt-2"
                                            />
                                        </div>
                                    </div>

                                    <div className="flex shrink-0 items-center gap-2">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                movePreference(
                                                    index,
                                                    -1,
                                                )
                                            }
                                            disabled={
                                                index ===
                                                0
                                            }
                                            title="Move up"
                                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            <ArrowUp className="h-4 w-4" />
                                        </button>

                                        <button
                                            type="button"
                                            onClick={() =>
                                                movePreference(
                                                    index,
                                                    1,
                                                )
                                            }
                                            disabled={
                                                index ===
                                                preferences.length -
                                                    1
                                            }
                                            title="Move down"
                                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            <ArrowDown className="h-4 w-4" />
                                        </button>

                                        <button
                                            type="button"
                                            onClick={() =>
                                                removePreference(
                                                    index,
                                                )
                                            }
                                            disabled={
                                                preferences.length <=
                                                1
                                            }
                                            title="Remove preference"
                                            className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ),
                    )}

                    <InputError
                        message={
                            errors.preferences
                        }
                    />
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <InputLabel
                    value="Principal Remarks"
                />

                <textarea
                    rows="4"
                    value={
                        data.principal_remarks
                    }
                    onChange={(event) =>
                        setData(
                            'principal_remarks',
                            event.target.value,
                        )
                    }
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Optional additional remarks"
                />

                <InputError
                    message={
                        errors.principal_remarks
                    }
                    className="mt-2"
                />
            </section>

            <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Link
                    href={route(
                        'principal.transfer-applications.index',
                    )}
                    className="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Cancel
                </Link>

                <PrimaryButton
                    disabled={processing}
                >
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Draft'
                          : 'Save as Draft'}
                </PrimaryButton>
            </div>
        </form>
    );
}
