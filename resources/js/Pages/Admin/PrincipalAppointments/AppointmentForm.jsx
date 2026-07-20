import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

export default function AppointmentForm({
    profile,
    data,
    setData,
    errors,
    processing,
    schools = [],
    options = {},
    editing = false,
    onSubmit,
    cancelRoute = 'principal.profile.show',
}) {
    const designations =
        options.designations ?? [
            'Principal',
            'Deputy Principal',
            'Assistant Principal',
            'Acting Principal',
            'Other',
        ];

    const appointmentTypes =
        options.appointmentTypes ?? [
            'Permanent',
            'Acting',
            'Covering',
            'Temporary',
            'Other',
        ];

    const selectedSchool = useMemo(
        () =>
            schools.find(
                (school) =>
                    String(school.id) ===
                    String(data.school_id),
            ) ?? null,
        [schools, data.school_id],
    );

    const initialZoneId =
        selectedSchool?.division?.zone?.id
            ? String(
                  selectedSchool.division.zone.id,
              )
            : '';

    const [selectedZoneId, setSelectedZoneId] =
        useState(initialZoneId);

    const zones = useMemo(() => {
        const zoneMap = new Map();

        schools.forEach((school) => {
            const zone =
                school.division?.zone;

            if (!zone?.id) {
                return;
            }

            zoneMap.set(
                String(zone.id),
                {
                    id: zone.id,
                    name: zone.name,
                },
            );
        });

        return Array.from(
            zoneMap.values(),
        ).sort((first, second) =>
            first.name.localeCompare(
                second.name,
            ),
        );
    }, [schools]);

    const filteredSchools = useMemo(() => {
        if (!selectedZoneId) {
            return [];
        }

        return schools
            .filter(
                (school) =>
                    String(
                        school.division
                            ?.zone?.id,
                    ) ===
                    String(
                        selectedZoneId,
                    ),
            )
            .sort((first, second) =>
                first.name.localeCompare(
                    second.name,
                ),
            );
    }, [
        schools,
        selectedZoneId,
    ]);

    useEffect(() => {
        if (
            data.school_id &&
            selectedSchool?.division?.zone?.id
        ) {
            setSelectedZoneId(
                String(
                    selectedSchool
                        .division.zone.id,
                ),
            );
        }
    }, [
        data.school_id,
        selectedSchool,
    ]);

    const handleZoneChange = (
        event,
    ) => {
        const zoneId =
            event.target.value;

        setSelectedZoneId(zoneId);

        setData(
            'school_id',
            '',
        );
    };

    const handleAppointmentDateChange = (
        event,
    ) => {
        const appointmentDate =
            event.target.value;

        setData({
            ...data,
            appointment_date:
                appointmentDate,
            start_date:
                appointmentDate,
        });
    };

    const handleCurrentChange = (
        event,
    ) => {
        const isCurrent =
            event.target.checked;

        setData({
            ...data,
            is_current: isCurrent,
            end_date: isCurrent
                ? ''
                : data.end_date,
            reason_for_end: isCurrent
                ? ''
                : data.reason_for_end,
        });
    };

    return (
        <form
            onSubmit={onSubmit}
            className="space-y-6"
        >
            <div className="rounded-xl border border-blue-100 bg-blue-50 p-4">
                <p className="font-semibold text-blue-900">
                    {profile.full_name}
                </p>

                <p className="mt-1 font-mono text-xs text-blue-700">
                    {profile.nic}
                </p>
            </div>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 className="text-lg font-bold text-slate-900">
                    School Appointment
                </h2>

                <p className="mt-1 text-sm text-slate-500">
                    Select the zone first,
                    then select a school
                    belonging to that zone.
                </p>

                <div className="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="zone_id"
                            value="Zone"
                        />

                        <select
                            id="zone_id"
                            value={
                                selectedZoneId
                            }
                            onChange={
                                handleZoneChange
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select zone
                            </option>

                            {zones.map(
                                (zone) => (
                                    <option
                                        key={zone.id}
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

                        {!selectedZoneId && (
                            <p className="mt-1 text-xs text-slate-500">
                                Select a zone
                                before choosing
                                the school.
                            </p>
                        )}
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="school_id"
                            value="School"
                        />

                        <select
                            id="school_id"
                            value={
                                data.school_id
                            }
                            disabled={
                                !selectedZoneId
                            }
                            onChange={(
                                event,
                            ) =>
                                setData(
                                    'school_id',
                                    event
                                        .target
                                        .value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm disabled:cursor-not-allowed disabled:bg-slate-100 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                {selectedZoneId
                                    ? 'Select school'
                                    : 'Select zone first'}
                            </option>

                            {filteredSchools.map(
                                (school) => (
                                    <option
                                        key={
                                            school.id
                                        }
                                        value={
                                            school.id
                                        }
                                    >
                                        {
                                            school.name
                                        }{' '}
                                        (
                                        {
                                            school.census_number
                                        }
                                        )
                                        {' - '}
                                        {school
                                            .division
                                            ?.name ||
                                            'Division not recorded'}
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={
                                errors.school_id
                            }
                            className="mt-2"
                        />

                        {selectedZoneId &&
                            filteredSchools.length ===
                                0 && (
                                <p className="mt-2 text-sm text-amber-600">
                                    No active
                                    schools were
                                    found for the
                                    selected zone.
                                </p>
                            )}
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="designation"
                            value="Designation"
                        />

                        <select
                            id="designation"
                            value={
                                data.designation
                            }
                            onChange={(
                                event,
                            ) =>
                                setData(
                                    'designation',
                                    event
                                        .target
                                        .value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select
                                designation
                            </option>

                            {designations.map(
                                (
                                    designation,
                                ) => (
                                    <option
                                        key={
                                            designation
                                        }
                                        value={
                                            designation
                                        }
                                    >
                                        {
                                            designation
                                        }
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={
                                errors.designation
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="appointment_type"
                            value="Appointment Type"
                        />

                        <select
                            id="appointment_type"
                            value={
                                data.appointment_type
                            }
                            onChange={(
                                event,
                            ) =>
                                setData(
                                    'appointment_type',
                                    event
                                        .target
                                        .value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select
                                appointment type
                            </option>

                            {appointmentTypes.map(
                                (type) => (
                                    <option
                                        key={type}
                                        value={type}
                                    >
                                        {type}
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={
                                errors.appointment_type
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="appointment_number"
                            value="Appointment Letter Number"
                        />

                        <TextInput
                            id="appointment_number"
                            value={
                                data.appointment_number ??
                                ''
                            }
                            className="mt-1 block w-full"
                            onChange={(
                                event,
                            ) =>
                                setData(
                                    'appointment_number',
                                    event
                                        .target
                                        .value,
                                )
                            }
                        />

                        <InputError
                            message={
                                errors.appointment_number
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="appointment_date"
                            value="Appointment Date"
                        />

                        <TextInput
                            id="appointment_date"
                            type="date"
                            value={
                                data.appointment_date ??
                                ''
                            }
                            className="mt-1 block w-full"
                            onChange={
                                handleAppointmentDateChange
                            }
                        />

                        <InputError
                            message={
                                errors.appointment_date
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="start_date"
                            value="Start Date"
                        />

                        <TextInput
                            id="start_date"
                            type="date"
                            value={
                                data.start_date ??
                                ''
                            }
                            readOnly
                            className="mt-1 block w-full bg-slate-100 text-slate-600"
                        />

                        <p className="mt-1 text-xs text-slate-500">
                            Start date is
                            automatically set to
                            the appointment date.
                        </p>

                        <InputError
                            message={
                                errors.start_date
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="end_date"
                            value="End Date"
                        />

                        <TextInput
                            id="end_date"
                            type="date"
                            value={
                                data.end_date ??
                                ''
                            }
                            disabled={
                                Boolean(
                                    data.is_current,
                                )
                            }
                            className="mt-1 block w-full disabled:bg-slate-100"
                            onChange={(
                                event,
                            ) =>
                                setData(
                                    'end_date',
                                    event
                                        .target
                                        .value,
                                )
                            }
                        />

                        <InputError
                            message={
                                errors.end_date
                            }
                            className="mt-2"
                        />
                    </div>

                    {!data.is_current && (
                        <div>
                            <InputLabel
                                htmlFor="reason_for_end"
                                value="Reason for End"
                            />

                            <TextInput
                                id="reason_for_end"
                                value={
                                    data.reason_for_end ??
                                    ''
                                }
                                className="mt-1 block w-full"
                                onChange={(
                                    event,
                                ) =>
                                    setData(
                                        'reason_for_end',
                                        event
                                            .target
                                            .value,
                                    )
                                }
                            />

                            <InputError
                                message={
                                    errors.reason_for_end
                                }
                                className="mt-2"
                            />
                        </div>
                    )}
                </div>
            </section>

            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <InputLabel
                    htmlFor="remarks"
                    value="Remarks"
                />

                <textarea
                    id="remarks"
                    rows="4"
                    value={
                        data.remarks ?? ''
                    }
                    onChange={(event) =>
                        setData(
                            'remarks',
                            event.target.value,
                        )
                    }
                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />

                <InputError
                    message={
                        errors.remarks
                    }
                    className="mt-2"
                />
            </section>

            <label className="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
                <input
                    type="checkbox"
                    checked={Boolean(
                        data.is_current,
                    )}
                    onChange={
                        handleCurrentChange
                    }
                    className="mt-1 rounded border-blue-300 text-blue-600 focus:ring-blue-500"
                />

                <span>
                    <span className="block text-sm font-semibold text-blue-900">
                        Current appointment
                    </span>

                    <span className="block text-xs leading-5 text-blue-700">
                        Selecting this
                        automatically closes any
                        existing current
                        appointment. The end date
                        and reason for end are
                        cleared.
                    </span>
                </span>
            </label>

            <div className="flex justify-end gap-3">
                <Link
                    href={route(
                        cancelRoute,
                        cancelRoute.startsWith(
                            'admin.',
                        )
                            ? profile.id
                            : undefined,
                    )}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Cancel
                </Link>

                <PrimaryButton
                    disabled={processing}
                >
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Appointment'
                          : 'Add Appointment'}
                </PrimaryButton>
            </div>
        </form>
    );
}
