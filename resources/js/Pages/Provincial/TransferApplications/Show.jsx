import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import AdminLayout from '@/Layouts/AdminLayout';
import { Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    CheckCircle2,
    Download,
    PlayCircle,
    RotateCcw,
    XCircle,
} from 'lucide-react';

function Detail({
    label,
    value,
}) {
    return (
        <div>
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                {label}
            </p>

            <p className="mt-1 text-sm font-medium text-slate-900">
                {value || '—'}
            </p>
        </div>
    );
}

export default function Show({
    application,
    can = {},
}) {
    const approveForm = useForm({
        recommendation:
            'Recommended for Transfer Board consideration.',
        remarks: '',
    });

    const rejectForm = useForm({
        recommendation:
            'Not Recommended',
        remarks: '',
        rejection_reason: '',
    });

    const returnForm = useForm({
        remarks: '',
        return_reason: '',
    });

    const startReview = () => {
        approveForm.post(
            route(
                'provincial.transfer-applications.start-review',
                application.id
            ),
            {
                preserveScroll: true,
            }
        );
    };

    return (
        <AdminLayout
            title="Provincial Application Review"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Provincial Application Review
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            {application.application_number}
                        </p>
                    </div>

                    <div className="flex gap-3">
                        <Link
                            href={route(
                                'provincial.transfer-applications.index'
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-white"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back
                        </Link>

                        {application.pdf_path && (
                            <a
                                href={route(
                                    'provincial.transfer-applications.pdf',
                                    application.id
                                )}
                                className="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white"
                            >
                                <Download className="h-4 w-4" />
                                Download PDF
                            </a>
                        )}
                    </div>
                </div>
            }
        >
            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div className="flex items-center justify-between">
                    <div>
                        <p className="text-sm font-semibold text-slate-500">
                            Current Status
                        </p>

                        <h2 className="mt-2 text-2xl font-bold text-slate-900">
                            {application.status}
                        </h2>
                    </div>

                    {can.start_review && (
                        <button
                            type="button"
                            onClick={startReview}
                            disabled={
                                approveForm.processing
                            }
                            className="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-700"
                        >
                            <PlayCircle className="h-5 w-5" />
                            Start Provincial Review
                        </button>
                    )}
                </div>
            </section>

            <div className="mt-6 grid gap-6 xl:grid-cols-3">
                <div className="space-y-6 xl:col-span-2">
                    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 className="text-lg font-bold text-slate-900">
                            Principal and Appointment
                        </h3>

                        <div className="mt-5 grid gap-5 sm:grid-cols-2">
                            <Detail
                                label="Principal Name"
                                value={
                                    application.principal_name
                                }
                            />

                            <Detail
                                label="NIC"
                                value={
                                    application.nic
                                }
                            />

                            <Detail
                                label="Employee Number"
                                value={
                                    application.employee_number
                                }
                            />

                            <Detail
                                label="Designation"
                                value={
                                    application.current_designation
                                }
                            />

                            <Detail
                                label="Service Grade"
                                value={
                                    application.service_grade
                                }
                            />

                            <Detail
                                label="Current School"
                                value={
                                    application.current_school?.name
                                }
                            />

                            <Detail
                                label="Origin Zone"
                                value={
                                    application.origin_zone?.name
                                }
                            />

                            <Detail
                                label="Service Months"
                                value={
                                    application.current_school_service_months
                                }
                            />
                        </div>
                    </section>

                    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 className="text-lg font-bold text-slate-900">
                            Transfer Request
                        </h3>

                        <div className="mt-5 space-y-5">
                            <Detail
                                label="Transfer Reason"
                                value={
                                    application.transfer_reason
                                }
                            />

                            <Detail
                                label="Reason Details"
                                value={
                                    application.reason_details
                                }
                            />

                            <Detail
                                label="Principal Remarks"
                                value={
                                    application.principal_remarks
                                }
                            />
                        </div>
                    </section>

                    <section className="rounded-2xl border border-blue-200 bg-blue-50 p-6">
                        <h3 className="text-lg font-bold text-slate-900">
                            Zonal Recommendation
                        </h3>

                        <div className="mt-5 grid gap-5 sm:grid-cols-2">
                            <Detail
                                label="Decision"
                                value={
                                    application.zonal_review?.decision
                                }
                            />

                            <Detail
                                label="Recommendation"
                                value={
                                    application.zonal_review?.recommendation
                                }
                            />

                            <Detail
                                label="Reviewer"
                                value={
                                    application.zonal_review?.reviewer?.name
                                }
                            />

                            <Detail
                                label="Remarks"
                                value={
                                    application.zonal_review?.remarks
                                }
                            />
                        </div>
                    </section>
                </div>

                <aside className="space-y-6">
                    {can.decide && (
                        <>
                            <form
                                onSubmit={(event) => {
                                    event.preventDefault();

                                    approveForm.post(
                                        route(
                                            'provincial.transfer-applications.approve',
                                            application.id
                                        )
                                    );
                                }}
                                className="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"
                            >
                                <h3 className="font-bold text-emerald-900">
                                    Approve Application
                                </h3>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="recommendation"
                                        value="Recommendation"
                                    />

                                    <input
                                        id="recommendation"
                                        value={
                                            approveForm.data.recommendation
                                        }
                                        onChange={(event) =>
                                            approveForm.setData(
                                                'recommendation',
                                                event.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-xl border-emerald-200"
                                    />

                                    <InputError
                                        message={
                                            approveForm.errors.recommendation
                                        }
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="approve_remarks"
                                        value="Remarks"
                                    />

                                    <textarea
                                        id="approve_remarks"
                                        rows="4"
                                        value={
                                            approveForm.data.remarks
                                        }
                                        onChange={(event) =>
                                            approveForm.setData(
                                                'remarks',
                                                event.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-xl border-emerald-200"
                                    />
                                </div>

                                <button
                                    type="submit"
                                    disabled={
                                        approveForm.processing
                                    }
                                    className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700"
                                >
                                    <CheckCircle2 className="h-5 w-5" />
                                    Approve
                                </button>
                            </form>

                            <form
                                onSubmit={(event) => {
                                    event.preventDefault();

                                    rejectForm.post(
                                        route(
                                            'provincial.transfer-applications.reject',
                                            application.id
                                        )
                                    );
                                }}
                                className="rounded-2xl border border-red-200 bg-red-50 p-5"
                            >
                                <h3 className="font-bold text-red-900">
                                    Reject Application
                                </h3>

                                <textarea
                                    rows="4"
                                    value={
                                        rejectForm.data.rejection_reason
                                    }
                                    onChange={(event) =>
                                        rejectForm.setData(
                                            'rejection_reason',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Enter rejection reason"
                                    className="mt-4 block w-full rounded-xl border-red-200"
                                />

                                <InputError
                                    message={
                                        rejectForm.errors.rejection_reason
                                    }
                                    className="mt-2"
                                />

                                <button
                                    type="submit"
                                    disabled={
                                        rejectForm.processing
                                    }
                                    className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700"
                                >
                                    <XCircle className="h-5 w-5" />
                                    Reject
                                </button>
                            </form>

                            <form
                                onSubmit={(event) => {
                                    event.preventDefault();

                                    returnForm.post(
                                        route(
                                            'provincial.transfer-applications.return-to-zone',
                                            application.id
                                        )
                                    );
                                }}
                                className="rounded-2xl border border-violet-200 bg-violet-50 p-5"
                            >
                                <h3 className="font-bold text-violet-900">
                                    Return to Zone
                                </h3>

                                <textarea
                                    rows="4"
                                    value={
                                        returnForm.data.return_reason
                                    }
                                    onChange={(event) =>
                                        returnForm.setData(
                                            'return_reason',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Explain what the Zone must clarify"
                                    className="mt-4 block w-full rounded-xl border-violet-200"
                                />

                                <InputError
                                    message={
                                        returnForm.errors.return_reason
                                    }
                                    className="mt-2"
                                />

                                <button
                                    type="submit"
                                    disabled={
                                        returnForm.processing
                                    }
                                    className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-700"
                                >
                                    <RotateCcw className="h-5 w-5" />
                                    Return to Zone
                                </button>
                            </form>
                        </>
                    )}

                    {application.provincial_review && (
                        <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 className="font-bold text-slate-900">
                                Provincial Decision
                            </h3>

                            <div className="mt-4 space-y-4">
                                <Detail
                                    label="Decision"
                                    value={
                                        application.provincial_review.decision
                                    }
                                />

                                <Detail
                                    label="Recommendation"
                                    value={
                                        application.provincial_review.recommendation
                                    }
                                />

                                <Detail
                                    label="Rejection Reason"
                                    value={
                                        application.provincial_review.rejection_reason
                                    }
                                />

                                <Detail
                                    label="Return Reason"
                                    value={
                                        application.provincial_review.return_reason
                                    }
                                />
                            </div>
                        </section>
                    )}
                </aside>
            </div>
        </AdminLayout>
    );
}
