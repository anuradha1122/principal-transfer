<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_application_id',
        'preference_order',
        'school_id',
        'preference_reason',
    ];

    protected function casts(): array
    {
        return [
            'preference_order' => 'integer',
        ];
    }

    public function transferApplication(): BelongsTo
    {
        return $this->belongsTo(
            TransferApplication::class
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
