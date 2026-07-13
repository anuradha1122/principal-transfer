import { Link, usePage } from '@inertiajs/react';
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
    const [profileOpen, setProfileOpen] = useState(false);

    const user = auth?.user;
    const roles = auth?.roles ?? [];

    return (
        <header className="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div className="flex h-20 items-center justify-between px-4 sm:px-6 lg:px-8">
                <div className="flex items-center gap-4">
                    <button
                        type="button"
                        onClick={onMenuClick}
                        className="rounded-xl border border-slate-200 p-2.5 text-slate-600 hover:bg-slate-50 lg:hidden"
                    >
                        <Menu className="h-5 w-5" />
                    </button>

                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">
                            Administration
                        </p>
                        <h1 className="text-lg font-bold text-slate-900">
                            Principal Transfer System
                        </h1>
                    </div>
                </div>

                <div className="relative">
                    <button
                        type="button"
                        onClick={() => setProfileOpen(!profileOpen)}
                        className="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-left hover:bg-slate-50"
                    >
                        <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                            <UserRound className="h-5 w-5" />
                        </div>

                        <div className="hidden sm:block">
                            <p className="max-w-48 truncate text-sm font-semibold text-slate-900">
                                {user?.name}
                            </p>
                            <p className="text-xs text-slate-500">
                                {roles[0] ?? 'User'}
                            </p>
                        </div>

                        <ChevronDown className="h-4 w-4 text-slate-400" />
                    </button>

                    {profileOpen && (
                        <div className="absolute right-0 mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <div className="border-b border-slate-100 px-4 py-3">
                                <p className="truncate text-sm font-semibold text-slate-900">
                                    {user?.name}
                                </p>
                                <p className="truncate text-xs text-slate-500">
                                    {user?.email}
                                </p>
                            </div>

                            <Link
                                href="/profile"
                                className="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50"
                            >
                                <UserRound className="h-4 w-4" />
                                My Profile
                            </Link>

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                className="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-red-600 hover:bg-red-50"
                            >
                                <LogOut className="h-4 w-4" />
                                Sign Out
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}
