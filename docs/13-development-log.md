# Development Log

## Project

Principal Transfer System

## Organization

Provincial Department of Education  
Sabaragamuwa Province, Sri Lanka

## Technology Stack

- Laravel
- React
- Inertia.js
- MySQL
- Laravel Breeze
- Spatie Laravel Permission
- Tailwind CSS
- Lucide React
- DomPDF
- Laravel Excel

## Local Development Path

```text
/Applications/MAMP/htdocs/principal-transfer
````

---

# Module 01: Project Foundation, Authentication, Roles and Admin Layout

## Status

Completed

## Purpose

Establish the Laravel, React, Inertia, authentication, authorization,
dashboard, and administration foundation for the Principal Transfer
System.

## Implemented

* Laravel project foundation
* React frontend
* Inertia.js integration
* MySQL database configuration
* Laravel Breeze authentication
* Login
* Logout
* Forgot-password workflow
* Password reset workflow
* Email verification
* Spatie Laravel Permission installation
* Role and permission database tables
* Initial system roles
* Super Admin account seeder
* Global Super Admin authorization handling
* Role-based dashboard redirection
* Admin dashboard
* Principal dashboard placeholder
* Zonal Director dashboard placeholder
* Provincial Director dashboard placeholder
* Transfer Board dashboard placeholder
* Shared authenticated layout
* AdminLayout
* Admin sidebar
* Admin topbar
* Shared frontend permission data
* Initial authorization checks
* Public Breeze registration disabled
* Initial feature tests
* Documentation directory and base documents

## System Roles Added

* Super Admin
* Principal
* Zonal Director
* Provincial Director
* Transfer Board Member
* Data Entry Officer

## Important Decisions

* Ordinary public registration remains disabled.
* Principal registration must use NIC-controlled registration.
* All backend actions require authorization.
* Frontend visibility checks are not treated as security.
* Inertia SSR remains disabled.
* All protected administrative pages use AdminLayout.

## Testing

Verified:

* Authentication
* Email verification
* Dashboard redirection
* Role assignment
* Super Admin access
* Unauthorized route access
* Admin layout rendering

## Result

The system foundation was completed successfully.

---

# Module 02: Roles, Permissions and User Management

## Status

Completed

## Purpose

Provide administration of users, roles, permissions, account status,
password resets, and access-control rules.

## Implemented

### User Management

* User list
* User search
* User filtering
* User creation
* User editing
* Role assignment
* Multiple-role support where permitted
* Account activation
* Account deactivation
* Email verification management
* Admin password reset
* Last login tracking
* Creator tracking
* Updater tracking

### User Protection Rules

* Inactive users cannot log in
* Users cannot deactivate themselves unsafely
* Users cannot delete themselves unsafely
* Last Super Admin cannot be deleted
* Last Super Admin cannot be deactivated
* Duplicate email addresses are rejected

### Role Management

* Role list
* Role creation
* Role editing
* Permission assignment
* System-role protection
* Safe role deletion restrictions

### Permission Management

* Permission list
* Permission creation
* Permission deletion where permitted
* Permission cache reset support
* Sidebar visibility based on permissions

### Tests

* User management feature tests
* Role management tests
* Permission assignment tests
* Last Super Admin protection tests
* Inactive login prevention tests
* Unauthorized access tests

## Important Decisions

* Permission checks are enforced in controllers and middleware.
* Frontend permission checks are used only for interface visibility.
* Super Admin access uses a global authorization rule.

## Result

User, role, and permission administration was completed successfully.

---

# Module 03: Zones, Divisions and Schools Management

## Status

Completed

## Purpose

Create the Sabaragamuwa Province education organization structure.

## Implemented

### Zones

* Zone table
* Zone model
* Zone controller
* Zone CRUD pages
* Zone search
* Zone filtering
* Active and inactive status
* Sort order
* Seven-zone seeder

### Divisions

* Division table
* Division model
* Division controller
* Division CRUD pages
* Zone relationship
* Search
* Filtering
* Active and inactive status

### Schools

* School table
* School model
* School controller
* School CRUD pages
* Division relationship
* Zone relationship through Division
* Census number
* School name
* School type
* Gender type
* School level
* Teaching mediums
* Address fields
* Contact fields
* Student count
* Teacher count
* National-school flag
* Active and inactive status

### Data Integrity

* Unique Zone codes
* Unique Division codes
* Unique School census numbers
* Zone deletion blocked when Divisions exist
* Division deletion blocked when Schools exist
* Historical inactive records preserved

### Seeders

* ZoneSeeder
* DivisionSeeder
* SchoolSeeder
* DatabaseSeeder order updated

### Frontend

* Admin navigation added
* Zone pages
* Division pages
* School pages
* Responsive tables
* Filters
* Search
* Empty states

### Tests

* Zone management tests
* Division management tests
* School management tests
* Relationship tests
* Deletion restriction tests
* Census-number validation tests

## Important Fixes

* Corrected School migration structure
* Ensured `mediums` is stored as JSON
* Added array cast for `mediums`
* Corrected Seeder fields to match actual database columns
* Avoided inserting Seeder code into Migration files

## Result

The provincial organization structure was completed successfully.

---

# Module 04: Principal Registry and NIC-Controlled Self-Registration

## Status

Completed

## Purpose

Allow only authorized Principals to register through approved NIC
records.

## Implemented

### Principal Registry

* Principal Registry table
* Principal Registry model
* Principal Registry controller
* Registry CRUD pages
* Search
* Filters
* Active status
* Registration status
* School relationship
* User relationship
* Profile relationship
* Duplicate NIC protection

### NIC Handling

* NIC normalization service
* Old NIC validation
* New NIC validation
* Uppercase normalization
* Duplicate normalized NIC prevention

### Registry Import

* CSV import page
* CSV upload
* Validation
* Duplicate handling
* CSV template download
* Import result feedback

### Principal Registration

* NIC verification page
* NIC verification endpoint
* Time-limited verification session
* Registration form
* User account creation
* Principal role assignment
* Principal Profile creation
* Registry-to-user linking
* Registry-to-profile linking
* Registry status update
* Registration timestamp
* Duplicate registration prevention
* Email verification redirect

### Security Rules

* Ordinary Breeze registration remains disabled
* Only verified Registry NIC records may register
* Inactive Registry records are rejected
* Registered Registry records are rejected
* Linked Registry records cannot be reused
* Verification sessions expire after fifteen minutes
* Account creation uses a database transaction

### Tests

* NIC verification tests
* Old NIC format tests
* New NIC format tests
* Invalid NIC tests
* Duplicate registration tests
* Registry linking tests
* Principal role assignment tests
* Registration session expiry tests

## Important Fixes

* Clarified that Registry status `registered` means account creation,
  not email verification
* Corrected registration relationships
* Ensured Principal Profile creation occurs during registration
* Investigated email verification 403 issues
* Confirmed `APP_URL` and signed-link host requirements
* Identified copied `&amp;` in verification URLs as a possible cause

## Result

NIC-controlled Principal self-registration was completed successfully.

---

# Module 05: Principal Profile and Current Appointment Management

## Status

Completed

## Purpose

Manage Principal personal, service, contact, and appointment records.

## Implemented

### Principal Profile

* Principal Profile table
* Principal Profile model
* User relationship
* Principal Registry relationship
* Appointment relationship
* Current appointment relationship
* Personal information
* Contact information
* Residential address
* Service information
* Employment status
* Qualifications summary
* Notes
* Profile completion status

### Admin Profile Management

* Profile list
* Search
* Filters
* Profile create
* Profile edit
* Profile show
* Profile deletion restrictions
* Registry relationship display
* User relationship display
* Appointment-history display

### Principal Self-Service Profile

* View own profile
* Edit own profile
* Update personal information
* Update contact information
* Update service information
* Update qualifications
* Update employment details
* Update notes
* Mark profile complete
* NIC field locked
* Ownership checks

### Principal Appointment Management

* Principal Appointment table
* Principal Appointment model
* Appointment history
* School relationship
* Designation
* Appointment type
* Appointment letter number
* Appointment date
* Start date
* End date
* Current appointment flag
* Reason for end
* Remarks

### Current Appointment Logic

* Only one appointment should be current
* New current appointment closes the previous current appointment
* Previous current appointment receives an end date
* Previous end date becomes one day before the new appointment date
* Current appointment relationship supported

### Admin Appointment Management

* Add appointment
* Edit appointment
* Delete permitted appointment
* View appointment history

### Principal Self-Service Appointment Management

* Add own appointment
* Edit own appointment
* Delete permitted own appointment
* Ownership authorization
* Separate Principal appointment pages

### User Interface

* Principal Profile Show page
* Principal Profile Edit page
* Appointment Create page
* Appointment Edit page
* Shared AppointmentForm
* Separate Principal sidebar
* Role-aware layout selection
* Principal dashboard improvements

### Tests

* Admin Principal Profile management tests
* Principal self-profile tests
* Ownership tests
* Current appointment tests
* Unauthorized access tests

## Important Fixes

* Fixed missing profile options causing `.map()` errors
* Added safe default values for gender, service grade, and employment
  status
* Updated profile rules so Principal may edit all profile and service
  information except NIC
* Changed `service_category` from restrictive ENUM behavior to a
  nullable string
* Added migration:
  `change_service_category_to_string_in_principal_profiles_table`
* Fixed MySQL data truncation for `service_category`
* Added Principal self-service appointment controller
* Added Principal self-service appointment request classes
* Added ownership validation
* Added zone-first School selection
* School list now filters by selected Zone
* Appointment Date automatically sets Start Date
* Start Date is read-only in the interface
* Backend validation requires Start Date to match Appointment Date
* Corrected accidental mixing of Transfer Application and Appointment
  form components
* Added safe default arrays for form options

## Result

Principal Profile and Appointment management was completed and later
enhanced during Module 06 work.

---

# Module 06: Transfer Cycles and Transfer Application Management

## Status

Completed

## Purpose

Provide Transfer Cycle administration and the complete Principal
application workflow from Draft creation to submission, PDF generation,
withdrawal, and application history.

## Implemented

### Transfer Cycle Management

* Transfer Cycle migration
* Transfer Cycle model
* Transfer Cycle controller
* Store request
* Update request
* Transfer Cycle CRUD
* Transfer Cycle list
* Transfer Cycle create
* Transfer Cycle edit
* Transfer Cycle show
* Cycle code
* Cycle name
* Transfer year
* Transfer type
* Application open date
* Application close date
* Effective date
* Minimum service requirement
* Maximum school preferences
* Publication status
* Cycle availability logic
* Admin sidebar integration

### Transfer Application Database

* Transfer Applications table
* Transfer Preferences table
* TransferApplication model
* TransferPreference model
* TransferCycle relationships
* PrincipalProfile relationships
* PrincipalAppointment relationships
* Current School relationship
* Preference relationships
* Application status tracking
* Application number
* Submission timestamp
* Withdrawal timestamp
* Withdrawal reason
* Declaration acceptance
* Applicant snapshot fields
* Current appointment snapshot fields
* Submitted PDF path
* Submitted PDF generation timestamp

### Transfer Application Eligibility

* Principal role required
* Principal Profile required
* Active employment status required
* Current appointment required
* Published Cycle required
* Open Cycle required
* Minimum service requirement
* Existing active application restriction
* Current School excluded from preferences
* Duplicate School preferences prohibited
* Maximum preference count enforced

### Draft Workflow

* Start application
* Create Draft
* Save transfer reason
* Save detailed explanation
* Save Medical Reason flag
* Save Spouse Employment flag
* Save Mutual Transfer flag
* Save Mutual Principal NIC
* Save Principal remarks
* Add ranked preferences
* Edit Draft
* Delete Draft
* View Draft
* Draft ownership checks

### Current Appointment Snapshot

Captured when creating the application:

* Principal name
* NIC
* Employee number
* Service grade
* Current designation
* Current appointment
* Current School
* Current appointment start date
* Service duration at current School

The snapshot is preserved independently of later profile or appointment
changes.

### School Preferences

* Ranked preference order
* School relationship
* Division relationship
* Zone relationship
* Preference reason
* Duplicate prevention
* Current School prevention
* Maximum count validation
* Preference display on Show pages

### Submission Workflow

* Final declaration
* Confirmation prompt
* Draft-only submission
* Open-Cycle revalidation
* Eligibility revalidation
* Preference validation
* Unique application number generation
* Status change to Submitted
* Submission timestamp
* Declaration storage
* Application locking
* Preference locking
* Redirect to Show page
* Success message

### Submitted PDF Generation

* DomPDF integration
* `TransferApplicationPdfService`
* Submitted PDF Blade view
* Private local storage
* Application number-based filename
* PDF path recording
* PDF generation timestamp
* PDF generation after submission
* PDF regeneration when file is missing
* Submission remains successful if PDF generation fails
* PDF failure logging
* Warning message on generation failure

### Principal PDF Access

* PDF button on Principal Application Index
* PDF button on Principal Application Show page
* Authorized Principal PDF route
* Ownership validation
* PDF remains available after withdrawal
* Normal anchor used for file download

### Admin PDF Access

* Admin Application Index
* Admin Application Show
* Admin PDF route
* Permission protection
* Submitted PDF download
* PDF information card
* PDF generation timestamp display

### Withdrawal Workflow

* Principal may withdraw where permitted
* Withdrawal reason required
* Withdrawal timestamp recorded
* Status becomes Withdrawn
* Withdrawn application remains in history
* Preferences remain
* Application number remains
* Submitted PDF remains available
* Withdrawn application cannot be edited
* Withdrawn application cannot be resubmitted directly

### Reapplication After Withdrawal

* Removed permanent unique restriction on:

  * Transfer Cycle ID
  * Principal Profile ID
* Added active-application query scope
* Withdrawn applications do not block a new application
* Cancelled applications may be treated as inactive
* New application is a separate record
* New application captures a fresh snapshot
* New application receives a new number after submission
* New application generates a separate PDF
* Previous application history remains unchanged

### Admin Transfer Application Pages

* Admin Application Index
* Search and filters
* Application status display
* Principal information
* Current appointment snapshot
* Transfer reason
* Ranked School preferences
* Withdrawal details
* Submitted PDF download
* Read-only application view

### Principal Transfer Application Pages

* Application Index
* Open Transfer Cycles
* Application history
* View action
* Edit Draft action
* PDF download action
* Status badge
* Pagination
* Empty state
* Application Create
* Application Edit
* Application Show
* Final submission form
* Withdrawal form
* Submitted PDF section

### Principal Navigation

* Separate Principal sidebar
* Principal Dashboard link
* Principal Profile link
* Principal Transfer Applications link
* Role-aware AdminLayout behavior

### Permissions

Added or updated permissions for:

* View Transfer Cycles
* Manage Transfer Cycles
* View Transfer Applications
* Create Transfer Applications
* Principal dashboard
* Principal profile access
* Principal appointment access

### Seeders

* DivisionSeeder
* SchoolSeeder
* RolePermissionSeeder updates
* DatabaseSeeder updates

### Documentation

Updated or prepared:

* Project Overview
* System Scope
* User Roles
* Database Design
* Module Structure
* Routes
* Permissions
* Business Rules
* Email Notifications
* PDF and Excel Exports
* Status Workflow
* Testing Checklist
* Development Log
* Change Log
* Next Chat Handover

### Tests

Added or updated:

* Admin Principal Profile Management Test
* Admin Transfer Cycle Management Test
* Principal Self Profile Test
* Principal Transfer Application Management Test

Test areas include:

* Transfer Cycle CRUD
* Application eligibility
* Draft creation
* Draft update
* Draft deletion
* Ownership
* Submission
* Preference validation
* Withdrawal
* Reapplication
* PDF authorization
* Profile editing
* Appointment rules

## Important Technical Fixes

### Transfer Application Unique Constraint

Initial design prevented a second application in the same Cycle even
after withdrawal.

Resolution:

* Added replacement single-column indexes
* Removed the old combined unique index
* Added status-aware application logic
* Used an active-application query scope
* Preserved withdrawn application history

### Duplicate Migration

Two migrations attempted to remove the same unique index.

Resolution:

* Removed the duplicate migration
* Retained one correct migration
* Recommended `migrate:fresh --seed` for local development

### MySQL Foreign Key Index Dependency

MySQL initially prevented dropping the unique index because foreign keys
depended on it.

Resolution:

* Added replacement indexes first
* Dropped the combined unique index afterward
* Added a status-related index

### `service_category` Data Truncation

MySQL raised:

```text
Data truncated for column 'service_category'
```

Cause:

* Database column used a restrictive ENUM
* Frontend allowed flexible text

Resolution:

* Added migration changing `service_category` to string
* Updated validation
* Preserved flexible service-category values

### React `.map()` Errors

Cause:

* Missing `options` props
* Direct use of undefined arrays

Resolution:

* Added safe defaults
* Used:

  * `options = {}`
  * `schools = []`
  * fallback arrays
* Used optional chaining

### Principal Show Page Navigation

The Principal Application Show page initially crashed because
`application` was referenced outside the component.

Resolution:

* Moved `canDownloadPdf` inside `Show({ application })`
* Added safe optional access
* Fixed the Application Index View action
* Added explicit navigation support

### PDF Button Placement

PDF download is now available from both:

* Principal Application Index
* Principal Application Show

Admin PDF download is available from:

* Admin Application Show

### Appointment Form Improvements

* Zone selected before School
* School list filtered by Zone
* Existing appointment preselects correct Zone
* Appointment Date sets Start Date
* Start Date made read-only
* Backend validates equality
* Current appointment clears End Date

## Manual Verification Performed

* Principal Profile update
* Service category update
* Principal Appointment creation
* Zone-based School filtering
* Transfer Cycle creation
* Draft application creation
* Draft Show page
* View action navigation
* Submission flow
* Submitted PDF display
* Admin Application Show
* Principal PDF buttons
* Admin PDF button
* Withdrawal flow
* Reapplication logic
* Git staged-file review

## Git

Previous completed commit:

```text
a8bb9d0 profile principal profiles and appointment management
```

Recommended Module 06 commit:

```text
Add transfer cycles and principal transfer application workflow
```

Recommended detailed commit command:

```bash
git commit \
  -m "Add transfer cycles and principal transfer application workflow" \
  -m "Add principal self-service appointment and profile updates, admin application views, submitted PDF generation, school and division seeders, permissions, documentation, and feature tests."
```

## Result

Module 06 was completed successfully.

The system now supports:

* Transfer Cycle administration
* Principal Draft applications
* Ranked School preferences
* Eligibility checks
* Final submission
* Submitted PDF generation
* Principal PDF access
* Admin PDF access
* Withdrawal
* Reapplication after withdrawal
* Application history
* Admin application viewing

---

# Current System Status

## Completed Modules

### Module 01

Project Foundation, Authentication, Roles and Admin Layout

### Module 02

Roles, Permissions and User Management

### Module 03

Zones, Divisions and Schools Management

### Module 04

Principal Registry and NIC-Controlled Self-Registration

### Module 05

Principal Profile and Current Appointment Management

### Module 06

Transfer Cycles and Transfer Application Management

---

# Next Exact Step

## Module 07

Zonal Director Transfer Review and Recommendation

Planned work:

* Zonal Director assignment to Zone
* Zone-restricted application access
* Zonal Application Index
* Zonal Application Show
* Review-start action
* Zonal recommendation
* Zonal approval
* Zonal rejection
* Mandatory rejection reason
* Reviewer identity
* Review timestamp
* Status transition history
* Submitted PDF access
* Zonal email notifications
* Zonal permissions
* Sidebar update
* Automated tests
* Documentation update
* Git commit

---

# Mandatory Continuation Rules

* Use Laravel, React, Inertia.js, and MySQL.
* Use Spatie Laravel Permission.
* Keep Inertia SSR disabled.
* Use AdminLayout for protected pages.
* Keep Principal navigation separate and role-aware.
* Enforce authorization on the backend.
* Enforce Principal ownership.
* Enforce Zonal access by assigned Zone.
* Preserve submitted application snapshots.
* Preserve withdrawn application history.
* Keep PDF files private.
* Use normal anchors for file downloads.
* Provide full copy-paste code.
* Update documentation after each module.
* Include tests.
* Include build commands.
* Include a Git commit.
* Record the next exact step.



## Module 07

Implemented Zonal Director Zone assignment, Zone-scoped transfer
application access, review start, recommendation, approval, rejection,
workflow action history, notifications, private PDF access, React
review pages, permissions and automated tests.
