import DashboardHeader from '@/Components/Dashboard/DashboardHeader';
import DashboardSection from '@/Components/Dashboard/DashboardSection';
import DashboardStatCard from '@/Components/Dashboard/DashboardStatCard';
import EmptyDashboardState from '@/Components/Dashboard/EmptyDashboardState';
import QuickActionCard from '@/Components/Dashboard/QuickActionCard';
import StatusSummary from '@/Components/Dashboard/StatusSummary';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Head,
    Link,
    usePage,
} from '@inertiajs/react';
import {
    Activity,
    AlertTriangle,
    ArrowRight,
    BadgeCheck,
    BellRing,
    Building2,
    CalendarRange,
    ChartNoAxesCombined,
    CheckCircle2,
    CircleX,
    ClipboardList,
    FileChartColumn,
    FileClock,
    FileText,
    Gauge,
    History,
    Map,
    School,
    ShieldCheck,
    UserRoundCheck,
    Users,
} from 'lucide-react';

const alertToneClasses = {
    blue: {
        container:
            'border-blue-200 bg-blue-50',
        icon:
            'bg-blue-100 text-blue-700',
        title:
            'text-blue-900',
        description:
            'text-blue-700',
    },

    amber: {
        container:
            'border-amber-200 bg-amber-50',
        icon:
            'bg-amber-100 text-amber-700',
        title:
            'text-amber-900',
        description:
            'text-amber-700',
    },

    red: {
        container:
            'border-red-200 bg-red-50',
        icon:
            'bg-red-100 text-red-700',
        title:
            'text-red-900',
        description:
            'text-red-700',
    },

    violet: {
        container:
            'border-violet-200 bg-violet-50',
        icon:
            'bg-violet-100 text-violet-700',
        title:
            'text-violet-900',
        description:
            'text-violet-700',
    },
};

const statusBadgeClasses = {
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
    activeCycle = null,
    statusSummary = [],
    zoneSummary = [],
    recentApplications = [],
    recentUsers = [],
    recentAuditLogs = [],
    systemAlerts = [],
    quickActionPermissions = {},
}) {
    const { auth } = usePage().props;

    const user =
        auth?.user ?? {};

    const roles =
        auth?.roles ?? [];

    return (
        <AdminLayout>
            <Head title="Admin Dashboard" />

            <div className="space-y-6">
                <DashboardHeader
                    eyebrow="System Administration"
                    title={`Welcome back, ${
                        user.name
                        ?? 'Administrator'
                    }`}
                    description="Monitor transfer operations, user activity, workflow progress and organizational data from one place."
                    userName={
                        user.name
                    }
                    role={
                        roles[0]
                        ?? 'Super Admin'
                    }
                    accent="admin"
                    icon={
                        Gauge
                    }
                    actionLabel="View Applications"
                    actionHref={route(
                        'admin.transfer-applications.index',
                    )}
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <DashboardStatCard
                        title="Total Users"
                        value={
                            summary.total_users
                        }
                        description={`${Number(
                            summary.active_users
                            ?? 0,
                        ).toLocaleString()} active accounts`}
                        icon={Users}
                        tone="blue"
                    />

                    <DashboardStatCard
                        title="Principal Profiles"
                        value={
                            summary.principal_profiles
                        }
                        description="Registered Principal profiles in the system."
                        icon={UserRoundCheck}
                        tone="cyan"
                    />

                    <DashboardStatCard
                        title="Zones"
                        value={
                            summary.zones
                        }
                        description={`${Number(
                            summary.schools
                            ?? 0,
                        ).toLocaleString()} schools configured`}
                        icon={Map}
                        tone="indigo"
                    />

                    <DashboardStatCard
                        title="Active Cycles"
                        value={
                            summary.active_cycles
                        }
                        description="Transfer cycles currently available."
                        icon={CalendarRange}
                        tone={
                            Number(
                                summary.active_cycles
                                ?? 0,
                            ) > 0
                                ? 'emerald'
                                : 'red'
                        }
                    />

                    <DashboardStatCard
                        title="Applications"
                        value={
                            summary.total_applications
                        }
                        description="Total transfer applications recorded."
                        icon={ClipboardList}
                        tone="slate"
                    />

                    <DashboardStatCard
                        title="Pending"
                        value={
                            summary.pending_applications
                        }
                        description="Applications still moving through the workflow."
                        icon={FileClock}
                        tone="amber"
                    />

                    <DashboardStatCard
                        title="Final Approved"
                        value={
                            summary.final_approved
                        }
                        description="Applications approved at the final decision stage."
                        icon={CheckCircle2}
                        tone="emerald"
                    />

                    <DashboardStatCard
                        title="Final Rejected"
                        value={
                            summary.final_rejected
                        }
                        description={`${Number(
                            summary.appeals
                            ?? 0,
                        ).toLocaleString()} appeals recorded`}
                        icon={CircleX}
                        tone="red"
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Current Transfer Cycle"
                        description="The latest active transfer cycle and application period."
                        icon={CalendarRange}
                        className="xl:col-span-1"
                    >
                        {activeCycle ? (
                            <div className="space-y-5">
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-[0.15em] text-slate-400">
                                        Active Cycle
                                    </p>

                                    <h3 className="mt-2 text-xl font-bold text-slate-900">
                                        {
                                            activeCycle.name
                                        }
                                    </h3>

                                    <span className="mt-3 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-700">
                                        {
                                            activeCycle.status
                                            ?? 'Active'
                                        }
                                    </span>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <CycleDate
                                        label="Application Opens"
                                        value={
                                            activeCycle.application_start_date
                                        }
                                    />

                                    <CycleDate
                                        label="Application Closes"
                                        value={
                                            activeCycle.application_end_date
                                        }
                                    />
                                </div>

                                <Link
                                    href={route(
                                        'admin.transfer-cycles.index',
                                    )}
                                    className="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900"
                                >
                                    Manage transfer cycles

                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            </div>
                        ) : (
                            <EmptyDashboardState
                                title="No active transfer cycle"
                                description="Create or activate a transfer cycle before applications are accepted."
                                actionLabel="Manage Cycles"
                                actionHref={route(
                                    'admin.transfer-cycles.index',
                                )}
                                icon={CalendarRange}
                            />
                        )}
                    </DashboardSection>

                    <DashboardSection
                        title="Application Status"
                        description="Current distribution of transfer applications by workflow status."
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
                        title="Applications by Zone"
                        description="Highest application volumes across configured Zones."
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
                        title="Recent Applications"
                        description="Latest transfer applications entered into the system."
                        icon={ClipboardList}
                        actionLabel="View all"
                        actionHref={route(
                            'admin.transfer-applications.index',
                        )}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <RecentApplicationsTable
                            applications={
                                recentApplications
                            }
                        />
                    </DashboardSection>
                </div>

                {systemAlerts.length > 0 && (
                    <DashboardSection
                        title="System Attention"
                        description="Items that may require administrative review."
                        icon={BellRing}
                    >
                        <div className="grid gap-4 md:grid-cols-2">
                            {systemAlerts.map(
                                (alert) => (
                                    <SystemAlert
                                        key={
                                            alert.id
                                        }
                                        alert={
                                            alert
                                        }
                                    />
                                ),
                            )}
                        </div>
                    </DashboardSection>
                )}

                <DashboardSection
                    title="Quick Actions"
                    description="Common administrative tasks and operational shortcuts."
                    icon={Gauge}
                >
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                        {quickActionPermissions.manageUsers && (
                            <QuickActionCard
                                title="Manage Users"
                                description="Create accounts and manage role assignments."
                                href={route(
                                    'admin.users.index',
                                )}
                                icon={Users}
                                tone="blue"
                            />
                        )}

                        {quickActionPermissions.viewRegistry && (
                            <QuickActionCard
                                title="Principal Registry"
                                description="Manage NIC-controlled Principal registration records."
                                href={route(
                                    'admin.principal-registry.index',
                                )}
                                icon={UserRoundCheck}
                                tone="cyan"
                            />
                        )}

                        {quickActionPermissions.manageCycles && (
                            <QuickActionCard
                                title="Transfer Cycles"
                                description="Configure application periods and workflow cycles."
                                href={route(
                                    'admin.transfer-cycles.index',
                                )}
                                icon={CalendarRange}
                                tone="emerald"
                            />
                        )}

                        {quickActionPermissions.viewApplications && (
                            <QuickActionCard
                                title="Applications"
                                description="Browse and inspect transfer applications."
                                href={route(
                                    'admin.transfer-applications.index',
                                )}
                                icon={ClipboardList}
                                tone="amber"
                            />
                        )}

                        {quickActionPermissions.viewReports && (
                            <QuickActionCard
                                title="Reports"
                                description="Open management reports and workflow analytics."
                                href={
                                    route().has(
                                        'reports.index',
                                    )
                                        ? route(
                                            'reports.index',
                                        )
                                        : route(
                                            'admin.reports.index',
                                        )
                                }
                                icon={FileChartColumn}
                                tone="violet"
                            />
                        )}

                        {quickActionPermissions.viewAuditLogs && (
                            <QuickActionCard
                                title="Audit Logs"
                                description="Review recorded system and user activity."
                                href={route(
                                    'admin.audit-logs.index',
                                )}
                                icon={ShieldCheck}
                                tone="slate"
                            />
                        )}
                    </div>
                </DashboardSection>

                <div className="grid gap-6 xl:grid-cols-2">
                    <DashboardSection
                        title="Recently Added Users"
                        description="Latest accounts registered or created in the system."
                        icon={Users}
                        actionLabel="Manage users"
                        actionHref={route(
                            'admin.users.index',
                        )}
                        noPadding
                    >
                        <RecentUsers
                            users={
                                recentUsers
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Recent Audit Activity"
                        description="Latest actions captured by the audit trail."
                        icon={History}
                        actionLabel="View audit logs"
                        actionHref={route(
                            'admin.audit-logs.index',
                        )}
                        noPadding
                    >
                        <RecentAuditLogs
                            logs={
                                recentAuditLogs
                            }
                        />
                    </DashboardSection>
                </div>
            </div>
        </AdminLayout>
    );
}

function CycleDate({
    label,
    value,
}) {
    return (
        <div className="rounded-xl bg-slate-50 p-3">
            <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                {label}
            </p>

            <p className="mt-1 text-sm font-bold text-slate-800">
                {value ?? 'Not set'}
            </p>
        </div>
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
                title="No Zone application data"
                description="Zone statistics will appear after applications are submitted."
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
                                    className="h-full rounded-full bg-blue-600"
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

function RecentApplicationsTable({
    applications = [],
}) {
    if (applications.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No applications yet"
                    description="Recent transfer applications will appear here."
                    actionLabel="View Applications"
                    actionHref={route(
                        'admin.transfer-applications.index',
                    )}
                    icon={ClipboardList}
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
                            Created
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
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                            statusBadgeClasses[
                                                application.status_tone
                                            ]
                                            ?? statusBadgeClasses.blue,
                                        ].join(' ')}
                                    >
                                        {
                                            application.status_label
                                        }
                                    </span>
                                </td>

                                <td className="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-500">
                                    {
                                        application.created_at
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

function RecentUsers({
    users = [],
}) {
    if (users.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No users found"
                    description="Newly created users will appear here."
                    icon={Users}
                />
            </div>
        );
    }

    return (
        <div className="divide-y divide-slate-100">
            {users.map(
                (user) => (
                    <Link
                        key={
                            user.id
                        }
                        href={
                            user.show_url
                        }
                        className="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50"
                    >
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-700">
                            {
                                initials(
                                    user.name,
                                )
                            }
                        </div>

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-center gap-2">
                                <p className="truncate text-sm font-bold text-slate-900">
                                    {
                                        user.name
                                    }
                                </p>

                                <span
                                    className={[
                                        'rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                                        user.is_active
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-red-50 text-red-700',
                                    ].join(' ')}
                                >
                                    {user.is_active
                                        ? 'Active'
                                        : 'Inactive'}
                                </span>
                            </div>

                            <p className="mt-1 truncate text-xs text-slate-500">
                                {
                                    user.email
                                }
                            </p>

                            <p className="mt-1 text-[11px] font-semibold text-slate-400">
                                {
                                    user.role
                                }
                            </p>
                        </div>

                        <ArrowRight className="h-4 w-4 shrink-0 text-slate-300" />
                    </Link>
                ),
            )}
        </div>
    );
}

function RecentAuditLogs({
    logs = [],
}) {
    if (logs.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No audit activity"
                    description="Recorded administrative actions will appear here."
                    icon={History}
                />
            </div>
        );
    }

    return (
        <div className="divide-y divide-slate-100">
            {logs.map(
                (log) => (
                    <Link
                        key={
                            log.id
                        }
                        href={
                            log.show_url
                        }
                        className="flex items-start gap-4 px-5 py-4 transition hover:bg-slate-50"
                    >
                        <div className="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                            <Activity className="h-5 w-5" />
                        </div>

                        <div className="min-w-0 flex-1">
                            <p className="text-sm font-bold text-slate-900">
                                {
                                    log.action
                                }
                            </p>

                            <p className="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                {
                                    log.description
                                }
                            </p>

                            <p className="mt-2 text-[11px] font-semibold text-slate-400">
                                {log.user_name}
                                {' • '}
                                {log.created_at}
                            </p>
                        </div>

                        <ArrowRight className="mt-2 h-4 w-4 shrink-0 text-slate-300" />
                    </Link>
                ),
            )}
        </div>
    );
}

function SystemAlert({
    alert,
}) {
    const classes =
        alertToneClasses[
            alert.tone
        ]
        ?? alertToneClasses.blue;

    return (
        <Link
            href={
                alert.href
            }
            className={[
                'group flex items-start gap-4 rounded-2xl border p-4 transition hover:-translate-y-0.5 hover:shadow-sm',
                classes.container,
            ].join(' ')}
        >
            <div
                className={[
                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                    classes.icon,
                ].join(' ')}
            >
                <AlertTriangle className="h-5 w-5" />
            </div>

            <div className="min-w-0 flex-1">
                <p
                    className={[
                        'text-sm font-bold',
                        classes.title,
                    ].join(' ')}
                >
                    {
                        alert.title
                    }
                </p>

                <p
                    className={[
                        'mt-1 text-xs leading-5',
                        classes.description,
                    ].join(' ')}
                >
                    {
                        alert.description
                    }
                </p>
            </div>

            <ArrowRight
                className={[
                    'mt-2 h-4 w-4 shrink-0 transition group-hover:translate-x-0.5',
                    classes.description,
                ].join(' ')}
            />
        </Link>
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

function initials(
    name = '',
) {
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map(
            (part) =>
                part.charAt(0),
        )
        .join('')
        .toUpperCase()
        || 'U';
}
