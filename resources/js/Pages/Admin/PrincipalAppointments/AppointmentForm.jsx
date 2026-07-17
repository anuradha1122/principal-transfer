import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function AppointmentForm({
    profile,
    data,
    setData,
    errors,
    processing,
    schools,
    options,
    editing = false,
    onSubmit,
}) {
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

            <div className="grid gap-6 md:grid-cols-2">
                <div className="md:col-span-2">
                    <InputLabel
                        htmlFor="school_id"
                        value="School"
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
                        className="mt-1 block w-full rounded-md border-gray-300"
                    >
                        <option value="">
                            Select school
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
                                }{' '}
                                Zone
                            </option>
                        ))}
                    </select>

                    <InputError
                        message={errors.school_id}
                        className="mt-2"
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
                        className="mt-1 block w-full rounded-md border-gray-300"
                    >
                        {options.designations.map(
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
                </div>

                <div>
                    <InputLabel
                        htmlFor="appointment_type"
                        value="Appointment Type"
                    />

                    <select
                        id="appointment_type"
                        value={data.appointment_type}
                        onChange={(event) =>
                            setData(
                                'appointment_type',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300"
                    >
                        {options.appointmentTypes.map(
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
                </div>

                <div>
                    <InputLabel
                        htmlFor="appointment_number"
                        value="Appointment Letter Number"
                    />

                    <TextInput
                        id="appointment_number"
                        value={data.appointment_number}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'appointment_number',
                                event.target.value,
                            )
                        }
                    />
                </div>

                {[
                    [
                        'appointment_date',
                        'Appointment Date',
                    ],
                    ['start_date', 'Start Date'],
                    ['end_date', 'End Date'],
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
                        htmlFor="reason_for_end"
                        value="Reason for End"
                    />

                    <TextInput
                        id="reason_for_end"
                        value={data.reason_for_end}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'reason_for_end',
                                event.target.value,
                            )
                        }
                    />
                </div>
            </div>

            <div>
                <InputLabel
                    htmlFor="remarks"
                    value="Remarks"
                />

                <textarea
                    id="remarks"
                    rows="4"
                    value={data.remarks}
                    onChange={(event) =>
                        setData(
                            'remarks',
                            event.target.value,
                        )
                    }
                    className="mt-1 block w-full rounded-md border-gray-300"
                />
            </div>

            <label className="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <input
                    type="checkbox"
                    checked={data.is_current}
                    onChange={(event) =>
                        setData(
                            'is_current',
                            event.target.checked,
                        )
                    }
                    className="mt-1 rounded border-gray-300 text-blue-600"
                />

                <span>
                    <span className="block text-sm font-semibold text-slate-800">
                        Current appointment
                    </span>

                    <span className="block text-xs leading-5 text-slate-500">
                        Selecting this automatically closes any existing current appointment.
                    </span>
                </span>
            </label>

            <div className="flex justify-end gap-3">
                <Link
                    href={route(
                        'admin.principal-profiles.show',
                        profile.id,
                    )}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
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
