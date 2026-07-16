import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import AdminLayout from '@/Layouts/AdminLayout';
import {
    Download,
    FileSpreadsheet,
    Upload,
} from 'lucide-react';
import { Link, useForm } from '@inertiajs/react';

export default function Import() {
    const {
        data,
        setData,
        post,
        processing,
        errors,
        progress,
    } = useForm({
        file: null,
        update_existing: false,
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'admin.principal-registry.import',
            ),
            {
                forceFormData: true,
            },
        );
    };

    return (
        <AdminLayout
            title="Import Principal Registry"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Import Principal Registry
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Import eligible NIC numbers from a CSV
                        file.
                    </p>
                </div>
            }
        >
            <div className="grid gap-6 xl:grid-cols-3">
                <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                    <form
                        onSubmit={submit}
                        className="space-y-6"
                    >
                        <div className="rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center">
                            <FileSpreadsheet className="mx-auto h-12 w-12 text-slate-400" />

                            <p className="mt-4 font-semibold text-slate-800">
                                Select a CSV file
                            </p>

                            <p className="mt-1 text-sm text-slate-500">
                                Maximum file size: 5 MB
                            </p>

                            <input
                                type="file"
                                accept=".csv,text/csv"
                                onChange={(event) =>
                                    setData(
                                        'file',
                                        event.target
                                            .files?.[0] ??
                                            null,
                                    )
                                }
                                className="mt-5 block w-full text-sm text-slate-600"
                            />

                            <InputError
                                message={errors.file}
                                className="mt-2"
                            />
                        </div>

                        <label className="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <input
                                type="checkbox"
                                checked={
                                    data.update_existing
                                }
                                onChange={(event) =>
                                    setData(
                                        'update_existing',
                                        event.target
                                            .checked,
                                    )
                                }
                                className="mt-1 rounded border-gray-300 text-blue-600"
                            />

                            <span>
                                <span className="block text-sm font-semibold text-slate-800">
                                    Update existing unregistered
                                    records
                                </span>

                                <span className="block text-xs leading-5 text-slate-500">
                                    Registered records are never
                                    overwritten by imports.
                                </span>
                            </span>
                        </label>

                        {progress && (
                            <div>
                                <div className="h-2 overflow-hidden rounded-full bg-slate-200">
                                    <div
                                        className="h-full bg-blue-600"
                                        style={{
                                            width: `${progress.percentage}%`,
                                        }}
                                    />
                                </div>

                                <p className="mt-2 text-xs text-slate-500">
                                    {
                                        progress.percentage
                                    }
                                    % uploaded
                                </p>
                            </div>
                        )}

                        <div className="flex justify-end gap-3">
                            <Link
                                href={route(
                                    'admin.principal-registry.index',
                                )}
                                className="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700"
                            >
                                Cancel
                            </Link>

                            <PrimaryButton
                                disabled={
                                    processing ||
                                    !data.file
                                }
                            >
                                <Upload className="mr-2 h-4 w-4" />
                                {processing
                                    ? 'Importing...'
                                    : 'Import CSV'}
                            </PrimaryButton>
                        </div>
                    </form>
                </section>

                <aside className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 className="font-bold text-slate-900">
                        CSV Columns
                    </h2>

                    <div className="mt-4 space-y-2 text-sm text-slate-600">
                        <p>
                            <strong>nic</strong> required
                        </p>
                        <p>full_name</p>
                        <p>name_with_initials</p>
                        <p>school_census_number</p>
                        <p>designation</p>
                        <p>employee_number</p>
                        <p>notes</p>
                    </div>

                    <a
                        href={route(
                            'admin.principal-registry.template',
                        )}
                        className="mt-6 inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        <Download className="h-4 w-4" />
                        Download Template
                    </a>
                </aside>
            </div>
        </AdminLayout>
    );
}
