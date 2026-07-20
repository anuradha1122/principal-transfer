import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AdminLayout from '@/Layouts/AdminLayout';
import UserForm from './UserForm';
import { useForm } from '@inertiajs/react';

export default function Edit({
    account,
    roles = [],
    zones = [],
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        name: account.name,
        email: account.email,
        role: account.role ?? '',
        assigned_zone_id:
            account.assigned_zone_id ?? '',
        is_active: Boolean(
            account.is_active
        ),
        email_verified: Boolean(
            account.email_verified
        ),
    });

    const passwordForm = useForm({
        password: '',
        password_confirmation: '',
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.users.update',
                account.id
            )
        );
    };

    const resetPassword = (event) => {
        event.preventDefault();

        passwordForm.put(
            route(
                'admin.users.reset-password',
                account.id
            ),
            {
                preserveScroll: true,

                onSuccess: () =>
                    passwordForm.reset(),
            }
        );
    };

    return (
        <AdminLayout
            title="Edit User"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit User
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update account access, role assignment
                        and organizational office.
                    </p>
                </div>
            }
        >
            <div className="space-y-6">
                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <UserForm
                        data={data}
                        setData={setData}
                        errors={errors}
                        processing={processing}
                        roles={roles}
                        zones={zones}
                        editing
                        onSubmit={submit}
                    />
                </div>

                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Reset Password
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        Set a temporary password for this user.
                    </p>

                    <form
                        onSubmit={resetPassword}
                        className="mt-5 grid gap-5 md:grid-cols-2"
                    >
                        <div>
                            <InputLabel
                                htmlFor="password"
                                value="New Password"
                            />

                            <TextInput
                                id="password"
                                type="password"
                                value={
                                    passwordForm.data
                                        .password
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    passwordForm.setData(
                                        'password',
                                        event.target.value
                                    )
                                }
                            />

                            <InputError
                                message={
                                    passwordForm.errors
                                        .password
                                }
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="password_confirmation"
                                value="Confirm New Password"
                            />

                            <TextInput
                                id="password_confirmation"
                                type="password"
                                value={
                                    passwordForm.data
                                        .password_confirmation
                                }
                                className="mt-1 block w-full"
                                onChange={(event) =>
                                    passwordForm.setData(
                                        'password_confirmation',
                                        event.target.value
                                    )
                                }
                            />

                            <InputError
                                message={
                                    passwordForm.errors
                                        .password_confirmation
                                }
                                className="mt-2"
                            />
                        </div>

                        <div className="md:col-span-2">
                            <PrimaryButton
                                disabled={
                                    passwordForm.processing
                                }
                            >
                                {passwordForm.processing
                                    ? 'Resetting...'
                                    : 'Reset Password'}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
