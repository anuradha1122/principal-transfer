import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, FilePlus2, Save } from 'lucide-react';

export default function Create({ applications = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        transfer_application_id: '',
        appeal_reason: '',
        appeal_details: '',
        requested_outcome: '',
        documents: [],
    });

    const selectedApplication = (applications ?? []).find(
        (application) =>
            String(application.id) === String(data.transfer_application_id),
    );

    const submit = (event) => {
        event.preventDefault();

        post(route('principal.transfer-appeals.store'), {
            forceFormData: true,
        });
    };

    return (
        <AdminLayout>
            <Head title="Create Transfer Appeal" />

            <div className="space-y-6">
                <header className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Create Transfer Appeal
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Create a draft appeal against a published final
                            decision.
                        </p>
                    </div>

                    <Link
                        href={route('principal.transfer-appeals.index')}
                        className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back
                    </Link>
                </header>

                <form onSubmit={submit} className="space-y-6">
                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-3">
                            <FilePlus2 className="h-5 w-5 text-blue-600" />
                            <h2 className="text-lg font-bold text-slate-900">
                                Original Application
                            </h2>
                        </div>

                        <div className="mt-5">
                            <label className="text-sm font-semibold text-slate-700">
                                Transfer Application
                                <span className="text-red-500"> *</span>
                            </label>

                            <select
                                value={data.transfer_application_id}
                                onChange={(event) =>
                                    setData(
                                        'transfer_application_id',
                                        event.target.value,
                                    )
                                }
                                className="mt-2 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    Select an eligible application
                                </option>

                                {(applications ?? []).map((application) => (
                                    <option
                                        key={application.id}
                                        value={application.id}
                                    >
                                        {application.application_number} ·{' '}
                                        {application.status}
                                    </option>
                                ))}
                            </select>

                            {errors.transfer_application_id && (
                                <p className="mt-2 text-sm text-red-600">
                                    {errors.transfer_application_id}
                                </p>
                            )}
                        </div>

                        {selectedApplication && (
                            <div className="mt-5 grid gap-4 rounded-xl bg-slate-50 p-5 md:grid-cols-3">
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Application
                                    </p>
                                    <p className="mt-1 font-semibold text-slate-900">
                                        {
                                            selectedApplication.application_number
                                        }
                                    </p>
                                </div>

                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Final Decision
                                    </p>
                                    <p className="mt-1 font-semibold text-slate-900">
                                        {selectedApplication.status}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Transfer Cycle
                                    </p>
                                    <p className="mt-1 font-semibold text-slate-900">
                                        {selectedApplication.transfer_cycle
                                            ?.name ?? '—'}
                                    </p>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 className="text-lg font-bold text-slate-900">
                            Appeal Details
                        </h2>

                        <div className="mt-5 grid gap-5 md:grid-cols-2">
                            <div className="md:col-span-2">
                                <label className="text-sm font-semibold text-slate-700">
                                    Appeal Reason
                                    <span className="text-red-500"> *</span>
                                </label>

                                <input
                                    type="text"
                                    value={data.appeal_reason}
                                    onChange={(event) =>
                                        setData(
                                            'appeal_reason',
                                            event.target.value,
                                        )
                                    }
                                    className="mt-2 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Brief reason for the appeal"
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
                                    <span className="text-red-500"> *</span>
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
                                    placeholder="Explain the facts, reasons, and supporting circumstances."
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
                                    <span className="text-red-500"> *</span>
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
                                    placeholder="Describe the decision or remedy you are requesting."
                                />

                                {errors.requested_outcome && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {errors.requested_outcome}
                                    </p>
                                )}
                            </div>

                            <div className="md:col-span-2">
                                <label className="text-sm font-semibold text-slate-700">
                                    Supporting Documents
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

                                <p className="mt-2 text-xs text-slate-500">
                                    Upload up to five PDF, JPG, or PNG files.
                                </p>

                                {errors.documents && (
                                    <p className="mt-2 text-sm text-red-600">
                                        {errors.documents}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="flex justify-end gap-3">
                        <Link
                            href={route('principal.transfer-appeals.index')}
                            className="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Cancel
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <Save className="h-4 w-4" />
                            {processing ? 'Saving...' : 'Save Draft'}
                        </button>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
