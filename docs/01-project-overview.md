# Project Overview

## Project Name

Principal Transfer System

## Organization

Provincial Department of Education  
Sabaragamuwa Province, Sri Lanka

## Purpose

The Principal Transfer System manages principal transfer applications
from principal registration and profile completion through zonal review,
provincial review, Transfer Board consideration, and final decision.

The system is designed to provide a controlled, transparent, and
auditable workflow for principal transfers within the Sabaragamuwa
Province education administration structure.

## Technology Stack

- Backend: Laravel
- Frontend: React with Inertia.js
- Database: MySQL
- Authentication: Laravel Breeze
- Authorization: Spatie Laravel Permission
- Styling: Tailwind CSS
- Icons: Lucide React
- PDF: DomPDF
- Excel: Laravel Excel
- Testing: Laravel Feature Tests

## Local Development Path

```text
/Applications/MAMP/htdocs/principal-transfer


Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=principal_transfer
DB_USERNAME=root
DB_PASSWORD=root
Main Users
Super Admin
Principal
Zonal Director
Provincial Director
Transfer Board Member
Data Entry Officer
Administrative Structure

The system is limited to the Sabaragamuwa Province.

The education structure is:

Province
    ↓
Zone
    ↓
Division
    ↓
School

The system currently supports the seven education zones in the
Sabaragamuwa Province.

Principal Registration Process

Ordinary Laravel Breeze public registration is disabled.

A principal may register only through NIC-controlled registration.

The registration process is:

The principal enters an NIC number.
The system normalizes and validates the NIC.
The NIC must exist in the Principal Registry.
The Principal Registry record must be active and unregistered.
The system creates the user account.
The Principal role is assigned automatically.
A principal profile is created and linked to the registry record.
The principal completes email verification.
The principal signs in and completes profile and appointment details.
Principal Profile Management

Each principal has one principal profile.

The profile stores:

Full name
Name with initials
NIC number
Employee number
Gender
Date of birth
Contact information
Residential address
Service category
Service grade
Employment status
First appointment date
Principal service entry date
Retirement date
Qualifications summary
Notes

Principals may update their own personal, contact, service, and
appointment information.

The NIC number remains locked because it is linked to the verified
Principal Registry record.

Principal Appointment Management

A principal may maintain a complete appointment history.

Each appointment stores:

School
Designation
Appointment type
Appointment letter number
Appointment date
Start date
End date
Current appointment status
Reason for ending
Remarks

The appointment date is used as the appointment start date.

When a new appointment is marked as current, the previous current
appointment is automatically closed.

The school selection process requires selecting a zone first and then
selecting a school within that zone.

Transfer Cycle Management

Authorized administrators may create and manage transfer cycles.

A transfer cycle defines:

Cycle name
Cycle code
Transfer type
Transfer year
Application opening date
Application closing date
Effective date
Minimum service requirement
Maximum school preferences
Publication status
Workflow settings

Principals may apply only during published and open transfer cycles.

Transfer Application Management

A principal may:

Start a transfer application
Save a draft
Edit a draft
Delete a draft
Select ranked school preferences
Submit the final application
Download the submitted PDF
Withdraw the application where permitted
View application history
Track the application status
Create a new application in the same cycle after withdrawal

The application stores a snapshot of the principal's current
appointment at the time the application is created.

The current school cannot be selected as a preferred transfer school.

Duplicate school preferences are prohibited.

Submitted Application PDF

When a transfer application is submitted:

The application receives a unique application number.
The application status changes to Submitted.
The submitted date is recorded.
The application becomes locked from editing.
A PDF copy of the submitted application is generated.
The PDF is stored in private application storage.
The principal may download the PDF from the application list or
application detail page.
Authorized administrators may download the same PDF from the admin
application detail page.

The submitted PDF remains available after withdrawal or later workflow
decisions.

Transfer Application Workflow

The planned transfer workflow is:

Principal creates a draft application.
Principal submits the application.
The system generates the submitted application PDF.
Zonal Director reviews the application.
Zonal Director approves, rejects, or records a recommendation.
Provincial Director reviews zonally approved applications.
Provincial Director approves, rejects, or sends the application to
the Transfer Board.
Transfer Board reviews applications.
Transfer Board records the final decision.
Principal views the final result.
Principal downloads the final or submitted documents where
available.
Security Rules
All protected routes require authentication.
Principal routes require the Principal role.
Backend authorization is mandatory.
Frontend visibility checks are not a security boundary.
Principals may access only their own profile, appointments, transfer
applications, and PDFs.
Authorized administrators may access records based on permissions.
Zonal Directors must be restricted to applications originating from
their assigned zone.
Submitted PDFs are stored privately and served through authorized
controller routes.
Super Admin access is handled through a global authorization rule.
Completed Modules
Module 01

Project Foundation, Authentication, Roles and Admin Layout

Module 02

Roles, Permissions and User Management

Module 03

Zones, Divisions and Schools Management

Module 04

Principal Registry and NIC-Controlled Self-Registration

Module 05

Principal Profile and Current Appointment Management

Module 06

Transfer Cycles and Transfer Application Management

## Module 07: Zonal Director Transfer Review and Recommendation

Implemented:

- Zonal Director Zone assignment
- Zone-restricted application access
- Zonal review queue
- Zonal application details
- Review start action
- Zonal recommendation
- Zonal approval
- Zonal rejection
- Mandatory rejection reason
- Application action history
- Zonal review notifications
- Private PDF download
- Backend direct-URL protection
