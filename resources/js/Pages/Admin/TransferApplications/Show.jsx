import AdminLayout from '@/Layouts/AdminLayout';
import { Link } from '@inertiajs/react';
import {
    ArrowLeft,
    Building2,
    CalendarDays,
    Download,
    FileText,
    Mail,
    MapPin,
    UserRound,
} from 'lucide-react';

function formatDate(value) {
    if (!value) {
        return 'Not recorded';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    }).format(new Date(value));
}

function formatDateTime(value) {
    if (!value) {
        return 'Not recorded';
    }

    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(value));
}

function DetailItem({
    label,
    value,
}) {
    return (
        <div>
            <dt className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </dt>

            <dd className="mt-1 text-sm font-semibold text-slate-800">
                {value || 'Not recorded'}
            </dd>
        </div>
    );
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

export default function Show({
    application,
}) {
    const canDownloadPdf = Boolean(
        application.submitted_at,
    );

    return (
        <AdminLayout
            title={
                application.application_number ||
                `Transfer Application #${application.id}`
            }
            header={
                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div className="flex items-center gap-3">
                        <Link
                            href={route(
                                'admin.transfer-applications.index',
                            )}
                            className="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                        >
                            <ArrowLeft className="h-5 w-5" />
                        </Link>

                        <div>
                            <h1 className="text-2xl font-bold text-slate-900">
                                {application.application_number ||
                                    `Draft Application #${application.id}`}
                            </h1>

                            <p className="mt-1 text-sm text-slate-500">
                                {
                                    application
                                        .transfer_cycle
                                        ?.name
                                }
                            </p>
                        </div>
                    </div>

                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                        {canDownloadPdf && (
                            <a
                                href={route(
                                    'admin.transfer-applications.pdf',
                                    application.id,
                                )}
                                className="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                            >
                                <Download className="h-4 w-4" />

                                Download Submitted PDF
                            </a>
                        )}

                        <span
                            className={[
                                'w-fit rounded-full px-4 py-2 text-sm font-semibold',
                                statusClass(
                                    application.status,
                                ),
                            ].join(' ')}
                        >
                            {application.status}
                        </span>
                    </div>
                </div>
            }
        >
            <div className="grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                    <div className="flex items-center gap-3">
                        <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <UserRound className="h-6 w-6" />
                        </div>

                        <div>
                            <h2 className="text-lg font-bold text-slate-900">
                                Principal Information
                            </h2>

                            <p className="text-sm text-slate-500">
                                Applicant identity and service
                                snapshot
                            </p>
                        </div>
                    </div>

                    <dl className="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        <DetailItem
                            label="Principal Name"
                            value={
                                application.principal_name
                            }
                        />

                        <DetailItem
                            label="NIC Number"
                            value={application.nic}
                        />

                        <DetailItem
                            label="Employee Number"
                            value={
                                application.employee_number
                            }
                        />

                        <DetailItem
                            label="Service Grade"
                            value={
                                application.service_grade
                            }
                        />

                        <DetailItem
                            label="Current Designation"
                            value={
                                application.current_designation
                            }
                        />

                        <DetailItem
                            label="Account Email"
                            value={
                                application
                                    .principal_profile
                                    ?.user?.email
                            }
                        />
                    </dl>
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <CalendarDays className="h-6 w-6" />
                    </div>

                    <h2 className="mt-5 text-lg font-bold text-slate-900">
                        Application Timeline
                    </h2>

                    <dl className="mt-5 space-y-5">
                        <DetailItem
                            label="Created At"
                            value={formatDateTime(
                                application.created_at,
                            )}
                        />

                        <DetailItem
                            label="Submitted At"
                            value={formatDateTime(
                                application.submitted_at,
                            )}
                        />

                        <DetailItem
                            label="PDF Generated At"
                            value={formatDateTime(
                                application
                                    .submitted_pdf_generated_at,
                            )}
                        />

                        <DetailItem
                            label="Withdrawn At"
                            value={formatDateTime(
                                application.withdrawn_at,
                            )}
                        />

                        <DetailItem
                            label="Declaration Accepted"
                            value={
                                application.declaration_accepted
                                    ? 'Yes'
                                    : 'No'
                            }
                        />
                    </dl>
                </section>
            </div>

            {canDownloadPdf && (
                <section className="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div className="flex items-start gap-3">
                            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                <FileText className="h-6 w-6" />
                            </div>

                            <div>
                                <h2 className="font-bold text-emerald-900">
                                    Submitted Application PDF
                                </h2>

                                <p className="mt-1 text-sm leading-6 text-emerald-700">
                                    This PDF contains the
                                    application information
                                    captured when the principal
                                    submitted the request.
                                </p>

                                {application
                                    .submitted_pdf_generated_at && (
                                    <p className="mt-1 text-xs text-emerald-600">
                                        Generated:{' '}
                                        {formatDateTime(
                                            application
                                                .submitted_pdf_generated_at,
                                        )}
                                    </p>
                                )}
                            </div>
                        </div>

                        <a
                            href={route(
                                'admin.transfer-applications.pdf',
                                application.id,
                            )}
                            className="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                        >
                            <Download className="h-4 w-4" />

                            Download PDF
                        </a>
                    </div>
                </section>
            )}

            {!canDownloadPdf && (
                <section className="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <div className="flex items-start gap-3">
                        <FileText className="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />

                        <div>
                            <p className="font-semibold text-amber-900">
                                PDF is not available yet
                            </p>

                            <p className="mt-1 text-sm leading-6 text-amber-700">
                                The application PDF will be
                                generated after the principal
                                submits the application.
                            </p>
                        </div>
                    </div>
                </section>
            )}

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex items-center gap-3">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <Building2 className="h-6 w-6" />
                    </div>

                    <div>
                        <h2 className="text-lg font-bold text-slate-900">
                            Current Appointment Snapshot
                        </h2>

                        <p className="text-sm text-slate-500">
                            Appointment information captured
                            when the application was created
                        </p>
                    </div>
                </div>

                <dl className="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    <DetailItem
                        label="Current School"
                        value={
                            application.current_school?.name
                        }
                    />

                    <DetailItem
                        label="Division"
                        value={
                            application.current_school
                                ?.division?.name
                        }
                    />

                    <DetailItem
                        label="Zone"
                        value={
                            application.current_school
                                ?.division?.zone?.name
                        }
                    />

                    <DetailItem
                        label="Appointment Start Date"
                        value={formatDate(
                            application.current_appointment_start_date,
                        )}
                    />

                    <DetailItem
                        label="Service at Current School"
                        value={`${application.current_school_service_months ?? 0} month(s)`}
                    />

                    <DetailItem
                        label="Current Designation"
                        value={
                            application.current_designation
                        }
                    />
                </dl>
            </section>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex items-center gap-3">
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <FileText className="h-6 w-6" />
                    </div>

                    <div>
                        <h2 className="text-lg font-bold text-slate-900">
                            Transfer Request
                        </h2>

                        <p className="text-sm text-slate-500">
                            Reason and supporting explanation
                        </p>
                    </div>
                </div>

                <dl className="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    <DetailItem
                        label="Transfer Reason"
                        value={
                            application.transfer_reason
                        }
                    />

                    <DetailItem
                        label="Medical Reason"
                        value={
                            application.has_medical_reason
                                ? 'Yes'
                                : 'No'
                        }
                    />

                    <DetailItem
                        label="Spouse Employment Reason"
                        value={
                            application.has_spouse_employment_reason
                                ? 'Yes'
                                : 'No'
                        }
                    />

                    <DetailItem
                        label="Mutual Transfer"
                        value={
                            application.is_mutual_transfer
                                ? 'Yes'
                                : 'No'
                        }
                    />

                    {application.is_mutual_transfer && (
                        <DetailItem
                            label="Mutual Principal NIC"
                            value={
                                application.mutual_principal_nic
                            }
                        />
                    )}
                </dl>

                <div className="mt-6 border-t border-slate-200 pt-5">
                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Detailed Explanation
                    </p>

                    <p className="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">
                        {application.reason_details ||
                            'Not recorded'}
                    </p>
                </div>

                {application.principal_remarks && (
                    <div className="mt-6 border-t border-slate-200 pt-5">
                        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Additional Remarks
                        </p>

                        <p className="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">
                            {
                                application.principal_remarks
                            }
                        </p>
                    </div>
                )}
            </section>

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="border-b border-slate-200 px-6 py-5">
                    <h2 className="text-lg font-bold text-slate-900">
                        Ranked School Preferences
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        Schools selected by the principal in
                        priority order
                    </p>
                </div>

                <div className="divide-y divide-slate-100">
                    {(application.preferences ?? []).map(
                        (preference) => (
                            <div
                                key={preference.id}
                                className="flex gap-4 px-6 py-5"
                            >
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                    {
                                        preference.preference_order
                                    }
                                </div>

                                <div className="flex-1">
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p className="font-bold text-slate-900">
                                                {preference
                                                    .school
                                                    ?.name ||
                                                    'School not recorded'}
                                            </p>

                                            <p className="mt-1 text-xs text-slate-500">
                                                Census No:{' '}
                                                {preference
                                                    .school
                                                    ?.census_number ||
                                                    'Not recorded'}
                                            </p>
                                        </div>

                                        <div className="flex items-center gap-2 text-xs text-slate-500">
                                            <MapPin className="h-4 w-4" />

                                            <span>
                                                {preference
                                                    .school
                                                    ?.division
                                                    ?.name ||
                                                    'Division not recorded'}{' '}
                                                Division ·{' '}
                                                {preference
                                                    .school
                                                    ?.division
                                                    ?.zone
                                                    ?.name ||
                                                    'Zone not recorded'}{' '}
                                                Zone
                                            </span>
                                        </div>
                                    </div>

                                    {preference.preference_reason && (
                                        <div className="mt-4 rounded-xl bg-slate-50 p-4">
                                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Preference Reason
                                            </p>

                                            <p className="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                                                {
                                                    preference.preference_reason
                                                }
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        ),
                    )}
                </div>

                {(application.preferences ?? [])
                    .length === 0 && (
                    <div className="px-6 py-12 text-center text-sm text-slate-500">
                        No school preferences were
                        recorded.
                    </div>
                )}
            </section>

            {application.status ===
                'Withdrawn' &&
                application.withdrawal_reason && (
                    <section className="mt-6 rounded-2xl border border-red-200 bg-red-50 p-6">
                        <h2 className="font-bold text-red-900">
                            Withdrawal Reason
                        </h2>

                        <p className="mt-3 whitespace-pre-line text-sm leading-7 text-red-700">
                            {
                                application.withdrawal_reason
                            }
                        </p>
                    </section>
                )}

            <div className="mt-6 rounded-2xl border border-blue-200 bg-blue-50 p-5">
                <div className="flex items-start gap-3">
                    <Mail className="mt-0.5 h-5 w-5 shrink-0 text-blue-600" />

                    <div>
                        <p className="font-semibold text-blue-900">
                            Review actions are not available
                            yet
                        </p>

                        <p className="mt-1 text-sm leading-6 text-blue-700">
                            Zonal approval and rejection
                            actions will be added in Module
                            07. This page currently provides
                            a complete read-only application
                            view.
                        </p>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
