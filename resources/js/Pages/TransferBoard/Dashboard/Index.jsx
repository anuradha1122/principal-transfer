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
    ChartNoAxesCombined,
    CircleX,
    ClipboardCheck,
    Clock3,
    FileChartColumn,
    FileClock,
    FileText,
    Gauge,
    ListChecks,
    Scale,
    ShieldCheck,
    TimerReset,
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

    slate:
        'bg-slate-100 text-slate-700',
};

export default function Index({
    summary = {},
    statusSummary = [],
    boardReviewQueue = [],
    waitlistedApplications = [],
    recentDecisions = [],
    appealSummary = [],
    appealQueue = [],
    documentSummary = [],
    recentDocuments = [],
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
            <Head title="Transfer Board Dashboard" />

            <div className="space-y-6">
                <DashboardHeader
                    eyebrow="Transfer Board Operations"
                    title="Transfer Board Dashboard"
                    description="Review Provincial recommendations, record final transfer decisions, manage waitlists and monitor appeal outcomes."
                    userName={user.name}
                    role="Transfer Board Member"
                    accent="board"
                    icon={ShieldCheck}
                    actionLabel="Review Applications"
                    actionHref={route(
                        'transfer-board.transfer-applications.index',
                    )}
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <DashboardStatCard
                        title="Total Applications"
                        value={
                            summary.total_applications
                        }
                        description="Transfer applications recorded across all workflow stages."
                        icon={ClipboardCheck}
                        tone="blue"
                    />

                    <DashboardStatCard
                        title="Awaiting Board Review"
                        value={
                            summary.awaiting_board_review
                        }
                        description="Applications requiring final Board evaluation."
                        icon={FileClock}
                        tone="amber"
                    />

                    <DashboardStatCard
                        title="Final Approved"
                        value={
                            summary.final_approved
                        }
                        description="Applications approved by the Transfer Board."
                        icon={BadgeCheck}
                        tone="emerald"
                    />

                    <DashboardStatCard
                        title="Final Rejected"
                        value={
                            summary.final_rejected
                        }
                        description="Applications rejected by the Transfer Board."
                        icon={CircleX}
                        tone="red"
                    />

                    <DashboardStatCard
                        title="Waitlisted"
                        value={
                            summary.waitlisted
                        }
                        description="Applications placed on the final waiting list."
                        icon={ListChecks}
                        tone="violet"
                    />

                    <DashboardStatCard
                        title="Appeals"
                        value={
                            summary.appeals
                        }
                        description={`${Number(
                            summary.pending_appeals
                            ?? 0,
                        ).toLocaleString()} appeals pending`}
                        icon={Scale}
                        tone="violet"
                    />

                    <DashboardStatCard
                        title="Published Documents"
                        value={
                            summary.published_documents
                        }
                        description="Transfer documents currently available to Principals."
                        icon={FileText}
                        tone="emerald"
                    />

                    <DashboardStatCard
                        title="Awaiting Publication"
                        value={
                            summary.unpublished_documents
                        }
                        description="Generated documents not yet publicly released."
                        icon={TimerReset}
                        tone="amber"
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Board Review Queue"
                        description="Oldest applications currently awaiting a final Board decision."
                        icon={FileClock}
                        actionLabel="Open review queue"
                        actionHref={route(
                            'transfer-board.transfer-applications.index',
                        )}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <BoardReviewQueue
                            applications={
                                boardReviewQueue
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Oldest Pending Case"
                        description="The application waiting longest for a final workflow outcome."
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
                                description="There are no unresolved applications requiring Board attention."
                                icon={BadgeCheck}
                            />
                        )}
                    </DashboardSection>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Application Status"
                        description="Current distribution of all transfer applications."
                        icon={ChartNoAxesCombined}
                        className="xl:col-span-2"
                    >
                        <StatusSummary
                            items={
                                statusSummary
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Appeal Status"
                        description="Current distribution of transfer appeal outcomes."
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
                    title="Quick Actions"
                    description="Common Transfer Board workflows and shortcuts."
                    icon={Gauge}
                >
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        {permissions.viewApplications && (
                            <QuickActionCard
                                title="Review Applications"
                                description="Open the final transfer decision queue."
                                href={route(
                                    'transfer-board.transfer-applications.index',
                                )}
                                icon={ClipboardCheck}
                                tone="amber"
                            />
                        )}

                        {permissions.viewAppeals && (
                            <QuickActionCard
                                title="Review Appeals"
                                description="Review submitted transfer appeals."
                                href={route(
                                    'transfer-board.transfer-appeals.index',
                                )}
                                icon={Scale}
                                tone="violet"
                            />
                        )}

                        {permissions.viewDocuments && (
                            <QuickActionCard
                                title="Transfer Documents"
                                description="Review generated transfer letters and decisions."
                                href={route(
                                    'admin.transfer-documents.index',
                                )}
                                icon={FileText}
                                tone="emerald"
                            />
                        )}

                        {permissions.viewReports && (
                            <QuickActionCard
                                title="Management Reports"
                                description="Open Board and Province-wide analytics."
                                href={route(
                                    'reports.index',
                                )}
                                icon={FileChartColumn}
                                tone="blue"
                            />
                        )}

                        <QuickActionCard
                            title="Notifications"
                            description="Review Board workflow notifications."
                            href={route(
                                'notifications.index',
                            )}
                            icon={Bell}
                            tone="cyan"
                        />
                    </div>
                </DashboardSection>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Recent Final Decisions"
                        description="Latest approved, rejected and waitlisted applications."
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
                        title="Waitlisted Applications"
                        description="Latest applications placed on the waiting list."
                        icon={ListChecks}
                        className="xl:col-span-1"
                        noPadding
                    >
                        <WaitlistedApplications
                            applications={
                                waitlistedApplications
                            }
                        />
                    </DashboardSection>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Appeal Review Queue"
                        description="Appeals currently awaiting review or final resolution."
                        icon={Scale}
                        actionLabel="View appeals"
                        actionHref={route(
                            'transfer-board.transfer-appeals.index',
                        )}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <AppealQueue
                            appeals={
                                appealQueue
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Document Publication"
                        description="Published and unpublished transfer documents."
                        icon={FileText}
                        className="xl:col-span-1"
                    >
                        <StatusSummary
                            items={
                                documentSummary
                            }
                        />
                    </DashboardSection>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Recent Transfer Documents"
                        description="Latest generated and published transfer documents."
                        icon={FileText}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <RecentDocuments
                            documents={
                                recentDocuments
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Notifications"
                        description="Recent Board workflow and appeal updates."
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

function BoardReviewQueue({
    applications = [],
}) {
    if (applications.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No applications awaiting Board review"
                    description="Provincially approved applications will appear here."
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
                                    <StatusBadge
                                        tone={
                                            application.status_tone
                                        }
                                    >
                                        {
                                            application.status_label
                                        }
                                    </StatusBadge>
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

                <p className="mt-1 text-xs font-semibold text-violet-700">
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
                    title="No final decisions"
                    description="Board approvals, rejections and waitlist decisions will appear here."
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
                        <DecisionIcon
                            tone={
                                decision.status_tone
                            }
                        />

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <p className="font-bold text-slate-900">
                                    {
                                        decision.application_number
                                    }
                                </p>

                                <StatusBadge
                                    tone={
                                        decision.status_tone
                                    }
                                >
                                    {
                                        decision.status_label
                                    }
                                </StatusBadge>
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

function WaitlistedApplications({
    applications = [],
}) {
    if (applications.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No waitlisted applications"
                    description="Applications placed on the waiting list will appear here."
                    icon={ListChecks}
                />
            </div>
        );
    }

    return (
        <div className="divide-y divide-slate-100">
            {applications.map(
                (application) => (
                    <Link
                        key={
                            application.id
                        }
                        href={
                            application.show_url
                        }
                        className="block px-5 py-4 transition hover:bg-slate-50"
                    >
                        <p className="font-bold text-blue-700">
                            {
                                application.application_number
                            }
                        </p>

                        <p className="mt-1 text-sm font-semibold text-slate-900">
                            {
                                application.principal_name
                            }
                        </p>

                        <p className="mt-1 text-xs text-slate-500">
                            {application.school_name}
                            {' • '}
                            {application.zone_name}
                        </p>

                        <p className="mt-2 text-[11px] font-semibold text-slate-400">
                            {
                                application.updated_at
                            }
                        </p>
                    </Link>
                ),
            )}
        </div>
    );
}

function AppealQueue({
    appeals = [],
}) {
    if (appeals.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No pending appeals"
                    description="Submitted appeals awaiting review will appear here."
                    icon={Scale}
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
                            Appeal
                        </TableHeading>

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
                    </tr>
                </thead>

                <tbody className="divide-y divide-slate-100 bg-white">
                    {appeals.map(
                        (appeal) => (
                            <tr
                                key={
                                    appeal.id
                                }
                                className="transition hover:bg-slate-50"
                            >
                                <td className="whitespace-nowrap px-5 py-4">
                                    <Link
                                        href={
                                            appeal.show_url
                                        }
                                        className="font-bold text-violet-700 hover:text-violet-900"
                                    >
                                        {
                                            appeal.appeal_number
                                        }
                                    </Link>
                                </td>

                                <td className="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-700">
                                    {
                                        appeal.application_number
                                    }
                                </td>

                                <td className="px-5 py-4 text-sm text-slate-700">
                                    {
                                        appeal.principal_name
                                    }
                                </td>

                                <td className="whitespace-nowrap px-5 py-4">
                                    <StatusBadge
                                        tone={
                                            appeal.status_tone
                                        }
                                    >
                                        {
                                            appeal.status_label
                                        }
                                    </StatusBadge>
                                </td>

                                <td className="whitespace-nowrap px-5 py-4 text-sm font-bold text-amber-700">
                                    {
                                        appeal.pending_days
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

function RecentDocuments({
    documents = [],
}) {
    if (documents.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No transfer documents"
                    description="Generated transfer documents will appear here."
                    icon={FileText}
                />
            </div>
        );
    }

    return (
        <div className="divide-y divide-slate-100">
            {documents.map(
                (document) => (
                    <Link
                        key={
                            document.id
                        }
                        href={
                            document.show_url
                        }
                        className="flex items-start gap-4 px-5 py-4 transition hover:bg-slate-50"
                    >
                        <div
                            className={[
                                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                                document.is_published
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-amber-50 text-amber-700',
                            ].join(' ')}
                        >
                            <FileText className="h-5 w-5" />
                        </div>

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <p className="font-bold text-slate-900">
                                    {
                                        document.title
                                    }
                                </p>

                                <span
                                    className={[
                                        'rounded-full px-2.5 py-1 text-xs font-bold',
                                        document.is_published
                                            ? badgeClasses.emerald
                                            : badgeClasses.amber,
                                    ].join(' ')}
                                >
                                    {document.is_published
                                        ? 'Published'
                                        : 'Not Published'}
                                </span>
                            </div>

                            <p className="mt-1 text-xs text-slate-500">
                                {
                                    document.document_number
                                }
                            </p>

                            <p className="mt-2 text-[11px] font-semibold text-slate-400">
                                {
                                    document.created_at
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

function DecisionIcon({
    tone,
}) {
    const classes =
        tone === 'emerald'
            ? 'bg-emerald-50 text-emerald-700'
            : tone === 'red'
                ? 'bg-red-50 text-red-700'
                : 'bg-violet-50 text-violet-700';

    return (
        <div
            className={[
                'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                classes,
            ].join(' ')}
        >
            {tone === 'emerald' ? (
                <BadgeCheck className="h-5 w-5" />
            ) : tone === 'red' ? (
                <CircleX className="h-5 w-5" />
            ) : (
                <ListChecks className="h-5 w-5" />
            )}
        </div>
    );
}

function StatusBadge({
    tone,
    children,
}) {
    return (
        <span
            className={[
                'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                badgeClasses[tone]
                ?? badgeClasses.blue,
            ].join(' ')}
        >
            {children}
        </span>
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
