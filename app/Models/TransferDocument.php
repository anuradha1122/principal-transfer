<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferDocument extends Model
{
    use Auditable, HasFactory;

    public const TYPE_TRANSFER_ORDER =
        'Transfer Order';

    public const TYPE_APPOINTMENT_LETTER =
        'Appointment Letter';

    public const TYPE_DECISION_LETTER =
        'Decision Letter';

    public const TYPES = [
        self::TYPE_TRANSFER_ORDER,
        self::TYPE_APPOINTMENT_LETTER,
        self::TYPE_DECISION_LETTER,
    ];

    protected $fillable = [
        'transfer_application_id',
        'document_type',
        'document_number',
        'issued_date',
        'effective_date',
        'generated_file_path',
        'signed_file_path',
        'issued_by',
        'generated_at',
        'is_published',
        'published_at',
        'published_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',

            'effective_date' => 'date',

            'generated_at' => 'datetime',

            'is_published' => 'boolean',

            'published_at' => 'datetime',
        ];
    }

    public function transferApplication(): BelongsTo
    {
        return $this->belongsTo(
            TransferApplication::class
        );
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'issued_by'
        );
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'published_by'
        );
    }

    public function scopePublished(
        Builder $query
    ): Builder {
        return $query->where(
            'is_published',
            true
        );
    }

    public function hasSignedCopy(): bool
    {
        return filled(
            $this->signed_file_path
        );
    }

    public function downloadablePath(): ?string
    {
        return $this->signed_file_path
            ?: $this->generated_file_path;
    }

    public function auditParent(): ?Model
    {
        return $this->transferApplication;
    }

    public function auditExcludedAttributes(): array
    {
        return [
            'generated_file_path',
            'signed_file_path',
            'file_path',
        ];
    }
}
