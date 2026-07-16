import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import {
    Building2,
    GraduationCap,
    ShieldCheck,
} from 'lucide-react';

export default function Register({
    token,
    registry,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        token,
        name:
            registry.full_name ||
            registry.name_with_initials ||
            '',
        email: '',
        password: '',
        password_confirmation: '',
        declaration: false,
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'principal-registration.store',
            ),
        );
    };

    return (
        <GuestLayout>
            <Head title="Create Principal Account" />

            <div className="mb-7 text-center">
                <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white">
                    <GraduationCap className="h-7 w-7" />
                </div>

                <h1 className="mt-4 text-2xl font-bold text-slate-900">
                    Create Your Account
                </h1>

                <p className="mt-2 text-sm text-slate-500">
                    Your NIC has been verified successfully.
                </p>
            </div>

            <div className="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div className="flex gap-3">
                    <ShieldCheck className="h-5 w-5 shrink-0 text-emerald-600" />

                    <div className="text-sm">
                        <p className="font-semibold text-emerald-900">
                            Verified NIC: {registry.nic}
                        </p>

                        <p className="mt-1 text-emerald-700">
                            {registry.designation ||
                                'Principal service record'}
                        </p>

                        {registry.school && (
                            <p className="mt-1 flex items-center gap-2 text-emerald-700">
                                <Building2 className="h-4 w-4" />
                                {registry.school.name}
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {errors.registration && (
                <div className="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {errors.registration}
                </div>
            )}

            <form onSubmit={submit}>
                <div>
                    <InputLabel
                        htmlFor="name"
                        value="Full Name"
                    />

                    <TextInput
                        id="name"
                        value={data.name}
                        className="mt-1 block w-full"
                        isFocused
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
                </div>

                <div className="mt-5">
                    <InputLabel
                        htmlFor="email"
                        value="Email Address"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="email"
                        onChange={(event) =>
                            setData(
                                'email',
                                event.target.value,
                            )
                        }
                    />

                    <InputError
                        message={errors.email}
                        className="mt-2"
                    />
                </div>

                <div className="mt-5">
                    <InputLabel
                        htmlFor="password"
                        value="Password"
                    />

                    <TextInput
                        id="password"
                        type="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(event) =>
                            setData(
                                'password',
                                event.target.value,
                            )
                        }
                    />

                    <InputError
                        message={errors.password}
                        className="mt-2"
                    />
                </div>

                <div className="mt-5">
                    <InputLabel
                        htmlFor="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        value={
                            data.password_confirmation
                        }
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(event) =>
                            setData(
                                'password_confirmation',
                                event.target.value,
                            )
                        }
                    />
                </div>

                <label className="mt-5 flex items-start gap-3">
                    <Checkbox
                        checked={data.declaration}
                        onChange={(event) =>
                            setData(
                                'declaration',
                                event.target.checked,
                            )
                        }
                    />

                    <span className="text-xs leading-5 text-slate-600">
                        I confirm that the information provided is
                        accurate and that this NIC belongs to me.
                    </span>
                </label>

                <InputError
                    message={errors.declaration}
                    className="mt-2"
                />

                <PrimaryButton
                    className="mt-6 flex w-full justify-center"
                    disabled={processing}
                >
                    {processing
                        ? 'Creating Account...'
                        : 'Create Principal Account'}
                </PrimaryButton>
            </form>
        </GuestLayout>
    );
}
