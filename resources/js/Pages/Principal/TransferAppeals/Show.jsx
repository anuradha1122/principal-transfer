import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Download,
    FileEdit,
    Send,
    Trash2,
    Undo2,
} from 'lucide-react';

const badgeClasses = {
    Draft: 'bg-slate-100 text-slate-700',
    Submitted: 'bg-blue-100 text-blue-700',
    'Under Review': 'bg-amber-100 text-amber-700',
    'Returned for Clarification': 'bg-orange-100 text-orange-700',
    Resubmitted: 'bg-violet-100 text-violet-700',
    Approved: 'bg-emerald-100 text-emerald-700',
    Rejected: 'bg-red-100 text-red-700',
    Withdrawn: 'bg-slate-200 text-slate-700',
};

function Detail({ label, value }) {
    return (
        <div>
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </p>
            <p className="mt-1 whitespace-pre-wrap text-sm font-medium text-slate-800">
                {value || '—'}
            </p>
        </div>
    );
}

export default function Show({ appeal = {} }) {
    const submitForm = useForm({
        declaration: false,
    });

    const clarificationForm = useForm({
        clarification_response: appeal.clarification_response ?? '',
        documents: [],
    });

    const submitAppeal = () => {
        if (
            !window.confirm(
                'Submit this appeal? It will be locked from further editing.',
            )
        ) {
            return;
        }

        submitForm.post(
            route('principal.transfer-appeals.submit', appeal.id),
        );
    };

    const withdrawAppeal = () => {
        if (!window.confirm('Withdraw this transfer appeal?')) {
            return;
        }

        router.post(
            route('principal.transfer-appeals.withdraw', appeal.id),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const deleteDraft = () => {
        if (
            !window.confirm(
                'Permanently delete this Draft appeal and its documents?',
            )
        ) {
            return;
        }

        router.delete(
            route('principal.transfer-appeals.destroy', appeal.id),
        );
    };

    const submitClarification = (event) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Submit this clarification and resubmit the appeal?',
            )
        ) {
            return;
        }

        clarificationForm.post(
            route('principal.transfer-appeals.clarify', appeal.id),
            {
                forceFormData: true,
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout>
            <Head title={appeal.appeal_number ?? 'Transfer Appeal'} />

            <div className="space-y-6">
                <header className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="text-2xl font-bold text-slate-900">
                                {appeal.appeal_number}
                            </h1>

                            <span
                                className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${
                                    badgeClasses[appeal.status] ??
                                    'bg-slate-100 text-slate-700'
                                }`}
                            >
                                {appeal.status}
                            </span>
                        </div>

                        <p className="mt-1 text-sm text-slate-500">
                            Application{' '}
                            {appeal.transfer_application
                                ?.application_number ?? '—'}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-3">
                        <Link
                            href={route('principal.transfer-appeals.index')}
                            className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back
                        </Link>

                        {appeal.status === 'Draft' && (
                            <>
                                <Link
                                    href={route(
                                        'principal.transfer-appeals.edit',
                                        appeal.id,
                                    )}
                                    className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                                >
                                    <FileEdit className="h-4 w-4" />
                                    Edit Draft
                                </Link>

                                <button
                                    type="button"
                                    onClick={deleteDraft}
                                    className="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                                >
                                    <Trash2 className="h-4 w-4" />
                                    Delete
                                </button>
                            </>
                        )}

                        {[
                            'Submitted',
                            'Returned for Clarification',
                            'Resubmitted',
                        ].includes(appeal.status) && (
                            <button
                                type="button"
                                onClick={withdrawAppeal}
                                className="inline-flex items-center justify-center gap-2 rounded-xl border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-50"
                            >
                                <Undo2 className="h-4 w-4" />
                                Withdraw
                            </button>
                        )}
                    </div>
                </header>

                <div className="grid gap-6 xl:grid-cols-3">
                    <div className="space-y-6 xl:col-span-2">
                        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-bold text-slate-900">
                                Appeal Details
                            </h2>

                            <div className="mt-5 grid gap-5 md:grid-cols-2">
                                <Detail
                                    label="Appeal Reason"
                                    value={appeal.appeal_reason}
                                />
                                <Detail
                                    label="Requested Outcome"
                                    value={appeal.requested_outcome}
                                />
                                <div className="md:col-span-2">
                                    <Detail
                                        label="Detailed Explanation"
                                        value={appeal.appeal_details}
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-bold text-slate-900">
                                Supporting Documents
                            </h2>

                            <div className="mt-4 space-y-3">
                                {(appeal.documents ?? []).map((document) => (
                                    <div
                                        key={document.id}
                                        className="flex items-center justify-between rounded-xl border border-slate-200 p-4"
                                    >
                                        <div>
                                            <p className="text-sm font-semibold text-slate-800">
                                                {document.original_name}
                                            </p>
                                            <p className="mt-1 text-xs text-slate-500">
                                                {document.mime_type ?? 'File'}
                                            </p>
                                        </div>

                                        <a
                                            href={route(
                                                'principal.transfer-appeals.documents.download',
                                                [
                                                    appeal.id,
                                                    document.id,
                                                ],
                                            )}
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                        >
                                            <Download className="h-4 w-4" />
                                            Download
                                        </a>
                                    </div>
                                ))}

                                {(appeal.documents ?? []).length === 0 && (
                                    <p className="text-sm text-slate-500">
                                        No supporting documents uploaded.
                                    </p>
                                )}
                            </div>
                        </div>

                        {(appeal.clarification_request ||
                            appeal.status ===
                                'Returned for Clarification') && (
                            <div className="rounded-2xl border border-orange-200 bg-orange-50 p-6 shadow-sm">
                                <h2 className="text-lg font-bold text-orange-900">
                                    Clarification
                                </h2>

                                <div className="mt-4">
                                    <Detail
                                        label="Clarification Requested"
                                        value={appeal.clarification_request}
                                    />
                                </div>

                                {appeal.status ===
                                'Returned for Clarification' ? (
                                    <form
                                        onSubmit={submitClarification}
                                        className="mt-6 space-y-4"
                                    >
                                        <div>
                                            <label className="text-sm font-semibold text-slate-700">
                                                Clarification Response
                                            </label>
                                            <textarea
                                                rows="6"
                                                value={
                                                    clarificationForm.data
                                                        .clarification_response
                                                }
                                                onChange={(event) =>
                                                    clarificationForm.setData(
                                                        'clarification_response',
                                                        event.target.value,
                                                    )
                                                }
                                                className="mt-2 w-full rounded-xl border-orange-300 focus:border-orange-500 focus:ring-orange-500"
                                            />
                                            {clarificationForm.errors
                                                .clarification_response && (
                                                <p className="mt-2 text-sm text-red-600">
                                                    {
                                                        clarificationForm
                                                            .errors
                                                            .clarification_response
                                                    }
                                                </p>
                                            )}
                                        </div>

                                        <input
                                            type="file"
                                            multiple
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            onChange={(event) =>
                                                clarificationForm.setData(
                                                    'documents',
                                                    Array.from(
                                                        event.target.files ??
                                                            [],
                                                    ),
                                                )
                                            }
                                            className="block w-full rounded-xl border border-orange-300 bg-white p-3 text-sm"
                                        />

                                        <button
                                            type="submit"
                                            disabled={
                                                clarificationForm.processing
                                            }
                                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50"
                                        >
                                            <Send className="h-4 w-4" />
                                            Submit Clarification
                                        </button>
                                    </form>
                                ) : (
                                    <div className="mt-5">
                                        <Detail
                                            label="Clarification Response"
                                            value={
                                                appeal.clarification_response
                                            }
                                        />
                                    </div>
                                )}
                            </div>
                        )}

                        {(appeal.status === 'Approved' ||
                            appeal.status === 'Rejected') && (
                            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h2 className="text-lg font-bold text-slate-900">
                                    Appeal Decision
                                </h2>

                                <div className="mt-5 grid gap-5 md:grid-cols-2">
                                    <Detail
                                        label="Outcome"
                                        value={appeal.decision_outcome}
                                    />
                                    <Detail
                                        label="Decision Reference"
                                        value={
                                            appeal.revised_decision_reference
                                        }
                                    />
                                    <Detail
                                        label="Revised School"
                                        value={appeal.revised_school?.name}
                                    />
                                    <Detail
                                        label="Revised Effective Date"
                                        value={
                                            appeal.revised_effective_date
                                        }
                                    />
                                    <Detail
                                        label="Revised Appointment Type"
                                        value={
                                            appeal.revised_appointment_type
                                        }
                                    />
                                    <Detail
                                        label="Decision Remarks"
                                        value={appeal.decision_remarks}
                                    />
                                    <div className="md:col-span-2">
                                        <Detail
                                            label="Rejection Reason"
                                            value={appeal.rejection_reason}
                                        />
                                    </div>
                                </div>
                            </div>
                        )}

                        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-bold text-slate-900">
                                Action History
                            </h2>

                            <div className="mt-5 space-y-5">
                                {(appeal.actions ?? []).map((action) => (
                                    <div
                                        key={action.id}
                                        className="border-l-2 border-blue-200 pl-4"
                                    >
                                        <p className="font-semibold text-slate-800">
                                            {action.action}
                                        </p>
                                        <p className="mt-1 text-sm text-slate-500">
                                            {action.actor?.name ?? 'System'} ·{' '}
                                            {action.acted_at
                                                ? new Date(
                                                      action.acted_at,
                                                  ).toLocaleString()
                                                : '—'}
                                        </p>
                                        {action.remarks && (
                                            <p className="mt-2 whitespace-pre-wrap text-sm text-slate-600">
                                                {action.remarks}
                                            </p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="space-y-6">
                        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-bold text-slate-900">
                                Original Decision
                            </h2>

                            <div className="mt-5 space-y-5">
                                <Detail
                                    label="Application Number"
                                    value={
                                        appeal.transfer_application
                                            ?.application_number
                                    }
                                />
                                <Detail
                                    label="Final Status"
                                    value={
                                        appeal.transfer_application?.status
                                    }
                                />
                                <Detail
                                    label="Decision Reference"
                                    value={
                                        appeal.transfer_application
                                            ?.transfer_board_decision
                                            ?.decision_reference
                                    }
                                />
                                <Detail
                                    label="Recommended School"
                                    value={
                                        appeal.transfer_application
                                            ?.transfer_board_decision
                                            ?.recommended_school?.name
                                    }
                                />
                                <Detail
                                    label="Effective Date"
                                    value={
                                        appeal.transfer_application
                                            ?.transfer_board_decision
                                            ?.effective_date
                                    }
                                />
                            </div>
                        </div>

                        {appeal.status === 'Draft' && (
                            <div className="rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                                <h2 className="text-lg font-bold text-blue-900">
                                    Submit Appeal
                                </h2>

                                <label className="mt-4 flex items-start gap-3">
                                    <input
                                        type="checkbox"
                                        checked={
                                            submitForm.data.declaration
                                        }
                                        onChange={(event) =>
                                            submitForm.setData(
                                                'declaration',
                                                event.target.checked,
                                            )
                                        }
                                        className="mt-1 rounded border-blue-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span className="text-sm text-blue-900">
                                        I confirm that the information provided
                                        in this appeal is accurate and complete.
                                    </span>
                                </label>

                                {submitForm.errors.declaration && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {submitForm.errors.declaration}
                                    </p>
                                )}

                                <button
                                    type="button"
                                    onClick={submitAppeal}
                                    disabled={
                                        submitForm.processing ||
                                        !submitForm.data.declaration
                                    }
                                    className="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <Send className="h-4 w-4" />
                                    Submit Appeal
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
