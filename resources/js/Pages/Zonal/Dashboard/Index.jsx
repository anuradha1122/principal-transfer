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
    FileClock,
    FileSearch,
    Gauge,
    MapPinned,
    RotateCcw,
    School,
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
    zone = {},
    summary = {},
    statusSummary = [],
    pendingApplications = [],
    recentDecisions = [],
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
            <Head title="Zonal Dashboard" />

            <div className="space-y-6">
                <DashboardHeader
                    eyebrow="Zonal Transfer Management"
                    title={`${zone.name ?? 'Assigned Zone'} Dashboard`}
                    description="Review transfer applications, monitor pending workload and record Zonal decisions for your assigned Zone."
                    userName={
                        user.name
                    }
                    role="Zonal Director"
                    accent="zonal"
                    icon={MapPinned}
                    actionLabel="Review Applications"
                    actionHref={route(
                        'zonal.transfer-applications.index',
                    )}
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <DashboardStatCard
                        title="Zone Applications"
                        value={
                            summary.total_applications
                        }
                        description={`Applications originating from ${zone.name ?? 'this Zone'}.`}
                        icon={ClipboardCheck}
                        tone="blue"
                    />

                    <DashboardStatCard
                        title="Awaiting Review"
                        value={
                            summary.awaiting_zonal_review
                        }
                        description="Applications requiring Zonal review or action."
                        icon={FileClock}
                        tone="amber"
                    />

                    <DashboardStatCard
                        title="Zonal Approved"
                        value={
                            summary.zonal_approved
                        }
                        description="Applications approved at the Zonal stage."
                        icon={BadgeCheck}
                        tone="emerald"
                    />

                    <DashboardStatCard
                        title="Zonal Rejected"
                        value={
                            summary.zonal_rejected
                        }
                        description="Applications rejected at the Zonal stage."
                        icon={CircleX}
                        tone="red"
                    />

                    <DashboardStatCard
                        title="Returned to Zone"
                        value={
                            summary.returned_to_zone
                        }
                        description="Applications returned for further clarification."
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
                        title="Divisions"
                        value={
                            zone.division_count
                        }
                        description="Education Divisions configured under this Zone."
                        icon={Building2}
                        tone="cyan"
                    />

                    <DashboardStatCard
                        title="Schools"
                        value={
                            zone.school_count
                        }
                        description="Schools configured under the assigned Zone."
                        icon={School}
                        tone="indigo"
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Zone Information"
                        description="Assigned organizational scope for this account."
                        icon={MapPinned}
                        className="xl:col-span-1"
                    >
                        <ZoneInformation
                            zone={zone}
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Application Status"
                        description="Distribution of applications across workflow states."
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
                        title="Pending Review Queue"
                        description="Oldest applications currently awaiting Zonal attention."
                        icon={FileSearch}
                        actionLabel="Open review queue"
                        actionHref={route(
                            'zonal.transfer-applications.index',
                        )}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <PendingApplicationsTable
                            applications={
                                pendingApplications
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Oldest Pending Case"
                        description="Application waiting the longest within this Zone."
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
                                description="There are no unresolved applications in the current Zone scope."
                                icon={BadgeCheck}
                            />
                        )}
                    </DashboardSection>
                </div>

                <DashboardSection
                    title="Quick Actions"
                    description="Common Zonal Director tasks."
                    icon={Gauge}
                >
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {permissions.viewApplications && (
                            <QuickActionCard
                                title="Review Applications"
                                description="Open the Zone application queue."
                                href={route(
                                    'zonal.transfer-applications.index',
                                )}
                                icon={ClipboardCheck}
                                tone="amber"
                            />
                        )}

                        {permissions.viewReports && (
                            <QuickActionCard
                                title="Zone Reports"
                                description="Review scoped reports and management analytics."
                                href={route(
                                    'reports.index',
                                )}
                                icon={ChartNoAxesCombined}
                                tone="blue"
                            />
                        )}

                        <QuickActionCard
                            title="Notifications"
                            description="Review application and workflow notifications."
                            href={route(
                                'notifications.index',
                            )}
                            icon={Bell}
                            tone="violet"
                        />

                        <QuickActionCard
                            title="Zone Overview"
                            description="Review Zone workload and pending decisions."
                            href={route(
                                'zonal.dashboard',
                            )}
                            icon={MapPinned}
                            tone="emerald"
                        />
                    </div>
                </DashboardSection>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Recent Zonal Decisions"
                        description="Latest approvals and rejections recorded by the Zone."
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
                        title="Notifications"
                        description="Recent workflow updates and review alerts."
                        icon={Bell}
                        className="xl:col-span-1"
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
            </div>
        </AdminLayout>
    );
}

function ZoneInformation({
    zone,
}) {
    return (
        <div className="space-y-4">
            <div className="rounded-2xl bg-emerald-50 p-5">
                <MapPinned className="h-7 w-7 text-emerald-700" />

                <h3 className="mt-4 text-xl font-bold text-slate-900">
                    {zone.name ?? 'Assigned Zone'}
                </h3>

                {zone.code && (
                    <p className="mt-1 text-sm font-semibold text-emerald-700">
                        Zone Code: {zone.code}
                    </p>
                )}
            </div>

            <div className="grid grid-cols-2 gap-3">
                <InfoBox
                    label="Divisions"
                    value={
                        zone.division_count
                        ?? 0
                    }
                />

                <InfoBox
                    label="Schools"
                    value={
                        zone.school_count
                        ?? 0
                    }
                />
            </div>

            <p className="text-xs leading-5 text-slate-500">
                All dashboard totals and review queues are restricted to this assigned Zone.
            </p>
        </div>
    );
}

function PendingApplicationsTable({
    applications = [],
}) {
    if (applications.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No applications awaiting review"
                    description="Newly submitted or returned applications will appear here."
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
                            Status
                        </TableHeading>

                        <TableHeading>
                            Pending
                        </TableHeading>

                        <TableHeading>
                            Submitted
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

                                <td className="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500">
                                    {
                                        application.submitted_at
                                    }
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
                    description="Zonal approvals and rejections will appear here."
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
                                    : 'bg-red-50 text-red-700',
                            ].join(' ')}
                        >
                            {decision.status_tone
                            === 'emerald' ? (
                                <BadgeCheck className="h-5 w-5" />
                            ) : (
                                <CircleX className="h-5 w-5" />
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
                                {
                                    decision.school_name
                                }
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

function InfoBox({
    label,
    value,
}) {
    return (
        <div className="rounded-xl bg-slate-50 p-3">
            <p className="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                {label}
            </p>

            <p className="mt-1 text-lg font-bold text-slate-900">
                {Number(
                    value ?? 0,
                ).toLocaleString()}
            </p>
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
