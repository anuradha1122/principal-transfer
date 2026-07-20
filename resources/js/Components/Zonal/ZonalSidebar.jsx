import { Link, usePage } from '@inertiajs/react';
import {
    ClipboardCheck,
    FileCheck2,
    Gauge,
    ListChecks,
    LogOut,
    X,
} from 'lucide-react';

const itemClasses = (active) =>
    [
        'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
        active
            ? 'bg-blue-600 text-white shadow-sm'
            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
    ].join(' ');

export default function ZonalSidebar({
    open = false,
    onClose = () => {},
}) {
    const page = usePage();

    const currentUrl =
        page?.url ?? window.location.pathname;

    const permissions =
        page?.props?.auth?.permissions ?? [];

    const user =
        page?.props?.auth?.user ?? null;

    const assignedZone =
        page?.props?.auth?.assignedZone ??
        page?.props?.auth?.assigned_zone ??
        null;

    const can = (permission) =>
        permissions.includes(permission);

    const isActive = (path) =>
        currentUrl === path ||
        currentUrl.startsWith(`${path}/`);

    const menuItems = [
        {
            label: 'Zonal Dashboard',
            href: route('zonal.dashboard'),
            path: '/zonal/dashboard',
            icon: Gauge,
            visible: can('view zonal dashboard'),
        },
        {
            label: 'Transfer Applications',
            href: route(
                'zonal.transfer-applications.index',
            ),
            path: '/zonal/transfer-applications',
            icon: ClipboardCheck,
            visible: can(
                'view zonal transfer applications',
            ),
        },
    ].filter((item) => item.visible);

    return (
        <>
            {open && (
                <button
                    type="button"
                    aria-label="Close sidebar"
                    onClick={onClose}
                    className="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden"
                />
            )}

            <aside
                className={[
                    'fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-300 lg:z-30 lg:translate-x-0 lg:shadow-none',
                    open
                        ? 'translate-x-0'
                        : '-translate-x-full',
                ].join(' ')}
            >
                <div className="flex h-20 items-center justify-between border-b border-slate-200 px-5">
                    <Link
                        href={route('zonal.dashboard')}
                        onClick={onClose}
                        className="flex min-w-0 items-center gap-3"
                    >
                        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm">
                            <ListChecks className="h-5 w-5" />
                        </div>

                        <div className="min-w-0">
                            <p className="truncate text-sm font-bold text-slate-900">
                                Zonal Review
                            </p>

                            <p className="truncate text-xs text-slate-500">
                                Principal Transfers
                            </p>
                        </div>
                    </Link>

                    <button
                        type="button"
                        onClick={onClose}
                        className="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                <div className="border-b border-slate-100 px-4 py-4">
                    <div className="rounded-2xl bg-slate-950 px-4 py-4 text-white">
                        <p className="text-xs font-semibold uppercase tracking-[0.18em] text-blue-300">
                            Assigned Zone
                        </p>

                        <p className="mt-2 truncate text-sm font-bold">
                            {assignedZone?.name
                                ? `${assignedZone.name} Zone`
                                : 'Zone not assigned'}
                        </p>

                        {assignedZone?.code && (
                            <p className="mt-1 text-xs text-slate-400">
                                Code: {assignedZone.code}
                            </p>
                        )}
                    </div>
                </div>

                <nav className="flex-1 overflow-y-auto p-4">
                    <div className="space-y-1">
                        {menuItems.map((item) => {
                            const Icon = item.icon;

                            return (
                                <Link
                                    key={item.label}
                                    href={item.href}
                                    onClick={onClose}
                                    className={itemClasses(
                                        isActive(item.path),
                                    )}
                                >
                                    <Icon className="h-5 w-5 shrink-0" />

                                    <span className="truncate">
                                        {item.label}
                                    </span>
                                </Link>
                            );
                        })}
                    </div>
                </nav>

                <div className="border-t border-slate-200 p-4">
                    <div className="rounded-xl bg-slate-50 p-3">
                        <div className="flex items-center gap-2 text-xs font-semibold text-slate-600">
                            <FileCheck2 className="h-4 w-4 shrink-0" />

                            <span>
                                Zone-restricted access
                            </span>
                        </div>
                    </div>

                    <div className="mt-3 flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white">
                            {user?.name
                                ?.charAt(0)
                                ?.toUpperCase() ?? 'Z'}
                        </div>

                        <div className="min-w-0 flex-1">
                            <p className="truncate text-sm font-bold text-slate-900">
                                {user?.name ??
                                    'Zonal Director'}
                            </p>

                            <p className="truncate text-xs text-slate-500">
                                Zonal Director
                            </p>
                        </div>
                    </div>

                    <Link
                        href={route('logout')}
                        method="post"
                        as="button"
                        className="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                    >
                        <LogOut className="h-4 w-4" />
                        Logout
                    </Link>
                </div>
            </aside>
        </>
    );
}
