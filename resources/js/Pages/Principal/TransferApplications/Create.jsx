import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import ApplicationForm from './ApplicationForm';

export default function Create({
    profile,
    cycle,
    schools,
    reasons,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        transfer_cycle_id: cycle.id,
        transfer_reason: '',
        reason_details: '',
        has_medical_reason: false,
        has_spouse_employment_reason: false,
        is_mutual_transfer: false,
        mutual_principal_nic: '',
        principal_remarks: '',
        preferences: [
            {
                zone_id: '',
                school_id: '',
                preference_reason: '',
            },
        ],
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'principal.transfer-applications.store',
            ),
        );
    };

    return (
        <AdminLayout
            title="New Transfer Application"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        New Transfer Application
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        {cycle.name} · Applications close on{' '}
                        {new Date(
                            cycle.application_close_date,
                        ).toLocaleDateString(
                            'en-LK',
                        )}
                    </p>
                </div>
            }
        >
            <ApplicationForm
                data={data}
                setData={setData}
                errors={errors}
                processing={processing}
                profile={profile}
                cycle={cycle}
                schools={schools}
                reasons={reasons}
                onSubmit={submit}
            />
        </AdminLayout>
    );
}
