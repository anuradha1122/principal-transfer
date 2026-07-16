import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import RegistryForm from './RegistryForm';

export default function Create({
    schools,
    designations,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        nic: '',
        full_name: '',
        name_with_initials: '',
        school_id: '',
        designation: '',
        employee_number: '',
        is_active: true,
        notes: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'admin.principal-registry.store',
            ),
        );
    };

    return (
        <AdminLayout
            title="Create Principal Registry Record"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create Registry Record
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Add an NIC that is eligible for controlled
                        principal registration.
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
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
