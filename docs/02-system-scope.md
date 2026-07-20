# System Scope

## Project Scope

The Principal Transfer System is an internal provincial education
management system for handling principal transfer applications in the
Sabaragamuwa Province.

The system supports the complete transfer workflow from principal
registration through the final Transfer Board decision.

## Geographic Scope

The system is limited to:

- Sabaragamuwa Province
- Ratnapura District
- Kegalle District
- Seven Education Zones
- Education Divisions under each Zone
- Schools under each Division

Multi-province operation is outside the current MVP scope.

## Included in MVP

### Authentication and Account Management

- Secure login
- Logout
- Password reset
- Email verification
- Account activation and deactivation
- Role assignment
- Permission assignment
- Last login tracking
- Admin password reset
- Inactive account login prevention

### Roles and Permissions

- Super Admin
- Principal
- Zonal Director
- Provincial Director
- Transfer Board Member
- Data Entry Officer
- Role-based dashboards
- Backend authorization
- Permission-based navigation
- Global Super Admin access rule

### Organization Structure

- Zone management
- Division management
- School management
- District assignment
- School census numbers
- School classification
- School level
- School gender type
- Teaching mediums
- School contact details
- Student and teacher population
- Active and inactive master records

### Principal Registry

- Principal Registry management
- NIC normalization
- Old NIC validation
- New NIC validation
- Registry search
- Registry filtering
- CSV import
- CSV template download
- Duplicate NIC prevention
- Registration status tracking
- Registry-to-user linking
- Registry-to-profile linking

### NIC-Controlled Registration

- Ordinary public registration disabled
- Principal NIC verification
- Time-limited NIC verification session
- Principal account creation
- Automatic Principal role assignment
- Principal profile creation
- Principal Registry status update
- Duplicate registration prevention
- Email verification redirect

### Principal Profile Management

- Personal information
- Contact information
- Address information
- Service information
- Employment status
- Qualifications summary
- Notes
- Profile completion status
- Principal self-service profile editing
- NIC field protection
- Admin profile management
- Profile search and filtering

### Principal Appointment Management

- Appointment history
- School appointment details
- Designation
- Appointment type
- Appointment letter number
- Appointment date
- Start date
- End date
- Current appointment flag
- Reason for ending
- Remarks
- Automatic closure of previous current appointment
- Zone-first school selection
- School filtering by selected zone
- Principal self-service appointment creation
- Principal self-service appointment editing
- Principal self-service appointment deletion
- Admin appointment management

### Transfer Cycle Management

- Transfer cycle creation
- Transfer cycle editing
- Transfer cycle viewing
- Transfer cycle deletion
- Transfer year
- Transfer type
- Application open date
- Application close date
- Effective date
- Minimum service requirement
- Maximum school preferences
- Publication status
- Open and closed cycle detection

### Transfer Application Management

- Transfer application creation
- Draft saving
- Draft editing
- Draft deletion
- Transfer reason capture
- Detailed explanation
- Medical reason flag
- Spouse employment reason flag
- Mutual transfer flag
- Mutual principal NIC
- Current appointment snapshot
- Current school snapshot
- Current designation snapshot
- Current school service calculation
- Ranked school preferences
- Duplicate preference prevention
- Current school preference prevention
- Application eligibility validation
- Final declaration
- Final submission
- Application number generation
- Application locking after submission
- Principal application history
- Admin application listing
- Admin application viewing

### Withdrawal and Reapplication

- Application withdrawal
- Withdrawal reason capture
- Withdrawal date recording
- Withdrawal status
- Withdrawn application history
- New application creation in the same cycle after withdrawal
- Only one active application per principal per cycle

### PDF Documents

- Submitted transfer application PDF generation
- Private PDF storage
- PDF generation timestamp
- Principal PDF download from application list
- Principal PDF download from application detail page
- Admin PDF download from application detail page
- PDF availability after withdrawal
- Authorized file delivery through controller routes

### Zonal Review

Planned for Module 07:

- Zone-restricted application access
- Zonal application list
- Zonal application detail view
- Zonal review
- Zonal recommendation
- Zonal approval
- Zonal rejection
- Zonal remarks
- Zonal decision timestamp
- Zonal reviewer tracking
- Application status history

### Provincial Review

Planned for a later module:

- Provincial review queue
- Review of zonally approved applications
- Provincial approval
- Provincial rejection
- Provincial remarks
- Provincial reviewer tracking
- Forwarding to Transfer Board

### Transfer Board

Planned for a later module:

- Board review queue
- Board member access
- Final transfer decision
- Approved school assignment
- Effective date
- Final remarks
- Final decision timestamp
- Final decision PDF

### Notifications

Included or planned within MVP:

- Email verification notification
- Registration confirmation
- Application submission notification
- Zonal decision notification
- Provincial decision notification
- Final decision notification

### Reports and Exports

Included or planned within MVP:

- Application reports
- Transfer cycle reports
- Zone-wise reports
- Status-wise reports
- Principal transfer history
- PDF exports
- Excel exports

### Audit Trail

Included or planned within MVP:

- Created by tracking
- Updated by tracking
- Submission timestamp
- Withdrawal timestamp
- PDF generation timestamp
- Review timestamps
- Decision timestamps
- Application status history
- Reviewer identity tracking

## Excluded from MVP

The following features are outside the current MVP:

- Automated vacancy matching
- Automatic transfer recommendation engine
- GIS road distance calculation
- GPS or map-based school selection
- Transfer appeals
- SMS gateway
- Mobile application
- Payroll integration
- Biometric integration
- Digital signatures
- Electronic document signing
- Advanced scoring algorithms
- Machine learning ranking
- Inter-provincial transfers
- Multi-province support
- National-level transfer workflow
- Real-time chat
- Public application access
- Anonymous application submission

## Future Enhancements

Potential future phases may include:

- GIS-based school distance calculation
- Automated vacancy matching
- Rule-based transfer scoring
- Machine learning recommendation support
- Transfer appeal management
- Digital signature support
- SMS notifications
- Mobile application
- Public transfer statistics
- Advanced dashboards
- National system integration
- API integration with existing education systems

## Scope Control Rules

- Features outside the MVP must not be added without explicit approval.
- Historical transfer records must never be deleted automatically.
- Submitted applications must remain immutable.
- Withdrawn applications must remain visible in history.
- A new application may be created after withdrawal.
- Current master data changes must not alter submitted application
  snapshots.
- PDF documents must remain privately stored.
- Frontend restrictions must always be backed by server-side
  authorization.
