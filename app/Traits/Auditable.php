<?php

namespace App\Traits;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(
            function (Model $model): void {
                app(AuditLogService::class)
                    ->modelEvent(
                        'created',
                        $model,
                        [],
                        $model->getAttributes()
                    );
            }
        );

        static::updated(
            function (Model $model): void {
                $changes = $model->getChanges();

                if ($changes === []) {
                    return;
                }

                $oldValues = [];

                foreach (array_keys($changes) as $attribute) {
                    $oldValues[$attribute] =
                        $model->getOriginal($attribute);
                }

                app(AuditLogService::class)
                    ->modelEvent(
                        'updated',
                        $model,
                        $oldValues,
                        $changes
                    );
            }
        );

        static::deleted(
            function (Model $model): void {
                app(AuditLogService::class)
                    ->modelEvent(
                        'deleted',
                        $model,
                        $model->getOriginal(),
                        []
                    );
            }
        );

        /*
         * Register the restored event only for models that actually use
         * SoftDeletes. Calling restored() on a normal model causes Laravel
         * to instantiate that model again while it is already booting.
         */
        if (
            in_array(
                SoftDeletes::class,
                class_uses_recursive(static::class),
                true
            )
        ) {
            static::restored(
                function (Model $model): void {
                    app(AuditLogService::class)
                        ->modelEvent(
                            'restored',
                            $model,
                            [],
                            $model->getAttributes()
                        );
                }
            );
        }
    }

    /**
     * Attributes excluded from automatic audit records.
     */
    public function auditExcludedAttributes(): array
    {
        return [];
    }

    /**
     * Attributes explicitly included in automatic audit records.
     *
     * An empty array means audit all fields except excluded attributes.
     */
    public function auditIncludedAttributes(): array
    {
        return [];
    }

    /**
     * Optional parent model for grouping related audit records.
     */
    public function auditParent(): ?Model
    {
        return null;
    }
}
