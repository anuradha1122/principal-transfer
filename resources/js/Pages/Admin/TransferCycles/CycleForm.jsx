import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function CycleForm({
    data,
    setData,
    errors,
    processing,
    options,
    editing = false,
    onSubmit,
}) {
    return (
        <form
            onSubmit={onSubmit}
            className="space-y-8"
        >
            <section>
                <h2 className="text-lg font-bold text-slate-900">
                    Cycle Information
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="name"
                            value="Cycle Name"
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
                            htmlFor="code"
                            value="Cycle Code"
                        />

                        <TextInput
                            id="code"
                            value={data.code}
                            className="mt-1 block w-full uppercase"
                            onChange={(event) =>
                                setData(
                                    'code',
                                    event.target.value
                                        .toUpperCase(),
                                )
                            }
                        />

                        <InputError
                            message={errors.code}
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="transfer_type"
                            value="Transfer Type"
                        />

                        <select
                            id="transfer_type"
                            value={data.transfer_type}
                            onChange={(event) =>
                                setData(
                                    'transfer_type',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-md border-gray-300"
                        >
                            {options.transferTypes.map(
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
                            htmlFor="transfer_year"
                            value="Transfer Year"
                        />

                        <TextInput
                            id="transfer_year"
                            type="number"
                            min="2020"
                            max="2100"
                            value={data.transfer_year}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'transfer_year',
                                    event.target.value,
                                )
                            }
                        />
                    </div>
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <h2 className="text-lg font-bold text-slate-900">
                    Application Period
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-3">
                    {[
                        [
                            'application_open_date',
                            'Application Open Date',
                        ],
                        [
                            'application_close_date',
                            'Application Close Date',
                        ],
                        [
                            'effective_from_date',
                            'Transfer Effective Date',
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
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <h2 className="text-lg font-bold text-slate-900">
                    Eligibility and Preferences
                </h2>

                <div className="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel
                            htmlFor="minimum_service_years"
                            value="Minimum Years at Current School"
                        />

                        <TextInput
                            id="minimum_service_years"
                            type="number"
                            min="0"
                            max="50"
                            value={data.minimum_service_years}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'minimum_service_years',
                                    event.target.value,
                                )
                            }
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="maximum_preferences"
                            value="Maximum School Preferences"
                        />

                        <TextInput
                            id="maximum_preferences"
                            type="number"
                            min="1"
                            max="10"
                            value={data.maximum_preferences}
                            className="mt-1 block w-full"
                            onChange={(event) =>
                                setData(
                                    'maximum_preferences',
                                    event.target.value,
                                )
                            }
                        />
                    </div>
                </div>

                <div className="mt-6 grid gap-4 md:grid-cols-3">
                    {[
                        [
                            'allow_same_zone_preferences',
                            'Allow preferences within current zone',
                        ],
                        [
                            'allow_other_zone_preferences',
                            'Allow preferences in other zones',
                        ],
                        [
                            'allow_withdrawal',
                            'Allow application withdrawal',
                        ],
                    ].map(([field, label]) => (
                        <label
                            key={field}
                            className="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4"
                        >
                            <input
                                type="checkbox"
                                checked={data[field]}
                                onChange={(event) =>
                                    setData(
                                        field,
                                        event.target.checked,
                                    )
                                }
                                className="mt-1 rounded border-gray-300 text-blue-600"
                            />

                            <span className="text-sm font-semibold text-slate-700">
                                {label}
                            </span>
                        </label>
                    ))}
                </div>
            </section>

            <section className="border-t border-slate-200 pt-8">
                <div>
                    <InputLabel
                        htmlFor="status"
                        value="Cycle Status"
                    />

                    <select
                        id="status"
                        value={data.status}
                        onChange={(event) =>
                            setData(
                                'status',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 md:w-1/2"
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

                <div className="mt-6">
                    <InputLabel
                        htmlFor="instructions"
                        value="Application Instructions"
                    />

                    <textarea
                        id="instructions"
                        rows="6"
                        value={data.instructions}
                        onChange={(event) =>
                            setData(
                                'instructions',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300"
                    />
                </div>

                <div className="mt-6">
                    <InputLabel
                        htmlFor="eligibility_notes"
                        value="Eligibility Notes"
                    />

                    <textarea
                        id="eligibility_notes"
                        rows="5"
                        value={data.eligibility_notes}
                        onChange={(event) =>
                            setData(
                                'eligibility_notes',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300"
                    />
                </div>
            </section>

            <div className="flex justify-end gap-3">
                <Link
                    href={route(
                        'admin.transfer-cycles.index',
                    )}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Cycle'
                          : 'Create Cycle'}
                </PrimaryButton>
            </div>
        </form>
    );
}
