import AdminLayout from '@/Layouts/AdminLayout';
import { Link } from '@inertiajs/react';
import {
    ArrowLeft,
    Download,
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
    document,
}) {
    const application =
        document.transfer_application;

    const decision =
        application?.transfer_board_decision;

    return (
        <AdminLayout
            title={document.document_type}
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            {document.document_type}
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            {document.document_number}
                        </p>
                    </div>

                    <div className="flex gap-3">
                        <Link
                            href={route(
                                'principal.transfer-documents.index',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back
                        </Link>

                        <a
                            href={route(
                                'principal.transfer-documents.download',
                                document.id,
                            )}
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            <Download className="h-4 w-4" />
                            Download
                        </a>
                    </div>
                </div>
            }
        >
            <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <Detail
                        label="Application Number"
                        value={
                            application?.application_number
                        }
                    />

                    <Detail
                        label="Final Decision"
                        value={
                            application?.status
                        }
                    />

                    <Detail
                        label="Issued Date"
                        value={
                            document.issued_date
                        }
                    />

                    <Detail
                        label="Current School"
                        value={
                            application
                                ?.current_school
                                ?.name
                        }
                    />

                    <Detail
                        label="Approved School"
                        value={
                            decision
                                ?.recommended_school
                                ?.name
                        }
                    />

                    <Detail
                        label="Effective Date"
                        value={
                            decision?.effective_date
                        }
                    />

                    <Detail
                        label="Appointment Type"
                        value={
                            decision?.appointment_type
                        }
                    />

                    <Detail
                        label="Decision Reference"
                        value={
                            decision?.decision_reference
                        }
                    />
                </div>
            </section>
        </AdminLayout>
    );
}
