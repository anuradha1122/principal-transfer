
---

# `docs/12-testing-checklist.md`

```md
# Testing Checklist

## Purpose

This checklist defines the required manual and automated verification
for the Principal Transfer System.

Testing must cover:

- Authentication
- Authorization
- Data validation
- Role restrictions
- Ownership restrictions
- Organization structure
- Principal registration
- Principal profiles
- Principal appointments
- Transfer cycles
- Transfer applications
- PDF generation
- Withdrawal
- Reapplication
- Responsive user interface
- Build stability
- Regression risks

A module is not complete until relevant tests pass.

---

# Test Environment

## Project Path

```text
/Applications/MAMP/htdocs/principal-transfer

Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=principal_transfer
DB_USERNAME=root
DB_PASSWORD=root
Standard Preparation
cd /Applications/MAMP/htdocs/principal-transfer
php artisan optimize:clear
composer dump-autoload

Where a full reset is acceptable:

php artisan migrate:fresh --seed

Start Laravel:

php artisan serve

Start Vite:

npm run dev

Build production assets:

npm run build

Run all tests:

php artisan test
General Smoke Test
 Home page loads.
 Login page loads.
 No JavaScript console errors appear.
 No missing Ziggy route errors appear.
 CSS and Tailwind styles load.
 Lucide icons display.
 Admin layout loads correctly.
 Principal layout loads correctly.
 No duplicate sidebar or navbar appears.
 Inertia SSR remains disabled.
 Production build completes successfully.
 Application works after php artisan optimize:clear.
Authentication Tests
Login
 Active verified user can log in.
 Invalid credentials are rejected.
 Inactive user cannot log in.
 Unverified user is redirected to email verification where
required.
 Successful login redirects according to role.
 Logout works.
 Session ends after logout.
Password Reset
 Forgot-password page loads.
 Valid email receives reset flow.
 Invalid email does not expose account existence unsafely.
 Reset token validation works.
 Password confirmation works.
 New password allows login.
Email Verification
 Verification notice page loads.
 Signed verification link succeeds.
 Expired link is rejected.
 Invalid signature is rejected.
 Resend verification notification works.
 Host and APP_URL match the generated verification link.
 HTML-escaped &amp; is not mistakenly copied into test URLs.
Role-Based Dashboard Tests
 Super Admin reaches Admin Dashboard.
 Principal reaches Principal Dashboard.
 Zonal Director reaches Zonal Dashboard.
 Provincial Director reaches Provincial Dashboard.
 Transfer Board Member reaches Board Dashboard.
 Data Entry Officer reaches the allowed operational dashboard.
 User cannot access another role's dashboard without permission.
 Unauthorized access returns HTTP 403.
 Super Admin global access works.
User Management Tests
 Authorized admin can view users.
 Authorized admin can create user.
 Duplicate email is rejected.
 Authorized admin can edit user.
 Roles can be assigned.
 Account can be activated.
 Account can be deactivated.
 Inactive user login is blocked.
 Admin password reset works.
 User cannot delete own account through admin management.
 User cannot deactivate own account unsafely.
 Last Super Admin cannot be deleted.
 Last Super Admin cannot be deactivated.
 Unauthorized user receives HTTP 403.
Role and Permission Tests
 Role list loads.
 Role can be created.
 Role can be updated.
 Permissions can be assigned.
 System role deletion protection works.
 Custom role deletion works where safe.
 Permission list loads.
 Permission can be created where permitted.
 Permission names remain unique.
 Sidebar items respect permissions.
 Hidden frontend buttons do not replace backend authorization.
 Direct unauthorized URL access returns HTTP 403.
 Permission cache can be reset successfully.

Commands:

php artisan permission:cache-reset
php artisan db:seed --class=RolePermissionSeeder
Organization Structure Tests
Zones
 Zone list loads.
 Zone can be created.
 Duplicate code is rejected.
 Zone can be viewed.
 Zone can be edited.
 Zone can be deactivated.
 Empty Zone can be deleted.
 Zone containing Divisions cannot be deleted.
 Unauthorized user receives HTTP 403.
Divisions
 Division list loads.
 Division requires a Zone.
 Division can be created.
 Duplicate code is rejected.
 Division can be edited.
 Division can be deactivated.
 Empty Division can be deleted.
 Division containing Schools cannot be deleted.
 Zone relationship loads correctly.
Schools
 School list loads.
 School requires a Division.
 Census number is unique.
 School can be created.
 School can be edited.
 School can be viewed.
 Teaching mediums save correctly.
 Teaching mediums load as an array.
 Contact fields save.
 Population fields save.
 National-school flag saves.
 Active status saves.
 Division and Zone relationships load.
 Inactive schools remain visible in historical records.
 School referenced by historical records is not deleted unsafely.
Principal Registry Tests
Administration
 Registry list loads.
 Registry search works.
 Registration-status filter works.
 Active-status filter works.
 Registry record can be created.
 Duplicate NIC is rejected.
 Normalized duplicate NIC is rejected.
 Registry record can be edited.
 Registered Registry record cannot be deleted.
 Unregistered safe Registry record can be deleted.
 School relationship loads.
CSV Import
 Import page loads.
 Template downloads.
 Valid CSV imports records.
 Invalid NIC rows are rejected.
 Duplicate NIC rows are reported.
 Invalid School references are reported.
 Import does not partially corrupt existing data.
 Unauthorized import access returns HTTP 403.
NIC-Controlled Registration Tests
NIC Verification
 Valid old NIC format is accepted.
 Valid new NIC format is accepted.
 Lowercase v or x is normalized.
 Spaces and formatting are normalized.
 Invalid NIC format is rejected.
 NIC not in Registry is rejected.
 Inactive Registry record is rejected.
 Registered Registry record is rejected.
 Registry linked to a user is rejected.
 Verification session is created.
 Verification session expires after fifteen minutes.
Account Creation
 Verified NIC allows registration page.
 Missing NIC verification blocks registration.
 Expired verification blocks registration.
 User account is created.
 Principal role is assigned.
 Registry links to created user.
 Registry status becomes registered.
 Registration timestamp is recorded.
 Principal Profile is created or linked.
 Duplicate account creation is prevented.
 Email verification notification is triggered.
 Transaction rollback works if account creation fails.
Principal Profile Tests
Admin Profile Management
 Profile list loads.
 Search works.
 Filters work.
 Profile detail page loads.
 User relationship loads.
 Registry relationship loads.
 Appointments load.
 Current appointment loads.
 Duplicate NIC is rejected.
 Duplicate employee number is rejected when provided.
 Unauthorized user receives HTTP 403.
Principal Self-Service
 Principal can view own profile.
 Principal cannot view another profile.
 Principal can open Edit Profile.
 Principal can update full name.
 Principal can update name with initials.
 Principal can update employee number.
 Principal can update gender.
 Principal can update date of birth.
 Principal can update mobile number.
 Principal can update alternate number.
 Principal can update personal email.
 Principal can update address.
 Principal can update service category.
 Arbitrary service-category string saves without ENUM truncation.
 Principal can update service grade.
 Principal can update employment status.
 Principal can update service dates.
 Principal can update qualifications.
 Principal can update notes.
 Principal can mark profile complete.
 Principal cannot change NIC.
 Modified request cannot bypass NIC protection.
 Profile page uses safe option arrays.
 Missing options prop does not cause .map() errors.
Principal Appointment Tests
Admin Appointment Management
 Admin can create appointment.
 Admin can edit appointment.
 Admin can delete permitted appointment.
 School relationship saves.
 Designation saves.
 Appointment type saves.
 Appointment number saves.
 Appointment date saves.
 Start date saves.
 End date saves.
 Remarks save.
 Current status saves.
Principal Self-Service Appointment Management
 Principal can open Create Appointment.
 Principal can create own appointment.
 Principal can edit own appointment.
 Principal cannot edit another Principal's appointment.
 Principal can delete permitted own appointment.
 Principal cannot delete another Principal's appointment.
 Ownership check returns HTTP 403 or 404 as designed.
Zone and School Selection
 Zone dropdown loads.
 School dropdown is disabled until Zone is selected.
 Selecting Zone filters Schools correctly.
 Changing Zone clears selected School.
 Existing appointment preselects the correct Zone.
 Existing School remains selected when editing.
 Empty Zone displays a useful message.
 Missing relationship data does not crash React.
Appointment Date Rules
 Selecting Appointment Date sets Start Date.
 Start Date is read-only in UI.
 Backend requires Start Date.
 Backend requires Start Date to match Appointment Date.
 Modified browser request with different Start Date is rejected.
 End Date cannot be before Start Date.
Current Appointment Rules
 New current appointment becomes current.
 Previous current appointment becomes non-current.
 Previous end date becomes one day before new start date.
 Current appointment end date is cleared.
 Only one current appointment remains.
 Transaction rollback prevents partial closure.
 Appointment history remains visible.
Transfer Cycle Tests
Admin CRUD
 Transfer Cycle list loads.
 Transfer Cycle can be created.
 Code is unique.
 Transfer year validates.
 Transfer type validates.
 Open date validates.
 Close date validates.
 Close date cannot precede open date.
 Effective date validates.
 Minimum service years validate.
 Maximum preference count validates.
 Status saves.
 Published status saves.
 Cycle can be viewed.
 Cycle can be edited.
 Safe Cycle can be deleted.
 Cycle with applications cannot be deleted unsafely.
 Unauthorized user receives HTTP 403.
Availability
 Published open Cycle appears to Principal.
 Unpublished Cycle does not appear.
 Future Cycle does not allow application.
 Closed Cycle does not allow application.
 Opening boundary date works.
 Closing boundary date works.
Transfer Application Tests
Application Index
 Principal Application Index loads.
 Only own applications are displayed.
 Open Cycles are displayed.
 Existing active application hides or blocks duplicate start action.
 Withdrawn application remains in history.
 Eye button opens Show page.
 Edit button appears only for Draft.
 PDF button appears only after submission.
 Pagination works.
 Empty state displays correctly.
 Status badge displays correct status.
Application Creation
 Principal with Profile can open Create page.
 Principal without Profile is blocked appropriately.
 Principal without current appointment is redirected with warning.
 Inactive Principal is blocked.
 Closed Cycle is rejected.
 Unpublished Cycle is rejected.
 Minimum service requirement is enforced.
 Existing active application blocks another application.
 Withdrawn application does not block a new application.
 Current appointment snapshot is captured.
 Current school snapshot is captured.
 Current designation snapshot is captured.
 Service duration is calculated.
 Draft saves successfully.
Transfer Reason
 Transfer reason is required.
 Detailed explanation validates.
 Medical-reason flag saves.
 Spouse-employment flag saves.
 Mutual-transfer flag saves.
 Mutual Principal NIC is required when mutual transfer is selected.
 Mutual Principal NIC cannot equal applicant NIC.
 Principal remarks save.
School Preferences
 At least one preference is required before submission.
 Preference order saves.
 Duplicate school preferences are rejected.
 Current School cannot be selected.
 Maximum preference count is enforced.
 School relationship loads.
 Division relationship loads.
 Zone relationship loads.
 Preference reason saves.
 Preferences remain ordered.
 Editing Draft updates preferences correctly.
 Removing a preference works in Draft.
 Submitted preferences cannot be edited.
Draft Actions
 Owner can view Draft.
 Owner can edit Draft.
 Owner can update Draft.
 Owner can delete Draft.
 Another Principal cannot view Draft.
 Another Principal cannot edit Draft.
 Another Principal cannot delete Draft.
 Submitted application cannot be edited.
 Submitted application cannot be deleted.
Transfer Application Submission Tests
 Draft may be submitted.
 Non-Draft submission is rejected.
 Ownership is verified.
 Open Cycle is revalidated.
 Eligibility is revalidated.
 At least one preference is required.
 Declaration must be accepted.
 Unique application number is generated.
 Status becomes Submitted.
 Submitted timestamp is recorded.
 Declaration flag is stored.
 Application becomes locked.
 Preferences become locked.
 Success message appears.
 Principal is redirected to Show page.
 Transaction prevents partial submission.
PDF Tests
Generation
 PDF generates after successful submission.
 PDF path is stored.
 PDF generation timestamp is stored.
 PDF file exists on local private disk.
 PDF is not stored publicly.
 PDF contains application number.
 PDF contains Principal information.
 PDF contains current appointment snapshot.
 PDF contains transfer reason.
 PDF contains preferences.
 PDF contains declaration.
 PDF opens successfully.
 PDF uses A4 format.
Principal Access
 Principal can download own submitted PDF from Index.
 Principal can download own submitted PDF from Show.
 Principal cannot download another Principal's PDF.
 Draft does not show PDF button.
 Draft PDF route is rejected or redirected.
 Download response uses PDF content type.
 Download filename is correct.
Admin Access
 Authorized Admin can download submitted PDF.
 Unauthorized Admin receives HTTP 403.
 Admin Show page displays PDF button after submission.
 Draft Admin page does not show official PDF button.
Regeneration and Failure
 Missing PDF file is regenerated.
 Missing path is regenerated.
 PDF-generation failure is logged.
 PDF-generation failure does not reverse submission.
 Warning is displayed when generation fails.
 Later download can regenerate PDF.
Withdrawal Tests
 Eligible Submitted application can be withdrawn.
 Eligible Zonal Review application can be withdrawn where allowed.
 Draft cannot use withdrawal action.
 Unauthorized status cannot be withdrawn.
 Withdrawal reason is required.
 Minimum reason length is enforced.
 Status becomes Withdrawn.
 Withdrawal timestamp is recorded.
 Withdrawal reason is stored.
 Owner can still view application.
 Owner can still download submitted PDF.
 Preferences remain.
 Application number remains.
 Withdrawn application cannot be edited.
 Withdrawn application cannot be submitted again.
 Withdrawn application cannot be deleted.
 Another Principal cannot withdraw the application.
Reapplication Tests
 Withdrawn application remains in history.
 Principal may create a new application in same Cycle.
 New application is a separate database record.
 New application captures a fresh appointment snapshot.
 New preferences are separate.
 Previous preferences remain unchanged.
 New submission receives a new application number.
 New submission generates a separate PDF.
 Previous PDF remains available.
 Only one new active application is allowed.
 Cancelled application permits reapplication where implemented.
Admin Transfer Application Tests
 Admin list loads.
 Search works.
 Status filter works.
 Cycle filter works.
 Zone filter works where implemented.
 Admin Show page loads.
 Principal relationship loads.
 Current School relationship loads.
 Preferences load.
 Submitted timestamp displays.
 Withdrawn timestamp displays.
 Withdrawal reason displays.
 PDF button displays for submitted records.
 Unauthorized access returns HTTP 403.
 Admin cannot alter submitted applicant data through unsupported
routes.
Planned Zonal Review Tests
Access Scope
 Zonal Director can access assigned Zone application.
 Zonal Director cannot access another Zone application.
 Direct URL manipulation does not bypass Zone restriction.
 Super Admin can access all Zones.
 Zonal filters do not expand authorized scope.
Review Actions
 Submitted application may enter Zonal Review.
 Invalid status cannot enter Zonal Review.
 Reviewer identity is recorded.
 Review timestamp is recorded.
 Remarks validate.
 Zonal approval changes status correctly.
 Zonal rejection changes status correctly.
 Rejection reason is required.
 Original submission remains unchanged.
 Submitted PDF remains available.
 Status history is created where implemented.
 Notification is sent where implemented.
Security Tests
 All protected routes require authentication.
 Verified middleware works.
 Principal routes require Principal role.
 Ownership checks exist for Profile.
 Ownership checks exist for Appointments.
 Ownership checks exist for Transfer Applications.
 Ownership checks exist for PDFs.
 Admin routes require permissions.
 File paths are not exposed.
 Private PDF cannot be accessed through public storage URL.
 Mass assignment does not allow protected fields.
 NIC cannot be changed through modified request.
 Status cannot be changed through general update request.
 Application number cannot be supplied by Principal.
 Submitted timestamp cannot be supplied by Principal.
 PDF path cannot be supplied by Principal.
 SQL injection attempts are handled by query builder.
 Invalid foreign keys are rejected.
 CSRF protection works.
 Unauthorized actions return HTTP 403.
React and UI Tests
General
 No undefined.map errors occur.
 Optional relationships use optional chaining.
 Empty arrays use safe defaults.
 Form processing state disables buttons.
 Validation errors display under fields.
 Confirmation dialogs appear for destructive actions.
 Download links use normal anchors.
 Page navigation uses Inertia Link or router.
 Eye View action is clickable.
 Browser Back navigation works.
 No duplicate action buttons appear unintentionally.
Responsive Layout

Test at:

 Mobile width
 Tablet width
 Laptop width
 Desktop width

Verify:

 Sidebar remains usable.
 Tables scroll horizontally.
 Buttons wrap correctly.
 Forms use one column on mobile.
 Headers do not overlap.
 Status badges remain visible.
 PDF buttons remain accessible.
 Dropdowns fit viewport.
Route Tests

Run:

php artisan route:list

Verify:

 Admin route names exist.
 Principal route names exist.
 Transfer Cycle routes exist.
 Transfer Application routes exist.
 PDF routes exist.
 PDF routes appear before general Show routes.
 No duplicate route names exist.
 No unintended /register route exists.
 Route model binding parameter names match controller arguments.

Specific checks:

php artisan route:list --name=principal.transfer-applications
php artisan route:list --name=admin.transfer-applications
php artisan route:list --name=transfer-applications.pdf
Database Tests
 All migrations run successfully.
 All rollbacks run where supported.
 Foreign keys exist.
 Index names do not exceed MySQL limits.
 Duplicate migrations do not exist.
 Transfer application unique constraint supports withdrawal
reapplication.
 service_category is a nullable string.
 PDF fields exist.
 Seeders use actual column names.
 Seeders respect foreign key order.
 migrate:fresh --seed completes successfully.

Commands:

php artisan migrate:status
php artisan migrate:fresh --seed

Use migrate:fresh only where destroying local data is acceptable.

Seeder Tests
 RolePermissionSeeder completes.
 ZoneSeeder creates seven Zones.
 DivisionSeeder creates valid Divisions.
 SchoolSeeder creates valid Schools.
 DatabaseSeeder calls seeders in correct order.
 Seeders can run more than once safely where designed.
 Seed data matches actual enum or string constraints.
 No data-truncation warnings occur.
Automated Test Files

Current or expected Feature Tests include:

tests/Feature/Admin/PrincipalProfileManagementTest.php
tests/Feature/Admin/TransferCycleManagementTest.php
tests/Feature/Principal/PrincipalSelfProfileTest.php
tests/Feature/Principal/TransferApplicationManagementTest.php

Future tests should include:

tests/Feature/Zonal/ZonalTransferReviewTest.php
tests/Feature/Provincial/ProvincialTransferReviewTest.php
tests/Feature/TransferBoard/TransferBoardDecisionTest.php
tests/Feature/Exports/TransferApplicationPdfTest.php
Final Module Verification

Before committing a module, run:

php artisan optimize:clear
php artisan migrate:status
php artisan test
npm run build

Where a full local reset is safe:

php artisan migrate:fresh --seed
php artisan test
npm run build

Check Git changes:

git status
git diff --cached

Commit only after:

 Migrations succeed.
 Seeders succeed.
 Tests pass.
 Frontend build passes.
 Manual workflow passes.
 Documentation is updated.
 No debug output remains.
 No accidental duplicate file remains.
 No private .env file is staged.
 No generated PDF is staged.
 No node_modules or vendor files are staged.
Module 06 Completion Checklist
 Transfer Cycle CRUD complete.
 Transfer Cycle permissions complete.
 Principal Draft creation complete.
 Draft editing complete.
 Draft deletion complete.
 Eligibility validation complete.
 Current appointment snapshot complete.
 Preference management complete.
 Submission complete.
 Application number generation complete.
 Submitted PDF generation complete.
 Private PDF storage complete.
 Principal PDF download complete.
 Admin PDF download complete.
 Withdrawal complete.
 Reapplication after withdrawal complete.
 Admin list and Show pages complete.
 Principal Index and Show pages complete.
 Automated tests complete.
 Documentation updated.
 Git commit created.
Next Exact Testing Step
Module 07:
Test Zonal Director zone-restricted application review,
recommendation, approval, rejection, audit history, and notifications.

For all three documents, paste only the Markdown content inside each block. The outer code fences are chat packaging, not content, because apparently even documentation needs customs control.



- Assigned-Zone application visible
- Other-Zone application hidden
- Direct other-Zone URL returns 403
- Submitted application enters Zonal Review
- Invalid status transition blocked
- Approval records reviewer and timestamp
- Rejection requires reason
- Action history recorded
- Notification dispatched
- PDF access restricted by Zone
- Principal sees updated status
- Submitted snapshot unchanged
