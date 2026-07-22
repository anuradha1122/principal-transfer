import DashboardHeader from '@/Components/Dashboard/DashboardHeader';
import DashboardSection from '@/Components/Dashboard/DashboardSection';
import DashboardStatCard from '@/Components/Dashboard/DashboardStatCard';
import EmptyDashboardState from '@/Components/Dashboard/EmptyDashboardState';
import NotificationPreview from '@/Components/Dashboard/NotificationPreview';
import QuickActionCard from '@/Components/Dashboard/QuickActionCard';
import WorkflowProgress from '@/Components/Dashboard/WorkflowProgress';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Head,
    Link,
    usePage,
} from '@inertiajs/react';
import {
    ArrowRight,
    Bell,
    BriefcaseBusiness,
    Building2,
    CalendarDays,
    CircleAlert,
    ClipboardCheck,
    FileClock,
    FilePlus2,
    Files,
    FileText,
    History,
    Pencil,
    Scale,
    School,
    Send,
    UserRound,
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
    profile = null,
    appointment = null,
    activeCycle = null,
    latestApplication = null,
    workflowSteps = [],
    latestAppeal = null,
    latestDocument = null,
    recentApplications = [],
    recentNotifications = [],
    unreadNotificationCount = 0,
    permissions = {},
}) {
    const { auth } = usePage().props;

    const user =
        auth?.user ?? {};

    const canCreateApplication =
        Boolean(
            permissions.createApplication,
        )
        && Boolean(
            activeCycle?.is_open,
        )
        && Boolean(
            profile?.profile_complete,
        );

    return (
        <AdminLayout>
            <Head title="Principal Dashboard" />

            <div className="space-y-6">
                <DashboardHeader
                    eyebrow="Principal Self Service"
                    title={`Welcome, ${
                        profile?.full_name
                        ?? user.name
                        ?? 'Principal'
                    }`}
                    description="Track your transfer application, review workflow decisions and manage your professional information."
                    userName={
                        profile?.full_name
                        ?? user.name
                    }
                    role="Principal"
                    accent="principal"
                    icon={UserRound}
                    actionLabel={
                        canCreateApplication
                            ? 'Create Application'
                            : 'View Applications'
                    }
                    actionHref={
                        canCreateApplication
                            ? route(
                                'principal.transfer-applications.create',
                            )
                            : route(
                                'principal.transfer-applications.index',
                            )
                    }
                />

                {! profile?.profile_complete && (
                    <ProfileWarning
                        profile={profile}
                    />
                )}

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <DashboardStatCard
                        title="My Applications"
                        value={
                            summary.applications
                        }
                        description="Transfer applications submitted from your account."
                        icon={Files}
                        tone="blue"
                    />

                    <DashboardStatCard
                        title="Active Applications"
                        value={
                            summary.active_applications
                        }
                        description="Applications still moving through the workflow."
                        icon={FileClock}
                        tone="amber"
                    />

                    <DashboardStatCard
                        title="My Appeals"
                        value={
                            summary.appeals
                        }
                        description="Appeals associated with your transfer applications."
                        icon={Scale}
                        tone="violet"
                    />

                    <DashboardStatCard
                        title="Published Documents"
                        value={
                            summary.published_documents
                        }
                        description="Transfer documents currently available to you."
                        icon={FileText}
                        tone="emerald"
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Current Transfer Cycle"
                        description="Current transfer application period and availability."
                        icon={CalendarDays}
                        className="xl:col-span-1"
                    >
                        {activeCycle ? (
                            <ActiveCycle
                                cycle={
                                    activeCycle
                                }
                                canCreate={
                                    canCreateApplication
                                }
                            />
                        ) : (
                            <EmptyDashboardState
                                title="No active transfer cycle"
                                description="A new application can be created when an active transfer cycle is announced."
                                icon={CalendarDays}
                            />
                        )}
                    </DashboardSection>

                    <DashboardSection
                        title="Latest Application"
                        description="Your most recent transfer application and current progress."
                        icon={ClipboardCheck}
                        className="xl:col-span-2"
                    >
                        {latestApplication ? (
                            <LatestApplication
                                application={
                                    latestApplication
                                }
                                steps={
                                    workflowSteps
                                }
                            />
                        ) : (
                            <EmptyDashboardState
                                title="No transfer application"
                                description="You have not created a transfer application yet."
                                actionLabel={
                                    canCreateApplication
                                        ? 'Create Application'
                                        : null
                                }
                                actionHref={
                                    canCreateApplication
                                        ? route(
                                            'principal.transfer-applications.create',
                                        )
                                        : null
                                }
                                icon={FilePlus2}
                            />
                        )}
                    </DashboardSection>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Current Appointment"
                        description="Your active school appointment and service information."
                        icon={School}
                        className="xl:col-span-1"
                    >
                        {appointment ? (
                            <AppointmentSummary
                                appointment={
                                    appointment
                                }
                            />
                        ) : (
                            <EmptyDashboardState
                                title="No current appointment"
                                description="Add your current school appointment before submitting a transfer application."
                                actionLabel="Add Appointment"
                                actionHref={route(
                                    'principal.appointments.create',
                                )}
                                icon={Building2}
                            />
                        )}
                    </DashboardSection>

                    <DashboardSection
                        title="Latest Appeal"
                        description="Most recent appeal linked to your transfer applications."
                        icon={Scale}
                        className="xl:col-span-1"
                    >
                        {latestAppeal ? (
                            <LatestAppeal
                                appeal={
                                    latestAppeal
                                }
                            />
                        ) : (
                            <EmptyDashboardState
                                title="No appeals"
                                description="Appeals submitted for your transfer decisions will appear here."
                                icon={Scale}
                            />
                        )}
                    </DashboardSection>

                    <DashboardSection
                        title="Latest Transfer Document"
                        description="Your newest generated or published transfer document."
                        icon={FileText}
                        className="xl:col-span-1"
                    >
                        {latestDocument ? (
                            <LatestDocument
                                document={
                                    latestDocument
                                }
                            />
                        ) : (
                            <EmptyDashboardState
                                title="No documents"
                                description="Published transfer letters and decisions will appear here."
                                icon={FileText}
                            />
                        )}
                    </DashboardSection>
                </div>

                <DashboardSection
                    title="Quick Actions"
                    description="Common Principal self-service actions."
                    icon={Send}
                >
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                        {canCreateApplication && (
                            <QuickActionCard
                                title="Create Application"
                                description="Start a new transfer application for the active cycle."
                                href={route(
                                    'principal.transfer-applications.create',
                                )}
                                icon={FilePlus2}
                                tone="blue"
                            />
                        )}

                        {permissions.viewApplications && (
                            <QuickActionCard
                                title="My Applications"
                                description="View application status, decisions and history."
                                href={route(
                                    'principal.transfer-applications.index',
                                )}
                                icon={Files}
                                tone="amber"
                            />
                        )}

                        <QuickActionCard
                            title="My Profile"
                            description="Review and update your Principal profile."
                            href={route(
                                'principal.profile.show',
                            )}
                            icon={UserRound}
                            tone="cyan"
                        />

                        <QuickActionCard
                            title="Appointments"
                            description="Manage your current and previous appointment history."
                            href={
                                appointment?.edit_url
                                ?? route(
                                    'principal.appointments.create',
                                )
                            }
                            icon={BriefcaseBusiness}
                            tone="emerald"
                        />

                        {permissions.viewAppeals && (
                            <QuickActionCard
                                title="My Appeals"
                                description="View and manage transfer appeals."
                                href={route(
                                    'principal.transfer-appeals.index',
                                )}
                                icon={Scale}
                                tone="violet"
                            />
                        )}

                        {permissions.viewDocuments && (
                            <QuickActionCard
                                title="My Documents"
                                description="Open published transfer documents."
                                href={route(
                                    'principal.transfer-documents.index',
                                )}
                                icon={FileText}
                                tone="slate"
                            />
                        )}
                    </div>
                </DashboardSection>

                <div className="grid gap-6 xl:grid-cols-3">
                    <DashboardSection
                        title="Recent Applications"
                        description="Your latest transfer application activity."
                        icon={History}
                        actionLabel="View all"
                        actionHref={route(
                            'principal.transfer-applications.index',
                        )}
                        className="xl:col-span-2"
                        noPadding
                    >
                        <RecentApplications
                            applications={
                                recentApplications
                            }
                        />
                    </DashboardSection>

                    <DashboardSection
                        title="Notifications"
                        description="Recent application and workflow updates."
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

function ProfileWarning({
    profile,
}) {
    return (
        <div className="flex flex-col gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-start gap-3">
                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <CircleAlert className="h-5 w-5" />
                </div>

                <div>
                    <h2 className="font-bold text-amber-900">
                        Complete your Principal information
                    </h2>

                    <p className="mt-1 text-sm leading-6 text-amber-700">
                        Your profile and current appointment must be complete before creating a transfer application.
                    </p>
                </div>
            </div>

            <Link
                href={
                    profile?.edit_url
                    ?? route(
                        'principal.profile.edit',
                    )
                }
                className="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-800"
            >
                <Pencil className="h-4 w-4" />
                Complete Profile
            </Link>
        </div>
    );
}

function ActiveCycle({
    cycle,
    canCreate,
}) {
    return (
        <div className="space-y-5">
            <div>
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h3 className="text-xl font-bold text-slate-900">
                        {cycle.name}
                    </h3>

                    <span
                        className={[
                            'rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide',
                            cycle.is_open
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-red-50 text-red-700',
                        ].join(' ')}
                    >
                        {cycle.is_open
                            ? 'Applications Open'
                            : 'Applications Closed'}
                    </span>
                </div>

                <p className="mt-2 text-sm text-slate-500">
                    Transfer applications are controlled by the published application period.
                </p>
            </div>

            <div className="grid grid-cols-2 gap-3">
                <InfoBox
                    label="Opens"
                    value={
                        cycle.application_start_date
                        ?? 'Not specified'
                    }
                />

                <InfoBox
                    label="Closes"
                    value={
                        cycle.application_end_date
                        ?? 'Not specified'
                    }
                />
            </div>

            {canCreate && (
                <Link
                    href={route(
                        'principal.transfer-applications.create',
                    )}
                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-800"
                >
                    <FilePlus2 className="h-4 w-4" />
                    Create Transfer Application
                </Link>
            )}
        </div>
    );
}

function LatestApplication({
    application,
    steps,
}) {
    return (
        <div className="space-y-6">
            <div className="flex flex-col gap-4 rounded-2xl bg-slate-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <Link
                        href={
                            application.show_url
                        }
                        className="text-lg font-bold text-blue-700 hover:text-blue-900"
                    >
                        {
                            application.application_number
                        }
                    </Link>

                    <p className="mt-1 text-sm text-slate-500">
                        {application.cycle_name
                            ?? 'Transfer cycle'}
                        {' • '}
                        {application.transfer_reason
                            ?? 'Transfer application'}
                    </p>

                    <p className="mt-2 text-xs font-medium text-slate-400">
                        Updated:{' '}
                        {application.updated_at
                            ?? application.created_at
                            ?? 'Not available'}
                    </p>
                </div>

                <div className="flex flex-wrap items-center gap-3">
                    <span
                        className={[
                            'rounded-full px-3 py-1.5 text-xs font-bold',
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

                    {application.edit_url && (
                        <Link
                            href={
                                application.edit_url
                            }
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50"
                        >
                            <Pencil className="h-3.5 w-3.5" />
                            Edit
                        </Link>
                    )}
                </div>
            </div>

            <WorkflowProgress
                steps={steps}
            />

            <div className="flex justify-end">
                <Link
                    href={
                        application.show_url
                    }
                    className="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900"
                >
                    View full application

                    <ArrowRight className="h-4 w-4" />
                </Link>
            </div>
        </div>
    );
}

function AppointmentSummary({
    appointment,
}) {
    return (
        <div className="space-y-4">
            <div className="rounded-2xl bg-blue-50 p-4">
                <div className="flex items-start gap-3">
                    <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <School className="h-5 w-5" />
                    </div>

                    <div>
                        <h3 className="font-bold text-slate-900">
                            {
                                appointment.school_name
                            }
                        </h3>

                        <p className="mt-1 text-sm text-slate-600">
                            {
                                appointment.designation
                            }
                        </p>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-2 gap-3">
                <InfoBox
                    label="Zone"
                    value={
                        appointment.zone_name
                        ?? 'Not assigned'
                    }
                />

                <InfoBox
                    label="Division"
                    value={
                        appointment.division_name
                        ?? 'Not assigned'
                    }
                />

                <InfoBox
                    label="Started"
                    value={
                        appointment.start_date
                        ?? 'Not recorded'
                    }
                />

                <InfoBox
                    label="Service"
                    value={
                        appointment.service_years
                        !== null
                            ? `${appointment.service_years} years`
                            : 'Not available'
                    }
                />
            </div>

            <Link
                href={
                    appointment.edit_url
                }
                className="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900"
            >
                Edit appointment

                <ArrowRight className="h-4 w-4" />
            </Link>
        </div>
    );
}

function LatestAppeal({
    appeal,
}) {
    return (
        <div className="space-y-4">
            <div className="rounded-2xl bg-violet-50 p-4">
                <Scale className="h-6 w-6 text-violet-700" />

                <h3 className="mt-3 font-bold text-slate-900">
                    {
                        appeal.reference_number
                    }
                </h3>

                <p className="mt-1 text-sm text-slate-500">
                    Application:{' '}
                    {appeal.application_number
                        ?? 'Not available'}
                </p>

                <span className="mt-3 inline-flex rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-700">
                    {
                        appeal.status_label
                    }
                </span>
            </div>

            <Link
                href={
                    appeal.show_url
                }
                className="inline-flex items-center gap-2 text-sm font-bold text-violet-700 hover:text-violet-900"
            >
                View appeal

                <ArrowRight className="h-4 w-4" />
            </Link>
        </div>
    );
}

function LatestDocument({
    document,
}) {
    return (
        <div className="space-y-4">
            <div
                className={[
                    'rounded-2xl p-4',
                    document.is_published
                        ? 'bg-emerald-50'
                        : 'bg-slate-50',
                ].join(' ')}
            >
                <FileText
                    className={[
                        'h-6 w-6',
                        document.is_published
                            ? 'text-emerald-700'
                            : 'text-slate-500',
                    ].join(' ')}
                />

                <h3 className="mt-3 font-bold text-slate-900">
                    {
                        document.title
                    }
                </h3>

                {document.document_number && (
                    <p className="mt-1 text-sm text-slate-500">
                        {
                            document.document_number
                        }
                    </p>
                )}

                <span
                    className={[
                        'mt-3 inline-flex rounded-full px-3 py-1 text-xs font-bold',
                        document.is_published
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-slate-200 text-slate-600',
                    ].join(' ')}
                >
                    {document.is_published
                        ? 'Published'
                        : 'Not published'}
                </span>
            </div>

            <div className="flex flex-wrap gap-3">
                <Link
                    href={
                        document.show_url
                    }
                    className="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900"
                >
                    View document
                </Link>

                {document.download_url && (
                    <a
                        href={
                            document.download_url
                        }
                        className="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900"
                    >
                        Download
                    </a>
                )}
            </div>
        </div>
    );
}

function RecentApplications({
    applications,
}) {
    if (applications.length === 0) {
        return (
            <div className="p-5">
                <EmptyDashboardState
                    title="No recent applications"
                    description="Your applications will appear here after they are created."
                    icon={Files}
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
                        className="flex items-start gap-4 px-5 py-4 transition hover:bg-slate-50"
                    >
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <Files className="h-5 w-5" />
                        </div>

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <p className="font-bold text-slate-900">
                                    {
                                        application.application_number
                                    }
                                </p>

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
                            </div>

                            <p className="mt-1 text-sm text-slate-500">
                                {
                                    application.transfer_reason
                                }
                            </p>

                            <p className="mt-2 text-xs font-medium text-slate-400">
                                {
                                    application.date
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

            <p className="mt-1 break-words text-sm font-bold text-slate-800">
                {value}
            </p>
        </div>
    );
}
