<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrincipalAppointment extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'principal_profile_id',
        'school_id',
        'designation',
        'appointment_type',
        'appointment_number',
        'appointment_date',
        'start_date',
        'end_date',
        'is_current',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function principalProfile(): BelongsTo
    {
        return $this->belongsTo(
            PrincipalProfile::class
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class
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
}
