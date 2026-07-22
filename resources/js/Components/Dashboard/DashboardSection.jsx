import { Link } from '@inertiajs/react';
import {
    ArrowRight,
} from 'lucide-react';

export default function DashboardSection({
    title,
    description,
    icon: Icon,
    actionLabel,
    actionHref,
    children,
    className = '',
    noPadding = false,
}) {
    return (
        <section
            className={[
                'overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm',
                className,
            ].join(' ')}
        >
            <div className="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div className="flex items-start gap-3">
                    {Icon && (
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                            <Icon className="h-5 w-5" />
                        </div>
                    )}

                    <div>
                        <h2 className="text-base font-bold text-slate-900">
                            {title}
                        </h2>

                        {description && (
                            <p className="mt-1 text-sm text-slate-500">
                                {description}
                            </p>
                        )}
                    </div>
                </div>

                {actionLabel && actionHref && (
                    <Link
                        href={actionHref}
                        className="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900"
                    >
                        {actionLabel}

                        <ArrowRight className="h-4 w-4" />
                    </Link>
                )}
            </div>

            <div
                className={
                    noPadding
                        ? ''
                        : 'p-5'
                }
            >
                {children}
            </div>
        </section>
    );
}
