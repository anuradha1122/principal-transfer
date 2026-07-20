import { Link, usePage } from '@inertiajs/react';
import {
    FileText,
    Files,
    GraduationCap,
    LayoutDashboard,
    UserRound,
    X,
} from 'lucide-react';

export default function PrincipalSidebar({
    open = false,
    onClose = () => {},
}) {
    const page = usePage();

    const currentUrl =
        page.url ??
        window.location.pathname;

    const permissions =
        page.props?.auth?.permissions ?? [];

    const can = (permission) =>
        permissions.includes(permission);

    const menuItems = [
        {
            label: 'Dashboard',
            href: route(
                'principal.dashboard',
            ),
            path: '/principal/dashboard',
            icon: LayoutDashboard,
            visible: can(
                'view principal dashboard',
            ),
        },
        {
            label: 'My Profile',
            href: route(
                'principal.profile.show',
            ),
            path: '/principal/profile',
            icon: UserRound,
            visible: can(
                'edit own principal profile',
            ),
        },
        {
            label: 'Transfer Applications',
            href: route(
                'principal.transfer-applications.index',
            ),
            path:
                '/principal/transfer-applications',
            icon: FileText,
            visible: can(
                'view own transfer applications',
            ),
        },
        {
            label: 'Transfer Documents',
            href: route(
                'principal.transfer-documents.index',
            ),
            path:
                '/principal/transfer-documents',
            icon: Files,
            visible: can(
                'view own transfer documents',
            ),
        },
    ].filter((item) => item.visible);

    const normalizePath = (value) => {
        if (!value) {
            return '/';
        }

        const path =
            value
                .split('?')[0]
                .split('#')[0];

        if (path.length > 1) {
            return path.replace(
                /\/+$/,
                '',
            );
        }

        return path;
    };

    const isActive = (path) => {
        const currentPath =
            normalizePath(
                currentUrl,
            );

        const menuPath =
            normalizePath(path);

        return (
            currentPath === menuPath ||
            currentPath.startsWith(
                `${menuPath}/`,
            )
        );
    };

    return (
        <>
            <button
                type="button"
                aria-label="Close sidebar overlay"
                className={[
                    'fixed inset-0 z-40 bg-slate-950/60 transition-opacity duration-200 lg:hidden',
                    open
                        ? 'opacity-100'
                        : 'pointer-events-none opacity-0',
                ].join(' ')}
                onClick={onClose}
            />

            <aside
                className={[
                    'fixed inset-y-0 left-0 z-50 w-72 transform transition-transform duration-200 lg:translate-x-0',
                    open
                        ? 'translate-x-0'
                        : '-translate-x-full',
                ].join(' ')}
            >
                <div className="flex h-full flex-col bg-slate-950 text-white">
                    <div className="flex h-20 items-center justify-between border-b border-white/10 px-5">
                        <Link
                            href={route(
                                'principal.dashboard',
                            )}
                            onClick={onClose}
                            className="flex min-w-0 items-center gap-3"
                        >
                            <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600">
                                <GraduationCap className="h-6 w-6" />
                            </div>

                            <div className="min-w-0">
                                <p className="truncate text-sm font-bold">
                                    Principal Transfer
                                </p>

                                <p className="truncate text-xs text-slate-400">
                                    Principal Portal
                                </p>
                            </div>
                        </Link>

                        <button
                            type="button"
                            onClick={onClose}
                            aria-label="Close sidebar"
                            className="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white lg:hidden"
                        >
                            <X className="h-5 w-5" />
                        </button>
                    </div>

                    <nav className="flex-1 space-y-1 overflow-y-auto px-3 py-5">
                        {menuItems.map(
                            (item) => {
                                const Icon =
                                    item.icon;

                                const active =
                                    isActive(
                                        item.path,
                                    );

                                return (
                                    <Link
                                        key={
                                            item.label
                                        }
                                        href={
                                            item.href
                                        }
                                        onClick={
                                            onClose
                                        }
                                        className={[
                                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                                            active
                                                ? 'bg-blue-600 text-white shadow-lg shadow-blue-950/30'
                                                : 'text-slate-300 hover:bg-white/10 hover:text-white',
                                        ].join(
                                            ' ',
                                        )}
                                    >
                                        <Icon className="h-5 w-5 shrink-0" />

                                        <span className="truncate">
                                            {
                                                item.label
                                            }
                                        </span>

                                        {active && (
                                            <span className="ml-auto h-2 w-2 shrink-0 rounded-full bg-white" />
                                        )}
                                    </Link>
                                );
                            },
                        )}
                    </nav>

                    <div className="border-t border-white/10 px-5 py-4">
                        <p className="text-xs leading-5 text-slate-500">
                            Provincial Department
                            of Education
                        </p>

                        <p className="text-xs leading-5 text-slate-500">
                            Sabaragamuwa Province
                        </p>
                    </div>
                </div>
            </aside>
        </>
    );
}
