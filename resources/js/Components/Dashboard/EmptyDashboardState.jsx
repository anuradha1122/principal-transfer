import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    Inbox,
} from 'lucide-react';

export default function EmptyDashboardState({
    title = 'Nothing to display',
    description = 'There is no information available for this section yet.',
    actionLabel,
    actionHref,
    icon: Icon = Inbox,
}) {
    return (
        <div className="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
            <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm">
                <Icon className="h-6 w-6" />
            </div>

            <h3 className="mt-4 text-base font-bold text-slate-900">
                {title}
            </h3>

            <p className="mt-2 max-w-md text-sm leading-6 text-slate-500">
                {description}
            </p>

            {actionLabel && actionHref && (
                <Link
                    href={actionHref}
                    className="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800"
                >
                    {actionLabel}

                    <ArrowRight className="h-4 w-4" />
                </Link>
            )}
        </div>
    );
}
