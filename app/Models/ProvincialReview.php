<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProvincialReview extends Model
{
    use Auditable, HasFactory;

    public const DECISION_PENDING =
        'Pending';

    public const DECISION_APPROVED =
        'Approved';

    public const DECISION_REJECTED =
        'Rejected';

    public const DECISION_RETURNED_TO_ZONE =
        'Returned to Zone';

    protected $fillable = [
        'transfer_application_id',
        'reviewer_id',
        'decision',
        'recommendation',
        'remarks',
        'rejection_reason',
        'return_reason',
        'review_started_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'review_started_at' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function isReturnedToZone(): bool
    {
        return $this->decision
            === self::DECISION_RETURNED_TO_ZONE;
    }

    public function auditParent(): ?Model
    {
        return $this->transferApplication;
    }
}
