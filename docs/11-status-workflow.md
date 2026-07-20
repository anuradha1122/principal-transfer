
# Transfer Application Status Workflow

## Purpose

This document defines the official status workflow for Principal
Transfer Applications.

Status values must remain consistent across:

- Database
- Models
- Controllers
- Form Requests
- React pages
- Filters
- Status badges
- Notifications
- PDF documents
- Reports
- Automated tests
- Documentation

Do not mix title-case statuses with unrelated snake_case values unless
the application deliberately maps internal constants to display labels.

The current system uses human-readable title-case values.

---

# Status List

The workflow uses or plans to use the following statuses:

```text
Draft
Submitted
Zonal Review
Zonal Approved
Zonal Rejected
Provincial Review
Provincial Approved
Provincial Rejected
Board Review
Waitlisted
Approved
Rejected
Withdrawn
Cancelled


Status Categories
Editable Status
Draft

Draft is the only normal status that allows the Principal to edit the
application and school preferences.

Active Workflow Statuses
Draft
Submitted
Zonal Review
Zonal Approved
Provincial Review
Provincial Approved
Board Review
Waitlisted
Approved

These statuses normally block another active application in the same
Transfer Cycle.

Rejected or Terminal Workflow Statuses
Zonal Rejected
Provincial Rejected
Rejected

These statuses terminate the current workflow unless a future appeal or
reapplication rule explicitly permits another action.

Principal-Initiated Terminal Status
Withdrawn

A withdrawn application remains in history and may allow a new
application in the same Transfer Cycle.

Administrative Terminal Status
Cancelled

Cancelled applications remain in history.

Cancellation must include an authorized reason and audit record when
the workflow is implemented.

Current Module 06 Workflow

The currently implemented workflow is:

Draft
  ↓
Submitted
  ↓
Withdrawn, where permitted

Administrative users can currently:

View submitted applications
View application details
Download submitted PDFs

Zonal review actions begin in Module 07.

Complete Planned Workflow
Draft
  ↓
Submitted
  ↓
Zonal Review
  ├── Zonal Approved
  │      ↓
  │   Provincial Review
  │      ├── Provincial Approved
  │      │      ↓
  │      │   Board Review
  │      │      ├── Approved
  │      │      ├── Rejected
  │      │      └── Waitlisted
  │      │
  │      └── Provincial Rejected
  │
  └── Zonal Rejected

Possible terminal actions from eligible early stages:

Submitted
  ↓
Withdrawn
Zonal Review
  ↓
Withdrawn

Withdrawal availability depends on cycle and workflow rules.

Draft
Meaning

The Principal has created an application but has not completed final
submission.

Allowed Actions

The owner may:

View
Edit
Update transfer reasons
Add preferences
Reorder preferences
Delete the Draft
Submit the application
Restrictions
Draft has no official submitted PDF.
Draft normally has no submitted timestamp.
Draft is not considered an official application submission.
Other Principals cannot access it.
Administrative review cannot begin.
Entry

Created by:

Principal creates transfer application
Possible Next Status
Submitted
Possible Removal

The owner may delete the Draft.

Deleted Drafts are not part of the official submitted history unless a
future audit rule records them separately.

Submitted
Meaning

The Principal has accepted the declaration and officially submitted the
application.

Entry Requirements
Application belongs to the authenticated Principal.
Current cycle is published and open.
Principal is eligible.
Current appointment exists.
Required fields are complete.
At least one preference exists.
Preference count does not exceed the cycle maximum.
Current school is not selected.
Duplicate preferences do not exist.
Declaration is accepted.
No other active application exists in the same cycle.
Actions Performed on Entry
Generate unique application number.
Set status to Submitted.
Record submitted_at.
Store declaration acceptance.
Lock application fields.
Lock school preferences.
Generate submitted PDF.
Store PDF path.
Record PDF generation timestamp.
Display submission confirmation.
Send notification where implemented.
Allowed Actions

The Principal may:

View
Download PDF
Withdraw where permitted
Track status

Authorized administrators may:

View
Download PDF
Restrictions
Principal cannot edit.
Principal cannot delete.
Preferences cannot be reordered.
Original submitted content cannot be changed.
Possible Next Statuses
Zonal Review
Withdrawn
Cancelled
Zonal Review
Meaning

The assigned Zonal Director has begun reviewing the submitted
application.

Entry Requirements
Application status is Submitted.
Reviewer has Zonal Director role or equivalent permission.
Reviewer is assigned to the application's current zone.
Application belongs to that zone.
Actions on Entry
Set status to Zonal Review.
Record reviewer.
Record review start timestamp.
Create status-history entry.
Notify the Principal where implemented.
Allowed Actions

The assigned Zonal Director may:

View submitted application
View applicant profile
View appointment history
View preferences
Download submitted PDF
Record review remarks
Approve
Reject

The Principal may:

View
Download PDF
Withdraw where permitted
Possible Next Statuses
Zonal Approved
Zonal Rejected
Withdrawn
Cancelled
Zonal Approved
Meaning

The Zonal Director has approved or recommended the application for
Provincial review.

Actions on Entry
Record Zonal decision.
Record reviewer.
Record decision timestamp.
Record remarks.
Create status-history entry.
Notify Principal.
Notify Provincial reviewer where implemented.
Allowed Actions
View
Download PDF
Proceed to Provincial Review
Restrictions
Original submitted application remains immutable.
Principal cannot withdraw unless policy explicitly allows it.
Next Status
Provincial Review
Zonal Rejected
Meaning

The application was rejected at Zonal level.

Actions on Entry
Record rejection reason.
Record reviewer.
Record timestamp.
Create status-history entry.
Notify Principal.
Allowed Actions
View
Download PDF
View decision
Restrictions
Workflow does not proceed to Provincial review.
Original application remains in history.
Original submitted PDF remains available.
Terminal Status

Yes, unless future policy allows:

Reconsideration
Appeal
New application
Administrative reopening
Provincial Review
Meaning

The Provincial Director has begun reviewing a Zonal-approved
application.

Entry Requirements
Application status is Zonal Approved.
Reviewer has Provincial Director role or equivalent permission.
Actions on Entry
Set status to Provincial Review.
Record reviewer.
Record review start timestamp.
Create history entry.
Notify Principal where implemented.
Allowed Actions

The Provincial Director may:

View application
View Zonal decision
View Principal information
View preferences
Download submitted PDF
Record remarks
Approve
Reject
Possible Next Statuses
Provincial Approved
Provincial Rejected
Cancelled
Provincial Approved
Meaning

The Provincial Director has approved the application for Transfer
Board consideration.

Actions on Entry
Record Provincial decision.
Record reviewer.
Record decision timestamp.
Create history entry.
Notify Principal.
Notify Transfer Board where implemented.
Next Status
Board Review
Provincial Rejected
Meaning

The application was rejected at Provincial level.

Actions on Entry
Record rejection reason.
Record reviewer.
Record timestamp.
Create history entry.
Notify Principal.
Restrictions
Application does not proceed to the Transfer Board.
Original application remains immutable.
Submitted PDF remains available.
Terminal Status

Yes, unless future approved policy allows another process.

Board Review
Meaning

The Transfer Board has received the application for final
consideration.

Entry Requirements
Application status is Provincial Approved.
Application has completed required prior review stages.
Actions on Entry
Set status to Board Review.
Record Board review start.
Create history entry.
Notify Principal where implemented.
Allowed Actions

Authorized Board users may:

View application
View Zonal review
View Provincial review
View preferences
Download submitted PDF
Record final remarks
Approve
Reject
Waitlist
Possible Next Statuses
Approved
Rejected
Waitlisted
Waitlisted
Meaning

The Transfer Board has not issued final approval or rejection and has
placed the application on a waiting list.

Allowed Actions
View
Download PDF
Record later Board decision
Maintain waiting-list history
Possible Next Statuses
Approved
Rejected
Cancelled
Restrictions
Principal cannot edit the original application.
Waitlisted application remains active.
A second active application in the same cycle remains blocked unless
policy states otherwise.
Approved
Meaning

The Transfer Board has issued final approval.

Data That May Be Recorded
Approved school
Approved division
Approved zone
Effective date
Final remarks
Decision maker
Decision timestamp
Final decision document
Actions on Entry
Record final decision.
Record approved school where applicable.
Record effective date.
Create history entry.
Notify Principal.
Generate final decision PDF where implemented.
Terminal Status

Yes.

Restrictions
Original submitted application remains immutable.
Final decision history cannot be deleted.
Submitted PDF remains available.
Rejected
Meaning

The Transfer Board has issued final rejection.

Actions on Entry
Record final rejection reason.
Record decision maker.
Record timestamp.
Create history entry.
Notify Principal.
Generate final decision document where implemented.
Terminal Status

Yes.

Restrictions
Original submitted application remains immutable.
Submitted PDF remains available.
Withdrawn
Meaning

The Principal has withdrawn the submitted application where workflow
rules permit.

Entry Requirements
Application belongs to authenticated Principal.
Current status permits withdrawal.
Transfer Cycle permits withdrawal.
Withdrawal reason is supplied.
Actions on Entry
Set status to Withdrawn.
Record withdrawal reason.
Record withdrawn_at.
Record user performing withdrawal.
Create history entry where available.
Notify relevant reviewers where implemented.
Allowed Actions

The Principal may:

View withdrawn application
Download submitted PDF
View withdrawal reason
Create a new application in the same cycle
Restrictions
Withdrawn application cannot be edited.
Withdrawn application cannot be resubmitted directly.
Withdrawn application cannot be deleted.
Previous preferences remain in history.
Previous PDF remains available.
Reapplication Rule

A new application is a separate record.

The new application:

captures a new snapshot
has new preferences
receives a new application number after submission
generates a new PDF
does not modify the withdrawn record
Terminal Status

Yes for that application record.

Cancelled
Meaning

An authorized administrator has cancelled the application due to an
approved administrative reason.

Possible Reasons
Duplicate administrative record
Invalid cycle configuration
Applicant no longer eligible
Official administrative cancellation
Data integrity correction
Actions on Entry
Record cancellation reason.
Record actor.
Record timestamp.
Create history entry.
Notify Principal where appropriate.
Restrictions
Cancellation requires backend authorization.
Cancellation must not delete the record.
Submitted PDF remains available where a submission existed.
Cancellation must not silently rewrite original data.
Terminal Status

Yes.

Valid Transition Matrix
Current Status	Allowed Next Statuses
Draft	Submitted
Submitted	Zonal Review, Withdrawn, Cancelled
Zonal Review	Zonal Approved, Zonal Rejected, Withdrawn, Cancelled
Zonal Approved	Provincial Review, Cancelled
Zonal Rejected	None, unless future policy permits reopening
Provincial Review	Provincial Approved, Provincial Rejected, Cancelled
Provincial Approved	Board Review, Cancelled
Provincial Rejected	None, unless future policy permits reopening
Board Review	Approved, Rejected, Waitlisted, Cancelled
Waitlisted	Approved, Rejected, Cancelled
Approved	None
Rejected	None
Withdrawn	None
Cancelled	None
Invalid Transition Examples

The following transitions must be rejected:

Draft → Approved
Draft → Zonal Approved
Submitted → Provincial Approved
Submitted → Board Review
Zonal Rejected → Provincial Review
Provincial Rejected → Board Review
Approved → Draft
Rejected → Draft
Withdrawn → Submitted
Cancelled → Submitted

A new application after withdrawal is not a transition.

It is a new TransferApplication record.

Status Authorization Rules
Principal

May cause:

Draft → Submitted
Submitted → Withdrawn
Zonal Review → Withdrawn, only where permitted

May not directly approve or reject an application.

Zonal Director

May cause:

Submitted → Zonal Review
Zonal Review → Zonal Approved
Zonal Review → Zonal Rejected

Only for applications in the assigned zone.

Provincial Director

May cause:

Zonal Approved → Provincial Review
Provincial Review → Provincial Approved
Provincial Review → Provincial Rejected
Transfer Board Member

May cause:

Provincial Approved → Board Review
Board Review → Approved
Board Review → Rejected
Board Review → Waitlisted
Waitlisted → Approved
Waitlisted → Rejected
Super Admin

May perform authorized workflow actions across all records, but must
still follow valid transition rules unless a specifically audited
override is implemented.

Status History Rules

Every status transition should record:

Transfer application ID
Previous status
New status
Action name
User performing the action
Role of actor
Remarks
Timestamp
Optional metadata

Suggested future table:

transfer_application_actions

Possible fields:

id
transfer_application_id
action
from_status
to_status
remarks
acted_by
acted_at
metadata
created_at
updated_at

Until this table is implemented, documentation must describe status
history as planned rather than falsely claiming it already exists.
Software documents occasionally benefit from acknowledging reality.

PDF Rules by Status
Status	Submitted PDF Available
Draft	No
Submitted	Yes
Zonal Review	Yes
Zonal Approved	Yes
Zonal Rejected	Yes
Provincial Review	Yes
Provincial Approved	Yes
Provincial Rejected	Yes
Board Review	Yes
Waitlisted	Yes
Approved	Yes
Rejected	Yes
Withdrawn	Yes
Cancelled	Yes, if previously submitted
Active Application Rule by Status
Status	Blocks another active application in same cycle
Draft	Yes
Submitted	Yes
Zonal Review	Yes
Zonal Approved	Yes
Zonal Rejected	Policy dependent
Provincial Review	Yes
Provincial Approved	Yes
Provincial Rejected	Policy dependent
Board Review	Yes
Waitlisted	Yes
Approved	Yes
Rejected	Policy dependent
Withdrawn	No
Cancelled	No

The current implemented reapplication rule explicitly allows a new
application after:

Withdrawn
Cancelled

Any additional reapplication status must be approved and implemented
explicitly.

UI Status Badge Guidelines

Suggested badge styles:

Status	Suggested Style
Draft	Slate
Submitted	Blue
Zonal Review	Amber
Zonal Approved	Emerald
Zonal Rejected	Red
Provincial Review	Amber
Provincial Approved	Emerald
Provincial Rejected	Red
Board Review	Violet
Waitlisted	Orange
Approved	Emerald
Rejected	Red
Withdrawn	Slate
Cancelled	Red

React pages should use one shared status-style helper where practical.

Testing Requirements

Tests must verify:

Draft may be submitted.
Non-Draft cannot be submitted.
Submitted application becomes locked.
Submitted application receives application number.
Submitted application records timestamp.
PDF is generated after submission.
Invalid transitions are rejected.
Principal cannot approve an application.
Wrong-zone Zonal Director cannot review.
Zonal approval uses the correct next status.
Zonal rejection ends the Zonal workflow.
Provincial review requires Zonal approval.
Board review requires Provincial approval.
Withdrawn application cannot be resubmitted.
Withdrawn application permits a new record in the same cycle.
Withdrawn application retains PDF access.
Terminal statuses reject further transitions.
Status labels match database and frontend values.


Submitted
    ↓
Zonal Review
    ├── Zonal Approved
    └── Zonal Rejected
