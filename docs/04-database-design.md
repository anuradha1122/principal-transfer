# Database Design

## Overview

The Principal Transfer System uses MySQL.

The database is designed to support:

- Authentication
- Roles and permissions
- Provincial education organization structure
- Principal Registry
- NIC-controlled principal registration
- Principal profiles
- Principal appointment history
- Transfer cycles
- Transfer applications
- Ranked school preferences
- Submitted application PDF storage
- Zonal review
- Provincial review
- Transfer Board decisions
- Notifications
- Reports
- Audit history

Historical application and appointment records must be preserved.

Submitted application snapshots must not change when master records are
updated later.

---

## users

Stores authenticated system users.

Key fields:

- id
- name
- email
- email_verified_at
- password
- is_active
- last_login_at
- created_by
- updated_by
- remember_token
- created_at
- updated_at

Key relationships:

- User may have one PrincipalRegistry record.
- User may have one PrincipalProfile.
- User may have many roles.
- User may have many permissions through roles.
- User may create or update administrative records.

Important rules:

- Email addresses must be unique.
- Inactive users cannot log in.
- Principal users are linked to a verified Principal Registry record.
- The last Super Admin account must not be removed or deactivated.

---

## roles

Created by Spatie Laravel Permission.

Stores system roles.

Examples:

- Super Admin
- Principal
- Zonal Director
- Provincial Director
- Transfer Board Member
- Data Entry Officer

---

## permissions

Created by Spatie Laravel Permission.

Stores system permissions.

Examples:

- view admin dashboard
- view principal dashboard
- view zonal dashboard
- view provincial dashboard
- view transfer board dashboard
- view users
- manage users
- view roles
- manage roles
- view zones
- manage zones
- view divisions
- manage divisions
- view schools
- manage schools
- view principal registry
- manage principal registry
- view principal profiles
- manage principal profiles
- view principal appointments
- manage principal appointments
- view transfer cycles
- manage transfer cycles
- view transfer applications
- create transfer applications

---

## zones

Stores the seven education zones in Sabaragamuwa Province.

Key fields:

- id
- name
- code
- district
- office_address
- telephone
- email
- is_active
- sort_order
- created_at
- updated_at

Key relationships:

- Zone has many Divisions.
- Zone has many Schools through Divisions.

Important rules:

- Zone code must be unique.
- A zone containing divisions cannot be deleted.
- Inactive zones remain available for historical records.

---

## divisions

Stores education divisions under zones.

Key fields:

- id
- zone_id
- name
- code
- office_address
- telephone
- email
- is_active
- sort_order
- created_at
- updated_at

Key relationships:

- Division belongs to Zone.
- Division has many Schools.

Important rules:

- A division must belong to one zone.
- Division code must be unique.
- A division containing schools cannot be deleted.
- Inactive divisions remain available for historical records.

---

## schools

Stores schools under education divisions.

Key fields:

- id
- division_id
- census_number
- name
- school_type
- gender_type
- school_level
- mediums
- address_line_1
- address_line_2
- city
- postal_code
- telephone
- email
- student_count
- teacher_count
- is_national_school
- is_active
- created_at
- updated_at

Key relationships:

- School belongs to Division.
- School belongs to a Zone through Division.
- School may have many PrincipalRegistry records.
- School may have many PrincipalAppointments.
- School may be referenced by many TransferApplications.
- School may be referenced by many TransferPreferences.

Important rules:

- Census number must be unique.
- `mediums` is stored as JSON.
- Inactive schools remain available for historical records.
- Submitted transfer applications must retain school references.

---

## principal_registries

Stores principals approved for self-registration.

Key fields:

- id
- nic
- normalized_nic
- full_name
- name_with_initials
- school_id
- designation
- employee_number
- registration_status
- registered_user_id
- is_active
- registered_at
- created_at
- updated_at

Key relationships:

- PrincipalRegistry belongs to School.
- PrincipalRegistry belongs to its registered User.
- PrincipalRegistry may have one PrincipalProfile.

Important rules:

- NIC must be unique.
- Normalized NIC must be unique.
- Registered user ID must be unique when present.
- Only active and unregistered registry records may be used for new
  registration.
- Registered registry records cannot be deleted.
- Registered NIC numbers cannot be reused.

---

## principal_profiles

Stores personal, contact, and service information for principals.

Key fields:

- id
- user_id
- principal_registry_id
- full_name
- name_with_initials
- nic
- employee_number
- gender
- date_of_birth
- mobile_number
- alternate_number
- personal_email
- address_line_1
- address_line_2
- city
- postal_code
- service_category
- service_grade
- first_appointment_date
- principal_service_entry_date
- retirement_date
- employment_status
- qualifications_summary
- notes
- profile_completed
- created_by
- updated_by
- created_at
- updated_at

Key relationships:

- PrincipalProfile belongs to User.
- PrincipalProfile belongs to PrincipalRegistry.
- PrincipalProfile has many PrincipalAppointments.
- PrincipalProfile has one current PrincipalAppointment.
- PrincipalProfile has many TransferApplications.

Important rules:

- Each user may have only one principal profile.
- Each Principal Registry record may have only one principal profile.
- NIC must be unique.
- Employee number must be unique when provided.
- NIC cannot be changed by the principal.
- Principals may update personal, contact, service, and appointment
  details.
- `service_category` is stored as a nullable string.
- `service_category` is not stored as a restrictive ENUM.

---

## principal_appointments

Stores complete appointment history for principals.

Key fields:

- id
- principal_profile_id
- school_id
- designation
- appointment_type
- appointment_number
- appointment_date
- start_date
- end_date
- is_current
- reason_for_end
- remarks
- created_by
- updated_by
- created_at
- updated_at

Key relationships:

- PrincipalAppointment belongs to PrincipalProfile.
- PrincipalAppointment belongs to School.
- PrincipalAppointment may be referenced as the current appointment
  snapshot source by TransferApplication.

Important rules:

- A principal may have many appointment records.
- Only one appointment should be current.
- Appointment date is used as start date.
- `start_date` must match `appointment_date`.
- When a new current appointment is created:
  - the previous current appointment is closed
  - the previous appointment `is_current` becomes false
  - the previous appointment end date becomes one day before the new
    start date
- Current appointments should not have an end date.
- Appointment history must remain available for transfer evaluation.

---

## transfer_cycles

Stores transfer application periods and eligibility rules.

Key fields:

- id
- name
- code
- transfer_type
- transfer_year
- application_open_date
- application_close_date
- effective_from_date
- minimum_service_years
- maximum_preferences
- allow_withdrawal
- status
- is_published
- created_by
- updated_by
- created_at
- updated_at

Key relationships:

- TransferCycle has many TransferApplications.

Important rules:

- Cycle code must be unique.
- Applications are allowed only for published and open cycles.
- Application opening and closing dates control availability.
- Minimum service requirement is configurable.
- Maximum preference count is configurable.
- Withdrawal may be enabled or disabled by cycle.

---

## transfer_applications

Stores principal transfer applications and a snapshot of the
principal's current service information.

Key fields:

- id
- transfer_cycle_id
- principal_profile_id
- current_principal_appointment_id
- current_school_id
- application_number
- status
- principal_name
- nic
- employee_number
- service_grade
- current_designation
- current_appointment_start_date
- current_school_service_months
- transfer_reason
- reason_details
- has_medical_reason
- has_spouse_employment_reason
- is_mutual_transfer
- mutual_principal_nic
- principal_remarks
- declaration_accepted
- submitted_at
- withdrawn_at
- withdrawal_reason
- submitted_pdf_path
- submitted_pdf_generated_at
- created_by
- updated_by
- created_at
- updated_at

Key relationships:

- TransferApplication belongs to TransferCycle.
- TransferApplication belongs to PrincipalProfile.
- TransferApplication belongs to current PrincipalAppointment.
- TransferApplication belongs to current School.
- TransferApplication has many TransferPreferences.

Important rules:

- Draft applications may be edited or deleted.
- Submitted applications cannot be edited.
- Submitted applications receive a unique application number.
- Current appointment information is stored as a snapshot.
- Master profile or school changes must not alter submitted snapshots.
- Submitted applications may be withdrawn where permitted.
- Withdrawn applications remain in history.
- A principal may have only one active application per cycle.
- Withdrawn or cancelled applications do not block a new application
  in the same cycle.
- The old database unique constraint on:
  - transfer_cycle_id
  - principal_profile_id
  was removed.
- Active application uniqueness is enforced by application logic and
  validation.
- Submitted PDFs are stored privately.
- PDF storage path and generation timestamp are recorded.

Suggested active statuses:

- Draft
- Submitted
- Zonal Review
- Zonal Approved
- Provincial Review
- Provincial Approved
- Board Review
- Waitlisted
- Approved

Suggested inactive or terminal statuses:

- Zonal Rejected
- Provincial Rejected
- Rejected
- Withdrawn
- Cancelled

---

## transfer_preferences

Stores ranked preferred schools for each transfer application.

Key fields:

- id
- transfer_application_id
- school_id
- preference_order
- preference_reason
- created_at
- updated_at

Key relationships:

- TransferPreference belongs to TransferApplication.
- TransferPreference belongs to School.

Important rules:

- Preference order must be preserved.
- Duplicate preferred schools are prohibited.
- The current school cannot be selected.
- Preference count must not exceed the transfer cycle maximum.
- Preference order should be unique within an application.

---

## Submitted PDF Storage

Submitted transfer application PDFs are stored using the local private
disk.

Example path:

```text
storage/app/private/transfer-applications/{cycle_id}/

Database fields:

submitted_pdf_path
submitted_pdf_generated_at

Important rules:

PDFs must not be placed in public storage.
PDFs must be downloaded through authorized controller routes.
Principals may download only their own PDFs.
Authorized administrators may download submitted application PDFs.
The same submitted PDF remains available after withdrawal.
Reapplication generates a separate application record and separate
PDF.
Future Workflow Tables

The following tables are planned for future modules.

transfer_application_actions

Planned to store every workflow action.

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
zonal_reviews

Planned to store Zonal Director decisions.

Possible fields:

id
transfer_application_id
zone_id
reviewer_id
recommendation
decision
remarks
reviewed_at
created_at
updated_at
provincial_reviews

Planned to store Provincial Director decisions.

Possible fields:

id
transfer_application_id
reviewer_id
decision
remarks
reviewed_at
created_at
updated_at
transfer_board_decisions

Planned to store final Transfer Board decisions.

Possible fields:

id
transfer_application_id
approved_school_id
decision
effective_date
remarks
decided_by
decided_at
created_at
updated_at
Data Integrity Principles
Historical transfer applications must be preserved.
Historical appointment records must be preserved.
Submitted snapshots must remain immutable.
Master-data edits must not rewrite submitted applications.
Foreign keys should use safe delete behavior.
Parent records with dependent records should not be deleted.
Inactive records should remain available for historical references.
Principal ownership must be enforced.
Zone-based access must be enforced in future review modules.


### users.assigned_zone_id

Nullable foreign key to zones.

Used for Zonal Director access restriction.

### transfer_applications.origin_zone_id

Immutable origin Zone captured from the submitted current-School
snapshot.

### transfer_application_actions

Stores immutable workflow actions and status transitions.

### zonal_reviews

Stores the separate Zonal review entity, recommendation, decision,
reviewer, remarks, rejection reason and review timestamps.
