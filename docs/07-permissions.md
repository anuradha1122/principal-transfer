# Permissions

Permissions are managed using Spatie Laravel Permission.

## Dashboard Permissions

- view admin dashboard
- view principal dashboard
- view zonal dashboard
- view provincial dashboard
- view transfer board dashboard

## Access Control

All backend routes and controller actions must perform server-side
permission checks.

Frontend permission checks are used only for display and navigation.
They are not a security boundary.

Super Admin access is handled by a global Gate::before rule.
