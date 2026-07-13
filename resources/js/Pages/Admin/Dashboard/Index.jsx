import AdminLayout from '@/Layouts/AdminLayout';
import {
    ArrowRight,
    FileCheck2,
    GraduationCap,
    KeyRound,
    MapPinned,
    School,
    ShieldCheck,
    Users,
} from 'lucide-react';

function StatisticCard({
    label,
    value,
    description,
    icon: Icon,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-sm font-medium text-slate-500">
                        {label}
                    </p>
                    <p className="mt-3 text-3xl font-bold text-slate-950">
                        {value}
                    </p>
                    <p className="mt-2 text-xs leading-5 text-slate-500">
                        {description}
                    </p>
                </div>

                <div className="rounded-xl bg-blue-50 p-3 text-blue-600">
                    <Icon className="h-6 w-6" />
                </div>
            </div>
        </div>
    );
}

export default function Index({
    statistics,
}) {
    const cards = [
        {
            label: 'System Users',
            value: statistics.users,
            description: 'All registered system accounts',
            icon: Users,
        },
        {
            label: 'Principals',
            value: statistics.principals,
            description: 'Registered principal accounts',
            icon: GraduationCap,
        },
        {
            label: 'System Roles',
            value: statistics.roles,
            description: 'Configured access roles',
            icon: ShieldCheck,
        },
        {
            label: 'Permissions',
            value: statistics.permissions,
            description: 'Available system capabilities',
            icon: KeyRound,
        },
    ];

    const modules = [
        {
            title: 'Organization Structure',
            description:
                'Manage seven zones, divisions and schools.',
            icon: MapPinned,
            status: 'Next module',
        },
        {
            title: 'Principal Registry',
            description:
                'Import eligible NIC numbers and control registration.',
            icon: GraduationCap,
            status: 'Planned',
        },
        {
            title: 'Transfer Applications',
            description:
                'Submit, review and track transfer applications.',
            icon: FileCheck2,
            status: 'Planned',
        },
        {
            title: 'School Directory',
            description:
                'Maintain official school information and hierarchy.',
            icon: School,
            status: 'Planned',
        },
    ];

    return (
        <AdminLayout
            title="Administration Dashboard"
            header={
                <div className="rounded-2xl bg-gradient-to-r from-slate-950 to-blue-950 px-6 py-7 text-white shadow-lg">
                    <p className="text-sm font-semibold text-blue-200">
                        Provincial Department of Education
                    </p>

                    <h2 className="mt-2 text-2xl font-bold sm:text-3xl">
                        Principal Transfer Administration
                    </h2>

                    <p className="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                        Manage principal transfer applications across
                        Sabaragamuwa Province through a transparent,
                        traceable approval workflow.
                    </p>
                </div>
            }
        >
            <div className="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                {cards.map((card) => (
                    <StatisticCard
                        key={card.label}
                        {...card}
                    />
                ))}
            </div>

            <div className="mt-8 grid gap-6 xl:grid-cols-3">
                <section className="xl:col-span-2">
                    <div className="mb-4 flex items-center justify-between">
                        <div>
                            <h3 className="text-lg font-bold text-slate-900">
                                System Modules
                            </h3>
                            <p className="mt-1 text-sm text-slate-500">
                                Core components of the transfer workflow
                            </p>
                        </div>
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2">
                        {modules.map((module) => {
                            const Icon = module.icon;

                            return (
                                <div
                                    key={module.title}
                                    className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                                >
                                    <div className="flex items-start justify-between">
                                        <div className="rounded-xl bg-slate-100 p-3 text-slate-700">
                                            <Icon className="h-6 w-6" />
                                        </div>

                                        <span className="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {module.status}
                                        </span>
                                    </div>

                                    <h4 className="mt-5 font-bold text-slate-900">
                                        {module.title}
                                    </h4>

                                    <p className="mt-2 text-sm leading-6 text-slate-500">
                                        {module.description}
                                    </p>
                                </div>
                            );
                        })}
                    </div>
                </section>

                <aside className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 className="text-lg font-bold text-slate-900">
                        Transfer Workflow
                    </h3>

                    <p className="mt-1 text-sm text-slate-500">
                        MVP approval stages
                    </p>

                    <div className="mt-6 space-y-3">
                        {[
                            'Principal submits application',
                            'Zonal Director reviews',
                            'Provincial Director reviews',
                            'Transfer Board adds result',
                            'Principal receives final decision',
                        ].map((stage, index) => (
                            <div
                                key={stage}
                                className="flex items-center gap-3"
                            >
                                <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                                    {index + 1}
                                </div>

                                <p className="text-sm font-medium text-slate-700">
                                    {stage}
                                </p>

                                {index < 4 && (
                                    <ArrowRight className="ml-auto h-4 w-4 text-slate-300" />
                                )}
                            </div>
                        ))}
                    </div>
                </aside>
            </div>
        </AdminLayout>
    );
}
