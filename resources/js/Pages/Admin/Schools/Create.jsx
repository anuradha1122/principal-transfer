import AdminLayout from '@/Layouts/AdminLayout';
import { useForm } from '@inertiajs/react';
import SchoolForm from './SchoolForm';

export default function Create(props) {
    const query = new URLSearchParams(
        window.location.search,
    );

    const selectedDivision =
        props.divisions.find(
            (division) =>
                String(division.id) ===
                String(query.get('division_id')),
        ) ?? null;

    const {
        data,
        setData,
        post,
        processing,
        errors,
    } = useForm({
        zone_id: selectedDivision?.zone_id ?? '',
        division_id: selectedDivision?.id ?? '',
        census_number: '',
        name: '',
        school_type: '',
        gender_type: 'Mixed',
        school_level: '',
        mediums: ['Sinhala'],
        address_line_1: '',
        address_line_2: '',
        city: '',
        postal_code: '',
        telephone: '',
        email: '',
        student_count: '',
        teacher_count: '',
        is_national_school: false,
        is_active: true,
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('admin.schools.store'));
    };

    return (
        <AdminLayout
            title="Create School"
            header={
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">
                        Create School
                    </h1>
                    <p className="mt-1 text-sm text-slate-500">
                        Add a school to the provincial education
                        structure.
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
                    onSubmit={submit}
                />
            </div>
        </AdminLayout>
    );
}
