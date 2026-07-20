import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import AdminLayout from '@/Layouts/AdminLayout';
import { Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    FileText,
} from 'lucide-react';
import { useMemo } from 'react';

export default function Create({
    applications = [],
    documentTypes = [],
    defaultIssuedDate = '',
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        transfer_application_id: '',
        document_type: '',
        document_number: '',
        issued_date: defaultIssuedDate,
        effective_date: '',
        remarks: '',
    });

    const selectedApplication = useMemo(
        () =>
            applications.find(
                (application) =>
                    String(application.id) ===
                    String(
                        data.transfer_application_id,
                    ),
            ) ?? null,
        [
            applications,
            data.transfer_application_id,
        ],
    );

    const availableTypes = useMemo(() => {
        if (!selectedApplication) {
            return [];
        }

        const existingTypes =
            selectedApplication.transfer_documents?.map(
                (document) =>
                    document.document_type,
            ) ?? [];

        const allowed =
            selectedApplication.status ===
            'Approved'
                ? [
                      'Transfer Order',
                      'Appointment Letter',
                  ]
                : ['Decision Letter'];

        return allowed.filter(
            (type) =>
                !existingTypes.includes(type),
        );
    }, [selectedApplication]);

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'admin.transfer-documents.store',
            ),
        );
    };

    return (
        <AdminLayout
            title="Generate Transfer Document"
            header={
                <div className="flex items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Generate Transfer Document
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Create an official document from a final Transfer Board decision.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.transfer-documents.index',
                        )}
                        className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back
                    </Link>
                </div>
            }
        >
            <form
                onSubmit={submit}
                className="mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div className="grid gap-6 md:grid-cols-2">
                    <div className="md:col-span-2">
                        <InputLabel
                            htmlFor="transfer_application_id"
                            value="Transfer Application"
                        />

                        <select
                            id="transfer_application_id"
                            value={
                                data.transfer_application_id
                            }
                            onChange={(event) => {
                                setData({
                                    ...data,
                                    transfer_application_id:
                                        event.target.value,
                                    document_type: '',
                                    effective_date:
                                        applications.find(
                                            (
                                                application,
                                            ) =>
                                                String(
                                                    application.id,
                                                ) ===
                                                String(
                                                    event
                                                        .target
                                                        .value,
                                                ),
                                        )
                                            ?.transfer_board_decision
                                            ?.effective_date ??
                                        '',
                                });
                            }}
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        >
                            <option value="">
                                Select application
                            </option>

                            {applications.map(
                                (application) => (
                                    <option
                                        key={
                                            application.id
                                        }
                                        value={
                                            application.id
                                        }
                                    >
                                        {
                                            application.application_number
                                        }
                                        {' - '}
                                        {
                                            application.principal_name
                                        }
                                        {' - '}
                                        {
                                            application.status
                                        }
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={
                                errors.transfer_application_id
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="document_type"
                            value="Document Type"
                        />

                        <select
                            id="document_type"
                            value={
                                data.document_type
                            }
                            disabled={
                                !selectedApplication
                            }
                            onChange={(event) =>
                                setData(
                                    'document_type',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300 disabled:bg-slate-100"
                        >
                            <option value="">
                                Select document type
                            </option>

                            {availableTypes.map(
                                (type) => (
                                    <option
                                        key={type}
                                        value={type}
                                    >
                                        {type}
                                    </option>
                                ),
                            )}
                        </select>

                        <InputError
                            message={
                                errors.document_type
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="document_number"
                            value="Document Number"
                        />

                        <input
                            id="document_number"
                            value={
                                data.document_number
                            }
                            onChange={(event) =>
                                setData(
                                    'document_number',
                                    event.target.value,
                                )
                            }
                            placeholder="TR/2026/0001"
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        />

                        <InputError
                            message={
                                errors.document_number
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="issued_date"
                            value="Issued Date"
                        />

                        <input
                            id="issued_date"
                            type="date"
                            value={data.issued_date}
                            onChange={(event) =>
                                setData(
                                    'issued_date',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        />

                        <InputError
                            message={
                                errors.issued_date
                            }
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            htmlFor="effective_date"
                            value="Effective Date"
                        />

                        <input
                            id="effective_date"
                            type="date"
                            value={
                                data.effective_date
                            }
                            onChange={(event) =>
                                setData(
                                    'effective_date',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        />
                    </div>

                    <div className="md:col-span-2">
                        <InputLabel
                            htmlFor="remarks"
                            value="Remarks"
                        />

                        <textarea
                            id="remarks"
                            rows="4"
                            value={data.remarks}
                            onChange={(event) =>
                                setData(
                                    'remarks',
                                    event.target.value,
                                )
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white disabled:opacity-60"
                >
                    <FileText className="h-5 w-5" />
                    {processing
                        ? 'Generating...'
                        : 'Generate Document'}
                </button>
            </form>
        </AdminLayout>
    );
}
