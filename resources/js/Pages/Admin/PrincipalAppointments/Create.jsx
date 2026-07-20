import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import AppointmentForm from './AppointmentForm';

export default function Create({
    profile,
    schools,
    options,
}) {
    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        school_id: '',
        designation: 'Principal',
        appointment_type: 'Permanent',
        appointment_number: '',
        appointment_date: '',
        start_date: '',
        end_date: '',
        reason_for_end: '',
        is_current: true,
        remarks: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'admin.principal-profiles.appointments.store',
                profile.id,
            ),
        );
    };

    return (
        <AdminLayout
            title="Add Principal Appointment"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Add Appointment
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Record a school appointment in the principal’s service history.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <AppointmentForm
                    profile={profile}
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    schools={schools}
                    options={options}
                    cancelRoute="principal.profile.show"
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
