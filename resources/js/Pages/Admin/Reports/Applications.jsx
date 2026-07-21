import DetailedReportPage from '@/Components/Reports/DetailedReportPage';

export default function Applications(props) {
    return (
        <DetailedReportPage
            {...props}
            title="Transfer Application Report"
            description="Detailed principal transfer applications by cycle, Zone, workflow status, school, and submission date."
            indexRoute={route(
                'admin.reports.applications',
            )}
            pdfRoute="admin.reports.applications.pdf"
            excelRoute="admin.reports.applications.excel"
            columns={[
                {
                    key: 'application_number',
                    label: 'Application',
                },
                {
                    key: 'principal_name',
                    label: 'Principal',
                },
                {
                    key: 'nic',
                    label: 'NIC',
                },
                {
                    key: 'cycle',
                    label: 'Cycle',
                },
                {
                    key: 'zone',
                    label: 'Zone',
                },
                {
                    key: 'current_school',
                    label: 'School',
                },
                {
                    key: 'status',
                    label: 'Status',
                },
                {
                    key: 'submitted_at',
                    label: 'Submitted',
                },
            ]}
        />
    );
}
