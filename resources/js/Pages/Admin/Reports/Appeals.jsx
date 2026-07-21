import DetailedReportPage from '@/Components/Reports/DetailedReportPage';

export default function Appeals(props) {
    return (
        <DetailedReportPage
            {...props}
            title="Transfer Appeal Report"
            description="Appeal submissions, grounds, review statuses, and final appeal outcomes."
            indexRoute={route(
                'admin.reports.appeals',
            )}
            pdfRoute="admin.reports.appeals.pdf"
            excelRoute="admin.reports.appeals.excel"
            columns={[
                {
                    key: 'appeal_number',
                    label: 'Appeal',
                },
                {
                    key: 'application_number',
                    label: 'Application',
                },
                {
                    key: 'principal_name',
                    label: 'Principal',
                },
                {
                    key: 'zone',
                    label: 'Zone',
                },
                {
                    key: 'application_status',
                    label: 'Application Status',
                },
                {
                    key: 'appeal_status',
                    label: 'Appeal Status',
                },
                {
                    key: 'grounds',
                    label: 'Grounds',
                },
                {
                    key: 'submitted_at',
                    label: 'Submitted',
                },
            ]}
        />
    );
}
