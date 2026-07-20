# Roles and Permissions

## Overview

The Principal Transfer System uses Spatie Laravel Permission for
role-based access control.

Permissions control access to:

- Dashboards
- User management
- Role and permission management
- Organization structure
- Principal Registry
- Principal profiles
- Principal appointments
- Transfer cycles
- Transfer applications
- PDF documents
- Zonal review
- Provincial review
- Transfer Board decisions
- Reports
- Audit information

All protected actions must be authorized on the backend.

Frontend permission checks are used only to control:

- Navigation visibility
- Button visibility
- Page presentation

Frontend checks are not a security boundary.

---

## Authorization Principles

- All protected routes require authentication.
- Appropriate routes require verified email addresses.
- Principal self-service routes require the Principal role.
- Controller actions must verify ownership where applicable.
- Zonal users must be restricted to their assigned zone.
- Provincial users must be restricted to provincial workflow records.
- Transfer Board users must be restricted to board-stage records.
- File downloads require backend authorization.
- Submitted application PDFs must not be publicly accessible.
- Super Admin access is handled by a global `Gate::before` rule.
- Unauthorized actions must return HTTP 403.
- Permissions must be seeded through `RolePermissionSeeder`.

---

## System Roles

The system uses the following roles:

- Super Admin
- Principal
- Zonal Director
- Provincial Director
- Transfer Board Member
- Data Entry Officer

---

# Dashboard Permissions

## Admin Dashboard

```text
view admin dashboard

Suggested roles:

Super Admin
Data Entry Officer
Principal Dashboard
view principal dashboard

Suggested roles:

Principal
Super Admin
Zonal Dashboard
view zonal dashboard

Suggested roles:

Zonal Director
Super Admin
Provincial Dashboard
view provincial dashboard

Suggested roles:

Provincial Director
Super Admin
Transfer Board Dashboard
view transfer board dashboard

Suggested roles:

Transfer Board Member
Super Admin
User Management Permissions
View Users
view users

Allows:

View user list
Search users
Filter users
View account status
View assigned roles

Suggested roles:

Super Admin
Create Users
create users

Allows:

Create internal users
Assign roles
Set account status

Suggested roles:

Super Admin
Update Users
update users

Allows:

Update user name
Update email
Update role assignments
Activate or deactivate accounts

Suggested roles:

Super Admin
Delete Users
delete users

Allows:

Delete permitted user accounts
Subject to self-deletion and last Super Admin protection

Suggested roles:

Super Admin
Reset User Passwords
reset user passwords

Allows:

Reset another user's password through administration

Suggested roles:

Super Admin
Role and Permission Management
View Roles
view roles

Suggested roles:

Super Admin
Create Roles
create roles

Suggested roles:

Super Admin
Update Roles
update roles

Suggested roles:

Super Admin
Delete Roles
delete roles

Suggested roles:

Super Admin

System roles must be protected from unsafe deletion.

View Permissions
view permissions

Suggested roles:

Super Admin
Manage Permissions
manage permissions

Allows:

Create permissions
Assign permissions to roles
Remove permitted custom permissions

Suggested roles:

Super Admin
Organization Structure Permissions
Zones
view zones
create zones
update zones
delete zones

Suggested roles:

Super Admin
Data Entry Officer, where authorized
Divisions
view divisions
create divisions
update divisions
delete divisions

Suggested roles:

Super Admin
Data Entry Officer, where authorized
Schools
view schools
create schools
update schools
delete schools

Suggested roles:

Super Admin
Data Entry Officer, where authorized

Important restrictions:

Zones containing divisions cannot be deleted.
Divisions containing schools cannot be deleted.
Schools referenced by historical records must not be removed
unsafely.
Principal Registry Permissions
View Principal Registry
view principal registry

Allows:

View registry list
Search registry records
View registration status

Suggested roles:

Super Admin
Data Entry Officer
Create Principal Registry Records
create principal registry

Suggested roles:

Super Admin
Data Entry Officer
Update Principal Registry Records
update principal registry

Suggested roles:

Super Admin
Data Entry Officer
Delete Principal Registry Records
delete principal registry

Suggested roles:

Super Admin
Data Entry Officer, where authorized

Registered records cannot be deleted.

Import Principal Registry
import principal registry

Allows:

Open import page
Upload CSV records
Download the import template

Suggested roles:

Super Admin
Data Entry Officer
Principal Profile Permissions
View Principal Profiles
view principal profiles

Allows:

View principal profile list
Search and filter profiles
View profile details
View service information

Suggested roles:

Super Admin
Data Entry Officer
Zonal Director, within assigned zone
Provincial Director
Transfer Board Member, for relevant applications
Create Principal Profiles
create principal profiles

Suggested roles:

Super Admin
Data Entry Officer

Principal registration may also create a linked profile automatically.

Update Principal Profiles
update principal profiles

Suggested roles:

Super Admin
Data Entry Officer
Zonal Director, where explicitly permitted in a future workflow
Delete Principal Profiles
delete principal profiles

Suggested roles:

Super Admin

Deletion must be blocked when historical applications or appointments
would be damaged.

Manage Own Principal Profile
manage own principal profile

Allows a Principal to:

View own profile
Edit permitted fields
Update personal information
Update contact information
Update service information
Update qualifications
Mark profile as complete

Restrictions:

NIC cannot be changed.
The Principal may access only their own profile.

Suggested roles:

Principal
Super Admin
Principal Appointment Permissions
View Principal Appointments
view principal appointments

Suggested roles:

Super Admin
Data Entry Officer
Zonal Director, within assigned zone
Provincial Director
Transfer Board Member, for relevant applications
Create Principal Appointments
create principal appointments

Suggested roles:

Super Admin
Data Entry Officer
Update Principal Appointments
update principal appointments

Suggested roles:

Super Admin
Data Entry Officer
Delete Principal Appointments
delete principal appointments

Suggested roles:

Super Admin
Data Entry Officer, where authorized
Manage Own Principal Appointments
manage own principal appointments

Allows a Principal to:

Add appointment records
Edit own appointment records
Delete own appointment records where permitted
Mark an appointment as current

Restrictions:

Ownership must be verified.
A Principal may access only their own appointment records.
Creating a new current appointment closes the previous current
appointment.

Suggested roles:

Principal
Super Admin
Transfer Cycle Permissions
View Transfer Cycles
view transfer cycles

Allows:

View cycle list
View cycle details
View application periods
View eligibility rules

Suggested roles:

Super Admin
Principal
Zonal Director
Provincial Director
Transfer Board Member
Data Entry Officer
Create Transfer Cycles
create transfer cycles

Suggested roles:

Super Admin
Update Transfer Cycles
update transfer cycles

Suggested roles:

Super Admin
Delete Transfer Cycles
delete transfer cycles

Suggested roles:

Super Admin

A cycle with submitted applications must not be deleted unsafely.

Manage Transfer Cycles

Where a combined permission is used, the system may use:

manage transfer cycles

This combined permission covers:

Create
Update
Publish
Close
Delete where safe

Suggested roles:

Super Admin
Transfer Application Permissions
View Transfer Applications
view transfer applications

Allows authorized administrative users to:

View transfer application list
View application details
View applicant snapshot
View school preferences
Download submitted PDFs

Suggested roles:

Super Admin
Zonal Director, within assigned zone
Provincial Director, at permitted workflow stages
Transfer Board Member, at permitted workflow stages
Create Transfer Applications
create transfer applications

Allows a Principal to:

Start an application
Save a draft
Add school preferences

Suggested roles:

Principal
Super Admin
Update Own Transfer Applications
update own transfer applications

Allows a Principal to:

Edit own draft application
Update school preferences
Update draft reasons

Restrictions:

Only Draft applications may be edited.
Ownership must be verified.

Suggested roles:

Principal
Super Admin
Delete Own Transfer Applications
delete own transfer applications

Allows:

Delete an owned Draft application

Restrictions:

Submitted applications cannot be deleted.
Historical withdrawn applications must remain available.

Suggested roles:

Principal
Super Admin
Submit Transfer Applications
submit transfer applications

Allows:

Submit an owned Draft application
Accept final declaration
Generate the application number
Trigger submitted PDF generation

Suggested roles:

Principal
Super Admin
Withdraw Transfer Applications
withdraw transfer applications

Allows:

Withdraw an owned eligible application
Record withdrawal reason
Record withdrawal timestamp

Suggested roles:

Principal
Super Admin
Download Own Transfer Application PDFs
download own transfer application pdfs

Allows a Principal to:

Download the PDF for an owned submitted application

Suggested roles:

Principal
Super Admin
Download Transfer Application PDFs
download transfer application pdfs

Allows authorized officers to:

Download submitted transfer application PDFs

Suggested roles:

Super Admin
Zonal Director, within assigned zone
Provincial Director, for authorized records
Transfer Board Member, for authorized records
Planned Module 07 Zonal Review Permissions
View Zonal Transfer Applications
view zonal transfer applications

Allows:

View applications originating from the assigned zone

Suggested roles:

Zonal Director
Super Admin
Review Zonal Transfer Applications
review zonal transfer applications

Allows:

Begin zonal review
Record review notes
Record recommendation

Suggested roles:

Zonal Director
Super Admin
Approve Zonal Transfer Applications
approve zonal transfer applications

Suggested roles:

Zonal Director
Super Admin
Reject Zonal Transfer Applications
reject zonal transfer applications

Suggested roles:

Zonal Director
Super Admin
Return Zonal Transfer Applications
return zonal transfer applications

Allows returning an application where the workflow explicitly permits
correction.

Suggested roles:

Zonal Director
Super Admin

Important restrictions:

Zonal Directors may access only applications from their assigned
zone.
The assigned zone restriction must be enforced in the backend.
Review actions must record reviewer identity and timestamp.
Planned Provincial Review Permissions
view provincial transfer applications
review provincial transfer applications
approve provincial transfer applications
reject provincial transfer applications
forward transfer applications to board

Suggested roles:

Provincial Director
Super Admin
Planned Transfer Board Permissions
view board transfer applications
review board transfer applications
approve final transfers
reject final transfers
waitlist transfer applications
record final transfer decisions

Suggested roles:

Transfer Board Member
Super Admin
Report and Export Permissions

Planned permissions:

view transfer reports
export transfer reports
view principal reports
export principal reports

Suggested roles depend on report scope.

Zone-restricted users must receive only zone-authorized records.

Audit Permissions

Planned permissions:

view audit logs
export audit logs

Suggested roles:

Super Admin
Suggested Role Permission Matrix
Permission Area	Super Admin	Principal	Zonal Director	Provincial Director	Board Member	Data Entry
Admin Dashboard	Yes	No	No	No	No	Yes
Principal Dashboard	Yes	Yes	No	No	No	No
Zonal Dashboard	Yes	No	Yes	No	No	No
Provincial Dashboard	Yes	No	No	Yes	No	No
Board Dashboard	Yes	No	No	No	Yes	No
Users and Roles	Yes	No	No	No	No	No
Zones, Divisions, Schools	Yes	No	View	View	View	Manage
Principal Registry	Yes	No	View	View	View	Manage
Own Profile	Yes	Manage Own	No	No	No	No
All Principal Profiles	Yes	No	Zone View	View	Relevant View	Manage
Own Appointments	Yes	Manage Own	No	No	No	No
Transfer Cycles	Manage	View	View	View	View	View
Own Applications	Yes	Manage Own	No	No	No	No
Zonal Review	Yes	No	Assigned Zone	View	No	No
Provincial Review	Yes	No	No	Yes	View	No
Final Board Decision	Yes	No	No	No	Yes	No
Submitted PDF	Yes	Own Only	Assigned Zone	Authorized	Authorized	Permission Based
Seeder Requirements

All permissions must be added to:

database/seeders/RolePermissionSeeder.php

After updating permissions, run:

php artisan db:seed --class=RolePermissionSeeder
php artisan permission:cache-reset
php artisan optimize:clear

Verify permissions:

php artisan tinker
Spatie\Permission\Models\Permission::orderBy('name')
    ->pluck('name');

Verify a role:

$role = Spatie\Permission\Models\Role::findByName('Principal');

$role->permissions->pluck('name');
Permission Naming Rules

Permission names must:

Use lowercase words
Be human-readable
Use consistent verbs
Avoid duplicate meanings
Match middleware and frontend checks exactly

Preferred verbs:

view
create
update
delete
manage
submit
withdraw
review
approve
reject
download
export

Changing a permission name requires updating:

Seeder
Middleware
Controller authorization
Sidebar checks
React page checks
Automated tests
Documentation


view zonal dashboard
view zonal transfer applications
review zonal transfer applications
approve zonal transfer applications
reject zonal transfer applications
download zonal transfer application pdfs
