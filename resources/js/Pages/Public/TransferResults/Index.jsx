import { Head, Link, router } from '@inertiajs/react';
import {
    Eye,
    Search,
} from 'lucide-react';
import { useState } from 'react';

export default function Index({
    documents,
    filters = {},
}) {
    const [form, setForm] = useState({
        search: filters.search ?? '',
        decision: filters.decision ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        router.get(
            route(
                'transfer-results.index'
            ),
            form,
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return (
        <>
            <Head title="Transfer Results" />

            <div className="min-h-screen bg-slate-100">
                <header className="bg-slate-950 text-white">
                    <div className="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                        <h1 className="text-3xl font-bold">
                            Principal Transfer Results
                        </h1>

                        <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                            Published final decisions of the Sabaragamuwa Provincial Principal Transfer Board.
                        </p>
                    </div>
                </header>

                <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <form
                        onSubmit={submit}
                        className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >
                        <div className="grid gap-4 md:grid-cols-[1fr_220px_auto]">
                            <div className="relative">
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
                                    placeholder="Search application number or principal name"
                                    className="block w-full rounded-xl border-slate-300 pl-10"
                                />
                            </div>

                            <select
                                value={form.decision}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        decision:
                                            event.target.value,
                                    })
                                }
                                className="rounded-xl border-slate-300"
                            >
                                <option value="">
                                    All decisions
                                </option>

                                <option value="Approved">
                                    Approved
                                </option>

                                <option value="Rejected">
                                    Rejected
                                </option>

                                <option value="Waitlisted">
                                    Waitlisted
                                </option>
                            </select>

                            <button
                                type="submit"
                                className="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white"
                            >
                                Search
                            </button>
                        </div>
                    </form>

                    <section className="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-slate-200">
                                <thead className="bg-slate-50">
                                    <tr>
                                        {[
                                            'Application',
                                            'Principal',
                                            'Current School',
                                            'Decision',
                                            'Approved School',
                                            'Action',
                                        ].map(
                                            (
                                                heading,
                                            ) => (
                                                <th
                                                    key={
                                                        heading
                                                    }
                                                    className="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                                >
                                                    {
                                                        heading
                                                    }
                                                </th>
                                            ),
                                        )}
                                    </tr>
                                </thead>

                                <tbody className="divide-y divide-slate-100">
                                    {documents.data.map(
                                        (document) => {
                                            const application =
                                                document.transfer_application;

                                            const decision =
                                                application
                                                    ?.transfer_board_decision;

                                            return (
                                                <tr
                                                    key={
                                                        document.id
                                                    }
                                                >
                                                    <td className="px-5 py-4 text-sm font-bold text-slate-900">
                                                        {
                                                            application?.application_number
                                                        }
                                                    </td>

                                                    <td className="px-5 py-4 text-sm text-slate-600">
                                                        {
                                                            application?.principal_name
                                                        }
                                                    </td>

                                                    <td className="px-5 py-4 text-sm text-slate-600">
                                                        {
                                                            application
                                                                ?.current_school
                                                                ?.name
                                                        }
                                                    </td>

                                                    <td className="px-5 py-4">
                                                        <span className="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                            {
                                                                application?.status
                                                            }
                                                        </span>
                                                    </td>

                                                    <td className="px-5 py-4 text-sm text-slate-600">
                                                        {
                                                            decision
                                                                ?.recommended_school
                                                                ?.name ??
                                                            '—'
                                                        }
                                                    </td>

                                                    <td className="px-5 py-4">
                                                        <Link
                                                            href={route(
                                                                'transfer-results.show',
                                                                document.id,
                                                            )}
                                                            className="inline-flex items-center gap-2 text-sm font-semibold text-blue-700"
                                                        >
                                                            <Eye className="h-4 w-4" />
                                                            View
                                                        </Link>
                                                    </td>
                                                </tr>
                                            );
                                        },
                                    )}

                                    {documents.data.length ===
                                        0 && (
                                        <tr>
                                            <td
                                                colSpan="6"
                                                className="px-5 py-12 text-center text-sm text-slate-500"
                                            >
                                                No published transfer results found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </section>
                </main>
            </div>
        </>
    );
}
