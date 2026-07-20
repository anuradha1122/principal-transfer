# User Roles

## Role Management

The system uses Spatie Laravel Permission for role and permission
management.

All protected actions must be authorized on the backend.

Frontend permission checks are used only to control navigation and
button visibility. They are not a security boundary.

Super Admin access is handled through a global authorization rule.

## Super Admin

The Super Admin has unrestricted access to all system modules,
configuration, records, reports, and administrative functions.

### Main Responsibilities

- Manage users
- Manage roles
- Manage permissions
- Activate and deactivate accounts
- Reset user passwords
- Manage zones
- Manage divisions
- Manage schools
- Manage Principal Registry records
- Import Principal Registry records
- Manage principal profiles
- Manage principal appointments
- Manage transfer cycles
- View all transfer applications
- Download submitted transfer PDFs
- View zonal decisions
- View provincial decisions
- View Transfer Board decisions
- Access reports
- Access audit information
- Manage future system settings

### Access Rules

- May access all zones
- May access all divisions
- May access all schools
- May access all principal records
- May access all transfer applications
- May access all workflow stages
- May bypass normal permission checks through the global Super Admin
  authorization rule

## Principal

The Principal is the applicant who creates and manages their own
profile, appointment history, and transfer applications.

### Main Responsibilities

- Register through NIC verification
- Verify email address
- Sign in to the principal dashboard
- View own profile
- Update own personal information
- Update own contact information
- Update own service information
- Update own qualifications
- Update own employment information
- Manage own appointment history
- Add own appointment records
- Edit own appointment records
- Delete own appointment records where permitted
- Select the current appointment
- View open transfer cycles
- Create transfer application drafts
- Edit transfer application drafts
- Delete transfer application drafts
- Select ranked preferred schools
- Submit transfer applications
- Download submitted application PDFs
- Withdraw applications where permitted
- Create a new application after withdrawal
- View transfer application history
- Track review status
- View final decisions

### Profile Restrictions

- The principal may update personal, contact, service, and appointment
  information.
- The principal cannot change the NIC number.
- The NIC number is linked to the verified Principal Registry record.
- The principal may access only their own profile.
- The principal may access only their own appointment records.

### Transfer Application Restrictions

- The principal may apply only during a published and open transfer
  cycle.
- The principal must have an active employment status.
- The principal must have a current appointment.
- The principal must satisfy the minimum service requirement.
- The current school cannot be selected as a preference.
- Duplicate preferred schools are prohibited.
- Only one active application is allowed per cycle.
- A withdrawn or cancelled application does not block a new
  application in the same cycle.
- Submitted applications cannot be edited.
- Submitted application PDFs remain available after withdrawal.
- The principal may download only their own transfer application PDFs.

## Zonal Director

The Zonal Director reviews transfer applications originating from
schools within the director's assigned education zone.

### Main Responsibilities

- Access the zonal dashboard
- View applications originating from the assigned zone
- Search and filter zonal applications
- View principal profile information
- View principal appointment history
- View current appointment snapshot
- View transfer reasons
- View ranked school preferences
- Download submitted application PDFs
- Record zonal review remarks
- Record zonal recommendation
- Approve an application at zonal level
- Reject an application at zonal level
- Return an application where workflow rules permit
- View zonal review history

### Access Restrictions

- May access only applications originating from the assigned zone.
- May not access applications from another zone.
- May not alter the principal's NIC.
- May not edit submitted application content directly.
- May update principal service or appointment information only where
  explicitly permitted by the workflow and authorization rules.
- All decisions must be recorded with reviewer identity and timestamp.

## Provincial Director

The Provincial Director reviews applications that have completed the
zonal review stage.

### Main Responsibilities

- Access the provincial dashboard
- View zonally reviewed applications
- View zonal recommendations
- View principal profile and appointment details
- View transfer application details
- View ranked school preferences
- Download submitted application PDFs
- Record provincial remarks
- Approve an application at provincial level
- Reject an application at provincial level
- Forward approved applications to the Transfer Board
- View provincial review history

### Access Restrictions

- May access applications from the Sabaragamuwa Province.
- May not alter submitted application content.
- May not change the principal's NIC.
- May not bypass required zonal review unless explicitly authorized.
- All decisions must be recorded with reviewer identity and timestamp.

## Transfer Board Member

The Transfer Board Member reviews applications forwarded to the
Transfer Board and records final transfer decisions.

### Main Responsibilities

- Access the Transfer Board dashboard
- View board-pending applications
- View principal information
- View appointment history
- View zonal recommendations
- View provincial recommendations
- View school preferences
- Download submitted application PDFs
- Record board remarks
- Approve a transfer
- Reject a transfer
- Waitlist an application
- Record the approved school
- Record the effective transfer date
- Record the final decision
- Generate or view final decision documents

### Access Restrictions

- May access only applications forwarded to the Transfer Board.
- May not edit the original submitted application.
- May not alter the principal's NIC.
- Final decisions must be recorded with board member identity and
  timestamp.
- Final records must remain available for audit and reporting.

## Data Entry Officer

The Data Entry Officer maintains organization and principal master
records.

### Main Responsibilities

- Manage zones where permitted
- Manage divisions where permitted
- Manage schools where permitted
- Manage Principal Registry records
- Import Principal Registry data
- Manage principal profile records where permitted
- Manage principal appointment records where permitted
- Correct non-workflow master data
- Support administrative data preparation

### Access Restrictions

- May not manage roles or permissions unless explicitly authorized.
- May not perform zonal review.
- May not perform provincial review.
- May not record Transfer Board decisions.
- May not alter submitted application content.
- May not download transfer PDFs unless granted the required
  permission.
- May not access records outside assigned administrative limits where
  zone restrictions apply.

## Role-Based Dashboard Routing

After login, users are redirected according to role:

- Super Admin → Admin Dashboard
- Principal → Principal Dashboard
- Zonal Director → Zonal Dashboard
- Provincial Director → Provincial Dashboard
- Transfer Board Member → Transfer Board Dashboard
- Data Entry Officer → Admin or assigned operational dashboard

## Authorization Principles

- Every controller action must perform backend authorization.
- Every protected route must use authentication middleware.
- Principal routes must require the Principal role.
- Zone-restricted users must be limited by assigned zone.
- Frontend button visibility must not replace backend checks.
- Ownership checks must be performed for principal records.
- Submitted PDFs must be served through authorized controller routes.
- Historical applications must remain accessible according to role.
- Super Admin may access all records.
- Unauthorized access must return HTTP 403.


## Zonal Director

A Zonal Director is assigned to one education Zone.

The Zonal Director may:

- View applications originating from the assigned Zone
- Download submitted application PDFs
- Start a Zonal review
- Record a recommendation
- Approve an application at Zonal level
- Reject an application at Zonal level
- Record remarks and rejection reasons

A Zonal Director may not access another Zone through filters, page
navigation, or direct URLs.
