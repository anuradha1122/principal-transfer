<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferAppealAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_appeal_id',
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

    public function transferAppeal(): BelongsTo
    {
        return $this->belongsTo(TransferAppeal::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by');
    }
}
