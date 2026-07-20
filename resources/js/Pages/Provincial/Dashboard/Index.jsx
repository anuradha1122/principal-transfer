import AdminLayout from '@/Layouts/AdminLayout';
import { Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    BadgeCheck,
    ClipboardCheck,
    Clock3,
    FileSearch,
    Landmark,
    RotateCcw,
    ShieldCheck,
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
    zoneSummary = [],
}) {
    const { auth } = usePage().props;

    const cards = [
        {
            label: 'Awaiting Review',
            value:
                summary.awaiting_review ?? 0,
            description:
                'Zonal-approved applications awaiting Provincial review',
            icon: ClipboardCheck,
            styles:
                'bg-blue-50 text-blue-600',
        },
        {
            label: 'Under Review',
            value:
                summary.under_review ?? 0,
            description:
                'Applications currently under Provincial assessment',
            icon: Clock3,
            styles:
                'bg-amber-50 text-amber-600',
        },
        {
            label: 'Approved',
            value:
                summary.approved ?? 0,
            description:
                'Applications recommended to the Transfer Board',
            icon: BadgeCheck,
            styles:
                'bg-emerald-50 text-emerald-600',
        },
        {
            label: 'Rejected',
            value:
                summary.rejected ?? 0,
            description:
                'Applications rejected at Provincial level',
            icon: XCircle,
            styles:
                'bg-red-50 text-red-600',
        },
        {
            label: 'Returned to Zone',
            value:
                summary.returned_to_zone ?? 0,
            description:
                'Applications returned for clarification',
            icon: RotateCcw,
            styles:
                'bg-violet-50 text-violet-600',
        },
    ];

    return (
        <AdminLayout
            title="Provincial Dashboard"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Provincial Dashboard
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Review principal transfer recommendations from all Zones.
                    </p>
                </div>
            }
        >
            <section className="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-violet-950 to-indigo-950 text-white shadow-lg">
                <div className="grid gap-8 px-6 py-8 lg:grid-cols-[1fr_auto] lg:items-center lg:px-8">
                    <div>
                        <div className="flex items-center gap-2 text-sm font-semibold text-violet-300">
                            <Landmark className="h-4 w-4" />
                            Provincial Department of Education
                        </div>

                        <h2 className="mt-3 text-3xl font-bold">
                            Welcome, {auth?.user?.name}
                        </h2>

                        <p className="mt-4 max-w-3xl text-sm leading-7 text-slate-300">
                            Review Zonal recommendations, record Provincial decisions and forward approved applications to the Transfer Board.
                        </p>

                        <div className="mt-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold">
                            <ShieldCheck className="h-4 w-4 text-violet-300" />
                            Province-wide access
                        </div>
                    </div>

                    <Link
                        href={route(
                            'provincial.transfer-applications.index'
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-slate-950 shadow-sm transition hover:bg-slate-100"
                    >
                        <FileSearch className="h-5 w-5" />
                        Open Review Queue
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

            <div className="mt-8 grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                    <div className="border-b border-slate-200 px-6 py-5">
                        <h3 className="text-lg font-bold text-slate-900">
                            Applications by Zone
                        </h3>

                        <p className="mt-1 text-sm text-slate-500">
                            Provincial review workload across all Zones
                        </p>
                    </div>

                    <div className="grid gap-4 p-6 sm:grid-cols-2">
                        {zoneSummary.length > 0 ? (
                            zoneSummary.map((item) => (
                                <div
                                    key={
                                        item.code
                                        ?? item.zone
                                    }
                                    className="rounded-2xl border border-slate-200 p-5"
                                >
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <p className="font-bold text-slate-900">
                                                {item.zone}
                                            </p>

                                            <p className="mt-1 text-xs text-slate-500">
                                                {item.code
                                                    ?? 'Zone'}
                                            </p>
                                        </div>

                                        <span className="rounded-full bg-violet-50 px-3 py-1 text-sm font-bold text-violet-700">
                                            {item.total}
                                        </span>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-sm text-slate-500">
                                No Provincial applications are currently available.
                            </p>
                        )}
                    </div>
                </section>

                <aside className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 className="text-lg font-bold text-slate-900">
                        Provincial Workflow
                    </h3>

                    <div className="mt-6 space-y-4">
                        {[
                            'Review Zonal recommendation',
                            'Check application and attachments',
                            'Approve, reject or return',
                            'Forward approved cases to Board',
                        ].map((stage, index) => (
                            <div
                                key={stage}
                                className="flex items-center gap-3"
                            >
                                <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-violet-600 text-xs font-bold text-white">
                                    {index + 1}
                                </div>

                                <span className="text-sm font-medium text-slate-700">
                                    {stage}
                                </span>

                                {index < 3 && (
                                    <ArrowRight className="ml-auto h-4 w-4 text-slate-300" />
                                )}
                            </div>
                        ))}
                    </div>
                </aside>
            </div>
        </AdminLayout>
    );
}
