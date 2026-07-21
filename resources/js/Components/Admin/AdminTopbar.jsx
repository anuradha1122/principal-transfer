import { Link, usePage } from '@inertiajs/react';
import NotificationBell from '@/Components/Notifications/NotificationBell';
import {
    ChevronDown,
    LogOut,
    Menu,
    UserRound,
} from 'lucide-react';
import { useState } from 'react';

export default function AdminTopbar({
    onMenuClick,
}) {
    const { auth } = usePage().props;

    const [profileOpen, setProfileOpen] =
        useState(false);

    const user = auth?.user ?? null;
    const roles = auth?.roles ?? [];

    return (
        <header className="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div className="flex h-20 items-center justify-between px-4 sm:px-6 lg:px-8">
                <div className="flex min-w-0 items-center gap-4">
                    <button
                        type="button"
                        onClick={onMenuClick}
                        aria-label="Open navigation menu"
                        className="rounded-xl border border-slate-200 p-2.5 text-slate-600 transition hover:bg-slate-50 lg:hidden"
                    >
                        <Menu className="h-5 w-5" />
                    </button>

                    <div className="min-w-0">
                        <p className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">
                            Administration
                        </p>

                        <h1 className="truncate text-lg font-bold text-slate-900">
                            Principal Transfer System
                        </h1>
                    </div>
                </div>

                <div className="flex items-center gap-2 sm:gap-3">
                    <NotificationBell />

                    <div className="relative">
                        <button
                            type="button"
                            onClick={() =>
                                setProfileOpen(
                                    (current) =>
                                        ! current
                                )
                            }
                            aria-expanded={
                                profileOpen
                            }
                            aria-haspopup="menu"
                            className="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-2 py-2 text-left transition hover:bg-slate-50 sm:px-3"
                        >
                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white">
                                <UserRound className="h-5 w-5" />
                            </div>

                            <div className="hidden min-w-0 sm:block">
                                <p className="max-w-48 truncate text-sm font-semibold text-slate-900">
                                    {user?.name ??
                                        'User'}
                                </p>

                                <p className="max-w-48 truncate text-xs text-slate-500">
                                    {roles[0] ??
                                        'User'}
                                </p>
                            </div>

                            <ChevronDown
                                className={[
                                    'hidden h-4 w-4 text-slate-400 transition-transform sm:block',
                                    profileOpen
                                        ? 'rotate-180'
                                        : '',
                                ].join(' ')}
                            />
                        </button>

                        {profileOpen && (
                            <>
                                <button
                                    type="button"
                                    aria-label="Close profile menu"
                                    onClick={() =>
                                        setProfileOpen(
                                            false
                                        )
                                    }
                                    className="fixed inset-0 z-40 cursor-default"
                                />

                                <div className="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                                    <div className="border-b border-slate-100 px-4 py-3">
                                        <p className="truncate text-sm font-semibold text-slate-900">
                                            {user?.name ??
                                                'User'}
                                        </p>

                                        <p className="truncate text-xs text-slate-500">
                                            {user?.email ??
                                                ''}
                                        </p>
                                    </div>

                                    <Link
                                        href={route(
                                            'profile.edit'
                                        )}
                                        onClick={() =>
                                            setProfileOpen(
                                                false
                                            )
                                        }
                                        className="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50"
                                    >
                                        <UserRound className="h-4 w-4" />
                                        My Profile
                                    </Link>

                                    <Link
                                        href={route(
                                            'logout'
                                        )}
                                        method="post"
                                        as="button"
                                        onClick={() =>
                                            setProfileOpen(
                                                false
                                            )
                                        }
                                        className="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50"
                                    >
                                        <LogOut className="h-4 w-4" />
                                        Sign Out
                                    </Link>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}
