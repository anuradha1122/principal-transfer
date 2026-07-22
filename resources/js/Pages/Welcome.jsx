import { Head, Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import {
    ArrowRight,
    BadgeCheck,
    BellRing,
    Building2,
    CheckCircle2,
    ClipboardCheck,
    FileCheck2,
    Landmark,
    LockKeyhole,
    MapPinned,
    School,
    ShieldCheck,
    Sparkles,
    UserCheck,
    UserPlus,
    GraduationCap,
} from 'lucide-react';

export default function Welcome({ auth }) {
    const dashboardUrl = route('dashboard');

    return (
        <>
            <Head title="Principal Transfer System" />

            <div className="min-h-screen bg-slate-950">
                <div className="relative overflow-hidden bg-slate-950">
                    <div className="pointer-events-none absolute inset-0">
                        <div className="absolute -left-24 top-20 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl" />

                        <div className="absolute right-0 top-0 h-[32rem] w-[32rem] rounded-full bg-indigo-500/15 blur-3xl" />

                        <div className="absolute bottom-0 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-cyan-500/10 blur-3xl" />

                        <div
                            className="absolute inset-0 opacity-[0.035]"
                            style={{
                                backgroundImage:
                                    'linear-gradient(rgba(255,255,255,0.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.8) 1px, transparent 1px)',
                                backgroundSize: '42px 42px',
                            }}
                        />
                    </div>

                    <header className="relative z-20 border-b border-white/10">
                        <div className="mx-auto flex max-w-7xl items-center justify-between gap-5 px-5 py-5 sm:px-6 lg:px-8">
                            <Link
                                href="/"
                                className="flex min-w-0 items-center gap-3"
                            >
                                <ApplicationLogo
                                    className="h-14 w-14 rounded-2xl bg-white p-1.5 shadow-lg shadow-black/20"
                                    showText
                                    textClassName="min-w-0 text-white"
                                />
                            </Link>

                            <nav className="flex shrink-0 items-center gap-2 sm:gap-3">
                                {auth?.user ? (
                                    <Link
                                        href={dashboardUrl}
                                        className="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-900 shadow-lg shadow-black/20 transition hover:bg-blue-50"
                                    >
                                        <span className="hidden sm:inline">
                                            Open Dashboard
                                        </span>

                                        <span className="sm:hidden">
                                            Dashboard
                                        </span>

                                        <ArrowRight className="h-4 w-4" />
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-3.5 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10 sm:px-4"
                                        >
                                            <LockKeyhole className="h-4 w-4" />

                                            <span className="hidden sm:inline">
                                                Sign In
                                            </span>
                                        </Link>

                                        <Link
                                            href={route(
                                                'principal-registration.verify-page',
                                            )}
                                            className="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-3.5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-500 sm:px-4"
                                        >
                                            <UserPlus className="h-4 w-4" />

                                            <span className="hidden md:inline">
                                                Principal Registration
                                            </span>

                                            <span className="md:hidden">
                                                Register
                                            </span>
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </div>
                    </header>

                    <main className="relative z-10">
                        <section className="mx-auto grid max-w-7xl gap-14 px-5 pb-20 pt-14 sm:px-6 sm:pt-20 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:px-8 lg:pb-28 lg:pt-24">
                            <div>
                                <div className="inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-500/10 px-4 py-2 text-sm font-semibold text-blue-200 backdrop-blur">
                                    <Sparkles className="h-4 w-4" />

                                    Provincial Department of Education
                                </div>

                                <h1 className="mt-7 max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl lg:leading-[1.08]">
                                    Transparent principal transfers through
                                    one secure platform
                                </h1>

                                <p className="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
                                    Submit transfer applications, follow each
                                    approval stage and receive official final
                                    decisions through the centralized
                                    Sabaragamuwa Provincial Principal Transfer
                                    System.
                                </p>

                                <div className="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                                    {auth?.user ? (
                                        <Link
                                            href={dashboardUrl}
                                            className="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-blue-950/40 transition hover:-translate-y-0.5 hover:bg-blue-500"
                                        >
                                            Open Your Dashboard

                                            <ArrowRight className="h-4 w-4" />
                                        </Link>
                                    ) : (
                                        <>
                                            <Link
                                                href={route('login')}
                                                className="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-blue-950/40 transition hover:-translate-y-0.5 hover:bg-blue-500"
                                            >
                                                Sign In to the System

                                                <ArrowRight className="h-4 w-4" />
                                            </Link>

                                            <Link
                                                href={route(
                                                    'principal-registration.verify-page',
                                                )}
                                                className="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-6 py-3.5 text-sm font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/10"
                                            >
                                                <UserPlus className="h-4 w-4" />

                                                Register Using NIC
                                            </Link>
                                        </>
                                    )}
                                </div>

                                <div className="mt-10 grid gap-4 sm:grid-cols-3">
                                    <TrustItem
                                        icon={ShieldCheck}
                                        title="Secure access"
                                        description="Role and permission controlled"
                                    />

                                    <TrustItem
                                        icon={ClipboardCheck}
                                        title="Trackable workflow"
                                        description="Every approval stage recorded"
                                    />

                                    <TrustItem
                                        icon={FileCheck2}
                                        title="Official decisions"
                                        description="Final results available online"
                                    />
                                </div>
                            </div>

                            <div className="relative">
                                <div className="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-blue-600/25 via-indigo-500/10 to-cyan-500/10 blur-2xl" />

                                <div className="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] p-4 shadow-2xl shadow-black/40 backdrop-blur-xl sm:p-6">
                                    <div className="rounded-[1.5rem] border border-white/10 bg-slate-900/80 p-6 sm:p-7">
                                        <div className="flex items-start justify-between gap-4">
                                            <div>
                                                <p className="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">
                                                    Digital Transfer Workflow
                                                </p>

                                                <h2 className="mt-3 text-2xl font-bold text-white">
                                                    From application to final
                                                    decision
                                                </h2>
                                            </div>

                                            <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-500/15 text-blue-300">
                                                <ShieldCheck className="h-6 w-6" />
                                            </div>
                                        </div>

                                        <div className="mt-8 space-y-3">
                                            <WorkflowStep
                                                number="01"
                                                icon={UserCheck}
                                                title="Principal submits"
                                                description="The Principal completes and submits the transfer application."
                                                active
                                            />

                                            <WorkflowStep
                                                number="02"
                                                icon={MapPinned}
                                                title="Zonal review"
                                                description="The assigned Zone reviews the application and recommendation."
                                            />

                                            <WorkflowStep
                                                number="03"
                                                icon={Landmark}
                                                title="Provincial review"
                                                description="The Provincial Director reviews the Zonal recommendation."
                                            />

                                            <WorkflowStep
                                                number="04"
                                                icon={BadgeCheck}
                                                title="Transfer Board decision"
                                                description="The Transfer Board records and publishes the final result."
                                            />
                                        </div>
                                    </div>

                                    <div className="mt-4 grid gap-4 sm:grid-cols-2">
                                        <div className="rounded-2xl border border-white/10 bg-white/[0.07] p-5">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-300">
                                                    <CheckCircle2 className="h-5 w-5" />
                                                </div>

                                                <div>
                                                    <p className="font-bold text-white">
                                                        NIC verified
                                                    </p>

                                                    <p className="text-xs text-slate-400">
                                                        Registry-controlled
                                                        registration
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="rounded-2xl border border-white/10 bg-white/[0.07] p-5">
                                            <div className="flex items-center gap-3">
                                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/15 text-violet-300">
                                                    <BellRing className="h-5 w-5" />
                                                </div>

                                                <div>
                                                    <p className="font-bold text-white">
                                                        Status alerts
                                                    </p>

                                                    <p className="text-xs text-slate-400">
                                                        Workflow notifications
                                                        and updates
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </main>
                </div>

                <section className="bg-white py-20 sm:py-24">
                    <div className="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                        <div className="mx-auto max-w-3xl text-center">
                            <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                <Building2 className="h-6 w-6" />
                            </div>

                            <h2 className="mt-5 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                                One platform for the complete transfer process
                            </h2>

                            <p className="mt-4 text-base leading-7 text-slate-600">
                                Every stakeholder receives a dedicated
                                dashboard based on their responsibility and
                                organizational scope.
                            </p>
                        </div>

                        <div className="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                            <FeatureCard
                                icon={GraduationCap}
                                title="Principals"
                                description="Create applications, upload supporting documents and follow the full approval progress."
                            />

                            <FeatureCard
                                icon={MapPinned}
                                title="Zonal Directors"
                                description="Review applications from Principals assigned to schools within the relevant Zone."
                            />

                            <FeatureCard
                                icon={Landmark}
                                title="Provincial Director"
                                description="Review Zonal recommendations and manage Province-wide transfer decisions."
                            />

                            <FeatureCard
                                icon={ShieldCheck}
                                title="Transfer Board"
                                description="Record final outcomes, manage appeals and publish official transfer documents."
                            />
                        </div>
                    </div>
                </section>

                <section className="border-y border-slate-200 bg-slate-50 py-20">
                    <div className="mx-auto grid max-w-7xl gap-10 px-5 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
                        <div>
                            <div className="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">
                                <BadgeCheck className="h-4 w-4" />

                                Official principal registry
                            </div>

                            <h2 className="mt-5 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                                Registration is protected by NIC verification
                            </h2>

                            <p className="mt-5 max-w-xl text-base leading-8 text-slate-600">
                                A Principal can create an account only when the
                                provided NIC matches an authorized record in the
                                Provincial Principal Registry. This prevents
                                unverified public account creation.
                            </p>

                            {!auth?.user && (
                                <Link
                                    href={route(
                                        'principal-registration.verify-page',
                                    )}
                                    className="mt-7 inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                                >
                                    <UserPlus className="h-4 w-4" />

                                    Start Principal Registration

                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            )}
                        </div>

                        <div className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/70 sm:p-8">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <SecurityCard
                                    icon={LockKeyhole}
                                    title="Protected accounts"
                                    description="Authenticated and role-controlled system access."
                                />

                                <SecurityCard
                                    icon={School}
                                    title="Verified appointment"
                                    description="Principal records connect with official schools and appointments."
                                />

                                <SecurityCard
                                    icon={ClipboardCheck}
                                    title="Auditable actions"
                                    description="Application reviews and decisions remain traceable."
                                />

                                <SecurityCard
                                    icon={FileCheck2}
                                    title="Official documents"
                                    description="Final transfer results are available through the system."
                                />
                            </div>
                        </div>
                    </div>
                </section>

                <section className="bg-blue-700">
                    <div className="mx-auto flex max-w-7xl flex-col items-start justify-between gap-7 px-5 py-14 sm:px-6 lg:flex-row lg:items-center lg:px-8">
                        <div>
                            <p className="text-sm font-bold uppercase tracking-[0.18em] text-blue-200">
                                Sabaragamuwa Province
                            </p>

                            <h2 className="mt-2 text-2xl font-black text-white sm:text-3xl">
                                Access the Provincial Principal Transfer System
                            </h2>

                            <p className="mt-3 max-w-2xl text-sm leading-7 text-blue-100 sm:text-base">
                                Sign in to continue your assigned transfer
                                workflow or verify your NIC to register as a
                                Principal.
                            </p>
                        </div>

                        <div className="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                            {auth?.user ? (
                                <Link
                                    href={dashboardUrl}
                                    className="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50"
                                >
                                    Open Dashboard

                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('login')}
                                        className="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-bold text-blue-800 transition hover:bg-blue-50"
                                    >
                                        <LockKeyhole className="h-4 w-4" />

                                        Sign In
                                    </Link>

                                    <Link
                                        href={route(
                                            'principal-registration.verify-page',
                                        )}
                                        className="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-300/40 bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-500"
                                    >
                                        <UserPlus className="h-4 w-4" />

                                        Verify NIC
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </section>

                <footer className="bg-slate-950">
                    <div className="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-8 text-center sm:px-6 md:flex-row md:items-center md:justify-between md:text-left lg:px-8">
                        <div className="flex items-center justify-center gap-3 md:justify-start">
                            <ApplicationLogo
                                className="h-12 w-12 rounded-xl bg-white p-1"
                                showText
                                textClassName="text-white"
                            />
                        </div>

                        <p className="text-xs leading-6 text-slate-500 sm:text-sm">
                            Provincial Department of Education,
                            Sabaragamuwa Province
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}

function TrustItem({
    icon: Icon,
    title,
    description,
}) {
    return (
        <div className="rounded-2xl border border-white/10 bg-white/[0.05] p-4 backdrop-blur">
            <Icon className="h-5 w-5 text-blue-300" />

            <p className="mt-3 text-sm font-bold text-white">
                {title}
            </p>

            <p className="mt-1 text-xs leading-5 text-slate-400">
                {description}
            </p>
        </div>
    );
}

function WorkflowStep({
    number,
    icon: Icon,
    title,
    description,
    active = false,
}) {
    return (
        <div
            className={[
                'flex gap-4 rounded-2xl border p-4 transition',
                active
                    ? 'border-blue-400/30 bg-blue-500/10'
                    : 'border-white/10 bg-white/[0.03]',
            ].join(' ')}
        >
            <div
                className={[
                    'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                    active
                        ? 'bg-blue-500 text-white'
                        : 'bg-white/10 text-slate-300',
                ].join(' ')}
            >
                <Icon className="h-5 w-5" />
            </div>

            <div className="min-w-0 flex-1">
                <div className="flex items-center justify-between gap-3">
                    <h3 className="font-bold text-white">
                        {title}
                    </h3>

                    <span className="text-[11px] font-black tracking-widest text-slate-500">
                        {number}
                    </span>
                </div>

                <p className="mt-1 text-xs leading-5 text-slate-400">
                    {description}
                </p>
            </div>
        </div>
    );
}

function FeatureCard({
    icon: Icon,
    title,
    description,
}) {
    return (
        <div className="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl hover:shadow-slate-200/70">
            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 transition group-hover:bg-blue-600 group-hover:text-white">
                <Icon className="h-6 w-6" />
            </div>

            <h3 className="mt-5 text-lg font-bold text-slate-900">
                {title}
            </h3>

            <p className="mt-3 text-sm leading-7 text-slate-600">
                {description}
            </p>
        </div>
    );
}

function SecurityCard({
    icon: Icon,
    title,
    description,
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-blue-700 shadow-sm">
                <Icon className="h-5 w-5" />
            </div>

            <h3 className="mt-4 font-bold text-slate-900">
                {title}
            </h3>

            <p className="mt-2 text-sm leading-6 text-slate-600">
                {description}
            </p>
        </div>
    );
}
