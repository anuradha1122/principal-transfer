import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

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
        <>
            <Head title="Transfer Result" />

            <div className="min-h-screen bg-slate-100">
                <header className="bg-slate-950 text-white">
                    <div className="mx-auto max-w-5xl px-4 py-10 sm:px-6">
                        <h1 className="text-3xl font-bold">
                            Transfer Result
                        </h1>

                        <p className="mt-2 text-slate-300">
                            {
                                application?.application_number
                            }
                        </p>
                    </div>
                </header>

                <main className="mx-auto max-w-5xl px-4 py-8 sm:px-6">
                    <Link
                        href={route(
                            'transfer-results.index',
                        )}
                        className="inline-flex items-center gap-2 text-sm font-semibold text-blue-700"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back to Results
                    </Link>

                    <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <Detail
                                label="Application Number"
                                value={
                                    application?.application_number
                                }
                            />

                            <Detail
                                label="Principal Name"
                                value={
                                    application?.principal_name
                                }
                            />

                            <Detail
                                label="NIC"
                                value={
                                    application?.nic
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
                                label="Final Decision"
                                value={
                                    application?.status
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
                                label="Published Date"
                                value={
                                    document.published_at
                                }
                            />
                        </div>
                    </section>
                </main>
            </div>
        </>
    );
}
