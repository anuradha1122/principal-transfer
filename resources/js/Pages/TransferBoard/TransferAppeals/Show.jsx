import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    CheckCircle2,
    Download,
    RotateCcw,
    Search,
    XCircle,
} from 'lucide-react';

const badgeClasses = {
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

export default function Show({
    appeal = {},
    schools = [],
}) {
    const returnForm = useForm({
        clarification_request: '',
    });

    const approveForm = useForm({
        decision_remarks: '',
        revised_school_id: '',
        revised_effective_date: '',
        revised_appointment_type: '',
        revised_decision_reference: '',
    });

    const rejectForm = useForm({
        rejection_reason: '',
        decision_remarks: '',
    });

    const startReview = () => {
        if (!window.confirm('Start reviewing this appeal?')) {
            return;
        }

        router.post(
            route(
                'transfer-board.transfer-appeals.start-review',
                appeal.id,
            ),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const submitReturn = (event) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Return this appeal to the Principal for clarification?',
            )
        ) {
            return;
        }

        returnForm.post(
            route(
                'transfer-board.transfer-appeals.return',
                appeal.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    const submitApproval = (event) => {
        event.preventDefault();

        if (
            !window.confirm(
                'Approve this appeal and unpublish the existing official documents?',
            )
        ) {
            return;
        }

        approveForm.post(
            route(
                'transfer-board.transfer-appeals.approve',
                appeal.id,
            ),
            {
                preserveScroll: true,
            },
        );
    };

    const submitRejection = (event) => {
        event.preventDefault();

        if (!window.confirm('Reject this transfer appeal?')) {
            return;
        }

        rejectForm.post(
            route(
                'transfer-board.transfer-appeals.reject',
                appeal.id,
            ),
            {
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
                                className={`rounded-full px-3 py-1 text-xs font-semibold ${
                                    badgeClasses[appeal.status] ??
                                    'bg-slate-100 text-slate-700'
                                }`}
                            >
                                {appeal.status}
                            </span>
                        </div>

                        <p className="mt-1 text-sm text-slate-500">
                            {appeal.principal_profile?.full_name ??
                                appeal.principal_profile?.user?.name ??
                                'Principal'}
                        </p>
                    </div>

                    <Link
                        href={route(
                            'transfer-board.transfer-appeals.index',
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back
                    </Link>
                </header>

                <div className="grid gap-6 xl:grid-cols-2">
                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 className="text-lg font-bold text-slate-900">
                            Appeal Details
                        </h2>

                        <div className="mt-5 grid gap-5">
                            <Detail
                                label="Appeal Reason"
                                value={appeal.appeal_reason}
                            />
                            <Detail
                                label="Detailed Explanation"
                                value={appeal.appeal_details}
                            />
                            <Detail
                                label="Requested Outcome"
                                value={appeal.requested_outcome}
                            />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 className="text-lg font-bold text-slate-900">
                            Original Decision
                        </h2>

                        <div className="mt-5 grid gap-5 md:grid-cols-2">
                            <Detail
                                label="Application"
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
                            <Detail
                                label="Appointment Type"
                                value={
                                    appeal.transfer_application
                                        ?.transfer_board_decision
                                        ?.appointment_type
                                }
                            />
                        </div>
                    </div>
                </div>

                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="text-lg font-bold text-slate-900">
                        Supporting Documents
                    </h2>

                    <div className="mt-4 grid gap-3 md:grid-cols-2">
                        {(appeal.documents ?? []).map((document) => (
                            <a
                                key={document.id}
                                href={route(
                                    'transfer-board.transfer-appeals.documents.download',
                                    [appeal.id, document.id],
                                )}
                                className="flex items-center justify-between rounded-xl border border-slate-200 p-4 hover:border-blue-200 hover:bg-blue-50"
                            >
                                <span className="text-sm font-semibold text-slate-700">
                                    {document.original_name}
                                </span>
                                <Download className="h-4 w-4 text-blue-600" />
                            </a>
                        ))}

                        {(appeal.documents ?? []).length === 0 && (
                            <p className="text-sm text-slate-500">
                                No supporting documents.
                            </p>
                        )}
                    </div>
                </div>

                {(appeal.clarification_request ||
                    appeal.clarification_response) && (
                    <div className="rounded-2xl border border-orange-200 bg-orange-50 p-6 shadow-sm">
                        <h2 className="text-lg font-bold text-orange-900">
                            Clarification
                        </h2>

                        <div className="mt-5 grid gap-5 md:grid-cols-2">
                            <Detail
                                label="Request"
                                value={appeal.clarification_request}
                            />
                            <Detail
                                label="Response"
                                value={appeal.clarification_response}
                            />
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

                {['Submitted', 'Resubmitted'].includes(
                    appeal.status,
                ) && (
                    <div className="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                        <h2 className="text-lg font-bold text-amber-900">
                            Start Review
                        </h2>
                        <p className="mt-2 text-sm text-amber-800">
                            Starting review assigns this appeal to you and
                            changes its status to Under Review.
                        </p>

                        <button
                            type="button"
                            onClick={startReview}
                            className="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700"
                        >
                            <Search className="h-4 w-4" />
                            Start Review
                        </button>
                    </div>
                )}

                {appeal.status === 'Under Review' && (
                    <div className="grid gap-6 xl:grid-cols-3">
                        <form
                            onSubmit={submitReturn}
                            className="rounded-2xl border border-orange-200 bg-white p-6 shadow-sm"
                        >
                            <h2 className="text-lg font-bold text-orange-700">
                                Return for Clarification
                            </h2>

                            <textarea
                                rows="7"
                                value={
                                    returnForm.data.clarification_request
                                }
                                onChange={(event) =>
                                    returnForm.setData(
                                        'clarification_request',
                                        event.target.value,
                                    )
                                }
                                placeholder="Explain what clarification is required."
                                className="mt-4 w-full rounded-xl border-orange-300 focus:border-orange-500 focus:ring-orange-500"
                            />

                            {returnForm.errors.clarification_request && (
                                <p className="mt-2 text-sm text-red-600">
                                    {
                                        returnForm.errors
                                            .clarification_request
                                    }
                                </p>
                            )}

                            <button
                                type="submit"
                                disabled={returnForm.processing}
                                className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50"
                            >
                                <RotateCcw className="h-4 w-4" />
                                Return Appeal
                            </button>
                        </form>

                        <form
                            onSubmit={submitApproval}
                            className="rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm"
                        >
                            <h2 className="text-lg font-bold text-emerald-700">
                                Approve Appeal
                            </h2>

                            <div className="mt-4 space-y-4">
                                <textarea
                                    rows="4"
                                    value={
                                        approveForm.data.decision_remarks
                                    }
                                    onChange={(event) =>
                                        approveForm.setData(
                                            'decision_remarks',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Decision remarks"
                                    className="w-full rounded-xl border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500"
                                />

                                <select
                                    value={
                                        approveForm.data.revised_school_id
                                    }
                                    onChange={(event) =>
                                        approveForm.setData(
                                            'revised_school_id',
                                            event.target.value,
                                        )
                                    }
                                    className="w-full rounded-xl border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="">
                                        No revised school
                                    </option>
                                    {(schools ?? []).map((school) => (
                                        <option
                                            key={school.id}
                                            value={school.id}
                                        >
                                            {school.name}
                                            {school.census_number
                                                ? ` (${school.census_number})`
                                                : ''}
                                        </option>
                                    ))}
                                </select>

                                <input
                                    type="date"
                                    value={
                                        approveForm.data
                                            .revised_effective_date
                                    }
                                    onChange={(event) =>
                                        approveForm.setData(
                                            'revised_effective_date',
                                            event.target.value,
                                        )
                                    }
                                    className="w-full rounded-xl border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500"
                                />

                                <input
                                    value={
                                        approveForm.data
                                            .revised_appointment_type
                                    }
                                    onChange={(event) =>
                                        approveForm.setData(
                                            'revised_appointment_type',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Revised appointment type"
                                    className="w-full rounded-xl border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500"
                                />

                                <input
                                    value={
                                        approveForm.data
                                            .revised_decision_reference
                                    }
                                    onChange={(event) =>
                                        approveForm.setData(
                                            'revised_decision_reference',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Revised decision reference"
                                    className="w-full rounded-xl border-emerald-300 focus:border-emerald-500 focus:ring-emerald-500"
                                />

                                {Object.values(
                                    approveForm.errors ?? {},
                                ).map((error, index) => (
                                    <p
                                        key={index}
                                        className="text-sm text-red-600"
                                    >
                                        {error}
                                    </p>
                                ))}
                            </div>

                            <button
                                type="submit"
                                disabled={approveForm.processing}
                                className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-50"
                            >
                                <CheckCircle2 className="h-4 w-4" />
                                Approve Appeal
                            </button>
                        </form>

                        <form
                            onSubmit={submitRejection}
                            className="rounded-2xl border border-red-200 bg-white p-6 shadow-sm"
                        >
                            <h2 className="text-lg font-bold text-red-700">
                                Reject Appeal
                            </h2>

                            <textarea
                                rows="6"
                                value={
                                    rejectForm.data.rejection_reason
                                }
                                onChange={(event) =>
                                    rejectForm.setData(
                                        'rejection_reason',
                                        event.target.value,
                                    )
                                }
                                placeholder="Rejection reason"
                                className="mt-4 w-full rounded-xl border-red-300 focus:border-red-500 focus:ring-red-500"
                            />

                            <textarea
                                rows="4"
                                value={
                                    rejectForm.data.decision_remarks
                                }
                                onChange={(event) =>
                                    rejectForm.setData(
                                        'decision_remarks',
                                        event.target.value,
                                    )
                                }
                                placeholder="Additional decision remarks"
                                className="mt-4 w-full rounded-xl border-red-300 focus:border-red-500 focus:ring-red-500"
                            />

                            {Object.values(
                                rejectForm.errors ?? {},
                            ).map((error, index) => (
                                <p
                                    key={index}
                                    className="mt-2 text-sm text-red-600"
                                >
                                    {error}
                                </p>
                            ))}

                            <button
                                type="submit"
                                disabled={rejectForm.processing}
                                className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                            >
                                <XCircle className="h-4 w-4" />
                                Reject Appeal
                            </button>
                        </form>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}
