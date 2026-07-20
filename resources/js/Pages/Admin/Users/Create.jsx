import AdminLayout from '@/Layouts/AdminLayout';
import UserForm from './UserForm';
import { useForm } from '@inertiajs/react';

export default function Create({
    roles = [],
    zones = [],
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '',
        assigned_zone_id: '',
        is_active: true,
        email_verified: true,
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route('admin.users.store')
        );
    };

    return (
        <AdminLayout
            title="Create User"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create User
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Create an officer account, assign its
                        system role and organizational office.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <UserForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    roles={roles}
                    zones={zones}
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
