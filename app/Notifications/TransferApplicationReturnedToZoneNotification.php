<?php

namespace App\Notifications;

use App\Models\TransferApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransferApplicationReturnedToZoneNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TransferApplication $application
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Application Returned to Zone',

            'message' => "Application {$this->application->application_number} was returned to the Zone for clarification.",

            'transfer_application_id' => $this->application->id,

            'status' => $this->application->status,
        ];
    }
}
