<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Zone;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'assigned_zone_id',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'created_by'
        );
    }

    public function principalRegistry(): HasOne
    {
        return $this->hasOne(
            PrincipalRegistry::class,
            'registered_user_id'
        );
    }

    public function principalProfile(): HasOne
    {
        return $this->hasOne(PrincipalProfile::class);
    }

    public function assignedZone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'assigned_zone_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function isZonalDirector(): bool
    {
        return $this->hasRole('Zonal Director');
    }

    public function hasAssignedZone(): bool
    {
        return $this->assigned_zone_id !== null;
    }
}
