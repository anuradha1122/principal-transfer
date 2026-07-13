# Database Design

## zones

Stores the seven education zones in Sabaragamuwa Province.

Key fields:

- id
- name
- code
- district
- office_address
- telephone
- email
- is_active
- sort_order

## divisions

Stores education divisions under zones.

Key fields:

- id
- zone_id
- name
- code
- office_address
- telephone
- email
- is_active
- sort_order

## schools

Stores schools under education divisions.

Key fields:

- id
- division_id
- census_number
- name
- school_type
- gender_type
- school_level
- mediums
- address
- contact information
- student_count
- teacher_count
- is_national_school
- is_active

## Relationships

- Zone has many Divisions.
- Zone has many Schools through Divisions.
- Division belongs to Zone.
- Division has many Schools.
- School belongs to Division.
- School belongs to a Zone through its Division.
