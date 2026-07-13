import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import DivisionForm from './DivisionForm';

export default function Create({ zones }) {
    const query = new URLSearchParams(
        window.location.search,
    );

    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        zone_id: query.get('zone_id') ?? '',
        name: '',
        code: '',
        office_address: '',
        telephone: '',
        email: '',
        is_active: true,
        sort_order: 0,
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('admin.divisions.store'));
    };

    return (
        <AdminLayout
            title="Create Education Division"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create Education Division
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Add a division under an education zone.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <DivisionForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    zones={zones}
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
