import AdminLayout from '@/Layouts/AdminLayout';
import { Link } from '@inertiajs/react';
import {
    Download,
    Eye,
    FileText,
} from 'lucide-react';

export default function Index({
    documents,
}) {
    return (
        <AdminLayout
            title="My Transfer Documents"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        My Transfer Documents
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        View and download published official transfer documents.
                    </p>
                </div>
            }
        >
            <section className="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                {documents.data.map(
                    (document) => (
                        <article
                            key={document.id}
                            className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                        >
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <FileText className="h-6 w-6" />
                            </div>

                            <h2 className="mt-4 font-bold text-slate-900">
                                {
                                    document.document_type
                                }
                            </h2>

                            <p className="mt-1 text-sm text-slate-500">
                                {
                                    document.document_number
                                }
                            </p>

                            <p className="mt-3 text-xs text-slate-500">
                                Application:{' '}
                                {
                                    document
                                        .transfer_application
                                        ?.application_number
                                }
                            </p>

                            <div className="mt-5 flex gap-3">
                                <Link
                                    href={route(
                                        'principal.transfer-documents.show',
                                        document.id,
                                    )}
                                    className="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 px-3 py-2.5 text-sm font-semibold text-slate-700"
                                >
                                    <Eye className="h-4 w-4" />
                                    View
                                </Link>

                                <a
                                    href={route(
                                        'principal.transfer-documents.download',
                                        document.id,
                                    )}
                                    className="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-blue-600 px-3 py-2.5 text-sm font-semibold text-white"
                                >
                                    <Download className="h-4 w-4" />
                                    Download
                                </a>
                            </div>
                        </article>
                    ),
                )}

                {documents.data.length === 0 && (
                    <div className="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                        No published transfer documents are available.
                    </div>
                )}
            </section>
        </AdminLayout>
    );
}
