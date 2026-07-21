import DetailedReportPage from '@/Components/Reports/DetailedReportPage';

export default function Decisions(props) {
    return (
        <DetailedReportPage
            {...props}
            title="Transfer Decision Report"
            description="Final Transfer Board outcomes, recommendations, decision references, and effective dates."
            indexRoute={route(
                'admin.reports.decisions',
            )}
            pdfRoute="admin.reports.decisions.pdf"
            excelRoute="admin.reports.decisions.excel"
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
                    key: 'zone',
                    label: 'Zone',
                },
                {
                    key: 'decision',
                    label: 'Decision',
                },
                {
                    key: 'decision_reference',
                    label: 'Reference',
                },
                {
                    key: 'recommended_school',
                    label: 'Recommended School',
                },
                {
                    key: 'effective_date',
                    label: 'Effective Date',
                },
            ]}
        />
    );
}
