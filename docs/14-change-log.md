# Change Log

## Project

Principal Transfer System

## Organization

Provincial Department of Education  
Sabaragamuwa Province, Sri Lanka

---

# Module 01: Project Foundation, Authentication, Roles and Admin Layout

## Added

- Created the Laravel project foundation.
- Added React with Inertia.js.
- Configured the MySQL database.
- Installed Laravel Breeze authentication.
- Added login functionality.
- Added logout functionality.
- Added password-reset functionality.
- Added email verification.
- Installed Spatie Laravel Permission.
- Added role and permission database tables.
- Added six initial system roles.
- Added the Super Admin account seeder.
- Added role-based dashboard redirection.
- Added Admin Dashboard.
- Added Principal Dashboard placeholder.
- Added Zonal Director Dashboard placeholder.
- Added Provincial Director Dashboard placeholder.
- Added Transfer Board Dashboard placeholder.
- Added AdminLayout.
- Added Admin sidebar.
- Added Admin topbar.
- Added shared frontend permissions.
- Added initial authorization tests.
- Added initial project documentation.

## Changed

- Disabled unrestricted Laravel Breeze registration.
- Configured protected administrative pages to use AdminLayout.
- Configured Super Admin access through a global authorization rule.
- Kept Inertia SSR disabled.

## Security

- Added authentication middleware to protected routes.
- Added verified-email middleware where required.
- Added role-based route protection.
- Confirmed that frontend checks are not treated as backend security.

---

# Module 02: Roles, Permissions and User Management

## Added

- Added user administration.
- Added user list.
- Added user search.
- Added user filters.
- Added user creation.
- Added user editing.
- Added role assignment.
- Added account activation.
- Added account deactivation.
- Added email verification management.
- Added administrator password reset.
- Added last login tracking.
- Added account creator tracking.
- Added account updater tracking.
- Added role administration.
- Added role creation.
- Added role editing.
- Added role permission assignment.
- Added permission administration.
- Added permission creation.
- Added safe permission deletion.
- Added user-management feature tests.
- Added role-management feature tests.
- Added authorization tests.

## Changed

- Updated login handling to prevent inactive users from signing in.
- Updated sidebar items to respect permissions.
- Updated dashboard access to respect roles and permissions.

## Security

- Added self-deactivation protection.
- Added self-deletion protection.
- Added last Super Admin deletion protection.
- Added last Super Admin deactivation protection.
- Added system-role deletion protection.
- Added backend permission checks for protected actions.

---

# Module 03: Zones, Divisions and Schools Management

## Added

- Added `zones` table.
- Added Zone model.
- Added Zone controller.
- Added Zone CRUD pages.
- Added Zone search.
- Added Zone filters.
- Added Zone active status.
- Added Zone sort order.
- Added seven-zone seeder.
- Added `divisions` table.
- Added Division model.
- Added Division controller.
- Added Division CRUD pages.
- Added Division search.
- Added Division filters.
- Added Division active status.
- Added `schools` table.
- Added School model.
- Added School controller.
- Added School CRUD pages.
- Added School census number.
- Added School type.
- Added School gender type.
- Added School level.
- Added teaching-medium management.
- Added School address fields.
- Added School contact fields.
- Added student count.
- Added teacher count.
- Added national-school flag.
- Added School active status.
- Added Zone, Division, and School navigation.
- Added organization-structure feature tests.
- Added DivisionSeeder.
- Added SchoolSeeder.

## Relationships

- Added Zone-to-Division relationship.
- Added Zone-to-School through Division relationship.
- Added Division-to-Zone relationship.
- Added Division-to-School relationship.
- Added School-to-Division relationship.
- Added School-to-Zone through Division relationship.

## Changed

- Updated School mediums to use structured JSON data.
- Updated School model to cast mediums as an array.
- Updated DatabaseSeeder order.
- Updated School seeding to match the actual database columns.

## Security and Integrity

- Added unique Zone code validation.
- Added unique Division code validation.
- Added unique School census-number validation.
- Prevented deletion of Zones containing Divisions.
- Prevented deletion of Divisions containing Schools.
- Preserved inactive records for historical use.

---

# Module 04: Principal Registry and NIC-Controlled Self-Registration

## Added

- Added `principal_registries` table.
- Added PrincipalRegistry model.
- Added Principal Registry controller.
- Added Principal Registry administration pages.
- Added Registry search.
- Added Registry filters.
- Added active and inactive Registry status.
- Added registration-status tracking.
- Added Registry-to-School relationship.
- Added Registry-to-User relationship.
- Added Registry-to-PrincipalProfile relationship.
- Added NIC normalization service.
- Added old NIC format validation.
- Added new NIC format validation.
- Added uppercase NIC normalization.
- Added Registry CSV import.
- Added Registry import validation.
- Added Registry import result feedback.
- Added Registry CSV template download.
- Added NIC verification page.
- Added NIC verification endpoint.
- Added time-limited NIC verification session.
- Added Principal registration form.
- Added Principal account creation.
- Added automatic Principal-role assignment.
- Added Registry-to-user linking.
- Added Principal Profile creation during registration.
- Added Registry registration-status update.
- Added Registry registration timestamp.
- Added email-verification redirect.
- Added Principal registration tests.

## Changed

- Replaced unrestricted public registration with NIC-controlled
  registration.
- Updated login page with the Principal-registration entry point.
- Updated Principal Registry records after successful registration.
- Updated registration flow to create or link a Principal Profile.

## Security and Integrity

- Prevented registration using an NIC that does not exist in the
  Registry.
- Prevented registration using an inactive Registry record.
- Prevented registration using an already registered Registry record.
- Prevented duplicate normalized NIC values.
- Prevented duplicate Principal registration.
- Added fifteen-minute NIC verification expiry.
- Prevented registered Registry records from being deleted.
- Wrapped registration in a database transaction.

## Fixes

- Corrected Registry-to-user relationship handling.
- Corrected Registry-to-profile relationship handling.
- Clarified that Registry status `registered` represents account
  registration, not email verification.
- Investigated signed email-verification URL failures.
- Confirmed that `APP_URL` must match the verification-link host.
- Identified copied HTML `&amp;` values as a possible verification-link
  failure source.

---

# Module 05: Principal Profile and Current Appointment Management

## Added

- Added `principal_profiles` table.
- Added PrincipalProfile model.
- Added Principal Profile controller.
- Added Principal Profile administration.
- Added Principal Profile list.
- Added Principal Profile search.
- Added Principal Profile filters.
- Added Principal Profile create page.
- Added Principal Profile edit page.
- Added Principal Profile show page.
- Added personal-information fields.
- Added contact-information fields.
- Added address fields.
- Added service-information fields.
- Added employment status.
- Added qualification summary.
- Added notes.
- Added profile-completion status.
- Added Principal self-service profile view.
- Added Principal self-service profile edit.
- Added Principal self-service profile update.
- Added `principal_appointments` table.
- Added PrincipalAppointment model.
- Added appointment history.
- Added current appointment relationship.
- Added School relationship.
- Added designation.
- Added appointment type.
- Added appointment letter number.
- Added appointment date.
- Added start date.
- Added end date.
- Added current-appointment flag.
- Added reason for ending.
- Added remarks.
- Added Admin appointment creation.
- Added Admin appointment editing.
- Added Admin appointment deletion.
- Added Principal self-service appointment creation.
- Added Principal self-service appointment editing.
- Added Principal self-service appointment deletion.
- Added Principal appointment ownership checks.
- Added Principal appointment form.
- Added appointment Create page.
- Added appointment Edit page.
- Added current-appointment closure logic.
- Added Principal Profile feature tests.
- Added Principal self-service profile tests.
- Added appointment authorization tests.

## Changed

- Updated Principal profile rules to allow editing personal, contact,
  service, and employment information.
- Kept NIC read-only for Principals.
- Updated appointment handling so only one record remains current.
- Updated previous current appointment to close automatically.
- Updated previous appointment end date to one day before the new
  appointment start date.
- Updated Principal dashboard to display profile and appointment
  information.
- Updated AdminLayout to support Principal navigation.
- Added a separate Principal sidebar.

## Security and Integrity

- Added Principal profile ownership validation.
- Added Principal appointment ownership validation.
- Prevented Principals from changing NIC.
- Prevented Principals from accessing another Principal's appointments.
- Preserved appointment history for transfer evaluation.

## Fixes

- Fixed missing profile-option arrays causing React `.map()` errors.
- Added safe default values to Principal Profile pages.
- Fixed current-appointment relationship handling.
- Fixed appointment form data-loading issues.
- Fixed profile field validation inconsistencies.

---

# Module 06: Transfer Cycles and Transfer Application Management

## Added

### Transfer Cycles

- Added `transfer_cycles` table.
- Added TransferCycle model.
- Added TransferCycle controller.
- Added StoreTransferCycleRequest.
- Added UpdateTransferCycleRequest.
- Added Transfer Cycle list page.
- Added Transfer Cycle create page.
- Added Transfer Cycle edit page.
- Added Transfer Cycle show page.
- Added shared CycleForm component.
- Added cycle name.
- Added cycle code.
- Added transfer type.
- Added transfer year.
- Added application opening date.
- Added application closing date.
- Added transfer effective date.
- Added minimum-service requirement.
- Added maximum-preference count.
- Added cycle status.
- Added published-cycle handling.
- Added open-cycle detection.
- Added Transfer Cycle navigation.
- Added Transfer Cycle feature tests.

### Transfer Applications

- Added `transfer_applications` table.
- Added `transfer_preferences` table.
- Added TransferApplication model.
- Added TransferPreference model.
- Added Principal TransferApplication controller.
- Added Admin TransferApplication controller.
- Added StoreTransferApplicationRequest.
- Added UpdateTransferApplicationRequest.
- Added SubmitTransferApplicationRequest.
- Added Principal Transfer Application Index.
- Added Principal Transfer Application Create page.
- Added Principal Transfer Application Edit page.
- Added Principal Transfer Application Show page.
- Added shared ApplicationForm component.
- Added Admin Transfer Application Index.
- Added Admin Transfer Application Show.
- Added application-number generation.
- Added application-status handling.
- Added transfer-reason capture.
- Added detailed-reason capture.
- Added medical-reason flag.
- Added spouse-employment-reason flag.
- Added mutual-transfer flag.
- Added mutual Principal NIC.
- Added Principal remarks.
- Added final declaration.
- Added submission timestamp.
- Added withdrawal timestamp.
- Added withdrawal reason.
- Added Principal application history.
- Added Admin application viewing.
- Added application pagination.
- Added status badges.
- Added application filters.
- Added application empty states.
- Added application feature tests.

### Current Appointment Snapshot

- Added Principal-name snapshot.
- Added NIC snapshot.
- Added employee-number snapshot.
- Added service-grade snapshot.
- Added current-designation snapshot.
- Added current-appointment reference.
- Added current-School reference.
- Added current-appointment-start-date snapshot.
- Added current-School-service-month calculation.

### Preferences

- Added ranked School preferences.
- Added preference order.
- Added preference reason.
- Added School relationship.
- Added Division display.
- Added Zone display.
- Added duplicate preference prevention.
- Added current-School preference prevention.
- Added maximum preference count validation.
- Added preference-order preservation.

### Submission

- Added final application submission.
- Added Draft-only submission.
- Added open-cycle revalidation.
- Added eligibility revalidation.
- Added declaration validation.
- Added unique application number.
- Added status change to Submitted.
- Added submission timestamp.
- Added application locking.
- Added preference locking.
- Added post-submission redirect.
- Added success feedback.

### PDF Generation

- Installed and configured DomPDF support.
- Added TransferApplicationPdfService.
- Added submitted-application Blade PDF view.
- Added submitted PDF path field.
- Added submitted PDF generation timestamp.
- Added private PDF storage.
- Added PDF generation after submission.
- Added PDF regeneration when the stored file is missing.
- Added Principal PDF download route.
- Added Admin PDF download route.
- Added PDF button on Principal Application Index.
- Added PDF button on Principal Application Show.
- Added PDF button on Admin Application Show.
- Added PDF availability after withdrawal.
- Added PDF generation failure logging.
- Added PDF generation warning handling.

### Withdrawal and Reapplication

- Added withdrawal action.
- Added mandatory withdrawal reason.
- Added status change to Withdrawn.
- Added withdrawal timestamp.
- Preserved withdrawn application history.
- Preserved preferences after withdrawal.
- Preserved application number after withdrawal.
- Preserved PDF after withdrawal.
- Prevented withdrawn application editing.
- Prevented withdrawn application resubmission.
- Allowed a new application after withdrawal.
- Allowed inactive cancelled applications to be excluded from active
  application checks.
- Added separate application record for reapplication.
- Added separate application number for reapplication.
- Added separate PDF for reapplication.

### Principal Appointment Improvements

- Added Principal Appointment controller.
- Added StoreOwnAppointmentRequest.
- Added UpdateOwnAppointmentRequest.
- Added Principal appointment Create page.
- Added Principal appointment Edit page.
- Added shared Principal AppointmentForm.
- Added Zone-first School selection.
- Added School filtering by selected Zone.
- Added existing Zone preselection during appointment editing.
- Added automatic Appointment Date to Start Date synchronization.
- Made Start Date read-only in the interface.
- Added backend validation requiring Start Date to match Appointment
  Date.
- Cleared End Date when an appointment is current.

### Principal Profile Improvements

- Updated Principal Profile controller.
- Updated Principal Profile validation.
- Updated Principal Profile Edit page.
- Updated Principal Profile Show page.
- Added flexible service-category input.
- Added service-category migration from restrictive ENUM behavior to a
  nullable string.

### Navigation and Layout

- Added PrincipalSidebar.
- Updated AdminSidebar.
- Updated AdminLayout.
- Added Principal Transfer Applications navigation.
- Updated Principal Dashboard.
- Added role-aware navigation behavior.
- Added active route highlighting.

### Seeders and Permissions

- Added DivisionSeeder.
- Added SchoolSeeder.
- Updated DatabaseSeeder.
- Updated RolePermissionSeeder.
- Added or updated Transfer Cycle permissions.
- Added or updated Transfer Application permissions.
- Added or updated Principal Profile permissions.
- Added or updated Principal Appointment permissions.

### Documentation

- Updated Project Overview.
- Updated System Scope.
- Updated User Roles.
- Updated Database Design.
- Updated Module Structure.
- Updated Routes.
- Updated Permissions.
- Updated Business Rules.
- Added or updated Email Notifications.
- Added or updated PDF and Excel Exports.
- Updated Status Workflow.
- Updated Testing Checklist.
- Updated Development Log.
- Updated Change Log.
- Updated Next Chat Handover.

## Changed

- Changed application uniqueness from one permanent application per
  Principal per Cycle to one active application per Principal per
  Cycle.
- Changed withdrawn applications so they no longer block reapplication.
- Changed cancelled applications so they may be treated as inactive.
- Changed submitted applications to become read-only.
- Changed School preference handling to preserve ranking.
- Changed PDF storage to private application storage.
- Changed PDF downloads to use authorized controller routes.
- Changed Principal Profile `service_category` from restrictive ENUM
  behavior to a string.
- Changed appointment workflow to require Zone selection before School
  selection.
- Changed appointment Start Date to derive from Appointment Date.
- Changed Principal navigation to use a dedicated sidebar.

## Database Changes

- Added migration:
  `create_transfer_cycles_table`.
- Added migration:
  `create_transfer_applications_table`.
- Added migration:
  `create_transfer_preferences_table`.
- Added migration:
  `change_service_category_to_string_in_principal_profiles_table`.
- Added migration:
  `add_pdf_fields_to_transfer_applications_table`.
- Updated School migration during local development.
- Added replacement indexes required before removing the old combined
  Transfer Application unique index.
- Removed the permanent unique constraint on:
  - `transfer_cycle_id`
  - `principal_profile_id`
- Added status-aware application lookup logic.

## Security and Integrity

- Added Principal application ownership checks.
- Added Principal PDF ownership checks.
- Added Admin PDF permission checks.
- Prevented Draft submission by another Principal.
- Prevented editing submitted applications.
- Prevented deleting submitted applications.
- Prevented duplicate active applications.
- Prevented duplicate School preferences.
- Prevented selecting the current School.
- Preserved submitted application snapshots.
- Preserved withdrawn application history.
- Kept PDF files outside public storage.
- Revalidated cycle eligibility during submission.

## Fixes

### Transfer Application Unique Index

- Fixed MySQL blocking reapplication after withdrawal.
- Added separate indexes before removing the combined unique index.
- Removed duplicate migration attempts that dropped the same index.
- Added application-level active-status enforcement.

### Service Category

- Fixed MySQL `Data truncated for column 'service_category'`.
- Replaced restrictive database ENUM behavior with a nullable string.
- Updated validation and frontend handling.

### React Errors

- Fixed undefined `.map()` errors.
- Added safe option defaults.
- Added safe relationship access.
- Added optional chaining.
- Added empty preference fallbacks.

### Principal Application Show Page

- Fixed `application is not defined`.
- Moved PDF availability logic inside the React component.
- Added safe transfer-cycle access.
- Added safe preference relationship access.
- Added PDF download section.

### Principal Application Index

- Corrected View action behavior.
- Added explicit navigation support.
- Kept PDF download available from the Index.
- Added safe application and pagination defaults.

### Appointment Forms

- Fixed incorrect School list behavior.
- Added Zone-based School filtering.
- Fixed existing appointment Zone preselection.
- Fixed Appointment Date and Start Date mismatch.
- Prevented manual Start Date changes.
- Fixed current-appointment End Date handling.

### PDF Workflow

- Fixed the requirement to make submitted PDF available from both
  Principal Index and Principal Show.
- Added Admin PDF access.
- Added PDF regeneration support.
- Ensured PDF failures do not reverse a successful submission.

## Tests Added or Updated

- Updated Admin Principal Profile Management Test.
- Added Admin Transfer Cycle Management Test.
- Updated Principal Self Profile Test.
- Added Principal Transfer Application Management Test.
- Added tests for:
  - Transfer Cycle CRUD
  - Application eligibility
  - Draft creation
  - Draft editing
  - Draft deletion
  - Application ownership
  - Preference validation
  - Application submission
  - Application locking
  - Withdrawal
  - Reapplication
  - PDF access
  - Profile updating
  - Appointment behavior

## Result

Module 06 completed the first operational Principal transfer-application
workflow.

The system now supports:

- Transfer Cycle management
- Principal profile and appointment self-service
- Transfer application Drafts
- Ranked School preferences
- Eligibility validation
- Final submission
- Application numbers
- Private submitted PDFs
- Principal PDF downloads
- Admin PDF downloads
- Withdrawal
- Reapplication
- Application history
- Admin application viewing

---

# Current Completed Modules

## Module 01

Project Foundation, Authentication, Roles and Admin Layout

## Module 02

Roles, Permissions and User Management

## Module 03

Zones, Divisions and Schools Management

## Module 04

Principal Registry and NIC-Controlled Self-Registration

## Module 05

Principal Profile and Current Appointment Management

## Module 06

Transfer Cycles and Transfer Application Management

---

# Next Planned Module

## Module 07

Zonal Director Transfer Review and Recommendation

Planned changes:

- Add Zonal Director Zone assignment.
- Add Zone-restricted application access.
- Add Zonal Transfer Application list.
- Add Zonal Transfer Application detail page.
- Add Review-start action.
- Add Zonal recommendation.
- Add Zonal approval.
- Add Zonal rejection.
- Add mandatory rejection reason.
- Add Zonal reviewer tracking.
- Add review timestamp.
- Add application action-history table.
- Add status transition validation.
- Add Zonal submitted-PDF access.
- Add Zonal email notifications.
- Add Zonal permissions.
- Add Zonal sidebar navigation.
- Add automated Zonal workflow tests.
- Update all related documentation.

---

# Git History Reference

Previous completed commit:

```text
a8bb9d0 profile principal profiles and appointment management

Recommended detailed commit:

git commit \
  -m "Add transfer cycles and principal transfer application workflow" \
  -m "Add principal self-service appointment and profile updates, admin application views, submitted PDF generation, school and division seeders, permissions, documentation, and feature tests."

Paste only the Markdown inside the block.

The first line must be:

```md
# Change Log

The final line must be:

git commit \
  -m "Add transfer cycles and principal transfer application workflow" \
  -m "Add principal self-service appointment and profile updates, admin application views, submitted PDF generation, school and division seeders, permissions, documentation, and feature tests."


## Module 07

- Added assigned Zone to users
- Added immutable origin Zone to transfer applications
- Added transfer application action history
- Added separate Zonal review records
- Added Zonal review routes and pages
- Added Zonal workflow notifications
- Added Zone-restricted backend authorization
