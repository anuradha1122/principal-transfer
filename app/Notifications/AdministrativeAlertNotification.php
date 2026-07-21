<?php

namespace App\Notifications;

class AdministrativeAlertNotification extends BaseSystemNotification
{
    public function __construct(
        string $title,
        string $message,
        string $category = 'administrative',
        string $severity = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        array $metadata = []
    ) {
        parent::__construct(
            title: $title,
            message: $message,
            category: $category,
            severity: $severity,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            metadata: $metadata
        );
    }
}
