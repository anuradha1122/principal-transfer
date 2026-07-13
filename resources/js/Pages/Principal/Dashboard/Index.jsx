import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import {
    CircleCheckBig,
    FilePlus2,
    History,
    UserRound,
} from 'lucide-react';

export default function Index() {
    const { auth } = usePage().props;

    const cards = [
        {
            title: 'My Profile',
            description:
                'Complete and review your principal profile.',
            icon: UserRound,
        },
        {
            title: 'New Application',
            description:
                'Create a principal transfer application.',
            icon: FilePlus2,
        },
        {
            title: 'Application Status',
            description:
                'Track the current approval stage.',
            icon: History,
        },
        {
            title: 'Final Decision',
            description:
                'View the Transfer Board result.',
            icon: CircleCheckBig,
        },
    ];

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Principal Dashboard
                </h2>
            }
        >
            <Head title="Principal Dashboard" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="rounded-2xl bg-slate-950 p-7 text-white">
                        <p className="text-sm text-blue-300">
                            Welcome
                        </p>

                        <h1 className="mt-2 text-2xl font-bold">
                            {auth.user.name}
                        </h1>

                        <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                            From this portal, you will be able to submit
                            and track your principal transfer
                            application.
                        </p>
                    </div>

                    <div className="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                        {cards.map((card) => {
                            const Icon = card.icon;

                            return (
                                <div
                                    key={card.title}
                                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                                >
                                    <div className="inline-flex rounded-xl bg-blue-50 p-3 text-blue-600">
                                        <Icon className="h-6 w-6" />
                                    </div>

                                    <h2 className="mt-4 font-bold text-slate-900">
                                        {card.title}
                                    </h2>

                                    <p className="mt-2 text-sm leading-6 text-slate-500">
                                        {card.description}
                                    </p>

                                    <span className="mt-4 inline-block rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Coming soon
                                    </span>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
