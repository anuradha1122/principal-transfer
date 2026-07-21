import DetailedReportPage from '@/Components/Reports/DetailedReportPage';

export default function Documents(props) {
    return (
        <DetailedReportPage
            {...props}
            title="Transfer Document Report"
            description="Generated transfer orders, appointment letters, decision letters, signed-copy status, and publication activity."
            indexRoute={route(
                'admin.reports.documents',
            )}
            pdfRoute="admin.reports.documents.pdf"
            excelRoute="admin.reports.documents.excel"
            extraFilters
            columns={[
                {
                    key: 'document_number',
                    label: 'Document',
                },
                {
                    key: 'document_type',
                    label: 'Type',
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
                    key: 'issued_date',
                    label: 'Issued',
                },
                {
                    key: 'has_signed_copy',
                    label: 'Signed',
                    render: (row) =>
                        row.has_signed_copy
                            ? 'Yes'
                            : 'No',
                },
                {
                    key: 'is_published',
                    label: 'Published',
                    render: (row) =>
                        row.is_published
                            ? 'Yes'
                            : 'No',
                },
            ]}
        />
    );
}
