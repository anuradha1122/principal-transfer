
---

# `docs/08-business-rules.md`

Your current business rules contradict the new principal editing rule and the withdrawn-application reapplication logic. :contentReference[oaicite:1]{index=1}

```md
# Business Rules

## Purpose

This document defines the mandatory business rules for the Principal
Transfer System.

Business rules must be enforced on the backend.

Frontend validation and visibility rules improve usability but do not
replace backend enforcement.

---

# General System Rules

- The system is limited to the Sabaragamuwa Province.
- All protected actions require authentication.
- Relevant protected actions require email verification.
- Role and permission checks must be performed on the backend.
- Frontend permission checks are not a security boundary.
- Historical records must be preserved.
- Submitted application snapshots must remain immutable.
- Sensitive files must use private storage.
- Unauthorized access must return HTTP 403.
- Super Admin may access all modules through the global authorization
  rule.
- Inertia SSR must remain disabled.

---

# Organization Structure Rules

## Zones

- The system contains education zones within Sabaragamuwa Province.
- A zone belongs to either Ratnapura District or Kegalle District.
- Zone codes must be unique.
- A zone containing divisions cannot be deleted.
- Inactive zones remain available for historical references.

## Divisions

- A division must belong to one zone.
- Division codes must be unique.
- A division containing schools cannot be deleted.
- Inactive divisions remain available for historical references.

## Schools

- A school must belong to one division.
- A school inherits its zone through its division.
- School census numbers must be unique.
- A school may support multiple teaching mediums.
- Teaching mediums are stored as structured JSON data.
- An inactive school cannot normally be selected for a new
  appointment or preference.
- Inactive schools remain visible in historical appointment and
  application records.
- Submitted transfer records must never lose their related school,
  division, or zone.

---

# User Account Rules

- User email addresses must be unique.
- Inactive users cannot log in.
- Users may not deactivate or delete their own account through unsafe
  administrative actions.
- The last Super Admin account cannot be deleted.
- The last Super Admin account cannot be deactivated.
- Password resets must be performed only by authorized users.
- Role assignment must be performed only by authorized users.
- Email verification must be completed where required.

---

# Principal Registration Rules

- Ordinary Laravel Breeze public registration is disabled.
- Principal registration is available only through NIC verification.
- The entered NIC must be normalized before comparison.
- Old NIC format supports nine digits followed by `V` or `X`.
- New NIC format supports twelve digits.
- NIC comparison is case-insensitive after normalization.
- The NIC must exist in `principal_registries`.
- The registry record must be active.
- The registry record must have an unregistered status.
- The registry record must not already have a registered user.
- A registered NIC cannot be reused.
- NIC verification sessions expire after fifteen minutes.
- Expired verification sessions cannot be used to register.
- Successful registration creates a user account.
- Successful registration assigns the Principal role.
- Successful registration creates or links the Principal Profile.
- Successful registration links the Principal Registry record to the
  created user.
- Successful registration changes registry status to registered.
- Successful registration records the registration timestamp.
- Registered registry records cannot be deleted.
- Duplicate principal accounts for the same NIC are prohibited.

---

# Principal Profile Rules

- Each Principal user may have at most one Principal Profile.
- Each Principal Registry record may have at most one Principal
  Profile.
- NIC numbers must be unique among Principal Profiles.
- Employee numbers must be unique when provided.
- The Principal may view only their own profile.
- Authorized administrators may view profiles according to
  permissions.
- Principals may update:
  - full name
  - name with initials
  - employee number
  - gender
  - date of birth
  - mobile number
  - alternate number
  - personal email
  - residential address
  - city
  - postal code
  - service category
  - service grade
  - first appointment date
  - principal service entry date
  - retirement date
  - employment status
  - qualifications
  - notes
  - profile completion status
- Principals cannot change their NIC number.
- NIC remains controlled by the verified Principal Registry.
- `service_category` is stored as a flexible string.
- `service_category` must not be restricted by an outdated database
  ENUM.
- Profile changes must not rewrite previously submitted application
  snapshots.
- A profile referenced by historical transfer applications must not
  be deleted unsafely.

---

# Principal Appointment Rules

- A Principal may have many appointment records.
- The Principal may manage only their own appointment records.
- Authorized administrators may manage appointment records according
  to permissions.
- Each appointment must belong to a school.
- The user must select a zone before selecting a school in the
  appointment form.
- The school dropdown must show only schools belonging to the selected
  zone.
- Appointment date is the official appointment start date.
- `start_date` must match `appointment_date`.
- A current appointment must not normally have an end date.
- Only one appointment should be current for a Principal.
- Creating or updating a record as current closes the previous current
  appointment.
- The previous current appointment:
  - receives `is_current = false`
  - receives an end date one day before the new appointment start date
- A new current appointment must not create overlapping current
  appointment periods.
- End date cannot be before start date.
- Reason for end may be required when an appointment is closed.
- Appointment history must remain available for transfer evaluation.
- Appointment changes must not rewrite application snapshots that were
  already captured.
- Appointment records referenced by submitted applications must not be
  deleted unsafely.

---

# Transfer Cycle Rules

- A transfer application may be created only for a valid Transfer
  Cycle.
- The cycle must be published.
- The current date must fall within the application opening and
  closing period.
- A closed cycle cannot accept new applications.
- A future unpublished cycle cannot accept applications.
- Cycle code must be unique.
- Transfer year must be valid.
- Application closing date cannot be before opening date.
- Effective date must be logically valid for the transfer year.
- Minimum service requirement is configurable per cycle.
- Maximum school preference count is configurable per cycle.
- Withdrawal may be enabled or disabled by cycle.
- A cycle containing submitted applications must not be deleted
  unsafely.
- Changing cycle rules must not invalidate or rewrite already submitted
  application snapshots.

---

# Transfer Application Eligibility Rules

Before creating or submitting an application:

- The user must have the Principal role.
- The user must have a Principal Profile.
- The profile must be active.
- Employment status must be Active.
- The Principal must have a current appointment.
- The current appointment must have a school.
- The application cycle must be open and published.
- The Principal must satisfy the cycle's minimum service requirement.
- Service duration is calculated from the current appointment start
  date.
- Missing or invalid appointment dates must block submission.
- The Principal must not already have another active application in
  the same cycle.

---

# Active Application Rule

A Principal may have only one active transfer application per cycle.

Active applications include statuses such as:

- Draft
- Submitted
- Zonal Review
- Zonal Approved
- Provincial Review
- Provincial Approved
- Board Review
- Waitlisted
- Approved

Inactive or terminal applications that may permit reapplication
include:

- Withdrawn
- Cancelled
- Rejected, where policy explicitly permits a new application

The database does not use a permanent unique constraint on:

```text
transfer_cycle_id + principal_profile_id

Active uniqueness must be enforced through application logic and
backend validation.

Draft Application Rules
A Principal may save an application as Draft.
Draft applications may be edited by the owner.
Draft applications may be deleted by the owner.
Draft applications may update ranked preferences.
Draft applications do not have a submitted timestamp.
Draft applications do not have a submitted PDF.
Draft applications do not receive a final application number unless
the implementation intentionally reserves one.
Administrators should not treat drafts as officially submitted
applications.
Another user cannot edit or delete a Principal's draft.
Draft applications must remain subject to current cycle rules.
Current Appointment Snapshot Rules

When an application is created, the system captures a snapshot of:

Principal name
NIC
Employee number
Service grade
Current designation
Current school
Current appointment
Current appointment start date
Service duration at the current school

The snapshot must remain unchanged after submission.

Later changes to:

Principal Profile
Principal Appointment
School name
Division
Zone
Service grade

must not silently rewrite the official submitted application snapshot.

Relationships may still be loaded for presentation, but official
submitted fields must preserve the captured values.

Transfer Reason Rules
A transfer reason is required.
Detailed explanation may be required according to the selected
reason.
Medical reason must be recorded using a boolean flag.
Spouse employment reason must be recorded using a boolean flag.
Mutual transfer must be recorded using a boolean flag.
Mutual principal NIC is required when mutual transfer is selected.
Mutual principal NIC must not match the applicant's own NIC.
Principal remarks may be optional.
Sensitive details must not be exposed in email notifications.
Future supporting documents must use private storage.
School Preference Rules
At least one school preference is required before submission.
Preference order must start from one.
Preference order must be unique within the application.
Duplicate preferred schools are prohibited.
The current school cannot be selected as a preference.
Preference count must not exceed the cycle's configured maximum.
Only eligible and active schools should normally be selectable.
School selection should support zone-based filtering.
Preferences must remain attached to the submitted application.
Submitted preferences cannot be edited.
Reordering preferences after submission is prohibited.
Historical school references must be preserved.
Submission Rules

A Draft application may be submitted only when:

The cycle remains open.
The applicant remains eligible.
The application belongs to the authenticated Principal.
Required fields are complete.
At least one valid school preference exists.
Preference count does not exceed the maximum.
The declaration is accepted.
The application does not conflict with another active application.

On successful submission:

A unique application number is generated.
Status changes to Submitted.
submitted_at is recorded.
declaration_accepted is stored as true.
The application becomes locked from editing.
Preferences become locked from editing.
A submitted PDF is generated.
PDF path is stored.
PDF generation timestamp is stored.
The Principal is redirected to the application detail page.
A success message is displayed.
A submission notification may be sent.

If PDF generation fails:

The application must remain submitted.
The failure must be logged.
The Principal must receive a warning.
The PDF may be regenerated on the next authorized download attempt.
PDF failure must not roll back the official application submission.
Application Number Rules
Each submitted application must receive a unique application number.
Application numbers must be generated by the backend.
Application numbers must not be supplied directly by the Principal.
Application numbers should identify the transfer cycle or year where
practical.
Application numbers must not be reused.
A reapplication after withdrawal receives a new application number.
Submitted Application Locking Rules

After submission:

The Principal cannot edit application fields.
The Principal cannot edit preferences.
The Principal cannot delete the application.
Authorized workflow officers may add review information.
Workflow officers must not alter the original applicant submission.
Any permitted correction must be explicitly audited.
The submitted PDF remains the official submission copy.
Master-data updates must not alter the submitted PDF snapshot.
PDF Rules
A submitted application must have an official PDF snapshot.
PDF generation uses DomPDF.
PDFs must be stored on the private local disk.
PDFs must not be exposed through storage/app/public.
PDF downloads must pass through authorized controller routes.
The Principal may download only their own application PDF.
Authorized administrators may download submitted PDFs.
Zonal Directors may download only PDFs from their assigned zone.
Provincial Directors may download authorized provincial-stage PDFs.
Transfer Board Members may download authorized board-stage PDFs.
Draft applications must not expose a submitted PDF button.
Submitted PDFs remain available after withdrawal.
A reapplication generates a separate PDF.
The stored PDF path and generated timestamp must be recorded.
Missing stored files may be regenerated from the submitted snapshot.
PDF generation failures must be logged.
Withdrawal Rules
Withdrawal is allowed only when enabled by cycle and workflow rules.
The application must belong to the authenticated Principal.
A withdrawal reason is required.
Withdrawal records:
status as Withdrawn
withdrawal reason
withdrawal timestamp
user performing the action
A withdrawn application cannot be edited.
A withdrawn application cannot be resubmitted directly.
The withdrawn application remains visible in history.
The submitted PDF remains available.
Withdrawal does not delete preferences.
Withdrawal does not delete the application number.
Withdrawal releases the active-application restriction.
The Principal may create a new application in the same cycle after
withdrawal.
The new application is a separate record.
The new application receives a new application number after
submission.
Previous withdrawn records must never be overwritten.
Reapplication Rules

A Principal may create another application in the same cycle when the
previous application is:

Withdrawn
Cancelled
Otherwise explicitly treated as inactive by policy

The new application:

must pass eligibility checks again
must capture a fresh current appointment snapshot
must create new preference records
must receive a new application number after submission
must generate a separate PDF
must not modify the previous application
must not delete the previous application history
Admin Application Rules

Authorized administrators may:

View transfer application lists
Search and filter applications
View applicant snapshots
View preferences
View submission and withdrawal timestamps
Download submitted PDFs

Administrators must not:

Edit an applicant's submitted fields directly
Delete submitted applications
Expose private PDFs publicly
Bypass permission checks

Super Admin may access all applications.

Other administrators require the appropriate permission.

Zonal Review Rules

Planned for Module 07:

A Zonal Director may access only applications originating from the
assigned zone.
Zone restriction must be based on the application's captured current
school or approved zone relationship.
Frontend filtering alone is insufficient.
Zonal review may begin only for eligible Submitted applications.
Reviewer identity must be recorded.
Review timestamp must be recorded.
Zonal remarks must be recorded where required.
Rejection requires a reason.
Approval must transition to the correct next status.
Every status change must be recorded in action history.
The original application submission remains immutable.
The Zonal Director may download the submitted PDF.
A Zonal Director may update profile or appointment information only
if explicitly permitted by the workflow and authorization policy.
Any administrative correction must be audited.
Provincial Review Rules

Planned for a later module:

Provincial review normally follows zonal approval.
The Provincial Director may review authorized provincial records.
Approval, rejection, and forwarding actions require backend
authorization.
Rejection requires remarks.
Reviewer and timestamp must be recorded.
Original submitted application fields remain immutable.
Every status change must be recorded.
Transfer Board Rules

Planned for a later module:

Only applications forwarded to the Board may enter Board Review.
Board members may view authorized applications.
Final decisions include:
Approved
Rejected
Waitlisted
Approved decisions may record the approved school.
Approved decisions may record an effective date.
Final remarks must be preserved.
Decision maker and timestamp must be recorded.
Original application data remains immutable.
Final decision documents must use private authorized downloads.
Status Rules

Status values must be consistent across:

Database
Models
Form Requests
Controllers
React pages
Status badges
Filters
Tests
Documentation

Current and planned display statuses include:

Draft
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
Cancelled

Do not mix these values with undocumented snake_case alternatives
unless the database deliberately uses constants that map to display
labels.

Audit Rules

The system must preserve:

Creator
Last updater
Submission timestamp
Withdrawal timestamp
PDF generation timestamp
Reviewer identity
Review timestamp
Status transitions
Decision remarks
Final decision timestamp

A future action-history table must record:

Application
Previous status
New status
Action
Actor
Remarks
Timestamp
Optional metadata
Notification Rules
Notifications should be sent at important workflow events.
Email failure must not reverse a successful business transaction.
Email failures must be logged.
Emails must not expose unnecessary personal or sensitive details.
Emails should contain secure system links rather than private file
URLs.
PDF files should not normally be attached where authorized download
links are safer.
Recipients must be selected according to role and workflow stage.
Deletion Rules

The following records must not be deleted unsafely:

Registered Principal Registry records
Principal Profiles with application history
Appointment records referenced by submitted applications
Transfer Cycles with applications
Submitted transfer applications
Withdrawn transfer applications
Schools referenced by historical records
Divisions containing schools
Zones containing divisions

Prefer:

inactive status
cancelled status
soft deletion
historical preservation

over destructive deletion.

Validation Rules

Backend validation must cover:

Required fields
Data types
Date ordering
Unique values
Existing foreign keys
Ownership
Role access
Permission access
Zone access
Active application uniqueness
Preference uniqueness
Maximum preference count
Current-school restriction
Declaration acceptance
Status transition validity

Read-only frontend inputs must still be validated on the backend.

Testing Rules

Automated and manual tests must cover:

Unauthorized access
Principal ownership
NIC protection
Appointment current-record behavior
Appointment date and start date equality
Zone-first school filtering
Cycle availability
Eligibility requirements
Duplicate preference prevention
Current school preference prevention
Draft creation
Draft editing
Draft deletion
Submission
Application locking
PDF generation
Principal PDF access
Admin PDF access
Unauthorized PDF access
Withdrawal
Reapplication after withdrawal
Historical record preservation


## Zonal Review Rules

- Only Submitted applications can enter Zonal Review.
- Only Zonal Review applications can be approved or rejected.
- A Zonal Director is restricted to their assigned Zone.
- Super Admin is not Zone restricted.
- Rejection requires a reason.
- Zonal actions do not modify the submitted application snapshot.
- Every Zonal transition creates an action-history record.
- Notification failure does not reverse the workflow decision.
