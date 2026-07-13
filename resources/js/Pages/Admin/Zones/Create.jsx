import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import ZoneForm from './ZoneForm';

export default function Create() {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        name: '',
        code: '',
        district: '',
        office_address: '',
        telephone: '',
        email: '',
        is_active: true,
        sort_order: 0,
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('admin.zones.store'));
    };

    return (
        <AdminLayout
            title="Create Education Zone"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create Education Zone
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Add an education zone under Sabaragamuwa
                        Province.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <ZoneForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
