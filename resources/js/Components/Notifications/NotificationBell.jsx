import NotificationBadge from '@/Components/Notifications/NotificationBadge';
import NotificationItem from '@/Components/Notifications/NotificationItem';
import { Link, router, usePage } from '@inertiajs/react';
import {
    Bell,
    CheckCheck,
    Inbox,
    X,
} from 'lucide-react';
import {
    useEffect,
    useRef,
    useState,
} from 'react';

export default function NotificationBell() {
    const page = usePage();

    const permissions =
        page.props.auth?.permissions ?? [];

    const roles =
        page.props.auth?.roles ?? [];

    const canView =
        roles.includes('Super Admin') ||
        permissions.includes(
            'view notifications',
        );

    const notificationProps =
        page.props.notifications ?? {};

    const recentNotifications =
        Array.isArray(
            notificationProps.recent,
        )
            ? notificationProps.recent
            : Array.isArray(
                  notificationProps.items,
              )
                ? notificationProps.items
                : Array.isArray(
                      notificationProps.notifications,
                  )
                    ? notificationProps.notifications
                    : Array.isArray(
                          notificationProps,
                      )
                        ? notificationProps
                        : [];

    const unreadCount = Number(
        notificationProps.unread_count
            ?? notificationProps.unreadCount
            ?? 0,
    );

    const [open, setOpen] =
        useState(false);

    const wrapperRef =
        useRef(null);

    useEffect(() => {
        const handleOutsideClick = (
            event,
        ) => {
            if (
                wrapperRef.current &&
                !wrapperRef.current.contains(
                    event.target,
                )
            ) {
                setOpen(false);
            }
        };

        document.addEventListener(
            'mousedown',
            handleOutsideClick,
        );

        return () => {
            document.removeEventListener(
                'mousedown',
                handleOutsideClick,
            );
        };
    }, []);

    useEffect(() => {
        setOpen(false);
    }, [page.url]);

    if (!canView) {
        return null;
    }

    const markAllAsRead = () => {
        router.post(
            route(
                'notifications.mark-all-as-read',
            ),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    setOpen(false);
                },
            },
        );
    };

    return (
        <div
            ref={wrapperRef}
            className="relative"
        >
            <button
                type="button"
                onClick={() =>
                    setOpen(
                        (current) =>
                            !current,
                    )
                }
                className={[
                    'relative inline-flex h-10 w-10 items-center justify-center rounded-xl border transition',
                    open
                        ? 'border-blue-300 bg-blue-50 text-blue-700'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-blue-300 hover:text-blue-700',
                ].join(' ')}
                aria-label="Notifications"
                aria-expanded={open}
            >
                <Bell className="h-5 w-5" />

                <NotificationBadge
                    count={unreadCount}
                    className="absolute -right-1.5 -top-1.5"
                />
            </button>

            {open && (
                <div className="absolute right-0 z-50 mt-3 w-[22rem] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:w-[26rem]">
                    <div className="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <div>
                            <h2 className="font-bold text-slate-900">
                                Notifications
                            </h2>

                            <p className="text-xs text-slate-500">
                                {unreadCount}{' '}
                                unread
                            </p>
                        </div>

                        <div className="flex items-center gap-1">
                            {unreadCount >
                                0 && (
                                <button
                                    type="button"
                                    onClick={
                                        markAllAsRead
                                    }
                                    className="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-white hover:text-blue-700"
                                    title="Mark all as read"
                                    aria-label="Mark all notifications as read"
                                >
                                    <CheckCheck className="h-4 w-4" />
                                </button>
                            )}

                            <button
                                type="button"
                                onClick={() =>
                                    setOpen(
                                        false,
                                    )
                                }
                                className="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-white hover:text-slate-800"
                                aria-label="Close notifications"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <div className="max-h-[28rem] divide-y divide-slate-100 overflow-y-auto">
                        {recentNotifications.length ===
                        0 ? (
                            <div className="px-6 py-12 text-center">
                                <Inbox className="mx-auto h-9 w-9 text-slate-300" />

                                <p className="mt-3 text-sm font-semibold text-slate-700">
                                    No notifications
                                </p>

                                <p className="mt-1 text-xs text-slate-500">
                                    New workflow
                                    updates will
                                    appear here.
                                </p>
                            </div>
                        ) : (
                            recentNotifications.map(
                                (
                                    notification,
                                ) => (
                                    <NotificationItem
                                        key={
                                            notification.id
                                        }
                                        notification={
                                            notification
                                        }
                                        compact
                                    />
                                ),
                            )
                        )}
                    </div>

                    <div className="border-t border-slate-200 bg-slate-50 p-3">
                        <Link
                            href={route(
                                'notifications.index',
                            )}
                            onClick={() =>
                                setOpen(false)
                            }
                            className="flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            View all
                            notifications
                        </Link>
                    </div>
                </div>
            )}
        </div>
    );
}
