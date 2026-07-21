import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Download, Save, Trash2 } from 'lucide-react';

export default function Edit({ appeal = {} }) {
    const { data, setData, post, processing, errors } = useForm({
        appeal_reason: appeal.appeal_reason ?? '',
        appeal_details: appeal.appeal_details ?? '',
        requested_outcome: appeal.requested_outcome ?? '',
        documents: [],
        _method: 'PUT',
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route('principal.transfer-appeals.update', appeal.id),
            {
                forceFormData: true,
            },
        );
    };

    const deleteDocument = (document) => {
        if (!window.confirm('Remove this supporting document?')) {
            return;
        }

        router.delete(
            route(
                'principal.transfer-appeals.documents.destroy',
                [appeal.id, document.id],
            ),
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <AdminLayout>
            <Head title={`Edit ${appeal.appeal_number ?? 'Appeal'}`} />

            <div className="space-y-6">
                <header className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Edit Transfer Appeal
                        </h1>
                        <p className="mt-1 text-sm text-slate-500">
                            {appeal.appeal_number}
                        </p>
                    </div>

                    <Link
                        href={route(
                            'principal.transfer-appeals.show',
                            appeal.id,
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back
                    </Link>
                </header>

                <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="grid gap-4 md:grid-cols-3">
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Application
                            </p>
                            <p className="mt-1 font-semibold text-slate-900">
                                {appeal.transfer_application
                                    ?.application_number ?? '—'}
                            </p>
                        </div>
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Final Decision
                            </p>
                            <p className="mt-1 font-semibold text-slate-900">
                                {appeal.transfer_application?.status ?? '—'}
                            </p>
                        </div>
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </p>
                            <p className="mt-1 font-semibold text-slate-900">
                                {appeal.status}
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={submit} className="space-y-6">
                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="grid gap-5 md:grid-cols-2">
                            <div className="md:col-span-2">
                                <label className="text-sm font-semibold text-slate-700">
                                    Appeal Reason
                                </label>
                                <input
                                    value={data.appeal_reason}
                                    onChange={(event) =>
                                        setData(
                                            'appeal_reason',
                                            event.target.value,
                                        )
                                    }
                                    className="mt-2 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                {errors.appeal_reason && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {errors.appeal_reason}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="text-sm font-semibold text-slate-700">
                                    Detailed Explanation
                                </label>
                                <textarea
                                    rows="8"
                                    value={data.appeal_details}
                                    onChange={(event) =>
                                        setData(
                                            'appeal_details',
                                            event.target.value,
                                        )
                                    }
                                    className="mt-2 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                {errors.appeal_details && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {errors.appeal_details}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label className="text-sm font-semibold text-slate-700">
                                    Requested Outcome
                                </label>
                                <textarea
                                    rows="8"
                                    value={data.requested_outcome}
                                    onChange={(event) =>
                                        setData(
                                            'requested_outcome',
                                            event.target.value,
                                        )
                                    }
                                    className="mt-2 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                                {errors.requested_outcome && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {errors.requested_outcome}
                                    </p>
                                )}
                            </div>

                            <div className="md:col-span-2">
                                <label className="text-sm font-semibold text-slate-700">
                                    Add Supporting Documents
                                </label>
                                <input
                                    type="file"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    onChange={(event) =>
                                        setData(
                                            'documents',
                                            Array.from(
                                                event.target.files ?? [],
                                            ),
                                        )
                                    }
                                    className="mt-2 block w-full rounded-xl border border-slate-300 bg-white p-3 text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    {(appeal.documents ?? []).length > 0 && (
                        <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 className="text-lg font-bold text-slate-900">
                                Existing Documents
                            </h2>

                            <div className="mt-4 space-y-3">
                                {(appeal.documents ?? []).map((document) => (
                                    <div
                                        key={document.id}
                                        className="flex items-center justify-between rounded-xl border border-slate-200 p-4"
                                    >
                                        <span className="text-sm font-medium text-slate-700">
                                            {document.original_name}
                                        </span>

                                        <div className="flex gap-2">
                                            <a
                                                href={route(
                                                    'principal.transfer-appeals.documents.download',
                                                    [
                                                        appeal.id,
                                                        document.id,
                                                    ],
                                                )}
                                                className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50"
                                            >
                                                <Download className="h-4 w-4" />
                                            </a>

                                            <button
                                                type="button"
                                                onClick={() =>
                                                    deleteDocument(document)
                                                }
                                                className="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            <Save className="h-4 w-4" />
                            {processing ? 'Saving...' : 'Update Draft'}
                        </button>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
