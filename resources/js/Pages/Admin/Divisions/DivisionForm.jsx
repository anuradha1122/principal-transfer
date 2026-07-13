import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link } from '@inertiajs/react';

export default function DivisionForm({
    data,
    setData,
    errors,
    processing,
    zones,
    editing = false,
    onSubmit,
}) {
    return (
        <form
            onSubmit={onSubmit}
            className="space-y-6"
        >
            <div className="grid gap-6 md:grid-cols-2">
                <div>
                    <InputLabel
                        htmlFor="zone_id"
                        value="Education Zone"
                    />

                    <select
                        id="zone_id"
                        value={data.zone_id}
                        onChange={(event) =>
                            setData(
                                'zone_id',
                                event.target.value,
                            )
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="">
                            Select education zone
                        </option>

                        {zones.map((zone) => (
                            <option
                                key={zone.id}
                                value={zone.id}
                            >
                                {zone.name} - {zone.district}
                            </option>
                        ))}
                    </select>

                    <InputError
                        message={errors.zone_id}
                        className="mt-2"
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="name"
                        value="Division Name"
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
                        value="Division Code"
                    />

                    <TextInput
                        id="code"
                        value={data.code}
                        className="mt-1 block w-full uppercase"
                        onChange={(event) =>
                            setData(
                                'code',
                                event.target.value
                                    .toUpperCase()
                                    .replace(/\s+/g, '-'),
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
                        htmlFor="sort_order"
                        value="Display Order"
                    />

                    <TextInput
                        id="sort_order"
                        type="number"
                        min="0"
                        value={data.sort_order}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'sort_order',
                                Number(event.target.value),
                            )
                        }
                    />

                    <InputError
                        message={errors.sort_order}
                        className="mt-2"
                    />
                </div>

                <div className="md:col-span-2">
                    <InputLabel
                        htmlFor="office_address"
                        value="Office Address"
                    />

                    <TextInput
                        id="office_address"
                        value={data.office_address}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'office_address',
                                event.target.value,
                            )
                        }
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="telephone"
                        value="Telephone"
                    />

                    <TextInput
                        id="telephone"
                        value={data.telephone}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'telephone',
                                event.target.value,
                            )
                        }
                    />
                </div>

                <div>
                    <InputLabel
                        htmlFor="email"
                        value="Email Address"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        onChange={(event) =>
                            setData(
                                'email',
                                event.target.value,
                            )
                        }
                    />
                </div>
            </div>

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
                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />

                <span className="text-sm font-semibold text-slate-800">
                    Active division
                </span>
            </label>

            <div className="flex justify-end gap-3">
                <Link
                    href={route('admin.divisions.index')}
                    className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                >
                    Cancel
                </Link>

                <PrimaryButton disabled={processing}>
                    {processing
                        ? 'Saving...'
                        : editing
                          ? 'Update Division'
                          : 'Create Division'}
                </PrimaryButton>
            </div>
        </form>
    );
}
