import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import SchoolForm from './SchoolForm';

export default function Edit(props) {
    const { school } = props;

    const {
        data,
        setData,
        put,
        processing,
        errors,
    } = useForm({
        zone_id: school.division?.zone_id ?? '',
        division_id: school.division_id ?? '',
        census_number: school.census_number ?? '',
        name: school.name ?? '',
        school_type: school.school_type ?? '',
        gender_type: school.gender_type ?? 'Mixed',
        school_level: school.school_level ?? '',
        mediums: school.mediums ?? [],
        address_line_1: school.address_line_1 ?? '',
        address_line_2: school.address_line_2 ?? '',
        city: school.city ?? '',
        postal_code: school.postal_code ?? '',
        telephone: school.telephone ?? '',
        email: school.email ?? '',
        student_count: school.student_count ?? '',
        teacher_count: school.teacher_count ?? '',
        is_national_school: Boolean(
            school.is_national_school,
        ),
        is_active: Boolean(school.is_active),
    });

    const submit = (event) => {
        event.preventDefault();

        put(
            route(
                'admin.schools.update',
                school.id,
            ),
        );
    };

    return (
        <AdminLayout
            title={`Edit ${school.name}`}
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Edit School
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Update school and administrative information.
                    </p>
                </div>
            }
        >
            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <SchoolForm
                    {...props}
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    editing
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
