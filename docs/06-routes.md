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


## Organization Structure

### Zones

- GET /admin/zones
- GET /admin/zones/create
- POST /admin/zones
- GET /admin/zones/{zone}
- GET /admin/zones/{zone}/edit
- PUT/PATCH /admin/zones/{zone}
- DELETE /admin/zones/{zone}

### Divisions

- GET /admin/divisions
- GET /admin/divisions/create
- POST /admin/divisions
- GET /admin/divisions/{division}
- GET /admin/divisions/{division}/edit
- PUT/PATCH /admin/divisions/{division}
- DELETE /admin/divisions/{division}

### Schools

- GET /admin/schools
- GET /admin/schools/create
- POST /admin/schools
- GET /admin/schools/{school}
- GET /admin/schools/{school}/edit
- PUT/PATCH /admin/schools/{school}
- DELETE /admin/schools/{school}


## Principal Registry Administration

- GET /admin/principal-registry
- GET /admin/principal-registry/create
- POST /admin/principal-registry
- GET /admin/principal-registry/{principal_registry}
- GET /admin/principal-registry/{principal_registry}/edit
- PUT/PATCH /admin/principal-registry/{principal_registry}
- DELETE /admin/principal-registry/{principal_registry}
- GET /admin/principal-registry/import
- POST /admin/principal-registry/import
- GET /admin/principal-registry/template

## Principal Registration

- GET /principal-registration
- POST /principal-registration/verify-nic
- GET /principal-registration/create
- POST /principal-registration
