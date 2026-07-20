import AdminLayout from '@/Layouts/AdminLayout';
import { Link, usePage } from '@inertiajs/react';
import {
    BadgeCheck,
    ClipboardCheck,
    Clock3,
    FileSearch,
    Gavel,
    ListChecks,
    XCircle,
} from 'lucide-react';

function StatisticCard({
    label,
    value,
    description,
    icon: Icon,
    styles,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
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
                        'flex h-12 w-12 items-center justify-center rounded-xl',
                        styles,
                    ].join(' ')}
                >
                    <Icon className="h-6 w-6" />
                </div>
            </div>
        </div>
    );
}

export default function Index({
    summary = {},
    recentDecisions = [],
}) {
    const { auth } = usePage().props;

    const cards = [
        {
            label: 'Awaiting Review',
            value:
                summary.awaiting_review ?? 0,
            description:
                'Provincially approved applications awaiting Board review',
            icon: ClipboardCheck,
            styles:
                'bg-blue-50 text-blue-600',
        },
        {
            label: 'Under Review',
            value:
                summary.under_review ?? 0,
            description:
                'Applications currently before the Transfer Board',
            icon: Clock3,
            styles:
                'bg-amber-50 text-amber-600',
        },
        {
            label: 'Approved',
            value:
                summary.approved ?? 0,
            description:
                'Applications approved by the Board',
            icon: BadgeCheck,
            styles:
                'bg-emerald-50 text-emerald-600',
        },
        {
            label: 'Rejected',
            value:
                summary.rejected ?? 0,
            description:
                'Applications rejected by the Board',
            icon: XCircle,
            styles:
                'bg-red-50 text-red-600',
        },
        {
            label: 'Waitlisted',
            value:
                summary.waitlisted ?? 0,
            description:
                'Applications waiting for a suitable vacancy',
            icon: ListChecks,
            styles:
                'bg-indigo-50 text-indigo-600',
        },
    ];

    return (
        <AdminLayout
            title="Transfer Board Dashboard"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Transfer Board Dashboard
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Review Provincially approved applications and record final decisions.
                    </p>
                </div>
            }
        >
            <section className="rounded-2xl bg-gradient-to-r from-slate-950 via-indigo-950 to-blue-950 p-8 text-white shadow-lg">
                <div className="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <div className="flex items-center gap-2 text-sm font-semibold text-indigo-300">
                            <Gavel className="h-4 w-4" />
                            Final Transfer Decision
                        </div>

                        <h2 className="mt-3 text-3xl font-bold">
                            Welcome, {auth?.user?.name}
                        </h2>

                        <p className="mt-4 max-w-3xl text-sm leading-7 text-slate-300">
                            Review Zonal and Provincial recommendations, record the final Board decision and publish the transfer result.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'transfer-board.transfer-applications.index'
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-950 hover:bg-slate-100"
                    >
                        <FileSearch className="h-5 w-5" />
                        Open Board Queue
                    </Link>
                </div>
            </section>

            <div className="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-5">
                {cards.map((card) => (
                    <StatisticCard
                        key={card.label}
                        {...card}
                    />
                ))}
            </div>

            <section className="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="border-b border-slate-200 px-6 py-5">
                    <h3 className="text-lg font-bold text-slate-900">
                        Recent Board Decisions
                    </h3>

                    <p className="mt-1 text-sm text-slate-500">
                        Latest final decisions recorded by the Transfer Board
                    </p>
                </div>

                <div className="divide-y divide-slate-100">
                    {recentDecisions.length > 0 ? (
                        recentDecisions.map(
                            (decision) => (
                                <div
                                    key={decision.id}
                                    className="grid gap-3 px-6 py-4 sm:grid-cols-[1fr_auto] sm:items-center"
                                >
                                    <div>
                                        <p className="font-bold text-slate-900">
                                            {
                                                decision
                                                    .transfer_application
                                                    ?.application_number
                                            }
                                        </p>

                                        <p className="mt-1 text-sm text-slate-500">
                                            {
                                                decision
                                                    .transfer_application
                                                    ?.principal_name
                                            }
                                            {' • '}
                                            {
                                                decision
                                                    .transfer_application
                                                    ?.origin_zone
                                                    ?.name
                                            }
                                        </p>
                                    </div>

                                    <span className="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                        {decision.decision}
                                    </span>
                                </div>
                            )
                        )
                    ) : (
                        <p className="px-6 py-10 text-center text-sm text-slate-500">
                            No final Board decisions have been recorded.
                        </p>
                    )}
                </div>
            </section>
        </AdminLayout>
    );
}
