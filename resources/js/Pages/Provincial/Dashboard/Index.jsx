import DashboardHeader from '@/Components/Dashboard/DashboardHeader';
import DashboardSection from '@/Components/Dashboard/DashboardSection';
import DashboardStatCard from '@/Components/Dashboard/DashboardStatCard';
import EmptyDashboardState from '@/Components/Dashboard/EmptyDashboardState';
import NotificationPreview from '@/Components/Dashboard/NotificationPreview';
import QuickActionCard from '@/Components/Dashboard/QuickActionCard';
import StatusSummary from '@/Components/Dashboard/StatusSummary';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Head,
    Link,
    usePage,
} from '@inertiajs/react';
import {
    ArrowRight,
    BadgeCheck,
    Bell,
    Building2,
    ChartNoAxesCombined,
    CircleX,
    ClipboardCheck,
    Clock3,
    FileChartColumn,
    FileClock,
    Gauge,
    History,
    RotateCcw,
    Scale,
    ShieldCheck,
} from 'lucide-react';

const badgeClasses = {
    blue:
        'bg-blue-50 text-blue-700',

    emerald:
        'bg-emerald-50 text-emerald-700',

    amber:
        'bg-amber-50 text-amber-700',

    red:
        'bg-red-50 text-red-700',

    violet:
        'bg-violet-50 text-violet-700',
};

export default function Index({
    summary = {},
    statusSummary = [],
    zoneSummary = [],
    reviewQueue = [],
    recentDecisions = [],
    appealSummary = [],
    oldestPending = null,
    recentNotifications = [],
    unreadNotificationCount = 0,
    permissions = {},
}) {
    const { auth } = usePage().props;

    const user =
        auth?.user ?? {};

    return (
        <AdminLayout>
            <Head title="Provincial Dashboard" />

            <div className="space-y-6">
                <DashboardHeader
                    eyebrow="Provincial Transfer Management"
                    title="Provincial Director Dashboard"
                    description="Monitor Province-wide transfer applications, review Zonal recommendations and manage Provincial decisions."
                    userName={
                        user.name
                    }
                    role="Provincial Director"
                    accent="provincial"
                    icon={ShieldCheck}
                    actionLabel="Review Applications"
                    actionHref={route(
                        'provincial.transfer-applications.index',
                    )}
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <DashboardStatCard
                        title="Province Applications"
                        value={
                            summary.total_applications
                        }
                        description="All transfer applications submitted across the Province."
                        icon={ClipboardCheck}
                        tone="blue"
                    />

                    <DashboardStatCard
                        title="Awaiting Review"
                        value={
                            summary.awaiting_provincial_review
                        }
                        description="Applications requiring Provincial review."
                        icon={FileClock}
                        tone="amber"
                    />

                    <DashboardStatCard
                        title="Provincial Approved"
                        value={
                            summary.provincial_approved
                        }
                        description="Applications approved at Provincial stage."
                        icon={BadgeCheck}
                        tone="emerald"
                    />

                    <DashboardStatCard
                        title="Provincial Rejected"
                        value={
                            summary.provincial_rejected
                        }
                        description="Applications rejected at Provincial stage."
                        icon={CircleX}
                        tone="red"
                    />

                    <DashboardStatCard
                        title="Returned to Zone"
                        value={
                            summary.returned_to_zone
                        }
                        description="Applications returned for Zone clarification."
                        icon={RotateCcw}
                        tone="violet"
                    />

                    <DashboardStatCard
                        title="Pending Workflow"
                        value={
                            summary.pending_applications
                        }
                        description="Applications without a final workflow outcome."
                        icon={Clock3}
                        tone="amber"
                    />

                    <DashboardStatCard
                        title="Appeals"
                        value={
                            summary.appeals
                        }
                        description={`${Number(
                            summary.pending_appeals
                            ?? 0,
                        ).toLocaleString()} pending appeals`}
                        icon={Scale}
                        tone="violet"
                    />

                    <DashboardStatCard
                        title="Pending Appeals"
                        value={
                            summary.pending_appeals
                        }
                        description="Appeals still awaiting review or decision."
                        icon={History}
                        tone="indigo"
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Applications by Zone"
                        description="Application volumes across Provincial Zones."
                        icon={Building2}
                        className="xl:col-span-1"
                    >
                        <ZoneSummary
                            items={
                                zoneSummary
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Application Status"
                        description="Province-wide workflow status distribution."
                        icon={ChartNoAxesCombined}
                        className="xl:col-span-2"
                    >
                        <StatusSummary
                            items={
                                statusSummary
                            }
                        />
                    </DashboardSection>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Provincial Review Queue"
                        description="Oldest applications currently awaiting Provincial action."
                        icon={FileClock}
                        actionLabel="Open review queue"
                        actionHref={route(
                            'provincial.transfer-applications.index',
                        )}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <ReviewQueueTable
                            applications={
                                reviewQueue
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Oldest Pending Case"
                        description="The longest unresolved Province-wide application."
                        icon={Clock3}
                        className="xl:col-span-1"
                    >
                        {oldestPending ? (
                            <OldestPendingCard
                                application={
                                    oldestPending
                                }
                            />
                        ) : (
                            <EmptyDashboardState
                                title="No pending cases"
                                description="There are no unresolved Provincial applications."
                                icon={BadgeCheck}
                            />
                        )}
                    </DashboardSection>
                </div>

                <DashboardSection
                    title="Quick Actions"
                    description="Common Provincial Director tasks."
                    icon={Gauge}
                >
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        {permissions.viewApplications && (
                            <QuickActionCard
                                title="Review Applications"
                                description="Open the Provincial application review queue."
                                href={route(
                                    'provincial.transfer-applications.index',
                                )}
                                icon={ClipboardCheck}
                                tone="amber"
                            />
                        )}

                        {permissions.viewAppeals && (
                            <QuickActionCard
                                title="Review Appeals"
                                description="Open the transfer appeal review queue."
                                href={route(
                                    'transfer-board.transfer-appeals.index',
                                )}
                                icon={Scale}
                                tone="violet"
                            />
                        )}

                        {permissions.viewReports && (
                            <QuickActionCard
                                title="Management Reports"
                                description="Review Province-wide transfer analytics."
                                href={route(
                                    'reports.index',
                                )}
                                icon={FileChartColumn}
                                tone="blue"
                            />
                        )}

                        <QuickActionCard
                            title="Notifications"
                            description="Review workflow and decision notifications."
                            href={route(
                                'notifications.index',
                            )}
                            icon={Bell}
                            tone="cyan"
                        />

                        {permissions.viewAuditLogs && (
                            <QuickActionCard
                                title="Audit Logs"
                                description="Review administrative activity records."
                                href={route(
                                    'admin.audit-logs.index',
                                )}
                                icon={ShieldCheck}
                                tone="slate"
                            />
                        )}
                    </div>
                </DashboardSection>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Recent Provincial Decisions"
                        description="Latest Provincial approvals, rejections and returns."
                        icon={ShieldCheck}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <RecentDecisions
                            decisions={
                                recentDecisions
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Appeal Status"
                        description="Current distribution of transfer appeals."
                        icon={Scale}
                        className="xl:col-span-1"
                    >
                        <StatusSummary
                            items={
                                appealSummary
                            }
                        />
                    </DashboardSection>
                </div>

                <DashboardSection
                    title="Notifications"
                    description="Recent Provincial workflow updates."
                    icon={Bell}
                >
                    <NotificationPreview
                        notifications={
                            recentNotifications
                        }
                        unreadCount={
                            unreadNotificationCount
                        }
                    />
                </DashboardSection>
            </div>
        </AdminLayout>
    );
}

function ZoneSummary({
    items = [],
}) {
    const maximum = Math.max(
        ...items.map(
            (item) =>
                Number(
                    item.total
                    ?? 0,
                ),
        ),
        1,
    );

    if (items.length === 0) {
        return (
            <EmptyDashboardState
                title="No Zone data"
                description="Zone application totals will appear after submissions are recorded."
                icon={Building2}
            />
        );
    }

    return (
        <div className="space-y-4">
            {items.map(
                (item) => {
                    const percentage =
                        (
                            Number(
                                item.total
                                ?? 0,
                            )
                            / maximum
                        )
                        * 100;

                    return (
                        <div
                            key={
                                item.zone_id
                                ?? item.zone_name
                            }
                        >
                            <div className="mb-2 flex items-center justify-between gap-3">
                                <span className="truncate text-sm font-semibold text-slate-700">
                                    {
                                        item.zone_name
                                    }
                                </span>

                                <span className="text-sm font-bold text-slate-900">
                                    {Number(
                                        item.total
                                        ?? 0,
                                    ).toLocaleString()}
                                </span>
                            </div>

                            <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    className="h-full rounded-full bg-indigo-600"
                                    style={{
                                        width: `${percentage}%`,
                                    }}
                                />
                            </div>
                        </div>
                    );
                },
            )}
        </div>
    );
}

function ReviewQueueTable({
    applications = [],
}) {
    if (applications.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No applications awaiting review"
                    description="Applications approved by Zones will appear here."
                    icon={BadgeCheck}
                />
            </div>
        );
    }

    return (
        <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-slate-200">
                <thead className="bg-slate-50">
                    <tr>
                        <TableHeading>
                            Application
                        </TableHeading>

                        <TableHeading>
                            Principal
                        </TableHeading>

                        <TableHeading>
                            Zone
                        </TableHeading>

                        <TableHeading>
                            Status
                        </TableHeading>

                        <TableHeading>
                            Pending
                        </TableHeading>
                    </tr>
                </thead>

                <tbody className="divide-y divide-slate-100 bg-white">
                    {applications.map(
                        (application) => (
                            <tr
                                key={
                                    application.id
                                }
                                className="transition hover:bg-slate-50"
                            >
                                <td className="whitespace-nowrap px-5 py-4">
                                    <Link
                                        href={
                                            application.show_url
                                        }
                                        className="font-bold text-blue-700 hover:text-blue-900"
                                    >
                                        {
                                            application.application_number
                                        }
                                    </Link>
                                </td>

                                <td className="px-5 py-4">
                                    <p className="text-sm font-semibold text-slate-900">
                                        {
                                            application.principal_name
                                        }
                                    </p>

                                    <p className="mt-1 text-xs text-slate-500">
                                        {
                                            application.school_name
                                        }
                                    </p>
                                </td>

                                <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {
                                        application.zone_name
                                    }
                                </td>

                                <td className="whitespace-nowrap px-5 py-4">
                                    <span
                                        className={[
                                            'rounded-full px-2.5 py-1 text-xs font-bold',
                                            badgeClasses[
                                                application.status_tone
                                            ]
                                            ?? badgeClasses.blue,
                                        ].join(' ')}
                                    >
                                        {
                                            application.status_label
                                        }
                                    </span>
                                </td>

                                <td className="whitespace-nowrap px-5 py-4 text-sm font-bold text-amber-700">
                                    {
                                        application.pending_days
                                    }{' '}
                                    days
                                </td>
                            </tr>
                        ),
                    )}
                </tbody>
            </table>
        </div>
    );
}

function OldestPendingCard({
    application,
}) {
    return (
        <div className="space-y-4">
            <div className="rounded-2xl bg-amber-50 p-5">
                <Clock3 className="h-7 w-7 text-amber-700" />

                <Link
                    href={
                        application.show_url
                    }
                    className="mt-4 block text-lg font-bold text-blue-700 hover:text-blue-900"
                >
                    {
                        application.application_number
                    }
                </Link>

                <p className="mt-2 text-sm font-semibold text-slate-900">
                    {
                        application.principal_name
                    }
                </p>

                <p className="mt-1 text-xs text-slate-500">
                    {
                        application.school_name
                    }
                </p>

                <p className="mt-1 text-xs font-semibold text-indigo-600">
                    {
                        application.zone_name
                    }
                </p>

                <div className="mt-4 rounded-xl bg-white p-3">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Pending Duration
                    </p>

                    <p className="mt-1 text-2xl font-bold text-amber-700">
                        {
                            application.pending_days
                        }{' '}
                        days
                    </p>
                </div>
            </div>

            <Link
                href={
                    application.show_url
                }
                className="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900"
            >
                Review application

                <ArrowRight className="h-4 w-4" />
            </Link>
        </div>
    );
}

function RecentDecisions({
    decisions = [],
}) {
    if (decisions.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No recent decisions"
                    description="Provincial approvals, rejections and returns will appear here."
                    icon={ShieldCheck}
                />
            </div>
        );
    }

    return (
        <div className="divide-y divide-slate-100">
            {decisions.map(
                (decision) => (
                    <Link
                        key={
                            decision.id
                        }
                        href={
                            decision.show_url
                        }
                        className="flex items-start gap-4 px-5 py-4 transition hover:bg-slate-50"
                    >
                        <div
                            className={[
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                                decision.status_tone
                                === 'emerald'
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : decision.status_tone
                                    === 'red'
                                        ? 'bg-red-50 text-red-700'
                                        : 'bg-violet-50 text-violet-700',
                            ].join(' ')}
                        >
                            {decision.status_tone
                            === 'emerald' ? (
                                <BadgeCheck className="h-5 w-5" />
                            ) : decision.status_tone
                            === 'red' ? (
                                <CircleX className="h-5 w-5" />
                            ) : (
                                <RotateCcw className="h-5 w-5" />
                            )}
                        </div>

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <p className="font-bold text-slate-900">
                                    {
                                        decision.application_number
                                    }
                                </p>

                                <span
                                    className={[
                                        'rounded-full px-2.5 py-1 text-xs font-bold',
                                        badgeClasses[
                                            decision.status_tone
                                        ]
                                        ?? badgeClasses.blue,
                                    ].join(' ')}
                                >
                                    {
                                        decision.status_label
                                    }
                                </span>
                            </div>

                            <p className="mt-1 text-sm text-slate-600">
                                {
                                    decision.principal_name
                                }
                            </p>

                            <p className="mt-1 text-xs text-slate-500">
                                {decision.school_name}
                                {' • '}
                                {decision.zone_name}
                            </p>

                            <p className="mt-2 text-[11px] font-semibold text-slate-400">
                                {
                                    decision.decided_at
                                }
                            </p>
                        </div>

                        <ArrowRight className="mt-3 h-4 w-4 shrink-0 text-slate-300" />
                    </Link>
                ),
            )}
        </div>
    );
}

function TableHeading({
    children,
}) {
    return (
        <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
            {children}
        </th>
    );
}
