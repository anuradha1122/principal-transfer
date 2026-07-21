<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const CATEGORY_MODEL = 'model';

    public const CATEGORY_WORKFLOW = 'workflow';

    public const CATEGORY_AUTHENTICATION = 'authentication';

    public const CATEGORY_DOCUMENT = 'document';

    public const CATEGORY_SECURITY = 'security';

    public const CATEGORY_SYSTEM = 'system';

    protected $fillable = [
        'request_id',
        'category',
        'event',
        'description',
        'auditable_type',
        'auditable_id',
        'parent_type',
        'parent_id',
        'user_id',
        'actor_name',
        'actor_email',
        'actor_roles',
        'old_status',
        'new_status',
        'old_values',
        'new_values',
        'metadata',
        'route_name',
        'http_method',
        'url',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'actor_roles' => 'array',
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Audit logs are append-only
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::updating(function (): bool {
            return false;
        });

        static::deleting(function (): bool {
            return false;
        });
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeCategory(
        Builder $query,
        ?string $category
    ): Builder {
        return $query->when(
            $category,
            fn (Builder $builder) => $builder->where(
                'category',
                $category
            )
        );
    }

    public function scopeEvent(
        Builder $query,
        ?string $event
    ): Builder {
        return $query->when(
            $event,
            fn (Builder $builder) => $builder->where(
                'event',
                $event
            )
        );
    }

    public function scopeForUser(
        Builder $query,
        ?int $userId
    ): Builder {
        return $query->when(
            $userId,
            fn (Builder $builder) => $builder->where(
                'user_id',
                $userId
            )
        );
    }

    public function scopeForSubject(
        Builder $query,
        string $type,
        int $id
    ): Builder {
        return $query
            ->where('auditable_type', $type)
            ->where('auditable_id', $id);
    }

    public function scopeForParent(
        Builder $query,
        string $type,
        int $id
    ): Builder {
        return $query
            ->where('parent_type', $type)
            ->where('parent_id', $id);
    }

    public function scopeBetweenDates(
        Builder $query,
        ?string $from,
        ?string $to
    ): Builder {
        return $query
            ->when(
                $from,
                fn (Builder $builder) => $builder->whereDate(
                    'occurred_at',
                    '>=',
                    $from
                )
            )
            ->when(
                $to,
                fn (Builder $builder) => $builder->whereDate(
                    'occurred_at',
                    '<=',
                    $to
                )
            );
    }
}
