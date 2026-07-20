<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferBoardDecision extends Model
{
    use HasFactory;

    public const DECISION_PENDING =
        'Pending';

    public const DECISION_APPROVED =
        'Approved';

    public const DECISION_REJECTED =
        'Rejected';

    public const DECISION_WAITLISTED =
        'Waitlisted';

    protected $fillable = [
        'transfer_application_id',
        'reviewer_id',
        'decision',
        'recommended_school_id',
        'effective_date',
        'appointment_type',
        'decision_reference',
        'remarks',
        'rejection_reason',
        'waitlist_reason',
        'review_started_at',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'review_started_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function transferApplication(): BelongsTo
    {
        return $this->belongsTo(
            TransferApplication::class
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewer_id'
        );
    }

    public function recommendedSchool(): BelongsTo
    {
        return $this->belongsTo(
            School::class,
            'recommended_school_id'
        );
    }

    public function isPending(): bool
    {
        return $this->decision
            === self::DECISION_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->decision
            === self::DECISION_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->decision
            === self::DECISION_REJECTED;
    }

    public function isWaitlisted(): bool
    {
        return $this->decision
            === self::DECISION_WAITLISTED;
    }
}
