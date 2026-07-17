<?php

namespace App\Models;

use App\Services\NicService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrincipalRegistry extends Model
{
    use HasFactory;

    protected $fillable = [
        'nic',
        'normalized_nic',
        'full_name',
        'name_with_initials',
        'school_id',
        'designation',
        'employee_number',
        'registration_status',
        'registered_user_id',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(
            function (PrincipalRegistry $registry): void {
                $registry->normalized_nic = app(
                    NicService::class
                )->normalize($registry->nic);

                if (! $registry->is_active) {
                    $registry->registration_status = 'disabled';
                }

                if (
                    $registry->is_active &&
                    $registry->registration_status === 'disabled'
                ) {
                    $registry->registration_status =
                        $registry->registered_user_id
                            ? 'registered'
                            : 'unregistered';
                }
            }
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function registeredUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'registered_user_id'
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

    public function isAvailableForRegistration(): bool
    {
        return $this->is_active
            && $this->registration_status === 'unregistered'
            && $this->registered_user_id === null;
    }

    public function principalProfile(): HasOne
    {
        return $this->hasOne(
            PrincipalProfile::class,
            'principal_registry_id'
        );
    }
}
