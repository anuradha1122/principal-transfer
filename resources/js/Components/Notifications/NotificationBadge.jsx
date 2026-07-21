export default function NotificationBadge({
    count = 0,
    className = '',
}) {
    if (!count || count <= 0) {
        return null;
    }

    const label = count > 99 ? '99+' : count;

    return (
        <span
            className={`inline-flex min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-white ${className}`}
        >
            {label}
        </span>
    );
}
