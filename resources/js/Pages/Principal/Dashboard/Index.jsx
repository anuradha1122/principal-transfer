import AdminLayout from '@/Layouts/AdminLayout';
import {
    CheckCircle2,
    FilePlus2,
    History,
    UserRound,
} from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function Dashboard({
    auth,
    profile,
    applications = [],
    openCycles = [],
}) {
    const latestApplication =
        applications?.[0] ?? null;

    return (
        <AdminLayout
            title="Principal Dashboard"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Principal Dashboard
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Manage your profile and transfer applications.
                    </p>
                </div>
            }
        >
            <section className="rounded-2xl bg-slate-950 p-8 text-white shadow-sm">
                <p className="text-sm font-medium text-blue-300">
                    Welcome
                </p>

                <h2 className="mt-2 text-3xl font-bold">
                    {auth?.user?.name}
                </h2>

                <p className="mt-4 max-w-2xl text-sm leading-7 text-slate-300">
                    From this portal, you can review your
                    principal profile, submit transfer
                    applications and track their progress.
                </p>
            </section>

            <div className="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <Link
                    href={route(
                        'principal.profile.show',
                    )}
                    className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                >
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <UserRound className="h-6 w-6" />
                    </div>

                    <h3 className="mt-5 font-bold text-slate-900">
                        My Profile
                    </h3>

                    <p className="mt-2 text-sm leading-6 text-slate-500">
                        Review and update your principal
                        profile information.
                    </p>

                    <span className="mt-5 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Available
                    </span>
                </Link>

                <Link
                    href={route(
                        'principal.transfer-applications.index',
                    )}
                    className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                >
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <FilePlus2 className="h-6 w-6" />
                    </div>

                    <h3 className="mt-5 font-bold text-slate-900">
                        New Application
                    </h3>

                    <p className="mt-2 text-sm leading-6 text-slate-500">
                        Start an application for an open
                        transfer cycle.
                    </p>

                    <span
                        className={[
                            'mt-5 inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                            openCycles.length > 0
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-amber-50 text-amber-700',
                        ].join(' ')}
                    >
                        {openCycles.length > 0
                            ? `${openCycles.length} open cycle(s)`
                            : 'No open cycles'}
                    </span>
                </Link>

                <Link
                    href={route(
                        'principal.transfer-applications.index',
                    )}
                    className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                >
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <History className="h-6 w-6" />
                    </div>

                    <h3 className="mt-5 font-bold text-slate-900">
                        Application Status
                    </h3>

                    <p className="mt-2 text-sm leading-6 text-slate-500">
                        Track submitted transfer applications
                        and current review stages.
                    </p>

                    <span className="mt-5 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {latestApplication
                            ? latestApplication.status
                            : 'No application'}
                    </span>
                </Link>

                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <CheckCircle2 className="h-6 w-6" />
                    </div>

                    <h3 className="mt-5 font-bold text-slate-900">
                        Final Decision
                    </h3>

                    <p className="mt-2 text-sm leading-6 text-slate-500">
                        View the final Transfer Board decision
                        when the review is completed.
                    </p>

                    <span className="mt-5 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {latestApplication &&
                        [
                            'Approved',
                            'Rejected',
                            'Waitlisted',
                        ].includes(
                            latestApplication.status,
                        )
                            ? latestApplication.status
                            : 'Pending'}
                    </span>
                </div>
            </div>
        </AdminLayout>
    );
}
