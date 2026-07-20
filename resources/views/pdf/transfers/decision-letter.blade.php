<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Transfer Decision Letter
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.7;
            color: #111827;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .meta {
            width: 100%;
            margin-bottom: 25px;
        }

        .meta td {
            padding: 4px 0;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .decision-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 15px;
            margin: 20px 0;
        }

        .signature {
            margin-top: 70px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>
            SABARAGAMUWA PROVINCIAL MINISTRY OF EDUCATION
        </h1>

        <p>
            Principal Transfer Decision Letter
        </p>
    </div>

    <table class="meta">
        <tr>
            <td class="label">
                Document Number
            </td>

            <td>
                {{ $document->document_number }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Issued Date
            </td>

            <td>
                {{ $document->issued_date?->format('Y-m-d') }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Application Number
            </td>

            <td>
                {{ $application->application_number }}
            </td>
        </tr>
    </table>

    <p>
        Dear
        <strong>
            {{ $application->principal_name }}
        </strong>,
    </p>

    <p>
        The Transfer Board has completed its consideration of your transfer
        application.
    </p>

    <div class="decision-box">
        <p>
            <strong>Final Decision:</strong>
            {{ $application->status }}
        </p>

        <p>
            <strong>Decision Reference:</strong>
            {{ $decision?->decision_reference }}
        </p>

        @if ($application->status === 'Rejected')
            <p>
                <strong>Reason:</strong>
                {{ $decision?->rejection_reason }}
            </p>
        @endif

        @if ($application->status === 'Waitlisted')
            <p>
                <strong>Reason:</strong>
                {{ $decision?->waitlist_reason }}
            </p>
        @endif

        @if ($decision?->remarks)
            <p>
                <strong>Remarks:</strong>
                {{ $decision->remarks }}
            </p>
        @endif
    </div>

    <div class="signature">
        <p>
            ....................................................
        </p>

        <p>
            Secretary, Transfer Board
        </p>
    </div>
</body>
</html>
