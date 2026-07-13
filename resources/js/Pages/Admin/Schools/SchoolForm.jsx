import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';
import { useMemo } from 'react';

export default function SchoolForm({
    data,
    setData,
    errors,
    processing,
    zones,
    divisions,
    schoolTypes,
    genderTypes,
    schoolLevels,
    mediumOptions,
    editing = false,
    onSubmit,
}) {
    const filteredDivisions = useMemo(() => {
        if (!data.zone_id) {
            return divisions;
        }

        return divisions.filter(
            (division) =>
                String(division.zone_id) ===
                String(data.zone_id),
        );
    }, [data.zone_id, divisions]);

    const toggleMedium = (medium) => {
        const selected = data.mediums.includes(medium);

        setData(
            'mediums',
            selected
                ? data.mediums.filter(
                      (item) => item !== medium,
                  )
                : [...data.mediums, medium],
        );
    };

    return (
        <form
            onSubmit={onSubmit}
            className="space-y-8"
        >
            <section>
                <h2 className="text-lg font-bold text-slate-900">
                    Administrative Assignment
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="zone_id"
                            value="Education Zone"
                        />

                        <select
                            id="zone_id"
                            value={data.zone_id}
                            onChange={(event) => {
                                setData((current) => ({
                                    ...current,
                                    zone_id:
                                        event.target.value,
                                    division_id: '',
                                }));
                            }}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select zone
                            </option>

                            {zones.map((zone) => (
                                <option
                                    key={zone.id}
                                    value={zone.id}
                                >
                                    {zone.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="division_id"
                            value="Education Division"
                        />

                        <select
                            id="division_id"
                            value={data.division_id}
                            onChange={(event) =>
                                setData(
                                    'division_id',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">
                                Select division
                            </option>

                            {filteredDivisions.map(
                                (division) => (
                                    <option
                                        key={division.id}
                                        value={division.id}
                                    >
                                        {division.name}
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={errors.division_id}
                            className="mt-2"
                        />
                    </div>
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <h2 className="text-lg font-bold text-slate-900">
                    School Information
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="census_number"
                            value="Census Number"
                        />

                        <TextInput
                            id="census_number"
                            value={data.census_number}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'census_number',
                                    event.target.value.trim(),
                                )
                            }
                        />

                        <InputError
                            message={errors.census_number}
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="name"
                            value="School Name"
                        />

                        <TextInput
                            id="name"
                            value={data.name}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'name',
                                    event.target.value,
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
                            htmlFor="school_type"
                            value="School Type"
                        />

                        <select
                            id="school_type"
                            value={data.school_type}
                            onChange={(event) =>
                                setData(
                                    'school_type',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            <option value="">
                                Select school type
                            </option>

                            {schoolTypes.map((type) => (
                                <option
                                    key={type}
                                    value={type}
                                >
                                    {type}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="gender_type"
                            value="Gender Type"
                        />

                        <select
                            id="gender_type"
                            value={data.gender_type}
                            onChange={(event) =>
                                setData(
                                    'gender_type',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            {genderTypes.map((type) => (
                                <option
                                    key={type}
                                    value={type}
                                >
                                    {type}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="school_level"
                            value="School Level"
                        />

                        <select
                            id="school_level"
                            value={data.school_level}
                            onChange={(event) =>
                                setData(
                                    'school_level',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            <option value="">
                                Select level
                            </option>

                            {schoolLevels.map((level) => (
                                <option
                                    key={level}
                                    value={level}
                                >
                                    {level}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <InputLabel value="Teaching Mediums" />

                        <div className="mt-2 flex flex-wrap gap-3">
                            {mediumOptions.map((medium) => (
                                <label
                                    key={medium}
                                    className="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2"
                                >
                                    <input
                                        type="checkbox"
                                        checked={data.mediums.includes(
                                            medium,
                                        )}
                                        onChange={() =>
                                            toggleMedium(medium)
                                        }
                                        className="rounded border-gray-300 text-blue-600"
                                    />
                                    <span className="text-sm text-slate-700">
                                        {medium}
                                    </span>
                                </label>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <h2 className="text-lg font-bold text-slate-900">
                    Contact and Address
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    {[
                        [
                            'address_line_1',
                            'Address Line 1',
                        ],
                        [
                            'address_line_2',
                            'Address Line 2',
                        ],
                        ['city', 'City'],
                        ['postal_code', 'Postal Code'],
                        ['telephone', 'Telephone'],
                        ['email', 'Email Address'],
                    ].map(([field, label]) => (
                        <div key={field}>
                            <InputLabel
                                htmlFor={field}
                                value={label}
                            />

                            <TextInput
                                id={field}
                                type={
                                    field === 'email'
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
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <h2 className="text-lg font-bold text-slate-900">
                    Statistics and Status
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="student_count"
                            value="Student Count"
                        />

                        <TextInput
                            id="student_count"
                            type="number"
                            min="0"
                            value={data.student_count}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'student_count',
                                    event.target.value,
                                )
                            }
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="teacher_count"
                            value="Teacher Count"
                        />

                        <TextInput
                            id="teacher_count"
                            type="number"
                            min="0"
                            value={data.teacher_count}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'teacher_count',
                                    event.target.value,
                                )
                            }
                        />
                    </div>
                </div>

                <div className="mt-6 grid gap-4 md:grid-cols-2">
                    <label className="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <input
                            type="checkbox"
                            checked={
                                data.is_national_school
                            }
                            onChange={(event) =>
                                setData(
                                    'is_national_school',
                                    event.target.checked,
                                )
                            }
                            className="rounded border-gray-300 text-blue-600"
                        />

                        <span className="text-sm font-semibold text-slate-800">
                            National school
                        </span>
                    </label>

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
                            className="rounded border-gray-300 text-blue-600"
                        />

                        <span className="text-sm font-semibold text-slate-800">
                            Active school
                        </span>
                    </label>
                </div>
            </section>

            <div className="flex justify-end gap-3">
                <Link
                    href={route('admin.schools.index')}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update School'
                          : 'Create School'}
                </PrimaryButton>
            </div>
        </form>
    );
}
