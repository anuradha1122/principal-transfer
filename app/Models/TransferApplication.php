<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_cycle_id',
        'principal_profile_id',
        'current_appointment_id',
        'application_number',
        'principal_name',
        'nic',
        'employee_number',
        'current_school_id',
        'current_designation',
        'service_grade',
        'current_appointment_start_date',
        'current_school_service_months',
        'transfer_reason',
        'reason_details',
        'has_medical_reason',
        'has_spouse_employment_reason',
        'is_mutual_transfer',
        'mutual_principal_nic',
        'status',
        'submitted_at',
        'withdrawn_at',
        'withdrawal_reason',
        'declaration_accepted',
        'principal_remarks',
        'created_by',
        'updated_by',
        'submitted_pdf_path',
        'submitted_pdf_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'current_appointment_start_date' => 'date',
            'current_school_service_months' => 'integer',
            'has_medical_reason' => 'boolean',
            'has_spouse_employment_reason' => 'boolean',
            'is_mutual_transfer' => 'boolean',
            'submitted_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'declaration_accepted' => 'boolean',
            'submitted_pdf_generated_at' => 'datetime',
            'declaration_accepted' => 'boolean',
        ];
    }

    public function transferCycle(): BelongsTo
    {
        return $this->belongsTo(
            TransferCycle::class
        );
    }

    public function principalProfile(): BelongsTo
    {
        return $this->belongsTo(
            PrincipalProfile::class
        );
    }

    public function currentAppointment(): BelongsTo
    {
        return $this->belongsTo(
            PrincipalAppointment::class,
            'current_appointment_id'
        );
    }

    public function currentSchool(): BelongsTo
    {
        return $this->belongsTo(
            School::class,
            'current_school_id'
        );
    }

    public function preferences(): HasMany
    {
        return $this
            ->hasMany(
                TransferPreference::class
            )
            ->orderBy('preference_order');
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

    public function scopeActiveForPrincipal(
        Builder $query,
        int $cycleId,
        int $principalProfileId
    ): Builder {
        return $query
            ->where(
                'transfer_cycle_id',
                $cycleId
            )
            ->where(
                'principal_profile_id',
                $principalProfileId
            )
            ->whereNotIn(
                'status',
                [
                    'Withdrawn',
                    'Cancelled',
                ]
            );
    }

    public function isEditableByPrincipal(): bool
    {
        return $this->status === 'Draft';
    }

    public function canBeWithdrawn(): bool
    {
        return $this->transferCycle
            && $this->transferCycle
                ->allow_withdrawal
            && in_array(
                $this->status,
                [
                    'Submitted',
                    'Zonal Review',
                ],
                true
            );
    }
}
