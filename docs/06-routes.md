# Routes

## Authentication

- GET /login
- POST /login
- POST /logout
- GET /forgot-password
- POST /forgot-password
- GET /reset-password/{token}
- POST /reset-password
- GET /verify-email
- GET /verify-email/{id}/{hash}
- POST /email/verification-notification

## Dashboard

- GET /dashboard
- GET /admin/dashboard
- GET /principal/dashboard
- GET /zonal/dashboard
- GET /provincial/dashboard
- GET /transfer-board/dashboard

## Profile

- GET /profile
- PATCH /profile
- DELETE /profile

## Registration

Ordinary Breeze registration is disabled.

NIC-controlled registration will be implemented in Module 04.
