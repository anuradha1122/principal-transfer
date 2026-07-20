import InputError from '@/Components/InputError';
import AdminLayout from '@/Layouts/AdminLayout';
import { Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Download,
    FileCheck2,
    RefreshCw,
    Upload,
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
    const uploadForm = useForm({
        signed_document: null,
    });

    const uploadSigned = (event) => {
        event.preventDefault();

        uploadForm.post(
            route(
                'admin.transfer-documents.upload-signed',
                document.id,
            ),
            {
                forceFormData: true,
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Transfer Document"
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

                    <div className="flex flex-wrap gap-3">
                        <Link
                            href={route(
                                'admin.transfer-documents.index',
                            )}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back
                        </Link>

                        <a
                            href={route(
                                'admin.transfer-documents.download',
                                document.id,
                            )}
                            className="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            <Download className="h-4 w-4" />
                            Download
                        </a>
                    </div>
                </div>
            }
        >
            <div className="grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                    <h2 className="text-lg font-bold text-slate-900">
                        Document Details
                    </h2>

                    <div className="mt-6 grid gap-5 sm:grid-cols-2">
                        <Detail
                            label="Document Number"
                            value={
                                document.document_number
                            }
                        />

                        <Detail
                            label="Document Type"
                            value={
                                document.document_type
                            }
                        />

                        <Detail
                            label="Application Number"
                            value={
                                document
                                    .transfer_application
                                    ?.application_number
                            }
                        />

                        <Detail
                            label="Principal"
                            value={
                                document
                                    .transfer_application
                                    ?.principal_name
                            }
                        />

                        <Detail
                            label="Issued Date"
                            value={
                                document.issued_date
                            }
                        />

                        <Detail
                            label="Effective Date"
                            value={
                                document.effective_date
                            }
                        />

                        <Detail
                            label="Issued By"
                            value={
                                document.issuer?.name
                            }
                        />

                        <Detail
                            label="Publication"
                            value={
                                document.is_published
                                    ? 'Published'
                                    : 'Not Published'
                            }
                        />
                    </div>
                </section>

                <aside className="space-y-6">
                    <form
                        onSubmit={uploadSigned}
                        className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >
                        <h3 className="font-bold text-slate-900">
                            Signed Document
                        </h3>

                        <p className="mt-2 text-sm text-slate-500">
                            Upload the signed PDF before publishing.
                        </p>

                        <input
                            type="file"
                            accept="application/pdf"
                            onChange={(event) =>
                                uploadForm.setData(
                                    'signed_document',
                                    event.target
                                        .files?.[0] ??
                                        null,
                                )
                            }
                            className="mt-4 block w-full text-sm"
                        />

                        <InputError
                            message={
                                uploadForm.errors
                                    .signed_document
                            }
                            className="mt-2"
                        />

                        <button
                            type="submit"
                            disabled={
                                uploadForm.processing
                            }
                            className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white"
                        >
                            <Upload className="h-5 w-5" />
                            Upload Signed PDF
                        </button>
                    </form>

                    <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="font-bold text-slate-900">
                            Publication
                        </h3>

                        {!document.is_published ? (
                            <button
                                type="button"
                                disabled={
                                    !document.signed_file_path
                                }
                                onClick={() =>
                                    router.post(
                                        route(
                                            'admin.transfer-documents.publish',
                                            document.id,
                                        ),
                                        {},
                                        {
                                            preserveScroll:
                                                true,
                                        },
                                    )
                                }
                                className="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <FileCheck2 className="h-5 w-5" />
                                Publish Result
                            </button>
                        ) : (
                            <button
                                type="button"
                                onClick={() =>
                                    router.post(
                                        route(
                                            'admin.transfer-documents.unpublish',
                                            document.id,
                                        ),
                                        {},
                                        {
                                            preserveScroll:
                                                true,
                                        },
                                    )
                                }
                                className="mt-4 w-full rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white"
                            >
                                Unpublish Result
                            </button>
                        )}

                        <button
                            type="button"
                            onClick={() =>
                                router.post(
                                    route(
                                        'admin.transfer-documents.regenerate',
                                        document.id,
                                    ),
                                    {},
                                    {
                                        preserveScroll:
                                            true,
                                    },
                                )
                            }
                            className="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700"
                        >
                            <RefreshCw className="h-5 w-5" />
                            Regenerate PDF
                        </button>
                    </section>
                </aside>
            </div>
        </AdminLayout>
    );
}
