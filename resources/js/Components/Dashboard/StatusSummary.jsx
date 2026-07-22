const toneClasses = {
    blue: {
        dot: 'bg-blue-500',
        badge: 'bg-blue-50 text-blue-700',
    },
    emerald: {
        dot: 'bg-emerald-500',
        badge: 'bg-emerald-50 text-emerald-700',
    },
    amber: {
        dot: 'bg-amber-500',
        badge: 'bg-amber-50 text-amber-700',
    },
    red: {
        dot: 'bg-red-500',
        badge: 'bg-red-50 text-red-700',
    },
    violet: {
        dot: 'bg-violet-500',
        badge: 'bg-violet-50 text-violet-700',
    },
    slate: {
        dot: 'bg-slate-500',
        badge: 'bg-slate-100 text-slate-700',
    },
};

export default function StatusSummary({
    items = [],
}) {
    const maximum = Math.max(
        ...items.map(
            (item) => Number(
                item.value ?? 0,
            ),
        ),
        1,
    );

    if (items.length === 0) {
        return (
            <p className="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                No status data is available.
            </p>
        );
    }

    return (
        <div className="space-y-4">
            {items.map((item) => {
                const tone =
                    toneClasses[item.tone]
                    ?? toneClasses.blue;

                const percentage =
                    (
                        Number(
                            item.value ?? 0,
                        )
                        / maximum
                    )
                    * 100;

                return (
                    <div
                        key={item.key ?? item.label}
                    >
                        <div className="mb-2 flex items-center justify-between gap-4">
                            <div className="flex min-w-0 items-center gap-2">
                                <span
                                    className={[
                                        'h-2.5 w-2.5 shrink-0 rounded-full',
                                        tone.dot,
                                    ].join(' ')}
                                />

                                <span className="truncate text-sm font-semibold text-slate-700">
                                    {item.label}
                                </span>
                            </div>

                            <span
                                className={[
                                    'rounded-full px-2.5 py-1 text-xs font-bold',
                                    tone.badge,
                                ].join(' ')}
                            >
                                {Number(
                                    item.value ?? 0,
                                ).toLocaleString()}
                            </span>
                        </div>

                        <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div
                                className={[
                                    'h-full rounded-full',
                                    tone.dot,
                                ].join(' ')}
                                style={{
                                    width: `${percentage}%`,
                                }}
                            />
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
