import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import {
    ChevronLeft,
    ChevronRight,
    Download,
    FileSpreadsheet,
    Filter,
    RotateCcw,
} from 'lucide-react';
import { useState } from 'react';

function Pagination({ links }) {
    return (
        <div className="flex flex-wrap justify-center gap-2">
            {links?.map((link, index) =>
                link.url ? (
                    <Link
                        key={index}
                        href={link.url}
                        preserveScroll
                        className={`inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm ${
                            link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : 'border-slate-200 bg-white text-slate-700'
                        }`}
                    >
                        {index === 0 ? (
                            <ChevronLeft className="h-4 w-4" />
                        ) : index === links.length - 1 ? (
                            <ChevronRight className="h-4 w-4" />
                        ) : (
                            <span
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        )}
                    </Link>
                ) : (
                    <span
                        key={index}
                        className="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border border-slate-100 bg-slate-50 px-3 text-sm text-slate-400"
                    >
                        {index === 0 ? (
                            <ChevronLeft className="h-4 w-4" />
                        ) : index === links.length - 1 ? (
                            <ChevronRight className="h-4 w-4" />
                        ) : (
                            link.label
                        )}
                    </span>
                ),
            )}
        </div>
    );
}

export default function DetailedReportPage({
    title,
    description,
    rows,
    columns,
    filters,
    transferCycles,
    zones,
    statuses = [],
    documentTypes = [],
    scope,
    canExport,
    indexRoute,
    pdfRoute,
    excelRoute,
    extraFilters = false,
}) {
    const [form, setForm] = useState({
        transfer_cycle_id:
            filters.transfer_cycle_id ?? '',
        zone_id: filters.zone_id ?? '',
        status: filters.status ?? '',
        document_type:
            filters.document_type ?? '',
        is_published:
            filters.is_published ?? '',
        date_from: filters.date_from ?? '',
        date_to: filters.date_to ?? '',
    });

    const query = Object.fromEntries(
        Object.entries(form).filter(
            ([, value]) => value !== '',
        ),
    );

    const submit = (event) => {
        event.preventDefault();

        router.get(indexRoute, query, {
            preserveState: true,
            replace: true,
        });
    };

    const reset = () => {
        setForm({
            transfer_cycle_id: '',
            zone_id: '',
            status: '',
            document_type: '',
            is_published: '',
            date_from: '',
            date_to: '',
        });

        router.get(indexRoute);
    };

    return (
        <AdminLayout>
            <Head title={title} />

            <div className="space-y-6">
                <div className="rounded-2xl bg-gradient-to-r from-slate-950 to-blue-950 px-6 py-7 text-white">
                    <div className="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h1 className="text-2xl font-bold sm:text-3xl">
                                {title}
                            </h1>
                            <p className="mt-2 max-w-3xl text-sm text-slate-300">
                                {description}
                            </p>
                        </div>

                        {canExport && (
                            <div className="flex flex-wrap gap-3">
                                <a
                                    href={route(
                                        pdfRoute,
                                        query,
                                    )}
                                    className="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold ring-1 ring-white/20 hover:bg-white/20"
                                >
                                    <Download className="h-4 w-4" />
                                    PDF
                                </a>

                                <a
                                    href={route(
                                        excelRoute,
                                        query,
                                    )}
                                    className="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600"
                                >
                                    <FileSpreadsheet className="h-4 w-4" />
                                    Excel
                                </a>
                            </div>
                        )}
                    </div>
                </div>

                <form
                    onSubmit={submit}
                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <select
                            value={form.transfer_cycle_id}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    transfer_cycle_id:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm"
                        >
                            <option value="">
                                All transfer cycles
                            </option>

                            {transferCycles.map((cycle) => (
                                <option
                                    key={cycle.id}
                                    value={cycle.id}
                                >
                                    {cycle.name}
                                </option>
                            ))}
                        </select>

                        <select
                            value={form.zone_id}
                            disabled={scope.is_zonal}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    zone_id:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm disabled:bg-slate-100"
                        >
                            <option value="">
                                All zones
                            </option>

                            {zones.map((zone) => (
                                <option
                                    key={zone.id}
                                    value={zone.id}
                                >
                                    {zone.name}
                                </option>
                            ))}
                        </select>

                        {!extraFilters && (
                            <select
                                value={form.status}
                                onChange={(event) =>
                                    setForm({
                                        ...form,
                                        status:
                                            event.target.value,
                                    })
                                }
                                className="rounded-xl border-slate-300 text-sm"
                            >
                                <option value="">
                                    All statuses
                                </option>

                                {statuses.map((status) => (
                                    <option
                                        key={status}
                                        value={status}
                                    >
                                        {status}
                                    </option>
                                ))}
                            </select>
                        )}

                        {extraFilters && (
                            <>
                                <select
                                    value={
                                        form.document_type
                                    }
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            document_type:
                                                event.target
                                                    .value,
                                        })
                                    }
                                    className="rounded-xl border-slate-300 text-sm"
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

                                <select
                                    value={
                                        form.is_published
                                    }
                                    onChange={(event) =>
                                        setForm({
                                            ...form,
                                            is_published:
                                                event.target
                                                    .value,
                                        })
                                    }
                                    className="rounded-xl border-slate-300 text-sm"
                                >
                                    <option value="">
                                        Any publication status
                                    </option>
                                    <option value="1">
                                        Published
                                    </option>
                                    <option value="0">
                                        Not published
                                    </option>
                                </select>
                            </>
                        )}

                        <input
                            type="date"
                            value={form.date_from}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    date_from:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm"
                        />

                        <input
                            type="date"
                            value={form.date_to}
                            onChange={(event) =>
                                setForm({
                                    ...form,
                                    date_to:
                                        event.target.value,
                                })
                            }
                            className="rounded-xl border-slate-300 text-sm"
                        />
                    </div>

                    <div className="mt-5 flex justify-end gap-3 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            onClick={reset}
                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            <RotateCcw className="h-4 w-4" />
                            Reset
                        </button>

                        <button
                            type="submit"
                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            <Filter className="h-4 w-4" />
                            Apply
                        </button>
                    </div>
                </form>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-200">
                            <thead className="bg-slate-50">
                                <tr>
                                    {columns.map((column) => (
                                        <th
                                            key={column.key}
                                            className="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                        >
                                            {column.label}
                                        </th>
                                    ))}
                                </tr>
                            </thead>

                            <tbody className="divide-y divide-slate-100">
                                {rows.data.length === 0 ? (
                                    <tr>
                                        <td
                                            colSpan={
                                                columns.length
                                            }
                                            className="px-5 py-14 text-center text-sm text-slate-500"
                                        >
                                            No report records
                                            found.
                                        </td>
                                    </tr>
                                ) : (
                                    rows.data.map((row) => (
                                        <tr
                                            key={row.id}
                                            className="hover:bg-slate-50"
                                        >
                                            {columns.map(
                                                (column) => (
                                                    <td
                                                        key={
                                                            column.key
                                                        }
                                                        className="max-w-xs px-5 py-4 text-sm text-slate-700"
                                                    >
                                                        {column.render
                                                            ? column.render(
                                                                  row,
                                                              )
                                                            : row[
                                                                  column
                                                                      .key
                                                              ] ??
                                                              '—'}
                                                    </td>
                                                ),
                                            )}
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="border-t border-slate-200 bg-slate-50 px-5 py-4">
                        <Pagination
                            links={rows.links}
                        />
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
