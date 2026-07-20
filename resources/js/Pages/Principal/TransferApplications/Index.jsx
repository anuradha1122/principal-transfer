import AdminLayout from '@/Layouts/AdminLayout';
import {
    Download,
    Eye,
    FileText,
    Pencil,
    Plus,
} from 'lucide-react';
import {
    Link,
    router,
} from '@inertiajs/react';

function formatDate(value) {
    if (!value) {
        return 'Not available';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat(
        'en-LK',
        {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
        },
    ).format(date);
}

function statusClass(status) {
    const classes = {
        Draft:
            'bg-slate-100 text-slate-700',

        Submitted:
            'bg-blue-50 text-blue-700',

        'Zonal Review':
            'bg-amber-50 text-amber-700',

        'Zonal Approved':
            'bg-emerald-50 text-emerald-700',

        'Zonal Rejected':
            'bg-red-50 text-red-700',

        'Provincial Review':
            'bg-amber-50 text-amber-700',

        'Provincial Approved':
            'bg-emerald-50 text-emerald-700',

        'Provincial Rejected':
            'bg-red-50 text-red-700',

        'Board Review':
            'bg-violet-50 text-violet-700',

        Approved:
            'bg-emerald-50 text-emerald-700',

        Rejected:
            'bg-red-50 text-red-700',

        Waitlisted:
            'bg-orange-50 text-orange-700',

        Withdrawn:
            'bg-slate-100 text-slate-600',

        Cancelled:
            'bg-red-50 text-red-700',
    };

    return (
        classes[status] ??
        'bg-slate-100 text-slate-700'
    );
}

export default function Index({
    applications = {
        data: [],
        links: [],
    },
    availableCycles = [],
}) {
    const applicationRows =
        applications?.data ?? [];

    const paginationLinks =
        applications?.links ?? [];

    const openApplication = (
        applicationId,
    ) => {
        router.visit(
            route(
                'principal.transfer-applications.show',
                applicationId,
            ),
            {
                method: 'get',
                preserveScroll: false,
                preserveState: false,
            },
        );
    };

    return (
        <AdminLayout
            title="My Transfer Applications"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        My Transfer Applications
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Create and track your principal
                        transfer requests.
                    </p>
                </div>
            }
        >
            {availableCycles.length > 0 && (
                <section className="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-6">
                    <div>
                        <h2 className="font-bold text-blue-900">
                            Open Transfer Cycles
                        </h2>

                        <p className="mt-1 text-sm text-blue-700">
                            Select an open cycle to begin a
                            new transfer application.
                        </p>
                    </div>

                    <div className="mt-4 grid gap-4 lg:grid-cols-2">
                        {availableCycles.map(
                            (cycle) => (
                                <div
                                    key={cycle.id}
                                    className="rounded-xl border border-blue-100 bg-white p-5 shadow-sm"
                                >
                                    <p className="font-bold text-slate-900">
                                        {cycle.name}
                                    </p>

                                    <p className="mt-1 text-sm text-slate-500">
                                        {cycle.code}
                                        {' · '}
                                        {
                                            cycle.transfer_type
                                        }
                                    </p>

                                    <p className="mt-3 text-xs text-slate-500">
                                        Applications close on{' '}
                                        {formatDate(
                                            cycle.application_close_date,
                                        )}
                                    </p>

                                    <Link
                                        href={route(
                                            'principal.transfer-applications.create',
                                            {
                                                transfer_cycle_id:
                                                    cycle.id,
                                            },
                                        )}
                                        className="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                    >
                                        <Plus className="h-4 w-4" />

                                        Start Application
                                    </Link>
                                </div>
                            ),
                        )}
                    </div>
                </section>
            )}

            <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="border-b border-slate-200 px-6 py-5">
                    <h2 className="font-bold text-slate-900">
                        Application History
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        View drafts, submitted applications
                        and previous transfer requests.
                    </p>
                </div>

                {applicationRows.length > 0 ? (
                    <>
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-slate-200">
                                <thead className="bg-slate-50">
                                    <tr>
                                        {[
                                            'Application',
                                            'Cycle',
                                            'Current School',
                                            'Reason',
                                            'Status',
                                            'Submitted',
                                            'Actions',
                                        ].map(
                                            (heading) => (
                                                <th
                                                    key={
                                                        heading
                                                    }
                                                    className="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                                >
                                                    {
                                                        heading
                                                    }
                                                </th>
                                            ),
                                        )}
                                    </tr>
                                </thead>

                                <tbody className="divide-y divide-slate-100">
                                    {applicationRows.map(
                                        (
                                            application,
                                        ) => {
                                            const canDownloadPdf =
                                                Boolean(
                                                    application.submitted_at,
                                                );

                                            return (
                                                <tr
                                                    key={
                                                        application.id
                                                    }
                                                    className="transition hover:bg-slate-50"
                                                >
                                                    <td className="whitespace-nowrap px-5 py-4">
                                                        <p className="font-semibold text-slate-900">
                                                            {application.application_number ||
                                                                `Draft #${application.id}`}
                                                        </p>

                                                        <p className="mt-1 text-xs text-slate-500">
                                                            Created{' '}
                                                            {formatDate(
                                                                application.created_at,
                                                            )}
                                                        </p>
                                                    </td>

                                                    <td className="px-5 py-4">
                                                        <p className="min-w-40 text-sm font-semibold text-slate-700">
                                                            {application
                                                                .transfer_cycle
                                                                ?.name ||
                                                                'Not available'}
                                                        </p>

                                                        {application
                                                            .transfer_cycle
                                                            ?.code && (
                                                            <p className="mt-1 text-xs text-slate-500">
                                                                {
                                                                    application
                                                                        .transfer_cycle
                                                                        .code
                                                                }
                                                            </p>
                                                        )}
                                                    </td>

                                                    <td className="min-w-48 px-5 py-4 text-sm text-slate-600">
                                                        {application
                                                            .current_school
                                                            ?.name ||
                                                            'Not available'}
                                                    </td>

                                                    <td className="min-w-44 px-5 py-4 text-sm text-slate-600">
                                                        {application.transfer_reason ||
                                                            'Not available'}
                                                    </td>

                                                    <td className="whitespace-nowrap px-5 py-4">
                                                        <span
                                                            className={[
                                                                'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                                                statusClass(
                                                                    application.status,
                                                                ),
                                                            ].join(
                                                                ' ',
                                                            )}
                                                        >
                                                            {
                                                                application.status
                                                            }
                                                        </span>
                                                    </td>

                                                    <td className="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                                        {formatDate(
                                                            application.submitted_at,
                                                        )}
                                                    </td>

                                                    <td className="whitespace-nowrap px-5 py-4">
                                                        <div className="flex items-center gap-2">
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    openApplication(
                                                                        application.id,
                                                                    )
                                                                }
                                                                className="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                                                title="View application"
                                                                aria-label="View application"
                                                            >
                                                                <Eye className="pointer-events-none h-4 w-4" />
                                                            </button>

                                                            {application.status ===
                                                                'Draft' && (
                                                                <Link
                                                                    href={route(
                                                                        'principal.transfer-applications.edit',
                                                                        application.id,
                                                                    )}
                                                                    className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                                                    title="Edit draft"
                                                                    aria-label="Edit draft"
                                                                >
                                                                    <Pencil className="h-4 w-4" />
                                                                </Link>
                                                            )}

                                                            {canDownloadPdf && (
                                                                <a
                                                                    href={route(
                                                                        'principal.transfer-applications.pdf',
                                                                        application.id,
                                                                    )}
                                                                    className="inline-flex h-10 items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100"
                                                                    title="Download submitted PDF"
                                                                >
                                                                    <Download className="h-4 w-4" />

                                                                    PDF
                                                                </a>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        },
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {paginationLinks.length >
                            3 && (
                            <div className="flex flex-wrap items-center justify-center gap-2 border-t border-slate-200 px-6 py-4">
                                {paginationLinks.map(
                                    (
                                        link,
                                        index,
                                    ) => {
                                        const label =
                                            link.label
                                                .replace(
                                                    '&laquo;',
                                                    '‹',
                                                )
                                                .replace(
                                                    '&raquo;',
                                                    '›',
                                                );

                                        if (
                                            !link.url
                                        ) {
                                            return (
                                                <span
                                                    key={
                                                        index
                                                    }
                                                    className="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400"
                                                >
                                                    {
                                                        label
                                                    }
                                                </span>
                                            );
                                        }

                                        return (
                                            <Link
                                                key={
                                                    index
                                                }
                                                href={
                                                    link.url
                                                }
                                                preserveScroll
                                                className={[
                                                    'rounded-lg border px-3 py-2 text-sm font-semibold transition',
                                                    link.active
                                                        ? 'border-blue-600 bg-blue-600 text-white'
                                                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50',
                                                ].join(
                                                    ' ',
                                                )}
                                            >
                                                {
                                                    label
                                                }
                                            </Link>
                                        );
                                    },
                                )}
                            </div>
                        )}
                    </>
                ) : (
                    <div className="flex flex-col items-center px-6 py-14 text-center">
                        <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100">
                            <FileText className="h-7 w-7 text-slate-400" />
                        </div>

                        <p className="mt-4 font-semibold text-slate-700">
                            No transfer applications
                        </p>

                        <p className="mt-1 max-w-md text-sm leading-6 text-slate-500">
                            You have not created a transfer
                            application yet. An application
                            can be started when a transfer
                            cycle is open.
                        </p>
                    </div>
                )}
            </section>
        </AdminLayout>
    );
}
