
---

# `docs/09-email-notifications.md`

The system scope includes basic email notifications as part of the MVP, so this document formalizes both implemented and planned workflow communication. :contentReference[oaicite:2]{index=2}

```md
# Email Notifications

## Purpose

This document defines email notification rules for the Principal
Transfer System.

Email notifications support workflow communication but must not control
or determine the success of core business transactions.

A successful database action must not be rolled back only because an
email could not be sent.

---

# General Email Rules

- Emails must be sent only for meaningful workflow events.
- Email delivery must not replace in-system status tracking.
- Email failures must be logged.
- Email failures must not break registration, submission, withdrawal,
  review, or decision workflows.
- Email recipients must be selected according to role and workflow.
- Sensitive information must be minimized.
- Private PDF storage paths must never be included in emails.
- Public storage URLs must never be used for submitted applications.
- Secure authenticated system links should be used.
- Submitted PDFs should normally be downloaded through the system.
- Attachments should be avoided where secure authorized downloads are
  available.
- Email content must use clear official language.
- Email subjects should include the application number where
  available.
- Emails must not claim a decision that was not successfully stored.
- Notifications should be queued in production where practical.
- Development may use the log mailer.

---

# Email Configuration

The system uses Laravel mail configuration.

Example `.env` settings:

```text
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"


Production values must use the approved mail provider.

After changing email configuration:

php artisan optimize:clear
Registration Notifications
Email Verification
Event

A Principal successfully creates an account through NIC-controlled
registration.

Recipient

The registered Principal.

Purpose

Request verification of the user's email address.

Trigger Point

After successful account creation.

Subject Example
Verify Your Principal Transfer System Email Address
Content

The email may include:

User name
Verification instructions
Signed verification link
Link expiry information
Support contact information

The email must not include:

Password
Full registry data
Internal authorization information
Implementation

Laravel's standard email verification notification may be used.

Registration Completed
Event

The Principal completes NIC-controlled account registration.

Recipient

The registered Principal.

Purpose

Confirm successful account creation and explain the next steps.

Subject Example
Principal Transfer System Registration Completed
Content

The email may include:

Principal name
Confirmation that the account was created
Email verification reminder
Instructions to complete profile information
Instructions to add current appointment details
Secure login link
Status

Optional if email verification already provides sufficient
communication.

Principal Profile Notifications
Profile Completion Reminder
Event

A Principal has registered but has not completed the required profile.

Recipient

The Principal.

Purpose

Remind the Principal to complete the profile before applying.

Subject Example
Complete Your Principal Profile
Content

The email may include:

Principal name
Profile completion requirement
Reminder that NIC cannot be changed
Secure profile link
Status

Planned.

Missing Current Appointment Reminder
Event

A Principal has no current appointment and cannot apply.

Recipient

The Principal.

Purpose

Request completion of current appointment information.

Subject Example
Current Appointment Information Required
Status

Planned.

Transfer Cycle Notifications
Transfer Cycle Published
Event

A Transfer Cycle becomes published and open.

Recipients

Eligible Principals.

Purpose

Inform Principals that applications are open.

Subject Example
Principal Transfer Applications Are Now Open
Content

The email may include:

Cycle name
Cycle code
Transfer year
Application opening date
Application closing date
Minimum service requirement
Maximum school preferences
Secure application link

The email must not guarantee eligibility before backend validation.

Status

Planned.

Transfer Cycle Closing Reminder
Event

A published cycle is approaching its closing date.

Recipients

Eligible Principals who:

have not created an application
have an unfinished Draft
otherwise need a reminder
Purpose

Remind Principals before the closing date.

Subject Example
Transfer Application Closing Date Reminder
Status

Planned.

Transfer Application Notifications
Draft Created
Event

A Principal creates a transfer application Draft.

Recipient

The Principal.

Purpose

Confirm that a Draft has been saved.

Subject Example
Transfer Application Draft Saved
Content

The email may include:

Cycle name
Draft reference
Reminder that the Draft is not yet submitted
Closing date
Secure edit link
Status

Optional.

Because draft creation may happen repeatedly, this notification should
not generate unnecessary email noise.

Application Submitted
Event

A Principal successfully submits a transfer application.

Recipients

Primary recipient:

Principal

Administrative recipients may include:

Assigned Zonal Director
Authorized transfer administration users
Purpose

Confirm official submission and notify the review authority.

Subject Example for Principal
Transfer Application Submitted - {application_number}
Subject Example for Zonal Director
New Principal Transfer Application Awaiting Review - {application_number}
Content for Principal

The email may include:

Application number
Cycle name
Submitted date
Current status
Confirmation that editing is locked
Confirmation that the PDF is available
Secure application link
Secure PDF download page link
Content for Zonal Director

The email may include:

Application number
Principal name
Current zone
Current school
Submitted date
Current status
Secure review link
Attachment Rule

The submitted PDF should normally not be attached.

The email should direct the user to an authenticated download page.

Failure Rule

If submission email fails:

Application remains submitted.
PDF remains generated or eligible for regeneration.
Email failure is logged.
The user receives no false failure message for the application
itself.
Status

Application submission notification is planned or may be implemented
with Module 07 integration.

Submitted PDF Generation Warning
Event

The application was submitted but the PDF could not be generated.

Recipient

The Principal.

Purpose

Explain that submission succeeded and PDF generation can be retried.

Subject Example
Transfer Application Submitted - PDF Pending
Content

The email may include:

Application number
Confirmation that submission succeeded
Notice that the PDF is temporarily unavailable
Secure application link
Instruction to try downloading later
Status

Optional.

The browser flash warning may be sufficient during the MVP.

Application Withdrawn
Event

A Principal successfully withdraws an eligible application.

Recipients
Principal
Assigned Zonal Director if review had started
Authorized transfer administration users where appropriate
Purpose

Confirm withdrawal and stop unnecessary review work.

Subject Example
Transfer Application Withdrawn - {application_number}
Content

The email may include:

Application number
Withdrawal date
Current status
Confirmation that historical records remain available
Confirmation that the submitted PDF remains available
Information that a new application may be created if cycle rules
permit
Secure history link

The email should not include the complete withdrawal reason unless
official policy requires it.

Status

Planned.

New Application After Withdrawal
Event

A Principal creates a new application after withdrawing a previous
application in the same cycle.

Recipient

The Principal.

Purpose

Confirm that the new Draft is separate from the withdrawn application.

Subject Example
New Transfer Application Draft Created
Status

Optional.

Zonal Review Notifications
Zonal Review Started
Event

A Zonal Director begins review.

Recipient

The Principal.

Purpose

Inform the Principal that the application is under zonal review.

Subject Example
Transfer Application Under Zonal Review - {application_number}
Content

The email may include:

Application number
Status
Review start date
Secure tracking link
Status

Planned for Module 07.

Zonal Application Approved
Event

The Zonal Director approves or recommends the application.

Recipients
Principal
Provincial Director or authorized provincial review users
Purpose

Notify the Principal and move the workflow to the next stage.

Subject Example for Principal
Zonal Review Completed - {application_number}
Subject Example for Provincial Director
Transfer Application Ready for Provincial Review - {application_number}
Content

The email may include:

Application number
Zonal decision
Decision date
Current status
Secure tracking or review link

Detailed internal remarks should not be emailed unless policy
explicitly requires them.

Status

Planned for Module 07.

Zonal Application Rejected
Event

The Zonal Director rejects the application.

Recipient

The Principal.

Purpose

Notify the Principal of the zonal decision.

Subject Example
Zonal Decision Recorded - {application_number}
Content

The email may include:

Application number
Decision
Decision date
Current status
General decision reason where approved for communication
Secure application link

Sensitive or internal remarks should remain in the protected system.

Status

Planned for Module 07.

Application Returned for Correction
Event

A Zonal Director returns an application where workflow rules permit
correction.

Recipient

The Principal.

Purpose

Request permitted corrections.

Subject Example
Transfer Application Requires Attention - {application_number}
Important Rule

A submitted application should not become editable unless a formal
return-for-correction workflow is implemented and audited.

Status

Planned only if the workflow approves this behavior.

Provincial Review Notifications
Provincial Review Started
Recipient

The Principal.

Subject Example
Transfer Application Under Provincial Review - {application_number}
Status

Planned.

Provincial Application Approved
Recipients
Principal
Transfer Board administration or members
Subject Example
Provincial Review Completed - {application_number}
Purpose

Notify that the application is proceeding to the Board stage.

Status

Planned.

Provincial Application Rejected
Recipient

The Principal.

Subject Example
Provincial Decision Recorded - {application_number}
Status

Planned.

Transfer Board Notifications
Application Sent to Transfer Board
Recipient

The Principal.

Subject Example
Transfer Application Sent to the Transfer Board - {application_number}
Status

Planned.

Final Transfer Approved
Recipient

The Principal.

Additional recipients may include:

Current school authority
Approved destination school authority
Relevant Zonal Director
Provincial administration
Subject Example
Final Transfer Decision - Approved - {application_number}
Content

The email may include:

Application number
Final status
Approved school
Effective date
Decision date
Secure final-decision link

Official decision letters should be downloaded securely from the
system.

Status

Planned.

Final Transfer Rejected
Recipient

The Principal.

Subject Example
Final Transfer Decision - {application_number}
Content

The email may include:

Application number
Final status
Decision date
Secure decision link
Status

Planned.

Application Waitlisted
Recipient

The Principal.

Subject Example
Transfer Application Waitlisted - {application_number}
Status

Planned.

Account Administration Notifications
Account Activated
Recipient

The affected user.

Subject Example
Your Principal Transfer System Account Has Been Activated
Status

Optional.

Account Deactivated
Recipient

The affected user.

Subject Example
Your Principal Transfer System Account Has Been Deactivated
Important Rule

Email delivery must not expose internal disciplinary or administrative
reasons.

Status

Optional.

Password Reset by Administrator
Recipient

The affected user.

Subject Example
Your Account Password Has Been Reset
Content

The email should not send a plain-text password unless the approved
security policy explicitly requires a temporary password.

A secure password-reset flow is preferred.

Status

Optional.

Recipient Resolution Rules
Principal Recipient

Use the authenticated user's verified account email.

Possible fallback:

Principal Profile personal email

Fallback must be used only if approved and validated.

Zonal Director Recipient

Resolve from:

Application's current zone
Assigned Zonal Director account
Active account status
Verified email status

The system must not send a zonal application to the wrong zone.

Provincial Director Recipient

Resolve from active users with the Provincial Director role or a
configured assigned recipient.

Transfer Board Recipient

Resolve from active users with the Transfer Board Member role or a
configured board mailbox.

Administrative Recipient

Use explicit configuration or authorized role assignment.

Avoid emailing every administrator by default.

Email Privacy Rules

Emails must not include:

Passwords
Password hashes
Full private profile records
Internal permission data
Private storage paths
Database IDs where avoidable
Unnecessary NIC details
Full medical descriptions
Sensitive spouse information
Confidential reviewer notes
Internal investigation comments
Unapproved rejection commentary

Emails may include:

Principal name
Application number
Cycle name
Generic status
Submission date
Review date
Decision date
General approved school
Secure authenticated system link

NIC should be masked if included.

Example:

********123V
PDF Email Rules
Submitted application PDFs remain in private storage.
Emails should link to the application detail page.
Emails should not expose direct filesystem paths.
Emails should not expose unsigned public download URLs.
Authorization must be checked at download time.
PDF attachments should be avoided during the MVP.
If attachments are later approved, recipient authorization and email
privacy must be reviewed carefully.
Email Failure Handling

Notification sending should use safe error handling.

Example pattern:

try {
    $recipient->notify(
        new TransferApplicationSubmittedNotification(
            $application
        )
    );
} catch (\Throwable $exception) {
    report($exception);
}

Important rules:

Do not reverse a completed database transaction because email failed.
Do not show the user that submission failed when only email failed.
Log the exception.
Allow administrators to inspect failed jobs where queues are used.
Consider retrying queued notifications.
Queue Rules

Production notifications should preferably implement:

ShouldQueue

Queue configuration may use:

QUEUE_CONNECTION=database

Required setup where database queues are used:

php artisan queue:table
php artisan migrate
php artisan queue:work

Queue workers must be managed by the production server process
manager.

Development may continue using synchronous or log delivery.

Notification Implementation Pattern

Each notification must document:

Event
Recipient
Trigger point
Notification class
Mail subject
Mail content
Secure link
Queue behavior
Failure handling
Tests

Suggested directory:

app/Notifications

Suggested examples:

PrincipalRegistrationCompletedNotification.php
TransferCyclePublishedNotification.php
TransferApplicationSubmittedNotification.php
TransferApplicationWithdrawnNotification.php
ZonalReviewStartedNotification.php
ZonalApplicationApprovedNotification.php
ZonalApplicationRejectedNotification.php
ProvincialApplicationApprovedNotification.php
ProvincialApplicationRejectedNotification.php
TransferBoardDecisionNotification.php
Testing Requirements

Email tests should use:

Notification::fake();

Tests must verify:

Correct notification class
Correct recipient
Notification sent only after successful workflow action
No notification for failed validation
No notification to unauthorized users
Correct application number
Correct secure route
Email failure does not reverse database changes
Wrong-zone Zonal Director does not receive notifications
Withdrawn applications send the correct status
Sensitive information is excluded

Example:

Notification::fake();

$this->actingAs($principal)
    ->post(
        route(
            'principal.transfer-applications.submit',
            $application
        ),
        [
            'declaration_accepted' => true,
        ]
    )
    ->assertRedirect();

Notification::assertSentTo(
    $principal,
    TransferApplicationSubmittedNotification::class
);
Current Implementation Status
Implemented
Laravel email verification workflow
Mail configuration foundation
Registration email verification
Completed Application Infrastructure
Transfer application submission
Application number generation
Submitted PDF generation
Private PDF storage
Principal PDF download
Admin PDF download
Withdrawal workflow
Planned Notification Integration
Application submitted
Application withdrawn
Transfer Cycle published
Zonal review started
Zonal approved
Zonal rejected
Provincial approved
Provincial rejected
Transfer Board final decision

Module 07 should introduce the first major transfer-review notification
set.


For all three files, paste only the Markdown content inside each code block. The surrounding triple backticks are merely chat scaffolding, not honorary members of your documentation.


- Zonal review started notification
- Zonal approved notification
- Zonal rejected notification

Notification failures are logged and must not roll back the completed
workflow transaction.
