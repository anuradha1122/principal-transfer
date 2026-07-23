import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import ApplicationForm from './ApplicationForm';

export default function Edit({
    application,
    profile,
    cycle,
    schools,
    reasons,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        transfer_cycle_id:
            application.transfer_cycle_id,
        transfer_reason:
            application.transfer_reason ?? '',
        reason_details:
            application.reason_details ?? '',
        has_medical_reason: Boolean(
            application.has_medical_reason,
        ),
        has_spouse_employment_reason:
            Boolean(
                application.has_spouse_employment_reason,
            ),
        is_mutual_transfer: Boolean(
            application.is_mutual_transfer,
        ),
        mutual_principal_nic:
            application.mutual_principal_nic ?? '',
        principal_remarks:
            application.principal_remarks ?? '',
        preferences:
            application.preferences?.map(
                (preference) => {
                    const selectedSchool =
                        schools.find(
                            (school) =>
                                String(school.id) ===
                                String(
                                    preference.school_id,
                                ),
                        );

                    return {
                        zone_id:
                            selectedSchool?.division
                                ?.zone?.id
                            ?? '',
                        school_id:
                            preference.school_id,
                        preference_reason:
                            preference.preference_reason
                            ?? '',
                    };
                },
            ) ?? [
                {
                    zone_id: '',
                    school_id: '',
                    preference_reason: '',
                },
            ],
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'principal.transfer-applications.update',
                application.id,
            ),
        );
    };

    return (
        <AdminLayout
            title="Edit Transfer Application"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Transfer Application
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update the draft before final submission.
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
                editing
                onSubmit={submit}
            />
        </AdminLayout>
    );
}
