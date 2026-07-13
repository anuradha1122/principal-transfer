import AdminLayout from '@/Layouts/AdminLayout';
import RoleForm from './RoleForm';
import { useForm } from '@inertiajs/react';

export default function Create({
    permissionGroups,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        name: '',
        permissions: [],
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('admin.roles.store'));
    };

    return (
        <AdminLayout
            title="Create Role"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create Role
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Define a role and its system permissions.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <RoleForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    permissionGroups={permissionGroups}
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
