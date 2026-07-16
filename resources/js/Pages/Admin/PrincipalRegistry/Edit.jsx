import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import RegistryForm from './RegistryForm';

export default function Edit({
    registry,
    schools,
    designations,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        nic: registry.nic ?? '',
        full_name: registry.full_name ?? '',
        name_with_initials:
            registry.name_with_initials ?? '',
        school_id: registry.school_id ?? '',
        designation: registry.designation ?? '',
        employee_number:
            registry.employee_number ?? '',
        is_active: Boolean(registry.is_active),
        notes: registry.notes ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.principal-registry.update',
                registry.id,
            ),
        );
    };

    return (
        <AdminLayout
            title="Edit Principal Registry Record"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Registry Record
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update principal eligibility and
                        administrative information.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <RegistryForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    schools={schools}
                    designations={designations}
                    editing
                    registered={
                        registry.registered_user_id !== null
                    }
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
