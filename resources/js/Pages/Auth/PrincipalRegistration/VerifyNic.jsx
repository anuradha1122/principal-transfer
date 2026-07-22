import ApplicationLogo from '@/Components/ApplicationLogo';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import {
    Head,
    Link,
    useForm,
} from '@inertiajs/react';
import {
    BadgeCheck,
    LogIn,
} from 'lucide-react';

export default function VerifyNic() {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        nic: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'principal-registration.verify',
            ),
        );
    };

    return (
        <GuestLayout>
            <Head title="Verify NIC" />

            <div className="mb-8 text-center">
                <div className="flex justify-center">
                    <ApplicationLogo
                        className="h-24 w-24 object-contain"
                    />
                </div>

                <h1 className="mt-5 text-2xl font-bold text-slate-900">
                    Principal Registration
                </h1>

                <p className="mt-2 text-sm leading-6 text-slate-500">
                    Enter your NIC number to confirm that you are
                    eligible to register.
                </p>
            </div>

            <form onSubmit={submit}>
                <div>
                    <InputLabel
                        htmlFor="nic"
                        value="National Identity Card Number"
                    />

                    <TextInput
                        id="nic"
                        value={data.nic}
                        className="mt-1 block w-full uppercase"
                        autoComplete="off"
                        isFocused
                        onChange={(event) =>
                            setData(
                                'nic',
                                event.target.value
                                    .toUpperCase(),
                            )
                        }
                        placeholder="123456789V or 200012345678"
                    />

                    <InputError
                        message={errors.nic}
                        className="mt-2"
                    />
                </div>

                <div className="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <div className="flex gap-3">
                        <BadgeCheck className="mt-0.5 h-5 w-5 shrink-0 text-blue-600" />

                        <p className="text-xs leading-5 text-blue-800">
                            Registration is available only for NIC
                            numbers included in the official
                            principal registry.
                        </p>
                    </div>
                </div>

                <PrimaryButton
                    className="mt-6 flex w-full justify-center"
                    disabled={processing}
                >
                    {processing
                        ? 'Verifying...'
                        : 'Verify NIC'}
                </PrimaryButton>
            </form>

            <div className="mt-6 border-t border-slate-200 pt-5 text-center">
                <Link
                    href={route('login')}
                    className="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 transition hover:text-blue-800"
                >
                    <LogIn className="h-4 w-4" />

                    Already registered? Sign in
                </Link>
            </div>
        </GuestLayout>
    );
}
