<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Appointment Letter
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

        .box {
            border: 1px solid #cbd5e1;
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
            Appointment Letter
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
    </table>

    <p>
        To:
        <strong>
            {{ $application->principal_name }}
        </strong>
    </p>

    <p>
        You are hereby appointed to serve at the school stated below in
        accordance with the Transfer Board decision.
    </p>

    <div class="box">
        <p>
            <strong>School:</strong>
            {{ $decision?->recommendedSchool?->name }}
        </p>

        <p>
            <strong>Effective Date:</strong>
            {{ $decision?->effective_date?->format('Y-m-d') }}
        </p>

        <p>
            <strong>Appointment Type:</strong>
            {{ $decision?->appointment_type }}
        </p>

        <p>
            <strong>Decision Reference:</strong>
            {{ $decision?->decision_reference }}
        </p>
    </div>

    <p>
        You are instructed to report for duty on the effective date and
        perform all duties attached to the appointment.
    </p>

    <div class="signature">
        <p>
            ....................................................
        </p>

        <p>
            Provincial Secretary of Education
        </p>
    </div>
</body>
</html>
