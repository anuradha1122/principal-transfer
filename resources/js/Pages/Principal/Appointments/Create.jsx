import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import AppointmentForm from './AppointmentForm';

export default function Create({
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
        is_current: true,
        remarks: '',
    });
    const submit = (event) => {
        event.preventDefault();

        post(
            route(
                'principal.appointments.store'
            )
        );
    };

    return (
        <AdminLayout
            title="Add Appointment"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Add Appointment
                    </h1>

                    <p className="mt-1 text-sm text-slate-500">
                        Add a school appointment to your service history.
                    </p>
                </div>
            }
        >
            <AppointmentForm
                data={data}
                setData={setData}
                errors={errors}
                processing={processing}
                schools={schools}
                options={options}
                onSubmit={submit}
            />
        </AdminLayout>
    );
}
