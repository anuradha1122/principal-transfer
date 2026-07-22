import { Link } from '@inertiajs/react';
import {
    ChevronRight,
    Clock3,
} from 'lucide-react';

export default function RecentActivityList({
    items = [],
    emptyMessage = 'No recent activity is available.',
}) {
    if (items.length === 0) {
        return (
            <p className="rounded-xl bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {emptyMessage}
            </p>
        );
    }

    return (
        <div className="divide-y divide-slate-100">
            {items.map((item, index) => {
                const ActivityIcon =
                    item.icon ?? Clock3;

                const content = (
                    <div className="flex items-start gap-3 py-4">
                        <div className="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                            <ActivityIcon className="h-4 w-4" />
                        </div>

                        <div className="min-w-0 flex-1">
                            <div className="flex flex-wrap items-start justify-between gap-2">
                                <p className="text-sm font-semibold text-slate-900">
                                    {item.title}
                                </p>

                                {item.badge && (
                                    <span className="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-blue-700">
                                        {item.badge}
                                    </span>
                                )}
                            </div>

                            {item.description && (
                                <p className="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                    {item.description}
                                </p>
                            )}

                            {item.date && (
                                <p className="mt-2 text-[11px] font-medium text-slate-400">
                                    {item.date}
                                </p>
                            )}
                        </div>

                        {item.href && (
                            <ChevronRight className="mt-2 h-4 w-4 shrink-0 text-slate-300" />
                        )}
                    </div>
                );

                if (item.href) {
                    return (
                        <Link
                            key={
                                item.id
                                ?? `${item.title}-${index}`
                            }
                            href={item.href}
                            className="block px-1 transition hover:bg-slate-50"
                        >
                            {content}
                        </Link>
                    );
                }

                return (
                    <div
                        key={
                            item.id
                            ?? `${item.title}-${index}`
                        }
                        className="px-1"
                    >
                        {content}
                    </div>
                );
            })}
        </div>
    );
}
