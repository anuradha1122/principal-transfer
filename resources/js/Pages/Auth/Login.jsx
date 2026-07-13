import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { GraduationCap, LockKeyhole } from 'lucide-react';

export default function Login({
    status,
    canResetPassword,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
        reset,
    } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            <div className="mb-8 text-center">
                <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-200">
                    <GraduationCap className="h-8 w-8" />
                </div>

                <h1 className="mt-5 text-2xl font-bold text-slate-900">
                    Principal Transfer System
                </h1>

                <p className="mt-2 text-sm font-medium text-slate-600">
                    Provincial Department of Education
                </p>

                <p className="mt-1 text-sm text-slate-500">
                    Sabaragamuwa Province
                </p>
            </div>

            {status && (
                <div className="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div>
                    <InputLabel
                        htmlFor="email"
                        value="Email Address"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused
                        onChange={(event) =>
                            setData('email', event.target.value)
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
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(event) =>
                            setData('password', event.target.value)
                        }
                    />

                    <InputError
                        message={errors.password}
                        className="mt-2"
                    />
                </div>

                <div className="mt-5 flex items-center justify-between gap-4">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(event) =>
                                setData(
                                    'remember',
                                    event.target.checked,
                                )
                            }
                        />

                        <span className="ms-2 text-sm text-slate-600">
                            Remember me
                        </span>
                    </label>

                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="text-sm font-medium text-blue-600 transition hover:text-blue-800 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Forgot password?
                        </Link>
                    )}
                </div>

                <PrimaryButton
                    className="mt-6 flex w-full items-center justify-center gap-2"
                    disabled={processing}
                >
                    <LockKeyhole className="h-4 w-4" />

                    {processing
                        ? 'Signing in...'
                        : 'Sign In'}
                </PrimaryButton>
            </form>

            <div className="mt-8 border-t border-slate-200 pt-5 text-center">
                <p className="text-xs leading-5 text-slate-500">
                    Access is restricted to authorized officers and
                    registered principals.
                </p>

                <p className="mt-1 text-xs text-slate-400">
                    Principal registration will be available through
                    NIC verification.
                </p>
            </div>
        </GuestLayout>
    );
}
