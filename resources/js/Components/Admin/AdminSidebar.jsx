import { Link, usePage } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import {
    BarChart3,
    Bell,
    Building2,
    CalendarRange,
    ChartNoAxesCombined,
    ClipboardCheck,
    Files,
    FileText,
    GraduationCap,
    LayoutDashboard,
    Map,
    School,
    Settings,
    ShieldCheck,
    UserRoundCheck,
    Users,
    X,
} from 'lucide-react';

export default function AdminSidebar({
    open,
    onClose,
}) {
    const page = usePage();

    const permissions =
        page.props.auth?.permissions ?? [];

    const roles =
        page.props.auth?.roles ?? [];

    const currentUrl =
        page.url ??
        window.location.pathname;

    const can = (permission) => {
        return (
            roles.includes('Super Admin') ||
            permissions.includes(permission)
        );
    };

    const menuItems = [
        {
            label: 'Dashboard',
            href: '/admin/dashboard',
            icon: LayoutDashboard,
            visible: can(
                'view admin dashboard',
            ),
        },
        {
            label: 'Users',
            href: '/admin/users',
            icon: Users,
            visible: can('view users'),
        },
        {
            label: 'Roles & Permissions',
            href: '/admin/roles',
            icon: ShieldCheck,
            visible: can(
                'manage roles and permissions',
            ),
        },
        {
            label: 'Zones',
            href: '/admin/zones',
            icon: Map,
            visible: can('view zones'),
        },
        {
            label: 'Divisions',
            href: '/admin/divisions',
            icon: Building2,
            visible: can('view divisions'),
        },
        {
            label: 'Schools',
            href: '/admin/schools',
            icon: School,
            visible: can('view schools'),
        },
        {
            label: 'Principal Registry',
            href: '/admin/principal-registry',
            icon: UserRoundCheck,
            visible: can(
                'view principal registry',
            ),
        },
        {
            label: 'Principal Profiles',
            href: '/admin/principal-profiles',
            icon: GraduationCap,
            visible: can(
                'view principal profiles',
            ),
        },
        {
            label: 'Transfer Cycles',
            href: '/admin/transfer-cycles',
            icon: CalendarRange,
            visible: can('view transfer cycles'),
        },
        {
            label: 'Transfer Applications',
            href: '/admin/transfer-applications',
            icon: FileText,
            visible: can('view transfer applications'),
        },
        {
            label: 'Notifications',
            href: route(
                'notifications.index',
            ),
            path: '/notifications',
            icon: Bell,
            visible: can(
                'view notifications',
            ),
            badge:
                page?.props?.notifications
                    ?.unread_count ?? 0,
        },
        {
            label: 'Transfer Board',
            href: '/admin/transfer-board',
            icon: ClipboardCheck,
            visible: can(
                'view board transfer applications',
            ),
            disabled: true,
        },
        {
            label: 'Transfer Documents',
            href: route(
                'admin.transfer-documents.index',
            ),
            icon: Files,
            visible: can(
                'view transfer documents',
            ),
        },
        {
            label: 'Transfer Reports',
            href: route(
                'admin.reports.index'
            ),
            path:
                '/admin/reports',
            icon: BarChart3,
            visible: can(
                'view reports'
            ),
        },
        {
            label: 'Reports & Analytics',
            href: route('reports.index'),
            path: '/reports',
            icon: ChartNoAxesCombined,
            visible:
                can('view management reports')
                || can('view provincial reports')
                || can('view zonal reports')
                || can('view transfer board reports')
                || can('view personal reports'),
        },
        {
            label: 'Audit Logs',
            href: route(
                'admin.audit-logs.index'
            ),
            path:
                '/admin/audit-logs',
            icon: ShieldCheck,
            visible: can(
                'view audit logs'
            ),
        },
        {
            label: 'System Settings',
            href: '/admin/settings',
            icon: Settings,
            visible: can(
                'manage system settings',
            ),
            disabled: true,
        },
    ];

    const normalizePath = (value) => {
        if (!value) {
            return '/';
        }

        const path =
            value.split('?')[0].split('#')[0];

        if (path.length > 1) {
            return path.replace(/\/+$/, '');
        }

        return path;
    };

    const isActive = (href) => {
        const currentPath =
            normalizePath(currentUrl);

        const menuPath =
            normalizePath(href);

        return (
            currentPath === menuPath ||
            currentPath.startsWith(
                `${menuPath}/`,
            )
        );
    };

    const sidebarContent = (
        <div className="flex h-full flex-col bg-slate-950 text-white">
            <div className="flex h-20 items-center justify-between border-b border-white/10 px-5">
                <Link
                    href="/admin/dashboard"
                    onClick={onClose}
                    className="flex items-center gap-3"
                >
                    <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600">
                        <GraduationCap className="h-6 w-6" />
                    </div>

                    <div>
                        <p className="text-sm font-bold">
                            Principal Transfer
                        </p>

                        <p className="text-xs text-slate-400">
                            Sabaragamuwa Province
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
                {menuItems
                    .filter(
                        (item) =>
                            item.visible,
                    )
                    .map((item) => {
                        const Icon =
                            item.icon;

                        const active =
                            isActive(
                                item.href,
                            );

                        if (item.disabled) {
                            return (
                                <div
                                    key={
                                        item.label
                                    }
                                    title="Available in a later module"
                                    className="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600"
                                >
                                    <Icon className="h-5 w-5 shrink-0" />

                                    <span>
                                        {
                                            item.label
                                        }
                                    </span>

                                    <span className="ml-auto rounded-full bg-white/5 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-600">
                                        Soon
                                    </span>
                                </div>
                            );
                        }

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

                                <span>
                                    {
                                        item.label
                                    }
                                </span>

                                {active && (
                                    <span className="ml-auto h-2 w-2 rounded-full bg-white" />
                                )}
                            </Link>
                        );
                    })}
            </nav>

            <div className="border-t border-white/10 px-5 py-4">
                <p className="text-xs leading-5 text-slate-500">
                    Provincial Department of
                    Education
                </p>

                <p className="text-xs leading-5 text-slate-500">
                    Sabaragamuwa Province
                </p>
            </div>
        </div>
    );

    return (
        <>
            <div
                role="button"
                tabIndex={-1}
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
                {sidebarContent}
            </aside>
        </>
    );
}
