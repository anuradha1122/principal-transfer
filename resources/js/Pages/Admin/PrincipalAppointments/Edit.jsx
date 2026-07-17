import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import AppointmentForm from './AppointmentForm';

function dateValue(value) {
    return value ? value.substring(0, 10) : '';
}

export default function Edit({
    appointment,
    profile,
    schools,
    options,
}) {
    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        school_id: appointment.school_id ?? '',
        designation:
            appointment.designation ?? 'Principal',
        appointment_type:
            appointment.appointment_type ?? 'Permanent',
        appointment_number:
            appointment.appointment_number ?? '',
        appointment_date: dateValue(
            appointment.appointment_date,
        ),
        start_date: dateValue(
            appointment.start_date,
        ),
        end_date: dateValue(
            appointment.end_date,
        ),
        is_current: Boolean(
            appointment.is_current,
        ),
        reason_for_end:
            appointment.reason_for_end ?? '',
        remarks: appointment.remarks ?? '',
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.principal-appointments.update',
                appointment.id,
            ),
        );
    };

    return (
        <AdminLayout
            title="Edit Principal Appointment"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit Appointment
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Update the principal’s school appointment
                        record.
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
                    editing
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
