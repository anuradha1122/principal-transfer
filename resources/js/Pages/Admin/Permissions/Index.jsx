import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    KeyRound,
    Trash2,
} from 'lucide-react';
import {
    router,
    useForm,
} from '@inertiajs/react';

export default function Index({ permissions }) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
    } = useForm({
        name: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('admin.permissions.store'), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const removePermission = (permission) => {
        if (
            !window.confirm(
                `Delete the permission "${permission.name}"?`,
            )
        ) {
            return;
        }

        router.delete(
            route(
                'admin.permissions.destroy',
                permission.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Permissions"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Permissions
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Maintain the individual capabilities assigned
                        to roles.
                    </p>
                </div>
            }
        >
            <div className="grid gap-6 xl:grid-cols-3">
                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        Create Permission
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        Use lowercase action-based names.
                    </p>

                    <form
                        onSubmit={submit}
                        className="mt-5"
                    >
                        <TextInput
                            value={data.name}
                            className="block w-full"
                            placeholder="example: approve appeals"
                            onChange={(event) =>
                                setData(
                                    'name',
                                    event.target.value,
                                )
                            }
                        />

                        <InputError
                            message={errors.name}
                            className="mt-2"
                        />

                        <PrimaryButton
                            className="mt-4"
                            disabled={processing}
                        >
                            {processing
                                ? 'Creating...'
                                : 'Create Permission'}
                        </PrimaryButton>
                    </form>
                </div>

                <div className="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                    <div className="border-b border-slate-200 px-6 py-5">
                        <h2 className="font-bold text-slate-900">
                            Available Permissions
                        </h2>
                    </div>

                    <div className="divide-y divide-slate-100">
                        {permissions.map((permission) => (
                            <div
                                key={permission.id}
                                className="flex items-center justify-between gap-4 px-6 py-4"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="rounded-lg bg-blue-50 p-2 text-blue-600">
                                        <KeyRound className="h-4 w-4" />
                                    </div>

                                    <div>
                                        <p className="font-medium text-slate-800">
                                            {permission.name}
                                        </p>

                                        <p className="text-xs text-slate-500">
                                            Used by{' '}
                                            {
                                                permission.roles_count
                                            }{' '}
                                            role(s)
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    disabled={
                                        permission.roles_count > 0
                                    }
                                    onClick={() =>
                                        removePermission(
                                            permission,
                                        )
                                    }
                                    className="rounded-lg p-2 text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-30"
                                >
                                    <Trash2 className="h-4 w-4" />
                                </button>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
