import AdminLayout from '@/Layouts/AdminLayout';
import {
    CheckCircle2,
    Download,
    Pencil,
    Send,
    Trash2,
} from 'lucide-react';
import {
    Link,
    router,
    useForm,
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
            month: 'long',
            day: '2-digit',
        },
    ).format(date);
}

function formatDateTime(value) {
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
            hour: '2-digit',
            minute: '2-digit',
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

export default function Show({
    application,
}) {
    const canDownloadPdf = Boolean(
        application?.submitted_at,
    );

    const submitForm = useForm({
        declaration_accepted: false,
    });

    const withdrawForm = useForm({
        withdrawal_reason: '',
    });

    const submitApplication = (
        event,
    ) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Submit this transfer application? You will not be able to edit it afterward.',
            )
        ) {
            return;
        }

        submitForm.post(
            route(
                'principal.transfer-applications.submit',
                application.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    const withdraw = (
        event,
    ) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Withdraw this transfer application?',
            )
        ) {
            return;
        }

        withdrawForm.post(
            route(
                'principal.transfer-applications.withdraw',
                application.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    const remove = () => {
        if (
            !window.confirm(
                'Delete this draft application?',
            )
        ) {
            return;
        }

        router.delete(
            route(
                'principal.transfer-applications.destroy',
                application.id,
            ),
        );
    };

    const canWithdraw = [
        'Submitted',
        'Zonal Review',
    ].includes(
        application.status,
    );

    return (
        <AdminLayout
            title={
                application.application_number ||
                'Draft Transfer Application'
            }
            header={
                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            {application.application_number ||
                                `Draft Application #${application.id}`}
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            {application
                                .transfer_cycle
                                ?.name ||
                                'Transfer cycle not available'}
                        </p>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        {application.status ===
                            'Draft' && (
                            <>
                                <Link
                                    href={route(
                                        'principal.transfer-applications.edit',
                                        application.id,
                                    )}
                                    className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    <Pencil className="h-4 w-4" />

                                    Edit Draft
                                </Link>

                                <button
                                    type="button"
                                    onClick={remove}
                                    className="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100"
                                >
                                    <Trash2 className="h-4 w-4" />

                                    Delete
                                </button>
                            </>
                        )}

                        {canDownloadPdf && (
                            <a
                                href={route(
                                    'principal.transfer-applications.pdf',
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
                                'inline-flex rounded-full px-4 py-2 text-sm font-semibold',
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
            <div className="grid gap-6 lg:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 className="font-bold text-slate-900">
                        Transfer Request
                    </h2>

                    <dl className="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <dt className="text-xs font-semibold uppercase text-slate-500">
                                Status
                            </dt>

                            <dd className="mt-1 font-semibold text-slate-800">
                                {application.status}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-xs font-semibold uppercase text-slate-500">
                                Transfer Reason
                            </dt>

                            <dd className="mt-1 font-semibold text-slate-800">
                                {application.transfer_reason ||
                                    'Not available'}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-xs font-semibold uppercase text-slate-500">
                                Current School
                            </dt>

                            <dd className="mt-1 font-semibold text-slate-800">
                                {application
                                    .current_school
                                    ?.name ||
                                    'Not available'}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-xs font-semibold uppercase text-slate-500">
                                Current Designation
                            </dt>

                            <dd className="mt-1 font-semibold text-slate-800">
                                {application.current_designation ||
                                    'Not available'}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-xs font-semibold uppercase text-slate-500">
                                Current Appointment Start
                            </dt>

                            <dd className="mt-1 font-semibold text-slate-800">
                                {formatDate(
                                    application.current_appointment_start_date,
                                )}
                            </dd>
                        </div>

                        <div>
                            <dt className="text-xs font-semibold uppercase text-slate-500">
                                Service at Current School
                            </dt>

                            <dd className="mt-1 font-semibold text-slate-800">
                                {application.current_school_service_months ??
                                    0}{' '}
                                month(s)
                            </dd>
                        </div>
                    </dl>

                    <div className="mt-6 border-t border-slate-200 pt-5">
                        <p className="text-xs font-semibold uppercase text-slate-500">
                            Detailed Explanation
                        </p>

                        <p className="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">
                            {application.reason_details ||
                                'Not available'}
                        </p>
                    </div>

                    {application.principal_remarks && (
                        <div className="mt-6 border-t border-slate-200 pt-5">
                            <p className="text-xs font-semibold uppercase text-slate-500">
                                Additional Remarks
                            </p>

                            <p className="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">
                                {
                                    application.principal_remarks
                                }
                            </p>
                        </div>
                    )}
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        Application Timeline
                    </h2>

                    <div className="mt-5 space-y-5 text-sm">
                        <div>
                            <p className="text-slate-500">
                                Created
                            </p>

                            <p className="font-semibold text-slate-800">
                                {formatDateTime(
                                    application.created_at,
                                )}
                            </p>
                        </div>

                        <div>
                            <p className="text-slate-500">
                                Submitted
                            </p>

                            <p className="font-semibold text-slate-800">
                                {formatDateTime(
                                    application.submitted_at,
                                )}
                            </p>
                        </div>

                        <div>
                            <p className="text-slate-500">
                                PDF Generated
                            </p>

                            <p className="font-semibold text-slate-800">
                                {formatDateTime(
                                    application
                                        .submitted_pdf_generated_at,
                                )}
                            </p>
                        </div>

                        <div>
                            <p className="text-slate-500">
                                Withdrawn
                            </p>

                            <p className="font-semibold text-slate-800">
                                {formatDateTime(
                                    application.withdrawn_at,
                                )}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            {canDownloadPdf && (
                <section className="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 className="font-bold text-emerald-900">
                                Submitted Application PDF
                            </h2>

                            <p className="mt-1 text-sm leading-6 text-emerald-700">
                                Download the official PDF copy
                                generated when this application
                                was submitted.
                            </p>
                        </div>

                        <a
                            href={route(
                                'principal.transfer-applications.pdf',
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

            <section className="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="border-b border-slate-200 px-6 py-5">
                    <h2 className="font-bold text-slate-900">
                        School Preferences
                    </h2>

                    <p className="mt-1 text-sm text-slate-500">
                        Schools selected in priority order
                    </p>
                </div>

                <div className="divide-y divide-slate-100">
                    {(application.preferences ??
                        []).map(
                        (
                            preference,
                        ) => (
                            <div
                                key={
                                    preference.id
                                }
                                className="flex gap-4 px-6 py-5"
                            >
                                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                    {
                                        preference.preference_order
                                    }
                                </div>

                                <div>
                                    <p className="font-semibold text-slate-900">
                                        {preference
                                            .school
                                            ?.name ||
                                            'School not available'}
                                    </p>

                                    <p className="mt-1 text-xs text-slate-500">
                                        {preference
                                            .school
                                            ?.division
                                            ?.name ||
                                            'Division not available'}{' '}
                                        Division ·{' '}
                                        {preference
                                            .school
                                            ?.division
                                            ?.zone
                                            ?.name ||
                                            'Zone not available'}{' '}
                                        Zone
                                    </p>

                                    {preference.preference_reason && (
                                        <p className="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">
                                            {
                                                preference.preference_reason
                                            }
                                        </p>
                                    )}
                                </div>
                            </div>
                        ),
                    )}
                </div>

                {(application.preferences ??
                    []).length === 0 && (
                    <div className="px-6 py-12 text-center text-sm text-slate-500">
                        No school preferences
                        recorded.
                    </div>
                )}
            </section>

            {application.status ===
                'Draft' && (
                <form
                    onSubmit={
                        submitApplication
                    }
                    className="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-6"
                >
                    <div className="flex items-start gap-3">
                        <CheckCircle2 className="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />

                        <div className="flex-1">
                            <h2 className="font-bold text-emerald-900">
                                Final Submission
                            </h2>

                            <p className="mt-1 text-sm leading-6 text-emerald-700">
                                Confirm that the
                                information is correct.
                                After submission, the
                                application cannot be
                                edited.
                            </p>

                            <label className="mt-4 flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    checked={
                                        submitForm
                                            .data
                                            .declaration_accepted
                                    }
                                    onChange={(
                                        event,
                                    ) =>
                                        submitForm.setData(
                                            'declaration_accepted',
                                            event
                                                .target
                                                .checked,
                                        )
                                    }
                                    className="mt-1 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"
                                />

                                <span className="text-sm text-emerald-900">
                                    I certify that
                                    the information
                                    provided is
                                    accurate.
                                </span>
                            </label>

                            {submitForm
                                .errors
                                .declaration_accepted && (
                                <p className="mt-2 text-sm text-red-600">
                                    {
                                        submitForm
                                            .errors
                                            .declaration_accepted
                                    }
                                </p>
                            )}

                            <button
                                type="submit"
                                disabled={
                                    submitForm.processing
                                }
                                className="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Send className="h-4 w-4" />

                                {submitForm.processing
                                    ? 'Submitting...'
                                    : 'Submit Application'}
                            </button>
                        </div>
                    </div>
                </form>
            )}

            {canWithdraw && (
                <form
                    onSubmit={withdraw}
                    className="mt-6 rounded-2xl border border-red-200 bg-red-50 p-6"
                >
                    <h2 className="font-bold text-red-900">
                        Withdraw Application
                    </h2>

                    <p className="mt-1 text-sm text-red-700">
                        The submitted PDF will remain
                        available after withdrawal.
                    </p>

                    <textarea
                        rows="4"
                        value={
                            withdrawForm.data
                                .withdrawal_reason
                        }
                        onChange={(event) =>
                            withdrawForm.setData(
                                'withdrawal_reason',
                                event.target.value,
                            )
                        }
                        placeholder="Explain why you are withdrawing"
                        className="mt-4 block w-full rounded-md border-red-200 shadow-sm focus:border-red-400 focus:ring-red-400"
                    />

                    {withdrawForm.errors
                        .withdrawal_reason && (
                        <p className="mt-2 text-sm text-red-600">
                            {
                                withdrawForm.errors
                                    .withdrawal_reason
                            }
                        </p>
                    )}

                    <button
                        type="submit"
                        disabled={
                            withdrawForm.processing
                        }
                        className="mt-4 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {withdrawForm.processing
                            ? 'Withdrawing...'
                            : 'Withdraw Application'}
                    </button>
                </form>
            )}

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
        </AdminLayout>
    );
}
