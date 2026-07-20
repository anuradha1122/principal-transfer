import AdminLayout from '@/Layouts/AdminLayout';
import { Link, router } from '@inertiajs/react';
import {
    Eye,
    FilePlus2,
    Filter,
    Search,
} from 'lucide-react';
import { useState } from 'react';

export default function Index({
    documents,
    filters = {},
    documentTypes = [],
}) {
    const [form, setForm] = useState({
        search: filters.search ?? '',
        document_type:
            filters.document_type ?? '',
        publication_status:
            filters.publication_status ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'admin.transfer-documents.index'
            ),
            form,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Transfer Documents"
            header={
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900">
                            Transfer Documents
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Generate, sign and publish official transfer documents.
                        </p>
                    </div>

                    <Link
                        href={route(
                            'admin.transfer-documents.create',
                        )}
                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        <FilePlus2 className="h-5 w-5" />
                        Generate Document
                    </Link>
                </div>
            }
        >
            <form
                onSubmit={submit}
                className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div className="grid gap-4 lg:grid-cols-4">
                    <div className="lg:col-span-2">
                        <label className="text-sm font-semibold text-slate-700">
                            Search
                        </label>

                        <div className="relative mt-1">
                            <Search className="absolute left-3 top-3 h-5 w-5 text-slate-400" />

                            <input
                                value={form.search}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        search:
                                            event.target.value,
                                    })
                                }
                                placeholder="Document number, application or principal"
                                className="block w-full rounded-xl border-slate-300 pl-10"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="text-sm font-semibold text-slate-700">
                            Document Type
                        </label>

                        <select
                            value={
                                form.document_type
                            }
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    document_type:
                                        event.target.value,
                                })
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        >
                            <option value="">
                                All document types
                            </option>

                            {documentTypes.map(
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
                    </div>

                    <div>
                        <label className="text-sm font-semibold text-slate-700">
                            Publication
                        </label>

                        <select
                            value={
                                form.publication_status
                            }
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    publication_status:
                                        event.target.value,
                                })
                            }
                            className="mt-1 block w-full rounded-xl border-slate-300"
                        >
                            <option value="">
                                All
                            </option>

                            <option value="published">
                                Published
                            </option>

                            <option value="unpublished">
                                Unpublished
                            </option>
                        </select>
                    </div>
                </div>

                <div className="mt-4 flex justify-end">
                    <button
                        type="submit"
                        className="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white"
                    >
                        <Filter className="h-4 w-4" />
                        Apply Filters
                    </button>
                </div>
            </form>

            <section className="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200">
                        <thead className="bg-slate-50">
                            <tr>
                                {[
                                    'Document',
                                    'Application',
                                    'Principal',
                                    'Type',
                                    'Published',
                                    'Action',
                                ].map((heading) => (
                                    <th
                                        key={heading}
                                        className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        {heading}
                                    </th>
                                ))}
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-slate-100">
                            {documents.data.map(
                                (document) => (
                                    <tr
                                        key={document.id}
                                        className="hover:bg-slate-50"
                                    >
                                        <td className="px-5 py-4 text-sm font-bold text-slate-900">
                                            {
                                                document.document_number
                                            }
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                document
                                                    .transfer_application
                                                    ?.application_number
                                            }
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                document
                                                    .transfer_application
                                                    ?.principal_name
                                            }
                                        </td>

                                        <td className="px-5 py-4 text-sm text-slate-600">
                                            {
                                                document.document_type
                                            }
                                        </td>

                                        <td className="px-5 py-4">
                                            <span
                                                className={[
                                                    'rounded-full px-3 py-1 text-xs font-semibold',
                                                    document.is_published
                                                        ? 'bg-emerald-50 text-emerald-700'
                                                        : 'bg-amber-50 text-amber-700',
                                                ].join(
                                                    ' ',
                                                )}
                                            >
                                                {document.is_published
                                                    ? 'Published'
                                                    : 'Not Published'}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <Link
                                                href={route(
                                                    'admin.transfer-documents.show',
                                                    document.id,
                                                )}
                                                className="inline-flex items-center gap-2 text-sm font-semibold text-blue-700"
                                            >
                                                <Eye className="h-4 w-4" />
                                                Open
                                            </Link>
                                        </td>
                                    </tr>
                                ),
                            )}

                            {documents.data.length ===
                                0 && (
                                <tr>
                                    <td
                                        colSpan="6"
                                        className="px-5 py-12 text-center text-sm text-slate-500"
                                    >
                                        No transfer documents found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </section>
        </AdminLayout>
    );
}
