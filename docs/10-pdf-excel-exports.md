# PDF and Excel Exports

## Purpose

This document defines the PDF and Excel export standards for the
Principal Transfer System.

Exports provide official documents, operational reports, administrative
summaries, and historical records.

All exports must follow backend authorization rules.

Frontend button visibility is not sufficient security.

---

# Technology

## PDF Generation

PDF documents use:

```text
barryvdh/laravel-dompdf

Laravel facade:

use Barryvdh\DomPDF\Facade\Pdf;
Excel Generation

Excel exports use:

maatwebsite/excel

Laravel facade:

use Maatwebsite\Excel\Facades\Excel;
General Export Rules
All export routes require authentication.
Export routes require appropriate role or permission checks.
Principal ownership must be verified.
Zonal access must be restricted to the assigned zone.
Provincial access must follow the provincial workflow scope.
Transfer Board access must follow the Board workflow scope.
Sensitive documents must use private storage.
Private PDFs must not be exposed through public URLs.
Download filenames must be clear and predictable.
Exports must preserve historical data.
Submitted application snapshots must remain immutable.
Export failures must be logged.
Export failures must not reverse successful workflow transactions.
PDF views must be printable on A4 paper.
Excel files must include clear column headings.
Dates must use a consistent format.
Empty values must display as blank, a dash, or Not recorded.
Export data must match the currently authorized record scope.
Exports must not expose passwords, tokens, internal permission data,
or unnecessary private information.
Current PDF Exports
Submitted Transfer Application PDF
Status

Implemented in Module 06.

Purpose

Creates the official PDF copy of a Principal's submitted transfer
application.

Generation Point

The PDF is generated after successful application submission.

The workflow is:

Validate application ownership.
Validate Draft status.
Validate open Transfer Cycle.
Validate school preferences.
Generate application number.
Change status to Submitted.
Record submission timestamp.
Commit the database transaction.
Generate the PDF.
Store the PDF privately.
Record the PDF path.
Record the PDF generation timestamp.
Service
app/Services/TransferApplicationPdfService.php
PDF View
resources/views/pdf/transfer-applications/submitted.blade.php
Storage Fields
submitted_pdf_path
submitted_pdf_generated_at
Private Storage Location

Example:

storage/app/private/transfer-applications/{transfer_cycle_id}/
Filename Format

Recommended pattern:

{application-number}-submitted.pdf

Example:

pts-2026-000001-submitted.pdf
PDF Content

The submitted application PDF should include:

Organization name
Document title
Application number
Application status
Transfer Cycle
Transfer year
Transfer type
Submission date
Principal name
NIC
Employee number
Service grade
Current designation
Current school
Current division
Current zone
Current appointment start date
Service duration at the current school
Transfer reason
Detailed explanation
Medical reason indicator
Spouse employment indicator
Mutual transfer indicator
Mutual Principal NIC where applicable
Principal remarks
Ranked school preferences
Declaration confirmation
Signature placeholders
Generated date and time
Security
The PDF must be stored on the local private disk.
It must not be stored under storage/app/public.
It must not be accessible through a direct public storage URL.
It must be delivered through an authorized controller.
Principals may download only their own submitted PDFs.
Administrators require the correct permission.
Future Zonal Directors must be restricted to their assigned zone.
Availability

The PDF is available after submission.

It remains available when the application becomes:

Submitted
Zonal Review
Zonal Approved
Zonal Rejected
Provincial Review
Provincial Approved
Provincial Rejected
Board Review
Approved
Rejected
Waitlisted
Withdrawn
Cancelled, where a submitted copy exists

Draft applications do not have an official submitted PDF.

Regeneration

If the PDF path is missing or the stored file no longer exists:

An authorized download request may regenerate the PDF.
Regeneration must use the submitted application snapshot.
Regeneration must not alter application content.
The new generation timestamp may be stored.
Failures must be logged.
Principal PDF Routes
Download Submitted Application PDF
GET /principal/transfer-applications/{transferApplication}/pdf

Route name:

principal.transfer-applications.pdf

Controller:

App\Http\Controllers\Principal\TransferApplicationController

Method:

downloadPdf

Authorization requirements:

Authenticated
Verified
Principal role
Application ownership
Application has been submitted
Principal Download Locations

The PDF button is available from:

Principal Transfer Application Index page
Principal Transfer Application Show page

A normal HTML anchor should be used for binary downloads:

<a
    href={route(
        'principal.transfer-applications.pdf',
        application.id,
    )}
>
    Download PDF
</a>

Do not use an Inertia <Link> for the PDF file response.

Admin PDF Routes
Download Submitted Application PDF
GET /admin/transfer-applications/{transferApplication}/pdf

Route name:

admin.transfer-applications.pdf

Controller:

App\Http\Controllers\Admin\TransferApplicationController

Method:

downloadPdf

Required permission:

view transfer applications
Admin Download Location

The PDF button is available from:

Admin Transfer Application Show page
Admin Security
The user must be authenticated.
The user must have the required permission.
Super Admin may access all records.
Future role restrictions must still apply by workflow scope.
Draft applications must not expose an official submitted PDF.
PDF Failure Handling

If PDF generation fails after successful submission:

The application remains Submitted.
The submission transaction is not reversed.
The exception is logged.
The Principal receives a warning message.
The PDF may be generated later through an authorized download.
Administrators may also trigger regeneration through download.
The application number and submitted timestamp remain valid.

Example handling:

try {
    $pdfService->generate(
        $transferApplication
    );
} catch (\Throwable $exception) {
    report($exception);
}
PDF Formatting Standards
Page
Paper size: A4
Orientation: Portrait unless the report requires landscape
Margins: Consistent on all sides
Default font: DejaVu Sans
Footer: System name and generation timestamp
Page breaks: Used for long sections
Tables: Must not overflow the page width
Typography
Clear document heading
Clear section headings
Readable body size
Consistent label styling
No decorative fonts
Sinhala or Tamil output requires a compatible Unicode font and
verified rendering support
Tables

Tables should:

Use visible borders
Use repeated headings where practical
Avoid excessive column widths
Wrap long text
Keep preference order visible
Display blank values consistently
Dates

Recommended PDF date format:

YYYY-MM-DD

Recommended date-time format:

YYYY-MM-DD HH:mm
Planned PDF Exports
Principal Profile PDF

Planned content:

Personal details
Contact information
Service details
Current appointment
Appointment history
Qualifications summary

Suggested route:

GET /admin/principal-profiles/{principalProfile}/pdf

Possible Principal route:

GET /principal/profile/pdf
Appointment History PDF

Planned content:

Principal identity
All appointment records
Current appointment indication
School, division, and zone
Appointment dates
End reasons
Transfer Cycle Summary PDF

Planned content:

Cycle details
Application counts
Status totals
Zone totals
Submission period
Eligibility rules
Zonal Review PDF

Planned for Module 07.

Possible content:

Application summary
Submitted application snapshot
Zonal reviewer
Zonal recommendation
Zonal decision
Remarks
Review timestamp
Provincial Review PDF

Planned content:

Application number
Zonal review result
Provincial review result
Provincial remarks
Reviewer
Timestamp
Final Decision PDF

Planned content:

Application number
Principal details
Current school
Final status
Approved school where applicable
Effective date
Transfer Board remarks
Decision date
Official signature placeholders
Excel Export Standards
General Rules

Excel exports must:

Use Laravel Excel
Use clear headings
Use one record per row
Format dates consistently
Use readable column widths
Exclude unauthorized records
Respect active filters where appropriate
Include an export timestamp where useful
Avoid exposing sensitive information unnecessarily

Suggested interfaces:

FromCollection
WithHeadings
WithMapping
ShouldAutoSize
WithStyles
Planned Excel Exports
Principal Registry Export

Suggested columns:

NIC
Normalized NIC
Full name
Name with initials
Employee number
School
Division
Zone
Registration status
Active status
Registered date
Principal Profile Export

Suggested columns:

Full name
NIC
Employee number
Gender
Service category
Service grade
Employment status
Current designation
Current school
Division
Zone
Profile completion status
Principal Appointment Export

Suggested columns:

Principal name
NIC
Employee number
School
Division
Zone
Designation
Appointment type
Appointment number
Appointment date
Start date
End date
Current status
Reason for end
Transfer Cycle Export

Suggested columns:

Cycle code
Cycle name
Transfer year
Transfer type
Opening date
Closing date
Minimum service years
Maximum preferences
Status
Published status
Total applications
Transfer Application Export

Suggested columns:

Application number
Principal name
NIC
Employee number
Service grade
Current designation
Current school
Division
Zone
Transfer reason
Status
Submitted date
Withdrawn date
Number of preferences
Zonal Review Export

Planned columns:

Application number
Principal
Current school
Zone
Submitted date
Zonal decision
Zonal recommendation
Reviewer
Reviewed date
Provincial Review Export

Planned columns:

Application number
Principal
Zone
Zonal decision
Provincial decision
Reviewer
Reviewed date
Transfer Board Decision Export

Planned columns:

Application number
Principal
Current school
Preferred schools
Final status
Approved school
Effective date
Decision date
Export Filtering Rules

Where filters are used in the index page, exports should normally
respect the same filters.

Possible filters:

Search text
Transfer Cycle
Transfer year
Status
Zone
Division
School
Transfer reason
Submitted date range
Employment status
Service grade

Export query parameters must be validated.

Unauthorized users must not expand their scope by modifying query
parameters manually.

Export Naming Rules

PDF examples:

PTS-2026-000001-submitted.pdf
principal-profile-123.pdf
transfer-cycle-2026-summary.pdf

Excel examples:

principal-registry-2026-07-20.xlsx
principal-profiles-2026-07-20.xlsx
transfer-applications-2026-07-20.xlsx
zonal-review-ratnapura-2026-07-20.xlsx

Filenames should:

Use lowercase where practical
Avoid spaces
Avoid unsafe characters
Include record reference or date
Use the correct extension
Export Authorization Matrix
Export	Super Admin	Principal	Zonal Director	Provincial Director	Board Member	Data Entry
Own Submitted Application PDF	Yes	Own only	No	No	No	No
All Submitted Application PDFs	Yes	No	Assigned zone	Authorized	Authorized	Permission based
Principal Profile PDF	Yes	Own only	Assigned zone	Authorized	Relevant only	Permission based
Principal Registry Excel	Yes	No	No	No	No	Permission based
Principal Profile Excel	Yes	No	Assigned zone	Authorized	No	Permission based
Transfer Application Excel	Yes	Own history only if implemented	Assigned zone	Authorized	Authorized	No
Zonal Review Excel	Yes	No	Assigned zone	View where allowed	No	No
Final Decision PDF	Yes	Own only	Relevant	Authorized	Authorized	No
Testing Requirements

PDF tests must verify:

Principal can download own submitted PDF.
Principal cannot download another Principal's PDF.
Draft application PDF returns a redirect or error.
Admin with permission can download a submitted PDF.
Unauthorized admin receives HTTP 403.
PDF response has application/pdf content type.
Download filename is correct.
Missing PDF file is regenerated.
Withdrawal does not remove PDF access.
Reapplication produces a separate PDF.
PDF generation failure does not reverse submission.

Excel tests must verify:

Authorized export succeeds.
Unauthorized export returns HTTP 403.
Export contains expected headings.
Filters are applied.
Zone restrictions are applied.
Sensitive fields are excluded.
File extension and MIME type are correct.
Verification Commands

Check DomPDF:

composer show barryvdh/laravel-dompdf

Check Laravel Excel:

composer show maatwebsite/excel

Check PDF routes:

php artisan route:list --name=transfer-applications.pdf

Run tests:

php artisan test

Clear caches:

php artisan optimize:clear

Build frontend:

npm run build
Current Implementation Status
Implemented
Submitted Transfer Application PDF
Private PDF storage
PDF path tracking
PDF generation timestamp
Principal PDF download from Index
Principal PDF download from Show
Admin PDF download from Show
PDF regeneration where the file is missing
PDF availability after withdrawal
Planned
Principal Profile PDF
Appointment History PDF
Transfer Cycle PDF
Zonal Review PDF
Provincial Review PDF
Final Transfer Board Decision PDF
Principal Registry Excel
Principal Profile Excel
Appointment Excel
Transfer Cycle Excel
Transfer Application Excel
Zonal Review Excel
Provincial Review Excel
Final Decision Excel


Zonal Directors may download submitted application PDFs only when the
application belongs to their assigned Zone.

Super Admin may download PDFs across all Zones.

PDF files remain privately stored.
