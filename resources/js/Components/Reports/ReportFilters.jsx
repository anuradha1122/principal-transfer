import { router } from '@inertiajs/react';
import { RotateCcw, Search } from 'lucide-react';
import { useState } from 'react';

const emptyFilters = {
    transfer_cycle_id: '',
    zone_id: '',
    status: '',
    transfer_reason: '',
    service_grade: '',
    current_designation: '',
    date_from: '',
    date_to: '',
};

export default function ReportFilters({
    filters = {},
    options = {},
}) {
    const [form, setForm] = useState({
        ...emptyFilters,
        ...filters,
    });

    const updateField = (field, value) => {
        setForm((current) => ({
            ...current,
            [field]: value,
        }));
    };

    const submit = (event) => {
        event.preventDefault();

        const query = Object.fromEntries(
            Object.entries(form).filter(
                ([, value]) =>
                    value !== ''
                    && value !== null
                    && value !== undefined,
            ),
        );

        router.get(
            route('reports.index'),
            query,
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    const reset = () => {
        setForm(emptyFilters);

        router.get(
            route('reports.index'),
            {},
            {
                preserveState: false,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    return (
        <form
            onSubmit={submit}
            className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        >
            <div className="mb-5">
                <h2 className="text-base font-bold text-slate-900">
                    Report Filters
                </h2>

                <p className="mt-1 text-sm text-slate-500">
                    Narrow the analytics by cycle, Zone, status and submission period.
                </p>
            </div>

            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <FilterSelect
                    label="Transfer Cycle"
                    value={form.transfer_cycle_id}
                    onChange={(value) =>
                        updateField(
                            'transfer_cycle_id',
                            value,
                        )
                    }
                    options={(options.cycles ?? []).map(
                        (cycle) => ({
                            value: cycle.id,
                            label: cycle.name,
                        }),
                    )}
                />

                <FilterSelect
                    label="Zone"
                    value={form.zone_id}
                    onChange={(value) =>
                        updateField(
                            'zone_id',
                            value,
                        )
                    }
                    options={(options.zones ?? []).map(
                        (zone) => ({
                            value: zone.id,
                            label: zone.name,
                        }),
                    )}
                />

                <FilterSelect
                    label="Application Status"
                    value={form.status}
                    onChange={(value) =>
                        updateField(
                            'status',
                            value,
                        )
                    }
                    options={
                        options.statuses ?? []
                    }
                />

                <FilterSelect
                    label="Transfer Reason"
                    value={form.transfer_reason}
                    onChange={(value) =>
                        updateField(
                            'transfer_reason',
                            value,
                        )
                    }
                    options={(
                        options.transfer_reasons
                        ?? []
                    ).map((value) => ({
                        value,
                        label: value,
                    }))}
                />

                <FilterSelect
                    label="Service Grade"
                    value={form.service_grade}
                    onChange={(value) =>
                        updateField(
                            'service_grade',
                            value,
                        )
                    }
                    options={(
                        options.service_grades
                        ?? []
                    ).map((value) => ({
                        value,
                        label: value,
                    }))}
                />

                <FilterSelect
                    label="Designation"
                    value={
                        form.current_designation
                    }
                    onChange={(value) =>
                        updateField(
                            'current_designation',
                            value,
                        )
                    }
                    options={(
                        options.designations
                        ?? []
                    ).map((value) => ({
                        value,
                        label: value,
                    }))}
                />

                <FilterInput
                    label="Submitted From"
                    type="date"
                    value={form.date_from}
                    onChange={(value) =>
                        updateField(
                            'date_from',
                            value,
                        )
                    }
                />

                <FilterInput
                    label="Submitted To"
                    type="date"
                    value={form.date_to}
                    onChange={(value) =>
                        updateField(
                            'date_to',
                            value,
                        )
                    }
                />
            </div>

            <div className="mt-5 flex flex-wrap justify-end gap-3">
                <button
                    type="button"
                    onClick={reset}
                    className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    <RotateCcw className="h-4 w-4" />
                    Reset
                </button>

                <button
                    type="submit"
                    className="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                >
                    <Search className="h-4 w-4" />
                    Apply Filters
                </button>
            </div>
        </form>
    );
}

function FilterSelect({
    label,
    value,
    onChange,
    options,
}) {
    return (
        <label className="block">
            <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                {label}
            </span>

            <select
                value={value ?? ''}
                onChange={(event) =>
                    onChange(event.target.value)
                }
                className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">
                    All
                </option>

                {options.map((option) => (
                    <option
                        key={`${label}-${option.value}`}
                        value={option.value}
                    >
                        {option.label}
                    </option>
                ))}
            </select>
        </label>
    );
}

function FilterInput({
    label,
    type,
    value,
    onChange,
}) {
    return (
        <label className="block">
            <span className="mb-1.5 block text-sm font-semibold text-slate-700">
                {label}
            </span>

            <input
                type={type}
                value={value ?? ''}
                onChange={(event) =>
                    onChange(event.target.value)
                }
                className="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
        </label>
    );
}
