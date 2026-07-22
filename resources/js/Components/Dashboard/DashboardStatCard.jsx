import { ArrowUpRight } from 'lucide-react';

const toneClasses = {
    blue: {
        icon: 'bg-blue-50 text-blue-700',
        accent: 'bg-blue-600',
    },
    emerald: {
        icon: 'bg-emerald-50 text-emerald-700',
        accent: 'bg-emerald-600',
    },
    amber: {
        icon: 'bg-amber-50 text-amber-700',
        accent: 'bg-amber-500',
    },
    red: {
        icon: 'bg-red-50 text-red-700',
        accent: 'bg-red-600',
    },
    violet: {
        icon: 'bg-violet-50 text-violet-700',
        accent: 'bg-violet-600',
    },
    slate: {
        icon: 'bg-slate-100 text-slate-700',
        accent: 'bg-slate-700',
    },
    cyan: {
        icon: 'bg-cyan-50 text-cyan-700',
        accent: 'bg-cyan-600',
    },
    indigo: {
        icon: 'bg-indigo-50 text-indigo-700',
        accent: 'bg-indigo-600',
    },
};

export default function DashboardStatCard({
    title,
    value = 0,
    description,
    icon: Icon,
    tone = 'blue',
    trend,
    suffix,
}) {
    const classes =
        toneClasses[tone]
        ?? toneClasses.blue;

    return (
        <article className="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div
                className={[
                    'absolute inset-x-0 top-0 h-1',
                    classes.accent,
                ].join(' ')}
            />

            <div className="flex items-start justify-between gap-4">
                <div className="min-w-0">
                    <p className="text-sm font-semibold text-slate-500">
                        {title}
                    </p>

                    <div className="mt-2 flex items-end gap-1.5">
                        <p className="text-3xl font-bold tracking-tight text-slate-900">
                            {Number(
                                value ?? 0,
                            ).toLocaleString()}
                        </p>

                        {suffix && (
                            <span className="pb-1 text-sm font-semibold text-slate-500">
                                {suffix}
                            </span>
                        )}
                    </div>

                    {description && (
                        <p className="mt-2 text-xs leading-5 text-slate-500">
                            {description}
                        </p>
                    )}

                    {trend && (
                        <div className="mt-3 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700">
                            <ArrowUpRight className="h-3.5 w-3.5" />
                            {trend}
                        </div>
                    )}
                </div>

                {Icon && (
                    <div
                        className={[
                            'flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl transition group-hover:scale-105',
                            classes.icon,
                        ].join(' ')}
                    >
                        <Icon className="h-6 w-6" />
                    </div>
                )}
            </div>
        </article>
    );
}
