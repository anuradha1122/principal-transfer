import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import ProfileForm from './ProfileForm';

function dateValue(value) {
    return value ? value.substring(0, 10) : '';
}

export default function Edit({
    profile,
    options,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        nic: profile.nic ?? '',
        employee_number:
            profile.employee_number ?? '',
        full_name: profile.full_name ?? '',
        name_with_initials:
            profile.name_with_initials ?? '',
        gender: profile.gender ?? '',
        date_of_birth: dateValue(
            profile.date_of_birth,
        ),
        mobile_number:
            profile.mobile_number ?? '',
        alternate_number:
            profile.alternate_number ?? '',
        personal_email:
            profile.personal_email ?? '',
        address_line_1:
            profile.address_line_1 ?? '',
        address_line_2:
            profile.address_line_2 ?? '',
        city: profile.city ?? '',
        postal_code:
            profile.postal_code ?? '',
        service_category:
            profile.service_category ??
            'Sri Lanka Principals Service',
        service_grade:
            profile.service_grade ?? '',
        first_appointment_date: dateValue(
            profile.first_appointment_date,
        ),
        principal_service_entry_date:
            dateValue(
                profile.principal_service_entry_date,
            ),
        retirement_date: dateValue(
            profile.retirement_date,
        ),
        employment_status:
            profile.employment_status ??
            'Active',
        qualifications_summary:
            profile.qualifications_summary ?? '',
        notes: profile.notes ?? '',
        profile_completed: Boolean(
            profile.profile_completed,
        ),
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.principal-profiles.update',
                profile.id,
            ),
        );
    };

    return (
        <AdminLayout
            title={`Edit ${profile.full_name}`}
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Principal Profile
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update personal and service information.
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
                    editing
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
