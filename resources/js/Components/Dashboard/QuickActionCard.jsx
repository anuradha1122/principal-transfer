import { Link } from '@inertiajs/react';
import {
    ArrowUpRight,
} from 'lucide-react';

const tones = {
    blue: 'bg-blue-50 text-blue-700 group-hover:bg-blue-100',
    emerald: 'bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100',
    amber: 'bg-amber-50 text-amber-700 group-hover:bg-amber-100',
    violet: 'bg-violet-50 text-violet-700 group-hover:bg-violet-100',
    slate: 'bg-slate-100 text-slate-700 group-hover:bg-slate-200',
    cyan: 'bg-cyan-50 text-cyan-700 group-hover:bg-cyan-100',
    red: 'bg-red-50 text-red-700 group-hover:bg-red-100',
    indigo: 'bg-indigo-50 text-indigo-700 group-hover:bg-indigo-100',
};

export default function QuickActionCard({
    title,
    description,
    href,
    icon: Icon,
    tone = 'blue',
}) {
    if (! href) {
        return null;
    }

    return (
        <Link
            href={href}
            className="group flex min-h-32 flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
        >
            <div className="flex items-start justify-between gap-3">
                {Icon && (
                    <div
                        className={[
                            'flex h-11 w-11 items-center justify-center rounded-xl transition',
                            tones[tone]
                            ?? tones.blue,
                        ].join(' ')}
                    >
                        <Icon className="h-5 w-5" />
                    </div>
                )}

                <ArrowUpRight className="h-4 w-4 text-slate-300 transition group-hover:text-blue-600" />
            </div>

            <div className="mt-5">
                <h3 className="font-bold text-slate-900">
                    {title}
                </h3>

                {description && (
                    <p className="mt-1 text-xs leading-5 text-slate-500">
                        {description}
                    </p>
                )}
            </div>
        </Link>
    );
}
