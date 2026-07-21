<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferAppeal extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_UNDER_REVIEW = 'Under Review';
    public const STATUS_RETURNED = 'Returned for Clarification';
    public const STATUS_RESUBMITTED = 'Resubmitted';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_WITHDRAWN = 'Withdrawn';

    public const DECISION_APPROVED = 'Approved';
    public const DECISION_REJECTED = 'Rejected';

    protected $fillable = [
        'transfer_application_id',
        'principal_profile_id',
        'appeal_number',
        'appeal_reason',
        'appeal_details',
        'requested_outcome',
        'status',
        'submitted_at',
        'review_started_at',
        'returned_at',
        'resubmitted_at',
        'withdrawn_at',
        'decided_at',
        'reviewer_id',
        'clarification_request',
        'clarification_response',
        'decision_outcome',
        'decision_remarks',
        'rejection_reason',
        'revised_school_id',
        'revised_effective_date',
        'revised_appointment_type',
        'revised_decision_reference',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'review_started_at' => 'datetime',
            'returned_at' => 'datetime',
            'resubmitted_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'decided_at' => 'datetime',
            'revised_effective_date' => 'date',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_RETURNED,
            self::STATUS_RESUBMITTED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ];
    }

    public static function activeStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_RETURNED,
            self::STATUS_RESUBMITTED,
        ];
    }

    public function transferApplication(): BelongsTo
    {
        return $this->belongsTo(TransferApplication::class);
    }

    public function principalProfile(): BelongsTo
    {
        return $this->belongsTo(PrincipalProfile::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function revisedSchool(): BelongsTo
    {
        return $this->belongsTo(School::class, 'revised_school_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TransferAppealDocument::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(TransferAppealAction::class)
            ->orderByDesc('acted_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::activeStatuses());
    }

    public function scopeForPrincipal(
        Builder $query,
        int $principalProfileId
    ): Builder {
        return $query->where('principal_profile_id', $principalProfileId);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function isSubmittedForReview(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUBMITTED,
            self::STATUS_RESUBMITTED,
        ], true);
    }

    public function canBeEdited(): bool
    {
        return $this->isDraft();
    }

    public function canBeSubmitted(): bool
    {
        return $this->isDraft();
    }

    public function canBeClarified(): bool
    {
        return $this->isReturned();
    }

    public function canBeWithdrawn(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_RETURNED,
            self::STATUS_RESUBMITTED,
        ], true);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_WITHDRAWN,
        ], true);
    }
}
