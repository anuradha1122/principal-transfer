<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseSystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected readonly string $title,
        protected readonly string $message,
        protected readonly string $category = 'system',
        protected readonly string $severity = 'info',
        protected readonly ?string $actionUrl = null,
        protected readonly ?string $actionLabel = null,
        protected readonly array $metadata = []
    ) {
        $this->onQueue(
            'notifications'
        );
    }

    public function via(
        object $notifiable
    ): array {
        return [
            'database',
        ];
    }

    public function toDatabase(
        object $notifiable
    ): array {
        return [
            'title' => $this->title,

            'message' => $this->message,

            'category' => $this->category,

            'severity' => $this->severity,

            'action_url' => $this->actionUrl,

            'action_label' => $this->actionLabel,

            'metadata' => $this->metadata,
        ];
    }
}
