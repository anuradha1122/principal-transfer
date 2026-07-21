<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AuditLogService
{
    /**
     * Record a generic audit event.
     *
     * Audit failures are deliberately caught and logged so that an audit
     * failure does not prevent the main business workflow from completing.
     */
    public function record(
        string $event,
        ?Model $auditable = null,
        array $options = []
    ): ?AuditLog {
        if (! config('audit.enabled', true)) {
            return null;
        }

        try {
            $actor = $this->resolveActor(
                $options['user'] ?? null
            );

            $oldValues = $this->sanitize(
                $options['old_values'] ?? []
            );

            $newValues = $this->sanitize(
                $options['new_values'] ?? []
            );

            $metadata = $this->sanitize(
                $options['metadata'] ?? []
            );

            $parent = $options['parent'] ?? null;

            return AuditLog::query()->create([
                'request_id' => $this->contextValue(
                    'audit.request_id'
                ),

                'category' => $options['category']
                    ?? AuditLog::CATEGORY_MODEL,

                'event' => $event,

                'description' => $options['description'] ?? null,

                'auditable_type' => $auditable
                    ? $auditable::class
                    : null,

                'auditable_id' => $auditable?->getKey(),

                'parent_type' => $parent instanceof Model
                    ? $parent::class
                    : ($options['parent_type'] ?? null),

                'parent_id' => $parent instanceof Model
                    ? $parent->getKey()
                    : ($options['parent_id'] ?? null),

                'user_id' => $actor?->id,

                'actor_name' => $actor?->name,

                'actor_email' => $actor?->email,

                'actor_roles' => $this->resolveActorRoles(
                    $actor
                ),

                'old_status' => $options['old_status'] ?? null,

                'new_status' => $options['new_status'] ?? null,

                'old_values' => $oldValues ?: null,

                'new_values' => $newValues ?: null,

                'metadata' => $metadata ?: null,

                'route_name' => config(
                    'audit.capture_route',
                    true
                )
                    ? $this->contextValue(
                        'audit.route_name'
                    )
                    : null,

                'http_method' => $this->contextValue(
                    'audit.http_method'
                ),

                'url' => config(
                    'audit.capture_url',
                    true
                )
                    ? $this->contextValue(
                        'audit.url'
                    )
                    : null,

                'ip_address' => config(
                    'audit.capture_ip_address',
                    true
                )
                    ? $this->contextValue(
                        'audit.ip_address'
                    )
                    : null,

                'user_agent' => config(
                    'audit.capture_user_agent',
                    true
                )
                    ? $this->contextValue(
                        'audit.user_agent'
                    )
                    : null,

                'occurred_at' => $options['occurred_at']
                    ?? now(),
            ]);
        } catch (Throwable $exception) {
            Log::error(
                'Audit log recording failed.',
                [
                    'event' => $event,

                    'auditable_type' => $auditable
                        ? $auditable::class
                        : null,

                    'auditable_id' => $auditable?->getKey(),

                    'exception' => $exception->getMessage(),
                ]
            );

            return null;
        }
    }

    /**
     * Record an explicit workflow action.
     */
    public function workflow(
        string $event,
        Model $auditable,
        ?string $oldStatus,
        ?string $newStatus,
        array $options = []
    ): ?AuditLog {
        return $this->record(
            $event,
            $auditable,
            array_merge(
                $options,
                [
                    'category' => AuditLog::CATEGORY_WORKFLOW,

                    'old_status' => $oldStatus,

                    'new_status' => $newStatus,
                ]
            )
        );
    }

    /**
     * Record a document-related action.
     */
    public function document(
        string $event,
        Model $document,
        array $options = []
    ): ?AuditLog {
        return $this->record(
            $event,
            $document,
            array_merge(
                $options,
                [
                    'category' => AuditLog::CATEGORY_DOCUMENT,
                ]
            )
        );
    }

    /**
     * Record authentication or account-access activity.
     */
    public function authentication(
        string $event,
        ?User $user = null,
        array $options = []
    ): ?AuditLog {
        return $this->record(
            $event,
            $user,
            array_merge(
                $options,
                [
                    'category' => AuditLog::CATEGORY_AUTHENTICATION,

                    'user' => $user,
                ]
            )
        );
    }

    /**
     * Record a security-sensitive activity.
     */
    public function security(
        string $event,
        ?Model $auditable = null,
        array $options = []
    ): ?AuditLog {
        return $this->record(
            $event,
            $auditable,
            array_merge(
                $options,
                [
                    'category' => AuditLog::CATEGORY_SECURITY,
                ]
            )
        );
    }

    /**
     * Create an audit record from an Eloquent model event.
     */
    public function modelEvent(
        string $event,
        Model $model,
        array $oldValues = [],
        array $newValues = []
    ): ?AuditLog {
        /*
         * Combine globally ignored fields with fields excluded by the
         * individual model.
         */
        $excludedAttributes = array_unique([
            ...config(
                'audit.ignored_fields',
                []
            ),

            ...(
                method_exists(
                    $model,
                    'auditExcludedAttributes'
                )
                    ? $model->auditExcludedAttributes()
                    : []
            ),
        ]);

        foreach ($excludedAttributes as $attribute) {
            unset(
                $oldValues[$attribute],
                $newValues[$attribute]
            );
        }

        /*
         * A model may optionally restrict auditing to explicitly included
         * fields.
         */
        $includedAttributes = method_exists(
            $model,
            'auditIncludedAttributes'
        )
            ? $model->auditIncludedAttributes()
            : [];

        if ($includedAttributes !== []) {
            $allowedAttributes = array_flip(
                $includedAttributes
            );

            $oldValues = array_intersect_key(
                $oldValues,
                $allowedAttributes
            );

            $newValues = array_intersect_key(
                $newValues,
                $allowedAttributes
            );
        }

        /*
         * Do not create empty update records after ignored or excluded
         * attributes have been removed.
         */
        if (
            $event === 'updated'
            && $oldValues === []
            && $newValues === []
        ) {
            return null;
        }

        $options = [
            'category' => AuditLog::CATEGORY_MODEL,

            'description' => sprintf(
                '%s %s.',
                class_basename($model),
                $event
            ),

            'old_values' => $oldValues,

            'new_values' => $newValues,
        ];

        if (
            array_key_exists(
                'status',
                $oldValues
            )
            || array_key_exists(
                'status',
                $newValues
            )
        ) {
            $options['old_status'] =
                $oldValues['status'] ?? null;

            $options['new_status'] =
                $newValues['status'] ?? null;
        }

        /*
         * Attach the model's parent record when the model defines one.
         */
        if (
            method_exists(
                $model,
                'auditParent'
            )
        ) {
            $parent = $model->auditParent();

            if ($parent instanceof Model) {
                $options['parent'] = $parent;
            }
        }

        return $this->record(
            strtolower(
                class_basename($model)
            ).'.'.$event,
            $model,
            $options
        );
    }

    /**
     * Sanitize values before storage.
     */
    public function sanitize(
        array $values
    ): array {
        $ignoredFields = config(
            'audit.ignored_fields',
            []
        );

        $sensitiveFields = config(
            'audit.sensitive_fields',
            []
        );

        $normalizedSensitiveFields = array_map(
            'strtolower',
            $sensitiveFields
        );

        $maximumLength = (int) config(
            'audit.maximum_value_length',
            10000
        );

        $values = Arr::except(
            $values,
            $ignoredFields
        );

        return collect($values)
            ->mapWithKeys(
                function (
                    mixed $value,
                    string|int $key
                ) use (
                    $normalizedSensitiveFields,
                    $maximumLength
                ): array {
                    $keyString = (string) $key;

                    if (
                        in_array(
                            Str::lower($keyString),
                            $normalizedSensitiveFields,
                            true
                        )
                    ) {
                        return [
                            $keyString => '[REDACTED]',
                        ];
                    }

                    return [
                        $keyString => $this->normalizeValue(
                            $value,
                            $maximumLength
                        ),
                    ];
                }
            )
            ->all();
    }

    /**
     * Resolve the user responsible for the audited action.
     */
    private function resolveActor(
        ?User $explicitUser
    ): ?User {
        if ($explicitUser instanceof User) {
            return $explicitUser;
        }

        if (! app()->bound('auth')) {
            return null;
        }

        try {
            $authenticatedUser = auth()->user();

            return $authenticatedUser instanceof User
                ? $authenticatedUser
                : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Retrieve request context without failing console commands or tests.
     */
    private function contextValue(
        string $key
    ): mixed {
        try {
            return Context::get($key);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Normalize a value so that it can be safely stored as JSON.
     */
    private function normalizeValue(
        mixed $value,
        int $maximumLength
    ): mixed {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(
                DATE_ATOM
            );
        }

        if ($value instanceof Model) {
            return [
                'type' => $value::class,

                'id' => $value->getKey(),
            ];
        }

        if (is_array($value)) {
            return $this->sanitize(
                $value
            );
        }

        if (is_object($value)) {
            return method_exists(
                $value,
                'toArray'
            )
                ? $this->sanitize(
                    $value->toArray()
                )
                : (string) $value;
        }

        if (is_string($value)) {
            return Str::limit(
                $value,
                $maximumLength,
                '…'
            );
        }

        return $value;
    }

    private function resolveActorRoles(
        ?User $actor
    ): ?array {
        if (! $actor) {
            return null;
        }

        try {
            return $actor
                ->getRoleNames()
                ->values()
                ->all();
        } catch (Throwable) {
            return null;
        }
    }
}
