import AdminLayout from '@/Layouts/AdminLayout';
import RoleForm from './RoleForm';
import { useForm } from '@inertiajs/react';

export default function Edit({
    role,
    permissionGroups,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        name: role.name,
        permissions: role.permissions,
    });

    const submit = (event) => {
        event.preventDefault();

        put(route('admin.roles.update', role.id));
    };

    return (
        <AdminLayout
            title={`Edit ${role.name}`}
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Role
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update role permissions and access.
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
                    editing
                    isSystem={role.is_system}
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
