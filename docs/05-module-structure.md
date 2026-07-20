
---

# `docs/05-module-structure.md`

Replace the entire existing file. The old version still says NIC registration is waiting to be implemented, despite Module 04 already being completed. :contentReference[oaicite:1]{index=1}

```md
# Module Delivery Structure

## Purpose

This document defines the required delivery pattern for every
development module in the Principal Transfer System.

Each module must follow the same structure so that implementation,
testing, documentation, and Git history remain consistent.

## Mandatory Technology Rules

- Backend must use Laravel.
- Frontend must use React with Inertia.js.
- Database must use MySQL.
- Authorization must use Spatie Laravel Permission.
- Styling must use Tailwind CSS.
- Icons must use Lucide React.
- PDF generation must use DomPDF.
- Excel export must use Laravel Excel.
- Inertia SSR must remain disabled.
- Protected administrative pages must use AdminLayout.
- Principal pages must use the approved principal layout/sidebar
  structure.
- Ordinary Breeze public registration must remain disabled.
- Principal registration is permitted only through NIC-controlled
  registration.

## Required Module Delivery Order

Each module must be delivered using the following structure.

### 1. Module Summary

Include:

- Module number
- Module title
- Purpose
- Main users
- Main workflow
- Dependencies on previous modules

### 2. Project Folder Command

Always begin with:

```bash
cd /Applications/MAMP/htdocs/principal-transfer

3. Package Installation

List any required Composer or NPM packages.

Examples:

composer require package/name
npm install package-name

Do not reinstall packages already present.

4. File Creation Commands

Provide terminal commands to create:

Migrations
Models
Form Requests
Controllers
Policies
Services
Notifications
Mail classes
Exports
Seeders
React pages
Blade PDF views
Tests
Documentation files where required
5. Database Migration

Provide:

Full migration code
Foreign keys
Indexes
Unique constraints
Nullable rules
Safe rollback logic
Short index names where MySQL index-length limits may apply

Do not modify an already-deployed migration unless the user is still
using migrate:fresh and explicitly accepts that approach.

Prefer new migrations for changes to existing tables.

6. Model

Provide:

Full model code
Fillable fields
Casts
Relationships
Query scopes
Helper methods
Status constants where useful
7. Relationships

Document all new relationships clearly.

Examples:

belongsTo
hasMany
hasOne
hasManyThrough
8. Form Requests

Provide separate request classes for:

Store
Update
Submit
Approve
Reject
Withdraw
Special workflow actions

Validation must match database constraints.

Backend validation must not rely on frontend controls.

9. Controllers

Provide full controller code.

Controllers must include:

Authorization
Ownership checks
Zone restrictions where applicable
Database transactions
Relationship loading
Search and filters
Pagination
Flash messages
Safe error handling
Redirects
PDF generation where required
10. Policies and Authorization

Every protected action must have backend authorization.

Use:

Middleware
Policies
Gates
Spatie permissions
Ownership validation
Zone-based restrictions

Frontend permission checks are only for user interface visibility.

They are not a security boundary.

11. Services

Create service classes for reusable business logic.

Examples:

NIC normalization
Eligibility calculation
Application number generation
PDF generation
Status transition validation
Zone restriction logic
Current appointment closure
12. Notifications and Email

Add notifications at appropriate workflow points.

Examples:

Registration completed
Application submitted
Application withdrawn
Zonal decision recorded
Provincial decision recorded
Final decision recorded

Notifications must not block critical transactions if email delivery
fails.

13. PDF and Excel Exports

Every module requiring documents or reports must include:

PDF view
PDF generation method
Authorized download route
Excel export class
Export route
Appropriate buttons in React pages

Private or sensitive PDF files must not be stored in public storage.

14. Seeders

Provide seeders where test or master data is required.

Seeders must:

Use actual database columns
Avoid duplicate records
Preserve foreign key order
Use stable codes
Support migrate:fresh --seed
15. Routes

Provide full route definitions.

Routes must include:

Correct prefixes
Correct route names
Correct middleware
Permission checks
Specific routes before general parameter routes
Resource parameter names where needed

Example:

Route::get(
    '/transfer-applications/{transferApplication}/pdf',
    [...]
);

Route::get(
    '/transfer-applications/{transferApplication}',
    [...]
);

The PDF route must appear before the general show route.

16. Sidebar and Navigation Update

Update:

Admin sidebar
Principal sidebar
Role-specific dashboard links
Permission visibility
Active-route highlighting

Navigation items must match actual route names.

17. React Pages

Provide full copy-paste React code.

Each module may require:

Index
Create
Edit
Show
Shared form component
Filters
Pagination
Status badges
Confirmation dialogs
PDF buttons
Empty states
Error messages
Responsive layouts

React pages must:

Use safe default props
Use optional chaining for nullable relationships
Avoid undefined .map() calls
Use Inertia Link for page navigation
Use normal anchor tags for file downloads
Preserve validation errors
Display processing states
18. Documentation Updates

Update all relevant documentation after each module.

Required documents may include:

01-project-overview.md
02-system-scope.md
03-user-roles.md
04-database-design.md
05-module-structure.md
06-routes.md
07-permissions.md
08-business-rules.md
11-status-workflow.md
13-development-log.md
14-change-log.md
15-next-chat-handover.md

Documentation must match actual:

Table names
Column names
Route names
Status values
Permissions
Business rules
Completed modules
Next exact step
19. Testing Checklist

Provide manual testing steps covering:

Happy path
Validation errors
Unauthorized access
Ownership restrictions
Role restrictions
Zone restrictions
Duplicate prevention
Status transitions
PDF download
Empty states
Mobile responsiveness
20. Automated Tests

Provide Laravel Feature Tests.

Tests should cover:

Authentication
Authorization
Permissions
Ownership
Validation
Database changes
Workflow transitions
PDF access
Withdrawal
Reapplication
Zone restrictions
21. Build and Verification Commands

Include:

php artisan optimize:clear
php artisan migrate:status
php artisan test
npm run build

Where appropriate, also include:

php artisan route:list
php artisan migrate:fresh --seed
composer dump-autoload
22. Git Commit

Provide a clear commit message.

Use:

git add .
git status
git commit -m "Clear module summary"
git push origin main

For larger modules, use a detailed commit:

git commit \
  -m "Add module title and primary workflow" \
  -m "Add controllers, requests, React pages, permissions, tests, exports, and documentation."
23. Development Log Update

Add:

Module title
Status
Implemented features
Important fixes
Testing result
Git commit hash
24. Change Log Update

Add a concise list of changes introduced by the module.

25. Next Exact Step

Every module must end with one clear next step.

Example:

Next Exact Step:
Module 07: Zonal Director Transfer Review and Recommendation
Coding Standards
Provide full copy-paste code when requested.
Do not omit imports.
Do not use placeholder method bodies.
Use transactions for multi-record workflows.
Use Form Requests for validation.
Use service classes for reusable logic.
Keep controllers readable.
Use consistent status values.
Use consistent route naming.
Use optional chaining in React.
Use safe default arrays and objects.
Avoid duplicate UI actions unless intentionally useful.
Keep PDF files private.
Preserve historical records.
Security Standards
All protected routes require authentication.
Role-based routes require correct role middleware.
Permission checks must exist on the backend.
Principal ownership must be verified.
Zone-restricted users must be restricted by assigned zone.
Submitted applications must be immutable.
NIC cannot be changed by the principal.
File downloads must be authorized.
Frontend checks are not sufficient security.
Module Completion Definition

A module is considered complete only when:

Database changes are implemented.
Models and relationships are complete.
Validation is complete.
Controllers are complete.
Authorization is complete.
Routes are registered.
Navigation is updated.
React pages work.
Tests pass.
Build passes.
Documentation is updated.
Git commit is created.
Next exact step is documented.
