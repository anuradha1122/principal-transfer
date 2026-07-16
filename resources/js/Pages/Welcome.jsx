import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    BadgeCheck,
    Building2,
    GraduationCap,
    LockKeyhole,
    ShieldCheck,
    UserPlus,
} from 'lucide-react';

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Principal Transfer System" />

            <div className="min-h-screen bg-slate-50">
                <header className="border-b border-slate-200 bg-white">
                    <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8">
                        <div className="flex items-center gap-3">
                            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white">
                                <GraduationCap className="h-6 w-6" />
                            </div>

                            <div>
                                <p className="font-bold text-slate-900">
                                    Principal Transfer System
                                </p>

                                <p className="text-xs text-slate-500">
                                    Sabaragamuwa Province
                                </p>
                            </div>
                        </div>

                        <nav className="flex items-center gap-3">
                            {auth?.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                >
                                    Dashboard
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('login')}
                                        className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                    >
                                        <LockKeyhole className="h-4 w-4" />
                                        Sign In
                                    </Link>

                                    <Link
                                        href={route(
                                            'principal-registration.verify-page',
                                        )}
                                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                    >
                                        <UserPlus className="h-4 w-4" />
                                        Principal Registration
                                    </Link>
                                </>
                            )}
                        </nav>
                    </div>
                </header>

                <main>
                    <section className="mx-auto grid max-w-7xl gap-12 px-6 py-20 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-28">
                        <div>
                            <div className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                                <BadgeCheck className="h-4 w-4" />
                                Provincial Department of Education
                            </div>

                            <h1 className="mt-7 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                                A transparent system for principal
                                transfers
                            </h1>

                            <p className="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                                Submit transfer applications, track
                                approval progress and receive official
                                transfer decisions through one secure
                                provincial platform.
                            </p>

                            <div className="mt-9 flex flex-wrap gap-4">
                                {auth?.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                    >
                                        Open Dashboard
                                        <ArrowRight className="h-4 w-4" />
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                        >
                                            Sign In
                                            <ArrowRight className="h-4 w-4" />
                                        </Link>

                                        <Link
                                            href={route(
                                                'principal-registration.verify-page',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            <UserPlus className="h-4 w-4" />
                                            Register Using NIC
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>

                        <div className="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60">
                            <div className="rounded-2xl bg-slate-900 p-7 text-white">
                                <ShieldCheck className="h-9 w-9 text-blue-400" />

                                <h2 className="mt-6 text-2xl font-bold">
                                    Controlled and secure access
                                </h2>

                                <p className="mt-3 leading-7 text-slate-300">
                                    Principal registration is permitted
                                    only when the NIC matches the official
                                    provincial principal registry.
                                </p>
                            </div>

                            <div className="mt-6 grid gap-4 sm:grid-cols-2">
                                <div className="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <Building2 className="h-6 w-6 text-blue-600" />

                                    <h3 className="mt-4 font-bold text-slate-900">
                                        Official workflow
                                    </h3>

                                    <p className="mt-2 text-sm leading-6 text-slate-600">
                                        Applications move through zonal,
                                        provincial and transfer board
                                        review.
                                    </p>
                                </div>

                                <div className="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                    <BadgeCheck className="h-6 w-6 text-blue-600" />

                                    <h3 className="mt-4 font-bold text-slate-900">
                                        Trackable decisions
                                    </h3>

                                    <p className="mt-2 text-sm leading-6 text-slate-600">
                                        Principals can monitor the status
                                        and final result of applications.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>

                <footer className="border-t border-slate-200 bg-white">
                    <div className="mx-auto max-w-7xl px-6 py-6 text-center text-sm text-slate-500 lg:px-8">
                        Provincial Department of Education,
                        Sabaragamuwa Province
                    </div>
                </footer>
            </div>
        </>
    );
}
