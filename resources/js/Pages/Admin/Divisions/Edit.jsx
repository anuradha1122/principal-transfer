import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import DivisionForm from './DivisionForm';

export default function Edit({
    division,
    zones,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        zone_id: division.zone_id ?? '',
        name: division.name ?? '',
        code: division.code ?? '',
        office_address: division.office_address ?? '',
        telephone: division.telephone ?? '',
        email: division.email ?? '',
        is_active: Boolean(division.is_active),
        sort_order: division.sort_order ?? 0,
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.divisions.update',
                division.id,
            ),
        );
    };

    return (
        <AdminLayout
            title={`Edit ${division.name}`}
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Education Division
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Update division information and zone
                        assignment.
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
                    editing
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
