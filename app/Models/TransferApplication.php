<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransferApplication extends Model
{
    use Auditable;
    use HasFactory;

    public const STATUS_DRAFT =
        'Draft';

    public const STATUS_SUBMITTED =
        'Submitted';

    public const STATUS_ZONAL_REVIEW =
        'Zonal Review';

    public const STATUS_ZONAL_APPROVED =
        'Zonal Approved';

    public const STATUS_ZONAL_REJECTED =
        'Zonal Rejected';

    public const STATUS_PROVINCIAL_REVIEW =
        'Provincial Review';

    public const STATUS_PROVINCIAL_APPROVED =
        'Provincial Approved';

    public const STATUS_PROVINCIAL_REJECTED =
        'Provincial Rejected';

    public const STATUS_RETURNED_TO_ZONE =
        'Returned to Zone';

    public const STATUS_BOARD_REVIEW =
        'Board Review';

    public const STATUS_APPROVED =
        'Approved';

    public const STATUS_REJECTED =
        'Rejected';

    public const STATUS_WAITLISTED =
        'Waitlisted';

    public const STATUS_WITHDRAWN =
        'Withdrawn';

    public const STATUS_CANCELLED =
        'Cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_ZONAL_REVIEW,
        self::STATUS_ZONAL_APPROVED,
        self::STATUS_ZONAL_REJECTED,
        self::STATUS_PROVINCIAL_REVIEW,
        self::STATUS_PROVINCIAL_APPROVED,
        self::STATUS_PROVINCIAL_REJECTED,
        self::STATUS_RETURNED_TO_ZONE,
        self::STATUS_BOARD_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_WAITLISTED,
        self::STATUS_WITHDRAWN,
        self::STATUS_CANCELLED,
    ];

    /**
     * Return all valid transfer application statuses.
     */
    public static function statusOptions(): array
    {
        return self::STATUSES;
    }

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
        'origin_zone_id',
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
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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

    public function originZone(): BelongsTo
    {
        return $this->belongsTo(
            Zone::class,
            'origin_zone_id'
        );
    }

    public function preferences(): HasMany
    {
        return $this
            ->hasMany(
                TransferPreference::class
            )
            ->orderBy(
                'preference_order'
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

    public function actions(): HasMany
    {
        return $this
            ->hasMany(
                TransferApplicationAction::class
            )
            ->latest('acted_at')
            ->latest('id');
    }

    public function zonalReview(): HasOne
    {
        return $this->hasOne(
            ZonalReview::class
        );
    }

    public function provincialReview(): HasOne
    {
        return $this->hasOne(
            ProvincialReview::class
        );
    }

    public function transferBoardDecision(): HasOne
    {
        return $this->hasOne(
            TransferBoardDecision::class
        );
    }

    public function transferDocuments(): HasMany
    {
        return $this
            ->hasMany(
                TransferDocument::class
            )
            ->latest('issued_date')
            ->latest('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */

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
                    self::STATUS_WITHDRAWN,
                    self::STATUS_CANCELLED,
                ]
            );
    }

    public function scopeForZone(
        Builder $query,
        ?int $zoneId
    ): Builder {
        if ($zoneId === null) {
            return $query->whereRaw(
                '1 = 0'
            );
        }

        return $query->where(
            'origin_zone_id',
            $zoneId
        );
    }

    public function scopeZonalQueue(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_SUBMITTED,
                self::STATUS_ZONAL_REVIEW,
                self::STATUS_RETURNED_TO_ZONE,
            ]
        );
    }

    public function scopeZonallyReviewed(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_ZONAL_APPROVED,
                self::STATUS_ZONAL_REJECTED,
            ]
        );
    }

    public function scopeProvincialQueue(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_ZONAL_APPROVED,
                self::STATUS_PROVINCIAL_REVIEW,
            ]
        );
    }

    public function scopeProvinciallyReviewed(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_PROVINCIAL_APPROVED,
                self::STATUS_PROVINCIAL_REJECTED,
                self::STATUS_RETURNED_TO_ZONE,
            ]
        );
    }

    public function scopeBoardQueue(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_PROVINCIAL_APPROVED,
                self::STATUS_BOARD_REVIEW,
            ]
        );
    }

    public function scopeBoardDecided(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_APPROVED,
                self::STATUS_REJECTED,
                self::STATUS_WAITLISTED,
            ]
        );
    }

    public function scopeFinalized(
        Builder $query
    ): Builder {
        return $query->whereIn(
            'status',
            [
                self::STATUS_APPROVED,
                self::STATUS_REJECTED,
                self::STATUS_WAITLISTED,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Principal workflow helpers
    |--------------------------------------------------------------------------
    */

    public function isEditableByPrincipal(): bool
    {
        return $this->status
            === self::STATUS_DRAFT;
    }

    public function canBeWithdrawn(): bool
    {
        return $this->transferCycle
            && $this->transferCycle
                ->allow_withdrawal
            && in_array(
                $this->status,
                [
                    self::STATUS_SUBMITTED,
                    self::STATUS_ZONAL_REVIEW,
                ],
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Zonal workflow helpers
    |--------------------------------------------------------------------------
    */

    public function canStartZonalReview(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_SUBMITTED,
                self::STATUS_RETURNED_TO_ZONE,
            ],
            true
        );
    }

    public function canReceiveZonalDecision(): bool
    {
        return $this->status
            === self::STATUS_ZONAL_REVIEW;
    }

    public function hasZonalDecision(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_ZONAL_APPROVED,
                self::STATUS_ZONAL_REJECTED,
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Provincial workflow helpers
    |--------------------------------------------------------------------------
    */

    public function canEnterProvincialReview(): bool
    {
        return $this->status
            === self::STATUS_ZONAL_APPROVED;
    }

    public function isUnderProvincialReview(): bool
    {
        return $this->status
            === self::STATUS_PROVINCIAL_REVIEW;
    }

    public function canReceiveProvincialDecision(): bool
    {
        return $this->status
            === self::STATUS_PROVINCIAL_REVIEW;
    }

    public function isProvincialApproved(): bool
    {
        return $this->status
            === self::STATUS_PROVINCIAL_APPROVED;
    }

    public function isProvincialRejected(): bool
    {
        return $this->status
            === self::STATUS_PROVINCIAL_REJECTED;
    }

    public function isReturnedToZone(): bool
    {
        return $this->status
            === self::STATUS_RETURNED_TO_ZONE;
    }

    public function hasProvincialDecision(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PROVINCIAL_APPROVED,
                self::STATUS_PROVINCIAL_REJECTED,
                self::STATUS_RETURNED_TO_ZONE,
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer Board workflow helpers
    |--------------------------------------------------------------------------
    */

    public function canEnterBoardReview(): bool
    {
        return $this->status
            === self::STATUS_PROVINCIAL_APPROVED;
    }

    public function isUnderBoardReview(): bool
    {
        return $this->status
            === self::STATUS_BOARD_REVIEW;
    }

    public function canReceiveBoardDecision(): bool
    {
        return $this->status
            === self::STATUS_BOARD_REVIEW;
    }

    public function isApproved(): bool
    {
        return $this->status
            === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status
            === self::STATUS_REJECTED;
    }

    public function isWaitlisted(): bool
    {
        return $this->status
            === self::STATUS_WAITLISTED;
    }

    public function hasFinalDecision(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_APPROVED,
                self::STATUS_REJECTED,
                self::STATUS_WAITLISTED,
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer document helpers
    |--------------------------------------------------------------------------
    */

    public function canGenerateTransferOrder(): bool
    {
        $decision =
            $this->transferBoardDecision;

        return $this->status
            === self::STATUS_APPROVED
            && $decision !== null
            && filled(
                $decision->recommended_school_id
            )
            && filled(
                $decision->effective_date
            )
            && filled(
                $decision->decision_reference
            );
    }

    public function canGenerateAppointmentLetter(): bool
    {
        return $this
            ->canGenerateTransferOrder();
    }

    public function canGenerateDecisionLetter(): bool
    {
        $decision =
            $this->transferBoardDecision;

        return in_array(
            $this->status,
            [
                self::STATUS_REJECTED,
                self::STATUS_WAITLISTED,
            ],
            true
        )
            && $decision !== null
            && filled(
                $decision->decision_reference
            );
    }

    public function canGenerateDocumentType(
        string $documentType
    ): bool {
        return match ($documentType) {
            TransferDocument::TYPE_TRANSFER_ORDER => $this
                ->canGenerateTransferOrder(),

            TransferDocument::TYPE_APPOINTMENT_LETTER => $this
                ->canGenerateAppointmentLetter(),

            TransferDocument::TYPE_DECISION_LETTER => $this
                ->canGenerateDecisionLetter(),

            default => false,
        };
    }

    public function hasTransferDocumentType(
        string $documentType
    ): bool {
        if (
            $this->relationLoaded(
                'transferDocuments'
            )
        ) {
            return $this
                ->transferDocuments
                ->contains(
                    'document_type',
                    $documentType
                );
        }

        return $this
            ->transferDocuments()
            ->where(
                'document_type',
                $documentType
            )
            ->exists();
    }

    public function hasPublishedTransferDocuments(): bool
    {
        if (
            $this->relationLoaded(
                'transferDocuments'
            )
        ) {
            return $this
                ->transferDocuments
                ->contains(
                    fn (
                        TransferDocument $document
                    ): bool => $document->is_published
                );
        }

        return $this
            ->transferDocuments()
            ->where(
                'is_published',
                true
            )
            ->exists();
    }

    public function transferAppeals(): HasMany
    {
        return $this->hasMany(TransferAppeal::class);
    }

    public function auditExcludedAttributes(): array
    {
        return [
            'submitted_pdf_path',
        ];
    }
}
