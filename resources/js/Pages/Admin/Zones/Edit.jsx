import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import ZoneForm from './ZoneForm';

export default function Edit({ zone }) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        name: zone.name ?? '',
        code: zone.code ?? '',
        district: zone.district ?? '',
        office_address: zone.office_address ?? '',
        telephone: zone.telephone ?? '',
        email: zone.email ?? '',
        is_active: Boolean(zone.is_active),
        sort_order: zone.sort_order ?? 0,
    });

    const submit = (event) => {
        event.preventDefault();

        put(route('admin.zones.update', zone.id));
    };

    return (
        <AdminLayout
            title={`Edit ${zone.name}`}
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Education Zone
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update zone office and administrative
                        information.
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
                    editing
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
