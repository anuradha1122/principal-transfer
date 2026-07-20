import AdminSidebar from '@/Components/Admin/AdminSidebar';
import AdminTopbar from '@/Components/Admin/AdminTopbar';
import PrincipalSidebar from '@/Components/Principal/PrincipalSidebar';
import ProvincialSidebar from '@/Components/Provincial/ProvincialSidebar';
import ZonalSidebar from '@/Components/Zonal/ZonalSidebar';
import { Head, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function AdminLayout({
    title,
    header,
    children,
}) {
    const [sidebarOpen, setSidebarOpen] =
        useState(false);

    const page = usePage();

    const flash =
        page.props.flash ?? {};

    const roles =
        page.props.auth?.roles ?? [];

    /*
     * Sidebar priority:
     *
     * 1. Super Admin
     * 2. Principal
     * 3. Zonal Director
     * 4. Provincial Director
     * 5. Other administrative roles
     */

    const isSuperAdmin =
        roles.includes('Super Admin');

    const isPrincipal =
        roles.includes('Principal') &&
        !isSuperAdmin;

    const isZonalDirector =
        roles.includes('Zonal Director') &&
        !isSuperAdmin &&
        !isPrincipal;

    const isProvincialDirector =
        roles.includes('Provincial Director') &&
        !isSuperAdmin &&
        !isPrincipal &&
        !isZonalDirector;

    const closeSidebar = () => {
        setSidebarOpen(false);
    };

    const openSidebar = () => {
        setSidebarOpen(true);
    };

    useEffect(() => {
        closeSidebar();
    }, [page.url]);

    useEffect(() => {
        const handleEscape = (event) => {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        };

        window.addEventListener(
            'keydown',
            handleEscape,
        );

        return () => {
            window.removeEventListener(
                'keydown',
                handleEscape,
            );
        };
    }, []);

    useEffect(() => {
        if (sidebarOpen) {
            document.body.style.overflow =
                'hidden';
        } else {
            document.body.style.overflow =
                '';
        }

        return () => {
            document.body.style.overflow =
                '';
        };
    }, [sidebarOpen]);

    const renderSidebar = () => {
        if (isPrincipal) {
            return (
                <PrincipalSidebar
                    open={sidebarOpen}
                    onClose={closeSidebar}
                />
            );
        }

        if (isZonalDirector) {
            return (
                <ZonalSidebar
                    open={sidebarOpen}
                    onClose={closeSidebar}
                />
            );
        }

        if (isProvincialDirector) {
            return (
                <ProvincialSidebar
                    open={sidebarOpen}
                    onClose={closeSidebar}
                />
            );
        }

        return (
            <AdminSidebar
                open={sidebarOpen}
                onClose={closeSidebar}
            />
        );
    };

    return (
        <div className="min-h-screen bg-slate-100">
            <Head title={title} />

            {renderSidebar()}

            <div className="min-h-screen lg:pl-72">
                <AdminTopbar
                    onMenuClick={openSidebar}
                />

                <main className="px-4 py-6 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        {flash.success && (
                            <div className="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                                {flash.success}
                            </div>
                        )}

                        {flash.error && (
                            <div className="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                                {flash.error}
                            </div>
                        )}

                        {flash.warning && (
                            <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                                {flash.warning}
                            </div>
                        )}

                        {flash.info && (
                            <div className="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-800">
                                {flash.info}
                            </div>
                        )}

                        {header && (
                            <div className="mb-6">
                                {header}
                            </div>
                        )}

                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
