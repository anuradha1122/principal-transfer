import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';
import {
    ArrowDown,
    ArrowUp,
    MapPinned,
    Plus,
    School,
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
        Array.isArray(data.preferences)
            ? data.preferences
            : [];

    const zones = Array.from(
        new Map(
            schools
                .filter(
                    (school) =>
                        school.division?.zone?.id,
                )
                .map((school) => [
                    String(
                        school.division.zone.id,
                    ),
                    {
                        id:
                            school.division.zone.id,
                        name:
                            school.division.zone.name,
                    },
                ]),
        ).values(),
    ).sort((first, second) =>
        first.name.localeCompare(
            second.name,
        ),
    );

    const updatePreference = (
        index,
        field,
        value,
    ) => {
        const updated = [
            ...preferences,
        ];

        updated[index] = {
            ...updated[index],
            [field]: value,
        };

        setData(
            'preferences',
            updated,
        );
    };

    const changePreferenceZone = (
        index,
        zoneId,
    ) => {
        const updated = [
            ...preferences,
        ];

        updated[index] = {
            ...updated[index],
            zone_id: zoneId,
            school_id: '',
        };

        setData(
            'preferences',
            updated,
        );
    };

    const addPreference = () => {
        const maximumPreferences =
            Number(
                cycle.maximum_preferences
                ?? 10,
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
                zone_id: '',
                school_id: '',
                preference_reason: '',
            },
        ]);
    };

    const removePreference = (
        index,
    ) => {
        if (
            preferences.length <= 1
        ) {
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
                    preference.school_id
                    ?? '',
                ),
            )
            .filter(Boolean);

    const schoolsForZone = (
        zoneId,
    ) => {
        if (!zoneId) {
            return [];
        }

        return schools
            .filter(
                (school) =>
                    String(
                        school.division
                            ?.zone?.id
                        ?? '',
                    ) ===
                    String(zoneId),
            )
            .sort((first, second) =>
                first.name.localeCompare(
                    second.name,
                ),
            );
    };

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
                            Review the selected transfer cycle before completing the application.
                        </p>
                    </div>

                    <span className="inline-flex w-fit rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {cycle.code}
                    </span>
                </div>

                <div className="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <InfoItem
                        label="Cycle Name"
                        value={cycle.name}
                    />

                    <InfoItem
                        label="Transfer Year"
                        value={
                            cycle.transfer_year
                        }
                    />

                    <InfoItem
                        label="Transfer Type"
                        value={
                            cycle.transfer_type
                        }
                    />

                    <InfoItem
                        label="Maximum Preferences"
                        value={
                            cycle.maximum_preferences
                            ?? 10
                        }
                    />
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-bold text-slate-900">
                    Principal and Current Appointment
                </h2>

                <p className="mt-1 text-sm text-slate-500">
                    These details are copied into the transfer application.
                </p>

                <div className="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <InfoItem
                        label="Full Name"
                        value={
                            profile.full_name
                        }
                    />

                    <InfoItem
                        label="NIC"
                        value={profile.nic}
                    />

                    <InfoItem
                        label="Employee Number"
                        value={
                            profile.employee_number
                            || 'Not recorded'
                        }
                    />

                    <InfoItem
                        label="Service Grade"
                        value={
                            profile.service_grade
                            || 'Not recorded'
                        }
                    />

                    <InfoItem
                        label="Current School"
                        value={
                            currentAppointment
                                ?.school?.name
                            || 'Not recorded'
                        }
                    />

                    <InfoItem
                        label="Designation"
                        value={
                            currentAppointment
                                ?.designation
                            || 'Not recorded'
                        }
                    />

                    <InfoItem
                        label="Division"
                        value={
                            currentAppointment
                                ?.school
                                ?.division
                                ?.name
                            || 'Not recorded'
                        }
                    />

                    <InfoItem
                        label="Zone"
                        value={
                            currentAppointment
                                ?.school
                                ?.division
                                ?.zone
                                ?.name
                            || 'Not recorded'
                        }
                    />
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-bold text-slate-900">
                    Transfer Reason
                </h2>

                <div className="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel value="Transfer Reason" />

                        <select
                            value={
                                data.transfer_reason
                            }
                            onChange={(event) =>
                                setData(
                                    'transfer_reason',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select transfer reason
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
                        <InputLabel value="Reason Details" />

                        <textarea
                            rows="5"
                            value={
                                data.reason_details
                            }
                            onChange={(event) =>
                                setData(
                                    'reason_details',
                                    event.target.value,
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
                    <ReasonCheckbox
                        title="Medical Reason"
                        description="This application includes a medical reason."
                        checked={Boolean(
                            data.has_medical_reason,
                        )}
                        onChange={(checked) =>
                            setData(
                                'has_medical_reason',
                                checked,
                            )
                        }
                    />

                    <ReasonCheckbox
                        title="Spouse Employment Reason"
                        description="This application includes a spouse employment reason."
                        checked={Boolean(
                            data.has_spouse_employment_reason,
                        )}
                        onChange={(checked) =>
                            setData(
                                'has_spouse_employment_reason',
                                checked,
                            )
                        }
                    />
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
                                    event.target.checked,
                                mutual_principal_nic:
                                    event.target.checked
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
                            Select this only when another principal has agreed to a mutual transfer.
                        </p>
                    </div>
                </label>

                {data.is_mutual_transfer && (
                    <div className="mt-5 max-w-xl">
                        <InputLabel value="Mutual Principal NIC" />

                        <TextInput
                            value={
                                data.mutual_principal_nic
                            }
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'mutual_principal_nic',
                                    event.target.value,
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
                            Select a Zone first, then select a school within that Zone.
                        </p>
                    </div>

                    <button
                        type="button"
                        onClick={addPreference}
                        disabled={
                            preferences.length >=
                            Number(
                                cycle.maximum_preferences
                                ?? 10,
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
                        ) => {
                            const filteredSchools =
                                schoolsForZone(
                                    preference.zone_id,
                                );

                            return (
                                <div
                                    key={index}
                                    className="rounded-2xl border border-slate-200 bg-slate-50/50 p-5"
                                >
                                    <div className="flex flex-col gap-4 lg:flex-row lg:items-start">
                                        <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                            {index + 1}
                                        </div>

                                        <div className="grid flex-1 gap-5 xl:grid-cols-3">
                                            <div>
                                                <InputLabel
                                                    value="Preferred Zone"
                                                />

                                                <div className="relative mt-1">
                                                    <MapPinned className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

                                                    <select
                                                        value={
                                                            preference.zone_id
                                                            ?? ''
                                                        }
                                                        onChange={(
                                                            event,
                                                        ) =>
                                                            changePreferenceZone(
                                                                index,
                                                                event.target.value,
                                                            )
                                                        }
                                                        className="block w-full rounded-md border-gray-300 py-2.5 pl-10 pr-9 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                    >
                                                        <option value="">
                                                            Select Zone
                                                        </option>

                                                        {zones.map(
                                                            (zone) => (
                                                                <option
                                                                    key={
                                                                        zone.id
                                                                    }
                                                                    value={
                                                                        zone.id
                                                                    }
                                                                >
                                                                    {
                                                                        zone.name
                                                                    }
                                                                </option>
                                                            ),
                                                        )}
                                                    </select>
                                                </div>

                                                <InputError
                                                    message={
                                                        errors[
                                                            `preferences.${index}.zone_id`
                                                        ]
                                                    }
                                                    className="mt-2"
                                                />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    value="Preferred School"
                                                />

                                                <div className="relative mt-1">
                                                    <School className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

                                                    <select
                                                        value={
                                                            preference.school_id
                                                            ?? ''
                                                        }
                                                        disabled={
                                                            !preference.zone_id
                                                        }
                                                        onChange={(
                                                            event,
                                                        ) =>
                                                            updatePreference(
                                                                index,
                                                                'school_id',
                                                                event.target.value,
                                                            )
                                                        }
                                                        className="block w-full rounded-md border-gray-300 py-2.5 pl-10 pr-9 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                                                    >
                                                        <option value="">
                                                            {preference.zone_id
                                                                ? 'Select School'
                                                                : 'Select Zone first'}
                                                        </option>

                                                        {filteredSchools.map(
                                                            (school) => {
                                                                const schoolId =
                                                                    String(
                                                                        school.id,
                                                                    );

                                                                const alreadySelected =
                                                                    selectedSchoolIds.includes(
                                                                        schoolId,
                                                                    )
                                                                    && String(
                                                                        preference.school_id
                                                                        ?? '',
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
                                                                        {school.census_number
                                                                            ? ` (${school.census_number})`
                                                                            : ''}
                                                                        {' - '}
                                                                        {school
                                                                            .division
                                                                            ?.name
                                                                            ?? 'No division'}
                                                                    </option>
                                                                );
                                                            },
                                                        )}
                                                    </select>
                                                </div>

                                                {preference.zone_id
                                                    && filteredSchools.length ===
                                                        0 && (
                                                        <p className="mt-2 text-xs font-medium text-amber-700">
                                                            No eligible schools are available in this Zone.
                                                        </p>
                                                    )}

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
                                                        preference.preference_reason
                                                        ?? ''
                                                    }
                                                    className="mt-1 block w-full"
                                                    onChange={(
                                                        event,
                                                    ) =>
                                                        updatePreference(
                                                            index,
                                                            'preference_reason',
                                                            event.target.value,
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
                                                    index === 0
                                                }
                                                title="Move up"
                                                className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
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
                                                className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
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
                                                className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-white text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            );
                        },
                    )}

                    <InputError
                        message={
                            errors.preferences
                        }
                    />
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <InputLabel value="Principal Remarks" />

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

function InfoItem({
    label,
    value,
}) {
    return (
        <div>
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </p>

            <p className="mt-1 text-sm font-semibold text-slate-800">
                {value}
            </p>
        </div>
    );
}

function ReasonCheckbox({
    title,
    description,
    checked,
    onChange,
}) {
    return (
        <label className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
            <input
                type="checkbox"
                checked={checked}
                onChange={(event) =>
                    onChange(
                        event.target.checked,
                    )
                }
                className="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />

            <div>
                <p className="text-sm font-semibold text-slate-800">
                    {title}
                </p>

                <p className="mt-1 text-xs text-slate-500">
                    {description}
                </p>
            </div>
        </label>
    );
}
