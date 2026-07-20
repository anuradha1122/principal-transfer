<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZonalReview extends Model
{
    use HasFactory;

    public const DECISION_APPROVED = 'approved';
    public const DECISION_REJECTED = 'rejected';

    protected $fillable = [
        'transfer_application_id',
        'zone_id',
        'reviewer_id',
        'recommendation',
        'decision',
        'remarks',
        'rejection_reason',
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
        return $this->belongsTo(TransferApplication::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('decision');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('decision', self::DECISION_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('decision', self::DECISION_REJECTED);
    }
}
