import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import ProfileForm from './ProfileForm';

export default function Create({
    availableAccounts,
    registries,
    options,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        user_id: '',
        principal_registry_id: '',
        nic: '',
        employee_number: '',
        full_name: '',
        name_with_initials: '',
        gender: '',
        date_of_birth: '',
        mobile_number: '',
        alternate_number: '',
        personal_email: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        postal_code: '',
        service_category:
            'Sri Lanka Principals Service',
        service_grade: '',
        first_appointment_date: '',
        principal_service_entry_date: '',
        retirement_date: '',
        employment_status: 'Active',
        qualifications_summary: '',
        notes: '',
        profile_completed: false,
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'admin.principal-profiles.store',
            ),
        );
    };

    return (
        <AdminLayout
            title="Create Principal Profile"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create Principal Profile
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Create the official personal and service record.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <ProfileForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    options={options}
                    availableAccounts={
                        availableAccounts
                    }
                    registries={registries}
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
