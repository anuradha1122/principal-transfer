import AdminLayout from '@/Layouts/AdminLayout';
import { Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    BadgeCheck,
    Building2,
    CheckCircle2,
    ClipboardCheck,
    Clock3,
    FileSearch,
    MapPinned,
    ShieldCheck,
    XCircle,
} from 'lucide-react';

function StatisticCard({
    label,
    value,
    description,
    icon: Icon,
    iconClassName,
    iconBackgroundClassName,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-sm font-semibold text-slate-600">
                        {label}
                    </p>

                    <p className="mt-3 text-3xl font-bold text-slate-950">
                        {value ?? 0}
                    </p>

                    <p className="mt-2 text-xs leading-5 text-slate-500">
                        {description}
                    </p>
                </div>

                <div
                    className={[
                        'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
                        iconBackgroundClassName,
                        iconClassName,
                    ].join(' ')}
                >
                    <Icon className="h-6 w-6" />
                </div>
            </div>
        </div>
    );
}

function WorkflowStep({
    number,
    title,
    description,
    active = false,
    completed = false,
    isLast = false,
}) {
    return (
        <div className="relative flex gap-4">
            {!isLast && (
                <div className="absolute left-4 top-9 h-[calc(100%-8px)] w-px bg-slate-200" />
            )}

            <div
                className={[
                    'relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                    completed
                        ? 'bg-emerald-600 text-white'
                        : active
                          ? 'bg-blue-600 text-white'
                          : 'bg-slate-100 text-slate-500',
                ].join(' ')}
            >
                {completed ? (
                    <CheckCircle2 className="h-4 w-4" />
                ) : (
                    number
                )}
            </div>

            <div className="pb-6">
                <h4
                    className={[
                        'text-sm font-bold',
                        active
                            ? 'text-blue-700'
                            : 'text-slate-900',
                    ].join(' ')}
                >
                    {title}
                </h4>

                <p className="mt-1 text-xs leading-5 text-slate-500">
                    {description}
                </p>
            </div>
        </div>
    );
}

export default function Index({
    zone,
    summary = {},
}) {
    const { auth } = usePage().props;

    const statistics = [
        {
            label: 'Awaiting Review',
            value: summary.submitted ?? 0,
            description:
                'Submitted applications waiting for Zonal review',
            icon: ClipboardCheck,
            iconClassName: 'text-blue-600',
            iconBackgroundClassName: 'bg-blue-50',
        },
        {
            label: 'Under Review',
            value: summary.under_review ?? 0,
            description:
                'Applications currently being assessed',
            icon: Clock3,
            iconClassName: 'text-amber-600',
            iconBackgroundClassName: 'bg-amber-50',
        },
        {
            label: 'Zonal Approved',
            value: summary.approved ?? 0,
            description:
                'Applications recommended by the Zone',
            icon: BadgeCheck,
            iconClassName: 'text-emerald-600',
            iconBackgroundClassName: 'bg-emerald-50',
        },
        {
            label: 'Zonal Rejected',
            value: summary.rejected ?? 0,
            description:
                'Applications rejected during Zonal review',
            icon: XCircle,
            iconClassName: 'text-red-600',
            iconBackgroundClassName: 'bg-red-50',
        },
    ];

    const totalApplications =
        Number(summary.submitted ?? 0) +
        Number(summary.under_review ?? 0) +
        Number(summary.approved ?? 0) +
        Number(summary.rejected ?? 0);

    return (
        <AdminLayout
            title="Zonal Dashboard"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Zonal Dashboard
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Review and manage principal transfer
                        applications assigned to your Zone.
                    </p>
                </div>
            }
        >
            <section className="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white shadow-lg">
                <div className="grid gap-8 px-6 py-8 lg:grid-cols-[1fr_auto] lg:items-center lg:px-8">
                    <div>
                        <div className="flex items-center gap-2 text-sm font-semibold text-blue-300">
                            <MapPinned className="h-4 w-4" />

                            <span>
                                {zone?.name
                                    ? `${zone.name} Zone`
                                    : 'Provincial Zone Access'}
                            </span>
                        </div>

                        <h2 className="mt-3 text-3xl font-bold">
                            Welcome, {auth?.user?.name}
                        </h2>

                        <p className="mt-4 max-w-3xl text-sm leading-7 text-slate-300">
                            Review submitted principal transfer
                            applications, record recommendations
                            and forward eligible applications to
                            the Provincial Director.
                        </p>

                        <div className="mt-6 flex flex-wrap gap-3">
                            <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-slate-100">
                                <ShieldCheck className="h-4 w-4 text-blue-300" />

                                Zone-restricted access
                            </div>

                            {zone?.code && (
                                <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-slate-100">
                                    <Building2 className="h-4 w-4 text-blue-300" />

                                    Zone Code: {zone.code}
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="rounded-2xl border border-white/10 bg-white/10 px-7 py-6 text-center backdrop-blur-sm">
                        <p className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-200">
                            Total Applications
                        </p>

                        <p className="mt-2 text-4xl font-bold">
                            {totalApplications}
                        </p>

                        <p className="mt-2 text-xs text-slate-300">
                            In the current Zonal queue
                        </p>
                    </div>
                </div>
            </section>

            <div className="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                {statistics.map((statistic) => (
                    <StatisticCard
                        key={statistic.label}
                        {...statistic}
                    />
                ))}
            </div>

            <div className="mt-8 grid gap-6 xl:grid-cols-3">
                <section className="xl:col-span-2">
                    <div className="rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 className="text-lg font-bold text-slate-900">
                                    Transfer Review Queue
                                </h3>

                                <p className="mt-1 text-sm text-slate-500">
                                    Open the Zone queue and review
                                    submitted applications.
                                </p>
                            </div>

                            <Link
                                href={route(
                                    'zonal.transfer-applications.index',
                                )}
                                className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                <FileSearch className="h-5 w-5" />

                                Open Review Queue
                            </Link>
                        </div>

                        <div className="grid gap-4 p-6 md:grid-cols-2">
                            <Link
                                href={route(
                                    'zonal.transfer-applications.index',
                                    {
                                        status: 'Submitted',
                                    },
                                )}
                                className="group rounded-2xl border border-slate-200 p-5 transition hover:border-blue-200 hover:bg-blue-50/40"
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                        <ClipboardCheck className="h-5 w-5" />
                                    </div>

                                    <ArrowRight className="h-5 w-5 text-slate-300 transition group-hover:translate-x-1 group-hover:text-blue-600" />
                                </div>

                                <h4 className="mt-5 font-bold text-slate-900">
                                    Awaiting Review
                                </h4>

                                <p className="mt-2 text-sm leading-6 text-slate-500">
                                    View newly submitted
                                    applications that have not yet
                                    entered Zonal review.
                                </p>

                                <span className="mt-4 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {summary.submitted ?? 0}{' '}
                                    application(s)
                                </span>
                            </Link>

                            <Link
                                href={route(
                                    'zonal.transfer-applications.index',
                                    {
                                        status: 'Zonal Review',
                                    },
                                )}
                                className="group rounded-2xl border border-slate-200 p-5 transition hover:border-amber-200 hover:bg-amber-50/40"
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                        <Clock3 className="h-5 w-5" />
                                    </div>

                                    <ArrowRight className="h-5 w-5 text-slate-300 transition group-hover:translate-x-1 group-hover:text-amber-600" />
                                </div>

                                <h4 className="mt-5 font-bold text-slate-900">
                                    Reviews in Progress
                                </h4>

                                <p className="mt-2 text-sm leading-6 text-slate-500">
                                    Continue reviewing applications
                                    that are already assigned for
                                    assessment.
                                </p>

                                <span className="mt-4 inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                    {summary.under_review ?? 0}{' '}
                                    application(s)
                                </span>
                            </Link>
                        </div>
                    </div>
                </section>

                <aside className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <ClipboardCheck className="h-5 w-5" />
                        </div>

                        <div>
                            <h3 className="font-bold text-slate-900">
                                Review Workflow
                            </h3>

                            <p className="mt-1 text-xs text-slate-500">
                                Application approval stages
                            </p>
                        </div>
                    </div>

                    <div className="mt-7">
                        <WorkflowStep
                            number={1}
                            title="Principal Submission"
                            description="The Principal completes and submits the transfer application."
                            completed
                        />

                        <WorkflowStep
                            number={2}
                            title="Zonal Review"
                            description="The Zonal Director checks the application and records a recommendation."
                            active
                        />

                        <WorkflowStep
                            number={3}
                            title="Provincial Review"
                            description="Approved Zonal applications proceed to Provincial assessment."
                        />

                        <WorkflowStep
                            number={4}
                            title="Transfer Board Decision"
                            description="The Transfer Board records the final transfer result."
                            isLast
                        />
                    </div>
                </aside>
            </div>

            <section className="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-5">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm">
                        <ShieldCheck className="h-5 w-5" />
                    </div>

                    <div>
                        <h3 className="font-bold text-slate-900">
                            Access restricted to{' '}
                            {zone?.name
                                ? `${zone.name} Zone`
                                : 'the assigned Zone'}
                        </h3>

                        <p className="mt-1 text-sm leading-6 text-slate-600">
                            You can only view and review transfer
                            applications submitted from schools
                            belonging to your assigned Zone.
                        </p>
                    </div>
                </div>
            </section>
        </AdminLayout>
    );
}
