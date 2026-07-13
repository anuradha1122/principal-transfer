import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AdminSidebar from '@/Components/Admin/AdminSidebar';
import AdminTopbar from '@/Components/Admin/AdminTopbar';

export default function AdminLayout({
    title,
    header,
    children,
}) {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { flash } = usePage().props;

    return (
        <div className="min-h-screen bg-slate-100">
            <Head title={title} />

            <AdminSidebar
                open={sidebarOpen}
                onClose={() => setSidebarOpen(false)}
            />

            <div className="lg:pl-72">
                <AdminTopbar
                    onMenuClick={() => setSidebarOpen(true)}
                />

                <main className="px-4 py-6 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        {flash?.success && (
                            <div className="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                {flash.success}
                            </div>
                        )}

                        {flash?.error && (
                            <div className="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                                {flash.error}
                            </div>
                        )}

                        {flash?.warning && (
                            <div className="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                {flash.warning}
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
