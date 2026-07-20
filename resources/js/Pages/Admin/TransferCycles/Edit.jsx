import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import CycleForm from './CycleForm';

function dateValue(value) {
    return value
        ? value.substring(0, 10)
        : '';
}

export default function Edit({
    cycle,
    options,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        name: cycle.name ?? '',
        code: cycle.code ?? '',
        transfer_type:
            cycle.transfer_type ?? 'Annual',
        transfer_year:
            cycle.transfer_year ?? '',
        application_open_date:
            dateValue(
                cycle.application_open_date,
            ),
        application_close_date:
            dateValue(
                cycle.application_close_date,
            ),
        effective_from_date:
            dateValue(
                cycle.effective_from_date,
            ),
        minimum_service_years:
            cycle.minimum_service_years ?? 3,
        maximum_preferences:
            cycle.maximum_preferences ?? 3,
        allow_same_zone_preferences:
            Boolean(
                cycle.allow_same_zone_preferences,
            ),
        allow_other_zone_preferences:
            Boolean(
                cycle.allow_other_zone_preferences,
            ),
        allow_withdrawal:
            Boolean(
                cycle.allow_withdrawal,
            ),
        status: cycle.status ?? 'Draft',
        instructions:
            cycle.instructions ?? '',
        eligibility_notes:
            cycle.eligibility_notes ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.transfer-cycles.update',
                cycle.id,
            ),
        );
    };

    return (
        <AdminLayout
            title="Edit Transfer Cycle"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Transfer Cycle
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update dates, rules and publication status.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <CycleForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    options={options}
                    editing
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
