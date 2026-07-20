
---

# `docs/06-routes.md`

Replace the entire existing file. The current version stops around Module 04 and is missing principal profiles, appointments, transfer cycles, applications, and PDF routes. :contentReference[oaicite:2]{index=2}

```md
# Routes

## Route Conventions

The system uses named Laravel routes.

Main prefixes:

- `/admin`
- `/principal`
- `/zonal`
- `/provincial`
- `/transfer-board`

Main route name prefixes:

- `admin.`
- `principal.`
- `zonal.`
- `provincial.`
- `transfer-board.`

All protected routes require:

- `auth`
- `verified`

Principal self-service routes also require:

- `role:Principal`

Specific routes must be declared before general parameter routes.

Example:

```text
/transfer-applications/{transferApplication}/pdf

must appear before:

/transfer-applications/{transferApplication}
Public Home
Home Page
GET /

Route name:

home
Authentication
Login
GET /login
POST /login
Logout
POST /logout
Forgot Password
GET /forgot-password
POST /forgot-password
Reset Password
GET /reset-password/{token}
POST /reset-password
Email Verification
GET /verify-email
GET /verify-email/{id}/{hash}
POST /email/verification-notification

Ordinary Breeze registration remains disabled.

NIC-Controlled Principal Registration
NIC Verification Page
GET /principal-registration

Route name:

principal-registration.verify-page
Verify NIC
POST /principal-registration/verify-nic

Route name:

principal-registration.verify
Registration Form
GET /principal-registration/create

Route name:

principal-registration.create
Create Principal Account
POST /principal-registration

Route name:

principal-registration.store
Account Profile

These routes manage the authenticated user account, not the official
principal service profile.

Edit Account Profile
GET /profile

Route name:

profile.edit
Update Account Profile
PATCH /profile

Route name:

profile.update
Delete Account
DELETE /profile

Route name:

profile.destroy
Dashboard Routing
Role Redirect Dashboard
GET /dashboard

Route name:

dashboard
Admin Dashboard
GET /admin/dashboard

Route name:

admin.dashboard

Required permission:

view admin dashboard
Principal Dashboard
GET /principal/dashboard

Route name:

principal.dashboard

Required permission:

view principal dashboard
Zonal Director Dashboard
GET /zonal/dashboard

Route name:

zonal.dashboard

Required permission:

view zonal dashboard
Provincial Director Dashboard
GET /provincial/dashboard

Route name:

provincial.dashboard

Required permission:

view provincial dashboard
Transfer Board Dashboard
GET /transfer-board/dashboard

Route name:

transfer-board.dashboard

Required permission:

view transfer board dashboard
Admin Routes

All routes below use the /admin prefix and admin. route-name prefix.

User Management
User Resource Routes
GET /admin/users
GET /admin/users/create
POST /admin/users
GET /admin/users/{user}
GET /admin/users/{user}/edit
PUT/PATCH /admin/users/{user}
DELETE /admin/users/{user}

Route names:

admin.users.index
admin.users.create
admin.users.store
admin.users.show
admin.users.edit
admin.users.update
admin.users.destroy
Reset User Password
PUT /admin/users/{user}/reset-password

Route name:

admin.users.reset-password
Role Management
GET /admin/roles
GET /admin/roles/create
POST /admin/roles
GET /admin/roles/{role}/edit
PUT/PATCH /admin/roles/{role}
DELETE /admin/roles/{role}

Route names:

admin.roles.index
admin.roles.create
admin.roles.store
admin.roles.edit
admin.roles.update
admin.roles.destroy
Permission Management
Permission List
GET /admin/permissions

Route name:

admin.permissions.index
Create Permission
POST /admin/permissions

Route name:

admin.permissions.store
Delete Permission
DELETE /admin/permissions/{permission}

Route name:

admin.permissions.destroy
Zone Management
GET /admin/zones
GET /admin/zones/create
POST /admin/zones
GET /admin/zones/{zone}
GET /admin/zones/{zone}/edit
PUT/PATCH /admin/zones/{zone}
DELETE /admin/zones/{zone}

Route names:

admin.zones.index
admin.zones.create
admin.zones.store
admin.zones.show
admin.zones.edit
admin.zones.update
admin.zones.destroy
Division Management
GET /admin/divisions
GET /admin/divisions/create
POST /admin/divisions
GET /admin/divisions/{division}
GET /admin/divisions/{division}/edit
PUT/PATCH /admin/divisions/{division}
DELETE /admin/divisions/{division}

Route names:

admin.divisions.index
admin.divisions.create
admin.divisions.store
admin.divisions.show
admin.divisions.edit
admin.divisions.update
admin.divisions.destroy
School Management
GET /admin/schools
GET /admin/schools/create
POST /admin/schools
GET /admin/schools/{school}
GET /admin/schools/{school}/edit
PUT/PATCH /admin/schools/{school}
DELETE /admin/schools/{school}

Route names:

admin.schools.index
admin.schools.create
admin.schools.store
admin.schools.show
admin.schools.edit
admin.schools.update
admin.schools.destroy
Principal Registry Administration
Registry List
GET /admin/principal-registry

Route name:

admin.principal-registry.index
Create Registry Record
GET /admin/principal-registry/create
POST /admin/principal-registry

Route names:

admin.principal-registry.create
admin.principal-registry.store
View Registry Record
GET /admin/principal-registry/{principal_registry}

Route name:

admin.principal-registry.show
Edit Registry Record
GET /admin/principal-registry/{principal_registry}/edit
PUT/PATCH /admin/principal-registry/{principal_registry}

Route names:

admin.principal-registry.edit
admin.principal-registry.update
Delete Registry Record
DELETE /admin/principal-registry/{principal_registry}

Route name:

admin.principal-registry.destroy
Import Registry Page
GET /admin/principal-registry/import

Route name:

admin.principal-registry.import-page
Import Registry Data
POST /admin/principal-registry/import

Route name:

admin.principal-registry.import
Download Import Template
GET /admin/principal-registry/template

Route name:

admin.principal-registry.template
Principal Profile Administration
GET /admin/principal-profiles
GET /admin/principal-profiles/create
POST /admin/principal-profiles
GET /admin/principal-profiles/{principal_profile}
GET /admin/principal-profiles/{principal_profile}/edit
PUT/PATCH /admin/principal-profiles/{principal_profile}
DELETE /admin/principal-profiles/{principal_profile}

Route names:

admin.principal-profiles.index
admin.principal-profiles.create
admin.principal-profiles.store
admin.principal-profiles.show
admin.principal-profiles.edit
admin.principal-profiles.update
admin.principal-profiles.destroy
Admin Principal Appointment Management
Create Appointment
GET /admin/principal-profiles/{principalProfile}/appointments/create

Route name:

admin.principal-profiles.appointments.create
Store Appointment
POST /admin/principal-profiles/{principalProfile}/appointments

Route name:

admin.principal-profiles.appointments.store
Edit Appointment
GET /admin/principal-appointments/{principalAppointment}/edit

Route name:

admin.principal-appointments.edit
Update Appointment
PUT /admin/principal-appointments/{principalAppointment}

Route name:

admin.principal-appointments.update
Delete Appointment
DELETE /admin/principal-appointments/{principalAppointment}

Route name:

admin.principal-appointments.destroy
Transfer Cycle Management
GET /admin/transfer-cycles
GET /admin/transfer-cycles/create
POST /admin/transfer-cycles
GET /admin/transfer-cycles/{transfer_cycle}
GET /admin/transfer-cycles/{transfer_cycle}/edit
PUT/PATCH /admin/transfer-cycles/{transfer_cycle}
DELETE /admin/transfer-cycles/{transfer_cycle}

Route names:

admin.transfer-cycles.index
admin.transfer-cycles.create
admin.transfer-cycles.store
admin.transfer-cycles.show
admin.transfer-cycles.edit
admin.transfer-cycles.update
admin.transfer-cycles.destroy
Admin Transfer Application Management
Application List
GET /admin/transfer-applications

Route name:

admin.transfer-applications.index
Download Submitted PDF
GET /admin/transfer-applications/{transferApplication}/pdf

Route name:

admin.transfer-applications.pdf

Required permission:

view transfer applications
View Application
GET /admin/transfer-applications/{transferApplication}

Route name:

admin.transfer-applications.show

The PDF route must appear before the general show route.

Principal Self-Service Routes

All routes below use:

/principal

and route-name prefix:

principal.

Required middleware:

auth
verified
role:Principal
Principal Profile
View Own Profile
GET /principal/profile

Route name:

principal.profile.show
Edit Own Profile
GET /principal/profile/edit

Route name:

principal.profile.edit
Update Own Profile
PUT /principal/profile

Route name:

principal.profile.update

Important rule:

The principal may update personal, contact, and service information.
NIC remains locked.
Principal Appointment Management
Create Appointment
GET /principal/appointments/create

Route name:

principal.appointments.create
Store Appointment
POST /principal/appointments

Route name:

principal.appointments.store
Edit Appointment
GET /principal/appointments/{principalAppointment}/edit

Route name:

principal.appointments.edit
Update Appointment
PUT /principal/appointments/{principalAppointment}

Route name:

principal.appointments.update
Delete Appointment
DELETE /principal/appointments/{principalAppointment}

Route name:

principal.appointments.destroy

Ownership validation must be enforced in the controller.

Principal Transfer Applications
Application List
GET /principal/transfer-applications

Route name:

principal.transfer-applications.index
Create Application
GET /principal/transfer-applications/create

Route name:

principal.transfer-applications.create

Query parameter:

transfer_cycle_id

Example:

/principal/transfer-applications/create?transfer_cycle_id=1
Store Draft Application
POST /principal/transfer-applications

Route name:

principal.transfer-applications.store
Edit Draft Application
GET /principal/transfer-applications/{transferApplication}/edit

Route name:

principal.transfer-applications.edit
Update Draft Application
PUT /principal/transfer-applications/{transferApplication}

Route name:

principal.transfer-applications.update
Submit Application
POST /principal/transfer-applications/{transferApplication}/submit

Route name:

principal.transfer-applications.submit
Withdraw Application
POST /principal/transfer-applications/{transferApplication}/withdraw

Route name:

principal.transfer-applications.withdraw
Delete Draft Application
DELETE /principal/transfer-applications/{transferApplication}

Route name:

principal.transfer-applications.destroy
Download Submitted PDF
GET /principal/transfer-applications/{transferApplication}/pdf

Route name:

principal.transfer-applications.pdf
View Application
GET /principal/transfer-applications/{transferApplication}

Route name:

principal.transfer-applications.show

Ownership validation must be enforced for all principal application
routes.

The PDF route must appear before the general show route.

Planned Module 07 Routes

The following routes are planned for the Zonal Director workflow.

Zonal Application List
GET /zonal/transfer-applications

Planned route name:

zonal.transfer-applications.index
Zonal Application View
GET /zonal/transfer-applications/{transferApplication}

Planned route name:

zonal.transfer-applications.show
Zonal Review
POST /zonal/transfer-applications/{transferApplication}/review

Planned route name:

zonal.transfer-applications.review
Zonal Approve
POST /zonal/transfer-applications/{transferApplication}/approve

Planned route name:

zonal.transfer-applications.approve
Zonal Reject
POST /zonal/transfer-applications/{transferApplication}/reject

Planned route name:

zonal.transfer-applications.reject
Zonal PDF Download
GET /zonal/transfer-applications/{transferApplication}/pdf

Planned route name:

zonal.transfer-applications.pdf

All planned zonal routes must enforce assigned-zone restrictions.

Route Verification Commands

List all routes:

php artisan route:list

List principal transfer routes:

php artisan route:list --name=principal.transfer-applications

List admin transfer routes:

php artisan route:list --name=admin.transfer-applications

List PDF routes:

php artisan route:list --name=transfer-applications.pdf

Clear route cache:

php artisan optimize:clear


Route::get(
            '/dashboard',
            ZonalDashboardController::class
        )
            ->middleware('permission:view zonal dashboard')
            ->name('dashboard');

        Route::get(
            '/transfer-applications',
            [
                ZonalTransferApplicationController::class,
                'index',
            ]
        )
            ->middleware(
                'permission:view zonal transfer applications'
            )
            ->name('transfer-applications.index');

        Route::get(
            '/transfer-applications/{transferApplication}/pdf',
            [
                ZonalTransferApplicationController::class,
                'downloadPdf',
            ]
        )
            ->middleware(
                'permission:download zonal transfer application pdfs'
            )
            ->name('transfer-applications.pdf');

        Route::post(
            '/transfer-applications/{transferApplication}/start-review',
            [
                ZonalTransferApplicationController::class,
                'startReview',
            ]
        )
            ->middleware(
                'permission:review zonal transfer applications'
            )
            ->name('transfer-applications.start-review');

        Route::post(
            '/transfer-applications/{transferApplication}/approve',
            [
                ZonalTransferApplicationController::class,
                'approve',
            ]
        )
            ->middleware(
                'permission:approve zonal transfer applications'
            )
            ->name('transfer-applications.approve');

        Route::post(
            '/transfer-applications/{transferApplication}/reject',
            [
                ZonalTransferApplicationController::class,
                'reject',
            ]
        )
            ->middleware(
                'permission:reject zonal transfer applications'
            )
            ->name('transfer-applications.reject');

        /*
         * Keep the general Show route after /pdf and action routes.
         */
        Route::get(
            '/transfer-applications/{transferApplication}',
            [
                ZonalTransferApplicationController::class,
                'show',
            ]
        )
            ->middleware(
                'permission:view zonal transfer applications'
            )
            ->name('transfer-applications.show');
