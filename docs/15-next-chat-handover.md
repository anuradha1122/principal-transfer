
# Next Chat Handover

## Purpose of This Document

This document is the complete continuation handover for the
Principal Transfer System.

Use this document in the next chat so development can continue without
changing the existing coding pattern, module-delivery structure,
frontend design style, business rules, authorization approach, or
documentation workflow.

The next chat must treat this document as the primary continuation
reference.

---

# Project

## Project Name

Principal Transfer System

## Organization

Provincial Department of Education  
Sabaragamuwa Province, Sri Lanka

## Purpose

The system manages Principal transfer applications from
NIC-controlled registration through:

- Principal profile completion
- Current appointment management
- Transfer-cycle application
- Zonal review
- Provincial review
- Transfer Board decision
- Final result tracking
- PDF documents
- Reports
- Audit history

The system is limited to the Sabaragamuwa Province.

---

# Local Development Environment

## Project Path

```bash
/Applications/MAMP/htdocs/principal-transfer
````

Always begin module work with:

```bash
cd /Applications/MAMP/htdocs/principal-transfer
```

## Database

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=principal_transfer
DB_USERNAME=root
DB_PASSWORD=root
```

## Local Server

Laravel:

```bash
php artisan serve
```

Vite:

```bash
npm run dev
```

Production frontend build:

```bash
npm run build
```

## Platform

* macOS
* MAMP
* PHP
* MySQL
* VS Code
* Git

---

# Technology Stack

## Backend

* Laravel
* Eloquent ORM
* Form Requests
* Service classes
* Policies, Gates, middleware, and controller authorization
* Database transactions
* Laravel Notifications
* Laravel Feature Tests

## Frontend

* React
* Inertia.js
* Tailwind CSS
* Lucide React
* Ziggy routes

## Authentication

* Laravel Breeze
* Email verification
* NIC-controlled Principal registration

## Authorization

* Spatie Laravel Permission
* Global Super Admin access rule
* Role middleware
* Permission middleware
* Ownership checks
* Future Zone-based restrictions

## Database

* MySQL

## Documents

* DomPDF
* Laravel Excel

## Important Frontend Rule

Do not enable Inertia SSR.

Inertia SSR must remain disabled.

---

# Main System Roles

## Super Admin

Can access and manage all system modules.

## Principal

Can:

* Register through NIC verification
* Verify email
* Maintain own profile
* Maintain own appointment history
* Create transfer applications
* Edit Draft applications
* Submit applications
* Download submitted PDFs
* Withdraw applications where permitted
* Reapply after withdrawal
* Track final decisions

## Zonal Director

Will:

* Access only applications originating from the assigned Zone
* Review submitted applications
* Record recommendations
* Approve or reject at Zonal level
* Download submitted PDFs
* Record remarks and review history

## Provincial Director

Will:

* Review Zonal-approved applications
* Approve or reject at Provincial level
* Forward approved applications to the Transfer Board
* Record remarks and history

## Transfer Board Member

Will:

* Review Board-stage applications
* Record final approval, rejection, or waitlist decision
* Record approved School and effective date
* Generate final decision documents

## Data Entry Officer

Can maintain permitted master data such as:

* Zones
* Divisions
* Schools
* Principal Registry
* Principal profiles
* Appointment records

---

# Administrative Structure

The hierarchy is:

```text
Sabaragamuwa Province
    ↓
District
    ↓
Zone
    ↓
Division
    ↓
School
```

The system contains seven education Zones within:

* Ratnapura District
* Kegalle District

Relationships:

* Zone has many Divisions.
* Division belongs to Zone.
* Division has many Schools.
* School belongs to Division.
* School belongs to Zone through Division.

---

# Completed Modules

## Module 01

### Project Foundation, Authentication, Roles and Admin Layout

Status:

```text
Completed
```

Implemented:

* Laravel project foundation
* React with Inertia.js
* MySQL configuration
* Laravel Breeze authentication
* Login
* Logout
* Password reset
* Email verification
* Spatie Laravel Permission
* Six system roles
* Super Admin seeder
* Role-based dashboards
* AdminLayout
* Admin sidebar
* Admin topbar
* Shared frontend permissions
* Global Super Admin access rule
* Initial tests
* Initial documentation
* Ordinary public registration disabled
* Inertia SSR disabled

---

## Module 02

### Roles, Permissions and User Management

Status:

```text
Completed
```

Implemented:

* User list
* User search and filters
* User creation
* User editing
* Role assignment
* Account activation
* Account deactivation
* Admin password reset
* Email-verification management
* Last-login tracking
* Created-by tracking
* Updated-by tracking
* Role management
* Permission assignment
* Permission management
* Last Super Admin protection
* Self-deactivation protection
* Self-deletion protection
* Inactive-login prevention
* Feature tests

---

## Module 03

### Zones, Divisions and Schools Management

Status:

```text
Completed
```

Implemented:

* Zone management
* Division management
* School management
* Seven-Zone seeder
* DivisionSeeder
* SchoolSeeder
* Search and filters
* Active and inactive statuses
* School census number
* School type
* School level
* Gender type
* Teaching mediums
* School contact information
* Student count
* Teacher count
* National-School flag
* Parent deletion protection
* Sidebar navigation
* Feature tests

Important rules:

* Zone containing Divisions cannot be deleted.
* Division containing Schools cannot be deleted.
* School census number must be unique.
* Inactive records remain available for history.

---

## Module 04

### Principal Registry and NIC-Controlled Self-Registration

Status:

```text
Completed
```

Implemented:

* Principal Registry
* NIC normalization
* Old NIC validation
* New NIC validation
* Registry administration
* Registry search and filters
* CSV import
* CSV template
* NIC verification page
* Time-limited NIC verification session
* Principal account creation
* Principal role assignment
* Principal Profile creation
* Registry-to-user linking
* Registry-to-profile linking
* Duplicate-registration prevention
* Email-verification redirect
* Registration tests

Important rules:

* Ordinary Breeze registration remains disabled.
* Principal registration works only through NIC verification.
* Registry record must be active and unregistered.
* Registered NIC numbers cannot be reused.
* NIC verification session expires after fifteen minutes.
* Registered Registry records cannot be deleted.

---

## Module 05

### Principal Profile and Current Appointment Management

Status:

```text
Completed
```

Implemented:

* PrincipalProfile model and database
* PrincipalAppointment model and database
* Admin Profile list
* Admin Profile create
* Admin Profile edit
* Admin Profile show
* Principal self-service Profile show
* Principal self-service Profile edit
* Principal self-service Profile update
* Personal information
* Contact information
* Address information
* Service information
* Employment status
* Qualifications
* Notes
* Profile completion
* Appointment history
* Current appointment management
* Automatic previous-appointment closure
* Admin appointment create
* Admin appointment edit
* Admin appointment delete
* Principal appointment create
* Principal appointment edit
* Principal appointment delete
* Ownership checks
* Profile and appointment tests

Important current rules:

* Principal may update personal, contact, service, employment, and
  appointment information.
* Principal cannot change NIC.
* NIC remains controlled by the Principal Registry.
* Only one appointment should be current.
* A new current appointment closes the previous current appointment.
* Previous end date becomes one day before the new appointment date.

Later improvements added during Module 06:

* Zone must be selected before School.
* School dropdown filters by selected Zone.
* Appointment Date automatically becomes Start Date.
* Start Date is read-only in the interface.
* Backend validates that Start Date matches Appointment Date.
* `service_category` changed from restrictive ENUM behavior to a
  nullable string.

---

## Module 06

### Transfer Cycles and Transfer Application Management

Status:

```text
Completed
```

Implemented:

### Transfer Cycles

* Transfer Cycle CRUD
* Cycle name
* Cycle code
* Transfer type
* Transfer year
* Application opening date
* Application closing date
* Effective date
* Minimum service requirement
* Maximum School preference count
* Published and open-cycle logic
* Admin Cycle pages
* Transfer Cycle tests

### Transfer Applications

* TransferApplication model
* TransferPreference model
* Principal Transfer Application controller
* Admin Transfer Application controller
* Draft creation
* Draft editing
* Draft deletion
* Transfer reason
* Detailed explanation
* Medical reason flag
* Spouse-employment reason flag
* Mutual-transfer flag
* Mutual Principal NIC
* Principal remarks
* Ranked School preferences
* Current appointment snapshot
* Current School snapshot
* Eligibility validation
* Final declaration
* Submission
* Unique application-number generation
* Application locking
* Principal application history
* Admin application list
* Admin application show
* Automated tests

### Eligibility Rules

A Principal must:

* Have the Principal role
* Have a Principal Profile
* Have Active employment status
* Have a current appointment
* Have a current School
* Apply during a published and open Cycle
* Meet the Cycle minimum-service requirement
* Have no other active application in the same Cycle

### School Preference Rules

* Current School cannot be selected.
* Duplicate Schools are not allowed.
* Preference order is preserved.
* Maximum count is controlled by the Cycle.

### Submitted PDF

Implemented:

* `TransferApplicationPdfService`
* DomPDF submitted-application view
* Private PDF storage
* PDF path field
* PDF-generation timestamp field
* PDF generation after submission
* PDF regeneration when missing
* Principal PDF download from Index
* Principal PDF download from Show
* Admin PDF download from Show
* PDF remains available after withdrawal
* PDF generation failure does not reverse submission

### Withdrawal

Implemented:

* Withdrawal action
* Withdrawal reason
* Withdrawal timestamp
* Status becomes Withdrawn
* Application history remains
* Preferences remain
* Application number remains
* Submitted PDF remains
* Withdrawn application cannot be edited
* Withdrawn application cannot be resubmitted directly

### Reapplication

Implemented:

* Only one active application is allowed per Principal per Cycle.
* Withdrawn applications do not block a new application.
* Cancelled applications may be treated as inactive.
* Reapplication creates a new record.
* Reapplication captures a fresh snapshot.
* Reapplication receives a new application number.
* Reapplication generates a separate PDF.
* Previous withdrawn history remains unchanged.

---

# Current Database Models

Main current models include:

```text
App\Models\User
App\Models\Zone
App\Models\Division
App\Models\School
App\Models\PrincipalRegistry
App\Models\PrincipalProfile
App\Models\PrincipalAppointment
App\Models\TransferCycle
App\Models\TransferApplication
App\Models\TransferPreference
```

Spatie models include:

```text
Spatie\Permission\Models\Role
Spatie\Permission\Models\Permission
```

---

# Current Main Controllers

## Admin

```text
App\Http\Controllers\Admin\UserController
App\Http\Controllers\Admin\RoleController
App\Http\Controllers\Admin\PermissionController
App\Http\Controllers\Admin\ZoneController
App\Http\Controllers\Admin\DivisionController
App\Http\Controllers\Admin\SchoolController
App\Http\Controllers\Admin\PrincipalRegistryController
App\Http\Controllers\Admin\PrincipalProfileController
App\Http\Controllers\Admin\PrincipalAppointmentController
App\Http\Controllers\Admin\TransferCycleController
App\Http\Controllers\Admin\TransferApplicationController
```

## Principal

```text
App\Http\Controllers\Principal\ProfileController
App\Http\Controllers\Principal\AppointmentController
App\Http\Controllers\Principal\TransferApplicationController
```

## Authentication

```text
App\Http\Controllers\Auth\PrincipalRegistrationController
```

---

# Current Main Form Requests

## Admin

```text
App\Http\Requests\Admin\StorePrincipalAppointmentRequest
App\Http\Requests\Admin\UpdatePrincipalAppointmentRequest
App\Http\Requests\Admin\StoreTransferCycleRequest
App\Http\Requests\Admin\UpdateTransferCycleRequest
```

## Principal

```text
App\Http\Requests\Principal\UpdateOwnProfileRequest
App\Http\Requests\Principal\StoreOwnAppointmentRequest
App\Http\Requests\Principal\UpdateOwnAppointmentRequest
App\Http\Requests\Principal\StoreTransferApplicationRequest
App\Http\Requests\Principal\UpdateTransferApplicationRequest
App\Http\Requests\Principal\SubmitTransferApplicationRequest
```

---

# Current Service Classes

```text
App\Services\TransferApplicationPdfService
```

Earlier registration work also uses NIC normalization logic.

---

# Current React Page Structure

## Layouts

```text
resources/js/Layouts/AdminLayout.jsx
```

AdminLayout is role-aware.

It supports:

* Admin navigation
* Principal navigation
* Page title
* Header section
* Responsive content
* Sidebar behavior

## Admin Sidebar

```text
resources/js/Components/Admin/AdminSidebar.jsx
```

## Principal Sidebar

```text
resources/js/Components/Principal/PrincipalSidebar.jsx
```

## Principal Profile Pages

```text
resources/js/Pages/Principal/Profile/Show.jsx
resources/js/Pages/Principal/Profile/Edit.jsx
```

## Principal Appointment Pages

```text
resources/js/Pages/Principal/Appointments/AppointmentForm.jsx
resources/js/Pages/Principal/Appointments/Create.jsx
resources/js/Pages/Principal/Appointments/Edit.jsx
```

## Admin Appointment Pages

```text
resources/js/Pages/Admin/PrincipalAppointments/AppointmentForm.jsx
resources/js/Pages/Admin/PrincipalAppointments/Create.jsx
resources/js/Pages/Admin/PrincipalAppointments/Edit.jsx
```

## Transfer Cycle Pages

```text
resources/js/Pages/Admin/TransferCycles/Index.jsx
resources/js/Pages/Admin/TransferCycles/Create.jsx
resources/js/Pages/Admin/TransferCycles/Edit.jsx
resources/js/Pages/Admin/TransferCycles/Show.jsx
resources/js/Pages/Admin/TransferCycles/CycleForm.jsx
```

## Principal Transfer Application Pages

```text
resources/js/Pages/Principal/TransferApplications/Index.jsx
resources/js/Pages/Principal/TransferApplications/Create.jsx
resources/js/Pages/Principal/TransferApplications/Edit.jsx
resources/js/Pages/Principal/TransferApplications/Show.jsx
resources/js/Pages/Principal/TransferApplications/ApplicationForm.jsx
```

## Admin Transfer Application Pages

```text
resources/js/Pages/Admin/TransferApplications/Index.jsx
resources/js/Pages/Admin/TransferApplications/Show.jsx
```

## PDF View

```text
resources/views/pdf/transfer-applications/submitted.blade.php
```

---

# Frontend Design Pattern

The next chat must continue the existing frontend style.

Do not suddenly introduce a completely different visual design.

## General Visual Style

Use:

* Clean administrative interface
* White cards
* Slate backgrounds
* Soft borders
* Rounded corners
* Subtle shadows
* Responsive spacing
* Clear page titles
* Small supporting descriptions
* Lucide icons
* Consistent button sizes
* Consistent status badges

Common card style:

```jsx
className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
```

Common page background:

```jsx
className="space-y-6"
```

Common primary button:

```jsx
className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
```

Common success button:

```jsx
className="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
```

Common danger button:

```jsx
className="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
```

Common secondary button:

```jsx
className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
```

Common icon-only action button:

```jsx
className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
```

## Page Header Pattern

Use:

```jsx
<header className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 className="text-2xl font-bold text-slate-900">
            Page Title
        </h1>

        <p className="mt-1 text-sm text-slate-500">
            Supporting description
        </p>
    </div>

    <div className="flex flex-wrap items-center gap-3">
        {/* Actions */}
    </div>
</header>
```

## Table Pattern

Tables should use:

* Responsive horizontal scroll
* `min-w-full`
* White background
* Slate header
* Comfortable row padding
* Visible action buttons
* Empty state
* Pagination

Recommended wrapper:

```jsx
<div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-slate-200">
            {/* Table */}
        </table>
    </div>
</div>
```

## Forms

Forms should use:

* Shared form components
* Two-column desktop layout
* One-column mobile layout
* `InputLabel`
* `TextInput`
* `InputError`
* Select inputs
* Textareas
* Clear required markers
* Disabled processing state
* Backend error display

Recommended grid:

```jsx
<div className="grid gap-5 md:grid-cols-2">
```

## Show Pages

Show pages should use:

* Header with status badge
* Main information card
* Side timeline card
* Separate cards for:

  * Applicant information
  * Appointment information
  * Preferences
  * Review information
  * PDF download
  * Status history
* Action buttons in header
* Safe nullable relationship display

## Status Badges

Use a shared status-style helper.

Current display statuses:

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
Approved
Rejected
Waitlisted
Withdrawn
Cancelled
```

Suggested colors:

* Draft: Slate
* Submitted: Blue
* Zonal Review: Amber
* Zonal Approved: Emerald
* Zonal Rejected: Red
* Provincial Review: Amber
* Provincial Approved: Emerald
* Provincial Rejected: Red
* Board Review: Violet
* Approved: Emerald
* Rejected: Red
* Waitlisted: Orange
* Withdrawn: Slate
* Cancelled: Red

## React Safety Rules

Always:

* Use safe prop defaults.
* Use optional chaining.
* Use fallback arrays.
* Avoid direct `.map()` on possibly undefined data.
* Use `application?.relation?.field`.
* Use `(items ?? []).map(...)`.
* Use `route()` names exactly.
* Use Inertia `Link` for page navigation.
* Use normal `<a>` for binary PDF downloads.
* Disable buttons during processing.
* Show validation errors.
* Use confirmation dialogs for destructive actions.
* Avoid duplicate action buttons unless intentional.

---

# Current Principal Transfer Application UI Behavior

## Index Page

Contains:

* Page title
* Available Transfer Cycles
* Application history
* View action
* Edit action for Draft
* PDF button for submitted applications
* Status badge
* Pagination
* Empty state

Important:

* Keep the PDF button on the Index page.
* Keep the PDF button on the Show page.
* Both locations should allow download.

## Show Page

Contains:

* Application number
* Cycle name
* Status badge
* Edit Draft button
* Delete Draft button
* PDF button
* Applicant and transfer details
* Timeline
* School preferences
* Final submission form for Draft
* Withdrawal form where permitted
* Withdrawal reason display
* Submitted PDF card

## Download Rule

Use:

```jsx
<a
    href={route(
        'principal.transfer-applications.pdf',
        application.id,
    )}
>
    Download PDF
</a>
```

Do not use Inertia Link for PDF binary downloads.

---

# Current Transfer Application Business Rules

## Active Application Rule

A Principal may have only one active application per Cycle.

Active statuses include:

```text
Draft
Submitted
Zonal Review
Zonal Approved
Provincial Review
Provincial Approved
Board Review
Waitlisted
Approved
```

Inactive statuses include at least:

```text
Withdrawn
Cancelled
```

A withdrawn application does not prevent creating a new application in
the same Cycle.

## Draft

Draft may be:

* Viewed
* Edited
* Deleted
* Submitted

Draft has no official submitted PDF.

## Submitted

Submitted application:

* Receives a unique application number
* Records submitted timestamp
* Becomes read-only
* Locks preferences
* Generates a private PDF
* Can be withdrawn where permitted
* Cannot be deleted

## Withdrawal

Withdrawal:

* Requires a reason
* Records timestamp
* Preserves application history
* Preserves preferences
* Preserves application number
* Preserves PDF
* Allows a new application in the same Cycle

## Snapshot

Transfer Application stores a snapshot of:

* Principal name
* NIC
* Employee number
* Service grade
* Current designation
* Current School
* Current appointment
* Current appointment start date
* Service duration

Later Profile or Appointment edits must not rewrite the submitted
snapshot.

---

# Current PDF Rules

* DomPDF is used.
* PDF files are stored privately.
* PDF files must not be exposed through public storage URLs.
* Principal may download only their own PDF.
* Admin access requires permission.
* Future Zonal access must be Zone restricted.
* PDF remains available after withdrawal.
* Missing PDF may be regenerated.
* PDF generation failure must not reverse successful submission.

Service:

```text
app/Services/TransferApplicationPdfService.php
```

View:

```text
resources/views/pdf/transfer-applications/submitted.blade.php
```

---

# Current Authorization Rules

## General

* All protected routes require authentication.
* Relevant routes require verified email.
* Principal routes require Principal role.
* Admin actions require permissions.
* Ownership checks are mandatory.
* Frontend checks are not enough.
* Unauthorized access returns HTTP 403.
* Super Admin uses global access rule.

## Principal

Principal may access only:

* Own Profile
* Own Appointments
* Own Transfer Applications
* Own PDFs

## Zonal Director

Module 07 must enforce:

* Assigned-Zone access only
* No access to another Zone
* Backend query restrictions
* Backend authorization
* Direct URL protection

---

# Current Route Groups

## Principal

Current Principal routes include:

```text
principal.dashboard
principal.profile.show
principal.profile.edit
principal.profile.update
principal.appointments.create
principal.appointments.store
principal.appointments.edit
principal.appointments.update
principal.appointments.destroy
principal.transfer-applications.index
principal.transfer-applications.create
principal.transfer-applications.store
principal.transfer-applications.edit
principal.transfer-applications.update
principal.transfer-applications.submit
principal.transfer-applications.withdraw
principal.transfer-applications.destroy
principal.transfer-applications.pdf
principal.transfer-applications.show
```

## Admin

Current Admin routes include:

```text
admin.dashboard
admin.users.*
admin.roles.*
admin.permissions.*
admin.zones.*
admin.divisions.*
admin.schools.*
admin.principal-registry.*
admin.principal-profiles.*
admin.principal-profiles.appointments.*
admin.principal-appointments.*
admin.transfer-cycles.*
admin.transfer-applications.index
admin.transfer-applications.show
admin.transfer-applications.pdf
```

Important route-order rule:

```text
PDF route must appear before general Show route.
```

Example:

```php
Route::get(
    '/transfer-applications/{transferApplication}/pdf',
    ...
);

Route::get(
    '/transfer-applications/{transferApplication}',
    ...
);
```

---

# Current Documentation Files

The following documentation files now exist or are being updated:

```text
docs/01-project-overview.md
docs/02-system-scope.md
docs/03-user-roles.md
docs/04-database-design.md
docs/05-module-structure.md
docs/06-routes.md
docs/07-permissions.md
docs/08-business-rules.md
docs/09-email-notifications.md
docs/10-pdf-excel-exports.md
docs/11-status-workflow.md
docs/12-testing-checklist.md
docs/13-development-log.md
docs/14-change-log.md
docs/15-next-chat-handover.md
```

The next chat must update all relevant documents after each module.

---

# Mandatory Module Delivery Pattern

Every future module must follow this exact structure.

## 1. Module Title

Example:

```text
Module 07: Zonal Director Transfer Review and Recommendation
```

## 2. Module Purpose

Explain:

* What the module does
* Who uses it
* How it connects to previous modules

## 3. Go to Project Folder

```bash
cd /Applications/MAMP/htdocs/principal-transfer
```

## 4. Package Installation

Only if needed.

Do not reinstall packages already present.

## 5. File Creation Commands

Provide terminal commands for:

* Migration
* Model
* Form Requests
* Controller
* Policy
* Service
* Notification
* Export
* Seeder
* Tests
* React pages
* Blade PDF views

## 6. Migration

Provide full migration code.

Include:

* Foreign keys
* Indexes
* Nullable rules
* Rollback
* Short index names
* Safe historical behavior

Prefer new migrations instead of modifying old deployed migrations.

## 7. Model

Provide full model code.

Include:

* Fillable
* Casts
* Relationships
* Scopes
* Status constants
* Helper methods

## 8. Relationships

Explain all relationships.

## 9. Form Requests

Provide full request classes.

Include validation and authorization.

## 10. Controller

Provide full controller code.

Include:

* Authorization
* Transactions
* Ownership
* Zone restriction
* Filters
* Pagination
* Relationship loading
* Redirects
* Flash messages

## 11. Policies and Authorization

Provide backend authorization.

Do not rely only on frontend visibility.

## 12. Services

Use services for reusable business logic.

## 13. Notifications

Add notifications in correct workflow locations.

Email failure must not reverse a successful transaction.

## 14. PDF and Excel

Add exports where required.

Sensitive files must remain private.

## 15. Seeders

Provide full Seeder code where needed.

## 16. Routes

Provide full route code.

Use correct middleware and route names.

## 17. Sidebar Update

Update the correct role sidebar.

## 18. React Pages

Provide complete copy-paste pages.

Do not provide incomplete fragments when full code is requested.

## 19. Documentation Updates

Update all affected documentation files.

## 20. Testing Checklist

Provide manual tests.

## 21. Automated Tests

Provide Feature Tests.

## 22. Commands

Include:

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan test
npm run build
```

## 23. Git Commit

Provide:

```bash
git add .
git status
git commit -m "..."
git push origin main
```

## 24. Next Exact Step

End every module with one clear next module.

---

# Coding Style Requirements

## Backend

* Provide full copy-paste code.
* Include all imports.
* Do not use placeholder methods.
* Use Form Requests.
* Use transactions for multi-table actions.
* Use services for reusable logic.
* Use policies or explicit authorization.
* Enforce ownership.
* Enforce Zone restrictions.
* Preserve historical records.
* Use clear method names.
* Use route-model binding consistently.
* Use status constants where practical.

## Frontend

* Provide full React files.
* Include all imports.
* Use Tailwind CSS.
* Use Lucide React.
* Use AdminLayout.
* Preserve existing design style.
* Use responsive layouts.
* Use safe default props.
* Use optional chaining.
* Use confirmation dialogs.
* Display validation errors.
* Display processing states.
* Use status badges.
* Use normal anchor tags for file downloads.

## Database

* Use MySQL-compatible migrations.
* Avoid overly long index names.
* Do not modify old migrations after deployment.
* Preserve historical references.
* Avoid destructive cascade behavior where history matters.
* Prefer inactive status or soft deletes over hard deletion.

---

# Important Existing Technical Decisions

## Inertia SSR

Do not enable Inertia SSR.

## Principal Registration

Do not restore ordinary Breeze registration.

Principal registration is NIC controlled.

## NIC

Principal cannot change NIC.

## Profile Editing

Principal may edit all permitted personal, contact, service, and
employment fields except NIC.

## Appointment Form

Zone first, then School.

Appointment Date sets Start Date.

Start Date is read-only.

## Transfer Application Uniqueness

Do not restore a permanent unique constraint on:

```text
transfer_cycle_id + principal_profile_id
```

The system allows reapplication after withdrawal.

Use active-status validation instead.

## PDF

Keep submitted PDFs private.

## Historical Records

Do not delete submitted or withdrawn history.

---

# Recent Important Fixes

## Service Category

Problem:

```text
Data truncated for column 'service_category'
```

Cause:

* Database used restrictive ENUM behavior.
* Frontend allowed flexible text.

Resolution:

* Changed `service_category` to nullable string.
* Updated validation.

## Application Reapplication

Problem:

* Withdrawn application blocked a new application in same Cycle.

Resolution:

* Removed permanent combined uniqueness.
* Added active-application logic.
* Preserved withdrawn history.

## MySQL Index Dependency

Problem:

* MySQL refused to drop a unique index because foreign keys depended on
  it.

Resolution:

* Added replacement indexes first.
* Dropped combined unique index after.

## React `.map()` Errors

Resolution:

* Use safe defaults.
* Use optional chaining.
* Never assume props exist.

## Show Page Error

Problem:

```text
application is not defined
```

Resolution:

* Keep application-dependent variables inside the component.

## PDF Buttons

Current approved UI:

* PDF button remains on Principal Index.
* PDF button also appears on Principal Show.
* Admin Show has PDF download.
* Both Principal locations remain.

---

# Current Git State

Previous confirmed commit:

```text
a8bb9d0 profile principal profiles and appointment management
```

The next commit should include all Module 06 and documentation work.

Recommended commit:

```bash
git commit \
  -m "Add transfer cycles and principal transfer application workflow" \
  -m "Add principal self-service appointment and profile updates, admin application views, submitted PDF generation, school and division seeders, permissions, documentation, and feature tests."
```

After committing:

```bash
git status
git log -2 --oneline
```

Expected:

```text
nothing to commit, working tree clean
```

---

# Next Exact Module

## Module 07: Zonal Director Transfer Review and Recommendation

This is the next development module.

Do not skip directly to Provincial review.

---

# Module 07 Required Scope

## Main Purpose

Allow Zonal Directors to review submitted Principal transfer
applications originating from their assigned education Zone.

## Main Users

* Zonal Director
* Super Admin

## Core Requirements

Module 07 must include:

### Zonal Director Zone Assignment

* Add a Zone assignment for Zonal Director users.
* Decide whether Zone assignment belongs in:

  * users table
  * user profile table
  * separate role-assignment table
* Prefer a clean reusable design.
* A Zonal Director must normally have one assigned Zone.
* Super Admin is not Zone restricted.

### Zonal Application Access

* Zonal Director sees only applications from assigned Zone.
* Zone is determined from the application current School snapshot or
  related current School.
* Direct URL access to another Zone must return HTTP 403.
* Query filters must never expand the authorized Zone scope.

### Zonal Application Index

Create:

```text
resources/js/Pages/Zonal/TransferApplications/Index.jsx
```

Include:

* Page title
* Status summary cards
* Search
* Transfer Cycle filter
* Status filter
* School filter
* Submitted date filter if practical
* Application table
* Principal name
* Application number
* Current School
* Submission date
* Status
* View action
* PDF action where permitted
* Pagination
* Empty state

### Zonal Application Show

Create:

```text
resources/js/Pages/Zonal/TransferApplications/Show.jsx
```

Include:

* Application number
* Status badge
* Principal snapshot
* Current appointment snapshot
* Transfer reason
* Detailed explanation
* Medical, spouse, and mutual-transfer indicators
* School preferences
* Submitted PDF download
* Application timeline
* Zonal review panel
* Recommendation field
* Remarks
* Approve button
* Reject button
* Mandatory rejection reason
* Confirmation dialogs
* Status-history card

### Zonal Review Start

Add action:

```text
Submitted → Zonal Review
```

Record:

* Reviewer
* Review start timestamp
* Status history
* Notification

### Zonal Approval

Add action:

```text
Zonal Review → Zonal Approved
```

Record:

* Reviewer
* Recommendation
* Remarks
* Decision timestamp
* Status history
* Notification

### Zonal Rejection

Add action:

```text
Zonal Review → Zonal Rejected
```

Require:

* Rejection reason
* Reviewer
* Decision timestamp
* Status history
* Notification

### Status History

Implement a history table.

Recommended table:

```text
transfer_application_actions
```

Suggested fields:

```text
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
```

Suggested model:

```text
App\Models\TransferApplicationAction
```

Relationships:

* TransferApplication has many actions.
* Action belongs to TransferApplication.
* Action belongs to User through `acted_by`.

### Zonal Review Record

Choose one of these clean designs:

Option A:

```text
zonal_reviews
```

Option B:

Store review fields directly in `transfer_applications`.

Preferred approach:

Use a separate `zonal_reviews` table because review data is a separate
workflow entity and may need history or future extension.

Suggested fields:

```text
id
transfer_application_id
zone_id
reviewer_id
recommendation
decision
remarks
review_started_at
reviewed_at
created_at
updated_at
```

### Notifications

Add:

```text
TransferApplicationZonalReviewStartedNotification
TransferApplicationZonalApprovedNotification
TransferApplicationZonalRejectedNotification
```

Email failure must not reverse the review decision.

### Permissions

Add and seed:

```text
view zonal transfer applications
review zonal transfer applications
approve zonal transfer applications
reject zonal transfer applications
download zonal transfer application pdfs
```

Assign to:

* Zonal Director
* Super Admin

### Routes

Expected route prefix:

```text
/zonal
```

Expected route-name prefix:

```text
zonal.
```

Expected routes:

```text
GET /zonal/transfer-applications
GET /zonal/transfer-applications/{transferApplication}
GET /zonal/transfer-applications/{transferApplication}/pdf
POST /zonal/transfer-applications/{transferApplication}/start-review
POST /zonal/transfer-applications/{transferApplication}/approve
POST /zonal/transfer-applications/{transferApplication}/reject
```

Suggested route names:

```text
zonal.transfer-applications.index
zonal.transfer-applications.show
zonal.transfer-applications.pdf
zonal.transfer-applications.start-review
zonal.transfer-applications.approve
zonal.transfer-applications.reject
```

Remember:

* PDF route before Show route.
* Backend Zone restriction on every route.

### Sidebar

Add a Zonal sidebar or role-aware Zonal menu.

Suggested items:

* Zonal Dashboard
* Transfer Applications
* Review Queue
* Reviewed Applications

Do not place Zonal-only items in Principal sidebar.

### Tests

Create:

```text
tests/Feature/Zonal/ZonalTransferReviewTest.php
```

Test:

* Zonal Director can view assigned-Zone application.
* Zonal Director cannot view another-Zone application.
* Super Admin can view all.
* Submitted application can enter Zonal Review.
* Invalid status cannot enter Zonal Review.
* Zonal approval works.
* Zonal rejection works.
* Rejection reason is required.
* Reviewer is recorded.
* Timestamp is recorded.
* Status history is recorded.
* PDF download respects Zone.
* Notifications are sent.
* Email failure does not reverse decision.
* Original submitted snapshot remains unchanged.
* Principal sees updated status.
* Unauthorized users receive HTTP 403.

---

# Module 07 Frontend Design Requirements

Use the same visual language as Modules 05 and 06.

## Zonal Index

Use:

* White cards
* Slate page background
* Blue and amber summary cards
* Rounded filters
* Clean table
* Status badges
* Eye action
* PDF action
* Responsive pagination

## Zonal Show

Recommended layout:

```text
Header
├── Application number
├── Cycle
├── Status badge
├── PDF button
└── Review actions

Main Grid
├── Left: Application details
└── Right: Timeline and reviewer information

Full Width
├── Principal details
├── Current appointment snapshot
├── School preferences
├── Zonal review form
└── Status history
```

## Action Buttons

Start review:

```text
Amber
```

Approve:

```text
Emerald
```

Reject:

```text
Red
```

PDF:

```text
Blue or Emerald
```

## Confirmation Text

Start review:

```text
Start reviewing this transfer application?
```

Approve:

```text
Approve this application at Zonal level?
```

Reject:

```text
Reject this application at Zonal level?
```

---

# Documentation to Update After Module 07

Update:

```text
docs/01-project-overview.md
docs/02-system-scope.md
docs/03-user-roles.md
docs/04-database-design.md
docs/06-routes.md
docs/07-permissions.md
docs/08-business-rules.md
docs/09-email-notifications.md
docs/10-pdf-excel-exports.md
docs/11-status-workflow.md
docs/12-testing-checklist.md
docs/13-development-log.md
docs/14-change-log.md
docs/15-next-chat-handover.md
```

---

# Commands Required at End of Module 07

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan test
npm run build
```

Where safe:

```bash
php artisan migrate:fresh --seed
```

Route checks:

```bash
php artisan route:list --name=zonal.transfer-applications
```

Permission checks:

```bash
php artisan permission:cache-reset
php artisan db:seed --class=RolePermissionSeeder
```

---

# Git Commit for Module 07

Recommended:

```bash
git add .
git status
git commit \
  -m "Add zonal transfer application review workflow" \
  -m "Add zone-restricted review access, zonal recommendations, approvals, rejections, status history, notifications, permissions, React pages, tests, and documentation."
git push origin main
```

---

# Mandatory Continuation Instructions for the Next Chat

Read this document first.

Then follow these rules:

1. Continue from Module 07.
2. Do not rebuild completed Modules 01 to 06.
3. Do not change the established frontend design.
4. Do not enable Inertia SSR.
5. Do not restore ordinary public registration.
6. Keep NIC locked for Principals.
7. Keep submitted PDFs private.
8. Keep PDF download on both Principal Index and Show.
9. Preserve withdrawn application history.
10. Preserve reapplication after withdrawal.
11. Use active-application validation rather than a permanent combined
    unique constraint.
12. Enforce Zonal access on the backend.
13. Provide full copy-paste code.
14. Include terminal commands.
15. Include migrations.
16. Include models.
17. Include requests.
18. Include controllers.
19. Include services.
20. Include policies and permissions.
21. Include notifications.
22. Include routes.
23. Include sidebar changes.
24. Include full React pages.
25. Include tests.
26. Include documentation updates.
27. Include Git commit.
28. End with the next exact module.

---

# Exact Prompt to Paste into the Next Chat

Copy and paste the text below into the next chat after uploading the
documentation files.

```text
Continue the Principal Transfer System from the uploaded
docs/15-next-chat-handover.md.

Read docs/15-next-chat-handover.md first, then read the other uploaded
documentation files before providing any code.

The project path is:

/Applications/MAMP/htdocs/principal-transfer

The stack is:

Laravel + React + Inertia.js + MySQL + Spatie Laravel Permission +
Tailwind CSS + Lucide React + DomPDF + Laravel Excel.

Do not enable Inertia SSR.

Modules 01 to 06 are completed.

The next exact module is:

Module 07: Zonal Director Transfer Review and Recommendation.

Continue using the exact existing module-delivery pattern:

1. Project folder command
2. Package installation only if needed
3. File creation commands
4. Migration
5. Model
6. Relationships
7. Form Requests
8. Controllers
9. Policies and backend authorization
10. Services
11. Notifications
12. PDF and Excel exports
13. Seeders
14. Routes
15. Sidebar update
16. Full React pages
17. Documentation updates
18. Testing checklist
19. Automated tests
20. Build commands
21. Git commit
22. Next exact step

Provide full copy-paste code, not partial snippets.

Keep the existing frontend design:

- AdminLayout
- White cards
- Slate backgrounds
- Rounded-xl and rounded-2xl components
- Tailwind CSS
- Lucide React icons
- Responsive tables
- Status badges
- Shared forms
- Safe optional chaining
- Safe default arrays
- Inertia Link for page navigation
- Normal anchor tags for PDF downloads

Important current rules:

- Principal registration is NIC controlled.
- Ordinary Breeze registration remains disabled.
- Principal cannot change NIC.
- Principal may edit other permitted profile, service, and appointment
  details.
- Zone must be selected before School in appointment forms.
- Appointment Date sets Start Date.
- Submitted applications are immutable.
- Submitted PDFs are private.
- PDF download remains available from both Principal Index and Show.
- A Principal may have only one active application per Cycle.
- Withdrawn applications remain in history.
- A new application is allowed after withdrawal.
- Do not restore a permanent unique constraint on
  transfer_cycle_id + principal_profile_id.
- Zonal Directors must be restricted to their assigned Zone on the
  backend.
- Frontend visibility checks are not sufficient authorization.

Start by giving the full implementation plan for Module 07, then
provide the exact commands and complete files in the established
pattern.
```

---

# Files to Upload to the Next Chat

Upload these documents:

```text
docs/01-project-overview.md
docs/02-system-scope.md
docs/03-user-roles.md
docs/04-database-design.md
docs/05-module-structure.md
docs/06-routes.md
docs/07-permissions.md
docs/08-business-rules.md
docs/09-email-notifications.md
docs/10-pdf-excel-exports.md
docs/11-status-workflow.md
docs/12-testing-checklist.md
docs/13-development-log.md
docs/14-change-log.md
docs/15-next-chat-handover.md
```

Also upload the current code inventory if available.

Recommended command:

```bash
find app database resources/js resources/views routes tests \
    -type f \
    | sort \
    > current-code-inventory.txt
```

Then upload:

```text
current-code-inventory.txt
```

For more detailed PHP and React structure:

```bash
find app database resources/js resources/views routes tests \
    -type f \
    \( -name "*.php" -o -name "*.jsx" -o -name "*.js" -o -name "*.md" \) \
    | sort \
    > current-code-inventory.txt
```

Also useful:

```bash
php artisan route:list > current-routes.txt
```

Upload:

```text
current-routes.txt
```

Optional Git history:

```bash
git log --oneline --decorate -20 > recent-git-history.txt
```

Upload:

```text
recent-git-history.txt
```

---

# Final Current Status

Completed:

Module 01
Module 02
Module 03
Module 04
Module 05
Module 06
Module 07

```

Next:

```text
Module 08: Provincial Director Transfer Review and Recommendation
```

