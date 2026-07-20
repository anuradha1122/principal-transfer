import AdminSidebar from '@/Components/Admin/AdminSidebar';
import AdminTopbar from '@/Components/Admin/AdminTopbar';
import PrincipalSidebar from '@/Components/Principal/PrincipalSidebar';
import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

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
     * A Super Admin may also temporarily have the Principal role.
     * In that situation, the admin sidebar must take priority.
     */
    const isPrincipal =
        roles.includes('Principal') &&
        !roles.includes('Super Admin');

    const closeSidebar = () => {
        setSidebarOpen(false);
    };

    const openSidebar = () => {
        setSidebarOpen(true);
    };

    return (
        <div className="min-h-screen bg-slate-100">
            <Head title={title} />

            {isPrincipal ? (
                <PrincipalSidebar
                    open={sidebarOpen}
                    onClose={closeSidebar}
                />
            ) : (
                <AdminSidebar
                    open={sidebarOpen}
                    onClose={closeSidebar}
                />
            )}

            <div className="lg:pl-72">
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
