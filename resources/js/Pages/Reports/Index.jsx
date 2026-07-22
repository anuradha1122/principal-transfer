import AnalyticsCard from '@/Components/Reports/AnalyticsCard';
import ReportFilters from '@/Components/Reports/ReportFilters';
import StatusDistributionChart from '@/Components/Reports/StatusDistributionChart';
import ZoneComparisonChart from '@/Components/Reports/ZoneComparisonChart';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Head,
    Link,
    usePage,
} from '@inertiajs/react';
import {
    BadgeCheck,
    CalendarClock,
    ChartNoAxesCombined,
    CircleX,
    Clock3,
    FileCheck2,
    FileText,
    ListChecks,
    Scale,
} from 'lucide-react';

export default function Index({
    filters = {},
    summary = {},
    statusDistribution = [],
    zoneDistribution = [],
    monthlyTrend = [],
    oldestPending = [],
    filterOptions = {},
    permissions = {},
}) {
    const { auth } = usePage().props;

    const roles = auth?.roles ?? [];

    const applicationShowRoute = (
        applicationId,
    ) => {
        if (
            roles.includes(
                'Super Admin',
            )
        ) {
            return route(
                'admin.transfer-applications.show',
                applicationId,
            );
        }

        if (
            roles.includes(
                'Zonal Director',
            )
        ) {
            return route(
                'zonal.transfer-applications.show',
                applicationId,
            );
        }

        if (
            roles.includes(
                'Provincial Director',
            )
        ) {
            return route(
                'provincial.transfer-applications.show',
                applicationId,
            );
        }

        if (
            roles.includes(
                'Transfer Board Member',
            )
        ) {
            return route(
                'transfer-board.transfer-applications.show',
                applicationId,
            );
        }

        if (
            roles.includes(
                'Principal',
            )
        ) {
            return route(
                'principal.transfer-applications.show',
                applicationId,
            );
        }

        return null;
    };

    return (
        <AdminLayout>
            <Head title="Management Reports" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">
                            Reports and Analytics
                        </p>

                        <h1 className="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            Management Analytics
                        </h1>

                        <p className="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                            Monitor transfer applications, workflow decisions,
                            Zone distribution and pending case age.
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-3">
                        {permissions.exportPdf && (
                            <button
                                type="button"
                                disabled
                                title="PDF export will be added in Phase 15.2"
                                className="inline-flex cursor-not-allowed items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-400"
                            >
                                <FileText className="h-4 w-4" />
                                Export PDF
                            </button>
                        )}

                        {permissions.exportExcel && (
                            <button
                                type="button"
                                disabled
                                title="Excel export will be added in Phase 15.2"
                                className="inline-flex cursor-not-allowed items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-400"
                            >
                                <FileCheck2 className="h-4 w-4" />
                                Export Excel
                            </button>
                        )}
                    </div>
                </div>

                <ReportFilters
                    filters={filters}
                    options={filterOptions}
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <AnalyticsCard
                        title="Total Applications"
                        value={
                            summary.total_applications
                        }
                        description="Applications matching the current report filters."
                        icon={ChartNoAxesCombined}
                    />

                    <AnalyticsCard
                        title="Final Approvals"
                        value={
                            summary.final_approved
                        }
                        description={`Approval rate: ${Number(
                            summary.approval_rate
                            ?? 0,
                        ).toFixed(2)}%`}
                        icon={BadgeCheck}
                    />

                    <AnalyticsCard
                        title="Final Rejections"
                        value={
                            summary.final_rejected
                        }
                        description="Applications rejected at final Board decision."
                        icon={CircleX}
                    />

                    <AnalyticsCard
                        title="Pending Decisions"
                        value={
                            summary.pending_decisions
                        }
                        description="Applications still moving through the workflow."
                        icon={Clock3}
                    />

                    <AnalyticsCard
                        title="Zonal Review"
                        value={
                            summary.zonal_review
                        }
                        description="Applications currently under Zonal review."
                        icon={ListChecks}
                    />

                    <AnalyticsCard
                        title="Provincial Review"
                        value={
                            summary.provincial_review
                        }
                        description="Applications currently under Provincial review."
                        icon={Scale}
                    />

                    <AnalyticsCard
                        title="Appeals"
                        value={
                            summary.appeals
                        }
                        description="Appeals associated with the filtered applications."
                        icon={CalendarClock}
                    />

                    <AnalyticsCard
                        title="Published Documents"
                        value={
                            summary.published_documents
                        }
                        description="Published transfer documents linked to these applications."
                        icon={FileCheck2}
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <StatusDistributionChart
                        items={
                            statusDistribution
                        }
                    />

                    <ZoneComparisonChart
                        items={
                            zoneDistribution
                        }
                    />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-1">
                        <h2 className="text-base font-bold text-slate-900">
                            Monthly Submission Trend
                        </h2>

                        <p className="mt-1 text-sm text-slate-500">
                            Applications submitted during the latest twelve months.
                        </p>

                        <div className="mt-5 space-y-3">
                            {monthlyTrend.length === 0 ? (
                                <p className="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                                    No monthly submission data is available.
                                </p>
                            ) : (
                                monthlyTrend.map(
                                    (item) => (
                                        <div
                                            key={
                                                item.month
                                            }
                                            className="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"
                                        >
                                            <span className="text-sm font-medium text-slate-700">
                                                {
                                                    item.label
                                                }
                                            </span>

                                            <span className="text-sm font-bold text-slate-900">
                                                {Number(
                                                    item.total
                                                    ?? 0,
                                                ).toLocaleString()}
                                            </span>
                                        </div>
                                    ),
                                )
                            )}
                        </div>
                    </div>

                    <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                        <div className="border-b border-slate-200 px-5 py-4">
                            <h2 className="text-base font-bold text-slate-900">
                                Oldest Pending Applications
                            </h2>

                            <p className="mt-1 text-sm text-slate-500">
                                Submitted applications that have remained unresolved the longest.
                            </p>
                        </div>

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
                                    {oldestPending.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan="5"
                                                className="px-5 py-12 text-center text-sm text-slate-500"
                                            >
                                                No pending applications match the selected filters.
                                            </td>
                                        </tr>
                                    ) : (
                                        oldestPending.map(
                                            (
                                                application,
                                            ) => {
                                                const showUrl =
                                                    applicationShowRoute(
                                                        application.id,
                                                    );

                                                return (
                                                    <tr
                                                        key={
                                                            application.id
                                                        }
                                                        className="hover:bg-slate-50"
                                                    >
                                                        <td className="whitespace-nowrap px-5 py-4">
                                                            {showUrl ? (
                                                                <Link
                                                                    href={
                                                                        showUrl
                                                                    }
                                                                    className="font-semibold text-blue-700 hover:text-blue-900"
                                                                >
                                                                    {
                                                                        application.application_number
                                                                    }
                                                                </Link>
                                                            ) : (
                                                                <span className="font-semibold text-slate-900">
                                                                    {
                                                                        application.application_number
                                                                    }
                                                                </span>
                                                            )}
                                                        </td>

                                                        <td className="px-5 py-4">
                                                            <p className="text-sm font-medium text-slate-900">
                                                                {
                                                                    application.principal_name
                                                                }
                                                            </p>

                                                            <p className="mt-0.5 text-xs text-slate-500">
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
                                                            <span className="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                                {
                                                                    application.status_label
                                                                }
                                                            </span>
                                                        </td>

                                                        <td className="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                                            {
                                                                application.pending_days
                                                            }{' '}
                                                            days
                                                        </td>
                                                    </tr>
                                                );
                                            },
                                        )
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
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
