export default function ZoneComparisonChart({
    items = [],
}) {
    const maximum = Math.max(
        ...items.map(
            (item) => Number(item.total ?? 0),
        ),
        1,
    );

    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <h2 className="text-base font-bold text-slate-900">
                    Applications by Zone
                </h2>

                <p className="mt-1 text-sm text-slate-500">
                    Comparison of transfer application volume by origin Zone.
                </p>
            </div>

            <div className="mt-6 space-y-4">
                {items.length === 0 ? (
                    <p className="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        No Zone data matches the selected filters.
                    </p>
                ) : (
                    items.map((item) => {
                        const percentage =
                            (Number(item.total ?? 0)
                                / maximum)
                            * 100;

                        return (
                            <div
                                key={
                                    item.zone_id
                                    ?? item.zone_name
                                }
                            >
                                <div className="mb-1.5 flex items-center justify-between gap-3">
                                    <span className="truncate text-sm font-medium text-slate-700">
                                        {item.zone_name}
                                    </span>

                                    <span className="text-sm font-bold text-slate-900">
                                        {Number(
                                            item.total ?? 0,
                                        ).toLocaleString()}
                                    </span>
                                </div>

                                <div className="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                    <div
                                        className="h-full rounded-full bg-emerald-600"
                                        style={{
                                            width: `${percentage}%`,
                                        }}
                                    />
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </div>
    );
}
