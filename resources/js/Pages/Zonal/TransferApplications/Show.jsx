import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Head,
    Link,
    router,
    useForm,
} from '@inertiajs/react';
import {
    ArrowLeft,
    Building2,
    CheckCircle2,
    Clock3,
    Download,
    FileText,
    MapPin,
    PlayCircle,
    ShieldCheck,
    UserRound,
    XCircle,
} from 'lucide-react';

const statusClasses = {
    Submitted:
        'bg-blue-100 text-blue-700 ring-blue-200',
    'Zonal Review':
        'bg-amber-100 text-amber-700 ring-amber-200',
    'Zonal Approved':
        'bg-emerald-100 text-emerald-700 ring-emerald-200',
    'Zonal Rejected':
        'bg-red-100 text-red-700 ring-red-200',
};

const formatDate = (value, includeTime = false) => {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        ...(includeTime
            ? {
                  hour: '2-digit',
                  minute: '2-digit',
              }
            : {}),
    }).format(new Date(value));
};

const BooleanBadge = ({ value, yesText, noText }) => (
    <span
        className={[
            'inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset',
            value
                ? 'bg-emerald-100 text-emerald-700 ring-emerald-200'
                : 'bg-slate-100 text-slate-600 ring-slate-200',
        ].join(' ')}
    >
        {value ? yesText : noText}
    </span>
);

const Detail = ({ label, value }) => (
    <div>
        <dt className="text-xs font-bold uppercase tracking-wide text-slate-500">
            {label}
        </dt>

        <dd className="mt-1 text-sm font-semibold text-slate-900">
            {value || '—'}
        </dd>
    </div>
);

export default function Show({
    application = {},
    abilities = {},
    recommendations = [],
}) {
    const approvalForm = useForm({
        recommendation:
            application?.zonal_review
                ?.recommendation ?? '',
        remarks:
            application?.zonal_review?.remarks ?? '',
    });

    const rejectionForm = useForm({
        recommendation:
            application?.zonal_review
                ?.recommendation ??
            'Not Recommended',
        remarks:
            application?.zonal_review?.remarks ?? '',
        rejection_reason:
            application?.zonal_review
                ?.rejection_reason ?? '',
    });

    const startReview = () => {
        if (
            !window.confirm(
                'Start reviewing this transfer application?',
            )
        ) {
            return;
        }

        router.post(
            route(
                'zonal.transfer-applications.start-review',
                application.id,
            ),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const approve = (event) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Approve this application at Zonal level?',
            )
        ) {
            return;
        }

        approvalForm.post(
            route(
                'zonal.transfer-applications.approve',
                application.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    const reject = (event) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Reject this application at Zonal level?',
            )
        ) {
            return;
        }

        rejectionForm.post(
            route(
                'zonal.transfer-applications.reject',
                application.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    const preferences = application?.preferences ?? [];
    const actions = application?.actions ?? [];

    return (
        <AdminLayout>
            <Head
                title={
                    application?.application_number ??
                    'Transfer Application'
                }
            />

            <div className="space-y-6">
                <header className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <Link
                            href={route(
                                'zonal.transfer-applications.index',
                            )}
                            className="mb-3 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-blue-600"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back to applications
                        </Link>

                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-bold text-slate-900">
                                {application?.application_number ??
                                    `Application #${application?.id}`}
                            </h1>

                            <span
                                className={`inline-flex rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset ${
                                    statusClasses[
                                        application?.status
                                    ] ??
                                    'bg-slate-100 text-slate-700 ring-slate-200'
                                }`}
                            >
                                {application?.status ?? 'Unknown'}
                            </span>
                        </div>

                        <p className="mt-2 text-sm text-slate-500">
                            {application?.transfer_cycle
                                ?.name ?? 'Transfer Cycle'}
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        {abilities?.download_pdf ? (
                            <a
                                href={route(
                                    'zonal.transfer-applications.pdf',
                                    application.id,
                                )}
                                className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                <Download className="h-4 w-4" />
                                Download PDF
                            </a>
                        ) : null}

                        {abilities?.start_review ? (
                            <button
                                type="button"
                                onClick={startReview}
                                className="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600"
                            >
                                <PlayCircle className="h-4 w-4" />
                                Start Review
                            </button>
                        ) : null}
                    </div>
                </header>

                <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                    <div className="space-y-6">
                        <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-3">
                                <UserRound className="h-5 w-5 text-blue-600" />

                                <h2 className="text-lg font-bold text-slate-900">
                                    Principal Details
                                </h2>
                            </div>

                            <dl className="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                                <Detail
                                    label="Full Name"
                                    value={
                                        application.principal_name_snapshot ??
                                        application
                                            ?.principal_profile
                                            ?.full_name ??
                                        application
                                            ?.principal_profile
                                            ?.user?.name
                                    }
                                />

                                <Detail
                                    label="NIC"
                                    value={
                                        application.nic_snapshot ??
                                        application
                                            ?.principal_profile
                                            ?.nic
                                    }
                                />

                                <Detail
                                    label="Employee Number"
                                    value={
                                        application.employee_number_snapshot ??
                                        application
                                            ?.principal_profile
                                            ?.employee_number
                                    }
                                />

                                <Detail
                                    label="Service Grade"
                                    value={
                                        application.service_grade_snapshot
                                    }
                                />

                                <Detail
                                    label="Designation"
                                    value={
                                        application.designation_snapshot
                                    }
                                />

                                <Detail
                                    label="Employment Status"
                                    value={
                                        application
                                            ?.principal_profile
                                            ?.employment_status
                                    }
                                />
                            </dl>
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-3">
                                <Building2 className="h-5 w-5 text-blue-600" />

                                <h2 className="text-lg font-bold text-slate-900">
                                    Current Appointment Snapshot
                                </h2>
                            </div>

                            <dl className="mt-6 grid gap-5 sm:grid-cols-2">
                                <Detail
                                    label="Current School"
                                    value={
                                        application.current_school_name_snapshot ??
                                        application
                                            ?.current_school
                                            ?.name
                                    }
                                />

                                <Detail
                                    label="Origin Zone"
                                    value={
                                        application
                                            ?.origin_zone
                                            ?.name ??
                                        application
                                            ?.current_school
                                            ?.division
                                            ?.zone?.name
                                    }
                                />

                                <Detail
                                    label="Appointment Date"
                                    value={formatDate(
                                        application.current_appointment_date_snapshot ??
                                            application.current_appointment_start_date_snapshot,
                                    )}
                                />

                                <Detail
                                    label="Service Duration"
                                    value={
                                        application.service_duration_snapshot
                                    }
                                />
                            </dl>
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-3">
                                <FileText className="h-5 w-5 text-blue-600" />

                                <h2 className="text-lg font-bold text-slate-900">
                                    Transfer Request
                                </h2>
                            </div>

                            <dl className="mt-6 space-y-5">
                                <Detail
                                    label="Transfer Reason"
                                    value={
                                        application.transfer_reason
                                    }
                                />

                                <div>
                                    <dt className="text-xs font-bold uppercase tracking-wide text-slate-500">
                                        Detailed Explanation
                                    </dt>

                                    <dd className="mt-2 whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                                        {application.detailed_explanation ??
                                            '—'}
                                    </dd>
                                </div>

                                <div className="flex flex-wrap gap-3">
                                    <BooleanBadge
                                        value={
                                            application.has_medical_reason
                                        }
                                        yesText="Medical reason"
                                        noText="No medical reason"
                                    />

                                    <BooleanBadge
                                        value={
                                            application.has_spouse_employment_reason
                                        }
                                        yesText="Spouse employment"
                                        noText="No spouse reason"
                                    />

                                    <BooleanBadge
                                        value={
                                            application.is_mutual_transfer
                                        }
                                        yesText="Mutual transfer"
                                        noText="Not mutual"
                                    />
                                </div>

                                {application.is_mutual_transfer ? (
                                    <Detail
                                        label="Mutual Principal NIC"
                                        value={
                                            application.mutual_principal_nic
                                        }
                                    />
                                ) : null}
                            </dl>
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center gap-3">
                                <MapPin className="h-5 w-5 text-blue-600" />

                                <h2 className="text-lg font-bold text-slate-900">
                                    School Preferences
                                </h2>
                            </div>

                            <div className="mt-5 space-y-3">
                                {preferences.length === 0 ? (
                                    <p className="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">
                                        No School preferences were
                                        recorded.
                                    </p>
                                ) : (
                                    preferences
                                        .slice()
                                        .sort(
                                            (a, b) =>
                                                (a.preference_order ??
                                                    0) -
                                                (b.preference_order ??
                                                    0),
                                        )
                                        .map(
                                            (
                                                preference,
                                                index,
                                            ) => (
                                                <div
                                                    key={
                                                        preference.id ??
                                                        index
                                                    }
                                                    className="flex items-start gap-4 rounded-xl border border-slate-200 p-4"
                                                >
                                                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-sm font-bold text-blue-700">
                                                        {preference.preference_order ??
                                                            index +
                                                                1}
                                                    </div>

                                                    <div>
                                                        <p className="font-semibold text-slate-900">
                                                            {preference
                                                                ?.school
                                                                ?.name ??
                                                                preference.school_name_snapshot ??
                                                                'Unknown School'}
                                                        </p>

                                                        <p className="mt-1 text-sm text-slate-500">
                                                            {preference
                                                                ?.school
                                                                ?.division
                                                                ?.name ??
                                                                'Unknown Division'}
                                                            {' · '}
                                                            {preference
                                                                ?.school
                                                                ?.division
                                                                ?.zone
                                                                ?.name ??
                                                                'Unknown Zone'}
                                                        </p>
                                                    </div>
                                                </div>
                                            ),
                                        )
                                )}
                            </div>
                        </section>

                        {abilities?.approve ||
                        abilities?.reject ? (
                            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div className="flex items-center gap-3">
                                    <ShieldCheck className="h-5 w-5 text-blue-600" />

                                    <h2 className="text-lg font-bold text-slate-900">
                                        Zonal Decision
                                    </h2>
                                </div>

                                <div className="mt-6 grid gap-6 lg:grid-cols-2">
                                    {abilities?.approve ? (
                                        <form
                                            onSubmit={
                                                approve
                                            }
                                            className="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5"
                                        >
                                            <h3 className="font-bold text-emerald-900">
                                                Approve
                                                Application
                                            </h3>

                                            <div className="mt-4">
                                                <InputLabel
                                                    htmlFor="approval_recommendation"
                                                    value="Recommendation *"
                                                />

                                                <select
                                                    id="approval_recommendation"
                                                    value={
                                                        approvalForm
                                                            .data
                                                            .recommendation
                                                    }
                                                    onChange={(
                                                        event,
                                                    ) =>
                                                        approvalForm.setData(
                                                            'recommendation',
                                                            event
                                                                .target
                                                                .value,
                                                        )
                                                    }
                                                    className="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                >
                                                    <option value="">
                                                        Select
                                                        recommendation
                                                    </option>

                                                    {(
                                                        recommendations ??
                                                        []
                                                    ).map(
                                                        (
                                                            recommendation,
                                                        ) => (
                                                            <option
                                                                key={
                                                                    recommendation
                                                                }
                                                                value={
                                                                    recommendation
                                                                }
                                                            >
                                                                {
                                                                    recommendation
                                                                }
                                                            </option>
                                                        ),
                                                    )}
                                                </select>

                                                <InputError
                                                    className="mt-2"
                                                    message={
                                                        approvalForm
                                                            .errors
                                                            .recommendation
                                                    }
                                                />
                                            </div>

                                            <div className="mt-4">
                                                <InputLabel
                                                    htmlFor="approval_remarks"
                                                    value="Remarks"
                                                />

                                                <textarea
                                                    id="approval_remarks"
                                                    rows="5"
                                                    value={
                                                        approvalForm
                                                            .data
                                                            .remarks
                                                    }
                                                    onChange={(
                                                        event,
                                                    ) =>
                                                        approvalForm.setData(
                                                            'remarks',
                                                            event
                                                                .target
                                                                .value,
                                                        )
                                                    }
                                                    className="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                />

                                                <InputError
                                                    className="mt-2"
                                                    message={
                                                        approvalForm
                                                            .errors
                                                            .remarks
                                                    }
                                                />
                                            </div>

                                            <button
                                                type="submit"
                                                disabled={
                                                    approvalForm.processing
                                                }
                                                className="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                <CheckCircle2 className="h-4 w-4" />
                                                Approve at
                                                Zonal Level
                                            </button>
                                        </form>
                                    ) : null}

                                    {abilities?.reject ? (
                                        <form
                                            onSubmit={
                                                reject
                                            }
                                            className="rounded-2xl border border-red-200 bg-red-50/50 p-5"
                                        >
                                            <h3 className="font-bold text-red-900">
                                                Reject
                                                Application
                                            </h3>

                                            <div className="mt-4">
                                                <InputLabel
                                                    htmlFor="rejection_reason"
                                                    value="Rejection Reason *"
                                                />

                                                <textarea
                                                    id="rejection_reason"
                                                    rows="5"
                                                    value={
                                                        rejectionForm
                                                            .data
                                                            .rejection_reason
                                                    }
                                                    onChange={(
                                                        event,
                                                    ) =>
                                                        rejectionForm.setData(
                                                            'rejection_reason',
                                                            event
                                                                .target
                                                                .value,
                                                        )
                                                    }
                                                    className="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                                                />

                                                <InputError
                                                    className="mt-2"
                                                    message={
                                                        rejectionForm
                                                            .errors
                                                            .rejection_reason
                                                    }
                                                />
                                            </div>

                                            <div className="mt-4">
                                                <InputLabel
                                                    htmlFor="rejection_remarks"
                                                    value="Additional Remarks"
                                                />

                                                <textarea
                                                    id="rejection_remarks"
                                                    rows="4"
                                                    value={
                                                        rejectionForm
                                                            .data
                                                            .remarks
                                                    }
                                                    onChange={(
                                                        event,
                                                    ) =>
                                                        rejectionForm.setData(
                                                            'remarks',
                                                            event
                                                                .target
                                                                .value,
                                                        )
                                                    }
                                                    className="mt-1 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                                                />

                                                <InputError
                                                    className="mt-2"
                                                    message={
                                                        rejectionForm
                                                            .errors
                                                            .remarks
                                                    }
                                                />
                                            </div>

                                            <button
                                                type="submit"
                                                disabled={
                                                    rejectionForm.processing
                                                }
                                                className="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                <XCircle className="h-4 w-4" />
                                                Reject at
                                                Zonal Level
                                            </button>
                                        </form>
                                    ) : null}
                                </div>
                            </section>
                        ) : null}

                        {application?.zonal_review ? (
                            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h2 className="text-lg font-bold text-slate-900">
                                    Recorded Zonal Review
                                </h2>

                                <dl className="mt-5 grid gap-5 sm:grid-cols-2">
                                    <Detail
                                        label="Reviewer"
                                        value={
                                            application
                                                ?.zonal_review
                                                ?.reviewer?.name
                                        }
                                    />

                                    <Detail
                                        label="Decision"
                                        value={
                                            application
                                                ?.zonal_review
                                                ?.decision
                                        }
                                    />

                                    <Detail
                                        label="Recommendation"
                                        value={
                                            application
                                                ?.zonal_review
                                                ?.recommendation
                                        }
                                    />

                                    <Detail
                                        label="Reviewed At"
                                        value={formatDate(
                                            application
                                                ?.zonal_review
                                                ?.reviewed_at,
                                            true,
                                        )}
                                    />
                                </dl>

                                {application
                                    ?.zonal_review
                                    ?.remarks ? (
                                    <div className="mt-5">
                                        <p className="text-xs font-bold uppercase tracking-wide text-slate-500">
                                            Remarks
                                        </p>

                                        <p className="mt-2 whitespace-pre-line rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                                            {
                                                application
                                                    .zonal_review
                                                    .remarks
                                            }
                                        </p>
                                    </div>
                                ) : null}

                                {application
                                    ?.zonal_review
                                    ?.rejection_reason ? (
                                    <div className="mt-5">
                                        <p className="text-xs font-bold uppercase tracking-wide text-red-600">
                                            Rejection Reason
                                        </p>

                                        <p className="mt-2 whitespace-pre-line rounded-xl bg-red-50 p-4 text-sm leading-6 text-red-700">
                                            {
                                                application
                                                    .zonal_review
                                                    .rejection_reason
                                            }
                                        </p>
                                    </div>
                                ) : null}
                            </section>
                        ) : null}
                    </div>

                    <aside className="space-y-6">
                        <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div className="flex items-center gap-3">
                                <Clock3 className="h-5 w-5 text-blue-600" />

                                <h2 className="font-bold text-slate-900">
                                    Application Timeline
                                </h2>
                            </div>

                            <dl className="mt-5 space-y-4">
                                <Detail
                                    label="Created"
                                    value={formatDate(
                                        application.created_at,
                                        true,
                                    )}
                                />

                                <Detail
                                    label="Submitted"
                                    value={formatDate(
                                        application.submitted_at,
                                        true,
                                    )}
                                />

                                <Detail
                                    label="Review Started"
                                    value={formatDate(
                                        application
                                            ?.zonal_review
                                            ?.review_started_at,
                                        true,
                                    )}
                                />

                                <Detail
                                    label="Zonal Decision"
                                    value={formatDate(
                                        application
                                            ?.zonal_review
                                            ?.reviewed_at,
                                        true,
                                    )}
                                />
                            </dl>
                        </section>

                        <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 className="font-bold text-slate-900">
                                Status History
                            </h2>

                            <div className="mt-5 space-y-5">
                                {actions.length === 0 ? (
                                    <p className="text-sm text-slate-500">
                                        No status history was
                                        recorded.
                                    </p>
                                ) : (
                                    actions.map(
                                        (action, index) => (
                                            <div
                                                key={
                                                    action.id ??
                                                    index
                                                }
                                                className="relative pl-6"
                                            >
                                                <span className="absolute left-0 top-1.5 h-3 w-3 rounded-full bg-blue-600 ring-4 ring-blue-100" />

                                                {index <
                                                actions.length -
                                                    1 ? (
                                                    <span className="absolute left-[5px] top-4 h-full w-px bg-slate-200" />
                                                ) : null}

                                                <p className="text-sm font-bold text-slate-900">
                                                    {action.to_status ??
                                                        action.action}
                                                </p>

                                                <p className="mt-1 text-xs text-slate-500">
                                                    {action
                                                        ?.actor
                                                        ?.name ??
                                                        'System'}
                                                    {' · '}
                                                    {formatDate(
                                                        action.acted_at,
                                                        true,
                                                    )}
                                                </p>

                                                {action.remarks ? (
                                                    <p className="mt-2 text-sm leading-5 text-slate-600">
                                                        {
                                                            action.remarks
                                                        }
                                                    </p>
                                                ) : null}
                                            </div>
                                        ),
                                    )
                                )}
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </AdminLayout>
    );
}
