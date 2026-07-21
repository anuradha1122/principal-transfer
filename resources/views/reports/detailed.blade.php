<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>{{ $title }}</title>

    <style>
        @page {
            margin: 18px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1e293b;
            font-size: 9px;
        }

        h1 {
            margin: 0 0 4px;
            font-size: 18px;
        }

        .meta {
            margin-bottom: 14px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: left;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .empty {
            padding: 20px;
            text-align: center;
            color: #64748b;
        }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>

    <div class="meta">
        Generated:
        {{ $generatedAt->format('Y-m-d H:i:s') }}

        &nbsp; | &nbsp;

        Records:
        {{ $rows->count() }}
    </div>

    @if ($type === 'applications')
        <table>
            <thead>
                <tr>
                    <th>Application</th>
                    <th>Principal</th>
                    <th>NIC</th>
                    <th>Cycle</th>
                    <th>Zone</th>
                    <th>School</th>
                    <th>Designation</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['application_number'] }}</td>
                        <td>{{ $row['principal_name'] }}</td>
                        <td>{{ $row['nic'] }}</td>
                        <td>{{ $row['cycle'] }}</td>
                        <td>{{ $row['zone'] }}</td>
                        <td>{{ $row['current_school'] }}</td>
                        <td>{{ $row['designation'] }}</td>
                        <td>{{ $row['transfer_reason'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td>{{ $row['submitted_at'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($type === 'decisions')
        <table>
            <thead>
                <tr>
                    <th>Application</th>
                    <th>Principal</th>
                    <th>Cycle</th>
                    <th>Zone</th>
                    <th>Decision</th>
                    <th>Reference</th>
                    <th>Recommended School</th>
                    <th>Effective Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['application_number'] }}</td>
                        <td>{{ $row['principal_name'] }}</td>
                        <td>{{ $row['cycle'] }}</td>
                        <td>{{ $row['zone'] }}</td>
                        <td>{{ $row['decision'] }}</td>
                        <td>{{ $row['decision_reference'] }}</td>
                        <td>{{ $row['recommended_school'] }}</td>
                        <td>{{ $row['effective_date'] }}</td>
                        <td>{{ $row['remarks'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($type === 'appeals')
        <table>
            <thead>
                <tr>
                    <th>Appeal</th>
                    <th>Application</th>
                    <th>Principal</th>
                    <th>Cycle</th>
                    <th>Zone</th>
                    <th>Application Status</th>
                    <th>Appeal Status</th>
                    <th>Grounds</th>
                    <th>Submitted</th>
                    <th>Decided</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['appeal_number'] }}</td>
                        <td>{{ $row['application_number'] }}</td>
                        <td>{{ $row['principal_name'] }}</td>
                        <td>{{ $row['cycle'] }}</td>
                        <td>{{ $row['zone'] }}</td>
                        <td>{{ $row['application_status'] }}</td>
                        <td>{{ $row['appeal_status'] }}</td>
                        <td>{{ $row['grounds'] }}</td>
                        <td>{{ $row['submitted_at'] }}</td>
                        <td>{{ $row['decided_at'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($type === 'documents')
        <table>
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Type</th>
                    <th>Application</th>
                    <th>Principal</th>
                    <th>Cycle</th>
                    <th>Zone</th>
                    <th>Issued</th>
                    <th>Effective</th>
                    <th>Signed</th>
                    <th>Published</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row['document_number'] }}</td>
                        <td>{{ $row['document_type'] }}</td>
                        <td>{{ $row['application_number'] }}</td>
                        <td>{{ $row['principal_name'] }}</td>
                        <td>{{ $row['cycle'] }}</td>
                        <td>{{ $row['zone'] }}</td>
                        <td>{{ $row['issued_date'] }}</td>
                        <td>{{ $row['effective_date'] }}</td>
                        <td>
                            {{ $row['has_signed_copy'] ? 'Yes' : 'No' }}
                        </td>
                        <td>
                            {{ $row['is_published'] ? 'Yes' : 'No' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
