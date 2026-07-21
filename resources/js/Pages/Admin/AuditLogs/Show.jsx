import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import {
    ArrowLeft,
    CalendarClock,
    Database,
    Fingerprint,
    Globe2,
    Network,
    Route,
    ShieldCheck,
    UserRound,
} from 'lucide-react';

function formatDateTime(value) {
    if (!value) {
        return 'Not available';
    }

    return new Intl.DateTimeFormat('en-LK', {
        dateStyle: 'full',
        timeStyle: 'medium',
    }).format(new Date(value));
}

function humanize(value) {
    if (!value) {
        return 'Not available';
    }

    return value
        .replaceAll('.', ' ')
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (character) =>
            character.toUpperCase(),
        );
}

function DataBlock({ title, value }) {
    const hasValue =
        value &&
        typeof value === 'object' &&
        Object.keys(value).length > 0;

    return (
        <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div className="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 className="font-bold text-slate-800">
                    {title}
                </h2>
            </div>

            <div className="p-5">
                {hasValue ? (
                    <pre className="max-h-[32rem] overflow-auto rounded-xl bg-slate-950 p-4 text-xs leading-6 text-slate-100">
                        {JSON.stringify(
                            value,
                            null,
                            2,
                        )}
                    </pre>
                ) : (
                    <p className="text-sm text-slate-500">
                        No values recorded.
                    </p>
                )}
            </div>
        </div>
    );
}

function DetailItem({
    icon: Icon,
    label,
    value,
}) {
    return (
        <div className="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div className="flex items-start gap-3">
                <div className="rounded-lg bg-white p-2 text-slate-500 shadow-sm ring-1 ring-slate-200">
                    <Icon className="h-4 w-4" />
                </div>

                <div className="min-w-0">
                    <p className="text-xs font-bold uppercase tracking-wide text-slate-500">
                        {label}
                    </p>
                    <p className="mt-1 break-words text-sm font-medium text-slate-800">
                        {value || 'Not available'}
                    </p>
                </div>
            </div>
        </div>
    );
}

export default function Show({ auditLog }) {
    return (
        <AdminLayout>
            <Head
                title={`Audit Log #${auditLog.id}`}
            />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            href={route(
                                'admin.audit-logs.index',
                            )}
                            className="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Back to audit logs
                        </Link>

                        <h1 className="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">
                            {humanize(auditLog.event)}
                        </h1>

                        <p className="mt-1 text-sm text-slate-500">
                            Audit record #{auditLog.id}
                        </p>
                    </div>

                    <span className="inline-flex w-fit rounded-full bg-blue-50 px-3 py-1.5 text-sm font-bold capitalize text-blue-700 ring-1 ring-blue-600/20">
                        {auditLog.category}
                    </span>
                </div>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-200 bg-gradient-to-r from-slate-950 to-blue-950 px-6 py-6 text-white">
                        <div className="flex items-start gap-4">
                            <div className="rounded-2xl bg-white/10 p-3 ring-1 ring-white/20">
                                <ShieldCheck className="h-7 w-7" />
                            </div>

                            <div>
                                <p className="text-sm font-semibold uppercase tracking-wide text-blue-200">
                                    Recorded event
                                </p>
                                <h2 className="mt-1 text-xl font-bold">
                                    {auditLog.description ??
                                        humanize(
                                            auditLog.event,
                                        )}
                                </h2>
                                <p className="mt-2 text-sm text-slate-300">
                                    {formatDateTime(
                                        auditLog.occurred_at,
                                    )}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-3">
                        <DetailItem
                            icon={UserRound}
                            label="Actor"
                            value={`${auditLog.actor.name}${
                                auditLog.actor.email
                                    ? ` (${auditLog.actor.email})`
                                    : ''
                            }`}
                        />

                        <DetailItem
                            icon={ShieldCheck}
                            label="Roles"
                            value={
                                auditLog.actor.roles.length
                                    ? auditLog.actor.roles.join(
                                          ', ',
                                      )
                                    : 'No role snapshot'
                            }
                        />

                        <DetailItem
                            icon={CalendarClock}
                            label="Occurred"
                            value={formatDateTime(
                                auditLog.occurred_at,
                            )}
                        />

                        <DetailItem
                            icon={Database}
                            label="Subject"
                            value={
                                auditLog.auditable_name
                                    ? `${auditLog.auditable_name} #${auditLog.auditable_id}`
                                    : 'System event'
                            }
                        />

                        <DetailItem
                            icon={Network}
                            label="Parent"
                            value={
                                auditLog.parent_name
                                    ? `${auditLog.parent_name} #${auditLog.parent_id}`
                                    : 'No parent record'
                            }
                        />

                        <DetailItem
                            icon={Fingerprint}
                            label="Request ID"
                            value={
                                auditLog.request_id
                            }
                        />
                    </div>
                </div>

                {(auditLog.old_status ||
                    auditLog.new_status) && (
                    <div className="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                        <p className="text-xs font-bold uppercase tracking-wide text-blue-700">
                            Status transition
                        </p>

                        <div className="mt-3 flex flex-wrap items-center gap-3">
                            <span className="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200">
                                {auditLog.old_status ??
                                    'None'}
                            </span>

                            <span className="font-bold text-blue-500">
                                →
                            </span>

                            <span className="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                                {auditLog.new_status ??
                                    'None'}
                            </span>
                        </div>
                    </div>
                )}

                <div className="grid gap-6 xl:grid-cols-3">
                    <DataBlock
                        title="Old values"
                        value={auditLog.old_values}
                    />

                    <DataBlock
                        title="New values"
                        value={auditLog.new_values}
                    />

                    <DataBlock
                        title="Metadata"
                        value={auditLog.metadata}
                    />
                </div>

                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <h2 className="font-bold text-slate-800">
                            Request information
                        </h2>
                    </div>

                    <div className="grid gap-4 p-5 md:grid-cols-2">
                        <DetailItem
                            icon={Route}
                            label="Route"
                            value={
                                auditLog.route_name
                            }
                        />

                        <DetailItem
                            icon={Network}
                            label="HTTP method"
                            value={
                                auditLog.http_method
                            }
                        />

                        <DetailItem
                            icon={Globe2}
                            label="IP address"
                            value={
                                auditLog.ip_address
                            }
                        />

                        <DetailItem
                            icon={Globe2}
                            label="URL"
                            value={auditLog.url}
                        />

                        <div className="md:col-span-2">
                            <DetailItem
                                icon={Globe2}
                                label="User agent"
                                value={
                                    auditLog.user_agent
                                }
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
