<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrincipalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'principal_registry_id',
        'nic',
        'employee_number',
        'full_name',
        'name_with_initials',
        'gender',
        'date_of_birth',
        'mobile_number',
        'alternate_number',
        'personal_email',
        'address_line_1',
        'address_line_2',
        'city',
        'postal_code',
        'service_category',
        'service_grade',
        'first_appointment_date',
        'principal_service_entry_date',
        'retirement_date',
        'employment_status',
        'qualifications_summary',
        'notes',
        'profile_completed',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'first_appointment_date' => 'date',
            'principal_service_entry_date' => 'date',
            'retirement_date' => 'date',
            'profile_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registry(): BelongsTo
    {
        return $this->belongsTo(
            PrincipalRegistry::class,
            'principal_registry_id'
        );
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(
            PrincipalAppointment::class
        );
    }

    public function currentAppointment(): HasOne
    {
        return $this
            ->hasOne(
                PrincipalAppointment::class
            )
            ->where(
                'is_current',
                true
            );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function transferApplications(): HasMany
    {
        return $this->hasMany(
            TransferApplication::class
        );
    }
}
