<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferApplicationAction extends Model
{
    use HasFactory;

    public const ACTION_SUBMITTED = 'submitted';
    public const ACTION_WITHDRAWN = 'withdrawn';
    public const ACTION_ZONAL_REVIEW_STARTED = 'zonal_review_started';
    public const ACTION_ZONAL_APPROVED = 'zonal_approved';
    public const ACTION_ZONAL_REJECTED = 'zonal_rejected';

    public const ACTION_PROVINCIAL_REVIEW_STARTED =
        'Provincial Review Started';

    public const ACTION_PROVINCIAL_APPROVED =
        'Provincial Approved';

    public const ACTION_PROVINCIAL_REJECTED =
        'Provincial Rejected';

    public const ACTION_RETURNED_TO_ZONE =
        'Returned to Zone';

    public const ACTION_BOARD_REVIEW_STARTED =
        'Board Review Started';

    public const ACTION_BOARD_APPROVED =
        'Board Approved';

    public const ACTION_BOARD_REJECTED =
        'Board Rejected';

    public const ACTION_BOARD_WAITLISTED =
        'Board Waitlisted';

    protected $fillable = [
        'transfer_application_id',
        'action',
        'from_status',
        'to_status',
        'remarks',
        'acted_by',
        'acted_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'acted_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function transferApplication(): BelongsTo
    {
        return $this->belongsTo(TransferApplication::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by');
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('acted_at')->orderByDesc('id');
    }
}
