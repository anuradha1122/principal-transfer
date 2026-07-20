import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import AdminLayout from '@/Layouts/AdminLayout';
import { Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    CheckCircle2,
    Download,
    ListChecks,
    PlayCircle,
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
    schools = [],
    appointmentTypes = [],
    can = {},
}) {
    const startForm = useForm({});

    const approveForm = useForm({
        recommended_school_id: '',
        effective_date: '',
        appointment_type: 'Permanent',
        decision_reference: '',
        remarks: '',
    });

    const rejectForm = useForm({
        decision_reference: '',
        rejection_reason: '',
        remarks: '',
    });

    const waitlistForm = useForm({
        decision_reference: '',
        waitlist_reason: '',
        remarks: '',
    });

    const startReview = () => {
        startForm.post(
            route(
                'transfer-board.transfer-applications.start-review',
                application.id
            ),
            {
                preserveScroll: true,
            }
        );
    };

    return (
        <AdminLayout
            title="Transfer Board Review"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Transfer Board Review
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            {
                                application.application_number
                            }
                        </p>
                    </div>

                    <div className="flex gap-3">
                        <Link
                            href={route(
                                'transfer-board.transfer-applications.index'
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back
                        </Link>

                        {application.submitted_pdf_path && (
                            <a
                                href={route(
                                    'transfer-board.transfer-applications.pdf',
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
                <div className="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
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
                                startForm.processing
                            }
                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            <PlayCircle className="h-5 w-5" />
                            Start Board Review
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
                                value={application.nic}
                            />

                            <Detail
                                label="Employee Number"
                                value={
                                    application.employee_number
                                }
                            />

                            <Detail
                                label="Current School"
                                value={
                                    application
                                        .current_school
                                        ?.name
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
                                label="Origin Zone"
                                value={
                                    application.origin_zone
                                        ?.name
                                }
                            />

                            <Detail
                                label="Current School Service"
                                value={`${application.current_school_service_months ?? 0} months`}
                            />
                        </div>
                    </section>

                    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 className="text-lg font-bold text-slate-900">
                            Transfer Request
                        </h3>

                        <div className="mt-5 space-y-5">
                            <Detail
                                label="Reason"
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
                                    application.zonal_review
                                        ?.decision
                                }
                            />

                            <Detail
                                label="Recommendation"
                                value={
                                    application.zonal_review
                                        ?.recommendation
                                }
                            />

                            <Detail
                                label="Reviewer"
                                value={
                                    application.zonal_review
                                        ?.reviewer?.name
                                }
                            />

                            <Detail
                                label="Remarks"
                                value={
                                    application.zonal_review
                                        ?.remarks
                                }
                            />
                        </div>
                    </section>

                    <section className="rounded-2xl border border-violet-200 bg-violet-50 p-6">
                        <h3 className="text-lg font-bold text-slate-900">
                            Provincial Recommendation
                        </h3>

                        <div className="mt-5 grid gap-5 sm:grid-cols-2">
                            <Detail
                                label="Decision"
                                value={
                                    application
                                        .provincial_review
                                        ?.decision
                                }
                            />

                            <Detail
                                label="Recommendation"
                                value={
                                    application
                                        .provincial_review
                                        ?.recommendation
                                }
                            />

                            <Detail
                                label="Reviewer"
                                value={
                                    application
                                        .provincial_review
                                        ?.reviewer?.name
                                }
                            />

                            <Detail
                                label="Remarks"
                                value={
                                    application
                                        .provincial_review
                                        ?.remarks
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
                                            'transfer-board.transfer-applications.approve',
                                            application.id
                                        )
                                    );
                                }}
                                className="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"
                            >
                                <h3 className="font-bold text-emerald-900">
                                    Approve Transfer
                                </h3>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="recommended_school_id"
                                        value="Approved School"
                                    />

                                    <select
                                        id="recommended_school_id"
                                        value={
                                            approveForm.data
                                                .recommended_school_id
                                        }
                                        onChange={(event) =>
                                            approveForm.setData(
                                                'recommended_school_id',
                                                event.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-xl border-emerald-200"
                                    >
                                        <option value="">
                                            Select School
                                        </option>

                                        {schools.map(
                                            (school) => (
                                                <option
                                                    key={
                                                        school.id
                                                    }
                                                    value={
                                                        school.id
                                                    }
                                                >
                                                    {
                                                        school.name
                                                    }
                                                    {' - '}
                                                    {
                                                        school
                                                            .division
                                                            ?.zone
                                                            ?.name
                                                    }
                                                </option>
                                            )
                                        )}
                                    </select>

                                    <InputError
                                        message={
                                            approveForm
                                                .errors
                                                .recommended_school_id
                                        }
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="effective_date"
                                        value="Effective Date"
                                    />

                                    <input
                                        id="effective_date"
                                        type="date"
                                        value={
                                            approveForm.data
                                                .effective_date
                                        }
                                        onChange={(event) =>
                                            approveForm.setData(
                                                'effective_date',
                                                event.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-xl border-emerald-200"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="appointment_type"
                                        value="Appointment Type"
                                    />

                                    <select
                                        id="appointment_type"
                                        value={
                                            approveForm.data
                                                .appointment_type
                                        }
                                        onChange={(event) =>
                                            approveForm.setData(
                                                'appointment_type',
                                                event.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-xl border-emerald-200"
                                    >
                                        {appointmentTypes.map(
                                            (type) => (
                                                <option
                                                    key={type}
                                                    value={type}
                                                >
                                                    {type}
                                                </option>
                                            )
                                        )}
                                    </select>
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="approve_reference"
                                        value="Decision Reference"
                                    />

                                    <input
                                        id="approve_reference"
                                        value={
                                            approveForm.data
                                                .decision_reference
                                        }
                                        onChange={(event) =>
                                            approveForm.setData(
                                                'decision_reference',
                                                event.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-xl border-emerald-200"
                                    />

                                    <InputError
                                        message={
                                            approveForm
                                                .errors
                                                .decision_reference
                                        }
                                        className="mt-2"
                                    />
                                </div>

                                <button
                                    type="submit"
                                    disabled={
                                        approveForm.processing
                                    }
                                    className="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700"
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
                                            'transfer-board.transfer-applications.reject',
                                            application.id
                                        )
                                    );
                                }}
                                className="rounded-2xl border border-red-200 bg-red-50 p-5"
                            >
                                <h3 className="font-bold text-red-900">
                                    Reject Application
                                </h3>

                                <input
                                    value={
                                        rejectForm.data
                                            .decision_reference
                                    }
                                    onChange={(event) =>
                                        rejectForm.setData(
                                            'decision_reference',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Decision reference"
                                    className="mt-4 block w-full rounded-xl border-red-200"
                                />

                                <textarea
                                    rows="4"
                                    value={
                                        rejectForm.data
                                            .rejection_reason
                                    }
                                    onChange={(event) =>
                                        rejectForm.setData(
                                            'rejection_reason',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Final rejection reason"
                                    className="mt-4 block w-full rounded-xl border-red-200"
                                />

                                <InputError
                                    message={
                                        rejectForm.errors
                                            .rejection_reason
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

                                    waitlistForm.post(
                                        route(
                                            'transfer-board.transfer-applications.waitlist',
                                            application.id
                                        )
                                    );
                                }}
                                className="rounded-2xl border border-indigo-200 bg-indigo-50 p-5"
                            >
                                <h3 className="font-bold text-indigo-900">
                                    Place on Waitlist
                                </h3>

                                <input
                                    value={
                                        waitlistForm.data
                                            .decision_reference
                                    }
                                    onChange={(event) =>
                                        waitlistForm.setData(
                                            'decision_reference',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Decision reference"
                                    className="mt-4 block w-full rounded-xl border-indigo-200"
                                />

                                <textarea
                                    rows="4"
                                    value={
                                        waitlistForm.data
                                            .waitlist_reason
                                    }
                                    onChange={(event) =>
                                        waitlistForm.setData(
                                            'waitlist_reason',
                                            event.target.value
                                        )
                                    }
                                    placeholder="Waitlist reason"
                                    className="mt-4 block w-full rounded-xl border-indigo-200"
                                />

                                <InputError
                                    message={
                                        waitlistForm.errors
                                            .waitlist_reason
                                    }
                                    className="mt-2"
                                />

                                <button
                                    type="submit"
                                    disabled={
                                        waitlistForm.processing
                                    }
                                    className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                                >
                                    <ListChecks className="h-5 w-5" />
                                    Waitlist
                                </button>
                            </form>
                        </>
                    )}

                    {application
                        .transfer_board_decision && (
                        <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 className="font-bold text-slate-900">
                                Final Board Decision
                            </h3>

                            <div className="mt-4 space-y-4">
                                <Detail
                                    label="Decision"
                                    value={
                                        application
                                            .transfer_board_decision
                                            .decision
                                    }
                                />

                                <Detail
                                    label="Reference"
                                    value={
                                        application
                                            .transfer_board_decision
                                            .decision_reference
                                    }
                                />

                                <Detail
                                    label="Approved School"
                                    value={
                                        application
                                            .transfer_board_decision
                                            .recommended_school
                                            ?.name
                                    }
                                />

                                <Detail
                                    label="Effective Date"
                                    value={
                                        application
                                            .transfer_board_decision
                                            .effective_date
                                    }
                                />

                                <Detail
                                    label="Rejection Reason"
                                    value={
                                        application
                                            .transfer_board_decision
                                            .rejection_reason
                                    }
                                />

                                <Detail
                                    label="Waitlist Reason"
                                    value={
                                        application
                                            .transfer_board_decision
                                            .waitlist_reason
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
