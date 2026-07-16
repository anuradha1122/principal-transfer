## Organization Structure Rules

- The system is limited to Sabaragamuwa Province.
- A zone belongs to either Ratnapura or Kegalle District.
- A division must belong to one zone.
- A school must belong to one division.
- A school inherits its zone through its division.
- School census numbers must be unique.
- A zone containing divisions cannot be deleted.
- A division containing schools cannot be deleted.
- Inactive master records remain available for historical records.
- Submitted transfer records must never lose their related school,
  division or zone.


## Principal Registration Rules

- Ordinary public registration is disabled.
- A principal must first verify an NIC.
- The NIC must exist in principal_registries.
- The registry record must be active.
- The registry status must be unregistered.
- The registry must not already have a registered user.
- NIC comparison uses a normalized uppercase value.
- Old NIC format supports nine digits followed by V or X.
- New NIC format supports twelve digits.
- NIC verification sessions expire after fifteen minutes.
- Successful registration assigns the Principal role.
- Successful registration changes the registry status to registered.
- Registered registry records cannot be deleted.
- Registered NIC numbers cannot be reused.
