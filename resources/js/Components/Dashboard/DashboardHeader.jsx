import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    CalendarDays,
} from 'lucide-react';

const accentClasses = {
    admin: 'from-slate-950 via-slate-900 to-blue-900',
    principal: 'from-blue-800 via-blue-700 to-cyan-600',
    zonal: 'from-emerald-800 via-emerald-700 to-teal-600',
    provincial: 'from-indigo-900 via-indigo-800 to-blue-700',
    board: 'from-violet-900 via-violet-800 to-indigo-700',
};

export default function DashboardHeader({
    eyebrow = 'Dashboard',
    title,
    description,
    userName,
    role,
    accent = 'admin',
    actionLabel,
    actionHref,
    icon: Icon,
}) {
    const today = new Intl.DateTimeFormat(
        'en-LK',
        {
            dateStyle: 'full',
        },
    ).format(new Date());

    return (
        <section
            className={[
                'relative overflow-hidden rounded-3xl bg-gradient-to-br p-6 text-white shadow-xl sm:p-8',
                accentClasses[accent]
                    ?? accentClasses.admin,
            ].join(' ')}
        >
            <div className="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl" />

            <div className="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-blue-300/10 blur-3xl" />

            <div className="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div className="max-w-3xl">
                    <div className="flex items-center gap-2 text-sm font-semibold text-white/75">
                        {Icon && (
                            <Icon className="h-4 w-4" />
                        )}

                        <span>
                            {eyebrow}
                        </span>
                    </div>

                    <h1 className="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                        {title}
                    </h1>

                    {description && (
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-white/75 sm:text-base">
                            {description}
                        </p>
                    )}

                    <div className="mt-5 flex flex-wrap items-center gap-3 text-sm">
                        {userName && (
                            <span className="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 font-semibold backdrop-blur">
                                {userName}
                            </span>
                        )}

                        {role && (
                            <span className="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-white/85 backdrop-blur">
                                {role}
                            </span>
                        )}

                        <span className="inline-flex items-center gap-2 text-white/70">
                            <CalendarDays className="h-4 w-4" />
                            {today}
                        </span>
                    </div>
                </div>

                {actionLabel && actionHref && (
                    <Link
                        href={actionHref}
                        className="inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-900 shadow-lg transition hover:-translate-y-0.5 hover:bg-slate-50"
                    >
                        {actionLabel}

                        <ArrowRight className="h-4 w-4" />
                    </Link>
                )}
            </div>
        </section>
    );
}
