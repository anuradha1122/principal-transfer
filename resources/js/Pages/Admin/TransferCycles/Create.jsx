import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import CycleForm from './CycleForm';

export default function Create({ options }) {
    const currentYear =
        new Date().getFullYear();

    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        name: '',
        code: '',
        transfer_type: 'Annual',
        transfer_year: currentYear,
        application_open_date: '',
        application_close_date: '',
        effective_from_date: '',
        minimum_service_years: 3,
        maximum_preferences: 3,
        allow_same_zone_preferences: true,
        allow_other_zone_preferences: true,
        allow_withdrawal: true,
        status: 'Draft',
        instructions: '',
        eligibility_notes: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'admin.transfer-cycles.store',
            ),
        );
    };

    return (
        <AdminLayout
            title="Create Transfer Cycle"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create Transfer Cycle
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Configure an application period and eligibility rules.
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
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
